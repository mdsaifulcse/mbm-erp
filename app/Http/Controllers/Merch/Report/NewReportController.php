<?php

namespace App\Http\Controllers\Merch\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Merch\Buyer;
use App\Models\Merch\Brand;
use App\Models\Merch\Style;
use App\Models\Merch\Season;
use App\Models\Merch\OrderEntry;
use App\Models\Merch\PurchaseOrder;
use App\Models\Merch\Reservation;
use App\Models\Merch\PoInseamSize;
use App\Models\Merch\PoSubStyle;
use App\Models\Merch\Country;
use App\Models\Merch\OrderBOM;
use App\Models\Merch\OrderBomOtherCosting;
use App\Models\Merch\OrderOperationNCost;
use App\Http\Controllers\Merch\ShortCodeLib as ShortCodeLib;
use DB, ACL, Validator,DataTables;



class NewReportController extends Controller
{

  public function styleDetailsFinal(Request $request){
   
        $b_permissions =  auth()->user()->buyer_permissions();
        $buyerList        = DB::table('mr_buyer as b')
        ->whereIn('b.b_id', $b_permissions)
        ->pluck('b.b_name', 'b.b_id');
        $seasonList = Season::pluck('se_name','se_id');
        $productTypeList  = collect(product_type_by_id())->pluck('prd_type_name', 'prd_type_id');
        //data by id
        $getBuyer = buyer_by_id();
        $getSeason = season_by_id();
        $getBrand = brand_by_id();
        $getProductType = product_type_by_id();
        $getGermentType = garment_type_by_id();

        $styleDetails = DB::table('mr_style AS b')
    
            ->select([
                DB::raw("GROUP_CONCAT( distinct (o.opr_name) order by oc.mr_operation_opr_id asc SEPARATOR ', ') AS operation "),
                DB::raw("GROUP_CONCAT(distinct (m.spmachine_name) SEPARATOR ', ') AS spmachine_name"),
                DB::raw("GROUP_CONCAT(distinct (st.sample_name)) AS sample"),
                
                
                'b.stl_id',
                'b.mr_buyer_b_id',
                'b.stl_no',
                'b.stl_description',
                'b.stl_product_name',
                'b.mr_season_se_id',
                'b.mr_brand_br_id',
                'b.prd_type_id',
                'b.gmt_id',
                'b.gender',
                'b.stl_smv',
                'b.stl_img_link',
                'b.stl_year'
                
 
            ])
            ->leftJoin('mr_style_operation_n_cost AS oc', 'oc.mr_style_stl_id','=',  'b.stl_id')
            ->leftJoin('mr_operation AS o', 'o.opr_id', '=',  'oc.mr_operation_opr_id')
            ->leftJoin('mr_style_sp_machine AS sm', 'sm.stl_id','=',  'b.stl_id')
            ->leftJoin('mr_special_machine AS m', 'm.spmachine_id', '=',  'sm.spmachine_id')
            ->leftJoin('mr_stl_sample AS ss', 'ss.stl_id','=',  'b.stl_id')
            ->leftJoin('mr_sample_type AS st', 'st.sample_id', '=',  'ss.sample_id')
            
            ->where(function ($query) use ($request) {
                if($request->gender != null){
                    $query->where('b.gender', $request->gender);
                  }
                if($request->buyer != ""){
                  $query->where('b.mr_buyer_b_id', '=', $request->buyer);
                  }
                if($request->season != ""){
                  $query->where('b.mr_season_se_id', '=', $request->season);
                  }
                if($request->productType != ""){
                  $query->where('b.prd_type_id', '=', $request->productType);
                  }
              
          })    
            ->orderBy('b.stl_id', 'desc')
            
            ->groupBy('oc.mr_style_stl_id') 
            ->get();

           
            $stlIds = collect($styleDetails)->pluck('stl_id');

            
           

            //dd($styleDetails);
        

        return view('merch.report.style_details_final', compact('buyerList','seasonList','productTypeList','styleDetails','getBuyer','getSeason','getBrand','getProductType','getGermentType'));
  }

	

   # show list
  public function styleDetails()
  {
    $b_permissions =  auth()->user()->buyer_permissions();
    $buyerList        = DB::table('mr_buyer as b')
    ->whereIn('b.b_id', $b_permissions)
    ->pluck('b.b_name', 'b.b_id')
    ->toArray();
    //$buyerList  = Buyer::pluck('b_name', 'b_id');
   
    $productTypeList  = collect(product_type_by_id())->pluck('prd_type_name', 'prd_type_id');
    $seasonList = Season::pluck('se_name','se_id');
    return view("merch.report.style_details", compact(
      "buyerList",
      "seasonList",
      'productTypeList'
    ));
  }

