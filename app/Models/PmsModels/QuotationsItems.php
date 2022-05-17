<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;

class QuotationsItems extends Model
{
    protected $table='quotations_items';

    protected $fillable = [
     'quotation_id', 
     'product_id', 
     'unit_price', 
     'qty', 
     'sub_total_price', 
     'discount', 
     'vat',
     'total_price'
    ];

    public function relQuotation()
    {
        return $this->belongsTo(Quotations::class, 'quotation_id', 'id');
    }

    public function relProduct()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
