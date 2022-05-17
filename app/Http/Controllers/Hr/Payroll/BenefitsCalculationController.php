<?php

namespace App\Http\Controllers\Hr\Payroll;

use App\Helpers\BnConvert;
use App\Helpers\EmployeeHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Hr\Reports\JobCardController as JobCard;
use App\Http\Requests\Hr\EndofJobBenefitRequest;
use App\Models\Employee;
use App\Models\Hr\AttendanceBonus;
use App\Models\Hr\EarnedLeave;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\HrAllGivenBenefits;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\SalaryAddDeduct;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\YearlyHolyDay;
use App\Repository\Hr\FinalSettlementRepository;
use Carbon\Carbon;
use DB, Response, Auth, Exception, DataTables, Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class BenefitsCalculationController extends Controller
{
    
    protected $finalSettlement;

    public function  __construct(FinalSettlementRepository $finalSettlement) 
    {
        $this->finalSettlement = $finalSettlement;
    }

    public function index(){
    	return view('hr.payroll.end-of-job.benefits');
    }

    public function getEmployeeDetails(Request $request)
    {
    	try{
            
            return $this->finalSettlement->getEndOfJobPropertiessByEmployee($request);

    	}catch(\Exception $e){
    		return $e->getMessage();
    	}
    }

    protected function checkExist($key, $arr)
    {
        $value = Carbon::parse($key)->addDay()->toDateString();
        if(in_array($value, $arr->toArray())){
            $key = $value;
            return $this->checkExist($key, $arr);
        }
        return $key;
    }

    public function getEmpJobcard($request)
    {
        // check first last month salary locked or not
        $result = $this->finalSettlement->getJobCardofLastWorking((array)$request);
        $lastLeaveDate = collect($result['leaveDate'])->keys()->last();
        $lastPresentDate = collect($result['presentDate'])->keys()->last();
        $last_date = max($lastPresentDate, $lastLeaveDate);

        if($last_date){
            $holidays = collect($result['holidayDate'])->keys();
            $last_date = $this->checkExist($last_date, $holidays);
        }else{
            $last_date = Carbon::parse($request->month_year.'-01')->subMonth()->endOfMonth()->toDateString();
        }


        $card = view('hr/reports/job_card/report', $result)->render();

        return array(
            'jobcard' => $card,
            'lastdate' => $last_date
        );
    }

    public function saveBenefits(EndofJobBenefitRequest $request)
    {
        return $this->finalSettlement->processBenefits($request);
    }

    public function previewBenefits($id)
    {
        $benefits = HrAllGivenBenefits::find($id);

        $employee = get_employee_by_id($benefits->associate_id);

        $jobDuration = $this->finalSettlement->getJobDuration($employee->as_doj, $benefits->status_date);

        return view('hr.common.end_of_job_final_pay', compact('employee','benefits','jobDuration'))->render();
    }

    public function givenBenefitList()
    {
        $unitList = DB::table('hr_unit')->pluck('hr_unit_short_name')->toArray();
        return view('hr.payroll.given_benefits_list', compact('unitList'));
    }

    public function getGivenBenefitData(Request $request)
    {
        $benefitType = [
            '2' => 'Resign',
            '3' => 'Termination',
            '4' => 'Dismiss',
            '5' => 'Left',
            '7' => 'Death',
            '8' => 'Retirement'
        ];
        $data = DB::table('hr_all_given_benefits as b')
                ->select([
                    'b.*',
                    'c.as_name',
                    'd.hr_unit_short_name as unit_name',
                    DB::raw('b.earn_leave_amount + b.service_benefits + b.subsistance_allowance + b.notice_pay + b.termination_benefits + b.death_benefits as total_amount')
                ])
                ->leftJoin('hr_as_basic_info as c', 'c.associate_id', 'b.associate_id')
                ->leftJoin('hr_unit as d', 'd.hr_unit_id', 'c.as_unit_id')
                ->whereIn('c.as_unit_id', auth()->user()->unit_permissions())
                ->whereIn('c.as_location', auth()->user()->location_permissions())
                ->orderBy('b.id', 'DESC')
                ->get();

        //$prev_month = Carbon::now()->subMonth()->format('Y-m-t');

        $lock = DB::table('salary_audit')
                        ->whereIn('unit_id', auth()->user()->unit_permissions())
                        ->orderBy('id',"DESC")
                        ->first();

        $lock_date = date('Y-m-t',strtotime($lock->year.'-'.$lock->month.'-01'));

        // dd($data);exit;
        return DataTables::of($data)->addIndexColumn()
                ->editColumn('benefit_on', function($data) use ($benefitType){
                    return $benefitType[$data->benefit_on];
                })
                ->addColumn('action', function($data) use ($lock_date) {
                    $btn = "<div class='text-nowrap'>";
                    
                    $btn .= "<a href=".url('hr/payroll/benefits?associate='.$data->associate_id)." class='btn btn-sm btn-primary' data-toggle='tooltip' title='View' style='margin-top:1px;'>
                        <i class=' fa fa-eye bigger-120'></i></a> ";

                    if(date('Y-m', strtotime($data->status_date)) == date('Y-m') || $data->status_date > $lock_date){
                        $btn .= "<a class='btn btn-sm btn-danger rollback text-white'  data-associate='".$data->associate_id."' data-name='".$data->as_name."' data-toggle='tooltip' title='Rollback Data' style='margin-top:1px;'><i class='fa fa-undo'></i></a> ";
                    }
                    

                    $btn .= "</div>";

                    return $btn;
                })
                ->rawColumns(['benefit_on','action'])
                ->toJson();
    }


    public function associtaeSearch(Request $request)
    {

 
        $cantacces = [];
     
        $userIdNotAccessible = DB::table('roles')
               ->whereIn('name',$cantacces)
               ->leftJoin('model_has_roles','roles.id','model_has_roles.role_id')
               ->pluck('model_has_roles.model_id');


        $data = [];
        if($request->has('keyword'))
        {
            $search = $request->keyword;
            $data = Employee::select("associate_id", DB::raw('CONCAT_WS(" - ", associate_id, as_name) AS associate_name'))
                ->where(function($q) use($search) {
                    $q->where("associate_id", "LIKE" , "%{$search}%");
                    $q->orWhere("as_name", "LIKE" , "%{$search}%");
                })
                ->whereIn('as_unit_id', auth()->user()->unit_permissions())
                ->whereNotIn('as_id', auth()->user()->management_permissions())
                ->take(20)
                ->get();
        }

        return response()->json($data);
    }

    
}
