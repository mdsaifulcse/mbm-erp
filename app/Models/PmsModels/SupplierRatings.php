<?php

namespace App\Models\PmsModels;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierRatings extends Model
{
    protected $table='supplier_rattings';
    protected $fillable = [
        'supplier_id',
        'communication',
        'on_time_delivery',
        'quality',
        'price',
        'working_year',
        'incident',
        'company_established',
        'total_score',
        'status',
        'remarks'
    ];

    public function relSuppliers()
    {
        return $this->belongsTo(Suppliers::class, 'supplier_id', 'id');
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
