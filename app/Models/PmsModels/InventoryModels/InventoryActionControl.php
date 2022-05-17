<?php

namespace App\Models\PmsModels\InventoryModels;

use App\Models\PmsModels\Product;
use App\Models\PmsModels\Warehouses;
use Illuminate\Database\Eloquent\Model;

class InventoryActionControl extends Model
{
    protected $product;
    protected $warehouses;
    protected $totalPrice;
    protected $qty;
    protected $status;
    protected $reference;

    public function __construct($product, $warehouses, $totalPrice, $qty, $status, $reference)
    {
        $this->product = $product;
        $this->warehouses = $warehouses;
        $this->totalPrice = $totalPrice;
        $this->qty = $qty;
        $this->status = $status;
        $this->reference = $reference;
        $this->insertInventorySummaries();
        $this->storeInventoryDetails();
        $this->storeInventoryLog();
    }


    public function insertInventorySummaries()
    {   

        $model =InventorySummary::where(['category_id'=>$this->product->category->id,'product_id'=>$this->product->id])->first();

        if (count((array)$model)>0) {
            $summary=$model;
            $summary->qty = $this->qty+$model->qty;
            $summary->total_price = $this->totalPrice+$model->total_price;
        }else{
            $summary = new InventorySummary();
            $summary->qty = $this->qty;
            $summary->unit_price = ($this->totalPrice/$this->qty);
            $summary->total_price = $this->totalPrice;
        }   

        $summary->category_id = $this->product->category->id;
        $summary->product_id = $this->product->id;
        $summary->status = $this->status;
        $summary->save();
    }

    public function storeInventoryDetails()
    {
        $model =InventoryDetails::where(['category_id'=>$this->product->category->id,'product_id'=>$this->product->id,'warehouse_id'=>$this->warehouses->id])->first();

        if (count((array)$model)>0) {
            $detail = $model;
            $detail->qty = $this->qty+$model->qty;
            $detail->total_price = $this->totalPrice+$model->total_price;
        }else{

            $detail = new InventoryDetails();
            $detail->qty = $this->qty;
            $detail->unit_price = ($this->totalPrice/$this->qty);
            $detail->total_price = $this->totalPrice;
        }   

        $detail->category_id = $this->product->category->id;
        $detail->product_id = $this->product->id;
        $detail->warehouse_id = $this->warehouses->id;

        $detail->status = $this->status;
        $detail->save();
    }


    public function storeInventoryLog()
    {
        $log = new InventoryLogs();
        $log->category_id = $this->product->category->id;
        $log->product_id = $this->product->id;
        $log->warehouse_id = $this->warehouses->id;
        $log->unit_price = ($this->totalPrice / $this->qty);
        $log->qty = $this->qty;
        $log->total_price = $this->totalPrice;
        $log->status = $this->status;
        $log->type = 'in';
        $log->reference = $this->reference;
        $log->save();
    }

    
}
