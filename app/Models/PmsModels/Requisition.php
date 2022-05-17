<?php

namespace App\Models\PmsModels;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class Requisition extends Model
{
    //use HasFactory;
    const REFNO=100;

    protected $fillable = ['author_id', 'requisition', 'status', 'approved_id','remarks','admin_remark','requisition_date','reference_no','is_send_to_rfp','created_by','updated_by','delivery_status','delivery_note'];


    public function items()
    {
        return $this->hasMany(RequisitionItem::class, 'requisition_id', 'id');
    }

    public function relUsersList()
    {
        return $this->belongsTo(\App\User::class, 'author_id', 'id');
    }
    public function requisitionItems()
    {
        return $this->hasMany(RequisitionItem::class, 'requisition_id', 'id');
    }

    public function requisitionTracking()
    {
        return $this->hasMany(RequisitionTracking::class, 'requisition_id', 'id');
    }

    public function relRequisitionDelivery(){
        return $this->hasMany(RequisitionDelivery::class,'requisition_id','id');
    }

     // TODO :: boot
    // boot() function used to insert logged user_id at 'created_by' & 'updated_by'
    public static function boot(){
        parent::boot();
        static::creating(function($query){
            if(Auth::check()){
                $query->created_by = @\Auth::user()->id;
            }
        });
        static::updating(function($query){
            if(Auth::check()){
                $query->updated_by = @\Auth::user()->id;
            }
        });
    }
}
