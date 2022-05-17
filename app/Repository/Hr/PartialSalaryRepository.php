<?php

namespace App\Repository\Hr;

use App\Models\Employee;
use App\Models\Hr\AttendanceBonusConfig;
use App\Models\Hr\Benefits;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\PartialSalary;
use App\Models\Hr\SalaryAddDeduct;
use App\Models\Hr\SalaryAdjustMaster;
use App\Repository\Hr\AttendanceProcessRepository;
use App\Repository\Hr\AttendanceRepository;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Collection;

class PartialSalaryRepository 
{
	protected $month;

	protected $year;

	protected $startDate;

    protected $salaryDate;
   
    protected $totalDay;

    protected $employee;

    protected $attendance;

    protected $benefit;

    protected $salaryAdjust = [
    	'add' => 0, // add amount and adjust amount
    	'deduct' => 0,
    	'adjust' => 0
    ];

    protected $status;

    protected $dayCount = 0;

    protected $structure = [
    	'as_id' => null,
        'month' => null,
        'year'  => null,
        'gross' => 0,
        'basic' => 0,
        'house' => 0,
        'medical' => 0,
        'transport' => 0,
        'food' => 0,
        'late_count' => 0,
        'present' => 0,
        'holiday' => 0,
        'absent' => 0,
        'leave' => 0,
        'absent_deduct' => 0,
        'salary_add_deduct_id' => null,
        'ot_rate' => 0,
        'ot_hour' => 0,
        'attendance_bonus' => 0,
        'production_bonus' => 0,
        'emp_status' => null,
        'stamp' => 10, // initial stamp amount 10 taka
        'pay_status' => 1, // cash payable
        'partial_amount' => 0,
        'leave_adjust' => 0,
        'salary_payable' => 0,
        'cash_payable' => 0,
        'bank_payable' => 0,
        'tds' => 0,
        'total_payable' => 0,
        'ot_status'=> null,
        'designation_id' => null,
        'sub_section_id' => null,
        'location_id' => null,
        'roaster_status' => null,
        'unit_id' => null,
        'created_by' => null
    ];

    protected $attendanceRepository;

    public function __construct(AttendanceRepository $attendanceRepository)
    {
    	$this->attendanceRepository = $attendanceRepository;
    }

	public function process($employee, $salary_date, $status)
    {
    	DB::beginTransaction();
    	try{
    		$this->employee = $employee;
    		$this->salaryDate = $salary_date;
            $this->status = $status;

    		// don't break the sequence
    		$dataProcess = $this->initiate()
    			->configureBenefit()
    			->cookAttAndOt()
    			->findHoliday()
    			->findLeave()
    			->findAndDeductForAbsent()
    			->salaryAdjustment()
    			->deductPartialAmount()
    			->cookSalary()
    			->proceed();

    		DB::commit();
            return $dataProcess;
		}catch(\Exception $e){
			DB::rollback();
			DB::table('error')->insert(['msg'=> 'Partial salary '.$this->employee->associate_id.' '.$e->getMessage()]);
			return (object) [
	        	'status' => 0
	        ];
		}
    }

    public function initiate()
    {
    	$this->month = date('m', strtotime($this->salaryDate));
        $this->year = date('Y', strtotime($this->salaryDate));
        $this->totalDay = date('d', strtotime($this->salaryDate));

        $yearMonth = $this->year.'-'.$this->month;
        $empdojMonth = date('Y-m', strtotime($this->employee->as_doj));
        $empdojDay = date('d', strtotime($this->employee->as_doj));

        $this->startDate = Carbon::parse($this->salaryDate)->firstOfMonth()->format('Y-m-d');
        if($empdojMonth ==  $yearMonth){
            $this->startDate = $this->employee->as_doj;
            $this->totalDay = $this->totalDay - $empdojDay + 1;
        }

        // calculate per day data


        // initiate all basic data
        $this->structure['as_id'] = $this->employee->associate_id;
        $this->structure['month'] = $this->month;
        $this->structure['year'] = $this->year;
        $this->structure['emp_status'] = $this->status;
        
        $this->structure['ot_status'] = $this->employee->as_ot;
        $this->structure['designation_id'] = $this->employee->as_designation_id;
        $this->structure['sub_section_id'] = $this->employee->as_subsection_id;
        $this->structure['location_id'] = $this->employee->as_location;
        $this->structure['roaster_status'] = $this->employee->shift_roaster_status;
        $this->structure['unit_id'] = $this->employee->as_unit_id;
        $this->structure['created_by'] = auth()->id();

        return $this;

	}

