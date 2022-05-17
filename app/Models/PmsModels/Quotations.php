<?php

namespace App\Models\PmsModels;

use App\Models\PmsModels\Purchase\PurchaseOrder;
use Illuminate\Database\Eloquent\Model;
use App\Models\PmsModels\Rfp\RequestProposal;
use Auth;

class Quotations extends Model
{
    const REFNO=100;
    
    protected $table='quotations';

    protected $fillable = [
        'supplier_id', 
        'request_proposal_id', 
        'reference_no', 
        'quotation_date', 
        'total_price', 
        'discount', 
        'vat', 
        'gross_price', 
        'status',
        'type',
        'is_approved',
        'remarks',
        'note',
        'quotation_file',
        'is_po_generate'
    ];

    public function relSuppliers()
    {
        return $this->belongsTo(Suppliers::class, 'supplier_id', 'id');
    }

    public function relRequestProposal()
    {
        return $this->belongsTo(RequestProposal::class, 'request_proposal_id', 'id');
    }

    public function relQuotationItems(){
        return $this->hasMany(QuotationsItems::class,'quotation_id','id');
    }

    public function relSelfQuotationSupplierByProposalId(){
        return $this->hasMany(Quotations::class,'request_proposal_id','request_proposal_id');
    }

    public function relUsersList()
    {
        return $this->hasOne(\App\User::class, 'id', 'created_by');
    }

    public function relPurchaseOrder()
    {
        return $this->hasOne(PurchaseOrder::class, 'quotation_id', 'id');
    }

    public static function boot(){
        parent::boot();
        static::creating(function($query){
            if(\Auth::check()){
                $query->created_by = @\Auth::user()->id;
            }
        });
        static::updating(function($query){
            if(\Auth::check()){
                $query->updated_by = @\Auth::user()->id;
            }
        });
    }
}
