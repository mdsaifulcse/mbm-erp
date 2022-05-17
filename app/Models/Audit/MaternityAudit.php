<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;

class MaternityAudit extends Model
{
	protected $table = 'hr_maternity_audit';
	protected $primaryKey = 'id';
    protected $guarded = ['id'];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
