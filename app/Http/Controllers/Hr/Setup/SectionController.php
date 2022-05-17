<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\Section;
use App\Models\Hr\Department;
use Validator,DB,ACL, Cache;

class SectionController extends Controller
{
    public function section()
    {
    	$areaList = collect(area_by_id())->pluck('hr_area_name','hr_area_id');
        $department = department_by_id();
        $sections= Section::get();
        $trashed = [];

    	return view('hr.setup.section', compact('areaList', 'sections', 'trashed', 'department'));
    }

    public function sectionStore(Request $request)
    {
    	$validator= Validator::make($request->all(),[
            'hr_section_area_id' => 'required|max:11',
    		'hr_section_department_id' => 'required|max:11',
    		// 'hr_section_name'    => 'required|max:128|unique:hr_section',
    		'hr_section_name_bn' => 'max:255',
    		'hr_section_code'    => 'max:10'
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
            Section::create($input);
            Cache::forget('section_by_id');
            toastr()->success('Successfully Section created');
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }


    // # Return Section List by Department ID
    public function getSectionListByDepartmentID(Request $request)
    {
        $list = "<option value=\"\">Select Section Name </option>";
        if (!empty($request->area_id) && !empty($request->department_id))
        {
            $lineList  = Section::where('hr_section_area_id', $request->area_id)
                    ->where('hr_section_department_id', $request->department_id)
                    ->where('hr_section_status', '1')
                    ->pluck('hr_section_name', 'hr_section_id');

            foreach ($lineList as $key => $value)
            {
                $list .= "<option value=\"$key\">$value</option>";
            }
        }
        return $list;
    }
    public function sectionDelete($id)
    {
        try {
            $section = Section::findOrFail($id);
            $section->delete();
            Cache::forget('section_by_id');
            toastr()->success('Successfully Section deleted');
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }
    public function sectionUpdate($id)
    {
        $areaList = Area::where('hr_area_status', 1)->pluck('hr_area_name','hr_area_id');
        $section= DB::table('hr_section')->where('hr_section_id',$id)->first();

        $departmentList= Department::where('hr_department_area_id', $section->hr_section_area_id)->pluck('hr_department_name', 'hr_department_id');

        $sections= DB::table('hr_section AS s')
                        ->Select(
                            's.hr_section_id',
                            's.hr_section_name',
                            's.hr_section_name_bn',
                            'a.hr_area_name',
                            'd.hr_department_name'
                        )
                        ->leftJoin('hr_area AS a', 'a.hr_area_id', '=', 's.hr_section_area_id')
                        ->leftJoin('hr_department AS d', 'd.hr_department_id', '=', 's.hr_section_department_id')
                        ->get();
        $trashed = [];
        return view('/hr/setup/section_update', compact('areaList','section','departmentList','sections', 'trashed'));
    }
    public function sectionUpdateStore(Request $request){
        // dd($request->all());
        $validator= Validator::make($request->all(),[
            'hr_section_area_id' => 'required|max:11',
            'hr_section_department_id' => 'required|max:11',
            'hr_section_name'    => 'required|max:128',
            'hr_section_name_bn' => 'max:255',
            'hr_section_code'    => 'max:10'
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
            $section = Section::findOrFail($request->hr_section_id);
            $section->update($input);
            Cache::forget('section_by_id');
            toastr()->success('Successfully Section updated');
            return redirect('hr/setup/section');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }
}
