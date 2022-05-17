<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class EarnedLeave extends Model
{
	protected $table = 'hr_earned_leave';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    /**
     * The accessors to append to the model's array form
     *
     * @var array
     */
    protected $appends = ['total','eligible','remaining'];

    /**
     * Access total earned Leave
     *
     * @return string
     */
    public function getTotalAttribute()
    {
        return $this->carried + $this->earned;
    }

    /**
     * Access remaining earned Leave
     *
     * @return string
     */
    public function getRemainingAttribute()
    {
        return ($this->carried + $this->earned - $this->enjoyed);
    }

    /**
     * Access eligible earned Leave
     *
     * @return string
     */
    public function getEligibleAttribute()
    {
        return ($this->carried + $this->earned - $this->enjoyed)/2;
    }
}
