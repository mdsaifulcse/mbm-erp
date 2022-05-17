<?php

namespace App\Models\PmsModels\Rfp;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PmsModels\Suppliers;
use Illuminate\Database\Eloquent\Model;

class RequestProposalDefineSupplier extends Model
{
    //use HasFactory;

    protected $table='request_proposal_define_suppliers';

    protected $fillable = ['request_proposal_id', 'supplier_id'];

    public function supplier(){
        return $this->belongsTo(Suppliers::class,'supplier_id','id');
    }


}
