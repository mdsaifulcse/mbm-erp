<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;
use App\Models\PmsModels\Requisition;

class RequisitionTracking extends Model
{
	protected $table = 'requisition_tracking';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = ['requisition_id','status','note'];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id', 'id');
    }

    //Tracking log entry

    public static function storeRequisitionTracking($requisition_id, $status, $note="")
    {
        $summary = new self();
        $summary->requisition_id = $requisition_id;
        $summary->status = $status;
        $summary->note = $note;
        $summary->save();
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
