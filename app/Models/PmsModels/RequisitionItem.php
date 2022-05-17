<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;

class RequisitionItem extends Model
{
    
    protected $fillable = ['requisition_id', 'product_id', 'qty', 'comment','created_by','updated_by','delivery_qty'];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function notification()
    {
        return $this->hasOne(Notification::class, 'requisition_item_id', 'id');
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
