<?php

namespace App\Models\Hr;

use App\Models\Employee;
use App\Models\Hr\HolidayRoaster;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class HolidayRoaster extends Model
{
    use LogsActivity;

    protected $table = "holiday_roaster";
    protected $guarded = [];

    public $timestamps = false;

    protected static $logAttributes = ['as_id', 'date', 'remarks', 'comment', 'status', 'reference_comment', 'reference_date', 'type'];

    protected static $logName = 'holiday_roster';
    protected static $logOnlyDirty = true;

    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->user()->id;
            $model->updated_by = NULL;
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->user()->id;
        });
    }
    public static function getHolidayYearMonthAsIdDateWise($year, $month, $asId, $date)
    {
      // dd($date);exit;
    	return HolidayRoaster::
    	select('remarks')
    	->where('year', $year)
    	->where('month', $month)
    	->where('as_id', $asId)
    	->where('date', $date)
    	->first();
    }

    public static function getHolidayYearMonthAsIdDateWiseRemark($year, $month, $asId, $date, $remark)
    {
    	return HolidayRoaster::where('year', $year)
    	->where('month', $month)
    	->where('as_id', $asId)
    	->where('date', $date)
    	->where('remarks', $remark)
    	->first();
    }

    public static function getHolidayYearMonthAsIdDateWiseRemarkMulti($year, $month, $asId, $date, $remarks)
    {
        return HolidayRoaster::where('year', $year)
        ->where('month', $month)
        ->where('as_id', $asId)
        ->where('date', $date)
        ->where(function ($query) use ($remarks) {
            $query->whereIn('remarks', $remarks);
        })
        ->first();
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'as_id', 'associate_id');
    }
}
