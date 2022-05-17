<?php

namespace App\Http\Controllers\Pms\Grn;

use App\Http\Controllers\Controller;
use App\Models\PmsModels\InventoryModels\InventoryActionControl;
use App\Models\PmsModels\Product;
use App\Models\PmsModels\Warehouses;
use Illuminate\Http\Request;
use App\Models\PmsModels\Purchase\PurchaseOrder;
use App\Models\PmsModels\Purchase\PurchaseOrderItem;
use App\Models\PmsModels\Grn\GoodsReceivedNote;
use App\Models\PmsModels\Grn\GoodsReceivedSummary;
use App\Models\PmsModels\Grn\GoodsReceivedItem;
use Illuminate\Support\Facades\Mail;
use DB,Auth;

class GRNController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title="Good Receive Note List";
        $query=new GoodsReceivedNote();
        $grns = $query->with('relPurchaseOrder','relPurchaseOrder.relPurchaseOrderItems','relGoodsReceivedItems')
             ->orderBy('id','DESC');//->paginate(50);

        $purchaseOrder='';
         if (isset($request->po_id)){
             $purchaseOrder=PurchaseOrder::findOrFail($request->po_id);
             $title.=" for P.O. Reference: $purchaseOrder->reference_no";

             $grns->where('goods_received_notes.purchase_order_id',$request->po_id);
         }
        $grns=$grns->get();

        return view('pms.backend.pages.grn.index',compact('title','grns','purchaseOrder'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function poListIndex()
    {
        try{

            $title="GRN Purchase Order List";

            $data = PurchaseOrder::with('relPurchaseOrderItems','relGoodReceiveNote','relGoodReceiveNote.relGoodsReceivedItems')->where('is_send','yes')->paginate(100);

            if (count($data)>0){
                $this->calculateGrnQtyAgainstPurchaseOrder($data);
            }
            return view('pms.backend.pages.grn.po-index',compact('title','data'));

        }catch(\Throwable $th){
            return $this->backWithWarning($th->getMessage());
        }

    }

    public function calculateGrnQtyAgainstPurchaseOrder($purchaseOrder){
        foreach ($purchaseOrder as $key=>$val){
            if (isset($val->relGoodReceiveNote)){

                $grnQty= $val->relGoodReceiveNote->each(function ($item,$i){
                    $item['grn_qty']= $item->relGoodsReceivedItems->sum('qty');
                });
                $val['total_grn_qty']=$val->relGoodReceiveNote->sum('grn_qty');
            }
        }

        return $purchaseOrder;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createGRN($id)
    {
        try{
            $purchaseOrder = PurchaseOrder::with('relPurchaseOrderItems','relGoodReceiveNote','relGoodReceiveNote.relGoodsReceivedItems')->where(['id'=>$id,'is_send'=>'yes'])->first();


            if (count($purchaseOrder->relGoodReceiveNote)>0){

                    $grnQty= $purchaseOrder->relPurchaseOrderItems->each(function ($item,$i)use($purchaseOrder){

                        $purchaseOrder->relGoodReceiveNote->each(function ($grnItem, $k)use($item){
                            $item['grn_qty']+=$grnItem->relGoodsReceivedItems->where('product_id',$item->product_id)->sum('qty');
                        });
                    });
            }

            //return $purchaseOrder;

            $result=$this->checkOrderQtyAndReceiveQty($purchaseOrder);
            if ($result==-1){
                return $this->backWithWarning('Received qty can not be greater than purchase order qty');
            }elseif ($result==0){
                return $this->backWithWarning('Total Product (s) Already Received');
            }


           $wareHouses = Warehouses::pluck('name','id');

            $title="GRN Create";
            $prefix='GRN-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
            $refNo=uniqueCode(16,$prefix,'goods_received_notes','id');

            return view('pms.backend.pages.grn.create',compact('title','purchaseOrder','refNo','wareHouses'));

        }catch(Throwable $th){
            return $this->backWithWarning($th->getMessage());
        }
    }

    public function checkOrderQtyAndReceiveQty($purchaseOrder){

        $grns = GoodsReceivedNote::with('relGoodsReceivedItems')
            ->whereIn('purchase_order_id',[$purchaseOrder->id])->get();

        $totalReceiveQty=0;
        foreach ($grns as $grn){
            $totalReceiveQty+=$grn->relGoodsReceivedItems->sum('qty');
        }

        $totalOrderQty=$purchaseOrder->relPurchaseOrderItems->sum('qty');

        return ($totalOrderQty<=>$totalReceiveQty);

        /*if (count($purchaseOrder->relPurchaseOrderItems)>0){
            foreach ($purchaseOrder->relPurchaseOrderItems as $item){
                $totalReceiveQty+=$item->relReceiveProduct->sum('qty');
            }
        }*/
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //return $request;
        $this->validate($request, [
            'received_date' => 'required|date',
            'reference_no'  => "required|unique:goods_received_notes|max:100",
            'challan'  => "required|max:100",
            'delivery_by'  => "nullable|max:200",
            'note'  => "nullable|max:500",
            'total_price'  => "required",
            'gross_price'  => "required",
            'challan_file' => 'image|mimes:jpeg,jpg,png,gif,pdf|nullable|max:5048',


        ]);

        $purchaseOrder=PurchaseOrder::with('relPurchaseOrderItems')->findOrFail($request->purchase_order_id);


        DB::beginTransaction();
        try{

            $challanFile='';
            if ($request->hasFile('challan_file'))
            {
                $challanFile=$this->fileUpload($request->file('challan_file'),'upload/grn/challan-file');
            }

            $goodsReceivedNote=GoodsReceivedNote::create(
                [
                    'purchase_order_id'=>$request->purchase_order_id,
                    'reference_no'=>$request->reference_no,
                    'challan'=>$request->challan,
                    'challan_file'=>$challanFile,
                    'total_price'=>$request->total_price,
                    'discount'=>$request->discount??0,
                    'vat'=>$request->vat??0,
                    'gross_price'=>$request->gross_price,
                    'received_date'=>date('Y-m-d',strtotime($request->received_date)),
                    'delivery_by'=>$request->delivery_by,
                    'receive_by'=>\Auth::user()->id,
                    'note '=>$request->note,
                    'created_by'=>\Auth::user()->id,
                ]
            );

            foreach ($request->product_id as $key=>$productId){

                if ($request->qty[$productId]!=0) {
                    $goodsReceiveItems[] = [
                        'goods_received_note_id' => $goodsReceivedNote->id,
                        'product_id' => $productId,
                        'unit_amount' => $request->unit_price[$productId],
                        'sub_total' => $request->unit_amount[$productId],
                        'qty' => $request->qty[$productId],
                        'total_amount' => $request->unit_amount[$productId],
                    ];

//                    $product=Product::findOrFail($productId);
//                    $wareHouse=Warehouses::findOrFail($request->warehouse_id[$productId]);

//                    new InventoryActionControl($product,$wareHouse,$request->unit_price[$productId],$request->qty[$productId],'active',$request->reference_no);
                }
            }

            GoodsReceivedItem::insert($goodsReceiveItems);

            $result=$this->changeReceiveStatus($request,$goodsReceivedNote);

            if ($result===false){
                return $this->backWithWarning('Received qty greater than purchase order qty');
            }

            DB::commit();

            return redirect('pms/supplier/rating/'.$purchaseOrder->relQuotation->supplier_id.'/'.$goodsReceivedNote->id)->with(['message'=>'GRN Successful','alert-type'=>'success']);

        }catch (Exception $e){
            DB::rollback();
            return $this->backWithError($e->getMessage());
        }


    }

    public function changeReceiveStatus($request,$goodsReceivedNote){

        $purchaseOrder=PurchaseOrder::with('relPurchaseOrderItems')->findOrFail($request->purchase_order_id);

        $totalOrderQty=$purchaseOrder->relPurchaseOrderItems->sum('qty');

        $grns = GoodsReceivedNote::with('relGoodsReceivedItems')
            ->whereIn('purchase_order_id',[$request->purchase_order_id])->get();

        $totalReceiveQty=0;
        foreach ($grns as $grn){
            $totalReceiveQty+=$grn->relGoodsReceivedItems->sum('qty');
        }

        if ($totalOrderQty<$totalReceiveQty){
            return false;
        }

        if ($totalOrderQty==$totalReceiveQty){
            $goodsReceivedNote->update(['received_status'=>'full']);
            return true;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $response=[];

        try{
            $modal = GoodsReceivedNote::with('relPurchaseOrder','relPurchaseOrder.relQuotation','relGoodsReceivedItems','relGoodsReceivedItems.relProduct')->findOrFail($id);
            if ($modal) {
                $body = \Illuminate\Support\Facades\View::make('pms.backend.pages.grn.show',
                    ['grn'=> $modal]);
                $contents = $body->render();

                $response['result'] = 'success';
                $response['body'] = $contents;
                $response['message'] = 'Successfully Generated PO';
            }else{
                $response['result'] = 'error';
                $response['message'] = 'GRN not found!!';
            }

        }catch(\Throwable $th){
            $response['result'] = 'error';
            $response['message'] = $th->getMessage();
        }

        return $response;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function purchaseOrderListAgainstGrn(){
        try{
           $title="Purchase Order List Against GRN";

            $purchaseOrdersAgainstGrn = PurchaseOrder::with('relPurchaseOrderItems','relGoodReceiveNote','relGoodReceiveNote.relGoodsReceivedItems')->where('is_send','yes')->whereHas('relGoodReceiveNote',function ($query){
                $query->whereRaw('purchase_orders.id=goods_received_notes.purchase_order_id');
            })->paginate(100);

            if (count($purchaseOrdersAgainstGrn)>0){
                $this->calculateGrnQtyAgainstPurchaseOrder($purchaseOrdersAgainstGrn);
            }

            return view('pms.backend.pages.grn.po-list-against-grn',compact('title','purchaseOrdersAgainstGrn'));

        }catch(\Throwable $th){
            return $this->backWithWarning($th->getMessage());
        }
    }

}
