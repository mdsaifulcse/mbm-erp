<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;
use Validator,DB,ACL, Cache;
use App\Models\Hr\Department;

class SubsectionController extends Controller
{
    #show subsection
    public function subsection()
    {
        $areaList = collect(area_by_id())->pluck('hr_area_name','hr_area_id');

        $subSections = Subsection::get();
        $department = department_by_id();
        $section = section_by_id();
        
        $trashed = [];
    	return view('hr.setup.subsection', compact('areaList', 'subSections', 'trashed', 'section', 'department'));
    }

    public function subsectionStore(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'hr_subsec_area_id'       => 'required|max:11',
            'hr_subsec_department_id' => 'required|max:11',
    		'hr_subsec_section_id'    => 'required|max:11',
    		//'hr_subsec_name'    => 'required|max:128|unique:hr_subsection',
    		'hr_subsec_name_bn' => 'max:255',
    		'hr_subsec_code'    => 'max:10'
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
            Subsection::create($input);
            Cache::forget('subSection_by_id');
            toastr()->success('Successfully Sub Section created');
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }

    // # Return Sub Section List by Area, Department & Section ID
    public function getSubSectionListBySectionID(Request $request)
    {
        $list = "<option value=\"\">Select Sub Section Name </option>";
        if (!empty($request->area_id) && !empty($request->department_id) && !empty($request->section_id))
        {
            $secList  = Subsection::where('hr_subsec_area_id', $request->area_id)
                    ->where('hr_subsec_department_id', $request->department_id)
                    ->where('hr_subsec_section_id', $request->section_id)
                    ->where('hr_subsec_status', '1')
                    ->pluck('hr_subsec_name', 'hr_subsec_id');

            foreach ($secList as $key => $value)
            {
                $list .= "<option value=\"$key\">$value</option>";
            }
        }
        return $list;
    }
    public function subsectionDelete($id)
    {
        try {
            $subsection = Subsection::findOrFail($id);
            $subsection->delete();
            Cache::forget('subSection_by_id');
            toastr()->success('Successfully Sub Section deleted');
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }
    public function subsectionUpdate($id)
    {
        $subSections= DB::table('hr_subsection AS ss')
                        ->Select(
                            'ss.hr_subsec_id',
                            'ss.hr_subsec_name',
                            'ss.hr_subsec_name_bn',
                            'a.hr_area_name',
                            'd.hr_department_name',
                            's.hr_section_name'
                        )
                        ->leftJoin('hr_area AS a', 'a.hr_area_id', '=', 'ss.hr_subsec_area_id')
                        ->leftJoin('hr_department AS d', 'd.hr_department_id', '=', 'ss.hr_subsec_department_id')
                        ->leftJoin('hr_section AS s', 's.hr_section_id', '=', 'ss.hr_subsec_section_id')
                        ->get();

        $subSection= DB::table('hr_subsection')->where('hr_subsec_id', $id)->first();

        $areaList = Area::where('hr_area_status', 1)->pluck('hr_area_name','hr_area_id');

        $departmentList = Department::where('hr_department_area_id', $subSection->hr_subsec_area_id)->pluck('hr_department_name','hr_department_id');
        $sectionList= Section::where('hr_section_id', $subSection->hr_subsec_section_id)->pluck('hr_section_name','hr_section_id');

        $trashed = [];

        return view('/hr/setup/subsection_update', compact('areaList', 'subSection', 'departmentList', 'sectionList','subSections','trashed'));
    }
    public function subsectionUpdateStore(Request $request){
        // dd($request->all());
        $validator= Validator::make($request->all(),[
            'hr_subsec_area_id'       => 'required|max:11',
            'hr_subsec_department_id' => 'required|max:11',
            'hr_subsec_section_id'    => 'required|max:11',
            'hr_subsec_name'    => 'required|max:128',
            'hr_subsec_name_bn' => 'max:255',
            'hr_subsec_code'    => 'max:10'
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
            $subSection = Subsection::findOrFail($request->hr_subsec_id);
            $subSection->update($input);
            Cache::forget('subSection_by_id');
            toastr()->success('Successfully Sub Section updated');
            return redirect('hr/setup/subsection');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }
}
