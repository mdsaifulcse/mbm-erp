<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
	protected $table = 'purchase_returns';
	protected $primaryKey = 'id';
    protected $guarded = [];
    protected $fillable = [
        'goods_received_item_id',
        'return_note',
        'return_qty',
        'status',
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function relGoodsReceivedItems()
    {
        return $this->belongsTo(App\Models\PmsModels\Grn\GoodsReceivedItem::class, 'goods_received_item_id', 'id');
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
