<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Imports\CategoryImport;
use App\Models\PmsModels\Category;
use App\Models\PmsModels\RequisitionType;
use App\Models\PmsModels\Warehouses;
use App\Models\PmsModels\CategoryDepartment;
use App\Models\Hr\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $title = 'Category';
            $categories = Category::with('requisitionType')->get();
            $requisitions = RequisitionType::all();
            $departments= Department::all();
            return view('pms.backend.pages.category.index', compact('title', 'categories','requisitions','departments'));
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
            'code' => ['required', 'string', 'max:255', 'unique:categories'],
            'name' => ['required', 'string', 'max:255'],
            'parent' => ['nullable', 'integer'],
            'requisition_type_id' => ['required', 'integer'],
        ]);
        try {
            $inputs = $request->all();
            unset($inputs['_token']);
            unset($inputs['_method']);


            $category=Category::create($inputs);

            $departments=$request->hr_department_id;
            $category->department()->sync($departments);

            return $this->backWithSuccess('Category created successfully');
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
    public function show(Category $category)
    {
        try {
            $category->src = route('pms.product-management.category.update',$category->id);
            $category->req_type = 'put';
            $category->parent_id = !$category->category?null:$category->category;

            $array=[];
            foreach($category->departmentsList as $key => $department){
                array_push($array,$department->hr_department_id);
            }

            $new_array=[];

            foreach(Department::whereIn('hr_department_id',$array)->select('hr_department_id')->get() as $values){
                array_push($new_array, $values->hr_department_id);
            }

            $data = [
                'status' => 'success',
                'info' => $category,
                'departments'=>Department::whereIn('hr_department_id',$array)->pluck('hr_department_id')->all()
            ];

            //dd($data['departments']);

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
    public function update(Request $request, Category $category)
    {
        $this->validate($request, [
            'code' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer'],
            'requisition_type_id' => ['required', 'integer'],
        ]);
        try {
            $inputs = $request->all();
            unset($inputs['_token']);
            unset($inputs['_method']);
            $category->update($inputs);

            $departments=$request->hr_department_id;
            $category->department()->sync($departments);

            return $this->backWithSuccess('Category updated successfully');
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
    public function destroy(Category $category)
    {
        try {
            $category->subCategory->each->delete();
            $category->delete();
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }


    public function importCategory(Request $request){

        $this->validate($request, [
            'category_file' => 'required|mimes:xls,xlsx'
        ]);

        $path = $request->file('category_file')->getRealPath();

        try {
            Excel::import(new CategoryImport(), $path);

            return $this->backWithSuccess('Category Data Imported successfully.');

        }catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

            $errorMessage='';
            $rowNumber=1;
            $rowNumber+=$e->failures()[0]->row();
            $column=$e->failures()[0]->attribute();

            $errorMessage.=$e->failures()[0]->errors()[0].' for row '.$rowNumber.' on Column '.$column;

            return $this->backWithError($errorMessage);
        }
    }
}
