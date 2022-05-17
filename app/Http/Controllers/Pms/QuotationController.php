<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\PmsModels\Product;
use App\Models\PmsModels\Suppliers;
use App\Models\PmsModels\Purchase\PurchaseOrder;
use App\Models\PmsModels\Purchase\PurchaseOrderItem;
use App\Models\PmsModels\Rfp\RequestProposal;
use App\Models\PmsModels\Rfp\RequestProposalDetails;
use App\Models\PmsModels\Rfp\RequestProposalDefineSupplier;
use App\Models\PmsModels\Quotations;
use App\Models\PmsModels\QuotationsItems;
use App\Models\PmsModels\SupplierPaymentTerm;
use App,DB;
use Illuminate\Support\Facades\Auth;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            $title='Quotations List';
            $quotations=Quotations::where('status','active')->paginate(30);
            return view('pms.backend.pages.quotation.index', compact('title','quotations'));
            }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function analysisIndex()
    {
        try {

            $title='Quotations Analysis';
            $quotations=Quotations::where('status','active')->where('is_approved','pending')->groupBy('request_proposal_id')->paginate(30);

            return view('pms.backend.pages.quotation.analysis-index', compact('title','quotations'));
            }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }

    }

    public function quotationItems($quotation_id)
    {
       
        $quotations=Quotations::where('id',$quotation_id)->where('status','active')->first();
        return view('pms.backend.pages.quotation.item-show', compact('quotations'));
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function quotationGenerate($proposal_id)
     {
        try {

            $title = 'Quotation Generate';

            $prefix='QG-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
            $refNo=uniqueCode(14,$prefix,'quotations','id');

            $supplierPaymentTerms=supplierPaymentTerm();
            $requestProposal=RequestProposal::where('id',$proposal_id)->with('defineToSupplier','requestProposalDetails','requestProposalDetails.product','createdBy')->first();
            $quotationSupplier=Quotations::where('request_proposal_id',$proposal_id)->select('supplier_id')->get();

            $quotationSupplier_array = array();
            foreach($quotationSupplier as $values){
                array_push($quotationSupplier_array,$values->supplier_id);
            }

            return view('pms.backend.pages.quotation.create', compact('title','requestProposal','refNo','supplierPaymentTerms','quotationSupplier_array'));

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
    public function store(Requests\Pms\QuotationRequest $request)
    {   
        //return $request->all();
        $type=$request->type;
        $modal=Quotations::where([
            'supplier_id'=>$request->supplier_id,
            'request_proposal_id'=>$request->request_proposal_id,
            'type'=>$type
        ])->first();

        if(!empty($modal)){
            return $this->backWithError('Already generate a quotation using this supplier!!');
        }

        DB::beginTransaction();
        try {

            $quotationFilePath='';
            if ($request->hasFile('quotation_file'))
            {
                $quotationFilePath=$this->fileUpload($request->file('quotation_file'),'upload/quotation/pdf-file');
            }

            $quotation=Quotations::create([
                'supplier_id'=>$request->supplier_id,
                'request_proposal_id'=>$request->request_proposal_id,
                'reference_no'=>$request->reference_no,
                'quotation_date'=>date('Y-m-d',strtotime($request->quotation_date)),
                'total_price'=>$request->sum_of_subtoal,
                'discount'=>$request->discount==null?0:$request->discount,
                'vat'=>$request->vat==null?0:$request->vat,
                'gross_price'=>$request->gross_price,
                'status'=>'active',
                'type'=>$type,
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
            }

           QuotationsItems::insert($quotationItemsInput);

           //Request Proposal Tracking
             \App\Models\PmsModels\RequestProposalTracking::StoreRequestProposalTracking($request->request_proposal_id,'Quotation-Generated');

            if (!is_null($request->payment_term_id)) {
                $this->storeSupplierPaymentTerm($quotation->id, $request);
            }

            DB::commit();

            return $this->backWithSuccess('Quotation Generated Successfully');

        }
        catch (Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }
    }

    public function storeSupplierPaymentTerm($quotationId,$request){

       SupplierPaymentTerm::create(
            [
                'quotation_id'=>$quotationId,
                'supplier_id'=>$request->supplier_id,
                'payment_term_id'=>$request->payment_term_id,
                'payment_percent'=>$request->payment_percent??0,
                'remarks'=>$request->remarks,
            ]
        );

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function compare($request_proposal_id)
    {
        try {

            $title='Quotations Compare Analysis';
            $quotations=Quotations::where('status','active')
                        ->where('is_approved','pending')
                        ->where('request_proposal_id',$request_proposal_id)
                        ->orderby('gross_price','asc')
                        ->get();

            return view('pms.backend.pages.quotation._compare', compact('title','quotations'));

            }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }

    }



    public function compareStore(Request $request)
    {   
        if(empty($request->quotation_id)){

            return $this->backWithError('Sorry Data Not Found!!');
        }
            DB::beginTransaction();
            try {

                foreach ($request->quotation_id as $key=>$quotation_id){

                    $modal=Quotations::where(['id'=>$quotation_id,'request_proposal_id'=>$request->request_proposal_id,'is_approved'=>'pending'])->first();

                    if(isset($modal)){

                        $modal->is_approved = 'processing';
                        $modal->note = $request->note[$key];
                        $modal->save();
                    }

                    /*if (!is_null($request->payment_term_id)) {
                        $quotationWiseSupplierPayment = SupplierPaymentTerm::firstOrNew(array('quotation_id' => $quotation_id,'supplier_id'=>$modal->supplier_id));

                        $quotationWiseSupplierPayment->quotation_id=$quotation_id;
                        $quotationWiseSupplierPayment->supplier_id=$modal->supplier_id;
                        $quotationWiseSupplierPayment->payment_term_id=$request->payment_term_id[$key];
                        $quotationWiseSupplierPayment->payment_percent=$request->payment_percent??0;
                        $quotationWiseSupplierPayment->remarks=$request->note[$key];
                        $quotationWiseSupplierPayment->save();
                    }*/
                }

                DB::commit();
                return $this->backWithSuccess('Successfully Send for approval');

            }catch (Throwable $th){

                DB::rollback();
                return $this->backWithError($th->getMessage());
            }
    }
   

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function approvalList()
    {
        $title='Quotations Request For Approved';

        try {

            $datas=Quotations::where(['status'=>'active','is_po_generate'=>'no'])
            ->whereNotIn('is_approved',['pending','approved'])
            ->orderBy('id','desc')
            ->groupBy('request_proposal_id')
            ->get();

            $quotationList = [];
            foreach ($datas as $data){
                foreach (Auth::user()->relApprovalRange as $range){
                    if ($range->min_amount <= $data->relQuotationItems->sum('total_price') && $range->max_amount >= $data->relQuotationItems->sum('total_price')){
                        $quotationList[] = $data;
                    }
                }
            }
            $quotationList = $this->paginate($quotationList, 30);

            return view('pms.backend.pages.quotation.approval-index', compact('title','quotationList'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function compareView($request_proposal_id)
    {
        try {

            $title='Quotations Compare Analysis';
            $quotations=Quotations::where('status','active')
                        ->whereIn('is_approved',['processing','halt'])
                        ->where('request_proposal_id',$request_proposal_id)
                        ->orderby('gross_price','asc')
                        ->get();

            //return response()->json($quotations);

            return view('pms.backend.pages.quotation._compare_view', compact('title','quotations'));

            }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }

    }

    public function approved(Request $request)
    {   

        DB::beginTransaction();
        try {

                $modal= new Quotations();

                $approvedCount = $modal->where('request_proposal_id',$request->request_proposal_id)->where('is_approved','approved')->count();

                 if ($approvedCount !=0) {
                   return $this->backWithWarning('Already approved One Quotations!!');
                }

                $quotation = $modal->where('request_proposal_id',$request->request_proposal_id)->where('id',$request->quotation_id)->first();

                if(isset($quotation)){

                    $quotation->is_approved = 'approved';
                    $quotation->remarks = $request->remarks;
                    $quotation->save();
                }

                //Request Proposal Tracking
             \App\Models\PmsModels\RequestProposalTracking::StoreRequestProposalTracking($request->request_proposal_id,'Quotation-Approved');

                DB::commit();
                return $this->backWithSuccess('Successfully approval !!');
            

        }catch (Throwable $th){

            DB::rollback();
            return $this->backWithError($th->getMessage());
        }
        return $this->backWithError('Sorry Data Not Found!!');
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleQuotationStatus(Request $request)
    {
        $row = Quotations::where('id',$request->id)->first();
        if(isset($row->id)){

            $new_status = $request->status;
            $new_text = $new_status == 'approved' ? 'Approved' : (($new_status == 'halt')? 'Halt' : 'Pending');

            $update = $row->update([
                            'is_approved' => $new_status,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => Auth::user()->id
                        ]);

            if($update){
                return response()->json([
                    'success' => true,
                    'new_text' => $new_text,
                    'message' => 'Data has been updated!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Something Went Wrong!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Data not found!'
        ]);
    }

    public function haltStatus(Request $request){

        try{

                $row = Quotations::findOrFail($request->id);

                $row->update([
                    'remarks' => $request->remarks,
                    'is_approved' => 'halt',
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id
                ]);

                //Request Proposal Tracking
             \App\Models\PmsModels\RequestProposalTracking::StoreRequestProposalTracking($row->request_proposal_id,'Quotation-Halt',$request->remarks);


            return $this->backWithSuccess('Quotation Successfully Halt!!');
        }catch (\Throwable $th){

            return $this->backWithError($th->getMessage());
        }
    }


    public function search(Request $request)
    {
        $response = [];

        $from_date=date('Y-m-d',strtotime($request->from_date));
        $to_date=date('Y-m-d',strtotime($request->to_date));

        $is_approved=$request->is_approved;
        $is_po_generate=$request->is_po_generate;

        $datas=Quotations::whereDate('quotation_date', '>=', $from_date)
            ->whereDate('quotation_date', '<=', $to_date)
            ->when($is_approved, function($query) use($is_approved){
                return $query->where('is_approved',$is_approved);
            })
            ->where(['is_po_generate'=>$is_po_generate,'status'=>'active'])
            ->paginate(100);

        $quotationList = [];
        foreach ($datas as $data){
            foreach (Auth::user()->relApprovalRange as $range){
                if ($range->min_amount <= $data->relQuotationItems->sum('total_price') && $range->max_amount >= $data->relQuotationItems->sum('total_price')){
                    $quotationList[] = $data;
                }
            }
        }
        $quotationList = $this->paginate($quotationList, 100);

        try {

            if(count($quotationList)>0)
            {
                $body = \Illuminate\Support\Facades\View::make('pms.backend.pages.quotation._quotation-list-search',
                    ['quotationList'=> $quotationList]);
                $contents = $body->render();

                $response['result'] = 'success';
                $response['body'] = $contents;
            }else{

                $response['result'] = 'error';
                $response['message'] = 'Data not found.!!';
            }

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }

        return $response;
    }


    public function generatePoList()
    {
        $title='Quotations Approved List';

        try {

            $quotationList=Quotations::where(['status'=>'active', 'is_approved'=>'approved', 'is_po_generate'=>'no'])
            ->orderBy('id','desc')
            ->paginate(30);

            return view('pms.backend.pages.quotation.generate-po-list', compact('title','quotationList'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function generatePoStore(Request $request)
    {   
        $response = [];

        $this->validate($request, [
            'id' => ['required']
        ]);

        try{

            $modal = Quotations::findOrFail($request->id);
            if ($modal) {
                $checkPo=PurchaseOrder::where('quotation_id',$modal->id)->count();
                if ($checkPo > 0) {
                    $response['result'] = 'warning';
                    $response['message'] = 'Already generate PO!!';
                }else{

                    $po_data = new PurchaseOrder();
                    $po_data->quotation_id = $modal->id;
                    $po_data->reference_no = 'PO-'.$modal->reference_no;
                    $po_data->po_date = date('Y-m-d');
                    $po_data->total_price = $modal->total_price;
                    $po_data->discount = $modal->discount;
                    $po_data->vat = $modal->vat;
                    $po_data->gross_price = $modal->gross_price;
                    $po_data->remarks = $modal->remarks;
                    $po_data->save();

                    foreach($modal->relQuotationItems as $key=> $values){

                        $po_items = new PurchaseOrderItem();
                        $po_items->po_id=$po_data->id; 
                        $po_items->product_id=$values->product_id; 
                        $po_items->unit_price=$values->unit_price; 
                        $po_items->qty=$values->qty;
                        $po_items->sub_total_price=$values->sub_total_price;
                        $po_items->discount=$values->discount;
                        $po_items->vat=$values->vat;
                        $po_items->total_price = $values->vat;
                        $po_items->save();
                    }

                    $modal->relRequestProposal->relQuotations->each->update(['is_po_generate'=>'yes']);
                    $response['result'] = 'success';
                    $response['message'] = 'Successfully Generated PO';
                }
            }else{
               $response['result'] = 'error';
               $response['message'] = 'Quotations not found!!';
                
            }

            //Request Proposal Tracking
             \App\Models\PmsModels\RequestProposalTracking::StoreRequestProposalTracking($modal->request_proposal_id,'PO-Generate');
           
               
        }catch (\Throwable $th){
            //return response()->json($th->getMessage());

            $response['result'] = 'error';
               $response['message'] = $th->getMessage();
        }

        return $response;

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function proposalDetailsView($id)
    {
       $title='Requests Proposal Details';

        try {

             $requestProposal=RequestProposal::where('id',$id)->with('defineToSupplier','requestProposalDetails','requestProposalDetails.product','createdBy')->first();

            return view('pms.backend.pages.rfp.request-proposal-details', compact('title','requestProposal'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

}