	public function configureBenefit()
	{
		$dateCount = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);

		$this->benefit = (object)[];
		$this->benefit->perDayBasic = round(($this->employee->ben_basic / 30),2);
		$this->benefit->perDayGross = round(($this->employee->ben_current_salary /  $dateCount),2);

		$this->structure['gross'] = $this->employee->ben_current_salary??0;
        $this->structure['basic'] = $this->employee->ben_basic??0;
        $this->structure['house'] = $this->employee->ben_house_rent??0;
        $this->structure['medical'] = $this->employee->ben_medical??0;
        $this->structure['transport'] = $this->employee->ben_transport??0;
        $this->structure['food'] = $this->employee->ben_food??0;

        return $this;
	}

    public function cookAttAndOt()
    {
    	$table = get_att_table($this->employee->as_unit_id);
        $this->attendance =  DB::table($table)
                ->select(
                    DB::raw('COUNT(*) as present'),
                    DB::raw('SUM(ot_hour) as ot_hour'),
                    DB::raw('COUNT(CASE WHEN late_status =1 THEN 1 END) AS late')
                )
                ->where('as_id',$this->employee->as_id)
                ->where('in_date','>=',$this->startDate)
                ->where('in_date','<=', $this->salaryDate)
                ->first();

        if($this->attendance){
        	$this->calculateOt();
	        $this->structure['present'] = $this->attendance->present;
	        $this->structure['late_count'] = $this->attendance->late;

	        $this->dayCount += $this->attendance->present;
        }

        return $this;
    }

    public function calculateOt()
    {
    	$overtimes = $this->attendance->ot_hour??0;

        // check if Friday has extra OT
        if($this->employee->shift_roaster_status == 1 ){
            $friday_ot = DB::table('hr_att_special')
                            ->where('as_id', $this->employee->as_id)
                            ->where('in_date','>=', $this->startDate)
                            ->where('in_date','<=', $this->salaryDate)
                            ->get()
                            ->sum('ot_hour');

            $overtimes = $overtimes + $friday_ot;
        }

        $diffExplode = explode('.', $overtimes);
        $minutes = (isset($diffExplode[1]) ? $diffExplode[1] : 0);
        $minutes = floatval('0.'.$minutes);
        if($minutes > 0 && $minutes != 1){
            $min = (int)round($minutes*60);
            $minOT = min_to_ot();
            $minutes = $minOT[$min]??0;
        }

        $this->structure['ot_hour'] = $diffExplode[0]+$minutes;
        $this->structure['ot_rate'] = number_format((($this->employee->ben_basic/208)*2), 2, ".", "");
        return $this;
    }

    public function findHoliday()
    {
    	$holidays = $this->attendanceRepository->getHolidays(
    		$this->employee, 
    		$this->startDate, 
    		$this->salaryDate);

    	$this->structure['holiday'] = count($holidays);
    	$this->dayCount += $this->structure['holiday'];
    	return $this;
    }

    public function findLeave()
    {
    	$leave = DB::table('hr_leave')
	        ->select(
	            DB::raw("SUM(DATEDIFF(leave_to, leave_from)+1) AS total")
	        )
	        ->where('leave_ass_id', $this->employee->associate_id)
	        ->where('leave_from', '>=', $this->startDate)
	        ->where('leave_to', '<=', $this->salaryDate)
	        ->first()->total??0;

	    $this->structure['leave'] = $leave;
	    $this->dayCount += $this->structure['leave'];

	    return $this;
    }


    public function findAndDeductForAbsent()
    {
    	$absent = $this->totalDay - $this->dayCount;
    	$absent = $absent < 0?0:$absent;
    	$this->structure['absent'] = $absent;
    	if($absent > 0){
    		$this->structure['absent_deduct'] = (int)($absent * $this->benefit->perDayBasic);
    	}
    	return $this;
    }

    public function salaryAdjustment()
    {
    	// normal add deduct
    	$addDeduct = SalaryAddDeduct::where('associate_id', $this->employee->associate_id)
	        ->where('month',  $this->month)
	        ->where('year',  $this->year)
	        ->first();

        if($addDeduct){
            $this->salaryAdjust['deduct'] = ($addDeduct->advp_deduct + $addDeduct->cg_deduct + $addDeduct->food_deduct + $addDeduct->others_deduct);
            $this->salaryAdjust['add'] = $addDeduct->salary_add;

            // set production bonus amount in structure
            $this->structure['production_bonus'] = $addDeduct->bonus_add;
            $this->structure['salary_add_deduct_id'] = $addDeduct->id;
        }

    	// leave adjust, salary add, increment arear and bonus arear 
    	$adjustMaster = SalaryAdjustMaster::with('adjusts')
    		->where('associate_id', $this->employee->associate_id)
    		->where('month', $this->month)
        	->where('year', $this->year)
        	->first();

        if($adjustMaster){
        	if(count($adjustMaster->adjusts)){
        		$this->salaryAdjust['adjust'] = $adjustMaster->adjusts->sum('amount');
        		$this->salaryAdjust['add'] += $this->salaryAdjust['adjust'];
        		// type wise 1= leave, 2 = substitute, 3 = salary, 4 = increment arear 
        		$this->structure['leave_adjust'] = $adjustMaster->adjusts->where('type',1)->sum('amount');
        	}
        }

        return $this;
    }

    public function deductPartialAmount()
    {
    	$params = [
        	'as_id' => $this->employee->as_id,
        	'month' => $this->month,
        	'year' => $this->year
        ];
    	$this->structure['partial_amount'] = PartialSalary::getEmployeeWisePartialAmount($params);
    	return $this;
    }


    public function cookSalary()
    {
    	$deduct = $this->structure['absent_deduct'] + $this->salaryAdjust['deduct'];
    	$this->structure['salary_payable'] = round(($this->benefit->perDayGross*$this->totalDay - $deduct),2);
    	$otAmount = $this->structure['ot_hour'] * $this->structure['ot_rate'];
    	$addAmount = $this->salaryAdjust['add'] + $this->structure['production_bonus'];
    	$partialAmount = $this->structure['partial_amount'];

    	$totalPayable = ceil((float)($this->structure['salary_payable'] + $otAmount + $addAmount - $partialAmount));

    	$this->structure['stamp'] = $totalPayable > 1000?10:0;
        $this->structure['salary_payable'] = $this->structure['salary_payable'] - $this->structure['stamp'];
    	$this->structure['total_payable']  = $totalPayable - $this->structure['stamp']; 
    	$this->structure['cash_payable']  = $this->structure['total_payable']; 

    	return $this;
    }

    public function proceed()
    {
        $response = (object)[];
    	$salary = $this->getSalaryOfThisMonth();
    	if(($this->structure['present'] + $this->structure['leave']) > 0){
	        if($salary){
	            $st = HrMonthlySalary::where('id', $salary->id)
	            	->update($this->structure);  
	        }else{
	        	$st = HrMonthlySalary::create($this->structure);
	            
	        }

	        $finalSalary = $this->getSalaryOfThisMonth();

            $response->status = 1;
            $response->salary = $finalSalary;
	    }else{
            $response->status = 2;
        }

        if(($response->status == 2 )|| ($this->employee->as_doj == $this->salaryDate) || ($this->status == 6)){
            // delete salary if exists
            if($salary != null){
                HrMonthlySalary::where('id', $salary->id)->delete();
            }
        }

        // after salary remove if exists
        $nextMonthYear = date('Y-m-d', strtotime('+1 month', strtotime($this->salaryDate)));
        $month = date('m', strtotime($nextMonthYear));
        $year = date('Y', strtotime($nextMonthYear));
        HrMonthlySalary::where('as_id', $this->employee->associate_id)
        ->where('month', '>=', $month)
        ->where('year', $year)
        ->delete();

        return $response;

    }

    public function getSalaryOfThisMonth()
    {
    	return HrMonthlySalary::where([
                'as_id' => $this->employee->associate_id,
                'month' => $this->month,
                'year' => $this->year
            ])
            ->first();
    }

}