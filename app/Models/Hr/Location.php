<?php

namespace App\Models\Hr;

use App\Models\Hr\Location;
use Illuminate\Database\Eloquent\Model;
use DB;
use Spatie\Activitylog\Traits\LogsActivity;

class Location extends Model
{
    use LogsActivity;

	protected $table = 'hr_location';
	protected $primaryKey = 'hr_location_id';
    protected $fillable = ['hr_location_name', 'hr_location_short_name', 'hr_location_name_bn', 'hr_location_address', 'hr_location_address_bn', 'hr_location_code', 'hr_location_status', 'hr_location_unit_id', 'created_by', 'updated_by'];

    protected static $logAttributes = ['hr_location_name', 'hr_location_short_name', 'hr_location_name_bn', 'hr_location_address', 'hr_location_address_bn', 'hr_location_code', 'hr_location_status', 'hr_location_unit_id'];
    protected static $logName = 'location';

    protected $dates = [
        'created_at', 'updated_at'
    ];
    
    public static function getLocationDistinct()
    {
    	return Location::groupBy('hr_location_name')->get();
    }

 	public static function getLocationNameBangla($id)
 	{
 		$locationName = '';
 		$location = Location::where('hr_location_id',$id)->first();
 		if($location) {
 			if($location->hr_location_name_bn != null) {
 				$locationName = $location->hr_location_name_bn;
 			} else {
 				$locationName = $location->hr_location_name;
 			}
 		}
 		return $locationName;

 	}

    public static function getUnitWiseLocation($unitId)
    {
        return DB::table('hr_location')
        ->where('hr_location_unit_id', $unitId)
        ->first();
    }
}
