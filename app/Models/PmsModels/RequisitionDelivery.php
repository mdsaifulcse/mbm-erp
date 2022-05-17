<?php

namespace App\Models\PmsModels;

use App\User;
use Illuminate\Database\Eloquent\Model;

class RequisitionDelivery extends Model
{
	protected $table = 'requisition_deliveries';
	protected $primaryKey = 'id';
    protected $fillable = ['requisition_id','reference_no','delivery_status','delivery_date','delivery_by','created_by','updated_by'];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function relRequisition(){
        return $this->belongsTo(Requisition::class,'requisition_id','id');
    }

    public function relDeliveryBy(){
        return $this->belongsTo(User::class,'delivery_by','id');
    }

    public function relDeliveryItems(){
        return $this->hasMany(RequisitionDeliveryItem::class,'requisition_delivery_id','id');
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
