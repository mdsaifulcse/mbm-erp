<?php

namespace App\Models\PmsModels;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //use HasFactory;

    protected $fillable = ['code', 'name', 'parent_id','requisition_type_id'];

    public function subCategory()
    {
        return $this->hasMany(\App\Models\PmsModels\Category::class, 'parent_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(\App\Models\PmsModels\Category::class, 'parent_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
    public function requisitionType()
    {
        return $this->belongsTo(RequisitionType::class, 'requisition_type_id', 'id');
    }

    public function department()
    {
        return $this->belongsToMany(\App\Models\Hr\Department::class, 'categories_department','category_id', 'hr_department_id');
    }

    public function departmentsList()
    {
        return $this->hasMany(CategoryDepartment::class, 'category_id', 'id');
    }

    public function relUser()
    {
        return $this->hasOne(\App\User::class, 'id', 'created_by');
    }
}
