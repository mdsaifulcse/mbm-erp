<?php

namespace App\Http\Controllers\Merch\PO;

use App\Http\Controllers\Controller;
use App\Models\Merch\MrPoBomOtherCosting;
use App\Models\Merch\MrPoOperationNCost;
use App\Models\Merch\OperationCost;
use App\Models\Merch\OrderBOM;
use App\Models\Merch\OrderBomOtherCosting;
use App\Models\Merch\OrderEntry;
use App\Models\Merch\OrderOperationNCost;
use App\Models\Merch\PoBOM;
use App\Models\Merch\PurchaseOrder;
use App\Models\Merch\SampleStyle;
use App\Models\Merch\StyleSpecialMachine;
use App\Models\Merch\McatItem;
use App\Packages\QueryExtra\QueryExtra;
use App\Exports\Merch\PoCostingExport;
use App\Exports\Merch\PoBomExport;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use Illuminate\Http\Request;

class CostingController extends Controller
{
	public function __construct()
    {
        ini_set('zlib.output_compression', 1);
    }

    public function show(Request $request, $id)
    {
    	try {
			$data = [];
			$data['id'] = $id;
			

    		$data['po'] = PurchaseOrder::findOrFail($id);
			if($data['po'] == null){
				toastr()->error("PO Not Found!");
				return back();
			}
    		$data['orderId'] = $data['po']->mr_order_entry_order_id;
			$data['order'] = OrderEntry::orderInfoWithStyle($data['orderId']);

			if($data['order'] == null){
				toastr()->error("Order Not Found!");
				return back();
			}

			// PO BOM & Costing
			$data['getBom'] = PoBOM::getPoIdWisePoBOM($id);
			$data['groupBom'] = collect($data['getBom']->toArray())->groupBy('mcat_id',true);

			$data['specialOperation'] = MrPoOperationNCost::getPoIdWiseOperationInfo($id, 2);
			$data['otherCosting'] = MrPoBomOtherCosting::getOpIdWisePoOtherCosting($id);

			// order costing info
			$data['orderCosting'] = OrderBOM::getOrderIdWiseOrderBOM($data['orderId']);
			$data['orderCosting'] = collect($data['orderCosting']->toArray())->keyBy('id')->toArray();

			$data['ordSPOperation'] = OrderOperationNCost::getOrderIdWiseOperationInfo($data['orderId'], 2);
			$data['ordOthCosting'] = OrderBomOtherCosting::getOrderIdWiseOrderOtherCosting($data['orderId']);
			// other operation
			$data['samples'] = SampleStyle::getStyleIdWiseSampleName($data['order']->mr_style_stl_id);
		    $data['operations'] = OperationCost::getStyleIdWiseOperationCostName($data['order']->mr_style_stl_id);
		    $data['machines'] = StyleSpecialMachine::getStyleIdWiseSpMachineName($data['order']->mr_style_stl_id);
		    // cache data
		    $data['getUnit'] = unit_by_id();
		    $data['getSupplier'] = supplier_by_id();
        	$data['getArticle'] = article_by_id();
        	$data['getItem'] = item_by_id();
		    $data['getColor'] = material_color_by_id();
			
		    $data['itemCategory'] = item_category_by_id();
		    $data['getBuyer'] = buyer_by_id();
		    $data['uom'] = uom_by_id();
			$data['uom'] = collect($data['uom'])->pluck('measurement_name','id');

			

		    return view('merch.po.costing', $data);

		} catch (\Exception $e) {
			//dd($e);
			$bug = $e->getMessage();
		    toastr()->error($bug);
		    return back();
		}
    }

	

	public function single_view(Request $request, $id)
    {
    	try {
			$data = [];
			$data['id'] = $id;
			

    		$data['po'] = PurchaseOrder::findOrFail($id);
			if($data['po'] == null){
				toastr()->error("PO Not Found!");
				return back();
			}
    		$data['orderId'] = $data['po']->mr_order_entry_order_id;
			$data['order'] = OrderEntry::orderInfoWithStyle($data['orderId']);

			if($data['order'] == null){
				toastr()->error("Order Not Found!");
				return back();
			}
			

			// PO BOM & Costing
			$data['getBom'] = PoBOM::getPoIdWisePoBOM($id);
			$data['groupBom'] = collect($data['getBom']->toArray())->groupBy('mcat_id',true);
			

			$data['specialOperation'] = MrPoOperationNCost::getPoIdWiseOperationInfo($id, 2);
			$data['otherCosting'] = MrPoBomOtherCosting::getOpIdWisePoOtherCosting($id);

			// item
        	$itemsId = array_column($data['getBom']->toArray(), 'mr_cat_item_id');
        	$data['getItems'] = McatItem::getItemListItemIdsWise($itemsId);
        	$data['getItems'] = collect($data['getItems']->toArray())->keyBy('id');

			// order costing info
			$data['orderCosting'] = OrderBOM::getOrderIdWiseOrderBOM($data['orderId']);
			$data['orderCosting'] = collect($data['orderCosting']->toArray())->keyBy('id')->toArray();

			$data['ordSPOperation'] = OrderOperationNCost::getOrderIdWiseOperationInfo($data['orderId'], 2);
			$data['ordOthCosting'] = OrderBomOtherCosting::getOrderIdWiseOrderOtherCosting($data['orderId']);
			// other operation
			$data['samples'] = SampleStyle::getStyleIdWiseSampleName($data['order']->mr_style_stl_id);
		    $data['operations'] = OperationCost::getStyleIdWiseOperationCostName($data['order']->mr_style_stl_id);
		    $data['machines'] = StyleSpecialMachine::getStyleIdWiseSpMachineName($data['order']->mr_style_stl_id);
		    // cache data
		    $data['getUnit'] = unit_by_id();
			$data['getSeason'] = season_by_id();
		    $data['getSupplier'] = supplier_by_id();
        	$data['getArticle'] = article_by_id();
        	$data['getItem'] = item_by_id();
		    $data['getColor'] = material_color_by_id();
		    $data['itemCategory'] = item_category_by_id();
		    $data['getBuyer'] = buyer_by_id();
			$data['getCountry'] = country_by_id();
		    $data['uom'] = uom_by_id();
			$data['uom'] = collect($data['uom'])->pluck('measurement_name','id');



		

			if(isset($request->export_costing)){
				$filename = 'Po-costing';
				$filename .= '.xlsx';
				return Excel::download(new PoCostingExport($data), $filename);
			}

			if(isset($request->export_bom)){
                $filename = 'po-Bom';
                $filename .= '.xlsx';
                return Excel::download(new PoBomExport($data), $filename);
            }

		   

		} catch (\Exception $e) {
			//dd($e);
			$bug = $e->getMessage();
		    toastr()->error($bug);
		    return back();
		}
    }

