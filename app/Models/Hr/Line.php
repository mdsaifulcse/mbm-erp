<?php

namespace App\Models\Hr;

use App\Models\Hr\Line;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Line extends Model
{
	use SoftDeletes, LogsActivity;

	protected $table = 'hr_line';
	protected $primaryKey = 'hr_line_id';
    protected $fillable = ['hr_line_unit_id', 'hr_line_floor_id', 'hr_line_name', 'hr_line_name_bn', 'hr_line_status', 'created_by', 'updated_by'];

    protected static $logAttributes = ['hr_line_unit_id', 'hr_line_floor_id', 'hr_line_name', 'hr_line_name_bn', 'hr_line_status'];
    protected static $logName = 'line';

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public static function getSelectedLineIdName($floor_id){
    	return Line::select(['hr_line_id','hr_line_name'])->where('hr_line_floor_id',$floor_id)->get();
    }
}
