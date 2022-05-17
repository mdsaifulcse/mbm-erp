<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Promotion extends Model
{
    use LogsActivity;

    protected $table = 'hr_promotion';
    protected $guarded = ['previous_designation'];
    public $timestamps = false;

    protected static $logAttributes = ['associate_id', 'previous_designation_id', 'current_designation_id', 'eligible_date', 'effective_date'];

    protected static $logName = 'promotion';
    protected static $logOnlyDirty = true;
}
