<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Designation;
use App\Models\Hr\designation_tree;
use App\Models\Hr\EmpType;
use Validator,DB, ACL, Cache, stdClass;
use Carbon\Carbon;

class DesignationController extends Controller
{
    #show department

       public function __construct()
    {
        ini_set('zlib.output_compression', 1);
    }


    public function designation()
    {
        //ACL::check(["permission" => "hr_setup"]);
        #-----------------------------------------------------------#
        $hr_grade = DB::table('hr_grade')
                    ->orderBy('grade_sequence' ,'asc')
                    ->pluck('grade_name','id');

                    // dd($parent_id);

        $emp_type= EmpType::where('hr_emp_type_status','1')
                   ->pluck('hr_emp_type_name','emp_type_id');

        $designations= DB::table('hr_designation AS d')
            ->select(
                'd.hr_designation_id',
                'd.hr_designation_name',
                'd.hr_designation_name_bn',
                'd.hr_designation_grade',
                'emp.hr_emp_type_name'
            )
            ->leftJoin('hr_emp_type AS emp', 'emp.emp_type_id', '=', 'd.hr_designation_emp_type')
            ->orderBy('d.hr_designation_name', 'DESC')
            ->get();
    	return view ('hr.setup.designation',compact('emp_type', 'designations','hr_grade'));
    }

public function parentget(Request $request)
    {

                $list = "<option value=\"\">Select Parent </option>";
        if (!empty($request->hr_designation_emp_type))
        {
            $lineList  =DB::table('hr_designation_view')
                    ->where('hr_designation_emp_type',$request->hr_designation_emp_type)
                    ->orderBy('grade_sequence' ,'desc')
                    ->pluck('hr_designation_name','hr_designation_id');

            foreach ($lineList as $key => $value)
            {
                $list .= "<option value=\"$key\">$value</option>";
            }
        }
        return $list;


    }


public function hierarchynew()
    {


            $designation_for_top_managenent =designation_tree::with('subcategory')
                                ->where('parent_id',0)
                                ->where('hr_designation_status',1)
                                ->get();


            $designation_for_managenent_staff=designation_tree:://setEmptype([1])
                                  with('subcategory')
                                ->where('parent_id',-1)
                                ->where('hr_designation_status',1)
                                ->get();

            $employee_count=DB::table('hr_designation_view')
                        ->pluck('employee_count','hr_designation_id');



                    $hr_grade = DB::table('hr_grade')
                    ->orderBy('grade_sequence' ,'asc')
                    ->pluck('grade_name','id');

                    $emp_type= EmpType::where('hr_emp_type_status','1')
                   ->pluck('hr_emp_type_name','emp_type_id');

                    $parent = DB::table('hr_designation')
                    ->orderBy('hr_designation_name' ,'asc')
                    ->pluck('hr_designation_name','hr_designation_id');


        return view('hr.setup.designation_hierarchy',compact( 'designation_for_top_managenent','designation_for_managenent_staff','hr_grade','emp_type'
            ,'parent','employee_count'));
    }

    public function get_hierarchynew_data(Request $request)
    {

           $designation_for_worker=designation_tree:://setEmptype([1])
                    with('subcategory')
                    ->where('parent_id',-2)
                    ->where('hr_designation_status',1)
                    ->get();
 

        return view('hr.setup.designation_hierarchy_worker',compact( 'designation_for_worker'))->render();;
    }



        public function getdata(Request $request)
         {
            

                $data= DB::table('hr_designation')
                        ->where('hr_designation_id','=',$request->id)
                        ->orderBy('hr_designation_name' ,'asc')
                        ->get();

                return $data;
        }
    



    public function designationStore(Request $request)
    {
        // dd('dddd');
        //ACL::check(["permission" => "hr_setup"]);
        #-----------------------------------------------------------#
    	$validator= Validator::make($request->all(),[
            'hr_designation_emp_type'=>'required|max:128',
            'hr_designation_name'=>'required|max:128|unique:hr_designation',
            'hr_designation_name_bn'=>'max:255',
            'hr_designation_grade'=>'required|max:128'
    	]);

    	if($validator->fails()){
    		return back()
    			->withErrors($validator)
    			->withInput()
    			->with('error', 'Please fillup all required fields!');
    	}
    	else
        {
    		$designation= new Designation;
            $designation->hr_designation_emp_type = $request->hr_designation_emp_type;
            $designation->hr_designation_name     = $request->hr_designation_name;
            $designation->hr_designation_name_bn  = $request->hr_designation_name_bn;
            $designation->hr_designation_grade  = $request->hr_designation_grade;

    		if ($designation->save())
            {
                $this->logFileWrite("Designation saved", $designation->hr_designation_id);
                Cache::forget('designation');
                return back()
                    ->withInput()
                    ->with('success', 'Save Successful.');
            }
            else
            {
                return back()
                    ->withInput()->with('error', 'Please try again.');
            }
    	}
    }


