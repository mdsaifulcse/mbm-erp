<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Exports\Hr\SalarySheetExport;
use App\Http\Controllers\Controller;
use App\Models\Hr\Benefits;
use App\Models\Employee;
use App\Repository\Hr\EmployeeRepository;
use App\Repository\Hr\SalaryRepository;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use DB, DataTables;
use Illuminate\Http\Request;

class SalaryReportController extends Controller
{
	protected $salary;
	protected $employee;
	public function __construct(SalaryRepository $salary, EmployeeRepository $employee)
	{
	    ini_set('zlib.output_compression', 1);
	    $this->salary = $salary;
	    $this->employee = $employee;
	}
    public function index(Request $request)
    {
        if(auth()->user()->hasRole('Buyer Mode')){
            return redirect('hrm/reports/monthly-salary');
        }
        $yearMonth = $request->year_month??date('Y-m');
        if(date('d') < 10){
            $yearMonth = date('Y-m', strtotime('-1 month'));
        }
    	$data['yearMonth'] = $yearMonth;
    	$data['months'] = monthly_navbar($data['yearMonth']);
        $data['salaryMax'] = Benefits::getSalaryRangeMax();
    	return view('hr.reports.salary.index', $data);
    }

    public function report(Request $request)
    {
        $getSalary = $this->processSalary($request);
    	$result = $this->salary->getSalaryReport($request, $getSalary);
        if(isset($request->export)){
            $result['salaryAdjust'] = DB::table('hr_salary_adjust_master AS m')
            ->select(DB::raw("concat(IFNULL(d.date, ''),' ',IFNULL(d.comment, '')) as data"),'m.associate_id','d.*')
            ->where('m.month', date('m', strtotime($request->year_month.'-01')))
            ->where('m.year', date('Y', strtotime($request->year_month.'-01')))
            ->leftjoin('hr_salary_adjust_details AS d', 'm.id', 'd.salary_adjust_master_id')
            ->get()
            ->groupBy('associate_id', true)
            ->map(function($q){
                return collect($q)->groupBy('type')
                    ->map(function($p){
                        $s = (object) array();
                        $s->sum = collect($p)->sum('amount');
                        $s->days = implode(',', collect($p)->pluck('data')->toArray());
                        return $s;
                    });
            });
            $filename = 'Salary Report - ';
            $filename .= '.xlsx';
            return Excel::download(new SalarySheetExport($result, 'report'), $filename);
        }
    	return view('hr.reports.salary.report', $result)->render();
    }

    public function processSalary($request)
    {
        $getSalary = $this->salary->getSalaryByMonth($request);
        if(count($getSalary) > 0){
            $getEmployee = collect($this->employee->getEmployeeByAssociateId(['associate_id', 'as_name', 'as_line_id', 'as_floor_id', 'as_oracle_code', 'as_doj', 'as_oracle_sl', 'temp_id', 'as_contact']))->keyBy('associate_id');
            $dataRow = $this->salary->getSalaryByFilter($request, $getSalary, $getEmployee);
            $getSalary = $this->employee->getEmployeeByFilter($request, $dataRow);
        }
        return $getSalary;
    }

