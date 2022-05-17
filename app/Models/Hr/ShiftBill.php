<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class ShiftBill extends Model
{
	protected $table = 'hr_shift_bills';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
