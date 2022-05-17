<?php

namespace App\Http\Controllers\Merch;

use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Merch\Brand;
use App\Models\Merch\Buyer;
use App\Models\Merch\OrderEntry;
use App\Models\Merch\ProductType;
use App\Models\Merch\Reservation;
use App\Models\Merch\Season;
use App\Models\Merch\Style;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $unitList = unit_by_id();
        $unitList = collect($unitList)->pluck('hr_unit_name', 'hr_unit_id');
        $buyerList = buyer_by_id();
        $buyerList = collect($buyerList)->pluck('b_name', 'b_id');
        $prdtypList = product_type_by_id();
        $prdtypList = collect($prdtypList)->pluck('prd_type_name', 'prd_type_id');
        return view('merch/reservation/index', compact('unitList', 'buyerList', 'prdtypList'));
    }
    public function getData(){

        $team =[];
        $data = Reservation::getReservationData($team);
        $ordered = OrderEntry::getReservationWiseOrderQty();
        // dd($ordered);
        $getUnit = unit_by_id();
        $getBuyer = buyer_by_id();
        $getProductType = product_type_by_id();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('hr_unit_name', function($data) use ($getUnit){
                return $getUnit[$data->hr_unit_id]['hr_unit_name']??'';
            })
            ->addColumn('b_name', function($data) use ($getBuyer){
                return $getBuyer[$data->b_id]->b_name??'';
            })
            ->addColumn('month_year', function($data){
                $month_year= date('F', mktime(0, 0, 0, $data->res_month, 10)). "-". $data->res_year;
                return $month_year;
            })
            ->addColumn('prd_type_name', function($data) use ($getProductType){
                return $getProductType[$data->prd_type_id]->prd_type_name??'';
            })
            ->addColumn('projection', function($data){
                return $data->res_quantity;
            })
            ->addColumn('confirmed', function($data) use ($ordered){
                return $ordered[$data->id]->qty??0;

            })
            ->addColumn('balance', function($data) use ($ordered){
                return $data->res_quantity - (isset($ordered[$data->id])?($ordered[$data->id]->qty??0):0);

            })
            // ->addColumn('action', function($data) use ($ordered){
            //     $yearMonth = $data->res_year.'-'.$data->res_month;
            //     $resLastMonth = date('Y-m', strtotime('-1 month', strtotime($yearMonth)));
            //     $flag = 0;
            //     if(strtotime(date('Y-m')) > strtotime($resLastMonth)){
            //         $flag = 1;
            //     }
            //     $action_buttons= '<div>
            //         <a class="btn btn-sm btn-primary add-new text-white" data-id="'.$data->id.'" data-type="Edit reservation" data-toggle="tooltip" title="Edit Reservation">
            //             <i class="ace-icon fa fa-pencil"></i>
            //         </a>';
            //         if($data->res_quantity > (isset($ordered[$data->id])?($ordered[$data->id]->qty??0):0) && $flag == 0) {
            //             $action_buttons.= '<a class="btn btn-sm add-new btn-success text-white" data-toggle="tooltip" title="Order Entry" data-type="Order Entry" data-resid="'.$data->id.'">
            //                 <i class="ace-icon fa fa-cart-plus "></i>
            //             </a>';
            //         }
            //         $action_buttons.= '<a class="btn btn-sm add-new btn-secondary text-white" data-toggle="tooltip" title="Order List" data-type="Order List" data-resid="'.$data->id.'">
            //                 <i class="ace-icon fa fa-list"></i>
            //             </a>';
            //     $action_buttons.= "</div>";
            //     return $action_buttons;
            // })

            ->addColumn('action', function($data) use ($ordered){
                $yearMonth = $data->res_year.'-'.$data->res_month;
                $resLastMonth = date('Y-m', strtotime('-1 month', strtotime($yearMonth)));
                $flag = 0;
                if(strtotime(date('Y-m')) > strtotime($resLastMonth)){
                    $flag = 1;
                }
                $action_buttons = "<center><div class=\"btn-group\">
                <a type=\"button\" class=\" dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\" title=\"Action\">
                    <i class=\"las la-cog action-icon-group\"></i>
                </a>";
                $action_buttons.= "<div class=\"dropdown-menu\">
                    <a class=\"dropdown-item btn btn-xs btn-secondary add-new\" data-id=\"$data->id\" data-type=\"Edit reservation\" data-toggle=\"tooltip\" title=\"Edit Reservation\">
                    <center><i class=\"ace-icon fa fa-pencil icon-color-edit\"></i></center>
                    </a>";

                    
                    if($data->res_quantity > (isset($ordered[$data->id])?($ordered[$data->id]->qty??0):0) && $flag == 0) {
                        $action_buttons.= "<a class=\"dropdown-item btn btn-xs btn-secondary add-new\" data-toggle=\"tooltip\" title=\"Order Entry\" data-type=\"Order Entry\" data-resid=\"$data->id\">
                        <center><i class=\"ace-icon fa fa-cart-plus icon-color-orderTOpo\"></i></center>
                        </a>";
                    }
                    $action_buttons.= "<a class=\"dropdown-item btn btn-xs btn-secondary add-new\" data-toggle=\"tooltip\" title=\"Order List\" data-type=\"Order List\" data-resid=\"$data->id\">
                    <center><i class=\"ace-icon fa fa-list icon-color-edit\"></i></center>
                        </a>";
                $action_buttons.= "</div></div></center>";
                return $action_buttons;
            })


           
            ->rawColumns(['hr_unit_name', 'b_name', 'month_year', 'prd_type_name', 'res_sah', 'projection', 'confirmed', 'balance','action'])
            ->make(true);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $unitList= unit_by_id();
        $unitList = collect($unitList)->pluck('hr_unit_name','hr_unit_id');
        return view('merch/reservation/create', compact('unitList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $data['type'] = 'error';
        
        DB::beginTransaction();
        try {
            $resYearMonth = explode('-', $input['res_year_month']);
            $input['res_month'] = $resYearMonth[1];
            $input['res_year'] = $resYearMonth[0];
            // check reservation exists
            $getRes = Reservation::checkReservationExists($input);
            if($getRes == true){
                $data['message'] = "Reservation already exists.";
                return response()->json($data);
            }
            // create reservation
            $input['created_by'] = auth()->user()->id;
            $resId = Reservation::create($input)->id;
            
            $data['url'] = url()->previous();
            $data['message'] = "Reservation Successfully Save.";

            DB::commit();
            $data['type'] = 'success';
            return response()->json($data);
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            $data['message'] = $bug;
            return response()->json($data);
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
        $unitList= unit_by_id();
        $unitList = collect($unitList)->pluck('hr_unit_name','hr_unit_id');
        $buyerList= buyer_by_id();
        $buyerList = collect($buyerList)->pluck('b_name','b_id');
        $reservation = Reservation::findOrFail($id);
        $prdtypList= product_type_by_id();
        $orderQty = OrderEntry::getOrderQtySumResIdWise($id);
        $orderQty = $orderQty->qty??0;
        $prdtypList = collect($prdtypList)->pluck('prd_type_name','prd_type_id');

        return view('merch/reservation/edit', compact('unitList', 'reservation', 'buyerList', 'prdtypList', 'orderQty'));
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
        $input = $request->all();
        $data['type'] = 'error';
        $yearMonth = explode('-', $input['res_year_month']);
        $input['res_month'] = $yearMonth[1];
        $input['res_year'] = $yearMonth[0];
        // return $input;
        DB::beginTransaction();
        try {
            // check reservation exists
            $getRes = Reservation::checkReservationIdWise($input);
            if($getRes != ''){
                if($getRes->id != $id){
                    $data['message'] = "Reservation already exists.";
                    return response()->json($data);
                }
                
            }
            $reservation = Reservation::findOrFail($id);
            
            // update reservation
            $input['updated_by'] = auth()->user()->id;
            $reservation->update($input);
            $data['url'] = url()->previous();
            $data['message'] = "Reservation Successfully Update.";
            
            DB::commit();
            $data['type'] = 'success';
            return response()->json($data);
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            $data['message'] = $bug;
            return response()->json($data);
        }
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

    public function orderEntry($resid)
    {
        try {
            $reservation = Reservation::getReservationIdWiseReservation($resid);
            $reservation->res_month = date('F', mktime(0, 0, 0, $reservation->res_month, 10));

            $ordered = OrderEntry::getOrderQtySumResIdWise($resid);
            $reservation->res_quantity = $reservation->res_quantity - $ordered->qty;

            $seasonList = Style::getSeasonStyleBuyerIdWise($reservation->b_id);
            $seasonList = collect($seasonList)->pluck('text', 'id');
            //dd($reservation);
            return view('merch/order/res_order', compact('reservation', 'seasonList'));
        } catch (\Exception $e) {
            return 'error';
        }
        
    }

    public function orderStore(Request $request)
    {
        $input = $request->all();
        // dd($input);
        
        $data['type'] = 'error';
        $yearMonth = explode('-', $input['order_year_month']);
        $input['order_month'] = $yearMonth[1];
        $input['order_year'] = $yearMonth[0];
        // return $input;
        DB::beginTransaction();
        try {
            // check order qty > reservation qty
            if($input['order_qty'] > $input['res_quantity']){
                DB::rollback();
                $data['message'] = "Order quantity can't large then "+$input['res_quantity']+" Quantity!";
                return response()->json($data);
            }

            // order entry & check
            $orderNo = make_order_number($input, $input['order_year']);
            $order = [
                'order_code'          => $orderNo,
                'res_id'              => $input['res_id'],
                'unit_id'             => $input['unit_id'],
                'mr_buyer_b_id'       => $input['b_id'],
                'order_month'         => $input['order_month'],
                'order_year'          => $input['order_year'],
                'mr_style_stl_id'     => $input['mr_style_stl_id'],
                'order_ref_no'        => $input['order_ref_no'],
                'order_qty'           => $input['order_qty'],
                'order_delivery_date' => $input['order_delivery_date'],
                'pcd'                 => $input['pcd'],
                'order_status'        => 1,
                'order_entry_source'  => 0,
                'created_by'          => auth()->user()->id
            ];
            $checkOrder = OrderEntry::getCheckOrderExists($order);
            if($checkOrder == true){
                DB::rollback();
                $data['message'] = "Order Already Exists";
                return response()->json($data);
            }

            $orderId = OrderEntry::insertGetId($order);
            $data['url'] = url('/merch/order/bom/').'/'.$orderId;
            $data['message'] = "Order Entry Successfully.";
            
            DB::commit();
            $data['type'] = 'success';
            return response()->json($data);
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            $data['message'] = $bug;
            return response()->json($data);
        }
    }

    public function orderList($resId)
    {
        try {
            $reservation = Reservation::findOrFail($resId);
            $orderQty = OrderEntry::getOrderQtySumResIdWise($resId);
            $orderQty = $orderQty->qty??0;
            $getOrder = OrderEntry::getOrderListWithStyleResIdWise($resId);
            $getBuyer = buyer_by_id();
            $getUnit = unit_by_id();
            $getSeason = season_by_id();
            $getBrand = brand_by_id();
            return view('merch/reservation/order_list', compact('getOrder', 'getBuyer', 'getUnit', 'getSeason', 'getBrand', 'reservation', 'orderQty'));
        } catch (\Exception $e) {
            return 'error';
        }
    }
    public function checkForOrder(Request $request)
    {
        $input = $request->all();
        try {
            // check reservation 
            $reservation = Reservation::getReservationForOrder($input);
            if($reservation != null){
                $order = OrderEntry::getResIdWiseOrder($reservation->id);
                $orderQty = $order->sum??0;
                $balance = $reservation->res_quantity - $orderQty;
                $reservationQty = $balance;
                if($balance >= $input['order_qty']){
                    $reservationQty = $balance;
                }
            }else{
                $reservationQty = $input['order_qty'];
            }
            return view('merch.reservation.for_order', compact('reservation', 'input', 'reservationQty'));
           
        } catch (\Exception $e) {
            // return $e->getMessage();
            return 'error';
        }
    }
}
