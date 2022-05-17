<?php
namespace App\Contracts;

interface BaseInterface{
    public function all();
   // public function paginate($count);
    public function find($id,$relation=[]);
   /* public function findBy($field,$value);
    public function store($data);
    public function update($data);
    public function delete($id);*/

}