  # get data
  public function getData(Request $request)
  {
    //  dd($request->all());
    $getBuyer = buyer_by_id();
    $getSeason = season_by_id();
    $getBrand = brand_by_id();
    $getProductType = product_type_by_id();
    $getGermentType = garment_type_by_id();
    
    

    // $data = Style::getStyleInfo(["stl_id","gmt_id","gender", "stl_description", "stl_type", "prd_type_id", "stl_img_link", "mr_buyer_b_id", "mr_brand_br_id", "prd_type_id", "stl_no", "stl_product_name", "stl_smv", "mr_season_se_id", 'stl_year', 'bom_status', 'costing_status']);
    


    $data = DB::table('mr_style AS b')
    
            ->select([
                DB::raw("GROUP_CONCAT( distinct (o.opr_name) order by oc.mr_operation_opr_id asc) AS operation "),
                DB::raw("GROUP_CONCAT(distinct (m.spmachine_name) ) AS spmachine_name"),
                DB::raw("GROUP_CONCAT(distinct (st.sample_name)) AS sample"),
                
                
                'b.stl_id',
                'b.mr_buyer_b_id',
                'b.stl_no',
                'b.stl_description',
                'b.stl_product_name',
                'b.mr_season_se_id',
                'b.mr_brand_br_id',
                'b.prd_type_id',
                'b.gmt_id',
                'b.gender',
                'b.stl_smv',
                'b.stl_img_link',
                'b.stl_year'
                
 
            ])
            ->leftJoin('mr_style_operation_n_cost AS oc', 'oc.mr_style_stl_id','=',  'b.stl_id')
            ->leftJoin('mr_operation AS o', 'o.opr_id', '=',  'oc.mr_operation_opr_id')
            ->leftJoin('mr_style_sp_machine AS sm', 'sm.stl_id','=',  'b.stl_id')
            ->leftJoin('mr_special_machine AS m', 'm.spmachine_id', '=',  'sm.spmachine_id')
            ->leftJoin('mr_stl_sample AS ss', 'ss.stl_id','=',  'b.stl_id')
            ->leftJoin('mr_sample_type AS st', 'st.sample_id', '=',  'ss.sample_id')
            
            ->where(function ($query) use ($request) {
                if($request->gender != null){
                    $query->where('b.gender', $request->gender);
                  }
                if($request->buyer != ""){
                  $query->where('b.mr_buyer_b_id', '=', $request->buyer);
                  }
                if($request->season != ""){
                  $query->where('b.mr_season_se_id', '=', $request->season);
                  }
                if($request->productType != ""){
                  $query->where('b.prd_type_id', '=', $request->productType);
                  }
              
          })    
            ->orderBy('b.stl_id', 'desc')
            
            ->groupBy('oc.mr_style_stl_id') 
            ->get();

           
            $stlIds = collect($data)->pluck('stl_id');

    
    
    return DataTables::of($data)
        ->addIndexColumn()
        ->editColumn('stl_img_link', function ($data) {
          $imageUrl = style_picture($data);
          return '<img src="'.asset($imageUrl).'" width="30" height="40">';
        })

        
        ->editColumn('b_name', function ($data) use ($getBuyer) {
            return $getBuyer[$data->mr_buyer_b_id]->b_name??'';
        })
        ->editColumn('gmt_name', function ($data) use ($getGermentType) {
          return $getGermentType[$data->gmt_id]->gmt_name??'';
      })
        ->editColumn('br_name', function ($data) use ($getBrand) {
            return $getBrand[$data->mr_brand_br_id]->br_name??'';
        })
        ->editColumn('prd_type_name', function ($data) use ($getProductType) {
            return $getProductType[$data->prd_type_id]->prd_type_name??'';
        })
        ->editColumn('se_name', function ($data) use ($getSeason) {
            return $getSeason[$data->mr_season_se_id]->se_name.'-'.date('y', strtotime($data->stl_year))??'';
        })
      //   ->addColumn('special_machine', function($special_machine) {
          
      //       return $special_machine;
          
      // })
        
        
        
        ->rawColumns(['stl_img_link','se_name','b_name','stl_no','spmachine_name'])
        ->make(true);
  }


  public function orderDetails(){

        $getBuyer = buyer_by_id();
        $getUnit = unit_by_id();
        $getSeason = season_by_id();
        $getBrand = brand_by_id();
        // return $getUnit;

        $orederDetails = OrderEntry::getOrderListWithStyleResIdWise();
        $orderIds = collect($orederDetails)->pluck('order_id');
        $orderFOB = DB::table('mr_order_bom_other_costing')
        ->whereIn('mr_order_entry_order_id', $orderIds)
        ->pluck('agent_fob', 'mr_order_entry_order_id');
//dd($orederDetails);
        
    return view('merch.report.order_details',compact(
      "getBuyer",
      "getUnit",
      'getSeason','orederDetails','orderFOB'
    ));
  }

 /*  ############################ */

