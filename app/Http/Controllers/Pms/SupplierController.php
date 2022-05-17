<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Models\Merch\Supplier;
use App\Models\PmsModels\Grn\GoodsReceivedNote;
use App\Models\PmsModels\PaymentTerm;
use App\Models\PmsModels\SupplierPaymentTerm;
use App\Models\PmsModels\SupplierRatings;
use App\Models\PmsModels\Suppliers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $title = 'Suppliers List';
            $suppliers = Suppliers::orderBy('id','DESC')->get();
            return view('pms.backend.pages.suppliers.index', compact('title','suppliers'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $title = 'Create New Supplier';
            $paymentTerms = PaymentTerm::orderBy('id','DESC')->select('id','term')->get();

            return view('pms.backend.pages.suppliers.create', compact('title','paymentTerms'));
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
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'email'],
            'phone' => ['required', 'string', 'max:14', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'mobile_no' => ['required', 'string', 'max:14', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'zipcode' => ['string', 'max:255'],
            'term_condition' => ['nullable', 'max:2000'],
            'payment_term_id' => ['array','min:1'],
            //'status' => ['string', 'max:20'],
        ]);

        DB::beginTransaction();
        try {
            $inputs = $request->all();

            $supplier=Suppliers::create($inputs);

            foreach ($request->payment_term_id as $key=>$paymentTermId){
                $paymentTermInput[]=[
                    'supplier_id'=>$supplier->id,
                    'payment_term_id'=>$paymentTermId,
                    'payment_percent'=>$request->payment_percent[$key],
                    'day_duration'=>$request->day_duration[$key],
                    'type'=>$request->type[$key],
                ];
            }

            SupplierPaymentTerm::insert($paymentTermInput);
            DB::commit();
            return $this->backWithSuccess('A supplier has been created successfully');
        }catch (\Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Suppliers $supplier)
    {
        try {
            $supplier->src = route('pms.supplier.update',$supplier->id);
            $supplier->req_type = 'put';
            $data = [
                'status' => 'success',
                'info' => $supplier
            ];

            //return $supplier;
            return response()->json($data);
        }catch (\Throwable $th){
            $data = [
                'status' => null,
                'info' => $th->getMessage()
            ];
            return response()->json($data);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Suppliers $supplier)
    {
        try {
            $title = 'Update Supplier Information';
            $paymentTerms = PaymentTerm::orderBy('id','DESC')->select('id','term')->get();
            $supplier->load('relPaymentTerms');
            return view('pms.backend.pages.suppliers.edit', compact('title','supplier','paymentTerms'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Suppliers $supplier)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'email'],
            'phone' => ['required', 'string', 'max:14', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['string', 'max:255'],
            'country' => ['string', 'max:255'],
            'zipcode' => [ 'string', 'max:255'],
            'term_condition' => ['nullable', 'max:2000'],
            'payment_term_id' => ['array','min:1'],
        ]);
        DB::beginTransaction();
        try {
            $inputs = $request->all();

             $supplier->load('relPaymentTerms');

             if (count($supplier->relPaymentTerms)>0){
                 SupplierPaymentTerm::where('supplier_id',$supplier->id)->delete();
             }

            foreach ($request->payment_term_id as $key=>$paymentTermId){
                $paymentTermInput[]=[
                    'supplier_id'=>$supplier->id,
                    'payment_term_id'=>$paymentTermId,
                    'payment_percent'=>$request->payment_percent[$key],
                    'day_duration'=>$request->day_duration[$key],
                    'type'=>$request->type[$key],
                ];
            }

            SupplierPaymentTerm::insert($paymentTermInput);


            $supplier->update($inputs);
            DB::commit();
            return $this->backWithSuccess('A supplier has been updated successfully');
        }catch (\Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Suppliers $supplier)
    {
        try {
//            $category->subCategory->each->delete();
            $supplier->delete();
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }


    public function showSupplierProfile($supplierId){

        $title = 'Supplier Profile Information';
        $supplierData = Suppliers::with('relPaymentTerms','relPaymentTerms.relPaymentTerm','SupplierRatings')->findOrFail($supplierId);
        return view('pms.backend.pages.suppliers.profile', compact('title', 'supplierData'));

    }

    public function showSupplierRatingFrom($supplierId,$grnId){

         $supplierCriteriaColumns=supplierCriteriaColumns();

        $supplierData = Suppliers::with('relPaymentTerms','relPaymentTerms.relPaymentTerm','SupplierRatings')->findOrFail($supplierId);
        $grn = GoodsReceivedNote::findOrFail($grnId);
        $title = "Give rating to supplier the ( $supplierData->name )";
        return view('pms.backend.pages.suppliers.rating', compact('title', 'supplierData','supplierCriteriaColumns','grn'));

    }

    public function storeSupplierRating(Request $request){

        DB::beginTransaction();
        try {

            $grn=GoodsReceivedNote::findOrFail($request->grn_id);
            $supplier=Suppliers::findOrFail($request->supplier_id);
            $totalColumn = ColumnCount('supplier_rattings');
            $totalScore =0;

            $ratingInput=[];
            foreach ($request->rating as $key=>$rate){

                $ratingInput[$key]=$request->rating[$key]??0;
                $totalScore+=$ratingInput[$key];
            }



            if ($grn->is_supplier_rating=='yes'){
                return $this->redirectBackWithWarning('Supplier Rating Already Submit','pms.grn.grn-process.index');
            }
            if ($totalScore<=0){
                return $this->backWithWarning('Rating is empty');
            }

            $ratingInput['created_by']=\Auth::user()->id;
            $ratingInput['supplier_id']=$request->supplier_id;
            $ratingInput['total_score']=$totalScore/$totalColumn;

            SupplierRatings::create($ratingInput);

            $grn->update(['is_supplier_rating'=>'yes']);
            DB::commit();
            return $this->redirectBackWithSuccess('Supplier Rating is successful','pms.grn.grn-process.index');

        }catch (\Throwable $th){
            DB::rollback();
            return response()->json($th->getMessage());
        }

    }

}
