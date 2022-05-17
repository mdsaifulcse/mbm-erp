<?php

namespace App\Models\PmsModels\InventoryModels;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PmsModels\Category;
use App\Models\PmsModels\Product;
use App\Models\PmsModels\Warehouses;


class InventoryLogs extends Model
{
    protected $table = 'inventory_logs';
    protected $fillable = [
        'category_id',
        'product_id',
        'warehouse_id',
        'unit_price',
        'qty',
        'total_price',
        'status',
        'type',
    ];

      //relation between category and inventory summaries.
    public function relCategory()
    {
        return $this->belongsTo(\App\Models\PmsModels\Category::class, 'category_id', 'id');
    }

    //relation between product and inventory summaries.
    public function relProduct()
    {
        return $this->belongsTo(\App\Models\PmsModels\Product::class, 'product_id', 'id');
    }

    //relation between product and inventory summaries.
    public function relWarehouse()
    {
        return $this->belongsTo(\App\Models\PmsModels\Warehouses::class, 'warehouse_id', 'id');
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
