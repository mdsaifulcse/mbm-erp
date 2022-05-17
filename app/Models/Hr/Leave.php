<?php

namespace App\Models\Hr;

use App\Models\Hr\Leave;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Leave extends Model
{
    use LogsActivity;

	protected $table = 'hr_leave';
    protected $guarded = [];

    protected static $logAttributes = ['leave_ass_id', 'leave_type', 'leave_from', 'leave_to', 'leave_applied_date', 'leave_status', 'leave_comment', 'leave_complete_status'];
    protected static $logName = 'leave';
    protected static $logOnlyDirty = true;

    protected $dates = [
        'created_at','leave_from','leave_to'
    ];

    public static function getDateStatusWiseEmployeeLeaveCheck($assId, $date, $status)
    {
    	return Leave::
        where('leave_ass_id', $assId)
        ->where('leave_from', '<=', $date)
        ->where('leave_to', '>=', $date)
        ->where('leave_status', $status)
        ->first();
    }
}
