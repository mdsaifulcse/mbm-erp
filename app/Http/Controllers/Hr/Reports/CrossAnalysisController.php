<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\Benefits;
use App\Models\Hr\HrMonthlySalary;
use DB;
use Illuminate\Http\Request;

class CrossAnalysisController extends Controller
{
    public function __construct()
    {
        ini_set('zlib.output_compression', 1);
    }

    public function index()
    {
        $yearMonth = $request->year_month??date('Y-m');
        $unitList  = collect(unit_by_id())->pluck('hr_unit_name', 'hr_unit_id');

        $locationList  = collect(location_by_id())->pluck('hr_location_name', 'hr_location_id');

        $areaList  = collect(area_by_id())->pluck('hr_area_name', 'hr_area_id');
        $salaryMin = 0;
        $salaryMax = Benefits::getSalaryRangeMax();
        return view('hr.reports.cross_analysis.index', compact('unitList','areaList', 'salaryMin', 'salaryMax', 'locationList', 'yearMonth'));
    }

    public function report(Request $request)
    {
        $input = $request->all();
        $data['type'] = 'error';
        
        try {
            $data['input'] = $input;
            if(!is_array($input['category_data'])){
                $input['category_data'] = (array)$input['category_data'];
            }
            $type = $input['type'];

            $empquery = Employee::select(DB::raw('DATE_FORMAT(as_doj, "%Y-%m") as joinmonth'), $input['type'], DB::raw("COUNT(*) AS joincount"))
                ->whereIn($input['type'], $input['category_data'])
                ->whereBetween(DB::raw('DATE_FORMAT(as_doj, "%Y-%m")'), [date('Y-m', strtotime($input['month_from'])), date('Y-m', strtotime($input['month_to']))]);
                if($input['otnonot'] != ''){
                    $empquery->where('as_ot', $input['otnonot']);
                }
            $getEmployee = $empquery->orderBy('joinmonth', 'asc')
                    ->groupBy('joinmonth', $input['type'])
                    ->get()
                    ->groupBy($input['type'], true)
                    ->map(function($q){
                        return collect($q)->keyBy('joinmonth');
                    });
            
            $query = HrMonthlySalary::
                select(DB::raw("CONCAT(year,'-',month) AS yearmonth"), 'hr_as_basic_info.'.$input['type'], DB::raw("SUM(gross) AS totalSalary"), DB::raw('COUNT(CASE WHEN emp_status = 1 THEN emp_status END) AS empcount'), DB::raw('COUNT(CASE WHEN emp_status = 2 THEN emp_status END) AS resignemp, COUNT(CASE WHEN emp_status = 5 THEN emp_status END) AS leftemp'))
                ->join('hr_as_basic_info', 'hr_monthly_salary.as_id', 'hr_as_basic_info.associate_id')
                ->whereIn('hr_as_basic_info.'.$input['type'], $input['category_data'])
                ->whereBetween(DB::raw("CONCAT(year,'-',month)"), [date('Y-m', strtotime($input['month_from'])), date('Y-m', strtotime($input['month_to']))]);
                if($input['otnonot'] != ''){
                    $query->where('hr_as_basic_info.as_ot', $input['otnonot']);
                }
            $data['getSalary'] = $query->orderBy('yearmonth', 'asc')
                    ->groupBy('yearmonth', $input['type'])
                    ->get()
                    ->map(function($q, $k) use ($getEmployee, $type){
                        if(isset($getEmployee[$q->{$type}][$q->yearmonth])){
                            $q->joinemp = $getEmployee[$q->{$type}][$q->yearmonth]['joincount'];
                        }else{
                            $q->joinemp = 0;
                        }
                        $q->leftresignemp = $q->resignemp + $q->leftemp;
                        return $q;
                    })
                    ->groupBy($input['type'], true);
                        
            $data['unit'] = unit_by_id();
            $data['location'] = location_by_id();
            $data['department'] = department_by_id();
            $data['section'] = section_by_id();
            $data['subSection'] = subSection_by_id();
            $data['designation'] = designation_by_id();
            $data['type'] = 'success';
            return Response()->json($data);
            // return view('hr.reports.cross_analysis.report', $data);
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();
            return Response()->json($data);
        }
    }

