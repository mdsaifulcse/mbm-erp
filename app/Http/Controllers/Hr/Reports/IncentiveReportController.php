<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Exports\Hr\IncentiveExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Models\Hr\Benefits;
use App\Repository\Hr\EmployeeRepository;
use DB;
use Illuminate\Http\Request;

class IncentiveReportController extends Controller
{
    protected $employee;
    public function __construct(EmployeeRepository $employee)
    {
        ini_set('zlib.output_compression', 1);
        $this->employee = $employee;
    }
    public function index()
    {
        $data['yearMonth'] = $request->year_month??date('Y-m');
        $data['form_date'] = $request->form_date??date('Y-m-01');
        $data['to_date'] = $request->to_date??date('Y-m-d', strtotime('-1 day'));
        $data['salaryMax'] = Benefits::getSalaryRangeMax();
        return view('hr.reports.incentive.index', $data);
    }

    public function report(Request $request)
    {
        if($request->date_type == 'month'){
            $request->from_date = date('Y-m-01', strtotime($request->month_year));
            $request->to_date = date('Y-m-t', strtotime($request->month_year));
        }
        $month = date('m', strtotime($request->to_date));
        $year = date('Y', strtotime($request->to_date));
        // 
        $getIncentive = $this->getIncentiveGroupByEmpId($request);
        if(count($getIncentive) > 0){
            $getEmployee = collect($this->employee->getEmployeeByAssociateId(['as_id','associate_id', 'as_name', 'as_line_id', 'as_floor_id', 'as_oracle_code', 'as_doj', 'as_section_id', 'as_subsection_id', 'as_unit_id', 'as_location', 'as_department_id', 'as_designation_id', 'as_ot', 'as_status']))->keyBy('as_id');
            $dataRow = $this->getIncentiveByFilter($request, $getIncentive, $getEmployee, $month, $year);
            // unit location permission
            $dataRow = collect($dataRow)->whereIn('as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('as_location', auth()->user()->location_permissions());
            // status check // add global table for month status wise 
            if(count($request->emp_status) > 0){
                $dataRow = collect($dataRow)->whereIn('emp_status', $request->emp_status);
            }
            // basic filter
            $getIncentive = $this->employee->getEmployeeByFilter($request, $dataRow);
            // dd($getIncentive);
        }
        $result = $this->getIncentiveSummerReport($request, $getIncentive);

        if(isset($request->export)){
            $filename = 'Incentive Bonus Report - ';
            $filename .= '.xlsx';
            return Excel::download(new IncentiveExport($result, [], 'report'), $filename);
        }
        return view('hr.reports.incentive.report', $result)->render();
    }

    public function getIncentiveGroupByEmpId($value='')
    {
        $getIncentive = $this->getIncentiveByRange($value);
        // group by and employee
        $getIncentiveGroup = collect($getIncentive)->groupBy('as_id')->map(function($q) {
            $b = $q->first();
            return (object)[
                'as_id' => $b->as_id,
                'count' => $q->count(),
                'amount' => $q->sum('amount'),
                'paid_amount' => $q->where('pay_status', 1)->sum('amount')
            ];
        });
        return $getIncentiveGroup;
    }
    public function getIncentiveByRange($value='')
    {
        $query = DB::table('hr_incentive_bonus')
        ->whereBetween('date', [$value->from_date, $value->to_date]);
        if($value->pay_type != null && $value->pay_type != ''){
            $query->where('pay_status', $value->pay_type);
        }
        return $query->get();
    }

    public function getIncentiveByFilter($input, $dataRow, $employee, $month, $year)
    {   
        // get benefit
        $getBenefit = Benefits::getBenefitDataByFields(['ben_current_salary', 'bank_name', 'bank_no', 'ben_bank_amount', 'ben_cash_amount']);
        $getSalary = DB::table('hr_monthly_salary')->select('as_id', 'month', 'year', 'unit_id', 'location_id', 'ot_status', 'emp_status')->where('month', $month)->where('year', $year)->get()->keyBy('as_id');

        return collect($dataRow)->map(function($q) use ($employee, $getBenefit, $getSalary) {
            $emp = $employee[$q->as_id]??'';
            if($emp != ''){
                $benefit = $getBenefit[$emp->associate_id]??'';
                $salary = $getSalary[$emp->associate_id]??'';
                if($salary != ''){
                    $q->as_department_id = $emp->as_department_id??'';
                    $q->as_designation_id = $emp->as_designation_id??'';
                    $q->as_section_id = $emp->as_section_id??'';
                    $q->as_subsection_id = $emp->as_subsection_id??'';
                    $q->as_line_id = $emp->as_line_id??'';
                    $q->as_floor_id = $emp->as_floor_id??'';
                    $q->as_location = $salary->location_id??$emp->as_location;
                    $q->associate_id = $emp->associate_id??'';
                    $q->as_name = $emp->as_name??'';
                    $q->as_unit_id = $salary->unit_id??$emp->as_unit_id;
                    $q->ot_status = $salary->ot_status??$emp->as_ot;
                    $q->emp_status = $salary->emp_status??$emp->as_status;
                    $q->gross = $benefit->ben_current_salary??0;
                    $q->cash_payable = $benefit->ben_cash_amount??0;
                    $q->bank_payable = $benefit->ben_bank_amount??0;
                    $q->bank_no = $benefit->bank_no??0;
                    $q->bank_name = $benefit->bank_name??'';
                    
                    return $q;
                }
                
            }
            
        });
    }

    public function getIncentiveSummerReport($input, $data)
    {
        $result['summary']      = $this->makeSummaryIncentive($data);

        $list = collect($data)
            ->groupBy($input['report_group'],true);
        if(!empty($input['selected'])){
            $input['report_format'] = 0;
        }

        if($input['report_format'] == 1){
            $list = $list->map(function($q){
                $q = collect($q);
                $sum  = (object)[];
                $sum->ot            = $q->where('ot_status', 1)->count();
                $sum->nonot         = $q->where('ot_status', 0)->count();
                $sum->nonotAmount   = $q->where('ot_status', 0)->sum('amount');
                $sum->otAmount      = $q->where('ot_status', 1)->sum('amount');
                $sum->cashPayable   = $q->where('bank_payable', 0)->where('cash_payable', '>', 0)->sum('amount');
                $sum->bankPayable   = $q->where('bank_payable', '>', 0)->sum('amount');
                $sum->paidAmount    = $q->sum('paid_amount');
                $sum->incentiveAmount    = $q->sum('amount');
                return $sum;
            })->all();
        }

        $result['uniqueGroup'] = $list;
        $result['input']       = $input->all();
        $result['format']      = $input['report_group'];
        $result['unit']        = unit_by_id();
        $result['location']    = location_by_id();
        $result['line']        = line_by_id();
        $result['floor']       = floor_by_id();
        $result['department']  = department_by_id();
        $result['designation'] = designation_by_id();
        $result['section']     = section_by_id();
        $result['subSection']  = subSection_by_id();
        $result['area']        = area_by_id();
        return $result;
    }

    protected function makeSummaryIncentive($data)
    {
        $data = collect($data);
        $sum  = (object)[];
        $sum->totalOt          = $data->where('ot_status', 1)->count();
        $sum->totalNonot       = $data->where('ot_status', 0)->count();
        $sum->totalNonotAmount = $data->where('ot_status', 0)->sum('amount');
        $sum->totalIncentive        = $data->sum('amount');
        $sum->totalCash        = $data->where('bank_payable', 0)->where('cash_payable', '>', 0)->sum('amount');
        $sum->totalBank        = $data->where('bank_payable','>', 0)->sum('amount');
        $sum->totalEmployees   = $data->count();
        $sum->totalOtAmount    = $data->where('ot_status', 1)->sum('amount');
        $sum->totalPaidAmount  = $data->sum('paid_amount');;
        return $sum;
    }
    
}
