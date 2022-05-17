<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Models\PmsModels\ProductUnit;
use Illuminate\Http\Request;

class ProductUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $title = 'Product Unit';
            $product_units = ProductUnit::orderby('id','desc')->get();
            $status=status();

            $prefix='PU-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
            $unit_code=uniqueCode(14,$prefix,'product_units','id');

            return view('pms.backend.pages.product-unit.index', compact('title', 'product_units','status','unit_code'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
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
        $this->validate($request, [
            'unit_name' => ['required', 'string', 'max:255'],
            'unit_code' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
        ]);

        try {

            $inputs = $request->all();
            unset($inputs['_token']);
            unset($inputs['_method']);
            
            $productUnit = ProductUnit::create($inputs);
            
            return $this->backWithSuccess('Product Unit has been added successfully');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ProductUnit  $productUnit
     * @return \Illuminate\Http\Response
     */
    public function show(ProductUnit $productUnit)
    {
        try {
            $productUnit->src = route('pms.product-management.product-unit.update',$productUnit->id);
            $productUnit->req_type = 'put';
            
            $data = [
                'status' => 'success',
                'info' => $productUnit
            ];

            return response()->json($data);
        }catch (\Throwable $th){
            $data = [
                'status' => null,
                'info' => $th->getMessage()
            ];
            return response()->json($data);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ProductUnit  $productUnit
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductUnit $productUnit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProductUnit  $productUnit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductUnit $productUnit)
    {
        $this->validate($request, [
            'unit_name' => ['required', 'string', 'max:255'],
            'unit_code' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
        ]);

        try {
         
            $inputs = $request->all();
            unset($inputs['_token']);
            unset($inputs['_method']);

            $productUnit->update($inputs);

            return $this->backWithSuccess('Product Unit has been updated successfully');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProductUnit  $productUnit
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductUnit $productUnit)
    {
       try {
        $productUnit->delete();
    }catch (\Throwable $th){
        return response()->json($th->getMessage());
    }
}
}
