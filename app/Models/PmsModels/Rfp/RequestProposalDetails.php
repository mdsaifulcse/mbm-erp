<?php

namespace App\Models\PmsModels\Rfp;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PmsModels\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestProposalDetails extends Model
{
    //use HasFactory,SoftDeletes;
    use SoftDeletes;

    const ACTIVE='Active';
    const INACTIVE='Inactive';
    const PENDING='Pending';


    protected $table='request_proposal_details';

    protected $fillable = ['request_proposal_id','requisition_id', 'product_id','qty','custom_qty', 'status','created_by','updated_by'];

    public function product(){
        return $this->belongsTo(Product::class,'product_id','id');
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