    public function hierarchy(Request $request)
    {
        $designation = $request->designation;
        if (!empty($designation) && sizeof($designation) > 0)
        {
            foreach ($designation as $id => $position)
            {
                Designation::where('hr_designation_id', $id)
                ->update(['hr_designation_position' => $position]);

                $this->logFileWrite("Designation Hierarchy(Position) Updated", $id);
            }
        }

        return response()
            ->json(['Designation position change successful.']);
    }

    # Return Designation List by Employee Type ID
    public function getDesignationListByEmployeeTypeID(Request $request)
    {
        $list = "<option value=\"\">Select Designation Name </option>";
        if (!empty($request->employee_type_id))
        {
            $desList  = Designation::where('hr_designation_emp_type', $request->employee_type_id)
                    ->where('hr_designation_status', '1')
                    ->pluck('hr_designation_name', 'hr_designation_id');

            foreach ($desList as $key => $value)
            {
                $list .= "<option value=\"$key\">$value</option>";
            }
        }
        return $list;
    }

    public function designationDelete($id)
    {
        DB::table('hr_designation')->where('hr_designation_id', '=', $id)->delete();
        $this->logFileWrite("Designation Deleted", $id);
        Cache::forget('designation');
        return redirect('/hr/setup/designation')->with('success', "Successfuly deleted Designation");
    }

    public function designationUpdate($id)
    {
        $emp_type= EmpType::where('hr_emp_type_status','1')->pluck('hr_emp_type_name','emp_type_id');
        $designation= DB::table('hr_designation')->where('hr_designation_id', '=', $id)->first();

        $designations= DB::table('hr_designation AS d')
            ->select(
                'd.hr_designation_id',
                'd.hr_designation_name',
                'd.hr_designation_name_bn',
                'd.hr_designation_grade',
                'emp.hr_emp_type_name'
            )
            ->leftJoin('hr_emp_type AS emp', 'emp.emp_type_id', '=', 'd.hr_designation_emp_type')
            ->orderBy('d.hr_designation_name', 'DESC')
            ->get();
        return view('/hr/setup/designation_update', compact('emp_type', 'designation','designations'));
    }

