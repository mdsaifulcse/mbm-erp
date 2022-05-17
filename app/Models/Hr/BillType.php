<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillType extends Model
{
    use SoftDeletes;
    
	protected $table = 'hr_bill_type';
	protected $primaryKey = 'id';
    protected $fillable = ['name', 'bangla_name', 'status', 'created_by', 'updated_by'];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];
}