    public function filterReport(Request $request)
    {
        $input = $request->all();

        try {
            $group = $input['report_group'];
            $data['unit'] = unit_by_id();
            $data['location'] = location_by_id();
            $data['department'] = department_by_id();
            $data['section'] = section_by_id();
            $data['subSection'] = subSection_by_id();
            $data['designation'] = designation_by_id();
            // benefit
            $getBenefit = DB::table('hr_benefits')
                ->select('ben_as_id', 'ben_joining_salary')
                ->get()
                ->keyBy('ben_as_id');

            $getEmployee = DB::table('hr_as_basic_info')
                ->select('associate_id', 'as_status', 'as_doj', DB::raw('DATE_FORMAT(as_doj, "%Y-%m") as joinmonth'), $input['report_group'])
                ->whereBetween(DB::raw('DATE_FORMAT(as_doj, "%Y-%m")'), [date('Y-m', strtotime($input['month_from'])), date('Y-m', strtotime($input['month_to']))])
                ->whereIn('as_unit_id', auth()->user()->unit_permissions())
                ->whereIn('as_location', auth()->user()->location_permissions())
                ->whereIn('as_unit_id', $input['unit'])
                ->whereIn('as_location', $input['location'])
                ->where('as_status', '!=', 0)
                ->orderBy('joinmonth', 'asc')
                ->get();

            $getEmployee = collect($getEmployee)->map(function($q) use ($getBenefit){
                $q->joinSalary = $getBenefit[$q->associate_id]->ben_joining_salary??0;
                return $q;
            });
            

            $getSalary = DB::table('hr_monthly_salary')
                ->select('as_id', 'month', 'year', DB::raw("CONCAT(year,'-',month) AS yearmonth"),'ot_status', 'sub_section_id', 'gross', 'emp_status', 'unit_id', 'location_id', 'pay_type', 'designation_id')
                ->whereBetween(DB::raw("CONCAT(year,'-',month)"), [date('Y-m', strtotime($input['month_from'])), date('Y-m', strtotime($input['month_to']))])
                ->whereIn('unit_id', auth()->user()->unit_permissions())
                ->whereIn('location_id', auth()->user()->location_permissions())
                ->whereIn('unit_id', $input['unit'])
                ->whereIn('location_id', $input['location'])
                ->orderBy('yearmonth', 'asc')
                ->get();
            // join
            $empMonthlyJoin = collect($getEmployee)
                ->groupBy($input['report_group'], true)
                ->map(function($q){
                    return collect($q)->groupBy('joinmonth', true)
                    ->map(function($j){
                        $d = (object)[];
                        $d->emp = $j->count();
                        $d->salary = $j->sum('joinSalary');
                        return $d;
                    });
                });

            $getSalaryInfo = collect($getSalary)->map(function($q) use ($data){
                $q->as_section_id = $data['subSection'][$q->sub_section_id]['hr_subsec_section_id']??'';
                $q->as_department_id = $data['subSection'][$q->sub_section_id]['hr_subsec_department_id']??'';
                $q->as_unit_id = $q->unit_id;
                $q->as_location = $q->location_id;
                $q->as_designation_id = $q->designation_id;
                $q->as_subsection_id = $q->sub_section_id;
                return $q;
            });
            // dd($getSalaryInfo[0]);
            $data['getSalaryGroup'] = collect($getSalaryInfo)
                ->groupBy($input['report_group'], true)
                ->map(function($q, $k) use ($empMonthlyJoin, $group){
                    return collect($q)->groupBy('yearmonth', true)
                    ->map(function($j, $m) use ($empMonthlyJoin, $group, $k){
                        $d = (object)[];
                        if(isset($empMonthlyJoin[$k][$m])){
                            $d->joinemp = $empMonthlyJoin[$k][$m]->emp;
                            $d->totalJoinSalary = $empMonthlyJoin[$k][$m]->salary;
                        }else{
                            $d->joinemp = 0;
                            $d->totalJoinSalary = 0;
                        }
                        $d->yearmonth = $m;
                        $d->totalSalary = $j->sum('gross');
                        $d->totalLeftSalary = $j->where('emp_status', 5)->sum('gross');
                        $d->totalResignSalary = $j->where('emp_status', 2)->sum('gross');
                        $d->totalLeftResignSalary = $d->totalLeftSalary + $d->totalResignSalary;
                        $d->empcount = $j->where('emp_status', 1)->count();
                        $d->leftemp = $j->where('emp_status', 5)->count();
                        $d->resignemp = $j->where('emp_status', 2)->count();
                        $d->leftresignemp = $d->leftemp + $d->resignemp;
                        return $d;
                    });
                });
            
            $data['input'] = $input; 
            $data['format'] = $input['report_group'];     
            $data['type'] = 'success';
            return view('hr.reports.cross_analysis.report', $data);
        } catch (\Exception $e) {
            $data['type'] = 'error';
            $data['message'] = $e->getMessage();
            return $data;
        }
    }

