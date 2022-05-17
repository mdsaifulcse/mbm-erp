<?php

namespace App\Models\Commercial;

use Illuminate\Database\Eloquent\Model;

class CmImpDataEntryAsset extends Model
{
    protected $table= "cm_imp_data_entry_asset";
    public $timestamps= false;

    public function unit()
    {
    	return $this->belongsTo('App\Models\Hr\Unit', 'hr_unit', 'hr_unit_id');
    }

}

