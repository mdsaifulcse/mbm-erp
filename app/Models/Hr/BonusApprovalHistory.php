<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class BonusApprovalHistory extends Model
{
	protected $table = 'hr_bonus_app_history';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
