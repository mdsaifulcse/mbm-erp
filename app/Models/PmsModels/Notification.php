<?php

namespace App\Models\PmsModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class Notification extends Model
{
	protected $table = 'notifications';
	protected $primaryKey = 'id';
    protected $fillable = [
        'user_id', 
        'requisition_item_id',
        'messages', 
        'type', 
        'read_at'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function relUser()
    {
        return $this->belongsTo(\App\User::class, 'user_id', 'id');
    }

    public function relRequisitionItem()
    {
        return $this->belongsTo(RequisitionItem::class, 'requisition_item_id', 'id');
    }
}