    public function salaryDataTable(Request $request)
    {
        $data = $this->processSalary($request);
        $designation = designation_by_id();
        $department = department_by_id();
        $section = section_by_id();
        $subSection = subSection_by_id();
        $line = line_by_id();
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('pic', function($data){
                return 'q';
            })
            ->addColumn('associate_id', function($data) use ($request){
                $month = $request->year_month;
                $jobCard = url("hr/operation/job_card?associate=$data->as_id&month_year=$month");
                return '<a class="job_card" data-name="'.$data->as_name.'" data-associate="'.$data->as_id.'" data-month-year="'.$month.'" data-toggle="tooltip" data-placement="top" title="" data-original-title="Job Card">'.$data->as_id.'</a> <br> '.$data->as_oracle_code;
            })
            ->addColumn('as_name', function($data){
                return $data->as_name.'<br> - '.$data->as_contact;
            })
            ->addColumn('hr_designation_name', function($data) use ($designation){
                return $designation[$data->as_designation_id]['hr_designation_name']??'';
            })
            ->addColumn('hr_department_name', function($data) use ($department){
                return $department[$data->as_department_id]['hr_department_name']??'';
            })
            ->addColumn('hr_section_name', function($data) use ($section){
                return $section[$data->as_section_id]['hr_section_name']??'';
            })
            // ->addColumn('hr_subsection_name', function($data) use ($subSection){
            //     return $subSection[$data->as_subsection_id]['hr_subsec_name']??'';
            // })
            ->addColumn('hr_line_name', function($data) use ($line){
                return $line[$data->as_line_id]['hr_line_name']??'';
            })
            ->addColumn('ot_status', function($data){
                return $data->ot_status == 1?'OT':'Non-OT';
            })
            ->addColumn('ot_hour', function($data){
                return numberToTimeClockFormat($data->ot_hour);
            })
            ->addColumn('total_day', function($data){
                return ($data->present + $data->holiday + $data->leave);
            })
            ->rawColumns(['DT_RowIndex', 'pic', 'associate_id', 'as_name', 'hr_designation_name', 'hr_department_name','hr_line_name', 'ot_status', 'present', 'absent', 'leave', 'holiday', 'ot_hour', 'total_day'])
            ->make(true);
    }

    public function bankSheetReport(Request $request)
    {
        $result['getEmployee'] = $this->processSalary($request);
        $result['input']       = $request->all();
        $result['unit']        = unit_by_id();
        $result['location']    = location_by_id();
        $result['line']        = line_by_id();
        $result['floor']       = floor_by_id();
        $result['department']  = department_by_id();
        $result['designation'] = designation_by_id();
        $result['section']     = section_by_id();
        $result['subSection']  = subSection_by_id();
        $result['area']        = area_by_id();
        $result['routing']     = bank_routing();
        if(isset($request->export)){
            $filename = 'Salary Report - ';
            $filename .= '.xlsx';
            return Excel::download(new SalarySheetExport($result, 'bank-report'), $filename);
        }
        return view('hr.payroll.bank_part.reports', $result)->render();
    }

    public function disburseSheet(Request $request)
    {
        try {
            $getSalary = $this->processSalary($request);
            $getSalary = collect($getSalary)->sortBy('serial');

            $getEmpIds = collect($getSalary)->pluck('as_id');
            $per_page = $request->perpage??6;
            $com['salaryList'] = collect($getSalary)
                            ->groupBy('as_unit_id',true)
                            ->map(function($q) use ($per_page){
                                return collect($q)->groupBy('as_location',true)
                                ->map(function($p) use ($per_page) {
                                    return collect($p)->chunk($per_page);
                                });
                            })
                            ->all();
            $com['sum'] = $this->salary->makeSummarySalary($getSalary);

            // employee bangla name
            $com['empBangla'] = DB::table('hr_employee_bengali')
            ->whereIn('hr_bn_associate_id', $getEmpIds)
            ->pluck('hr_bn_associate_name', 'hr_bn_associate_id');

            // salary adjust
            $com['salaryAddDeduct'] = DB::table('hr_salary_add_deduct')
                ->where('year', date('Y', strtotime($request->year_month)))
                ->where('month', date('m', strtotime($request->year_month)))
                ->get()->keyBy('associate_id')->toArray();

            $firstDateMonth = date('Y-m-d', strtotime($request->year_month.'-01'));
            $lastDateMonth = date('Y-m-t', strtotime($firstDateMonth));

            $com['salaryIncrement'] = DB::table('hr_increment')
                ->select('associate_id','increment_amount')
                ->where('effective_date','>=',$firstDateMonth)
                ->where('effective_date','<=', $lastDateMonth)
                ->get()->keyBy('associate_id')->toArray();

            // salary adjustment 
            $com['salaryAdjust'] = DB::table('hr_salary_adjust_master AS m')
            ->select(DB::raw("concat(IFNULL(d.date, ''),' ',IFNULL(d.comment, '')) as data"),'m.associate_id','d.*')
            ->where('m.month', date('m', strtotime($request->year_month)))
            ->where('m.year', date('Y', strtotime($request->year_month)))
            ->leftjoin('hr_salary_adjust_details AS d', 'm.id', 'd.salary_adjust_master_id')
            ->get()
            ->groupBy('associate_id', true)
            ->map(function($q){
                return collect($q)->groupBy('type')
                    ->map(function($p){
                        $s = (object) array();
                        $s->sum = collect($p)->sum('amount');
                        $s->days = implode(',', collect($p)->pluck('data')->toArray());
                        return $s;
                    });
            });

            $com['input']       = $request->all();
            $com['unit']        = unit_by_id();
            $com['location']    = location_by_id();
            $com['line']        = line_by_id();
            $com['floor']       = floor_by_id();
            $com['department']  = department_by_id();
            $com['designation'] = designation_by_id();
            $com['section']     = section_by_id();
            $com['subSection']  = subSection_by_id();
            $com['area']        = area_by_id();

            if(isset($request->sheet) && $request->sheet == '1'){
                $view = view('hr.operation.salary.payslip', $com)->render();
            }else{
                $view = view('hr.operation.salary.disbursed', $com)->render();
            }
            return response(['view' => $view]);
        } catch (\Exception $e) {
            return $e->getMessage();
            return 'error';
        }
    }

