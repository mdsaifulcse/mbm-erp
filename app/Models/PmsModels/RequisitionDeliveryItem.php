<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;

class RequisitionDeliveryItem extends Model
{
	protected $table = 'requisition_delivery_items';
	protected $primaryKey = 'id';
    protected $fillable = ['requisition_delivery_id','product_id','delivery_qty','status'];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function relRequisitionDelivery()
    {
        return $this->belongsTo(RequisitionDelivery::class, 'requisition_delivery_id', 'id');
    }

    //define static boot/register for created_by & updated_by
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