    public function designationupdateStore(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'hr_designation_emp_type'=>'required',
                'hr_designation_name'=>'required',
                'parent_id1'=>'required',
                'hr_designation_name_bn'=>'required',
                'designation_short_name'=>'required|min:3',
                'hr_designation_grade'=>'required'
            ]);


            if($validator->fails()){
                return [
                    'type' => 'error',
                    'msg' => $validator->errors()->first()
                ];
            };


            $unique_short_name=DB::table('hr_designation')
            ->where('hr_designation_id','<>',$request->hr_designation_id)
            ->where( DB::raw('upper(designation_short_name)'),strtoupper($request->designation_short_name))
            ->get()
            ->count();


            $unique_designation_name=DB::table('hr_designation')
            ->where( DB::raw('upper(hr_designation_name)'),strtoupper($request->hr_designation_name))
            ->where('hr_designation_id','<>',$request->hr_designation_id)
            ->get()
            ->count();

            if ($unique_short_name==1 or $unique_designation_name==1){
                return [
                    'type' => 'error',
                    'msg' => 'Designation Name or Short Name Already Exist'
                ];  
            }



                if ($request->action_type=='U' ){ // for update designation name
                    DB::table('hr_designation')->where('hr_designation_id', '=', $request->hr_designation_id)
                    ->update([
                        'hr_designation_emp_type'=>$request->hr_designation_emp_type,
                        'hr_designation_name'=>$request->hr_designation_name,
                        'parent_id'=>$request->parent_id1,
                        'hr_designation_name_bn'=>$request->hr_designation_name_bn,
                        'designation_short_name'=>$request->designation_short_name,
                        'hr_designation_grade'=>$request->hr_designation_grade,
                        'hr_designation_status'=>$request->hr_designation_status,
                        'updated_at'=>Carbon::now()->toDateTimeString(),
                        'updated_by'=>auth()->user()->id
                    ]);

                    return [
                        'type' => 'success',
                        'msg' => 'update Successful'
                    ];
                }

                if ($request->action_type=='P' ){ // for change designation type or parent

                        if ($request->parent_id==null or $request->parent_id==''){
                            return [
                                'type' => 'error',
                                'msg' => 'Please Select New parent id..'
                            ];  
                }

                $parent_count=DB::table('hr_designation')
                            ->where('parent_id',$request->hr_designation_id)
                            ->get()
                            ->count();

                if ($parent_count>0){
                            return [
                                'type' => 'error',
                                'msg' => 'Can not Change .Please Change Child Record First..'
                                ];
                }



                DB::table('hr_designation')->where('hr_designation_id', '=', $request->hr_designation_id)
                ->update([
                    'hr_designation_emp_type'=>$request->hr_designation_emp_type,
                    'hr_designation_name'=>$request->hr_designation_name,
                    'parent_id'=>$request->parent_id,
                    'hr_designation_name_bn'=>$request->hr_designation_name_bn,
                    'designation_short_name'=>$request->designation_short_name,
                    'hr_designation_grade'=>$request->hr_designation_grade,
                    'hr_designation_status'=>1,
                    'updated_at'=>Carbon::now()->toDateTimeString(),
                    'updated_by'=>auth()->user()->id
                ]);
                return [
                    'type' => 'success',
                    'msg' => 'Parent Change Successful'
                ];
            }

            if ($request->action_type=='C' ){ // for create designation
                $unique_short_name=DB::table('hr_designation')
                ->where( DB::raw('upper(designation_short_name)'),strtoupper($request->designation_short_name))
                ->get()
                ->count();

                $unique_designation_name=DB::table('hr_designation')
                ->where( DB::raw('upper(hr_designation_name)'),strtoupper($request->hr_designation_name))
                ->get()
                ->count();

                if ($unique_short_name==1 or $unique_designation_name==1){
                    return [
                        'type' => 'error',
                        'msg' => 'Designation Name or Short Name Already Exist'
                    ];  
                }

                if ($request->parent_id1==null or $request->parent_id1==''){
                    return [
                        'type' => 'error',
                        'msg' => 'Please Select parent id..'
                    ];  
                }
// dd($request->parent_id);
                DB::table('hr_designation')->insert([
                    [
                        'hr_designation_emp_type'=>$request->hr_designation_emp_type,
                        'hr_designation_name'=>strtoupper($request->hr_designation_name),
                        'parent_id'=>$request->hr_designation_id,
                        'hr_designation_name_bn'=>$request->hr_designation_name_bn,
                        'designation_short_name'=>strtoupper($request->designation_short_name),
                        'hr_designation_grade'=>$request->hr_designation_grade,
                        'hr_designation_status'=>1,
                        'created_at'=>Carbon::now()->toDateTimeString(),
                        'created_by'=>auth()->user()->id
                    ]
                ]);

                return [
                    'type' => 'success',
                    'msg' => 'Create Successful'
                ];
            }

            if ($request->action_type=='I' ){ // for inactive designation

                $parent_count=DB::table('hr_designation')
                                ->where('parent_id',$request->hr_designation_id)
                                ->get()
                                ->count();

                if ($parent_count>0){

                return [
                    'type' => 'error',
                    'msg' => 'Can not Inactive .Please Inactive Child Record First..'
                    ];
                }

                DB::table('hr_designation')->where('hr_designation_id', '=', $request->hr_designation_id)
                ->update([
                    'hr_designation_status'=>0,
                    'updated_at'=>Carbon::now()->toDateTimeString(),
                    'updated_by'=>auth()->user()->id,
                    'deleted_at'=>Carbon::now()->toDateTimeString()
                ]);
                return [
                    'type' => 'success',
                    'msg' => 'Inactive Successful'
                ];
            }


        } catch (\Exception $e) {
// dd($e->getMessage())
            return [
                'type' => 'error',
                'msg' => $e->getMessage()
            ];
        }


    }

    public function searchDesignation(Request $request)
    {
        $input = $request->all();
        $getDesignation = Designation::where('hr_designation_name', 'LIKE', '%'.$input['keyvalue'].'%')->limit(10)->get();
        $data = array();
        foreach ($getDesignation as $designation) {
            $des = new stdClass();
            $des->id = $designation['hr_designation_id'];
            $des->name = $designation['hr_designation_name'];
            $data[] = $des;
        }
        return $data;
    }

}
