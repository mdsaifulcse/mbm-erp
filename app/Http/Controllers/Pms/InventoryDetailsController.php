<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Warehouses;
use App\Models\InventoryModels\InventoryDetails;


class InventoryDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->show('0&0&0');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InventoryDetails  $inventoryDetails
     * @return \Illuminate\Http\Response
     */
    public function getProduct($category_id){
        return Product::when($category_id>0,function($query) use($category_id){
            return $query->where('category_id',$category_id);
        })
        ->orderBy('name','asc')
        ->get();
    }
     /**
     * Display the specified resource.
     *
     * @param  \App\Models\InventoryDetails  $inventoryDetails
     * @return \Illuminate\Http\Response
     */
    public function show($data)
    {
        $category_id=explode('&', $data)[0] ? explode('&', $data)[0] : 0;
        $product_id=explode('&', $data)[1] ? explode('&', $data)[1] : 0;
        $warehouse_id=explode('&', $data)[2] ? explode('&', $data)[2] : 0;

        $data = [
            'title' => 'Inventory Details List',
            'category_id' => $category_id,
            'product_id' => $product_id,
            'warehouse_id' => $warehouse_id,
            'categories' => Category::orderBy('name','asc')->get(),
            'warehouses' => Warehouses::orderBy('name','asc')->get(),
            'inventory_data' => InventoryDetails::when($category_id>0,function($query) use($category_id){
                return$query->where('category_id',$category_id);
            })->when($product_id>0,function($query) use($product_id){
                return$query->where('product_id',$product_id);
            })->when($warehouse_id>0,function($query) use($warehouse_id){
                return$query->where('warehouse_id',$warehouse_id);
            })->where('status','active')->paginate(20)
        ];

        return view('backend.pages.inventory.inventory-details.index',$data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InventoryDetails  $inventoryDetails
     * @return \Illuminate\Http\Response
     */
    public function edit(InventoryDetails $inventoryDetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InventoryDetails  $inventoryDetails
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InventoryDetails $inventoryDetails)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InventoryDetails  $inventoryDetails
     * @return \Illuminate\Http\Response
     */
    public function destroy(InventoryDetails $inventoryDetails)
    {
        //
    }
}
