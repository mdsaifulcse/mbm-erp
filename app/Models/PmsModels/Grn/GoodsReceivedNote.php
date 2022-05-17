<?php

namespace App\Models\PmsModels\Grn;

use App\Models\PmsModels\Purchase\PurchaseOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsReceivedNote extends Model
{
    use SoftDeletes;
	protected $table = 'goods_received_notes';
	protected $primaryKey = 'id';

    protected $guarded = [];
    const REFNO=100;
    protected $fillable = ['purchase_order_id','reference_no','total_price','discount','vat','gross_price','received_status','is_sent_to_accounts','is_supplier_rating','challan','challan_file','received_date','delivery_by','receive_by','note','created_by','updated_by'];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function relPurchaseOrder()
    {
        return $this->belongsTo(\App\Models\PmsModels\Purchase\PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    public function relGoodsReceivedItems()
    {
        return $this->hasMany(GoodsReceivedItem::class, 'goods_received_note_id', 'id');
    }

    public function relUsersList()
    {
        return $this->hasOne(\App\User::class, 'id', 'created_by');
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
