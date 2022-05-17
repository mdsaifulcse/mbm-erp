<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class ShiftExtraBreak extends Model
{
	protected $table = 'hr_shift_extra_break';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
