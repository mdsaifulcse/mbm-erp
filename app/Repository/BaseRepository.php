<?php

namespace App\Repository;


abstract class BaseRepository 
{

    protected $modelName;


    public function all()
    {
        // TODO: Implement getAll() method.
        $instance = $this->getNewInstance();
        return $instance->all();
    }

    public function find($id,$relation=[])
    {
        // TODO: Implement getById() method.
        $instance = $this->getNewInstance();
        return $instance->with($relation)->find($id);
    }

    protected function getNewInstance(){
        $model=$this->modelName;
        return new $model;
    }

    public function getAuthUnit($unit = null)
    {
        return empty($unit)?auth()->user()->unit_permissions():$unit;
    }


    public function getAuthLocation($location = null)
    {
        return empty($location)?auth()->user()->location_permissions():$location;
    }
}