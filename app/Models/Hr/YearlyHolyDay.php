<?php

namespace App\Models\Hr;

use App\Models\Hr\YearlyHolyDay;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class YearlyHolyDay extends Model
{
    use LogsActivity;

    protected $table= "hr_yearly_holiday_planner";
    public $timestamps= false;
    protected $primaryKey = 'hr_yhp_id';
    protected $guarded = [];

    protected static $logAttributes = ['hr_yhp_unit', 'hr_yhp_dates_of_holidays', 'hr_yhp_comments', 'hr_yhp_status', 'hr_yhp_open_status', 'reference_comment', 'reference_date', 'flag', 'holiday_type'];

    protected static $logName = 'holiday_planner';
    protected static $logOnlyDirty = true;

    public static function getCheckUnitDayWiseHoliday($unit, $day, $status = null)
    {
    	$queue = YearlyHolyDay::
        where('hr_yhp_unit', $unit)
        ->where('hr_yhp_dates_of_holidays', $day);
        if($status == 'non-ot'){
            $queue->where('hr_yhp_open_status', '!=', 2);
        }
        return $queue->first();
    }

    public static function getCheckUnitDayWiseHolidayStatus($unit, $day, $status)
    {
        return YearlyHolyDay::
        where('hr_yhp_unit', $unit)
        ->where('hr_yhp_dates_of_holidays', $day)
        ->where('hr_yhp_open_status', $status)
        ->first();
    }

    public static function getCheckUnitDayWiseHolidayStatusMulti($unit, $day, $status)
    {
        return YearlyHolyDay::
        where('hr_yhp_unit', $unit)
        ->where('hr_yhp_dates_of_holidays', $day)
        ->where(function ($query) use ($status) {
            $query->whereIn('hr_yhp_open_status', $status);
        })
        ->first();
    }
}
