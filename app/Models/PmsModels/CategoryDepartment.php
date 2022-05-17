<?php

namespace App\Models\PmsModels;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryDepartment extends Model
{
    //use HasFactory;
    protected $table='categories_department';
    protected $fillable = ['category_id', 'hr_department_id'];

    public function category()
    {
        return $this->belongsTo(\App\Models\PmsModels\Category::class, 'category_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Hr\Department::class, 'hr_department_id', 'hr_department_id');
    }
}
