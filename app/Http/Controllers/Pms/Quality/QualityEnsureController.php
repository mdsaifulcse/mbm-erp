<?php

namespace App\Http\Controllers\Pms\Quality;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

use App\Models\PmsModels\InventoryModels\InventoryActionControl;
use App\Models\PmsModels\Product;
use App\Models\PmsModels\Warehouses;
use App\Models\PmsModels\PurchaseReturn;
use App\Models\PmsModels\Purchase\PurchaseOrder;
use App\Models\PmsModels\Purchase\PurchaseOrderItem;
use App\Models\PmsModels\Grn\GoodsReceivedNote;
use App\Models\PmsModels\Grn\GoodsReceivedSummary;
use App\Models\PmsModels\Grn\GoodsReceivedItem;

use Illuminate\Support\Facades\Mail;
use DB,Auth,Session,redirect;

class QualityEnsureController extends Controller
{
    /**
     * Display a approved listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{

            $title="Quality Ensure Approval List";

            $approval_list = GoodsReceivedNote::whereHas('relGoodsReceivedItems', function($query){
                return $query->where('quality_ensure','approved');
            })->paginate(30);

            return view('pms.backend.pages.quality.approved-index',compact('title','approval_list'));

        }catch(\Throwable $th){
         return $this->backWithError($th->getMessage());
     }
 }

   /**
     * Show the Grn Wise Approved Item List.
     *
     * @return \Illuminate\Http\Response
     */