 //get report view
 public function getReport(Request $request){

  $start = !empty($request->range_from)?$request->range_from:'';
  $end  =  !empty($request->range_to)?$request->range_to:'';

  $unit = !empty($request->unit_id)?$request->unit_id:'';
  $buyer  =  !empty($request->buyer_id)?$request->buyer_id:'';

  $unitList =DB::table('hr_unit')->pluck('hr_unit_name','hr_unit_id');
  $buyerList = DB::table('mr_buyer')->pluck('b_name','b_id');
  $start = !empty($request->range_from)?$request->range_from:'';
  $end  =  !empty($request->range_to)?$request->range_to:'';

  if($request->type == 'style'){

    $style = DB::table('mr_style')
               ->leftJoin('mr_stl_bom_other_costing','mr_style.stl_id','=','mr_stl_bom_other_costing.mr_style_stl_id')
               ->leftJoin('mr_buyer','mr_style.mr_buyer_b_id','=','mr_buyer.b_id')
               ->when(!empty($request->range_from), function ($query) use($request){
                      return $query->whereBetween('stl_added_on', array(date($request->range_from), date($request->range_to)));
                 })
               ->groupBy('mr_style.stl_id')
               ->get();

             return view("merch.report.merch_report",compact("style","unitList","buyerList","start","end"));
  }elseif ($request->type == 'order') {
    if(auth()->user()->hasRole('merchandiser')){
      $lead_associateId[] = auth()->user()->associate_id;
     $team_members = DB::table('hr_as_basic_info as b')
        ->where('associate_id',auth()->user()->associate_id)
        ->leftJoin('mr_excecutive_team','b.as_id','mr_excecutive_team.team_lead_id')
        ->leftJoin('mr_excecutive_team_members','mr_excecutive_team.id','mr_excecutive_team_members.mr_excecutive_team_id')
        ->pluck('member_id');
    $team_members_associateId = DB::table('hr_as_basic_info as b')
                                 ->whereIn('as_id',$team_members)
                                 ->pluck('associate_id');
     $team = array_merge($team_members_associateId->toArray(),$lead_associateId);
     //dd($team);exit;
   }elseif (auth()->user()->hasRole('merchandising_executive')) {
      $executive_associateId[] = auth()->user()->associate_id;
         $team = $executive_associateId;
    }else{
     $team =[];
    }

       if(!empty($team)){
         $orders = DB::table('mr_order_entry')
                 ->when(!empty($request->range_from), function ($query) use($request){
                        return $query->whereBetween('mr_order_entry.created_at', array(date($request->range_from), date($request->range_to)));
                        })
                ->when(!empty($request->unit_id), function ($query) use($request){
                       return $query->where('mr_order_entry.unit_id',$request->unit_id);
                       })
                 ->when(!empty($request->buyer_id), function ($query) use($request){
                        return $query->where('mr_order_entry.mr_buyer_b_id',$request->buyer_id);
                        })
                 ->whereIn('mr_order_entry.created_by', $team)

                   ->paginate(10);
       }else{
         $orders = DB::table('mr_order_entry')
                 ->when(!empty($request->range_from), function ($query) use($request){
                        return $query->whereBetween('mr_order_entry.created_at', array(date($request->range_from), date($request->range_to)));
                        })
                ->when(!empty($request->unit_id), function ($query) use($request){
                       return $query->where('mr_order_entry.unit_id',$request->unit_id);
                       })
                 ->when(!empty($request->buyer_id), function ($query) use($request){
                        return $query->where('mr_order_entry.mr_buyer_b_id',$request->buyer_id);
                        })

                   ->paginate(10);
       }


                    //dd($orders);exit;

           $orders['data'] = $this->formateOrder($orders->toArray(),$buyerList,$unitList,$team);

             return view("merch.report.merch_report",compact("orders","unitList","buyerList","start","end","unit","buyer"));

  }elseif($request->type == 'bp'){
   if(!empty($request->range_from)){
      $res_month_year = explode('-', $request->range_from);
      $f_month = $res_month_year[1];
      $f_year = $res_month_year[0];
   }
   if(!empty($request->range_to)){
    $res_month_tyear = explode('-', $request->range_to);
    $t_month = $res_month_year[1];
    $t_year = $res_month_year[0];
 }

     if(!empty($f_month) && !empty($f_year) && !empty($t_month) && !empty($t_year)){

        $reservation = DB::table('mr_capacity_reservation')
               ->leftJoin('hr_unit','mr_capacity_reservation.hr_unit_id','=','hr_unit.hr_unit_id')

               ->leftJoin('mr_buyer','mr_capacity_reservation.b_id','=','mr_buyer.b_id')
               ->leftJoin('mr_product_type','mr_capacity_reservation.prd_type_id','=','mr_product_type.prd_type_id')
               ->whereBetween('mr_capacity_reservation.res_month', array(date($f_month), date($t_month)))
               ->orwhereBetween('mr_capacity_reservation.res_year', array(date($f_year), date($t_year)))
               ->get();


     $reservation = $this->formateRes($reservation->toArray());

     $data_store_unique = [];
     foreach($reservation as $k=>$each_res) {
       if(is_array($each_res) || is_object($each_res)) {
         foreach($each_res as $k2=>$e_res) {
           $data_store_unique[$k][] = $e_res->res_month.'-'.$e_res->res_year;

         }

         $data_store_unique[$k] = array_count_values($data_store_unique[$k]);

       }
     }

       // dd($reservation);exit;
     return view("merch.report.merch_report",compact("data_store_unique","reservation","unitList","buyerList","start","end","unit","buyer"));

   }else{
     $reservation = DB::table('mr_capacity_reservation')
              ->leftJoin('hr_unit','mr_capacity_reservation.hr_unit_id','=','hr_unit.hr_unit_id')

              ->leftJoin('mr_buyer','mr_capacity_reservation.b_id','=','mr_buyer.b_id')
              ->leftJoin('mr_product_type','mr_capacity_reservation.prd_type_id','=','mr_product_type.prd_type_id')
             ->get();


    $reservation = $this->formateRes($reservation->toArray());

    $data_store_unique = [];
    foreach($reservation as $k=>$each_res) {
      if(is_array($each_res) || is_object($each_res)) {
        foreach($each_res as $k2=>$e_res) {
          $data_store_unique[$k][] = $e_res->res_month.'-'.$e_res->res_year;

        }

        $data_store_unique[$k] = array_count_values($data_store_unique[$k]);

      }
    }

     // dd($reservation);exit;
    return view("merch.report.merch_report",compact("data_store_unique","reservation","unitList","buyerList","start","end","unit","buyer"));

   }

  }else{

    return view("merch.report.merch_report",compact("unitList","buyerList"));
 }

}

private function formateRes($reservation)
{
  $result = [];

   $gtotal = 0;
      $shtotal = 0;

  $bid = array_unique(array_column($reservation,'b_id'),SORT_REGULAR);

  foreach ($bid as $b) {

    $buyers =DB::table('mr_capacity_reservation')

               // ->leftJoin('mr_buyer','mr_capacity_reservation.b_id','=','mr_buyer.b_id')
               // ->leftJoin('mr_product_type','mr_capacity_reservation.prd_type_id','=','mr_product_type.prd_type_id')
               ->where('mr_capacity_reservation.b_id',$b)
               // ->groupBy('mr_capacity_reservation.b_id')
               ->get();
               // dd($buyers);exit;
               // $result[] = $buyers;

   foreach ($buyers as $bu) {
     //dd($result);exit;

          $buinfo =DB::table('mr_capacity_reservation')

               ->leftJoin('mr_buyer','mr_capacity_reservation.b_id','=','mr_buyer.b_id')
               ->leftJoin('mr_product_type','mr_capacity_reservation.prd_type_id','=','mr_product_type.prd_type_id')
               ->where('mr_capacity_reservation.res_id',$bu->res_id)
               ->first();

       $gtotal += $buinfo->res_quantity;
       $shtotal += $buinfo->res_sah;


       $result[$bu->b_id][] = $buinfo;
       // $result['gtotal'] = $gtotal;
       // $result['shtotal'] = $shtotal;

   }
  }

   //dd($result);exit;
  return $result;
}

//order report data fromating
private function formateOrder($orders,$buyerList,$unitList,$team){

        $result = [];
         $unitId = array_unique(array_column($orders['data'],'unit_id'),SORT_REGULAR);

        foreach ($unitId as $unit) {
              $buyers = DB::table('mr_order_entry')->where('unit_id','=',$unit)->select('mr_buyer_b_id','order_id')->get();

              foreach ($buyers as $k=>$buyer) {
                 $border = DB::table('mr_order_entry')
                               ->leftJoin('mr_style','mr_order_entry.mr_style_stl_id','=','mr_style.stl_id')
                               ->leftJoin('mr_stl_bom_other_costing','mr_style.stl_id','=','mr_stl_bom_other_costing.mr_style_stl_id')
                               ->leftJoin('mr_capacity_reservation','mr_order_entry.res_id','=','mr_capacity_reservation.res_id')
                                ->where('mr_order_entry.mr_buyer_b_id','=',$buyer->mr_buyer_b_id)
                                ->where('mr_order_entry.order_id','=',$buyer->order_id)
                                // ->whereIn('mr_order_entry.created_by',$team)
                                ->groupBy('mr_style.stl_id')
                                ->first();
                $result[$unitList[$unit]][$buyerList[$buyer->mr_buyer_b_id]][] = $border;

              }
        }
  return $result;
}

 /*  ############################ */
 
   
}
