<?php

namespace App\Repository\Hr;


use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Employee;
use App\Models\Hr\EarnedLeave;
use App\Models\Hr\Leave;
use App\Models\Hr\SalaryAdjustDetails;
use App\Models\Hr\SalaryAdjustMaster;
use App\Repository\Hr\AttendanceRepository;
use Carbon\Carbon;
use DB;

class LeaveRepository 
{
    const SICK_LEAVE    = 14;

    const CASUAL_LEAVE  = 10;

    protected $leaveBalanceStructure = [
        'sick' => [
            'total' => self::SICK_LEAVE,
            'enjoyed' => 0,
        ],
        'casual' => [
            'total' => self::CASUAL_LEAVE,
            'enjoyed' => 0,
        ],
        'earned' => [
            'total' => 0,
            'enjoyed' => 0,
        ],
    ];

    protected $attendanceRepository;

    public function __construct(AttendanceRepository $attendanceRepository)
    {
        $this->attendanceRepository = $attendanceRepository;
    }


    /**
     * steps of leave entry
     * 1. Leave form validation
     * 2. Leave Balance check
     * 3. If previous month, check monthly salary lock
     *    (a) locked == 1 && absent that day => employee will get leave adjustment
     *    (b) locked == 0 => salary process of previous month
     * 4. Remove present and absent records
     * 5. Process Salary
     *
     * @param  params  $request 
     * @return 
     */
    
    

    public function store($request)
    {
        DB::beginTransaction();
        try{

            $associateId = $request->leave_ass_id;
            $employee = $this->getEmployeeWithBenefit($associateId);

            $days    = count($request->leave_days);

            $verify  = $this->verifyLeaveBalance($associateId, $request->leave_type, $days);

            if($verify['success'] ==  false){
                return back()->withInput()->with('error', $verify['msg']);
            }

            // store leave data
            $firstDateOfCurrentMonth = date('Y-m-01');

            // leave entry records
            $leaveEntry = [];

            
            $processLastMonthSalary = false;
            $processCurrentMonthSalary = false;
            $leaveAdjustment    = [];

            $minDay = min($request->leave_days);

            //$lastMonth = date('Y-m-d', strtotime('-1 month'));
            $lastMonth = date('Y-m-d', strtotime($request->leave_from));
            $lockActivity = monthly_activity_close([
                'month' => date('m', strtotime($lastMonth)),
                'year'  => date('Y', strtotime($lastMonth)),
                'unit_id'  => $employee->as_unit_id
            ]);
            


            foreach($request->leave_days as $key => $dt){
                // separate entry for leave records
                $leaveEntry[$key] = [
                    'leave_ass_id' => $associateId,
                    'leave_type'   => $request->leave_type,
                    'leave_from'   => $dt,
                    'leave_to'     => $dt,
                    'leave_status' => 1,
                    'leave_applied_date' => $request->leave_applied_date??date('Y-m-d'),
                    'leave_comment'=> $request->leave_comment??'',
                    'created_by' => auth()->user()->id
                ];

                // check leave is previous month
                if($firstDateOfCurrentMonth > $dt){
                    // leave will be adjusted if salary locked
                    if($lockActivity == 0){
                        $processLastMonthSalary = true;
                    }else{
                        // add leave adjustment comment
                        $leaveEntry[$key]['leave_comment']   = 'Adjustment for '.date('F, Y', strtotime($request->leave_applied_date));
                        // should not generate salary and make adjustment
                        $leaveAdjustment[] = $dt; 

                    }
                }else{
                    $processCurrentMonthSalary = true;
                }
            }
            //dd($leaveEntry);
            
            // if leave exists => insert
            $leave = Leave::insert($leaveEntry);

            if($leave){
                if(count($leaveAdjustment) > 0){
                    // first check is there any present exists
                    $basic = ($employee->ben_basic/30);
                    $month = date('m', strtotime($request->leave_applied_date));
                    $year = date('Y', strtotime($request->leave_applied_date));
                    $this->leaveAdjust($associateId, $month, $year, $basic ,$leaveAdjustment);
                }

                // remove absent data
                $absent = $this->attendanceRepository->removeAbsent($associateId, $request->leave_days);
                // remove attendance data
                $absent = $this->attendanceRepository->removePresent(
                        $employee->as_unit_id, $employee->as_id, $request->leave_days
                    );

                // now call salary process of last month
                if($processLastMonthSalary){
                    $this->callSalaryProcess($employee->as_id, $employee->as_unit_id, $lastMonth);
                }
                // now call salary process of current month
                if($processCurrentMonthSalary){
                    $this->callSalaryProcess($employee->as_id, $employee->as_unit_id);
                }
            }

            DB::commit();
            return back()->with('success', 'Leave successfully entry');
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return back()->withInput()->with('error', $bug);
        }
    }

