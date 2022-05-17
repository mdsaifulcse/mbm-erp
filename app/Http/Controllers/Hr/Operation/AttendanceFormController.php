<?php

namespace App\Http\Controllers\hr\Operation;

use App\Http\Controllers\Controller;
use App\Models\Hr\Benefits;
use App\Repository\Hr\AttendanceProcessRepository;
use App\Repository\Hr\EmployeeRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Contracts\Hr\EmployeeInterface;
use App\Exports\Hr\DailyReportExport;
use App\Models\Employee;
use App\Models\Hr\Absent;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Location;
use App\Models\Hr\Unit;
use Box\Spout\Writer\Style\StyleBuilder;
use DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Maatwebsite\Excel\Facades\Excel;


class AttendanceFormController extends Controller
{
    protected $employee;
    public function __construct(EmployeeRepository $employee){
        $this->employee = $employee;
    }


    public function index(){

       $data['unitList']  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->orderBy('hr_unit_name', 'desc')
            ->pluck('hr_unit_name', 'hr_unit_id');

        $data['locationList']  = Location::where('hr_location_status', '1')
            ->whereIn('hr_location_id', auth()->user()->location_permissions())
            ->orderBy('hr_location_name', 'desc')
            ->pluck('hr_location_name', 'hr_location_id');

        $data['salaryMin'] = get_salary_min();
        $data['salaryMax'] = get_salary_max();
        $data['areaList']  = DB::table('hr_area')->where('hr_area_status', '1')
            ->pluck('hr_area_name', 'hr_area_id');

        $data['reportType'] =[];



        $data['yearMonth'] = $request->year_month??date('Y-m');
        return view('hr.operation.attendance_form.index', $data);
    }



    public function getFridays($year,$month){
        $date = "$year-$month-01";
        $first_day = date('N',strtotime($date));
        $first_day = 7 - $first_day - 1;
        $last_day =  date('t',strtotime($date));
        $days = array();
        for($i=$first_day; $i<=$last_day; $i=$i+7 ){
            $days[] = $i;
        }
        return  $days;
    }

    public function report(Request $request)
    {

        try {
                
            $input=$request->all();
            // dd($request->as_id);

            $data['unit_name']= DB::table('hr_unit')
                ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
                ->where('hr_unit_id', $request->unit)
                ->pluck('hr_unit_name')
                ->first();
            if ($request->unit==145)
            {
                  $data['unit_name']='MBM+MFW+MBM2';
            } 
            $data['department_by_id']= DB::table('hr_department')
            ->where('hr_department_id', $request->department)
            ->pluck('hr_department_name')
            ->first();

              $data['area_by_id']= DB::table('hr_area')
            ->where('hr_area_id', $request->area)
            ->pluck('hr_area_name')
            ->first();

               $data['section_by_id']= DB::table('hr_section')
            ->where('hr_section_id',$request->section)
            ->pluck('hr_section_name')
            ->first();
 
            // $hr_line= DB::table('hr_line')
            // ->where('hr_line_id', $request->line_id)
            // ->pluck('hr_line_name')
            // ->first();

            // $hr_floor= DB::table('hr_floor')
            // ->where('hr_floor_id', $request->floor_id)
            // ->pluck('hr_floor_name')
            // ->first();

              $data['subsection_by_id']= DB::table('hr_subsection')
            ->where('hr_subsec_id', $request->subSection)
            ->pluck('hr_subsec_name')
            ->first();

            $data['location_name'] = location_by_id()
            ->where('hr_location_id', $request->location)
            ->pluck('hr_location_name', 'hr_location_id')
            ->first();

            $unit_id=$request->unit;
            if ($request->unit==145)
            {
                $unit_id='1,4,5';
            }

            $data1= DB::table('hr_as_basic_info')
                ->select('hr_as_basic_info.associate_id','hr_as_basic_info.as_name','hr_as_basic_info.as_doj', 'hr_employee_bengali.hr_bn_associate_name')
                ->whereIn('as_unit_id', auth()->user()->unit_permissions())
                ->whereIn('as_location', auth()->user()->location_permissions())
                ->leftJoin('hr_employee_bengali', 'hr_as_basic_info.associate_id', 'hr_employee_bengali.hr_bn_associate_id')
                ->whereIn('as_status',[1,6] )
                ->when($request->location!='', function($q) use($request) {
                    $q->where('as_location',$request->location);
                    })
                ->when($request->area!='', function($q) use($request) {
                    $q->where('as_area_id',$request->area);
                    })
                ->when($request->section!='', function($q) use($request) {
                    $q->where('as_section_id',$request->section);
                    })
                ->when($request->subsection!='', function($q) use($request) {
                    $q->where('as_subsection_id',$request->subsection);
                    })
                 ->when($request->otnonot!='', function($q) use($request) {
                    $q->where('as_ot',$request->otnonot);
                    })
                 ->when(!empty($request->as_id), function($q) use($request) {

                    $q->whereIn('associate_id',$request->as_id);
                    })
                ->where('as_unit_id', $request->unit)
                ->where('as_department_id', $request->department)
                ->get();

            $total_days_month = date('t', strtotime($request->year_month.'-01'));

            $month_year = $request->year_month;

            
            $data['results'] = $data1;

            $data['total_days_month'] =$total_days_month;
            $data['month_year'] = $month_year;

            return view('hr.operation.attendance_form.report', $data)->render();


        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
            return 'error';
        }
    }
}
