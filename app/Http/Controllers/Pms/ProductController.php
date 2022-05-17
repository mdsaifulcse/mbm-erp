<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Models\PmsModels\Brand;
use App\Models\PmsModels\Category;
use App\Models\PmsModels\Product;
use App\Models\PmsModels\Suppliers;
use App\Models\PmsModels\ProductUnit;
use Illuminate\Http\Request;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $title = 'Product';
            $products = Product::orderby('id','desc')->paginate(30);
            $categories = Category::all();
            $suppliers = Suppliers::pluck('name','id')->all();
            $brands = Brand::pluck('name','id')->all();
            $unit = ProductUnit::pluck('unit_name','id')->all();

            $prefix='P-'.date('y', strtotime(date('Y-m-d'))).'-MBM-';
            $sku=uniqueCode(14,$prefix,'products','id');

            return view('pms.backend.pages.products.index', compact('title', 'products', 'categories', 'suppliers','brands','sku','unit'));
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
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'string', 'max:255'],
            'brand_id' => ['required', 'string', 'max:255'],
            'product_unit_id' => ['required', 'string', 'max:255'],
            'tax' => ['required', 'regex:/^\d*(\.\d{1,2})?$/'],
            'unit_price' => ['required', 'regex:/^\d*(\.\d{1,2})?$/'],
            'sku' => ['required'],
            'supplier' => ['required'],
        ]);

        //dd($request->supplier);

        try {
            $suppliers = $request->supplier;
            $inputs = $request->all();
            unset($inputs['_token']);
            unset($inputs['_method']);
            unset($inputs['supplier']);

            $product = Product::create($inputs);
            $product->suppliers()->sync($suppliers);
            return $this->backWithSuccess('Product has been added successfully');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        try {
            $product->src = route('pms.product-management.product.update',$product->id);
            $product->req_type = 'put';
            $suppliers = [];
            foreach ($product->suppliers as $supplier){
                $suppliers[] = $supplier->id;
            }

            $product->supplier = $suppliers;
            $data = [
                'status' => 'success',
                'info' => $product
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $this->validate($request, [
            'sku' => ['required'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'string', 'max:255'],
            'brand_id' => ['required', 'string', 'max:255'],
            'product_unit_id' => ['required', 'string', 'max:255'],
            'unit_price' => ['required', 'regex:/^\d*(\.\d{1,2})?$/'],
            'tax' => ['required', 'regex:/^\d*(\.\d{1,2})?$/'],
            'supplier' => ['required'],
        ]);

        try {
            $suppliers = $request->supplier;
            $inputs = $request->all();
            unset($inputs['_token']);
            unset($inputs['_method']);
            unset($inputs['supplier']);

            $product->update($inputs);
            $product->suppliers()->sync($suppliers);
            return $this->backWithSuccess('Product has been updated successfully');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        try {
            $product->suppliers()->sync([]);
            $product->delete();
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }

    public function importProduct(Request $request)
    {
        $this->validate($request, [
            'product_file'  => 'mimes:xls,xlsx'
        ]);

        $path = $request->file('product_file')->getRealPath();

        try {

            Excel::import(new ProductImport, $path);

            return $this->backWithSuccess('Excel Data Imported successfully.');

        }catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $error=[];
            foreach ($failures as $failure) {
                $failure->row(); 
                $failure->attribute(); 
                $error[]=$failure->errors(); 
                $failure->values(); 
            }

            return $this->backWithError($error[0][0]);
        }
    }
}
