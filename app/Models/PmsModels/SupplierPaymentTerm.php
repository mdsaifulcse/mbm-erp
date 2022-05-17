<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;

class SupplierPaymentTerm extends Model
{
	protected $table = 'supplier_payment_terms';
	protected $primaryKey = 'id';
    protected $guarded = [];
    protected $fillable=[
        'supplier_id',
        'payment_term_id', 
        'payment_percent', 
        'day_duration',
        'type',
        'remarks',
    ];

    CONST ADVANCE ='Advance';
    CONST DUE ='Due';

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function relPaymentTerm()
    {
        return $this->belongsTo(PaymentTerm::class,'payment_term_id', 'id');
    }

    public function relSupplier()
    {
        return $this->belongsTo(Suppliers::class,'supplier_id ', 'id');
    }
}
