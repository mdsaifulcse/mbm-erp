<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class PartialSalaryMaster extends Model
{
	protected $table = 'hr_partial_salary_master';
	protected $primaryKey = 'id';
    protected $guarded = [];

    // protected $dates = [
    //     'created_at', 'updated_at'
    // ];
}
