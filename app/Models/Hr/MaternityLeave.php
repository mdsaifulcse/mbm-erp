<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class MaternityLeave extends Model
{
    protected $table = "hr_maternity_leave";

    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at','leave_from','leave_to'
    ];


    public function medical()
    {
    	return $this->hasOne('App\Models\Hr\MaternityMedical','hr_maternity_leave_id','id');
    }
    

    public function payment()
    {
        return $this->hasOne('App\Models\Hr\MaternityPayment','hr_maternity_leave_id','id');
    }


    public function audit()
    {
        return $this->hasOne('App\Models\Audit\MaternityAudit','hr_maternity_leave_id','id');
    }


    public function employee()
    {
        return $this->belongsTo('App\Models\Employee','associate_id','associate_id');
    }


    
}
