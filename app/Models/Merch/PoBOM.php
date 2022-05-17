<?php

namespace App\Models\Merch;

use App\Models\Merch\PoBOM;
use Illuminate\Database\Eloquent\Model;
use DB;

class PoBOM extends Model
{
	// public $with = ['category', 'item'];
    protected $table= 'mr_po_bom_costing_booking';
    protected $guarded = [];

    public static function getOrderBomOrderIdWiseSelectItemIdName($orderId)
    {
    	return OrderBOM::where('order_id', $orderId)
    	->get();
    }

    public function category()
	{
	    return $this->belongsTo('App\Models\Merch\MainCategory', 'mr_material_category_mcat_id', 'mcat_id');
	}

	public function item()
	{
	    return $this->belongsTo('App\Models\Merch\McatItem', 'mr_cat_item_id', 'id');
	}

	public function supplier()
	{
	    return $this->belongsTo('App\Models\Merch\Supplier', 'mr_supplier_sup_id', 'sup_id');
	}

	public function article()
	{
	    return $this->belongsTo('App\Models\Merch\Article', 'mr_article_id', 'id');
	}

	public function composition()
	{
	    return $this->belongsTo('App\Models\Merch\Composition', 'mr_composition_id', 'id');
	}


	public function construction()
	{
	    return $this->belongsTo('App\Models\Merch\Construction', 'mr_construction_id', 'id');
	}

	  // public function order_bom_placement()
	  // {
	  // 	return $this->hasMany('App\Models\Merch\OrdBomPlacement', ['order_id', 'mr_cat_item_id'], ['order_id', 'item_id']);
	  // }

	public static function getPoIdWisePoBOM($poId)
    {
        return DB::table('mr_po_bom_costing_booking as p')
            ->join('mr_order_bom_costing_booking as o', 'o.id', '=', 'p.ord_bom_id')
            ->join('mr_stl_bom_n_costing as s', 'o.stl_bom_id', '=', 's.id')
			->select('p.id', 'p.mr_material_category_mcat_id AS mcat_id', 'p.mr_cat_item_id', 'p.item_description', 'p.clr_id', 'p.size', 'p.mr_supplier_sup_id', 'p.mr_article_id', 'p.uom', 'p.consumption', 'p.bom_term', 'p.precost_fob', 'p.precost_lc', 'p.precost_freight', 'p.precost_unit_price', 'p.extra_percent','p.gmt_qty', DB::raw('(p.consumption/100)*p.extra_percent AS qty'), DB::raw('((p.consumption/100)*p.extra_percent)+p.consumption AS total'),DB::raw('ceil((((p.consumption/100)*p.extra_percent)+p.consumption)*p.gmt_qty) AS requiredqty'), 'p.sl', 'p.depends_on', 'p.order_id', 'p.ord_bom_id', 'p.po_id','p.thread_brand', DB::raw('(((s.consumption / 100) * s.extra_percent) + s.consumption) + s.precost_unit_price as style_cost'), 's.precost_unit_price as style_unit_price', 'o.stl_bom_id', DB::raw('(((o.consumption / 100) * o.extra_percent) + o.consumption) + o.precost_unit_price as order_cost'))
			->where('p.po_id', $poId)
			->orderBy('p.sl', 'asc')
			->get();
    }

    public static function getPoWiseItem($poId, $selectedField)
    {
        $query = DB::table('mr_po_bom_costing_booking')
        ->where('po_id', $poId);
        if($selectedField != 'all'){
            $query->select($selectedField);
        }
        return $query->get();
    }

}
