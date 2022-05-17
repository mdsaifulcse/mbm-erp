<?php

namespace App\Http\Controllers\Pms;
use App\Http\Controllers\Controller;
use App\Mail\Pms\RequestForProposalToSupplierMail;
use App\Models\PmsModels\Product;
use App\Models\PmsModels\Requisition;
use App\Models\PmsModels\RequisitionItem;
use App\Models\PmsModels\RequisitionType;
use App\Models\PmsModels\Rfp\RequestProposal;
use App\Models\PmsModels\Rfp\RequestProposalDefineSupplier;
use App\Models\PmsModels\Rfp\RequestProposalDetails;
use App\Models\PmsModels\Quotations;
use App\Models\PmsModels\QuotationsItems;
use App\Models\PmsModels\Suppliers;
use Illuminate\Http\Request;
use App\Http\Requests;
use DB,Validator,Auth;
use Illuminate\Support\Facades\Mail;

class RequestProposalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {

            $title = 'Request Proposal List';

            $requestProposals=RequestProposal::with('defineToSupplier','requestProposalDetails','requestProposalDetails.product','createdBy','relQuotations')->whereNotIn('quotation_generate_type',['complete'])->paginate(20);

            return view('pms.backend.pages.rfp.index', compact('title','requestProposals'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function requisitionsIndex()
    {
        try {

            $title = 'RFP Requisition List';

            $requistion_data=Requisition::where(['status'=>1,'approved_id'=>1,'is_send_to_rfp'=>'yes','delivery_status'=>'processing'])->paginate(30);

            $requisition_user_list=Requisition::join('users','users.id','=','requisitions.author_id')
            ->groupBy('requisitions.author_id')
            ->where(['requisitions.status'=>1,'requisitions.approved_id'=>1,'requisitions.is_send_to_rfp'=>'yes','requisitions.delivery_status'=>'processing'])
            ->get(['users.id','users.name']);

            return view('pms.backend.pages.rfp.deft-requisition-index', compact('title','requistion_data','requisition_user_list'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

     /**
     * Rfp list view serarch.
     * Search between from and to date and also user can search by employee
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function rfpRequisitionListViewSearch(Request $request)
    {
        $response = [];

        $from_date=date('Y-m-d',strtotime($request->from_date));
        $to_date=date('Y-m-d',strtotime($request->to_date));

        $requisition_by=$request->requisition_by;
        $requisition_status=$request->requisition_status;

        $requistion_data=Requisition::whereDate('requisition_date', '>=', $from_date)
        ->whereDate('requisition_date', '<=', $to_date)
        ->when($requisition_by, function($query) use($requisition_by){
            return $query->where('author_id',$requisition_by);
        })
        ->where(['status'=>1,'approved_id'=>1,'is_send_to_rfp'=>'yes','delivery_status'=>'processing'])
        ->paginate(30);

        try {
            if(count($requistion_data)>0)
            {
                $body = \Illuminate\Support\Facades\View::make('pms.backend.pages.rfp.rfp-search-result-view',
                    ['requistion_data'=> $requistion_data]);
                $contents = $body->render();

                $response['result'] = 'success';
                $response['body'] = $contents;
            }else{
                $response['body'] = '';
                $response['result'] = 'error';
                $response['message'] = 'Data not found.!!';
            }

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }

        return $response;
    }

    public function convertToRfp(Request $request)
    {
        $response = [];

        $requisition=Requisition::where(['id'=>$request->requisition_id,'status'=>1,'approved_id'=>1,'is_send_to_rfp'=>'yes','delivery_status'=>'processing'])->first();

        //Start transaction
        DB::beginTransaction();

        try {
            if(count((array)$requisition)>0)
            {
                $requisition->delivery_status = 'rfp';
                $requisition->save();

            //\App\Models\PmsModels\RequisitionTracking::storeRequisitionTracking($requisition->id,'rfp');
                //Commit data
                DB::commit();


                $response['result'] = 'success';
                $response['message'] = 'Successfully Converted to RFP!!';
            }else{
                $response['result'] = 'error';
                $response['message'] = 'Data not found.!!';
            }

        }catch (\Throwable $th){
            //If process has any problem then rollback the data
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }

        return $response;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {

            $title = 'Proposal Create';
            $supplierList=Suppliers::pluck('name','id')->all();
            
            $products = Product::whereHas('requisitionItem.requisition', function($query){
                return $query->where(['status'=>1,'is_send_to_rfp'=>'yes','delivery_status'=>'rfp']);
            })->whereHas('requisitionItem', function($query){
                return $query->where('is_send','no');
            })
            ->with(['category','requisitionItem', 'requisitionItem.requisition'])
                ->paginate(30);

            $prefix='RP-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
            $refNo=uniqueCode(14,$prefix,'request_proposals','id');

            return view('pms.backend.pages.rfp.create', compact('title','products','supplierList','refNo'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function requisitionDetailsByProductId($product_id)
    {
        try {

            $title = 'Create Request for Proposal';
            $product = Product::findOrFail($product_id);
            $items = RequisitionItem::where(['product_id'=>$product_id,'is_send'=>'no'])->whereHas('requisition', function($query){
                return $query->where('status', 1);
            })->get();

            return view('pms.backend.pages.proposal._product-wise-requisition', compact('title','product', 'items'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Requests\Pms\RequestProposalRequest $request)
    {


        DB::beginTransaction();
        try {

            $proposalType='';
            if ($request->has('type')){
                $proposalType='online';
            }else{
                $proposalType='manual';
            }

            $requestProposal=RequestProposal::create([
                'type'=>$proposalType,
                'reference_no'=>$request->reference_no,
                'request_date'=>date('Y-m-d',strtotime($request->request_date)),
            ]);

            foreach ($request->supplier_id as $key=>$supplier_id){
               $requestProposalDefineInput[]=[
                   'request_proposal_id'=>$requestProposal->id,
                   'supplier_id'=>$supplier_id,
               ];
            }

            foreach ($request->product_id as $i=>$product_id){
                $requestProposalDetailsInput[]=[
                    'request_proposal_id'=>$requestProposal->id,
                    'product_id'=>$product_id,
                    'request_qty'=>$request->request_qty[$product_id],
                    'qty'=>$request->qty[$product_id],
                    'created_by'=>\Auth::user()->id,
                    'created_at'=>date('Y-m-d h:i'),
                    //'requisition_id'=>$requisitionItem->requisition_id,
                ];
            }

            //For update column (Is_Send) on requisition items table 
            RequisitionItem::whereIn('product_id',$request->product_id)
                                ->where('is_send','no')
                                ->update(['is_send'=>'yes']);
                
            RequestProposalDefineSupplier::insert($requestProposalDefineInput);

            RequestProposalDetails::insert($requestProposalDetailsInput);

            //Request Proposal Tracking
             \App\Models\PmsModels\RequestProposalTracking::StoreRequestProposalTracking($requestProposal->id,'RFP');

            $this->mailSendToSuppliers($requestProposal->id,$request->supplier_id,$proposalType);

            DB::commit();

            return $this->backWithSuccess('Request For Proposal Successfully Created');
        }
        catch (\Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }
    }


    public function mailSendToSuppliers($requestProposalId,$supplierIds,$proposalType=null){

        $suppliers =  Suppliers::whereIn('id',$supplierIds)->get();

        foreach ($suppliers as $key=>$supplier){

            $requestProposal=RequestProposal::with('defineToSupplier','requestProposalDetails','requestProposalDetails.product')
            ->whereHas('defineToSupplier',function ($query)use ($supplier) {
                $query->where('request_proposal_define_suppliers.supplier_id',$supplier->id);
            })
            ->find($requestProposalId);


            Mail::to($supplier->email)->send(new RequestForProposalToSupplierMail($supplier,$requestProposal,'Request For Proposal',$proposalType));

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RequestProposal  $requestProposal
     * @return \Illuminate\Http\Response
     */
    public function show(RequestProposal $requestProposal)
    {
        $title='Request Proposal Details';
         $requestProposal->load('defineToSupplier','defineToSupplier.supplier','requestProposalDetails','requestProposalDetails.product','requestProposalDetails.product.category','createdBy');

        return view('pms.backend.pages.rfp.request-proposal-details', compact('title','requestProposal'));
    }

  
    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function sendToPurchase($req_id)
    {
        try {

            $title = 'Requisition send to purchase';

            $requisition = RequisitionItem::whereHas('requisition', function($query){
                return $query->where('delivery_status','processing');
            })
            ->where('requisition_id',$req_id)
            ->get();


            $getProductIds=[];
            foreach($requisition as $data){
                array_push($getProductIds,$data->product_id);
            }

            $selectSupplierIds=DB::table('products_supplier')->whereIn('product_id',$getProductIds)->groupBy('supplier_id')->get(['supplier_id']);

            $getSupplierIds=[];
            foreach($selectSupplierIds as $data){
                array_push($getSupplierIds,$data->supplier_id);
            }

            $supplierList= Suppliers::whereIn('id',$getSupplierIds)->pluck('name','id')->all();


            if ($requisition->count() > 0) {

                return view('pms.backend.pages.store.store-inventory-purchase', compact('title','requisition','req_id','supplierList'));
            }else{
                return $this->backWithError('Already purchase this requisition.');
            }

        }catch (\Throwable $th){

            return $this->backWithError($th->getMessage());
        }
    }


    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function sendToPurchaseSubmit(Request $request)
    {   
        $this->validate($request, [
            'request_date' => ['required', 'date'],
            'reference_no' => ['required','max:15','unique:request_proposals'],
            'supplier_id' => ['required'],
            "supplier_id.*"  => "exists:suppliers,id",
            "product_id"    => "required|min:1",
        ]);

        try {

            // Transaction Start Here
            DB::beginTransaction();

            //update requistion id
            $requisition=Requisition::where(['id'=>$request->requisition_id,'status'=>1,'approved_id'=>1,'is_send_to_rfp'=>'yes','delivery_status'=>'processing'])
            ->update(['delivery_status'=>'rfp','updated_by'=>\Auth::user()->id,
                    'updated_at'=>date('Y-m-d h:i')]);


            $requestProposal=RequestProposal::create([
                'type'=>'direct-purchase',
                'reference_no'=>$request->reference_no,
                'request_date'=>date('Y-m-d',strtotime($request->request_date)),
            ]);

            //Requistion Tracking
            // \App\Models\PmsModels\RequisitionTracking::storeRequisitionTracking($request->requisition_id,'direct-purchase');

        //Generate Quotation
        $maxId=Quotations::max('id')+1;
        $refNo=Quotations::REFNO+$maxId;
        $refNo='QG-'.$refNo;

        $quotationFilePath='';
        if ($request->hasFile('quotation_file'))
        {
            $quotationFilePath=$this->fileUpload($request->file('quotation_file'),'upload/quotation/pdf-file');
        }
        $quotation=Quotations::create([
            'supplier_id'=>$request->supplier_id,
            'request_proposal_id'=>$requestProposal->id,
            'reference_no'=>$refNo,
            'quotation_date'=>date('Y-m-d',strtotime($request->request_date)),
            'total_price'=>$request->sum_of_subtoal,
            'discount'=>$request->discount==null?0:$request->discount,
            'vat'=>$request->vat==null?0:$request->vat,
            'gross_price'=>$request->gross_price,
            'status'=>'active',
            'type'=>'direct-purchase',
            'quotation_file'=>$quotationFilePath
        ]);

        foreach ($request->product_id as $i=>$product_id){
                $quotationItemsInput[]=[
                    'quotation_id'=>$quotation->id,
                    'product_id'=>$product_id,
                    'unit_price'=>$request->unit_price[$product_id],
                    'qty'=>$request->qty[$product_id],
                    'sub_total_price'=>$request->sub_total_price[$product_id],
                    'discount'=>0,
                    'vat'=>0,
                    'total_price'=>$request->sub_total_price[$product_id],
                    'created_at'=>date('Y-m-d h:i'),
                ];

                $requestProposalDetailsInput[]=[
                    'request_proposal_id'=>$requestProposal->id,
                    'product_id'=>$product_id,
                    'request_qty'=>$request->qty[$product_id],
                    'qty'=>$request->qty[$product_id],
                    'created_by'=>\Auth::user()->id,
                    'created_at'=>date('Y-m-d h:i'),
                ];
            }

            //For update column (Is_Send) on requisition items table 
            RequisitionItem::where('requisition_id',$request->requisition_id)->whereIn('product_id',$request->product_id)->where('is_send','no')->update(['is_send'=>'yes']);
            RequestProposalDefineSupplier::insert([
               'request_proposal_id'=>$requestProposal->id,
               'supplier_id'=>$request->supplier_id,
           ]);
            //Add request proposal details data
            RequestProposalDetails::insert($requestProposalDetailsInput);
            //Add quotation items data
           QuotationsItems::insert($quotationItemsInput);

            DB::commit();

            return $this->redirectBackWithSuccess('Successfully send to purchase department!!','pms.quotation.quotations.index');

        }catch (\Throwable $th){

            //If there are any exceptions, rollback the transaction`
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }

        return back();
        
    }

    //complete quotation generate

    public function rfpQuotationgenerateComplete(Request $request)
    {
        $response=[];

        $data=RequestProposal::where('id',$request->req_proposal_id)->first();

        //Start transaction
        DB::beginTransaction();

        try {
            if(!empty($data))
            {
                $data->quotation_generate_type = 'complete';
                $data->save();
                //Commit data
                DB::commit();

                $response['result'] = 'success';
                $response['message'] = 'Successfully Complete This Request Proposal!!';
            }else{
                $response['result'] = 'error';
                $response['message'] = 'Data not found.!!';
            }

        }catch (\Throwable $th){
            //If process has any problem then rollback the data
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }

        return $response;

    }
}
