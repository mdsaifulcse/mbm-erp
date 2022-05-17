<?php

namespace App\Models\PmsModels\Purchase;

use App\Models\PmsModels\Grn\GoodsReceivedNote;
use App\Models\PmsModels\Quotations;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    const REFNO=100;

    protected $table='purchase_orders';

    protected $fillable = [
        'quotation_id',
        'reference_no',
        'po_date',
        'total_price',
        'discount',
        'vat',
        'gross_price',
        'remarks',
        'is_send'
    ];

    public function relQuotation()
    {
        return $this->belongsTo(Quotations::class, 'quotation_id', 'id');
    }

    public function relPurchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'po_id', 'id');
    }

    public function relUsersList()
    {
        return $this->hasOne(\App\User::class, 'id', 'created_by');
    }

    public function relGoodReceiveNote()
    {
        return $this->hasMany(GoodsReceivedNote::class,'purchase_order_id', 'id');
    }


    public static function boot(){
        parent::boot();
        static::creating(function($query){
            if(\Auth::check()){
                $query->created_by = @\Auth::user()->id;
            }
        });
        static::updating(function($query){
            if(\Auth::check()){
                $query->updated_by = @\Auth::user()->id;
            }
        });
    }
}
