<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;

use App\Models\PmsModels\Product;
use App\Models\PmsModels\Requisition;
use App\Models\PmsModels\RequisitionDelivery;
use App\Models\PmsModels\RequisitionDeliveryItem;
use App\Models\PmsModels\RequisitionItem;
use App\Models\PmsModels\RequisitionTracking;
use App\Models\PmsModels\RequisitionType;
use App\Models\PmsModels\Suppliers;
use App\Models\PmsModels\Warehouses;
use App\Models\PmsModels\Notification;
use App\Models\PmsModels\InventoryModels\InventorySummary;
use App\Models\PmsModels\InventoryModels\InventoryDetails;
use App\Models\PmsModels\InventoryModels\InventoryLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB,Validator, Str;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($deliveryStatus=null)
    {
        try {
            $deliveryStatus=$deliveryStatus??'partial-delivered';

            $title =' Delivery list for Requisition';

            $requisitions=Requisition::with('relUsersList','requisitionItems','relRequisitionDelivery','relRequisitionDelivery.relDeliveryItems')
                ->where(['status'=>1,'delivery_status'=>$deliveryStatus])->paginate(30);


            foreach ($requisitions as $key=>$requisition){
                $requisition['requisition_qty']=$requisition->requisitionItems->sum('qty');

                $requisition->relRequisitionDelivery->each(function ($item,$i){
                    $item['delivery_qty']= $item->relDeliveryItems->sum('delivery_qty');
                });
                $requisition['total_delivery_qty']=$requisition->relRequisitionDelivery->sum('delivery_qty');
            }

            return view('pms.backend.pages.requisition-delivery.index', compact('title','requisitions'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function storeRequisitionListView()
    {
        try {
            $title = 'Store Requisition List View';

            $department=Requisition::join('users','users.id','=','requisitions.author_id')
            ->join('hr_as_basic_info','hr_as_basic_info.associate_id','=','users.associate_id')
            ->join('hr_department','hr_department.hr_department_id','=','hr_as_basic_info.as_department_id')
            ->groupBy('hr_department.hr_department_id')
            ->where(['status'=>1,'delivery_status'=>'processing','is_send_to_rfp'=>'no'])
            ->whereNotIn('delivery_status',['delivered','partial-delivered'])
            ->get(['hr_department.hr_department_id','hr_department.hr_department_name']);


            $requistion_data=Requisition::with('relUsersList')
            ->where(['status'=>1,'delivery_status'=>'processing','is_send_to_rfp'=>'no'])
            ->whereNotIn('delivery_status',['delivered','partial-delivered'])
            ->orderBy('id','DESC')
            ->paginate(30);

            return view('pms.backend.pages.store.store-requisition-list-view', compact('title','department','requistion_data'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }


    /**
     * Rfp list view serarch.
     * Search between from and to date and also user can search by employee
     */
    public function departmentWiseEmployee(Request $request)
    {
       
        $response=[];
        $response['data']='';

        $department_id = $request->department_id;


        $employee=Requisition::whereHas('relUsersList.employee',function($query) use($department_id){
                return $query->where('as_department_id',$department_id);
            })
        ->groupBy('author_id')
        ->where(['status'=>1,'delivery_status'=>'processing','is_send_to_rfp'=>'no'])
        ->whereNotIn('delivery_status',['delivered','partial-delivered'])
        ->get();

         $response['data'] .= '<option value="">--Select One--</option>';
        if (!empty($employee)) {
            foreach ($employee as $values) {
                 $response['data'] .= '<option value="' . $values->relUsersList->id . '">' . $values->relUsersList->name . '</option>';
            }
        }else{
             $response['data'] .= "<option value=''>No Employee Found!!</option>";
        }
       
        $response['result'] = 'success';
        
        return $response;

    }


     /**
     * Rfp list view serarch.
     * Search between from and to date and also user can search by employee
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function storeRequisitionListViewSearch(Request $request)
    {
        $response = [];

        $from_date=date('Y-m-d', strtotime($request->from_date));
        $to_date=date('Y-m-d', strtotime($request->to_date));

        $requisition_by=$request->requisition_by;
        $requisition_status=$request->requisition_status;

        $requistion_data=Requisition::whereDate('requisition_date', '>=', $from_date)
        ->whereDate('requisition_date', '<=', $to_date)
        ->when($requisition_by, function($query) use($requisition_by){
            return $query->where('author_id',$requisition_by);
        })
        ->where(['status'=>$requisition_status,'delivery_status'=>'processing','is_send_to_rfp'=>'no'])
        ->paginate(30);

        try {
            if(count($requistion_data)>0)
            {
                $body = \Illuminate\Support\Facades\View::make('pms.backend.pages.store.store-search-result-view',
                    ['requistion_data'=> $requistion_data]);
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

    public function rfpRequisitionList(Request $request)
    {
         try {

            $title = 'RFP Requisition List';


            $department=Requisition::join('users','users.id','=','requisitions.author_id')
            ->join('hr_as_basic_info','hr_as_basic_info.associate_id','=','users.associate_id')
            ->join('hr_department','hr_department.hr_department_id','=','hr_as_basic_info.as_department_id')
            ->groupBy('hr_department.hr_department_id')
            ->where(['status'=>1,'is_send_to_rfp'=>'yes'])
            ->whereNotIn('delivery_status',['delivered','partial-delivered'])
            ->get(['hr_department.hr_department_id','hr_department.hr_department_name']);


            $requisition =Requisition::with('relUsersList')
            ->where(['status'=>1,'is_send_to_rfp'=>'yes'])
            ->whereNotIn('delivery_status',['delivered','partial-delivered'])
            ->orderBy('id','DESC')
            ->paginate(30);

            return view('pms.backend.pages.store.store-rfp-requisition-list', compact('title','requisition','department'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Rfp list view serarch.
     * Search between from and to date and also user can search by employee
     */
    public function rfpDepartmentWiseEmployee(Request $request)
    {
       
        $response=[];
        $response['data']='';

        $department_id = $request->department_id;

        $employee=Requisition::whereHas('relUsersList.employee',function($query) use($department_id){
                return $query->where('as_department_id',$department_id);
            })
        ->where(['status'=>1,'is_send_to_rfp'=>'yes'])
        ->whereNotIn('delivery_status',['delivered','partial-delivered'])
        ->groupBy('author_id')
        ->get();

         $response['data'] .= '<option value="">--Select One--</option>';
        if (!empty($employee)) {
            foreach ($employee as $values) {
                 $response['data'] .= '<option value="' . $values->relUsersList->id . '">' . $values->relUsersList->name . '</option>';
            }
        }else{
             $response['data'] .= "<option value=''>No Employee Found!!</option>";
        }
       
        $response['result'] = 'success';
        
        return $response;

    }

     /**
     * Rfp list view serarch.
     * Search between from and to date and also user can search by employee
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function rfpRequisitionSearch(Request $request)
    {
        $response = [];

        $from_date=date('Y-m-d', strtotime($request->from_date));
        $to_date=date('Y-m-d', strtotime($request->to_date));

        $requisition_by=$request->requisition_by;
        $requisition_status=$request->requisition_status;

        $requisition=Requisition::whereDate('requisition_date', '>=', $from_date)
        ->whereDate('requisition_date', '<=', $to_date)
        ->when($requisition_by, function($query) use($requisition_by){
            return $query->where('author_id',$requisition_by);
        })
        ->where(['status'=>$requisition_status,'is_send_to_rfp'=>'yes'])
        ->whereNotIn('delivery_status',['delivered','partial-delivered'])
        ->paginate(30);

        try {
            if(count($requisition)>0)
            {
                $body = \Illuminate\Support\Facades\View::make('pms.backend.pages.store.rfp-search-result-view',
                    ['requisition'=> $requisition]);
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


    public function requisitionItemsList($id)
    {
        try {

            $title = "Requisition Items List";

            $requisition=Requisition::where('id',$id)
            ->where(['status'=>1,'is_send_to_rfp'=>'yes'])
            ->whereNotIn('delivery_status',['delivered','partial-delivered'])
            ->first();


            return view('pms.backend.pages.store.requisition-items-list', compact('title','requisition'));

            }catch (\Throwable $th){

            return $this->backWithError($th->getMessage());
        }
    }

    public function sendNotificationToUsers(Request $request)
    {
        $this->validate($request, [
            'items_id' => ['required'],
        ]);
        try {
                DB::beginTransaction();
                foreach($request->items_id as $key=>$item){

                    $requisition_item=RequisitionItem::where('requisition_id',$request->requisition_id)
                    ->where('id',$item)
                    ->first();

                    if(!empty($requisition_item))
                    {  
                        $notification = new Notification();
                        $notification->user_id = $requisition_item->requisition->author_id;
                        $notification->requisition_item_id = $item;
                        $notification->messages = 'Reference No:'.$requisition_item->requisition->reference_no.' And Item Name: '.$requisition_item->product->name.'. Please Collect Your product from store';
                        $notification->save();

                        DB::commit();

                        return $this->backWithSuccess('Successfully send to users');
                    }else{
                        return $this->backWithWarning('No data found');
                    }
                }

        }catch (\Throwable $th){
            DB::rollback();

            return $this->backWithError($th->getMessage());
        }

        return back();


    }

    public function storeInventoryCompare($req_id)
    {
        try {

            $title = 'Store Inventory Compare';

            $requisition = RequisitionItem::where('requisition_id',$req_id)->get();

            return view('pms.backend.pages.store.store-inventory-compare', compact('title','requisition'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function confirmDelivery($req_id)
    {
        try {

            $prefix='CD-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
            $refNo=uniqueCode(14,$prefix,'requisition_deliveries','id');

           $requisition=Requisition::with('relUsersList')->findOrFail($req_id);

           $title = 'Store Inventory Confirm Delivery to: '.$requisition->relUsersList->name;

           $requisitionItems = RequisitionItem::whereHas('requisition', function($query){
                return $query->whereIn('delivery_status',['processing','partial-delivered']);
                })
                ->where('requisition_id',$req_id)->get();

            return view('pms.backend.pages.store.store-inventory-delivery', compact('title','requisitionItems','requisition','refNo'));
          
        }catch (\Throwable $th){

            return $this->backWithError($th->getMessage());
        }

    }


    public function confirmDeliverySubmit(Request $request)
    {

        $this->validate($request, [
            'delivery_qty'  => "required|array|min:1",
            'delivery_date' => 'required|date',
            'reference_no'  => "required|unique:requisition_deliveries|max:100",
        ]);

        $countDeliveryItems = count(array_filter($request->delivery_qty,function ($deliveryQty){
            return !is_null($deliveryQty);
        }));

        if ($countDeliveryItems<=0){
            return $this->backWithWarning('Please select at least one product for delivery.');
        }

        DB::beginTransaction();
        try {

            $requisition_id=$request->requisition_id;
            $requisitionDelivery=$this->storeRequisitionDelivery($request);


            $itemTotalDeliveryQty=0;
            $totalDeliveryQty=0;
            foreach ($request->delivery_qty as $productId=>$deliveryQty){

                if (!is_null($deliveryQty)){
                    // prepare pre-requisite data ---------
                    $product = Product::where('id',$productId)->first();
                    $requisitionItem = RequisitionItem::where('requisition_id',$requisition_id)
                        ->where('product_id',$productId)->first();

                    $itemTotalDeliveryQty=($deliveryQty+$requisitionItem->delivery_qty); //(current delivery qty + previous delivery qty)
                    $totalDeliveryQty+=$itemTotalDeliveryQty;

                    $requisitionDeliveryItemInput[]=[
                        'requisition_delivery_id'=>$requisitionDelivery->id,
                        'product_id'=>$productId,
                        'delivery_qty'=>$deliveryQty,
                    ];


                    // check requisition qty and delivery qty(current delivery qty + previous delivery qty)
                    if($itemTotalDeliveryQty > ($requisitionItem->qty)){
                        return $this->backWithWarning('Delivery qty is greater then requisition qty. Please Adjust It for product '.$product->name);
                    }

                    // update item wise total delivery Qty (current delivery qty + previous delivery qty)
                    $requisitionItem->update(['delivery_qty'=>$itemTotalDeliveryQty]);


                    $result=$this->updateInventoryAndLog($request,$product,$deliveryQty,$request->warehouse_id[$productId]);

                    if ($result===false){  // check delivery qty and warehouse wise product qty
                        return $this->backWithWarning('Delivery qty may not grater than store qty for product '.$product->name);
                    }


                    $requisitionModel =Requisition::findOrFail($requisition_id);

                    $totalRequisitionQty=$requisitionModel->requisitionItems->sum('qty');
                    //Tracking
                    RequisitionTracking::storeRequisitionTracking($requisitionModel->id,'delivered');

                    //Update requisition status (current delivery qty + previous delivery qty)
                    if ($totalDeliveryQty==$totalRequisitionQty){
                        $requisitionModel->update(['delivery_status'=>'delivered']);
                    }else{
                        $requisitionModel->update(['delivery_status'=>'partial-delivered']);
                    }
                }
            }

            RequisitionDeliveryItem::insert($requisitionDeliveryItemInput);

            DB::commit();

            return $this->redirectBackWithSuccess('Requisition Delivery Successfully','pms.store-manage.store-requistion-list');
        }catch (Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }


    }

    public function storeRequisitionDelivery($request){

        return RequisitionDelivery::create(
            [
                'requisition_id'=>$request->requisition_id,
                'reference_no'=>$request->reference_no,
                'delivery_date'=>date('Y-m-d',strtotime($request->delivery_date)),
                'note'=>$request->note,
                'delivery_by'=>Auth::user()->id,
                'created_by'=>Auth::user()->id,
            ]);
    }


    public function updateInventoryAndLog($request,$product,$deliveryQty,$warehouseId){
        //Update Inventory Summary
        $InventorySummary = InventorySummary::where([
            'product_id'=>$product->id
        ])->first();

        $InventorySummary->qty = ($InventorySummary->qty)-($deliveryQty);
        $InventorySummary->total_price = ($InventorySummary->qty-$deliveryQty)*$InventorySummary->unit_price;
        $InventorySummary->status = 'active';
        $InventorySummary->save();

        //Update Inventory details
        $InventoryDetails = InventoryDetails::where([
            'category_id'=>$product->category_id,
            'product_id'=>$product->id,
            'warehouse_id'=>$warehouseId
        ])->first();

        if ($deliveryQty>$InventoryDetails->qty){
            return false;
        }

        $InventoryDetails->qty = $InventoryDetails->qty-$deliveryQty;
        $InventoryDetails->total_price = ($InventoryDetails->qty-$deliveryQty)*$InventoryDetails->unit_price;
        $InventoryDetails->status = 'active';
        $InventoryDetails->save();

        //Add Trace on Invetory Logs/Transection Table
        $InventoryLogs = new InventoryLogs();
        $InventoryLogs->category_id = $product->category_id;
        $InventoryLogs->product_id = $product->id;
        $InventoryLogs->warehouse_id = $warehouseId;
        $InventoryLogs->unit_price = $InventoryDetails->unit_price;
        $InventoryLogs->qty = $deliveryQty;
        $InventoryLogs->total_price = $deliveryQty*$InventoryDetails->unit_price;
        $InventoryLogs->status = 'active';
        $InventoryLogs->type = 'out';
        $InventoryLogs->save();

        return true;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function purchaseDepartment(Request $request)
    {
        $response = [];

        $requisition=Requisition::where(['id'=>$request->requisition_id,'status'=>1,'approved_id'=>1,'is_send_to_rfp'=>'no','delivery_status'=>'processing'])->first();

        //Start transaction
        DB::beginTransaction();

        try {
            if(count((array)$requisition)>0)
            {
                $requisition->is_send_to_rfp = 'yes';
                $requisition->save();
                //Tracking
                \App\Models\PmsModels\RequisitionTracking::storeRequisitionTracking($requisition->id,'processing');
                //Commit data
                DB::commit();

                $response['result'] = 'success';
                $response['message'] = 'Successfully Send to Purchase Department!!';
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

