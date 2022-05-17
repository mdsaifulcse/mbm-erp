<?php

namespace App\Models\Hr;

use App\Models\Hr\Shift;
use App\Models\Hr\Unit;
use App\Models\Hr\hrLateCountCustomize;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class hrLateCountCustomize extends Model
{
    use Compoships, LogsActivity;

    protected $table = "hr_late_count_customizes";
    protected $fillable = ['hr_unit_id', 'hr_shift_name', 'date_from', 'date_to', 'time', 'comment', 'created_by', 'updated_by'];

    protected static $logAttributes = ['hr_unit_id', 'hr_shift_name', 'date_from', 'date_to', 'time', 'comment'];
    protected static $logName = 'latecountcustomize';
    
    public function unit()
    {
    	return $this->belongsTo(Unit::class,'hr_unit_id','hr_unit_id');
    }

    public function shift()
    {
        return $this->hasOne(Shift::class, ['hr_shift_unit_id', 'hr_shift_name'], ['hr_unit_id', 'hr_shift_name'])->latest();
    }

    public static function checkExistsAlreadyHaving($data)
    {

    	return hrLateCountCustomize::
    	where('hr_unit_id', $data['hr_unit_id'])
        ->where('hr_shift_name', $data['hr_shift_name'])
    	->where('date_from', '<=', $data['date_from'])
    	->where('date_to', '>=', $data['date_to'])
    	->first();

    }
}
