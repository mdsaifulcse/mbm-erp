<?php

namespace App\Models\Hr;

use App\Models\Employee;
use App\Models\Hr\Subsection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Subsection extends Model
{
	use SoftDeletes, LogsActivity;

	protected $table = 'hr_subsection';
	protected $primaryKey = 'hr_subsec_id';
    protected $fillable = ['hr_subsec_area_id', 'hr_subsec_department_id', 'hr_subsec_section_id', 'hr_subsec_name', 'hr_subsec_name_bn', 'hr_subsec_status', 'hr_subsec_code', 'created_by', 'updated_by'];

    protected static $logAttributes = ['hr_subsec_area_id', 'hr_subsec_department_id', 'hr_subsec_section_id', 'hr_subsec_name', 'hr_subsec_name_bn', 'hr_subsec_status', 'hr_subsec_code'];
    protected static $logName = 'subsection';

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public static function getSubSectionList()
    {
        return Subsection::pluck('hr_subsec_name', 'hr_subsec_id');
    }
    public function getSubSectionWiseEmp($unitId, $areaId, $department_id, $floor_id, $section_id, $subsection_id)
    {
        $where = [
            'as_unit_id' => $unitId,
            'as_area_id' => $areaId,
            'as_department_id' => $department_id,
            'as_floor_id' => $floor_id,
            'as_section_id' => $section_id,
            'as_subsection_id' => $subsection_id,
            'as_status' => 1
        ];
        return Employee::where($where)->get();
    }

    public static function getSubSectionSectionIdWise($id)
    {
    	return Subsection::where('hr_subsec_section_id', $id)->where('hr_subsec_status', 1)->get();
    }
}
