<?php

namespace App\Http\Controllers\Hr\Recruitment;

use App\Helpers\Custom;
use App\Helpers\EmployeeHelper;
use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\Department;
use App\Models\Hr\Designation;
use App\Models\Hr\DesignationUpdateLog;
use App\Models\Hr\EducationLevel;
use App\Models\Hr\EmpType;
use App\Models\Employee;
use App\Models\Hr\EmployeeBengali;
use App\Models\Hr\Floor;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Line;
use App\Models\Hr\LoanApplication;
use App\Models\Hr\Location;
use App\Models\Hr\Nominee;
use App\Models\Hr\Section;
use App\Models\Hr\Shift;
use App\Models\Hr\ShiftRoaster;
use App\Models\Hr\Station;
use App\Models\Hr\Subsection;
use App\Models\Hr\Unit;
use App\Models\Hr\promotion;
use Auth, DB, Validator, Image, Session, ACL, PDF, Response, Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class EmployeeBanglaController extends Controller
{
   

    public function index()
    {
        // dd('ddd');
        $reportCount = employee_count();

        $employeeTypes = EmpType::where('hr_emp_type_status', '1')->distinct()->orderBy('hr_emp_type_name', 'ASC')->pluck('hr_emp_type_name');
        $empTypes = EmpType::where('hr_emp_type_status', '1')
                            ->pluck('hr_emp_type_name', 'emp_type_id');
        $unitList  = Unit::where('hr_unit_status', '1')
            ->distinct()
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->orderBy('hr_unit_name_bn', 'ASC')
            ->pluck('hr_unit_name_bn');
        $allUnit= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())
                        ->pluck('hr_unit_name', 'hr_unit_id');
        $floorList = Floor::where('hr_floor_status', '1')->distinct()->orderBy('hr_floor_name', 'ASC')->pluck('hr_floor_name');
        $lineList  = Line::where('hr_line_status', '1')->distinct()->orderBy('hr_line_name', 'ASC')->pluck('hr_line_name');
        $shiftList  = Shift::where('hr_shift_status', '1')->distinct()->orderBy('hr_shift_name', 'ASC')->pluck('hr_shift_name');
        $areaList  = Area::where('hr_area_status', '1')->distinct()->orderBy('hr_area_name', 'ASC')->pluck('hr_area_name');
        $departmentList  = Department::where('hr_department_status', '1')->distinct()->orderBy('hr_department_name', 'ASC')->pluck('hr_department_name');
        $designationList  = Designation::where('hr_designation_status', '1')->distinct()->orderBy('hr_designation_name', 'ASC')->pluck('hr_designation_name');
        $sectionList  = Section::where('hr_section_status', '1')->distinct()->orderBy('hr_section_name', 'ASC')->pluck('hr_section_name');
        $subSectionList  = Subsection::where('hr_subsec_status', '1')->distinct()->orderBy('hr_subsec_name', 'ASC')->pluck('hr_subsec_name');
        $educationList  = EducationLevel::pluck('education_level_title');

        return view('hr.recruitment.employee_list_bangla', compact(
            'reportCount',
            'employeeTypes',
            'unitList',
            'floorList',
            'lineList',
            'shiftList',
            'areaList',
            'departmentList',
            'designationList',
            'sectionList',
            'subSectionList',
            'educationList',
            "allUnit",
            "empTypes"
        ));
    }
    
   


    public function getData(Request $request)
    {

        
        $data = DB::table('hr_as_basic_info AS b')
            ->select([
                DB::raw('b.as_id AS serial_no'),
                'b.associate_id',
                'bn.hr_bn_associate_name',
                'e.hr_emp_type_name AS hr_emp_type_name',
                'u.hr_unit_name_bn',
                'f.hr_floor_name_bn',
                'l.hr_line_name_bn',
                'lc.hr_location_name',
                'dp.hr_department_name_bn',
                'dg.hr_designation_name_bn',
                'dg.hr_designation_position',
                'dg.hr_designation_grade',
                'b.as_gender',
                'b.as_ot',
                'b.as_doj',
                'b.as_dob',
                'b.as_status',
                'b.as_oracle_code',
                'b.as_rfid_code',
                'sec.hr_section_name_bn',
                'subsec.hr_subsec_name_bn',
                'b.as_shift_id',
                'ben.ben_current_salary',
                'adv.emp_adv_info_per_vill',
                'adv.emp_adv_info_per_po',
                'adv.emp_adv_info_pres_house_no',
                'adv.emp_adv_info_pres_road',
                'adv.emp_adv_info_pres_po',
                'adv.emp_adv_info_per_dist',
                'adv.emp_adv_info_per_upz',
                'adv.emp_adv_info_spouse',
                'bn.hr_bn_mother_name',
                'bn.hr_bn_spouse_name',
                'bn.hr_bn_permanent_village',
                'bn.hr_bn_permanent_po',
                'bn.hr_bn_present_road',
                'bn.hr_bn_present_house',
                'bn.hr_bn_present_po',
                'b.as_name',
                'bn.hr_bn_father_name',
                'b.as_contact',
                'ben.bank_no'
            ])
            ->leftJoin('hr_area AS a', 'a.hr_area_id', '=', 'b.as_area_id')
            ->leftJoin('hr_employee_bengali AS bn', 'bn.hr_bn_associate_id', '=', 'b.associate_id')
            ->leftJoin('hr_emp_type AS e', 'e.emp_type_id', '=', 'b.as_emp_type_id')
            ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'b.as_unit_id')
            ->leftJoin('hr_floor AS f', 'f.hr_floor_id', '=', 'b.as_floor_id')
            ->leftJoin('hr_line AS l', 'l.hr_line_id', '=', 'b.as_line_id')
            ->leftJoin('hr_location AS lc', 'lc.hr_location_id', '=', 'b.as_location')
            ->leftJoin('hr_department AS dp', 'dp.hr_department_id', '=', 'b.as_department_id')
            ->leftJoin('hr_designation AS dg', 'dg.hr_designation_id', '=', 'b.as_designation_id')
            ->leftJoin('hr_section AS sec', 'sec.hr_section_id', '=', 'b.as_section_id')
            ->leftJoin('hr_subsection AS subsec', 'subsec.hr_subsec_id', '=', 'b.as_subsection_id')
            ->leftJoin('hr_benefits AS ben', 'b.associate_id', '=', 'ben.ben_as_id')
            ->leftJoin('hr_as_adv_info AS adv', 'b.associate_id', '=', 'adv.emp_adv_info_as_id')
            ->whereIn('b.as_location', auth()->user()->location_permissions())
            ->where(function ($query) use ($request) {
                if($request->otnonot != null){
                    $query->where('b.as_ot', '=', $request->otnonot);
                }
                if($request->emp_type != ""){
                    $query->where('b.as_emp_type_id', '=', $request->emp_type);
                }
                if($request->as_status == 6){
                    $query->where('b.as_status', '=', $request->as_status);
                }else{
                    $query->where('b.as_status', '=', 1); 
                }
                if($request->doj_from != ""){
                    $query->where('b.as_doj', '>=', $request->doj_from);
                }
                if($request->doj_to != ""){
                    $query->where('b.as_doj', '<=', $request->doj_to);
                }
                if($request->unit != ""){
                    $query->where('b.as_unit_id', '=', $request->unit);
                }
            })
            ->whereNotIn('as_id', auth()->user()->management_permissions())
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->orderBy('ben.ben_current_salary','DESC')
            ->get();
