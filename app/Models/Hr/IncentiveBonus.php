<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class IncentiveBonus extends Model
{
	protected $table = 'hr_incentive_bonus';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
