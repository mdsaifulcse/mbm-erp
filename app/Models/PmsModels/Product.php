<?php

namespace App\Models\PmsModels;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //use HasFactory;

    protected $fillable = ['name', 'category_id', 'brand_id', 'tax', 'unit_price','product_unit_id','sku'];

    public function suppliers()
    {
        return $this->belongsToMany(Suppliers::class, 'products_supplier','product_id', 'supplier_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function productUnit()
    {
        return $this->belongsTo(productUnit::class, 'product_unit_id', 'id');
    }

    public function requisitionItem()
    {
        return $this->hasMany(RequisitionItem::class, 'product_id', 'id');
    }

    public function relInventorySummary()
    {
        return $this->hasOne(\App\Models\PmsModels\InventoryModels\InventorySummary::class, 'product_id', 'id');
    }

    public function relInventoryDetails()
    {
        return $this->hasMany(\App\Models\PmsModels\InventoryModels\InventoryDetails::class, 'product_id', 'id');
    }
}
