<?php
namespace App\Http\Controllers\Hr\TimeAttendance;
use App\Helpers\Custom;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hr\LeaveRequest;
use App\Jobs\BuyerManualLeaveApproveProcess;
use App\Jobs\ProcessMonthlySalary;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Employee;
use App\Models\Hr\Absent;
use App\Models\Hr\Benefits;
use App\Models\Hr\EarnedLeave;
use App\Models\Hr\Leave;
use App\Models\Hr\LeaveApproval;
use App\Models\Hr\SalaryAdjustDetails;
use App\Models\Hr\SalaryAdjustMaster;
use App\Repository\Hr\AttendanceRepository;
use App\Repository\Hr\LeaveRepository; 
use Auth, Validator, ACL, DB,stdClass,DateTime, DatePeriod, DateInterval;
use Carbon\Carbon;
use Illuminate\Http\Request;


class LeaveWorkerController extends Controller
{
    protected $leaveRepository;

    public function __construct(LeaveRepository $leaveRepository)
    {
        $this->leaveRepository = $leaveRepository;
    }

    public function showForm()
    {
        return view('hr.timeattendance.leave_worker');
    }


    public function getLeaveBalance(Request $request)
    {
        try{

            $employee = Employee::where('associate_id', $request->associate_id)->first();
            $balance  = $this->leaveRepository->getLeaveBalance($request->associate_id, $employee->as_doj);
            $view     = view('hr.timeattendance.leave.leave-balance-employee', compact('employee','balance'))->render();

            return [
                'view' => $view,
                'balance' => $balance
            ];
        }catch(\Exception $e){
            return $e;
        }
    }

    

    public function splitDays(Request $request, AttendanceRepository $att)
    {
        $fromDate = $request->from_date;
        $toDate   = $request->to_date;

        $employee = Employee::where('associate_id', $request->associate_id)->first();

        // get holidays by employee and date range
        $holidays = $att->getHolidays($employee, $fromDate, $toDate);

        // get present date by employee
        $present  = $att->getPresentDateByEmployee(
                        $employee->as_id, $employee->as_unit_id, $fromDate, $toDate
                    );

        $leave    = $this->leaveRepository
                         ->leaveDays($request->associate_id, $fromDate, $toDate)
                         ->pluck('leave_from')
                         ->map(function($q){
                            return $q->toDateString();
                         });

        $leaveDate = displayBetweenTwoDates($fromDate, $toDate);

        
        return view('hr.timeattendance.leave.split-leave-days',compact('holidays','present','leaveDate','leave'))->render();
    }


    public function saveData(LeaveRequest $request)
    {
        return $this->leaveRepository->store($request);    
    }


    # store data
    public function leaveApprove($id, $type)
    {
        
        

    }


    public function LeaveStatusCheckAndUpdate(){
        $leave_exists= Leave::where('leave_status', 1)
                            ->where('leave_complete_status', 0)
                            ->whereDate('leave_from', date('Y-m-d'))
                            ->get();
        if(!empty($leave_exists)){

            foreach($leave_exists AS $leave){
                DB::table('hr_leave')
                    ->where('id', $leave->id)
                    ->update([
                        'leave_complete_status' => 1
                        ]);

               // $this->logFileWrite('Leave Status Updated!', $leave->leave_ass_id);

                if($leave->leave_type == "Maternity"){
                    DB::table('hr_as_basic_info')
                        ->where('associate_id', $leave->leave_ass_id)
                        ->update([
                            'as_status' => 6
                            ]);
                }
            }
        }



        $leave_exists= Leave::where('leave_status', 1)
                            ->where('leave_complete_status', 1)
                            ->whereDate('leave_to', date('Y-m-d', strtotime("-1 days")))
                            ->get();

        if(!empty($leave_exists)){

            foreach($leave_exists AS $leave){
                DB::table('hr_leave')
                    ->where('id', $leave->id)
                    ->update([
                        'leave_complete_status' => 2
                        ]);

               // $this->logFileWrite('Leave Status Updated!', $leave->leave_ass_id);

                if($leave->leave_type == "Maternity"){
                    DB::table('hr_as_basic_info')
                        ->where('associate_id', $leave->leave_ass_id)
                        ->update([
                            'as_status' => 1
                            ]);
                }
            }
        }
    }
}
