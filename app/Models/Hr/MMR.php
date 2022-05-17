<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class MMR extends Model
{
	protected $table = 'hr_mmr_settings';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
