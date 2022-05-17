<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class Absent extends Model
{
    protected $table= "hr_absent";
    protected $fillable= ['associate_id', 'date', 'hr_unit', 'absent_type', 'comment'];

    protected $appends = ['xx','month'];

    public function getXxAttribute()
    {
        return 'hi';
    }

    public function getMonthAttribute()
    {
        return \Carbob\Carbon::parse($this->date)->format('M -Y');
    }

    public static function checkDateRangeEmployeeAbsent($start, $end, $asId)
    {
    	return Absent::
    	where('associate_id', $asId)
    	->whereDate('date','>=', $start)
    	->whereDate('date','<=', $end)
    	->get();
    }

    public static function getAbsentCheckExists($associate_id, $date)
    {
        return Absent::
        where('associate_id', $associate_id)
        ->where('date', $date)
        ->first();
    }
}