   public function ensureCheck($id)
   {
    try{

        $title="Quality Ensure Check";

        $grn = GoodsReceivedNote::with('relPurchaseOrder','relPurchaseOrder.relQuotation','relGoodsReceivedItems','relGoodsReceivedItems.relProduct','relGoodsReceivedItems.relPurchaseOrderReturns')
        ->where('id',$id)
        ->first();
        
        $grn->rel_goods_received_items = $grn->relGoodsReceivedItems()->whereIn('quality_ensure',['pending'])->get();

        $wareHouses = Warehouses::select('name','id')->get();

        return view('pms.backend.pages.quality.pending-index',compact('title','grn','wareHouses'));
    }catch(\Throwable $th){
        return $this->backWithError($th->getMessage());
    }
}


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {

        $model = GoodsReceivedItem::where('id',$request->id)->first();

        $product=Product::findOrFail($model->product_id);
        if ($request->warehouse_id) {
            $wareHouse=Warehouses::findOrFail($request->warehouse_id);
        }

        if(isset($model->id) && $request->quality_ensure==='approved'){

            DB::beginTransaction();
            try{
                $new_text = 'Approved';
                $update=$model->update([
                    'quality_ensure' => $request->quality_ensure,
                    'received_qty' =>  number_format($model->qty,2),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id
                ]);

                new InventoryActionControl($product,$wareHouse,$model->total_amount,$model->qty,'active',$model->relGoodsReceivedNote->reference_no);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'new_text' => $new_text,
                    'message' => 'Successfully Updated this Item Quality Status!!'
                ]);
            }catch (\Throwable $th){
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => $th->getMessage()
                ]);
            }

        }elseif(isset($model->id) && $request->quality_ensure==='return-change' || $request->quality_ensure==='return'){

            if (($model->qty) < $request->return_qty) {
                return $this->backWithWarning('Your return qty is greater then maximum qty');
            }

            DB::beginTransaction();
            try{
                $update=$model->update([
                    'quality_ensure' => $request->quality_ensure,
                    'received_qty' =>  $model->qty-$request->return_qty,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id
                ]);

                if ($update) {
                    PurchaseReturn::create([
                        'goods_received_item_id'=>$model->id,
                        'return_note' => $request->return_note,
                        'return_qty' => $request->return_qty,
                        'status' => $request->quality_ensure,
                    ]);

                    new InventoryActionControl($product,$wareHouse,($model->qty-$request->return_qty)*$model->unit_amount,$model->qty-$request->return_qty,'active',$model->relGoodsReceivedNote->reference_no);
                }

                DB::commit();

                //Session::flash('message', 'Successfully Updated this Item Quality Status!!');
                //return redirect('pms/quality-ensure/ensure-check/'.$model->goods_received_note_id);
                return $this->backWithSuccess('Successfully Updated this Item Quality Status!!');

            }catch (Throwable $th){
                DB::rollback();
                return $this->backWithError($th->getMessage());
            }
        }

        return back();
    }


    /**
    * Show the Grn Wise Approved Item List.
    *
    * @return \Illuminate\Http\Response
    */

    public function grnWiseApprovedItemList($id)
    {
        try{

            $title="Quality Ensure Approval List";

            $approval_list = GoodsReceivedItem::where('goods_received_note_id',$id)->where('quality_ensure','approved')->paginate(30);

            return view('pms.backend.pages.quality.approved-list',compact('title','approval_list'));

        }catch(\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }


    /**
    * Show the Grn Wise Return List.
    *
    * @return \Illuminate\Http\Response
    */


    public function returnlList()
    {
        try{

            $title="Quality Ensure Return List";

            $return_list = GoodsReceivedNote::whereHas('relGoodsReceivedItems', function($query){
                return $query->where('quality_ensure','return');
            })->paginate(30);

            return view('pms.backend.pages.quality.return-index',compact('title','return_list'));

        }catch(\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
    * Show the Grn Wise Single Return Item List.
    *
    * @return \Illuminate\Http\Response
    */

    public function grnWiseReturnItemList($id)
    {
        try{

            $title="Quality Ensure Return List";

            $return_list = GoodsReceivedItem::where('goods_received_note_id',$id)->where('quality_ensure','return')->paginate(30);

            return view('pms.backend.pages.quality.return-list',compact('title','return_list'));

        }catch(\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }


    /**
    * Show the Grn Wise Return Change List.
    *
    * @return \Illuminate\Http\Response
    */

    public function returnChangeList()
    {
        try{

            $title="Quality Ensure Return Chnage List";

            $return_change_list = GoodsReceivedNote::whereHas('relGoodsReceivedItems', function($query){
                return $query->where('quality_ensure','return-change');
            })->paginate(30);

            return view('pms.backend.pages.quality.return-change-index',compact('title','return_change_list'));

        }catch(\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
    * Show the Grn Wise Return Item List.
    *
    * @return \Illuminate\Http\Response
    */
    public function grnWiseReturnChangeItemList($id)
    {
        try{

            $title="Quality Ensure Return Change List";

            $return_change_list = GoodsReceivedItem::where('goods_received_note_id',$id)
            ->where('quality_ensure','return-change')->paginate(30);
            $wareHouses = Warehouses::select('name','id')->get();


            return view('pms.backend.pages.quality.return-change-list',compact('title','return_change_list','wareHouses'));

        }catch(\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function returnChangeReceived(Request $request)
    {    

        try{

            DB::beginTransaction();
            foreach($request->id as $key=>$id){

                $model=GoodsReceivedItem::where(['id'=>$id,'quality_ensure'=>'return-change'])->first();

                $product=Product::findOrFail($model->product_id);
                if ($request->warehouse_id[$key]) {
                    $wareHouse=Warehouses::findOrFail($request->warehouse_id[$key]);
                }

                if(isset($model->id) && $request->status==='received'){

                    if (($model->qty-$model->received_qty) < $request->received_qty[$key]) {
                        return $this->backWithWarning('Your return qty is greater then maximum qty');
                    }

                    $total_received_qty = $model->received_qty+$request->received_qty[$key];

                    if ($total_received_qty==$model->qty) {
                     $quality_ensure='approved';
                 }else{
                     $quality_ensure='return-change';
                 }


                 $update=$model->update([
                    'quality_ensure' => $quality_ensure,
                    'received_qty' =>  $total_received_qty,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id
                ]);

                 if ($update) {
                    PurchaseReturn::create([
                        'goods_received_item_id'=>$model->id,
                        'return_note' => $request->return_note,
                        'return_qty' => $request->received_qty[$key],
                        'status' => $request->status,
                    ]);

                    new InventoryActionControl($product,$wareHouse,($request->received_qty[$key]*$model->unit_amount),$request->received_qty[$key],'active',$model->relGoodsReceivedNote->reference_no);
                }
            }
        }

        DB::commit();
        return $this->backWithSuccess('Successfully Updated this Item Quality Status!!');

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
    public function show($id)
    {
        //
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
}