    /**
     * Get employee basic info
     *
     * @param  string  $associateId 
     * @return collection
     */

    public function getEmployeeWithBenefit($associateId)
    {
        return DB::table('hr_as_basic_info as b')
                ->select('b.as_id','b.associate_id','b.as_unit_id','b.as_doj','b.shift_roaster_status','ben.ben_current_salary','ben.ben_basic')
                ->leftJoin('hr_benefits as ben','b.associate_id','ben.ben_as_id')
                ->where('b.associate_id', $associateId)
                ->first();
    }


    /**
     * call salary process
     *
     * @param  string  $as_id 
     * @param  int  $unit_id 
     * @param  date  $monthYear 
     * @return boolean
     */

    public function callSalaryProcess($as_id, $unit_id, $date = null)
    {
        $nMonth = ($date == null)? date('Y-m-d'):$date;
        $table = get_att_table($unit_id);
        $month = date('m', strtotime($nMonth));
        $year  = date('Y', strtotime($nMonth));
        $days  = ($date == null)?date('d'):date('t',strtotime($nMonth));

        $queue = (new ProcessUnitWiseSalary($table, $month, $year, $as_id, $days))
            ->onQueue('salarygenerate')
            ->delay(Carbon::now()->addSeconds(2));
            dispatch($queue);
    }


    /**
     * add leave adjustment
     *
     * @param  string  $associate_id 
     * @param  string  $month 
     * @param  string  $year 
     * @param  array   $adjust_days 
     * @return boolean
     */

    public function leaveAdjust($associate_id, $month, $year, $basic ,$adjust_days)
    {
        if(count($adjust_days)){
            $master = SalaryAdjustMaster::firstOrNew([
                'associate_id' => $associate_id,
                'month' => $month,
                'year' => $year
            ]);
            $master->save();

            $adjust = [];
            foreach($adjust_days as $key => $dt){
                $adjust[] = [
                    'salary_adjust_master_id' => $master->id,
                    'date' => $dt,
                    'amount' => round($basic,2),
                    'type'   => 1
                ];
            }
            SalaryAdjustDetails::insert($adjust);
        }
    }


    public function chekLockActivity($date, $unit)
    {
        return monthly_activity_close([
            'month' => date('m', strtotime($date)),
            'year'  => date('Y', strtotime($date)),
            'unit'  => $unit
        ]);
    }

    /**
     * get leave days of an employee
     *
     * @param  string  $associateId 
     * @param  string  $startDate 
     * @param  string  $endDate 
     * @return array
     */


    public function leaveDays($associateId, $startDate, $endDate = null)
    {
        return Leave::where('leave_ass_id', $associateId)
            ->when($endDate != null, function($q) use ($startDate, $endDate) {
                $q->where('leave_from','>=', $startDate)
                  ->where('leave_from','<=', $endDate);
            })->when($endDate == null, function($q) use ($startDate) {
                $q->where('leave_from',$startDate);
            })
            ->get();
    }

    /**
     * verify leave balance Employee
     *
     * @param  string  $asId 
     * @param  string  $type 
     * @param  int     $day 
     * @return array
     */