    public function ajaxStore(Request $request)
    {
    	$input = $request->all();
    	$data['type'] = 'error';
    	// return $input;
    	DB::beginTransaction();
    	try {
    		// BOM costing update
    		$updateCosting = [];
    		for ($i=0; $i < sizeof($input['itemid']); $i++){
    			$itemId = $input['itemid'][$i];
            	if($itemId != null){
/*            		$term = "C&F";
        			if($input['precost_fob'][$i] > 0 || $input['precost_lc'][$i] > 0 || $input['precost_freight'][$i] > 0){
        				$term = "FOB";
        			}*/

            		$bom = [
            			'bom_term' => $input['terms'][$i],
            			'precost_fob' => $input['precost_fob'][$i],
            			'precost_lc' => $input['precost_lc'][$i],
            			'precost_freight' => $input['precost_freight'][$i],
            			'precost_unit_price' => $input['precost_unit_price'][$i]
            		];
            		$updateCosting[] =
                    [
                        'data' => $bom,
                        'keyval' => $input['bomitemid'][$i]
                    ];
            		// PoBOM::where('id', $input['bomitemid'][$i])->update($bom);
            	}
            }
            // update mr_po_bom_costing_booking
            if(count($updateCosting) > 0){
                (new QueryExtra)
                ->table('mr_po_bom_costing_booking')
                ->whereKey('id')
                ->bulkup($updateCosting);
            }

            // mr_po_operation_n_cost - update
            if(isset($input['op_id'])){
            	$updateOpCost = [];
            	for ($s=0; $s < sizeof($input['op_id']); $s++) {

					// MrPoOperationNCost::updateOrCreate(
					// [
					// 	"id"                      => $request->op_id[$s],
					// 	"mr_order_entry_order_id" => $request->order_id,
					// 	"po_id"                   => $request->po_id
					// ],
					// [
					// 	"mr_operation_opr_id" => $request->mr_operation_opr_id[$s],
					// 	"opr_type" 		      => $request->opr_type[$s],
					// 	"uom"                 => $request->spuom[$s],
					// 	"unit_price" 	      => $request->spunitprice[$s]
					// ]);
					$spItem = [
						"opr_type" 	 => $request->opr_type[$s],
						"uom"        => $request->spuom[$s],
						"unit_price" => $request->spunitprice[$s]
					];
					$updateOpCost[] =
                    [
                        'data' => $spItem,
                        'keyval' => $request->op_id[$s]
                    ];

					// $this->logFileWrite("Style Operation updated", $request->style_op_id[$s]);
				}

				// update mr_po_operation_n_cost
                if(count($updateOpCost) > 0){
                    (new QueryExtra)
                    ->table('mr_po_operation_n_cost')
                    ->whereKey('id')
                    ->bulkup($updateOpCost);
                }
            }

			// mr_po_bom_other_costing - insert or Update
			MrPoBomOtherCosting::updateOrCreate(
			[
				"po_id"                   => $request->po_id,
				"mr_order_entry_order_id" => $request->order_id
			],
			[
				"cm"           	  => $request->cm,
				"net_fob" 		  => $request->net_fob,
				"agent_fob"       => $request->agent_fob,
				"buyer_fob" 	  => $request->buyer_fob,
				"testing_cost" 	  => $request->testing_cost,
				"commercial_cost" => $request->commercial_cost,
				"buyer_comission_percent" => $request->buyer_comission_percent,
				"agent_comission_percent" => $request->agent_comission_percent
			]);

			// update purchase order
			PurchaseOrder::where('po_id', $input['po_id'])->update(['country_fob' => $request->agent_fob]);

            //log_file_write("Costing Successfully Save", $input['stl_id']);
            DB::commit();
	        $data['type'] = 'success';
	        $data['message'] = "Costing Successfully Save.";
	        return response()->json($data);
    	} catch (\Exception $e) {
    		DB::rollback();
    		$bug = $e->getMessage();
	        $data['message'] = $bug;
	        return response()->json($data);
    	}
    }
}
