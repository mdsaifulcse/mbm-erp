<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class EducationDegree extends Model
{
    use LogsActivity;

    protected $table= "hr_education_degree_title";
    protected $guarded= ["_token"];
    
    protected static $logAttributes = ['education_level_id', 'education_degree_title'];
    protected static $logName = 'educationdegree';
}
