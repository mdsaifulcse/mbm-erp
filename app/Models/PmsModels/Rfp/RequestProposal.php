<?php

namespace App\Models\PmsModels\Rfp;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PmsModels\Quotations;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestProposal extends Model
{
    //use HasFactory;
    use SoftDeletes;

    const ACTIVE='Active';
    const INACTIVE='Inactive';
    const PENDING='Pending';

    protected $table='request_proposals';

    protected $fillable = ['reference_no', 'request_date', 'remarks', 'status','type','created_by','updated_by'];

    public function requestProposalDetails(){
        return $this->hasMany(RequestProposalDetails::class,'request_proposal_id','id');
    }

    public function defineToSupplier(){
        return $this->hasMany(RequestProposalDefineSupplier::class,'request_proposal_id','id');
    }

    public function createdBy(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function relQuotations()
    {
        return $this->hasMany(Quotations::class, 'request_proposal_id', 'id');
    }



    // TODO :: boot
    // boot() function used to insert logged user_id at 'created_by' & 'updated_by'
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
