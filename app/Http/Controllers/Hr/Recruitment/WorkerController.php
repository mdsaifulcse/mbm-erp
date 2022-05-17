<?php

namespace App\Http\Controllers\Hr\Recruitment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Hr\IDGenerator as IDGenerator;
use App\Models\Employee;
use App\Models\Hr\AdvanceInfo;
use App\Models\Hr\MedicalInfo;
use App\Models\Hr\Worker;
use App\Models\Hr\EmpType;
use App\Models\Hr\Unit;
use App\Models\Hr\Area;
use App\Models\Hr\Department;
use App\Models\Hr\Designation;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;
use App\Models\Hr\Location;
use Yajra\Datatables\Datatables;
use Auth, DB, Validator, Image, ACL;

class WorkerController extends Controller
{

    /*
    *----------------------------------------------
    * Worker Recruitment
    *----------------------------------------------
    */
    public function recruitForm()
    {
        // ACL::check(["permission" => "hr_recruitment_worker"]);
        #-----------------------------------------------------------#

        $employeeTypes  = EmpType::where('hr_emp_type_status', '1')->pluck('hr_emp_type_name', 'emp_type_id');
        $unitList  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_short_name', 'hr_unit_id');
        $areaList  = Area::where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');

        $user_unit= Auth()->user()->unit_id();
        DB::statement(DB::raw('set @sl=0'));
        $recruitList= Worker::whereIn('worker_unit_id', auth()->user()->unit_permissions())
                            ->where('worker_doctor_acceptance', '0')
                            ->where('worker_is_migrated', '0')
                            ->orderBy('worker_doj', "DESC")
                            ->select([
                                DB::raw('@sl := @sl + 1 AS sl'),
                                'worker_id',
                                'worker_name',
                                'worker_doj'
                            ])
                            ->get();
        return view('hr/recruitment/worker_recruit', compact('employeeTypes', 'unitList', 'areaList', 'recruitList'));
    }

