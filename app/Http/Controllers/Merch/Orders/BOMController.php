<?php

namespace App\Http\Controllers\Merch\Orders;

use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Merch\Brand;
use App\Models\Merch\Buyer;
use App\Models\Merch\OperationCost;
use App\Models\Merch\OrderBOM;
use App\Models\Merch\OrderEntry;
use App\Models\Merch\SampleStyle;
use App\Models\Merch\Season;
use App\Models\Merch\Style;
use App\Models\Merch\StyleSpecialMachine;
use App\Models\UOM;
use App\Packages\QueryExtra\QueryExtra;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Exports\Merch\OrderBomExport;
use Maatwebsite\Excel\Facades\Excel;

class BOMController extends Controller
{
	public function __construct()
    {
        ini_set('zlib.output_compression', 1);
    }

    public function index(){

		$unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())->pluck('hr_unit_name', 'hr_unit_id');
		$buyerList= Buyer::whereIn('b_id', auth()->user()->buyer_permissions())->pluck('b_name', 'b_id');
		$brandList= Brand::pluck('br_name','br_id');
		$styleList= Style::pluck('stl_no', 'stl_id');
		$seasonList= Season::pluck('se_name', 'se_id');
		return view("merch.order_bom.order_bom_list", compact('buyerList', 'seasonList', 'unitList', 'brandList', 'styleList'));
	}
    // Order List Data for Order BOM
	public function getListData(){
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

	 	}elseif (auth()->user()->hasRole('merchandising_executive')) {
		 	$executive_associateId[] = auth()->user()->associate_id;

		 	$teamid = DB::table('hr_as_basic_info as b')
				->where('associate_id',auth()->user()->associate_id)
				->leftJoin('mr_excecutive_team_members','b.as_id','mr_excecutive_team_members.member_id')
				->pluck('mr_excecutive_team_id');
			$team_lead = DB::table('mr_excecutive_team')
					 ->whereIn('id',$teamid)
					 ->leftJoin('hr_as_basic_info as b','mr_excecutive_team.team_lead_id','b.as_id')
					 ->pluck('associate_id');
			$team_members_associateId = DB::table('mr_excecutive_team_members')
									->whereIn('mr_excecutive_team_id',$teamid)
									->leftJoin('hr_as_basic_info as b','mr_excecutive_team_members.member_id','b.as_id')
									->pluck('associate_id');

			$team = array_merge($team_members_associateId->toArray(),$team_lead->toArray());
		}else{
		 	$team =[];
		}
		$getBuyer = buyer_by_id();
		$getSeason = season_by_id();
		$getBrand = brand_by_id();
		$query= DB::table('mr_order_entry AS OE')
		->select([
			"OE.order_id",
			"OE.order_code",
			"stl.stl_no",
			"stl.stl_year",
			"stl.mr_season_se_id",
			"stl.mr_brand_br_id",
			"OE.order_ref_no",
			"OE.mr_buyer_b_id",
			"OE.order_qty",
			"OE.order_delivery_date",
			"OE.unit_id"
		])
		->whereIn('OE.mr_buyer_b_id', auth()->user()->buyer_permissions())
		->leftJoin('mr_style AS stl', 'stl.stl_id', "OE.mr_style_stl_id");
		if(!empty($team)){
			$query->whereIn('OE.created_by', $team);
		}
		$data = $query->orderBy('OE.order_id', 'DESC')
		->get();
		$getUnit = unit_by_id();

		return DataTables::of($data)
		->addIndexColumn()
		->editColumn('hr_unit_name', function($data) use ($getUnit){
			return $getUnit[$data->unit_id]['hr_unit_name']??'';
		})
		->editColumn('b_name', function($data) use ($getBuyer){
			return $getBuyer[$data->mr_buyer_b_id]->b_name??'';
		})
		->editColumn('br_name', function($data) use ($getBrand){
			return $getBrand[$data->mr_brand_br_id]->br_name??'';
		})
		->editColumn('se_name', function($data) use ($getSeason){
			return $getSeason[$data->mr_season_se_id]->se_name??''. '-'.$data->stl_year;
		})
		->editColumn('order_delivery_date', function($data){
			return custom_date_format($data->order_delivery_date);
		})
		->addColumn('action', function ($data) {
			$action_buttons= "<div class=\"btn-group\">
			<a href=".url('merch/order/bom/'.$data->order_id)." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Order BOM\">
			<i class=\"ace-icon fa fa-pencil bigger-120\"></i>
			</a></div>";
			return $action_buttons;
		})
		->rawColumns([
            'order_code', 'hr_unit_name', 'b_name', 'br_name', 'se_name', 'stl_no', 'order_qty', 'order_delivery_date', 'action'
        ])
        ->make(true);
	}

    public function show(Request $request, $id)
    {
		try {
			$order = OrderEntry::orderInfoWithStyle($id);
            $uomList  = DB::table('uom')->select('measurement_name as id','measurement_name AS text')->get();
            //$uomList  = UOM::all();

			if($order != null){

				$uom = uom_by_id();
  				$uom = collect($uom)->pluck('measurement_name','id');
  				$getBom = DB::table('mr_order_bom_costing_booking AS b')

				->select('b.id', 'b.mr_material_category_mcat_id AS mcat_id', 'b.mr_cat_item_id', 'c.order_qty', 'b.item_description', 'b.clr_id', 'b.size', 'b.mr_supplier_sup_id','b.thread_brand', 'b.mr_article_id', 'b.uom', 'b.consumption', 'b.garments_qty', 'b.extra_percent', DB::raw('(consumption/100)*extra_percent AS qty'), DB::raw('((consumption/100)*extra_percent)+consumption AS total'),
					DB::raw('(((consumption/100)*extra_percent)+consumption)*c.order_qty as required_qty'),'b.sl', 'b.depends_on', 'b.order_id', 'b.stl_bom_id')
				->where('b.order_id', $id)
				->leftJoin('mr_order_entry as c', 'c.order_id','b.order_id')
				->orderBy('b.sl', 'asc')
				->get();



				// dd($getBom);


				if(count($getBom) == 0){
					$getBom = DB::table('mr_stl_bom_n_costing AS b')
					->select('b.id', 'b.mr_material_category_mcat_id AS mcat_id', 'b.mr_cat_item_id', 'b.item_description', 'b.clr_id', 'b.size', 'b.mr_supplier_sup_id', 'b.mr_article_id', 'b.uom', 'b.consumption', 'b.extra_percent','b.gmt_qty', DB::raw('(consumption/100)*extra_percent AS qty'), DB::raw('((consumption/100)*extra_percent)+consumption AS total'), 'b.sl', 'b.id AS stl_bom_id')
					->where('b.mr_style_stl_id', $order->mr_style_stl_id)
					->orderBy('b.sl', 'asc')
					->get();
				}
				// return $getBom;
				$getSupplier = array();
				$getArticle = array();
				$getItems = array();
				$groupBom = collect($getBom->toArray())->groupBy('mcat_id',true);


				if(count($getBom) > 0){
					// get Supplier
					$getCat = array_keys($groupBom->toArray());
					$supplierData = DB::table('mr_supplier');
					$supplierDataSql = $supplierData->toSql();
					$getSupplier = DB::table('mr_supplier_item_type AS si')
					->select('s.sup_id', 's.sup_name', 'si.mcat_id')
					->join(DB::raw('(' . $supplierDataSql. ') AS s'), function($join) use ($supplierData) {
		                $join->on('s.sup_id','si.mr_supplier_sup_id')->addBinding($supplierData->getBindings());
		            })
	            	->whereIn('si.mcat_id', $getCat)
	            	->get()
	            	->groupBy('mcat_id',true)
	            	->toArray();

	            	$getItemSupplier = array_column($getBom->toArray(), 'mr_supplier_sup_id');
	            	$getItemSup = array_unique($getItemSupplier);
	            	// get Article
	            	$getArticle = DB::table('mr_article')
	            	->select('id', 'art_name', 'mr_supplier_sup_id')
	            	->whereIn('mr_supplier_sup_id', $getItemSup)
	            	->get()
	            	->groupBy('mr_supplier_sup_id',true)
	            	->toArray();

	            	// item
	            	// dd($itemsId);
	            	$itemsId = array_column($getBom->toArray(), 'mr_cat_item_id');
	            	$getItems = DB::table('mr_cat_item AS i')
		            ->select('i.id','i.item_name','i.item_code', 'i.dependent_on')
		            ->whereIn('i.id', $itemsId)
		            ->get()
		            ->keyBy('id')
		            ->toArray();

	            	$uomData = DB::table('uom');
            		$uomData_sql = $uomData->toSql();

	            	$getItemUom = DB::table('mr_cat_item_uom AS iu')
	            	->select('u.id AS id', 'u.measurement_name AS text','iu.mr_cat_item_id')
	            	->whereIn('iu.mr_cat_item_id', $itemsId)
	            	->leftjoin(DB::raw('(' . $uomData_sql. ') AS u'), function($join) use ($uomData) {
		                $join->on('iu.uom_id','u.id')->addBinding($uomData->getBindings());
		            })
	            	->get()
	            	->groupBy('mr_cat_item_id',true)
	            	->toArray();
	            	// return $getItemUom;
	            	foreach ($getItems as $key => $item) {
	            		if(isset($getItemUom[$item->id])){
	            			$itemUom  = collect($getItemUom[$item->id])->pluck('text', 'id');
	            			$item->uom = $itemUom;
	            		}else{
	            			$item->uom = $uom;
	            		}
	            	}
				}

			    $samples = SampleStyle::getStyleIdWiseSampleName($order->mr_style_stl_id);
			    $operations = OperationCost::getStyleIdWiseOperationCostName($order->mr_style_stl_id);
			    $machines = StyleSpecialMachine::getStyleIdWiseSpMachineName($order->mr_style_stl_id);
			    $getColor = DB::table("mr_material_color")->select('clr_id AS id', 'clr_name AS text')->get();
			    $itemCategory = item_category_by_id();
			    $getUnit = unit_by_id();
			    $getBuyer = buyer_by_id();

                if(isset($request->export)){
							$filename = 'Style-Bom';
							$filename .= '.xlsx';
							return Excel::download(new OrderBomExport($data), $filename);
						}

			    //dd($getColor);
			    return view('merch.order_bom.index', compact('order','uomList', 'samples', 'operations', 'machines', 'getColor', 'itemCategory', 'uom', 'groupBom', 'getArticle', 'getSupplier', 'getItems', 'getUnit', 'getBuyer'));
			}
			toastr()->error("Order Not Found!");
			return back();
		} catch (\Exception $e) {
			$bug = $e->getMessage();
		    toastr()->error($bug);
		    return back();
		}
    }

    public function ajaxStore(Request $request)
    {
    	$input = $request->all();
    	$data['type'] = 'error';
    	$data['value'] = [];
    	// return $input;
    	DB::beginTransaction();
    	try {
    		// check order exists
    		$oldItem = array_filter($input['bomitemid'], 'strlen');
	    	$getItemBom = OrderBOM::getOrderWiseItem($input['order_id'], ['id']);
	    	$itemBomCount = count($getItemBom);
	    	$getBomId = collect($getItemBom->toArray())->pluck('id')->toArray();
	    	$itemDiff = array_diff($getBomId, $oldItem);
	    	// return $itemDiff;
	    	for ($d=0; $d < count($itemDiff); $d++) {
	    		OrderBOM::whereIn('id', $itemDiff)->delete();
	    	}



    		$sl = 1;
    		$updateBOM = [];
    		for ($i=0; $i<sizeof($input['itemid']); $i++){
    			$itemId = $input['itemid'][$i];
            	if($itemId != null){
            		$bom = [
            			'mr_style_stl_id' => $input['stl_id'],
            			'mr_material_category_mcat_id' => $input['itemcatid'][$i],
            			'mr_cat_item_id' => $itemId,
            			'item_description' => $input['description'][$i],
            			'clr_id' => $input['color'][$i],
            			'size' => $input['size_width'][$i],
            			'mr_supplier_sup_id' => $input['supplierid'][$i],
            			'mr_article_id' => $input['articleid'][$i],
            			'uom' => $input['uomname'][$i],
            			'consumption' => $input['consumption'][$i],
                        'garments_qty' => $input['garments_qty'][$i],
                        'thread_brand' => $input['thread_brand'][$i],
            			'extra_percent' => $input['extraper'][$i],
                        'precost_req_qty' => ($input['consumption'][$i]+(($input['consumption'][$i]*$input['extraper'][$i])/100))*$request->order_qty,
            			'order_id' => $input['order_id'],
            			'depends_on' => $input['depends_on'][$i],
            			'sl' => $sl,
            			'stl_bom_id' => $input['stl_bom_id'][$i],
            		];
            		if($input['bomitemid'][$i] != null && $itemBomCount > 0){
            			// update
            			$updateBOM[] =
					    [
							'data' => $bom,
							'keyval' => $input['bomitemid'][$i]
					    ];
            			// OrderBOM::where('id', $input['bomitemid'][$i])->update($bom);
            		}else{
            			// create
            			$bomId = OrderBOM::create($bom)->id;
            			$data['value'][$i] = $bomId;
            		}

            		$sl++;
            	}

            }

            // update mr_order_bom_costing_booking
            if(count($updateBOM) > 0){
            	(new QueryExtra)
			    ->table('mr_order_bom_costing_booking')
			    ->whereKey('id')
			    ->bulkup($updateBOM);
            }

            //log_file_write("BOM Successfully Save", $input['stl_id']);
            DB::commit();
	        $data['type'] = 'success';
	        $data['message'] = "BOM Successfully Save.";
	        return response()->json($data);
    	} catch (\Exception $e) {
    		DB::rollback();
    		$bug = $e->getMessage();
	        $data['message'] = $bug;
	        return response()->json($data);
    	}
    }

    ///// single view

     public function orderSingleview(Request $request, $id)
    {
		try {
			$data['order'] = OrderEntry::orderInfoWithStyle($id);
            $data['uomList']  = DB::table('uom')->select('measurement_name as id','measurement_name AS text')->get();
            //$uomList  = UOM::all();

			if($data['order']  != null){

				$uom = uom_by_id();
  				$data['uom'] = collect($uom)->pluck('measurement_name','id');
  				$data['getBom'] = DB::table('mr_order_bom_costing_booking AS b')
                 ->select('b.id', 'b.mr_material_category_mcat_id AS mcat_id', 'b.mr_cat_item_id', 'c.order_qty', 'b.item_description', 'b.clr_id', 'b.size', 'b.mr_supplier_sup_id','b.thread_brand', 'b.mr_article_id', 'b.uom', 'b.consumption', 'b.garments_qty', 'b.extra_percent', DB::raw('(consumption/100)*extra_percent AS qty'), DB::raw('((consumption/100)*extra_percent)+consumption AS total'),
					DB::raw('(((consumption/100)*extra_percent)+consumption)*c.order_qty as required_qty'),'b.sl', 'b.depends_on', 'b.order_id', 'b.stl_bom_id')
				->where('b.order_id', $id)
				->leftJoin('mr_order_entry as c', 'c.order_id','b.order_id')
				->orderBy('b.sl', 'asc')
				->get();



				// dd($getBom);


				if(count($data['getBom'] ) == 0){
					$data['getBom']  = DB::table('mr_stl_bom_n_costing AS b')
					->select('b.id', 'b.mr_material_category_mcat_id AS mcat_id', 'b.mr_cat_item_id', 'b.item_description', 'b.clr_id', 'b.size', 'b.mr_supplier_sup_id', 'b.mr_article_id', 'b.uom', 'b.consumption', 'b.extra_percent','b.gmt_qty', DB::raw('(consumption/100)*extra_percent AS qty'), DB::raw('((consumption/100)*extra_percent)+consumption AS total'), 'b.sl', 'b.id AS stl_bom_id')
					->where('b.mr_style_stl_id', $order->mr_style_stl_id)
					->orderBy('b.sl', 'asc')
					->get();
				}
				// return $getBom;
				$data['getSupplier'] = array();
				$data['getArticle'] = array();
				$data['getItems'] = array();
				$data['groupBom'] = collect($data['getBom']->toArray())->groupBy('mcat_id',true);


				if(count($data['getBom']) > 0){
					// get Supplier
					$data['getCat'] = array_keys($data['groupBom']->toArray());
					$supplierData= DB::table('mr_supplier');
					$supplierDataSql= $supplierData->toSql();
					$data['getSupplier'] = DB::table('mr_supplier_item_type AS si')
					->select('s.sup_id', 's.sup_name', 'si.mcat_id')
					->join(DB::raw('(' . $supplierDataSql. ') AS s'), function($join)
					 use ($supplierData ) {
		                $join->on('s.sup_id','si.mr_supplier_sup_id')->addBinding($supplierData->getBindings());
		            })
	            	->whereIn('si.mcat_id', $data['getCat'])
	            	->get()
	            	->groupBy('mcat_id',true)
	            	->toArray();
///////////////

	            	$data['getItemSupplier'] = array_column($data['getBom']->toArray(), 'mr_supplier_sup_id');
	            	$data['getItemSup'] = array_unique($data['getItemSupplier']);
	            	// get Article
	            	$data['getArticle'] = DB::table('mr_article')
	            	->select('id', 'art_name', 'mr_supplier_sup_id')
	            	->whereIn('mr_supplier_sup_id', $data['getItemSup'])
	            	->get()
	            	->groupBy('mr_supplier_sup_id',true)
	            	->toArray();

	            	// item
	            	// dd($itemsId);
	            	$data['itemsId'] = array_column($data['getBom']->toArray(), 'mr_cat_item_id');
	            	// dd($itemsId);
	            	$data['getItems'] = DB::table('mr_cat_item AS i')
		            ->select('i.id','i.item_name','i.item_code', 'i.dependent_on')
		            ->whereIn('i.id', $data['itemsId'])
		            ->get()
		            ->keyBy('id')
		            ->toArray();

	            	$uomData = DB::table('uom');
            		$data['uomData_sql'] = $uomData->toSql();

	            	$data['getItemUom'] = DB::table('mr_cat_item_uom AS iu')
	            	->select('u.id AS id', 'u.measurement_name AS text','iu.mr_cat_item_id')
	            	->whereIn('iu.mr_cat_item_id', $data['itemsId'])
	            	->leftjoin(DB::raw('(' . $data['uomData_sql']. ') AS u'), function($join) use ($uomData) {
		                $join->on('iu.uom_id','u.id')->addBinding($uomData->getBindings());
		            })
	            	->get()
	            	->groupBy('mr_cat_item_id',true)
	            	->toArray();
	            	// return $getItemUom;
	            	foreach ($data['getItems'] as $key => $item) {
	            		if(isset($data['getItemUom'][$item->id])){
	            			$data['itemUom']  = collect($data['getItemUom'][$item->id])->pluck('text', 'id');
	            			$data['item'] = (object)[];
	            			$data['item']->uom = $data['itemUom'] ;
	            		}else{
	            			$data['item'] = (object)[];

	            			$data['item'] = $uom;
	            		}
	            	}
				}

				// dd($data['getBom']);

			    $data['samples'] = SampleStyle::getStyleIdWiseSampleName($data['order']->mr_style_stl_id);
			    $data['operations'] = OperationCost::getStyleIdWiseOperationCostName($data['order']->mr_style_stl_id);
			    $data['machines'] = StyleSpecialMachine::getStyleIdWiseSpMachineName($data['order']->mr_style_stl_id);
			    $data['getColor'] = DB::table("mr_material_color")->select('clr_id AS id', 'clr_name AS text')->get();
			    $data['itemCategory'] = item_category_by_id();
			    $data['getUnit'] = unit_by_id();
			    $data['getBuyer'] = buyer_by_id();

			     $data['excelVieworder']= DB::table('mr_order_entry AS c')
                ->select('j.hr_unit_name','c.order_code','h.stl_no','g.b_name','c.order_qty')
				->where('c.order_id', $id)
				->leftJoin('mr_buyer as g', 'g.b_id','c.mr_buyer_b_id')
				->leftJoin('mr_style as h', 'h.stl_id','c.mr_style_stl_id')
				->leftJoin('hr_unit as j', 'j.hr_unit_id','c.unit_id')
				->first();

				// dd($excelVieworder);

			    $data['excelView']= DB::table('mr_order_bom_costing_booking AS b')
                 ->select('j.hr_unit_name','c.order_code','h.stl_no','g.b_name','e.item_name','f.art_name','d.sup_name','b.id', 'b.mr_material_category_mcat_id AS mcat_id', 'b.mr_cat_item_id', 'c.order_qty', 'b.item_description', 'b.clr_id', 'b.size', 'b.mr_supplier_sup_id','b.thread_brand', 'b.mr_article_id', 'b.uom', 'b.consumption', 'b.garments_qty', 'b.extra_percent', DB::raw('(consumption/100)*extra_percent AS qty'), DB::raw('((consumption/100)*extra_percent)+consumption AS total'),
					DB::raw('(((consumption/100)*extra_percent)+consumption)*c.order_qty as required_qty'),'b.sl', 'b.depends_on', 'b.order_id', 'b.stl_bom_id')
				->where('b.order_id', $id)
				->leftJoin('mr_order_entry as c', 'c.order_id','b.order_id')
				->leftJoin('mr_supplier as d', 'd.sup_id','b.mr_supplier_sup_id')
				->leftJoin('mr_cat_item as e', 'e.id','b.mr_cat_item_id')
				->leftJoin('mr_article as f', 'f.id','b.mr_article_id')
				->leftJoin('mr_buyer as g', 'g.b_id','c.mr_buyer_b_id')
				->leftJoin('mr_style as h', 'h.stl_id','b.mr_style_stl_id')
				->leftJoin('hr_unit as j', 'j.hr_unit_id','c.unit_id')
				->orderBy('b.sl', 'asc')
				->get();
				// dd($excelView);
               
               // dd($request->export);
               /// For Order download
                if($request->export=='excel'){
							$filename = 'Order-Bom';
							$filename .= '.xlsx';
							return Excel::download(new OrderBomExport($data), $filename);
						}

 
				// if($request->export=='excel'){
				// 			$filename = 'Order-Costing';
				// 			$filename .= '.xlsx';
				// 			return Excel::download(new OrderCostingExport($data), $filename);
				// 		}	

			    //dd($getColor);
			    return view('merch.order_bom.orderSingleView', $data);
			}
			toastr()->error("Order Not Found!");
			return back();
		} catch (\Exception $e) {
			// dd($e);
			$bug = $e->getMessage();
		    toastr()->error($bug);
		    return back();
		}
    }
}
