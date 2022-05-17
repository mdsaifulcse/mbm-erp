<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;


class RequestProposalTracking extends Model
{
	protected $table = 'request_proposal_tracking';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $fillable = ['request_proposal_id','status','note'];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function relRequestProposal()
    {
        return $this->belongsTo(RequestProposal::class, 'request_proposal_id', 'id');
    }

    //Tracking log entry

    public static function StoreRequestProposalTracking($request_proposal_id, $status, $note="")
    {
        $summary = new self();
        $summary->request_proposal_id = $request_proposal_id;
        $summary->status = $status;
        $summary->note = $note;
        $summary->save();
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
