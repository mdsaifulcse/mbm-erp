<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Models\PmsModels\InventoryModels\InventorySummary;
use App\Models\PmsModels\InventoryModels\InventoryDetails;
use App\Models\PmsModels\Category;
use App\Models\PmsModels\Product;
use App\Models\PmsModels\Warehouses;
use Illuminate\Http\Request;
use Illuminate\Http\Requests;


class InventorySummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->show('0&0');
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
    public function store(Requests\InventorySummaryRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InventorySummary  $InventorySummary
     * @return \Illuminate\Http\Response
     */
    public function show($data)
    {
        $category_id=explode('&', $data)[0] ? explode('&', $data)[0] : 0;
        $product_id=explode('&', $data)[1] ? explode('&', $data)[1] : 0;
       
        $data = [
            'title' => 'Inventory Summary List',
            'category_id' => $category_id,
            'product_id' => $product_id,
            'categories' => Category::orderBy('name','asc')->get(),
            'inventory_data' => InventorySummary::when($category_id>0,function($query) use($category_id){
                return$query->where('category_id',$category_id);
            })->when($product_id>0,function($query) use($product_id){
                return$query->where('product_id',$product_id);
            })->where('status','active')->paginate(20)
        ];

        return view('pms.backend.pages.inventory.inventory-summary.index',$data);
    }

    /**
     * Show the form for product wise Inventory details.
     *
     * @param  \App\Models\InventorySummary  $InventorySummary
     * @return \Illuminate\Http\Response
     */

    public function warehouseWiseProductInventoryDetails($product_id)
    {
           try {

            $title = 'Warehouse Wise Product Inventory Details';

            $product = Product::findOrFail($product_id);

            return view('pms.backend.pages.inventory.inventory-summary.warehouse-wise-inventory-details', compact('title','product'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InventorySummary  $InventorySummary
     * @return \Illuminate\Http\Response
     */
    public function edit(InventorySummary $InventorySummary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InventorySummary  $InventorySummary
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InventorySummary $InventorySummary)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InventorySummary  $InventorySummary
     * @return \Illuminate\Http\Response
     */
    public function destroy(InventorySummary $InventorySummary)
    {
        //
    }
}
