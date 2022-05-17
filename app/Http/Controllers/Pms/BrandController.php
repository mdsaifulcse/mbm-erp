<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Models\PmsModels\Brand;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use DB;

use App\Imports\BrandsImport;
use Maatwebsite\Excel\Facades\Excel;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $title = 'Brands';
            return view('pms.backend.pages.brand.index', compact('title'));
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
        try {
            $data = [
                'status' => 'success',
                'info' => Brand::all()
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            if ($request->hasFile('image')) {
                $image = $request->image;
                $x = 'abcdefghijklmnopqrstuvwxyz0123456789';
                $x = str_shuffle($x);
                $x = substr($x, 0, 6) . 'DAC.';
                $filename = time() . $x . $image->getClientOriginalExtension();
                Image::make($image->getRealPath())
//                    ->resize(128, 128)
                ->save(public_path('/upload/brands/' . $filename));
                $path = "/upload/brands/" . $filename;
//            'code', 'name', 'file_name', 'image'
                $data['file_name'] = $request->image->getClientOriginalName();
                $data['image'] = $path;
            }
            $data['code'] = $request->code;
            $data['name'] = $request->name;

            $brand = Brand::create($data);

            $data = [
                'status' => 'success',
                'info' => [$brand]
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand)
    {
        try {
            $data = [
                'status' => 'success',
                'info' => $brand
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
    public function update(Request $request, Brand $brand)
    {
        try {
           if ($request->hasFile('image')) {
               if ($brand->image){
                   if (file_exists(public_path($brand->image))){
                       unlink(public_path($brand->image));
                   }
               }
               $image = $request->image;
               $x = 'abcdefghijklmnopqrstuvwxyz0123456789';
               $x = str_shuffle($x);
               $x = substr($x, 0, 6) . 'DAC.';
               $filename = time() . $x . $image->getClientOriginalExtension();
               Image::make($image->getRealPath())
//                    ->resize(128, 128)
                   ->save(public_path('/upload/brands/' . $filename));
               $path = "/upload/brands/" . $filename;
//            'code', 'name', 'file_name', 'image'
               $data['file_name'] = $request->image->getClientOriginalName();
               $data['image'] = $path;
           }
           $data['code'] = $request->code;
           $data['name'] = $request->name;

           $brand->update($data);

            $data = [
                'status' => 'success',
                'info' => $request->all()
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        try {
            if ($brand->image){
                if (file_exists(public_path($brand->image))){
                    unlink(public_path($brand->image));
                }
            }
            $brand->delete();
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }

    public function importBrand(Request $request)
    {
        $this->validate($request, [
            'brand_file'  => 'required|mimes:xls,xlsx'
        ]);

        $path = $request->file('brand_file')->getRealPath();

        try {

            Excel::import(new BrandsImport, $path);

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
