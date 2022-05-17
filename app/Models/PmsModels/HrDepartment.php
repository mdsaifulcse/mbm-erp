<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;

class HrDepartment extends Model
{
	protected $table = 'hr_department';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
//        'name', 'hod_name', 'hod_email', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'
    ];

    protected $fillable = [
        'hr_department_id', 'hr_department_area_id', 'hr_department_name', 'hr_department_name_bn', 'hr_department_code', 'hr_department_min_range', 'hr_department_max_range', 'hr_department_status', 'sequence', 'created_by', 'updated_by', 'deleted_at'
    ];
}
