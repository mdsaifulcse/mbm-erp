<?php

namespace App\Models\PmsModels;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionType extends Model
{
    //use HasFactory;

    protected $fillable = ['name'];

    public function requisitionItem()
    {
        return $this->hasMany(RequisitionItem::class, 'type_id', 'id');
    }
}