    public function verifyLeaveBalance($asId, $type, $day = 0)
    {
        $type     = strtolower($type);
        if($type != 'special'){
            $balance  = $this->getLeaveBalance($asId);
            
            $availNow = $balance->{$type}->total - $balance->{$type}->enjoyed;
            //$availNow = ($type == 'earned' )?($availNow/2):$availNow;
            if($day > $availNow){
                return [
                    'success' => false,
                    'message' => 'This employee has only '.$availNow.' day(s) of '.ucwords($type).' leave'  
                ];
            }
        }

        return [
            'success' => true
        ];
    }

    /**
     * get leave balance of an Employee
     *
     * @param  string  $asId 
     * @param  string  $type 
     * @param  int     $day 
     * @return array   of balance sheet ref to $this->leaveBalanceStructure
     */
    public function getLeaveBalance($asId, $as_doj = null)
    {


        $enjoyed = $this->getEnjoyedLeaveByYear($asId);
        $earned  = $this->getEarnedLeaveBalance($asId);

        // convert array to object
        $bl = json_decode(json_encode($this->leaveBalanceStructure));

        // if a employee joined current year ovverride default amount
        if($as_doj){
            if($as_doj > date('Y-01-01') ){
                $month = date('m', strtotime($as_doj));
                $bl->sick->total    = ceil((self::SICK_LEAVE/12)*(12-($month-1)));
                $bl->casual->total  = ceil((self::CASUAL_LEAVE/12)*(12-($month-1)));
            }
        }

        if($enjoyed){
            $bl->sick->enjoyed    = $enjoyed->sick;
            $bl->casual->enjoyed  = $enjoyed->casual;
            $bl->special          = $enjoyed->special;
        }

        if(count($earned)>0){
            $bl->earned->total   = $earned->first()->total; 
            $bl->earned->enjoyed = $earned->first()->enjoyed; 
        }

        return $bl;

    }

    /**
     * get enjoyed leave balance of a year an Employee
     *
     * @param  string  $asId 
     * @param  int     $year 
     * @return collection   of leave amount 
     */
    public function getEnjoyedLeaveByYear($asId, $year = null)
    {
        $year = ($year == null)?date('Y'):$year;

        return  Leave::select(
                    DB::raw("
                        SUM(CASE WHEN leave_type = 'Casual' THEN DATEDIFF(leave_to, leave_from)+1 END) AS casual,
                        SUM(CASE WHEN leave_type = 'Earned' THEN DATEDIFF(leave_to, leave_from)+1 END) AS earned,
                        SUM(CASE WHEN leave_type = 'Sick' THEN DATEDIFF(leave_to, leave_from)+1 END) AS sick,
                        SUM(CASE WHEN leave_type = 'Special' THEN DATEDIFF(leave_to, leave_from)+1 END) AS special,
                        SUM(DATEDIFF(leave_to, leave_from)+1) AS total
                    ")
                )
                ->where('leave_status', '1')
                ->whereYear('leave_from', $year)
                ->where("leave_ass_id", $asId)
                ->first();
    }

    /**
     * get editable leave of an Employee after salary lock
     *
     * @param  date    $cutoffDate 
     * @param  string  $associateId 
     * @return collection   
     */
    public function getEditAbleLeave($cutoffDate, $associateId = null)
    {
        return  Leave::where('leave_from','>=', $cutoffDate)
                ->when($associateId != null, function($q){
                    $q->where("leave_ass_id", $associateId);
                })
                ->get();
    }

    /**
     * get earned leave balance of employeees
     *
     * @param  string  $asId 
     * @return collection   of leave amount 
     */
    public function getEarnedLeaveBalance($asId)
    {
        $asId = is_array($asId)?$asId:[$asId];
        
        return EarnedLeave::whereIn('associate_id', $asId)
                ->where('leave_year', date('Y'))
                ->get();
    }

    
}