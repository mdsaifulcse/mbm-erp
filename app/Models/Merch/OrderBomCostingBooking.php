<?php

namespace App\Models\Merch;

use App\Models\Merch\Article;
use App\Models\Merch\Composition;
use App\Models\Merch\Construction;
use App\Models\Merch\McatItem;
use App\Models\Merch\Supplier;
use Illuminate\Database\Eloquent\Model;

class OrderBomCostingBooking extends Model
{
	// public $with = ['cat_item', 'article', 'construction', 'composition', 'supplier'];
    protected $table= 'mr_order_bom_costing_booking';
    public $timestamps= false;

    public function cat_item()
    {
        return $this->belongsTo(McatItem::class, 'mr_cat_item_id', 'id');
    }
    public function article()
    {
        return $this->belongsTo(Article::class, 'mr_article_id', 'id');
    }
    public function construction()
    {
        return $this->belongsTo(Construction::class, 'mr_construction_id', 'id');
    }
    public function composition()
    {
        return $this->belongsTo(Composition::class, 'mr_composition_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'mr_supplier_sup_id', 'sup_id');
    }
}
