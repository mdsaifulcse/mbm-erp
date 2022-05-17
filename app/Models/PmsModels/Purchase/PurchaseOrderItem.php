<?php

namespace App\Models\PmsModels\Purchase;

use App\Models\PmsModels\Grn\GoodsReceivedItem;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $table='purchase_order_items';

    protected $fillable = [
        'po_id',
        'product_id',
        'unit_price',
        'qty',
        'sub_total_price',
        'discount',
        'vat',
        'total_price'
    ];

    public function relPurchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'id');
    }

    public function relProduct()
    {
        return $this->belongsTo(\App\Models\PmsModels\Product::class, 'product_id', 'id');
    }

    public function relReceiveProduct(){
        return $this->hasMany(GoodsReceivedItem::class,'product_id','product_id');
    }


}
