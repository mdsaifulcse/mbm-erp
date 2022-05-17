<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ShiftHistory extends Model
{
	protected $table = 'hr_shift_history';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $appends = ['hr_shift_out_time','has_default_value', 'hr_shift_night_flag'];

    protected $dates = [
        'created_at', 'updated_at'
    ];


    public function getHrShiftOutTimeAttribute()
    {
        return Carbon::parse($this->hr_shift_end_time)->addMinutes($this->hr_shift_break_time)->format('H:i:s');
    }

    public function getHasDefaultValueAttribute()
    {
        return true;
    }

    public function getHrShiftNightFlagAttribute()
    {
        return ($this->hr_shift_start_time > $this->hr_shift_end_time)?1:0;
    }
}