    public function previewSalary(Request $request)
    {
        $employee = get_employee_by_id($request->associate_id);
        $voucher = '';
        $exp = explode("-",$request->salary_date);
        $salary = DB::table('hr_monthly_salary')
            ->where('as_id', $employee->associate_id)
            ->where('year', $exp[0])
            ->where('month', $exp[1])
            ->first();

        if($salary){
            $voucher = view('hr.common.partial_salary_sheet', compact('salary','employee'))->render();
        }else{
            $voucher = '<p class="text-center">No partial salary found!</p>';
        }
        return $voucher;
    }

    public function salaryAudit(Request $request)
    {
        
        $getSalary = $this->processSalary($request);
        dd($getSalary);
        $result = $this->salary->getSalaryReport($request, $getSalary);
        if(isset($request->export)){
            $filename = 'Salary Report - ';
            $filename .= '.xlsx';
            return Excel::download(new SalarySheetExport($result, 'report'), $filename);
        }
        return view('hr.reports.salary.report', $result)->render();
    }
    
    // summery
    public function summeryIndex(Request $request)
    {
        $yearMonth = $request->year_month??date('Y-m');
        if(date('d') < 10){
            $yearMonth = date('Y-m', strtotime('-1 month'));
        }
        $data['yearMonth'] = $yearMonth;
        $data['months'] = monthly_navbar($data['yearMonth']);
        $data['salaryMax'] = Benefits::getSalaryRangeMax();
        return view('hr.reports.salary_summery.index', $data);
    }
    
