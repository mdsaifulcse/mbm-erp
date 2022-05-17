<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\EducationLevel;
use App\Models\Hr\EducationDegree;
use DB, Validator, ACL;
class EducationLevelController extends Controller
{
    public function showForm(){
    	//ACL::check(["permission" => "hr_setup"]);
        #-----------------------------------------------------------#
    	$levelList= DB::table('hr_education_level AS l')->pluck('education_level_title', 'id');
        $degrees=DB::table('hr_education_degree_title AS a')
                    ->select(
                        "a.id",
                        "b.education_level_title",
                        "a.education_degree_title"
                    )
                    ->leftJoin("hr_education_level AS b", "b.id", "=", "a.education_level_id")
                    ->get();


    	return view('hr/setup/education_title', compact('levelList','degrees'));
    }
    public function saveData(Request $request){
    	//ACL::check(["permission" => "hr_setup"]);
        #-----------------------------------------------------------#
        $validator= Validator::make($request->all(),[
            'education_level_id'  => 'required',
        	'education_degree_title' => 'required| max:128'
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
            EducationDegree::create($input);
            toastr()->success('Successfully EducationDegree created');
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }

    public function degreeDelete($id){
        try {
            $educationDegree = EducationDegree::findOrFail($id);
            $educationDegree->delete();
            toastr()->success('Successfully EducationDegree deleted');
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }

    public function degreeEdit($id){
        $levelList= DB::table('hr_education_level AS l')->pluck('education_level_title', 'id');

        $degree=DB::table('hr_education_degree_title AS a')
                ->select(
                    "a.id",
                    "b.education_level_title",
                    "a.education_degree_title",
                    "b.id AS lvl_id"
                )
                ->leftJoin("hr_education_level AS b", "b.id", "=", "a.education_level_id")->where('a.id', $id)->first();
        $degrees=DB::table('hr_education_degree_title AS a')
                    ->select(
                        "a.id",
                        "b.education_level_title",
                        "a.education_degree_title"
                    )
                    ->leftJoin("hr_education_level AS b", "b.id", "=", "a.education_level_id")
                    ->get();


        return view('hr/setup/education_title_edit', compact('degree','levelList','degrees'));
    }

    public function degreeUpdate(Request $request){
        $validator= Validator::make($request->all(),[
            'education_level_id'  => 'required',
            'education_degree_title' => 'required| max:128'
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
            $educationDegree = EducationDegree::findOrFail($request->id);
            $educationDegree->update($input);
            
            toastr()->success('Successfully EducationDegree updated');
            return redirect('hr/setup/education_title');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }
}

