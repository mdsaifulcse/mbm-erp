<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;

use App\Models\PmsModels\Menu\Menu;
use App\Models\PmsModels\Product;
use App\Models\PmsModels\Requisition;
use App\Models\PmsModels\RequisitionTracking;
use App\Models\PmsModels\RequisitionItem;
use App\Models\PmsModels\RequisitionType;
use App\Models\PmsModels\Category;
use App\Models\PmsModels\CategoryDepartment;
use App\Models\PmsModels\Notification;
use App\Models\PmsModels\RequisitionDelivery;
use App\Models\PmsModels\RequisitionDeliveryItem;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use DB,Validator, Str;
use Carbon\Carbon;

class RequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            $title = 'Requisition';
            $requisitions = Requisition::with('items')->when(isset(auth()->user()->employee->as_department_id), function($query){
                return $query->where('created_by',Auth::user()->id);
            })
            ->orderBy('id','DESC')->paginate(100);


            return view('pms.backend.pages.requisitions.index', compact('title', 'requisitions'));
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

            $category_id = CategoryDepartment::when(isset(auth()->user()->employee->as_department_id), function($query){
                return $query->where('hr_department_id', auth()->user()->employee->as_department_id);
            })->pluck('category_id')->toArray();

            
            $categories = Category::where('parent_id',null)->whereIn('id', $category_id)->get();

            $title = 'Create Requisition';
            $requisition = null;

            $prefix='RQ-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
            $refNo=uniqueCode(14,$prefix,'requisitions','id');

            return view('pms.backend.pages.requisitions.create', compact('title', 'requisition','refNo','categories'));
        }catch (Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }


    public function loadCategoryWiseProducts($categoryId,Request $request){

        $response='';


        $categoryProducts=Product::select('id','name')->where(['category_id'=>$categoryId]);

        if (isset($request->products_id)){
           //return explode(',',$request->products_id);
            $categoryProducts=$categoryProducts->whereNotIn('id',explode(',',$request->products_id));
        }

        $categoryProducts=$categoryProducts->get();

        $response .= '<select name="product_id[]" id="product_1" class="form-control select2 product" required>';
        $response .= '<option value="">--Select Product--</option>';
        if (!empty($categoryProducts)) {
            foreach ($categoryProducts as $data) {
                $response.= '<option value="' . $data->id . '">' . $data->name . '</option>';
            }
        }else{
            $response .= "<option value=''>No Product Found!!</option>";
        }
        $response .= "</select>";

        return $response;

    }
    
     public function loadCategoryWiseSubcategory($categoryId){

        $response='';
        $subcategory=Category::where('parent_id',$categoryId)->get();

        if (isset($subcategory) && count((array)$subcategory)>0) {
            $response .= '<option value="">--Select Subcategory--</option>';
            foreach ($subcategory as $data) {
                $response.= '<option value="'.$data->id.'">'.$data->name.'('.$data->code.')'.'</option>';
            }
        }else{
            $response .= "<option value=''>No Category Found!!</option>";
        }
        
        return $response;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request,$edit=false)
    {

        DB::beginTransaction();
        try {

            $requisition=Requisition::create([
                'reference_no'=>$request->reference_no,
                'requisition'=>uniqueStringGenerator(),
                'requisition_date'=>date('Y-m-d h:i',strtotime($request->requisition_date)),
                'author_id'=>Auth::user()->id,
                'remarks'=>$request->remarks,
            ]);

            foreach ($request->qty as $key=>$qty){

                $requisitionItemInput[]=[
                    'requisition_id'=>$requisition->id,
                    'product_id'=>$request->product_id[$key],
                    'qty'=>$qty,
                    'created_at'=>date('Y-m-d h:i'),
                    'created_by'=>Auth::user()->id,
                ];

            }

            RequisitionItem::insert($requisitionItemInput);

            RequisitionTracking::storeRequisitionTracking($requisition->id,'draft');

            //Tracking
            DB::commit();

            if ($edit==false){
                return $this->redirectBackWithSuccess('Requisition has been successfully applied','pms.requisition.requisition.index');
            }else{
                return $requisition;
            }

        }
        catch (\Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }

    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Requisition  $requisition
     * @return \Illuminate\Http\Response
     */
    public function show(Requisition $requisition)
    {
        try {

            $title = 'Requisition Show';
            $requisition = Requisition::with('items','items.product','items.product.category')->findOrFail($requisition->id);
            return view('pms.backend.pages.requisitions.show', compact('title','requisition'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function showRequisition($id)
    {
        try {
            $title = 'Requisition Show';
            $requisition = Requisition::with('items','items.product','items.product.category')->findOrFail($id);
            return view('pms.backend.pages.requisitions.show', compact('title','requisition'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Requisition  $requisition
     * @return \Illuminate\Http\Response
     */
    public function edit(Requisition $requisition)
    {
        
       
        try {
            $title = 'Requisition Update';

            $ids = CategoryDepartment::when(isset(auth()->user()->employee->as_department_id), function($query){
                return $query->where('hr_department_id', auth()->user()->employee->as_department_id);
            })->pluck('category_id')->toArray();

            $categories = Category::where('parent_id',null)->whereIn('id', $ids)->get();
            $requisition->load('requisitionItems','requisitionItems.product');

            return view('pms.backend.pages.requisitions.edit', compact('title', 'categories','requisition'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Requisition  $requisition
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Requisition $requisition)
    {

        DB::beginTransaction();
        try {
            RequisitionItem::where('requisition_id',$requisition->id)->delete();
            $requisition->delete();

             $this->store($request,true);
            DB::commit();
            
            return $this->redirectBackWithSuccess('Requisition has been successfully Update','pms.requisition.requisition.index');

        }
        catch (\Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Requisition  $requisition
     * @return \Illuminate\Http\Response
     */
    public function destroy(Requisition $requisition)
    {
        //
    }

   

    public function destroyItem(RequisitionItem $item)
    {
        try {
            $requisition = $item->requisition;
            $item->delete();
            if ($requisition->items->count() < 1){
                $requisition->delete();
            }
            return response()->json($requisition->items->count());
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }

    /**
     * Rfp list view.
     */

    public function requisitionListView()
    {
        try {
            $title = 'Requisition List View';

            $requisition_user_list=Requisition::when(isset(Auth::user()->employee->as_department_id),
                function($query){
                    return $query->whereHas('relUsersList.employee',function($query){
                        return $query->where('as_department_id',Auth::user()->employee->as_department_id);
                    });
            })
            ->join('users','users.id','=','requisitions.author_id')
            ->groupBy('requisitions.author_id')
            // ->where(['requisitions.status'=>0,'requisitions.approved_id'=>NULL])
            ->get(['users.id','users.name']);


           $requistion_data=Requisition::when(isset(Auth::user()->employee->as_department_id), function($query){
                return $query->whereHas('relUsersList.employee',function($query){
                    return $query->where('as_department_id',Auth::user()->employee->as_department_id);
                });
            })
            ->orderBy('id','DESC')->where('status',0)->paginate(30);

            return view('pms.backend.pages.requisitions.requisition-list-index', compact('title','requisition_user_list','requistion_data'));
        }catch (Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    function needtokeep(){
        $requistions=Requisition::when(isset(Auth::user()->employee->as_department_id), function($query){
            return $query->whereHas('relUsersList.employee',function($query){
                return $query->where('as_department_id',Auth::user()->employee->as_department_id);
            });
        })
            ->where('status',0)
            ->get();

        $requistion_data = [];
        foreach ($requistions as $requistion){
            $tp=0;
            foreach ($requistion->items as $item){
                $tp += ($item->product->unit_price * $item->qty);
            };
            $requistion->total_price = $tp;
            foreach (Auth::user()->relApprovalRange as $range){
                if ($range->min_amount <= $requistion->total_price && $range->max_amount >= $requistion->total_price){
                    $requistion_data[] = $requistion;
                }
            }
        }
        return $this->paginate($requistion_data, 30);
    }

    /**
     * Rfp list view serarch.
     * Search between from and to date and also user can search by employee
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function requisitionListViewSearch(Request $request)
    {
        $response = [];

        $from_date=date('Y-m-d H:i:s',strtotime($request->from_date));
        $to_date=date('Y-m-d H:i:s',strtotime($request->to_date));

        $requisition_by=$request->requisition_by;
        $requisition_status=$request->requisition_status;

        $requistion_data=Requisition::when(isset(Auth::user()->employee->as_department_id),
            function($query){
                return $query->whereHas('relUsersList.employee',function($query){
                    return $query->where('as_department_id',Auth::user()->employee->as_department_id);
                });
        })
        ->when($requisition_by, function($query) use($requisition_by){
            return $query->where('author_id',$requisition_by);
        })
        ->when($requisition_status, function($query) use($requisition_status){
            return $query->where('status',$requisition_status);
        })
        ->whereDate('requisition_date', '>=', $from_date)
        ->whereDate('requisition_date', '<=', $to_date)
        ->orderBy('id','DESC')
        ->paginate(30);

        try {

            if(count($requistion_data)>0)
            {
                $body = \Illuminate\Support\Facades\View::make('pms.backend.pages.requisitions._requisition-list-search',
                    ['requistion_data'=> $requistion_data]);
                $contents = $body->render();
                $response['result'] = 'success';
                $response['body'] = $contents;
            }else{
                $response['result'] = 'error';
                $response['message'] = 'No Data Found!!';
            }

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }

        return $response;
    }

    /**
     * Rfp list view.
     * Change requisiton status (Approve and rejected)
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function toggleRequisitionStatus(Request $request)
    {
        $row = Requisition::where('id',$request->id)->first();
        if(isset($row->id)){

            $new_status = $request->status;
            $new_text = $new_status == 1 ? 'Acknowledge' : (($new_status == 2)? 'Halt' : 'Pending');
            $new_approved = $new_status == 1 ? 1:(($new_status == 2)? 1 : Null);
            $new_message= $new_status == 1 ? 'Succesfully Updated To Acknowledgement' : (($new_status == 2)? 'Succesfully Updated To Halt' : (($new_status == 0)? 'Succesfully Send to Department Head':'Succesfully Updated To Pending'));

            $update = $row->update([
                            'status' => $new_status,
                            'approved_id' => $new_approved,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => Auth::user()->id
                        ]);
            if ($new_status==1) {
                RequisitionTracking::storeRequisitionTracking($row->id,'approved');
            }

            if ($new_status==0) {
                RequisitionTracking::storeRequisitionTracking($row->id,'pending');
            }

            if($update){
                return response()->json([
                    'success' => true,
                    'new_text' => $new_text,
                    'message' => $new_message
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

    public function haltRequisitionStatus(Requests\Pms\RequisitionRequest $request){

        try{

            $row = Requisition::findOrFail($request->id);

                $row->update([
                    'admin_remark' => $request->admin_remark,
                    'status' => 2,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id
                ]);

            RequisitionTracking::storeRequisitionTracking($row->id,'halt',$request->admin_remark);


            return $this->backWithSuccess('Requisition Successfully Halt');
        }catch (\Throwable $th){

        }
    }


    public function showTracking(Request $request)
    {
       $response=[];

       $requisition = Requisition::where('id',$request->id)->first();

       if (isset($requisition)) {
          
         $body = \Illuminate\Support\Facades\View::make('pms.backend.pages.requisitions.tracking',
            ['requisition'=> $requisition]);

         $contents = $body->render();
          $response['result'] = 'success';
          $response['body'] = $contents;
       }else{
                $response['result'] = 'error';
                $response['message'] = 'Data not found.!!';
        }

        return $response;

    }

    public function notificationAll(Request $request)
    {
        try{
            $title="Notification List";
            $notification = Notification::when(isset(Auth::user()->employee->as_department_id),function($query){
                return $query->whereHas('relRequisitionItem.requisition.relUsersList.employee',function($query){
                    return $query->where('as_department_id',Auth::user()->employee->as_department_id);
                });
            })
            ->where('user_id',auth()->user()->id)
            ->orderBy('id','asc')
            ->paginate(30);

            return view('pms.backend.pages.requisitions.notification-list', compact('title','notification'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function markAsRead(Request $request)
    {
        $response=[];

        $notification = Notification::where('id',$request->id)->first();

        if (isset($notification)) {

            $notification->type = 'read';
            $notification->read_at = date('Y-m-d h:i:s');
            $notification->save();

            $response['result'] = 'success';
            $response['message'] = 'Successfully Read Notification';

        }else{
            $response['result'] = 'error';
            $response['message'] = 'No Notification Found';
        }

        return $response;
    }

    public function deliveredRequisitionList()
    {
       try{
            $title="Deliverd Order List";
            $status=['pending','acknowledge'];

            $delivered_requisition = RequisitionDeliveryItem::when(isset(Auth::user()->employee->as_department_id),function($query){

                    return $query->whereHas('relRequisitionDelivery.relRequisition.relUsersList.employee',function($query){
                        return $query->where('as_department_id',Auth::user()->employee->as_department_id);
                    });
                })->whereHas('relRequisitionDelivery.relRequisition',function($query){
                    return $query->where('author_id',Auth::user()->id);
                })
                ->orderBy('id','desc')
                ->paginate(30);

            return view('pms.backend.pages.requisition-delivery.delivered-requisition-list', compact('title','delivered_requisition','status'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function deliveredRequisitionAck(Request $request)
    {
        try{

            $response=[];

            $delivered_requisition = RequisitionDeliveryItem::when(isset(Auth::user()->employee->as_department_id),function($query){
                    return $query->whereHas('relRequisitionDelivery.relRequisition.relUsersList.employee',function($query){
                        return $query->where('as_department_id',Auth::user()->employee->as_department_id);
                    });
                })->whereHas('relRequisitionDelivery.relRequisition',function($query){
                    return $query->where('author_id',Auth::user()->id);
                })
                ->where('id',$request->id)
                ->first();

        if (isset($delivered_requisition)) {

            $delivered_requisition->status = 'acknowledge';
            $delivered_requisition->save();

            $response['result'] = 'success';
            $response['message'] = 'Successfully Acknowledged.';

        }else{
            $response['result'] = 'error';
            $response['message'] = 'No Data Found';
        }

        }catch (\Throwable $th){

            $response['result'] = 'error';
            $response['message'] = $th->getMessage();
        }

        return $response;
    }

    public function deliveredRequisitionSearch(Request $request)
    {
        $response = [];

        $from_date=date('Y-m-d H:i:s',strtotime($request->from_date));
        $to_date=date('Y-m-d H:i:s',strtotime($request->to_date));

        $status=$request->status;

        $delivered_requisition = RequisitionDeliveryItem::when(isset(Auth::user()->employee->as_department_id),function($query){
            return $query->whereHas('relRequisitionDelivery.relRequisition.relUsersList.employee',function($query){
                return $query->where('as_department_id',Auth::user()->employee->as_department_id);
            });
        })->whereHas('relRequisitionDelivery.relRequisition',function($query){
            return $query->where('author_id',Auth::user()->id);
        })
        ->when($status, function($query) use($status){
            return $query->where('status',$status);
        })
        ->when($from_date, function($query) use($from_date){
            return $query->whereHas('relRequisitionDelivery',function($query) use($from_date){
                return $query->whereDate('delivery_date', '>=',$from_date);
            });
        })
        ->when($to_date, function($query) use($to_date){
            return $query->whereHas('relRequisitionDelivery',function($query) use($to_date){
                return $query->whereDate('delivery_date', '<=',$to_date);
            });
        })
        ->orderBy('id','DESC')
        ->paginate(30);

        try {

            if(count($delivered_requisition)>0)
            {
                $body = \Illuminate\Support\Facades\View::make('pms.backend.pages.requisition-delivery.delivered-requisition-search',
                    ['delivered_requisition'=> $delivered_requisition]);
                $contents = $body->render();
                $response['result'] = 'success';
                $response['body'] = $contents;
            }else{
                $response['result'] = 'error';
                $response['message'] = 'No Data Found!!';
            }

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }

        return $response;
    }

}