// dd($data);
        $perm = false;
        if(auth()->user()->can('Manage Employee') || auth()->user()->hasRole('Super Admin')){
            $perm = true; 
        }

        $sal = check_permission('Salary Sheet') || check_permission('Salary Report');
        // education
        $educationDegree = DB::table('hr_education_degree_title')->pluck('education_degree_title', 'id');
        $educations = DB::table('hr_education AS e')
        ->select(DB::raw('t.*'))
        ->from(DB::raw('(SELECT * FROM hr_education ORDER BY id DESC) t'))
        ->groupBy('t.education_as_id')
        ->pluck('education_degree_id_1', 'education_as_id');
        // dis
        $getDist = DB::table('hr_dist')
        ->pluck('dis_name', 'dis_id');
        // dis
        $getUpa = DB::table('hr_upazilla')
        ->pluck('upa_name', 'upa_id');

        return Datatables::of($data)
            ->addIndexColumn()
            ->editColumn('as_ot', function($user){
                if($user->as_ot==1){
                    $ot_id2="OT";
                }
                else{
                    $ot_id2="Non OT";
                }
                return ($ot_id2);
            })
            ->editColumn('hr_designation_grade', function($user){
                if($user->hr_designation_grade > 0 ){
                    return $user->hr_designation_grade;
                }
                else{
                    return '';
                }
                
            })
            ->addColumn('age', function($user){
                // if($user->as_dob){
                //     return Carbon::parse($user->as_dob)->age;
                // }
                // else{
                //     return '';
                // }
                return date('Y-m-d', strtotime($user->as_dob));
                
            })
            ->addColumn('education', function($user) use ($educations, $educationDegree){
                $edu = $educations[$user->associate_id]??'';
                if($edu != ''){
                    $educationDeg = $educationDegree[$edu]??'';
                }else{
                    $educationDeg = '';
                }
                return $educationDeg;
                
            })
            ->addColumn('salary', function($user) use ($sal){
                if($sal){
                    $salary = $user->ben_current_salary;
                }else{
                    $salary = '';
                }
                return $salary;
                
            })
            ->addColumn('permanent_address', function($user){
                return $user->emp_adv_info_per_vill.', '.$user->emp_adv_info_per_po;
                
            })
            ->addColumn('permanent_dist', function($user) use ($getDist){
                return $getDist[$user->emp_adv_info_per_dist]??'';
                
            })
            ->addColumn('permanent_upazila', function($user) use ($getUpa){
                return $getUpa[$user->emp_adv_info_per_upz]??'';
                
            })
            ->addColumn('present_address', function($user){
                return $user->emp_adv_info_pres_house_no.', '.$user->emp_adv_info_pres_road.', '.$user->emp_adv_info_pres_po;
                
            })
            ->editColumn('action', function ($user) use($perm) {

                  $return = "<a href=></a> ";

                // $return = "<a href=".url('hr/recruitment/employee/show/'.$user->associate_id)." class=\"btn btn-sm btn-success\" data-toggle='tooltip' data-placement='top' title='' data-original-title='View Employee Profile'>
                //         <i class=\"ace-icon fa fa-eye bigger-120\"></i>
                //     </a> ";
                    // if($perm){

                    //     $return .= "<a href=".url('hr/recruitment/employee/edit/'.$user->associate_id)." class=\"btn btn-sm btn-primary\" data-toggle=\"tooltip\" title=\"Edit\" style=\"margin-top:1px;\">
                    //         <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                    //     </a>";
                    // }
                return $return;
            })
            ->rawColumns([
                
                'as_status',
                'age',
                'action',
                'as_ot',
                'education',
                'salary',
                'present_address',
                'permanent_address',
                'permanent_dist',
                'permanent_upazila'
            ])
            ->make(true);
    }

    //get select data by unit
    public function getDropdownData(Request $request)
    {
        // dd($request->all());
        $employeeTypes = EmpType::where('hr_emp_type_status', '1')->distinct()->orderBy('hr_emp_type_name', 'ASC')->pluck('hr_emp_type_name');
        $unitList  = Unit::where('hr_unit_status', '1')
            ->distinct()
            ->where('hr_unit_id',$request->unit)
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->orderBy('hr_unit_short_name', 'ASC')
            ->pluck('hr_unit_short_name');

        $floorList = Floor::where('hr_floor_status', '1')->distinct()->where('hr_floor_unit_id',$request->unit)->orderBy('hr_floor_name', 'ASC')->pluck('hr_floor_name');
        $lineList  = Line::where('hr_line_status', '1')->distinct()->where('hr_line_unit_id',$request->unit)->orderBy('hr_line_name', 'ASC')->pluck('hr_line_name');
        $departmentList  = Department::where('hr_department_status', '1')->distinct()->orderBy('hr_department_name', 'ASC')->pluck('hr_department_name');
        
        return Response::json(array(
            'unitList' => $unitList,
            'floorList' => $floorList,
            'lineList' => $lineList
            
        ));

    }
    
    
}
