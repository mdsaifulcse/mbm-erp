<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class ShiftCustomBreak extends Model
{
	protected $table = 'hr_shift_custom_break';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
