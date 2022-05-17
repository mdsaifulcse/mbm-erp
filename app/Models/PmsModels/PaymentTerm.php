<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class PaymentTerm extends Model
{
    use SoftDeletes;
    const ACTIVE='Active';
    const INACTIVE='Inactive';
	protected $table = 'payment_terms';
	protected $primaryKey = 'id';
    protected $fillable = ['term','remarks','status','created_by','updated_by'];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    // TODO :: boot
    // boot() function used to insert logged user_id at 'created_by' & 'updated_by'
    public static function boot(){
        parent::boot();
        static::creating(function($query){
            if(Auth::check()){
                $query->created_by = @\Auth::user()->id;
            }
        });
        static::updating(function($query){
            if(Auth::check()){
                $query->updated_by = @\Auth::user()->id;
            }
        });
    }
}