    public function recruitStore(Request $request)
    {
        // ACL::check(["permission" => "hr_recruitment_worker"]);
        #-----------------------------------------------------------#
        $validator = Validator::make($request->all(), [
            'worker_name'           => 'required|max:128',
            'worker_doj'            => 'required|date',
            'worker_emp_type_id'    => 'required',
            'worker_designation_id' => 'required',
            'worker_unit_id'        => 'required',
            'worker_area_id'        => 'required',
            'worker_department_id'  => 'required',
            'worker_section_id'     => 'max:11',
            'worker_subsection_id'  => 'max:11',
            'worker_gender'         => 'required|max:15',
            'worker_dob'            => 'date',
            'worker_contact'        => 'required',
            'worker_ot'             => 'required|max:1',
            'worker_nid'            => 'max:30',
            'as_oracle_code'        => 'nullable|unique:hr_worker_recruitment',
            'as_rfid'               => 'nullable|unique:hr_worker_recruitment'
        ]);

        if ($validator->fails())
        {
            return back()
                    ->withErrors($validator)
                    ->withInput();
        }
        try {
            #-------------------Post Data------------------
            $postData = array(
                "worker_name"           => strtoupper($request->worker_name),
                "worker_doj"            => (!empty($request->worker_doj)?date('Y-m-d',strtotime($request->worker_doj)):null),
                "worker_emp_type_id" => $request->worker_emp_type_id,
                "worker_designation_id" => $request->worker_designation_id,
                "worker_unit_id" => $request->worker_unit_id,
                "worker_area_id" => $request->worker_area_id,
                "worker_department_id" => $request->worker_department_id,
                "worker_section_id" => $request->worker_section_id,
                "worker_subsection_id" => $request->worker_subsection_id,
                "worker_ot" => $request->worker_ot,
                "worker_gender" => $request->worker_gender,
                "worker_dob" => (!empty($request->worker_dob)?date('Y-m-d',strtotime($request->worker_dob)):null),
                "worker_contact" => $request->worker_contact,
                "worker_nid" => $request->worker_nid,
                "as_oracle_code"   => $request->as_oracle_code,
                "as_rfid"   => $request->as_rfid
            );

            #-------------------Update------------------
            if (!empty($request->worker_id))
            {
                if (Worker::where('worker_id', $request->worker_id)->update($postData))
                {
                    $this->logFileWrite("Employee recruitment Updated", $request->worker_id);
                    return back()
                        ->with('success', 'Update Successful.');
                }
                else
                {
                    return back()
                        ->withInput()->with('error', 'Update Error: Please try again.');
                }
            }
            else
            {
                #-------------------Save--------------------
                if (Worker::insert($postData))
                {
                    $id = Worker::all()->last()->worker_id;
                    $this->logFileWrite("Employee recruitment Saved", $id );
                    return back()
                        ->with('success', 'Employee recruitment Save Successful.');
                }
                else
                {
                    return back()
                        ->withInput()->with('error', 'Insert Error: Please try again.');
                }

            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
        
    }
    public function recruitEdit(Request $request)
    {
        // ACL::check(["permission" => "hr_recruitment_worker"]);
        #-----------------------------------------------------------#
        $validator = Validator::make($request->all(), [
            'worker_name'           => 'required|max:128',
            'worker_doj'            => 'required|date',
            'worker_emp_type_id'    => 'required',
            'worker_designation_id' => 'required',
            'worker_unit_id'        => 'required',
            'worker_area_id'        => 'required',
            'worker_department_id'  => 'required',
            'worker_section_id'     => 'max:11',
            'worker_subsection_id'  => 'max:11',
            'worker_gender'         => 'required|max:15',
            'worker_dob'            => 'date',
            'worker_contact'        => 'required',
            'worker_ot'             => 'required|max:1'
            
        ]);

        if ($validator->fails())
        {
            return back()
                    ->withErrors($validator)
                    ->withInput();
        }
        try {
            #-------------------Post Data------------------
            $postData = array(
                "worker_name"           => strtoupper($request->worker_name),
                "worker_doj"            => (!empty($request->worker_doj)?date('Y-m-d',strtotime($request->worker_doj)):null),
                "worker_emp_type_id" => $request->worker_emp_type_id,
                "worker_designation_id" => $request->worker_designation_id,
                "worker_unit_id" => $request->worker_unit_id,
                "worker_area_id" => $request->worker_area_id,
                "worker_department_id" => $request->worker_department_id,
                "worker_section_id" => $request->worker_section_id,
                "worker_subsection_id" => $request->worker_subsection_id,
                "worker_ot" => $request->worker_ot,
                "worker_gender" => $request->worker_gender,
                "worker_dob" => (!empty($request->worker_dob)?date('Y-m-d',strtotime($request->worker_dob)):null),
                "worker_contact" => $request->worker_contact,
                "worker_nid" => $request->worker_nid,
                "as_oracle_code"   => $request->as_oracle_code,
                "as_rfid"   => $request->as_rfid
            );

            #-------------------Update------------------
            if (!empty($request->worker_id))
            {
                if (Worker::where('worker_id', $request->worker_id)->update($postData))
                {
                    $this->logFileWrite("Employee recruitment Updated", $request->worker_id);
                    return back()
                        ->with('success', 'Update Successful.');
                }
                else
                {
                    return back()
                        ->withInput()->with('error', 'Update Error: Please try again.');
                }
            }
            return back()->with('error', "Something wrong!, Please Page reload and try again");
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
        
    }


    public function recruitList()
    {
        $employeeTypes  = EmpType::where('hr_emp_type_status', '1')->pluck('hr_emp_type_name');
        $unitList  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_short_name');
        $areaList  = Area::where('hr_area_status', '1')->pluck('hr_area_name');
        $departmentList= Department::where('hr_department_status',1)->pluck('hr_department_name');
        $designationList  = Designation::where('hr_designation_status', '1')->pluck('hr_designation_name');
        $sectionList  = Section::where('hr_section_status', '1')->pluck('hr_section_name');
        $subsectionList  = Subsection::where('hr_subsec_status', '1')->pluck('hr_subsec_name');

        return view('hr.recruitment.worker_recruit_list', compact('employeeTypes','unitList','areaList','departmentList','designationList','sectionList','subsectionList'));
    }


    public function recruitData()
    {
        DB::statement(DB::raw('set @serial_no=0'));
        $data = DB::table('hr_worker_recruitment AS w')
            ->select(
                DB::raw('@serial_no := @serial_no + 1 AS serial_no'),
                'w.worker_id',
                'w.worker_name',
                'w.worker_doj',
                'e.hr_emp_type_name',
                'u.hr_unit_short_name',
                'a.hr_area_name',
                'dp.hr_department_name',
                'dg.hr_designation_name',
                's.hr_section_name',
                'ss.hr_subsec_name',
                'w.worker_gender',
                'w.worker_dob',
                'w.worker_ot',
                'w.worker_contact',
                'w.worker_nid',
                'w.as_oracle_code',
                'w.as_rfid',
                'w.worker_unit_id'
            )
            ->leftJoin('hr_area AS a', 'a.hr_area_id', '=', 'w.worker_area_id')
            ->leftJoin('hr_emp_type AS e', 'e.emp_type_id', '=', 'w.worker_emp_type_id')
            ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'w.worker_unit_id')
            ->leftJoin('hr_department AS dp', 'dp.hr_department_id', '=', 'w.worker_department_id')
            ->leftJoin('hr_designation AS dg', 'dg.hr_designation_id', '=', 'w.worker_designation_id')
            ->leftJoin('hr_section AS s', 's.hr_section_id', '=', 'w.worker_section_id')
            ->leftJoin('hr_subsection AS ss', 'ss.hr_subsec_id', '=', 'w.worker_subsection_id')
            // ->whereIn('w.worker_unit_id', auth()->user()->unit_permissions())
            ->where('w.worker_doctor_acceptance', '0')
            ->where('w.worker_is_migrated', '0')
            ->orderBy('w.worker_id','desc')
            ->get();

        return Datatables::of($data)
            ->editColumn('worker_ot', function($data){
                return (($data->worker_ot==1)?"OT":"Non OT");
            })
            ->editColumn('action', function ($data) {
                $medicalIcon = '<b style="color:red">Unit Null</p>';
                if($data->worker_unit_id != null || $data->worker_unit_id != ''){
                    $medicalIcon = "<a href=".url('hr/recruitment/worker/medical_edit/'.$data->worker_id)." class=\"btn btn-xs btn-warning\" data-toggle=\"tooltip\" title=\"Medical Entry\"> <i class=\"ace-icon fa fa-user-md bigger-120\"></i> </a>";
                }
                return "<div class=\"btn-group col-sm-2\" style=\"padding-left: 0px;padding-right: 0px;width: 80px;\">

                    <a href=".url('hr/recruitment/worker/recruit_edit/'.$data->worker_id)." class=\"btn btn-xs btn-primary\" data-toggle=\"tooltip\" title=\"Edit Information\">
                        <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                    </a>".$medicalIcon."</div>";
            })
            ->rawColumns([
                'serial_no',
                'action'
            ])
            ->make(true);
    }


    public function recruitEditForm(Request $request)
    {
        try {
            $employee = DB::table('hr_worker_recruitment AS w')
            ->select([
                'w.*',
                'e.hr_emp_type_name',
                'u.hr_unit_short_name',
                'a.hr_area_name',
                'dp.hr_department_name',
                'dg.hr_designation_name',
                's.hr_section_name',
                'ss.hr_subsec_name'
            ])
            ->leftJoin('hr_area AS a', 'a.hr_area_id', '=', 'w.worker_area_id')
            ->leftJoin('hr_emp_type AS e', 'e.emp_type_id', '=', 'w.worker_emp_type_id')
            ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'w.worker_unit_id')
            ->leftJoin('hr_department AS dp', 'dp.hr_department_id', '=', 'w.worker_department_id')
            ->leftJoin('hr_designation AS dg', 'dg.hr_designation_id', '=', 'w.worker_designation_id')
            ->leftJoin('hr_section AS s', 's.hr_section_id', '=', 'w.worker_section_id')
            ->leftJoin('hr_subsection AS ss', 'ss.hr_subsec_id', '=', 'w.worker_subsection_id')
            ->where('w.worker_id', $request->worker_id)
            ->where('w.worker_is_migrated', '0')
            ->first();
            

            $employeeTypes  = EmpType::where('hr_emp_type_status', '1')->pluck('hr_emp_type_name', 'emp_type_id');
            $unitList  = Unit::where('hr_unit_status', '1')
                ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
                ->pluck('hr_unit_short_name', 'hr_unit_id');
            $areaList  = Area::where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');

            $user_unit= Auth()->user()->unit_id();
            DB::statement(DB::raw('set @sl=0'));
            $recruitList= Worker::whereIn('worker_unit_id', auth()->user()->unit_permissions())
            ->where('worker_doctor_acceptance', '0')
            ->where('worker_is_migrated', '0')
            ->orderBy('worker_doj', "DESC")
            ->select([
                DB::raw('@sl := @sl + 1 AS sl'),
                'worker_id',
                'worker_name',
                'worker_doj'
            ])
            ->get();

            return view('hr/recruitment/worker_recruit_edit', compact('employee', 'employeeTypes', 'unitList', 'areaList', 'recruitList'));

        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return back()->with('error', $bug);
        }

    }


    /*
    *----------------------------------------------
    * Worker Medical
    *----------------------------------------------
    */

    public function editMedical(Request $request)
    {
        $employee = DB::table('hr_worker_recruitment AS w')
            ->select('*')
            ->where('w.worker_id', $request->worker_id)
            ->first();

        $user_unit = auth()->user()->unit_id();
        DB::statement(DB::raw('set @sl=0'));
        $medicalList= Worker::whereIn('worker_unit_id', auth()->user()->unit_permissions())
                ->where('worker_is_migrated', '0')
                ->orderBy('worker_doj', "DESC")
                ->select([
                    DB::raw('@sl := @sl + 1 AS sl'),
                    'worker_id',
                    'worker_name',
                    'worker_doj'
                ])
                ->get();

        return view('hr/recruitment/worker_medical_edit', compact('employee', 'medicalList'));
    }

    public function medicalStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'worker_id'             => 'required',
            'worker_name'           => 'required|max:128',
            'worker_doj'            => 'required|date',
            'worker_height'         => 'required|max:10',
            'worker_weight'         => 'required|max:10',
            'worker_tooth_structure' => 'max:64',
            'worker_blood_group'     => 'required|max:10',
            'worker_identification_mark' => 'max:255',
            'worker_doctor_age_confirm' => 'required|max:20',
            'worker_doctor_comments'    => 'required|max:255',
            'worker_doctor_signature'   => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'worker_doctor_acceptance'  => 'required|max:1',
        ]);

        if ($validator->fails())
        {
            return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Please fillup all required fields!');
        }
        else
        {

            $worker_doctor_signature = null;
            if($request->hasFile('worker_doctor_signature'))
            {
                $file = $request->file('worker_doctor_signature');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $directory = 'assets/images/employee/med_info/'.date("Y").'/'.date("m").'/'.date("d").'/';
                //If the directory doesn't already exists.
                if(!is_dir($directory)){
                    //Create our directory.
                    mkdir($directory, 755, true);
                }
                $worker_doctor_signature = '/'.$directory . $filename;
                Image::make($file)->save(public_path( $worker_doctor_signature ) );
            }
            else
            {
                $worker_doctor_signature = $request->old_signature;
            }

            #-------------------Post Data------------------
            $postData = array(
                "worker_height"            => $request->worker_height,
                "worker_weight"            => $request->worker_weight,
                "worker_tooth_structure"   => $request->worker_tooth_structure,
                "worker_blood_group"       => $request->worker_blood_group,
                "worker_identification_mark" => $request->worker_identification_mark,
                "worker_doctor_age_confirm"  => $request->worker_doctor_age_confirm,
                "worker_doctor_comments"   => $request->worker_doctor_comments,
                "worker_doctor_signature"  => $worker_doctor_signature,
                "worker_doctor_acceptance" => $request->worker_doctor_acceptance
            );

            #-------------------Update------------------
            if (!empty($request->worker_id))
            {
                if (Worker::where('worker_id', $request->worker_id)->update($postData))
                {
                    $this->logFileWrite("Worker medical entry Updated", $request->worker_id);
                    return back()
                        ->with('success', 'Update Successful.');
                }
                else
                {
                    return back()
                        ->withInput()->with('error', 'Please try again.');
                }
            }
            else
            {
                return back()
                    ->withInput()->with('error', 'Please try again.');
            }
        }
    }

    public function showMedicalList()
    {
        return view('hr/recruitment/worker_medical_list');
    }

    public function medicalData()
    {
        DB::statement(DB::raw('set @serial_no=0'));
        $data = DB::table('hr_worker_recruitment AS w')
            ->select(
                DB::raw('@serial_no := @serial_no + 1 AS serial_no'),
                'w.worker_id',
                'w.worker_name',
                'w.worker_doj',
                'w.worker_height',
                'w.worker_weight',
                'w.worker_tooth_structure',
                'w.worker_blood_group',
                'w.worker_identification_mark',
                'w.worker_doctor_age_confirm',
                'w.worker_doctor_comments',
                'w.worker_doctor_signature',
                'w.worker_doctor_acceptance',
                'w.as_rfid',
                'w.as_oracle_code'
            )
            ->whereIn('w.worker_doctor_acceptance', [1,2])
            ->where('w.worker_is_migrated', '0')
            ->whereIn('w.worker_unit_id', auth()->user()->unit_permissions())
            ->orderBy('w.worker_id','desc')
            ->get();


        return Datatables::of($data)
            ->editColumn('worker_doctor_acceptance', function ($data) {
                if ($data->worker_doctor_acceptance==1)
                {
                    return "<span class='label label-success'>Yes</span>";
                }
                else
                {
                    return "<span class='label label-danger'>No</span>";
                }
            })
            ->editColumn('height_weight', function ($data) {
                return "<strong>Height: </strong> $data->worker_height<br/><strong>Weight: </strong> $data->worker_weight";
            })
            ->editColumn('action', function ($data) {

                $button = "<div class=\"btn-group col-sm-2\" style=\"padding-left: 0px;padding-right: 0px;width: 100px;\">
                        <a href=".url('hr/recruitment/worker/medical_edit/'.$data->worker_id)." class=\"btn btn-xs btn-primary\" data-toggle=\"tooltip\" title=\"Edit Information\">
                            <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                        </a>";

                if ($data->worker_doctor_acceptance==1 && !auth()->user()->hasRole("hr_medical"))
                {
                    $button .= "<a href=".url('hr/recruitment/worker/ie_skill_edit/'.$data->worker_id)." class=\"btn btn-xs btn-warning\" data-toggle=\"tooltip\" title=\"IE Skill Test\">
                            <i class=\"ace-icon fa fa-cogs bigger-120\"></i>
                        </a>
                        <a href=".url("hr/recruitment/worker/migrate/". $data->worker_id)." onclick=\"return confirm('Are you sure?')\" class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Migrate Now!\">
                            <i class=\"ace-icon fa fa-plus bigger-120\"></i>
                        </a>";
                }

                $button .= "</div>";

                return $button;
            })
            ->rawColumns([
                'serial_no',
                'height_weight',
                'worker_doctor_acceptance',
                'action'
            ])
            ->make(true);
    }

    /*
    *----------------------------------------------
    * Worker IE Skill Test
    *----------------------------------------------
    */

    public function editIeSkill(Request $request)
    {
        // ACL::check(["permission" => "hr_recruitment_worker"]);
        #-----------------------------------------------------------#
        $employee = Worker::where('worker_id', $request->worker_id)
            ->where('worker_doctor_acceptance', 1)
            ->first();

        $user_unit= Auth()->user()->unit_id();
        DB::statement(DB::raw('set @sl=0'));
        $ieList= Worker::whereIn('worker_unit_id', auth()->user()->unit_permissions())
                            ->where('worker_is_migrated', '0')
                            ->where('worker_doctor_acceptance', 1)
                            ->orderBy('worker_doj', "DESC")
                            ->select([
                                DB::raw('@sl := @sl + 1 AS sl'),
                                'worker_id',
                                'worker_name',
                                'worker_doj'
                            ])
                            ->get();

        return view('hr/recruitment/worker_ie_skill_edit', compact('employee','ieList'));
    }

    public function ieSkillStore(Request $request)
    {
        // ACL::check(["permission" => "hr_recruitment_worker"]);
        #-----------------------------------------------------------#

        $validator = Validator::make($request->all(), [
            'worker_id'   => 'required',
            'worker_name' => 'required|max:255',
            'worker_doj'  => 'required|date',
            'worker_pigboard_test' => 'required|min:1',
            'worker_finger_test'   => 'required|min:1',
            'worker_color_join'    => 'required|min:1',
            'worker_color_band_join' => 'required|min:1',
            'worker_color_top_stice' => 'required|min:1',
            'worker_urmol_join'    => 'required|min:1',
            'worker_clip_join'     => 'required|min:1',
            'worker_salary'        => 'required'
        ]);

        if ($validator->fails())
        {
            return back()
                ->withInput()
                ->withErrors();
        }
        try {
            $postData = array(
                'worker_pigboard_test' => $request->worker_pigboard_test,
                'worker_finger_test'   => $request->worker_finger_test,
                'worker_color_join'    => $request->worker_color_join,
                'worker_color_band_join' => $request->worker_color_band_join,
                'worker_box_pleat_join'  => $request->worker_box_pleat_join,
                'worker_color_top_stice' => $request->worker_color_top_stice,
                'worker_urmol_join'    => $request->worker_urmol_join,
                'worker_clip_join'     => $request->worker_clip_join,
                'worker_salary'        => $request->worker_salary
            );

            Worker::where('worker_id', $request->worker_id)->update($postData);

            $this->logFileWrite("Worker Skill Updated", $request->worker_id);
            return back()
                    ->with("success", "Update Successful.");
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return back()->with('error', $bug);
        }

    }

    public function showIeSkillList()
    {
        // ACL::check(["permission" => "hr_recruitment_worker"]);
        #-----------------------------------------------------------#
        return view('hr/recruitment/worker_ie_skill_list');
    }

    public function getIeSkillData()
    {
        // ACL::check(["permission" => "hr_recruitment_worker"]);
        #-----------------------------------------------------------#

        DB::statement(DB::raw('set @serial_no=0'));
        $data = DB::table('hr_worker_recruitment AS w')
            ->select(
                DB::raw('@serial_no := @serial_no + 1 AS serial_no'),
                'w.worker_id',
                'w.worker_name',
                'w.worker_doj',
                DB::raw('CASE WHEN w.worker_pigboard_test=1 THEN "Yes" ELSE "No" END AS worker_pigboard_test'),
                DB::raw('CASE WHEN w.worker_finger_test=1 THEN "Yes" ELSE "No" END AS worker_finger_test'),
                DB::raw('CASE WHEN w.worker_color_join=1 THEN "Yes" ELSE "No" END AS worker_color_join'),
                DB::raw('CASE WHEN w.worker_color_band_join=1 THEN "Yes" ELSE "No" END AS worker_color_band_join'),
                DB::raw('CASE WHEN w.worker_box_pleat_join=1 THEN "Yes" ELSE "No" END AS worker_box_pleat_join'),
                DB::raw('CASE WHEN w.worker_color_top_stice=1 THEN "Yes" ELSE "No" END AS worker_color_top_stice'),
                DB::raw('CASE WHEN w.worker_urmol_join=1 THEN "Yes" ELSE "No" END AS worker_urmol_join'),
                DB::raw('CASE WHEN w.worker_clip_join=1 THEN "Yes" ELSE "No" END AS worker_clip_join'),
                'w.worker_salary'
            )
            ->where('w.worker_doctor_acceptance', '1')
            ->where('w.worker_is_migrated', '0')
            ->whereIn('w.worker_unit_id', auth()->user()->unit_permissions())
            ->orderBy('w.worker_id','desc')
            ->get();

        return Datatables::of($data)
            ->editColumn('action', function ($data) {
                return "<div class=\"btn-group col-sm-2\" style=\" width:80px;\">
                    <a href=".url('hr/recruitment/worker/ie_skill_edit/'.$data->worker_id)." class=\"btn btn-xs btn-primary\" data-toggle=\"tooltip\" title=\"Edit IE Skill\">
                        <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                    </a>
                    <a href=".url('hr/recruitment/worker/migrate/'. $data->worker_id)." onclick=\"return confirm('Are you sure?')\" class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Migrate Now!\">
                        <i class=\"ace-icon fa fa-plus bigger-120\"></i>
                    </a>
                </div>";
            })
            ->rawColumns([
                'serial_no',
                'action'
            ])
            ->make(true);
    }


    /*
    *----------------------------------------------
    * Migrate
    *----------------------------------------------
    */
    


}