    public function filterReport1(Request $request)
    {
        $input = $request->all();
        
        $group = $input['report_group'];
        try {
            $data['input'] = $input;
            $empquery = Employee::select(DB::raw('DATE_FORMAT(as_doj, "%Y-%m") as joinmonth'), $input['report_group'], DB::raw("COUNT(*) AS joincount"))
                ->whereBetween(DB::raw('DATE_FORMAT(as_doj, "%Y-%m")'), [date('Y-m', strtotime($input['month_from'])), date('Y-m', strtotime($input['month_to']))])
                ->whereIn('as_unit_id', auth()->user()->unit_permissions())
                ->whereIn('as_location', auth()->user()->location_permissions());
                
                if(count($input['unit']) > 0){
                    $empquery->whereIn('as_unit_id', $input['unit']);
                }
                if(count($input['location']) > 0){
                    $empquery->whereIn('as_location', $input['location']);
                }
                if($input['area'] != ''){
                    $empquery->where('as_area_id', $input['area']);
                }
                if($input['department'] != ''){
                    $empquery->where('as_department_id', $input['department']);
                }
                if($input['section'] != ''){
                    $empquery->where('as_section_id', $input['section']);
                }
                if($input['subSection'] != ''){
                    $empquery->where('as_subsection_id', $input['subSection']);
                }
                if($input['designation'] != ''){
                    $empquery->where('as_designation_id', $input['designation']);
                }
                if($input['floor_id'] != ''){
                    $empquery->where('as_floor_id', $input['floor_id']);
                }
                if($input['line_id'] != ''){
                    $empquery->where('as_line_id', $input['line_id']);
                }
                if($input['otnonot'] != ''){
                    $empquery->where('as_ot', $input['otnonot']);
                }
            $getEmployee = $empquery->orderBy('joinmonth', 'asc')
                ->groupBy('joinmonth', $input['report_group'])
                ->get()
                ->groupBy($input['report_group'], true)
                ->map(function($q){
                    return collect($q)->keyBy('joinmonth');
                });
            // employee basic sql binding
            $employeeData = DB::table('hr_as_basic_info');
            $employeeData_sql = $employeeData->toSql();
            $getSalary = DB::table('hr_monthly_salary')
                ->select(DB::raw("CONCAT(year,'-',month) AS yearmonth"), 'as_id', 'emp_status', 'sub_section_id', 'unit_id', 'location_id', 'ot_status')
                ->whereIn('unit_id', auth()->user()->unit_permissions())
                ->whereIn('location_id', auth()->user()->location_permissions())
                ->when(count($input['unit']) > 0, function ($query) use ($input) {
                    return $query->whereIn('unit_id', $input['unit']);
                })
                ->when(count($input['location']) > 0, function ($query) use ($input) {
                    return $query->whereIn('location_id', $input['location']);
                })
                ->whereBetween(DB::raw("CONCAT(year,'-',month)"), [date('Y-m', strtotime($input['month_from'])), date('Y-m', strtotime($input['month_to']))])
                ->when($input['otnonot']!=null, function ($query) use ($input) {
                    return $query->where('ot_status', $input['otnonot']);
                })
                ->get();
            dd($getSalary[0]);

            $query = HrMonthlySalary::
                select(DB::raw("CONCAT(year,'-',month) AS yearmonth"), 'emp.'.$input['report_group'], DB::raw("SUM(gross) AS totalSalary"), DB::raw('COUNT(CASE WHEN emp_status = 1 THEN emp_status END) AS empcount'), DB::raw('COUNT(CASE WHEN emp_status = 2 THEN emp_status END) AS resignemp, COUNT(CASE WHEN emp_status = 5 THEN emp_status END) AS leftemp'))
                ->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function ($join) use ($employeeData) {
                    $join->on('hr_monthly_salary.as_id', '=', 'emp.associate_id')->addBinding($employeeData->getBindings());
                })
                ->whereBetween(DB::raw("CONCAT(year,'-',month)"), [date('Y-m', strtotime($input['month_from'])), date('Y-m', strtotime($input['month_to']))]);
                
                if(count($input['unit']) > 0){
                    $query->whereIn('hr_monthly_salary.unit_id', $input['unit']);
                }
                if(count($input['location']) > 0){
                    $query->whereIn('hr_monthly_salary.location_id', $input['location']);
                }
                // if($input['area'] != ''){
                //     $query->where('hr_as_basic_info.as_area_id', $input['area']);
                // }
                // if($input['department'] != ''){
                //     $query->where('hr_as_basic_info.as_department_id', $input['department']);
                // }
                // if($input['section'] != ''){
                //     $query->where('hr_as_basic_info.as_section_id', $input['section']);
                // }
                // if($input['subSection'] != ''){
                //     $query->where('hr_as_basic_info.as_subsection_id', $input['subSection']);
                // }
                // if($input['designation'] != ''){
                //     $query->where('hr_as_basic_info.as_designation_id', $input['designation']);
                // }
                // if($input['floor_id'] != ''){
                //     $query->where('hr_as_basic_info.as_floor_id', $input['floor_id']);
                // }
                // if($input['line_id'] != ''){
                //     $query->where('hr_as_basic_info.as_line_id', $input['line_id']);
                // }
                // if($input['otnonot'] != ''){
                //     $query->where('hr_as_basic_info.as_ot', $input['otnonot']);
                // }
            $data['getSalary'] = $query->orderBy('yearmonth', 'asc')
                    ->groupBy('yearmonth', $input['report_group'])
                    ->get();
            dd($data['getSalary']);
                    $data['getSalary']->map(function($q, $k) use ($getEmployee, $group){
                        if(isset($getEmployee[$q->{$group}][$q->yearmonth])){
                            $q->joinemp = $getEmployee[$q->{$group}][$q->yearmonth]['joincount'];
                        }else{
                            $q->joinemp = 0;
                        }
                        $q->leftresignemp = $q->resignemp + $q->leftemp;
                        return $q;
                    })
                    ->groupBy($input['report_group'], true);
            return $data['getSalary'];       
            $data['unit'] = unit_by_id();
            $data['location'] = location_by_id();
            $data['department'] = department_by_id();
            $data['section'] = section_by_id();
            $data['subSection'] = subSection_by_id();
            $data['designation'] = designation_by_id();
            $data['type'] = 'success';
            return $data;   
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();
            return $data;
        }
    }
}
