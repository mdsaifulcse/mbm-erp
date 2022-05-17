<?php

namespace App\Models\PmsModels;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouses extends Model
{
    //use HasFactory;

    protected $fillable = ['code', 'name', 'phone', 'email', 'location', 'address'];
}
