<?php

namespace App\Http\Controllers\Merch;

use App\Http\Controllers\Controller;
use App\Models\Merch\BomCosting;
use App\Models\Merch\BomOtherCosting;
use App\Models\Merch\OperationCost;
use App\Models\Merch\OrderBOM;
use App\Models\Merch\OrderBomOtherCosting;
use App\Models\Merch\OrderEntry;
use App\Models\Merch\OrderOperationNCost;
use App\Models\Merch\PurchaseOrder;
use App\Models\Merch\Reservation;
use App\Models\Merch\Style;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class OrderController extends Controller
{
    public function __construct()
    {
        ini_set('zlib.output_compression', 1);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $unitList= unit_by_id();
        $unitList= collect($unitList)->pluck('hr_unit_name', 'hr_unit_id');
        $buyerList= buyer_by_id();
        $buyerList= collect($buyerList)->pluck('b_name', 'b_id');
        $brandList= brand_by_id();
        return view("merch/order/list", compact('unitList','buyerList','brandList'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if(isset($request->stl_id) && $request->stl_id != ''){
            $style = Style::getStyleIdWiseStyleInfo($request->stl_id, ['stl_id', 'stl_no', 'prd_type_id', 'mr_season_se_id', 'stl_year', 'mr_brand_br_id', 'mr_buyer_b_id', 'unit_id', 'stl_product_name']);
            $season = season_by_id();
            $productType = product_type_by_id();
            $brand = brand_by_id();
            $unitList = unit_by_id();
            $buyerList = buyer_by_id();
            $reservation = Reservation::getReservationForOrder((array)$style);
            // dd($reservation);
            if($reservation !=null){
            $order = OrderEntry::getResIdWiseOrder($reservation->id);
            $orderQty = $order->sum??0;// dd($reservation->id);
            $reservation->resbalance=$reservation->res_quantity-$orderQty;
             }
            // dd($reservation);

              
            // $reservation_month =Reservation::getReservationForOrderEntyr((array)$style)->pluck('res_month');
            // $reservationMonth = collect($reservation_month->toArray())->map(function($q){
            //     return collect($q)->only(['res_month', 'res_year'])->all();
            // });

              // dd($reservation);
          // if($reservation != null){
          //       $order = OrderEntry::getResIdWiseOrder($reservation->id);
          //       $orderQty = $order->sum??0;
          //       $reservation->balance = $reservation->res_quantity - $orderQty;
          //       // dd($reservation);
          //   }
         
        return view('merch.order.create', compact('style', 'season', 'productType', 'brand', 'unitList', 'buyerList', 'reservation'));
        }


        return view('merch.order.create');
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
           //dd($input);
        $data['type'] = 'error';

        $input['created_by'] = auth()->user()->id;
        if(!isset($input['hr_unit_id'])){
            $input['hr_unit_id'] = $input['unit_id'];
        }else{
            $input['unit_id'] = $input['hr_unit_id'];
        }
        if(!isset($input['b_id'])){
            $input['b_id'] = $input['mr_buyer_b_id'];
        }else{
            $input['mr_buyer_b_id'] = $input['b_id'];
        }
        
        DB::beginTransaction();
        try {

            // if($input['res_id'] == '0'){
                $resYearMonth = explode('-', $input['res_year_month']);
                $input['res_month'] = $resYearMonth[1];
                $input['res_year'] = $resYearMonth[0];
                $order = OrderEntry::getResIdWiseOrder($input['res_id']);
                $orderQty = $order->sum??0;
                $input['res_quantity'] =$input['res_quantity']+$orderQty;
                // dd($input['res_quantity']);
                // check reservation exists
                $getRes = Reservation::checkReservationExists($input);
                if($getRes == true){
                    $up = Reservation::findOrFail($input['res_id']);
                    $up->update($input);
                    // return 'hi';
                    // $data['message'] = "Reservation already exists.";
                    // return response()->json($data);
                }else{
                // create reservation
                
                $input['res_id'] = Reservation::create($input)->id;
                }
                // $up = Reservation::findOrFail($id);
                // $up->update($input);

                // update or create


            // }
            // check & create order base on mr_style_stl_id
            if($input['mr_style_stl_id'] == null && $input['mr_style_stl_id'] == ''){
                DB::rollback();
                $data['message'] = "Style Not Found!";
                return response()->json($data);
            }
            // check order qty > reservation qty
            if($input['order_qty'] > $input['res_quantity']){
                DB::rollback();
                $data['message'] = "Order quantity can't large then Reservation Quantity!";
                return response()->json($data);
            }
            $ordYearMonth = explode('-', $input['order_year_month']);
            $input['order_month'] = $ordYearMonth[1];
            $input['order_year'] = $ordYearMonth[0];
            $input['bom_status'] = 1;
            $input['costing_status'] = 1;
            // order entry
            $input['order_code'] = make_order_number($input, $input['order_year']);

            $checkOrder = OrderEntry::getCheckOrderExists($input);

             // dd($checkOrder);
            if($checkOrder == true){
                DB::rollback();
                $data['message'] = "Order Already Exists";
                return response()->json($data);
            }

            $orderId = OrderEntry::create($input)->order_id;
            // style BOM & Costing
            $stlBomCosting = BomCosting::getStyleWiseItem($input['mr_style_stl_id'], 'all');

            // Order BOM create
            $orderBOMCosting = collect($stlBomCosting)->map(function($q) use ($orderId) {
                $bomNCosting = collect($q)->toArray();
                $bomNCosting['stl_bom_id'] = $bomNCosting['id'];
                $bomNCosting['order_id'] = $orderId;
                $bomNCosting['created_by'] = auth()->user()->id;
                unset($bomNCosting['id']);
                return $bomNCosting;
            });
            OrderBOM::insert($orderBOMCosting->toArray());
            // style Special operation
            $getStlSP = OperationCost::getStyleIdWiseSpOperationInfo($input['mr_style_stl_id'], 2)->toArray();

            // order Special operation create
            $orderSP = collect($getStlSP)->map(function($q) use ($input, $orderId){
                $sp = collect($q)->toArray();
                $sp['mr_order_entry_order_id'] = $orderId;
                unset($sp['style_op_id']);
                return $sp;
            });
            OrderOperationNCost::insert($orderSP->toArray());
            // style other costing
            $styleOtherCosting = BomOtherCosting::getStyleIdWiseStyleOtherCosting($input['mr_style_stl_id']);
            // order other costing create
            $ordOtherCosting = collect($styleOtherCosting)->toArray();
            $ordOtherCosting['mr_order_entry_order_id'] = $orderId;
            unset($ordOtherCosting['id']);

            OrderBomOtherCosting::insert($ordOtherCosting);

            Style::where('stl_id', $request->mr_style_stl_id)->update([
                // 'stl_type' => 'B',
                'stl_type' => 'Bulk',
            ]);


            $data['message'] = "Order Entry Successfully.";
            $data['type'] = 'success';
            $data['url'] = url("/merch/orders?view=$orderId");
            DB::commit();
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
        $order = OrderEntry::orderInfoWithStyle($id);
        $pagesize = '';
        return view('merch.order.show', compact('order', 'pagesize'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $order = OrderEntry::orderInfoWithStyle($id);
            $unitList = unit_by_id();
            $buyerList = buyer_by_id();
            $seasonList = season_by_id();
            $getRes = Reservation::getReservationIdWiseReservation($order->res_id);
            $getOrd = OrderEntry::getResIdWiseOrder($order->res_id);
            $resQty = (($getRes->res_quantity??0) - $getOrd->sum) + $order->order_qty;

            $poQty = PurchaseOrder::getPoOrderSumQtyOrderIdWise($id);
            $poQty = $poQty??0;

            return view('merch.order.edit', compact('order', 'unitList', 'buyerList', 'poQty', 'resQty', 'seasonList'));
        } catch (\Exception $e) {
            return 'error';
        }
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
        $yearMonth = explode('-', $input['ord_year_month']);
        $input['order_month'] = $yearMonth[1];
        $input['order_year'] = $yearMonth[0];
        // return $input;
        DB::beginTransaction();
        try {
            // get order
            $getOrder = OrderEntry::getOrderInfoIdWise($id);
            if($getOrder == null){
                $data['message'] = "Order Not Found!";
            }

            // update order
            $ordId = OrderEntry::where('order_id', $id)->update([
                'order_month'         => $input['order_month'],
                'order_year'          => $input['order_year'],
                'order_ref_no'        => $input['order_ref_no'],
                'order_qty'           => $input['order_qty'],
                'order_delivery_date' => $input['order_delivery_date'],
                'pcd'                 => $input['pcd'],
                'updated_by'          => auth()->user()->id
            ]);

            $data['url'] = url()->previous();
            $data['message'] = "Order Successfully Update.";

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

    public function list()
    {
        $team =[];
        $getBuyer = buyer_by_id();
        $getUnit = unit_by_id();
        $getSeason = season_by_id();
        $getBrand = brand_by_id();
        // return $getUnit;

        $data = OrderEntry::getOrderListWithStyleResIdWise();
        $orderIds = collect($data)->pluck('order_id');
        $orderFOB = DB::table('mr_order_bom_other_costing')
        ->whereIn('mr_order_entry_order_id', $orderIds)
        ->pluck('agent_fob', 'mr_order_entry_order_id');
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('order_code', function ($data){
                return '<a class="add-new" id="order-view-'.$data->order_id.'" data-orderid="'.$data->order_id.'" data-type="Order View" data-toggle="tooltip" data-placement="top" title="" data-original-title="Order View">'.$data->order_code.'</a>';
            })
            ->addColumn('b_name', function ($data) use ($getBuyer){
                return $getBuyer[$data->mr_buyer_b_id]->b_name??'';
            })
            ->addColumn('hr_unit_name', function ($data) use ($getUnit){
                return $getUnit[$data->unit_id]['hr_unit_name']??'';
            })
            ->editColumn('br_name', function ($data) use ($getBrand) {
                return $getBrand[$data->style->mr_brand_br_id]->br_name??'';
            })
            ->addColumn('se_name', function ($data) use ($getSeason){
                $seName = $getSeason[$data->style->mr_season_se_id]->se_name??'';
                return $seName.'-'.date('y', strtotime($data->style->stl_year))??'';
            })
            ->addColumn('stl_no', function ($data){
                return $data->style->stl_no??'';
            })
            ->editColumn('order_delivery_date', function($data){
                return custom_date_format($data->order_delivery_date);
            })
            ->editColumn('fob', function($data) use ($orderFOB) {
                return $orderFOB[$data->order_id]??0;
            })
            ->addColumn('action', function ($data) {
                

                    $return="<center>
                             <div class=\"btn-group\">
                            <a type=\"button\" class=\" dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\" title=\"Action\">
                            <i class=\"las la-cog icon-action action-icon-group\"></i>
                            </a>";
                    $return.= "<div class=\"dropdown-menu\">";

                    $return.= "<a href='#' class=\"dropdown-item btn btn-xs btn-secondary add-new\" 
                                        data-type=\"Order Edit\" 
                                        data-toggle=\"tooltip\" 
                                        title=\"Order Edit\" 
                                        data-orderid=\"$data->order_id\">
                           <center>
                            <i class=\"las ace-icon fa fa-pencil icon-color-edit \"></i>
                            </center>
                            </a>";        

                    $bomStatus = ($data->bom_status == 1)?'Edit Order BOM':'Create Order BOM';
                    $bomClass = ($data->bom_status == 1)?'btn-primary':'btn-warning';
                    $return.="<a href='".url("merch/order/bom/$data->order_id")."' 
                               class=\"dropdown-item btn btn-xs btn-secondary '.$bomClass.'\" 
                               data-toggle=\"tooltip\" 
                               title=\"$bomStatus\">
                              <center>
                              <i class=\"las la-clipboard-list icon-color-bom\"></i>
                              </center>
                              </a>";

                    $costingStatus = ($data->bom_status == 1)?'Edit Order Costing':'Create Order Costing';
                    // $costingClass = ($data->costing_status == 1)?'btn-primary':'btn-warning';
                    $return .='<a href="'.url('merch/order/costing/'.$data->order_id).'" 
                                class="dropdown-item btn  " data-toggle="tooltip" 
                                title="'.$costingStatus.'">
                                <i class="las la-file-invoice-dollar icon-color-costing "></i>
                                
                                </a>';


                    if($data->bom_status == 1 && $data->costing_status == 1){
                    $return .= "<a href='".url("merch/po-order?order_id=$data->order_id")."' 
                               class=\"dropdown-item btn btn-xs btn-secondary '.$bomClass.'\" 
                               data-toggle=\"tooltip\" 
                               title=\"Order to PO\">
                              <center>
                              <i class=\"las la-shopping-cart icon-color-orderTOpo\"></i>
                              </center>
                              </a>";
                     }
                    $return.="</div>";
                    $return.="</div> </center>";
             return $return;


            })


            ->rawColumns(['order_code', 'order_ref_no', 'hr_unit_name', 'b_name', 'se_name', 'stl_no', 'order_qty', 'order_delivery_date', 'fob', 'action'])
            ->make(true);
    }


     public function monthReservatiionCheck(Request $request)
    {
          // dd($request->all());
          $style_info = Style::getStyleIdWiseStyleInfo($request->stl_id, ['stl_id', 'stl_no', 'prd_type_id', 'mr_season_se_id', 'stl_year', 'mr_brand_br_id', 'mr_buyer_b_id', 'unit_id', 'stl_product_name']);
          $style_info->mnthyr=$request->mnthyr;
           // dd($style_info);

          $reservation = Reservation::getReservationForOrderEntyr((array)$style_info);
           if($reservation!=null){
              $order = OrderEntry::getResIdWiseOrder($reservation->id);
              $orderQty = $order->sum??0;
              // dd($reservation->id);
              $reservation->resbalance=$reservation->res_quantity-$orderQty; 
              // dd($reservation);
             }else{
                $reservation = (object)[];
               $reservation->resbalance=null;
              }
        
        return response()->json($reservation);
    }
}
