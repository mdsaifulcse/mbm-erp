<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;

class SampleRequisition extends Model
{
	protected $table = 'mr_sample_requisition';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
