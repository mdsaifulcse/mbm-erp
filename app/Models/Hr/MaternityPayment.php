<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class MaternityPayment extends Model
{
	protected $table = 'hr_maternity_payment';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    protected $appends = ['total_payment'];


    public function getTotalPaymentAttribute()
    {
        return $this->first_payment + $this->second_payment;
    }
}
