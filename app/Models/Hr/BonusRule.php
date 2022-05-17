<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use DB;

class BonusRule extends Model
{
    protected $table= "hr_bonus_rule";

    protected $guarded = ['id'];

    public static function getBonusList()
    {
    	return DB::table('hr_bonus_rule')
        ->select('hr_bonus_rule.*', DB::raw("CONCAT(bonus_type_id,'-',bonus_year) AS combine_year"))
        ->where('status', 1)
    	->orderBy('id', 'desc')
    	->get();
    }

    public static function getBonusListByYear($year, $unitId='')
    {
        if($unitId == ''){
            $unitId = auth()->user()->unit_permissions();
        }
        return DB::table('hr_bonus_rule')
        ->select('hr_bonus_rule.*', DB::raw("CONCAT(bonus_type_id,'-',bonus_year) AS combine_year"))
        ->where('status', 1)
        ->where('bonus_year', $year)
        ->whereIn('unit_id', $unitId)
        ->orderBy('id', 'desc')
        ->get();
    }

    public static function getNonApprovalBonusList()
    {
        return DB::table('hr_bonus_rule')
        ->whereNull('approved_at')
        ->where('status', 1)
        ->orderBy('id', 'desc')
        ->get();
    }

    public static function getApprovalGroupBonusList()
    {
    	return DB::table('hr_bonus_rule AS r')
		->select('r.id', DB::raw('CONCAT_WS(" - ", bonus_type_name, bonus_year) AS text'))
		->join('hr_bonus_type AS b', 'r.bonus_type_id', 'b.id')
		->whereIn('r.unit_id', auth()->user()->unit_permissions())
    	->whereNotNull('r.approved_at')
    	->orderBy('r.id', 'desc')
        ->where('r.status', 1)
    	->groupBy('text')
    	->get();
    }

    public static function getUnitGroupBonusList()
    {
    	return DB::table('hr_bonus_rule AS r')
		->select('r.id', DB::raw('CONCAT_WS(" - ", bonus_type_name, bonus_year) AS text'))
		->join('hr_bonus_type AS b', 'r.bonus_type_id', 'b.id')
		->whereIn('r.unit_id', auth()->user()->unit_permissions())
    	->orderBy('r.id', 'desc')
        ->where('r.status', 1)
    	->groupBy('text')
    	->get();
    }

    public static function getBonusListById($id)
    {
        return DB::table('hr_bonus_rule as r')
        ->select('b.bonus_type_name','b.bangla_name','b.eligible_month', 'r.*')
        ->join('hr_bonus_type AS b', 'r.bonus_type_id', 'b.id')
        ->where('r.id', $id)
        ->first();
    }
}