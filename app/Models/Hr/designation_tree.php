<?php

namespace App\Models\Hr;

use App\Models\Employee;
use App\Models\Hr\designation_tree;
use Illuminate\Database\Eloquent\Model;

class designation_tree extends Model
{
	
    protected $table = 'hr_designation';
	  protected $primaryKey = 'hr_designation_id';
    protected $guarded = [];

    public function subcategory(){
        return $this->hasMany(designation_tree::class,'parent_id')
                      ->whereIn('hr_designation_emp_type', [1,2,3])
                      ->where('hr_designation_status',1);
    }





}