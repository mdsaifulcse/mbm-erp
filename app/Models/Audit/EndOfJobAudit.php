<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;

class EndOfJobAudit extends Model
{
	protected $table = 'hr_end_of_job_audit';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
