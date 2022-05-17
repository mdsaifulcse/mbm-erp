<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use Spatie\Activitylog\Traits\LogsActivity;

class AttCEIL extends Model
{
    use LogsActivity;
    
	protected $table = 'hr_attendance_ceil';
    protected $guarded = ['id'];
    
    protected static $logAttributes = ['in_time', 'out_time', 'late_status', 'ot_hour', 'hr_shift_code'];

    protected static $logName = 'attendance';

    protected $dates = [
        'created_at'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'as_id', 'as_id');
    }
}
