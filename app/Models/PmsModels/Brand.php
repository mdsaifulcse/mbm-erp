<?php

namespace App\Models\PmsModels;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    //use HasFactory;

    protected $fillable = ['code', 'name', 'file_name', 'image'];

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id', 'id');
    }
}
