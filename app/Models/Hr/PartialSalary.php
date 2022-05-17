<?php

namespace App\Models\Hr;

use App\Models\Hr\PartialSalaryMaster;
use Illuminate\Database\Eloquent\Model;
use DB;

class PartialSalary extends Model
{
	protected $table = 'hr_partial_salary';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function master()
    {
        return $this->belongsTo(PartialSalaryMaster::class, 'partial_master_id', 'id');
    }

    public static function getEmployeeWisePartialAmount($value='')
    {
        return DB::table('hr_partial_salary as p')
            ->join('hr_partial_salary_master as pm', 'p.partial_master_id', 'pm.id')
            ->where('p.as_id', $value['as_id'])
            ->where('p.month', $value['month'])
            ->where('p.year', $value['year'])
            ->where('pm.audit_status', 'S')
            ->pluck('p.total_payable')
            ->first()??0;
    }
}
