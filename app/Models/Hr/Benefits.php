<?php

namespace App\Models\Hr;

use App\Models\Hr\Benefits;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use DB;

class Benefits extends Model
{
    use LogsActivity;

    protected $table= "hr_benefits";

    protected $primaryKey = 'ben_id';
    public $timestamps = false;
    protected $guarded = [];

    protected static $logAttributes = ['ben_joining_salary', 'ben_cash_amount', 'ben_bank_amount', 'ben_tds_amount', 'ben_current_salary', 'bank_name', 'bank_no'];

    protected static $logName = 'benefit';
    protected static $logOnlyDirty = true;

    public static function getSalaryRangeMin()
    {
    	return Benefits::min('ben_current_salary');
    }

    public static function getSalaryRangeMax()
    {
    	return Benefits::max('ben_current_salary');
    }

    public static function getEmployeeAssIdwise($assId)
    {
        return Benefits::
        where('ben_as_id', $assId)
        ->first();
    }

    public static function getBenefitDataByFields($selected=null, $asIds=null)
    {
        $query = DB::table('hr_benefits')->select('ben_as_id');
        if($selected != null){
            $query->addSelect($selected);
        }
        if($asIds != null){
            $query->whereIn('ben_as_id', $asIds);
        }
        return $query->get()->keyBy('ben_as_id');
    }


}
