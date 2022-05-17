<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Exports\Hr\BillExport;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\Benefits;
use App\Repository\Hr\BillAnnounceRepository;
use App\Repository\Hr\EmployeeRepository;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BillReportController extends Controller
{
    protected $bill;
    protected $employee;
    public function __construct(BillAnnounceRepository $bill, EmployeeRepository $employee)
    {
        ini_set('zlib.output_compression', 1);
        $this->bill = $bill;
        $this->employee = $employee;
    }
    public function index()
    {
        $data['yearMonth'] = $request->year_month??date('Y-m');
        $data['form_date'] = $request->form_date??date('Y-m-01');
        $data['to_date'] = $request->to_date??date('Y-m-d', strtotime('-1 day'));
        $data['months'] = monthly_navbar($data['yearMonth']);
        $data['salaryMax'] = Benefits::getSalaryRangeMax();
        $data['billType'] = bill_type_by_id();
        return view('hr.reports.bill.index', $data);
    }

    public function report(Request $request)
    {
        if($request->date_type == 'month'){
            $request->from_date = date('Y-m-01', strtotime($request->month_year));
            $request->to_date = date('Y-m-t', strtotime($request->month_year));
        }
        $getBill = $this->bill->getBillGroupByEmpId($request);
        if(count($getBill) > 0){
            $getEmployee = collect($this->employee->getEmployeeByAssociateId(['as_id','associate_id', 'as_name', 'as_line_id', 'as_floor_id', 'as_oracle_code', 'as_doj', 'as_section_id', 'as_subsection_id', 'as_unit_id', 'as_location', 'as_department_id', 'as_designation_id', 'as_ot', 'as_status']))->keyBy('as_id');
            $dataRow = $this->bill->getBillByFilter($request, $getBill, $getEmployee);
            // unit location permission
            $dataRow = collect($dataRow)->whereIn('as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('as_location', auth()->user()->location_permissions());
            // status check // add global table for month status wise 
            if(count($request->emp_status) > 0){
                $dataRow = collect($dataRow)->whereIn('emp_status', $request->emp_status);
            }
            // basic filter
            $getBill = $this->employee->getEmployeeByFilter($request, $dataRow);
            // dd($getBill);
        }
        $result = $this->bill->getBillSummerReport($request, $getBill);

        if(isset($request->export)){
            $filename = 'Bill Report - ';
            $filename .= '.xlsx';
            return Excel::download(new BillExport($result, [], 'report'), $filename);
        }
        return view('hr.reports.bill.report', $result)->render();
    }

    public function singleReport(Request $request)
    {
        $data['type'] = 'error';
        try {
            $input = $request->all();
            $query = DB::table("hr_bill")
            ->select('amount','bill_date')
            ->where('as_id', $input['as_id'])
            ->whereBetween('bill_date', [$input['from_date'], $input['to_date']]);
            if($input['pay_status'] != null){
                $query->where('pay_status', $input['pay_status']);
            }
            $getBill = $query->get()->keyBy('bill_date');
            $billDate = collect($getBill)->pluck('bill_date');
            // attendance data
            $getAtt = [];
            if(count($billDate) > 0){
                $tableName = get_att_table($input['unit']);
                $getAtt = DB::table($tableName)
                ->where('as_id', $input['as_id'])
                ->whereIn('in_date', $billDate)
                ->orderBy('in_date', 'asc')
                ->get(['in_date', 'in_time', 'out_time', 'remarks']);
                if(count($getAtt) > 0){
                    $getAtt = collect($getAtt)->map(function($q) use ($getBill){
                        $q->amount = $getBill[$q->in_date]->amount??0;
                        return $q;
                    });
                }
            }
            $data['employee'] = Employee::getEmployeeAsIdWiseSelectedField($input['as_id'], ['as_name', 'as_designation_id', 'as_doj', 'as_section_id', 'as_ot', 'associate_id']);
            $data['designation'] = designation_by_id();
            $data['section'] = section_by_id();
            $data['type'] = 'success';
            $data['value'] = $getAtt;
            $data['totalAmount'] = collect($getAtt)->sum('amount');
            return view('hr.reports.bill.single_report', $data);
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();
            return 'Something wrong, please try again!';
        }
    }
}
