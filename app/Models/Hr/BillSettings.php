<?php

namespace App\Models\Hr;

use App\Models\Hr\BillSpecialSettings;
use App\Models\Hr\BillType;
use DB;
use Illuminate\Database\Eloquent\Model;

class BillSettings extends Model
{
	protected $table = 'hr_bill_settings';
	protected $primaryKey = 'id';
    protected $fillable = ['unit_id', 'code', 'bill_type_id', 'amount', 'start_date', 'end_date', 'pay_type', 'duration', 'as_ot'];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function special()
    {
    	return $this->hasMany(BillSpecialSettings::class, 'bill_setup_id', 'id');
    }

    public function bill_type()
    {
        return $this->belongsTo(BillType::class, 'bill_type_id', 'id');
    }

     public function available_special() {
        return $this->special()->whereNull('end_date');
    }

    public static function checkExistsCode($code)
    {
        return DB::table('hr_bill_settings')
        ->where('code', $code)
        ->pluck('code')
        ->first();
    }

    public static function checkUnitTypeWiseExistsCode($value)
    {
        return DB::table('hr_bill_settings')
        ->where('unit_id', $value['unit_id'])
        ->where('bill_type_id', $value['bill_type_id'])
        ->orderBy('id', 'desc')
        ->pluck('code')
        ->first();
    }

    public static function updatePreviousBillUnitWiseStatus($value)
    {
        $endDate = date('Y-m-d', strtotime($value['start_date']));
        return DB::table('hr_bill_settings')
        ->where('unit_id', $value['unit_id'])
        ->where('bill_type_id', $value['bill_type_id'])
        ->whereNull('end_date')
        ->update([
            'end_date' => $endDate,
            'status' => 0,
            'updated_by' => auth()->user()->id
        ]);
    }
}