    public function summeryReport(Request $request)
    {
        $getEmployee = Employee::with('benefits:ben_as_id,ben_current_salary,bank_no,bank_name,ben_cash_amount,ben_bank_amount,ben_tds_amount')
            ->select('associate_id', 'as_name', 'as_status AS emp_status', 'as_status_date', 'as_ot AS ot_status', 'as_gender', 'as_pic', 'as_emp_type_id', 'as_designation_id', 'as_department_id', 'as_section_id', 'as_subsection_id', 'as_oracle_code', 'as_doj', 'as_contact', 'as_line_id', 'as_floor_id', 'temp_id', 'as_oracle_sl', 'as_area_id', 'as_unit_id', 'as_location')
            ->whereIn('as_unit_id', $request->unit)
            ->whereIn('as_location', $request->location)
            ->whereIn('as_status', $request->emp_status)
            ->get();
        
        $getGrade = designation_grade_by_id();
        $getDesignation = designation_by_id();
        
        $getEmployees = collect($getEmployee)->filter(function ($value) { 
            return !is_null($value->benefits); 
        });

        $employees = collect($getEmployees)->map(function($q) use ($getGrade, $getDesignation) {
            if($q->benefits != ''){
                $p = (object)[];
                $designationGrade = $getDesignation[$q->as_designation_id]['grade_id']??'';
                if($designationGrade == ''){
                    $gradeSequence = 0;
                    $gradeName = $getDesignation[$q->as_designation_id]['hr_designation_grade']??'';
                }else{
                    $gradeSequence = $getGrade[$designationGrade]->grade_sequence??0;
                    $gradeName = $getGrade[$designationGrade]->grade_name??'';
                }
                $p->as_id = $q->associate_id;
                $p->as_name = $q->as_name;
                $p->emp_status = $q->emp_status;
                $p->ot_status = $q->ot_status;
                $p->as_emp_type_id = $q->as_emp_type_id;
                $p->as_designation_id = $q->as_designation_id;
                $p->as_department_id = $q->as_department_id;
                $p->as_section_id = $q->as_section_id;
                $p->as_subsection_id = $q->as_subsection_id;
                $p->as_doj = date('Y-m-d', strtotime($q->as_doj));
                $p->as_line_id = $q->as_line_id;
                $p->as_floor_id = $q->as_floor_id;
                $p->as_area_id = $q->as_area_id;
                $p->as_unit_id  = $q->as_unit_id;
                $p->as_location  = $q->as_location;
                $p->serial = $q->as_oracle_sl.$q->temp_id;
                $doj = date('Y-m-d', strtotime($q->as_doj));

                $p->service_length = Carbon::createFromFormat('Y-m-d', $doj)->diff(Carbon::now())->format('%y');
                $p->grade_sequence = $gradeSequence;
                $p->grade_name = $gradeName;
                $p->gross = $q->benefits != ''?$q->benefits->ben_current_salary:0;
                $p->bank_no = $q->benefits != ''?$q->benefits->bank_no:'';
                $p->bank_name = $q->benefits != ''?$q->benefits->bank_name:'';
                $p->pay_type = $q->bank_name;
                $p->cash_payable = $q->benefits->ben_cash_amount??0;
                $p->bank_payable = $q->benefits->ben_bank_amount??0;
                $p->stamp = 10;
                $p->tds = $q->benefits->ben_tds_amount??0;
                $p->partial_amount = 0;
                $p->attendance_bonus = 0;
                $p->food_deduct = 0;
                $p->ot_hour = 0;
                $p->ot_rate = 0;
                $p->total_payable = $q->benefits->ben_current_salary??0;
                return $p;
            }
            
        });
        
        $gemployees = collect($employees)->sortByDesc(function ($d) {
            return $d->grade_sequence.$d->gross;
        })->toArray();

        $getSalary = $this->employee->getEmployeeByFilterAll($request, $gemployees);
        
        $result = $this->salary->getSalaryReport($request, $getSalary);
        if(isset($request->export)){
            $filename = 'Salary Report - ';
            $filename .= '.xlsx';
            return Excel::download(new SalarySheetExport($result, 'summery_report'), $filename);
        }
        return view('hr.reports.salary_summery.report', $result)->render();
        
    }

    // public function summeryReport(Rfp $request)
    // {
    //     $getSalary = $this->salary->getSalaryByMonth($request);
    //     if(count($getSalary) > 0){
    //         $getEmployee = collect($this->employee->getEmployeeByAssociateId(['associate_id', 'as_name', 'as_line_id', 'as_floor_id', 'as_oracle_code', 'as_doj', 'as_oracle_sl', 'temp_id', 'as_contact']))->keyBy('associate_id');
    //         $dataRow = $this->salary->getSalaryByFilter($request, $getSalary, $getEmployee);
            
    //         $dataRow = collect($dataRow)->sortByDesc(function ($salary, $key) {
    //                     return $salary->grade_sequence.$salary->gross;
    //                 })->all();
    //         $getSalary = $this->employee->getEmployeeByFilterAll($request, $dataRow);
    //     }

    //     $result = $this->salary->getSalaryReport($request, $getSalary);
    //     if(isset($request->export)){
    //         $filename = 'Salary Report - ';
    //         $filename .= '.xlsx';
    //         return Excel::download(new SalarySheetExport($result, 'summery_report'), $filename);
    //     }
    //     return view('hr.reports.salary_summery.report', $result)->render();
    // }
}
