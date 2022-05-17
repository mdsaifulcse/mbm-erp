<?php

namespace App\Models\PmsModels;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ApprovalRangeSetup extends Model
{
	protected $table = 'approval_range_setups';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    protected $fillable = [
        'min_amount',
        'max_amount'
    ];

    public function relUsers()
    {
        return $this->belongsToMany(User::class,'user_approval_range','approval_range_id','user_id');
    }

    //define static boot/register for created_by & updated_by
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
