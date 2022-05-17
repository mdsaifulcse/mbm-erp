<?php

namespace App\Models\Hr;

use App\Models\Employee;
use App\Models\Hr\Shift;
use App\Models\Hr\ShiftBill;
use App\Models\Hr\ShiftCustomBreak;
use App\Models\Hr\ShiftExtraBreak;
use App\Models\Hr\ShiftHistory;
use App\Models\Hr\Unit;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Carbon\Carbon;

class Shift extends Model
{ 
    protected $table= 'hr_shift';

    protected $primaryKey = 'hr_shift_id';

    public $timestamps= false;


    protected $appends = ['hr_shift_out_time','default_shift_time'];

    protected static $logAttributes = ['hr_shift_name', 'hr_shift_code', 'hr_shift_start_time', 'hr_shift_end_time', 'hr_shift_break_time', 'hr_shift_status', 'hr_shift_night_flag', 'hr_shift_default', 'bill_eligible', 'ot_status', 'ot_shift'];

    protected static $logName = 'shift';
    protected static $logOnlyDirty = true;

    use Compoships, LogsActivity;

    public function getHrShiftOutTimeAttribute()
    {
        return Carbon::parse($this->hr_shift_end_time)->addMinutes($this->hr_shift_break_time)->format('H:i:s');
    }


    public function getDefaultShiftTimeAttribute()
    {
        return (object)[
                "hr_shift_name" => $this->hr_shift_name,
                "hr_shift_code" => $this->hr_shift_code,
                "hr_shift_start_time" => $this->hr_shift_start_time,
                "hr_shift_end_time" => $this->hr_shift_end_time,
                "hr_shift_break_time" => $this->hr_shift_break_time,
                "ot_status" => $this->ot_status,
                "ot_shift" => $this->ot_shift,
                "hr_break_start_time" => $this->hr_default_break_start,
                "hr_shift_out_time" => $this->hr_shift_out_time,
                "hr_shift_night_flag" => $this->hr_shift_night_flag,
                "hr_shift_unit_id" => $this->hr_shift_unit_id,
                "start_date" => null,
                "end_date" => null,
                "has_default_value" => false
            ];
    } 

    public static function getShiftIdWise($id)
    {
    	return Shift::where('hr_shift_id', $id)->first();
    }

    public static function getShiftNameGetId($id)
    {
        return Shift::select('hr_shift_name')->where('hr_shift_id', $id)->pluck('hr_shift_name')->first();
    }

    public static function checkExistsTimeWiseShift($data)
    {
    	return Shift::
    	where('hr_shift_id', $data['hr_shift_id'])
    	->where('hr_shift_start_time', $data['hr_shift_start_time'])
    	->where('hr_shift_end_time', $data['hr_shift_end_time'])
    	->where('hr_shift_break_time', $data['hr_shift_break_time'])
    	->first();
    }

    public static function getShiftsByUnitIdWiseUqiue($unit_id){
        return Shift::
        where('hr_shift_unit_id', $unit_id)
        ->select('hr_shift_name')
        ->distinct('hr_shift_name')
        ->get()
        ->toArray();
    }

    

    public static function checkExistsShiftCode($unit, $code)
    {
        return Shift::
        where('hr_shift_unit_id', $unit)
        ->where('hr_shift_code', $code)
        ->first();
    }

    public static function getCheckUniqueUnitIdShiftName($unit, $shiftName)
    {
        return Shift::
        where('hr_shift_unit_id', $unit)
        ->where('hr_shift_name', $shiftName)
        ->latest()
        ->first();
    }

    public static function getCheckUniqueUnitIdTime($unit, $data)
    {
        return Shift::
        where('hr_shift_unit_id', $unit)
        ->where('hr_shift_start_time', $data['hr_shift_start_time'])
        ->where('hr_shift_end_time', $data['hr_shift_end_time'])
        ->where('hr_shift_break_time', $data['hr_shift_break_time'])
        ->first();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'hr_shift_unit_id', 'hr_unit_id');
    }

    // get associated shift bills with history

    public function bills()
    {
        return $this->hasMany(ShiftBill::class,'hr_shift_id','hr_shift_id');
    }

    // get shift extra break

    public function breaks()
    {
        return $this->hasMany(ShiftExtraBreak::class,'hr_shift_id','hr_shift_id');
    }

    // get shift history

    public function histories()
    {
        return $this->hasMany(ShiftHistory::class,'hr_shift_id','hr_shift_id')->orderBy('id','desc');
    }

    // get shift break custom rules

    public function customBreaks()
    {
        return $this->hasMany(ShiftCustomBreak::class,'hr_shift_id','hr_shift_id');
    }

}
