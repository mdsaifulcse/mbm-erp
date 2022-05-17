<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;

class HrDesignation extends Model
{
	protected $table = 'hr_designations';
	protected $primaryKey = 'id';
    protected $guarded = [];

//    protected $dates = [
//        'created_at', 'updated_at'
//    ];

    protected $fillable = [
        'hr_designation_emp_type', 'parent_id', 'hr_designation_name', 'hr_designation_name_bn', 'designation_short_name', 'grade_id', 'hr_designation_grade', 'hr_designation_position', 'hr_designation_status', 'created_by', 'updated_by', 'deleted_at'
    ];

}
