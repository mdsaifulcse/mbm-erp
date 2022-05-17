lm<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\Department;
use Validator, ACL,DB, Cache;


class DepartmentController extends Controller
{
    public function department()
    {
        $areaList = collect(area_by_id())->pluck('hr_area_name','hr_area_id');
        $departments= Department::all();
        $trashed= Department::onlyTrashed();
    	return view('hr.setup.department', compact('areaList', 'departments','trashed'));
    }

    public function departmentStore(Request $request)
    {
    	$validator= Validator::make($request->all(),[
            'hr_department_area_id' =>'required|max:11',
            'hr_department_name'    =>'required|max:128',
    		'hr_department_name_bn' =>'required|max:255',
            'hr_department_code'    =>'required|max:2|unique:hr_department',
            'hr_department_min_range' =>'required| max:10',
    		'hr_department_max_range' =>'required| max:10'
    	]);

        if($validator->fails()){
            foreach ($validator->errors()->all() as $message){
                toastr()->error($message);
            }
            return back()->withInput();
        }

        $input = $request->all();
        $input['created_by'] = auth()->user()->id;
        try {
            Department::create($input);
            Cache::forget('department_by_id');
            toastr()->success('Successfully Department created');
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }

    # Return Department List by Area ID
    public function getDepartmentListByAreaID(Request $request)
    {
        $list = "<option value=\"\">Select Department Name </option>";
        if (!empty($request->area_id))
        {
            $lineList  = Department::where('hr_department_area_id', $request->area_id)
                    ->where('hr_department_status', '1')
                    ->pluck('hr_department_name', 'hr_department_id');

            foreach ($lineList as $key => $value)
            {
                $list .= "<option value=\"$key\">$value</option>";
            }
        }
        return $list;
    }
    
    public function departmentDelete($id)
    {
        try {
            $department = Department::findOrFail($id);
            $department->delete();
            Cache::forget('department_by_id');
            toastr()->success('Successfully Department deleted');
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }
    public function departmentUpdate($id)
    {
        // dd($id);
        $areaList = Area::where('hr_area_status', 1)->pluck('hr_area_name','hr_area_id');
        $department= DB::table('hr_department')->where('hr_department_id', $id)->first();
        $departments= Department::all();
        $trashed= Department::onlyTrashed();
        return view('/hr/setup/department_update', compact('areaList', 'department','departments','trashed'));
    }

    public function departmentUpdateStore(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'hr_department_area_id' =>'required|max:11',
            'hr_department_name'    =>'required|max:128',
            'hr_department_name_bn' =>'required|max:255',
            'hr_department_code'    =>'required|max:2',
            'hr_department_min_range'    =>'required| max:10',
            'hr_department_max_range'    =>'required| max:10'
        ]);

        if($validator->fails()){
            foreach ($validator->errors()->all() as $message){
                toastr()->error($message);
            }
            return back()->withInput();
        }

        $input = $request->all();
        $input['updated_by'] = auth()->user()->id;
        try {
            $department = Department::findOrFail($request->hr_department_id);
            $department->update($input);
            Cache::forget('department_by_id');
            toastr()->success('Successfully Department updated');
            return redirect('hr/setup/department');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }

}
