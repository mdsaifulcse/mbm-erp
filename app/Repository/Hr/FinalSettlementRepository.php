<?php

namespace App\Repository\Hr;

use App\Helpers\EmployeeHelper;
use App\Models\Employee;
use App\Models\Hr\EarnedLeave;
use App\Models\Hr\HrAllGivenBenefits;
use App\Repository\BaseRepository;
use App\Repository\Hr\JobCardRepository;
use App\Repository\Hr\PartialSalaryRepository;
use Carbon\Carbon;
use DB;

class FinalSettlementRepository extends BaseRepository
{
    protected $jobCardRepository;

	protected $partialSalaryRepository;

	protected $employee;

    public $benefitType = [
        '2' => 'Resign',
        '3' => 'Termination',
        '4' => 'Dismiss',
        '5' => 'Left',
        '7' => 'Death',
        '8' => 'Retirement'
    ];

	protected $assistantManagerToAbove = ['L6','L7','L8','L9','L10','L11','L12','L13','L14','L15',
	'L16','L17','L18','L19'];


    public function __construct(JobCardRepository $jobCardRepository, PartialSalaryRepository $partialSalaryRepository)
    {
        $this->jobCardRepository = $jobCardRepository;
        $this->partialSalaryRepository = $partialSalaryRepository;
    }

    public function get($input)
    {
        $unit = $this->getAuthUnit($input['unit']);
        $location = $this->getAuthLocation($input['location']);

        return HrAllGivenBenefits::with([
                'audit', 
                'employee'
            ])
            ->whereHas('employee', function($p) use ($input, $location, $unit) {
                $p->whereIn('as_location', $location)
                    ->whereIn('as_unit_id', $unit)
                    ->when(!empty($input['line_id']), function ($query) use($input){
                        $query->where('as_line_id', $input['line_id']);
                    })
                    ->when(!empty($input['floor_id']), function ($query) use($input){
                        $query->where('as_floor_id',$input['floor_id']);
                    })
                   ->when($input['otnonot']!=null, function ($query) use($input){
                        $query->where('as_ot',$input['otnonot']);
                    })
                    ->when(!empty($input['area']), function ($query) use($input){
                        $query->where('as_area_id',$input['area']);
                    })
                    ->when(!empty($input['department']), function ($query) use($input){
                        $query->where('as_department_id',$input['department']);
                    })
                    ->when(!empty($input['section']), function ($query) use($input){
                        $query->where('as_section_id', $input['section']);
                    })
                    ->when(!empty($input['subSection']), function ($query) use($input){
                        $query->where('as_subsection_id', $input['subSection']);
                    });
            })
            ->where('status_date','>=', $input['from_date'])
            ->where('status_date','<=', $input['to_date'])
            ->orderBy('id', 'DESC')
            ->get();
    }


    public function getEndOfJobPropertiessByEmployee($request)
    {
    	// check benefits
    	$benefits = DB::table('hr_all_given_benefits')
                ->where('associate_id', $request->emp_id)->first();

        $employee = get_employee_by_id($request->emp_id);


        $earnedLeave = 0;

        $response = (object)[];
        
        // return benefits page
        if($benefits){
        	$response->has_benefits = 1;
        	$years = 0; $months = 0;
        	$jobDuration = $this->getJobDuration($employee->as_doj, $benefits->status_date);
        	$response->benefits = view('hr.common.end_of_job_final_pay', compact('employee','benefits','jobDuration'))->render();
        	$response->salary = '';
        	$date = $benefits->salary_date;
        	if(date('m',strtotime($date)) == date('m',strtotime($benefits->status_date))){
	        	$response->salary = $this->getSalaryVoucher($employee,  $date);
        	}
        }else{
        	// show employee for taking status
        	$response->has_benefits = 0;
        	$response->attendance = $this->getJobCardofLastWorking($employee->associate_id, $employee->as_unit_id);
        	$year = Carbon::parse($response->attendance->last_date)->format('Y');
        	

        	$earnedLeave = EarnedLeave::where('associate_id', $employee->associate_id)
                ->where('leave_year', $year)
                ->first()->remaining??0;
        }

        $response->profile = view('hr.payroll.end-of-job.employee-profile', compact('employee','earnedLeave'))->render();

        return (array) $response;
    }


    public function processBenefits($request)
    {
    	$employee = get_employee_by_id($request->associate_id);
    	$response = (object)[];
    	//DB::beginTransaction();
        $response->status = 'success';
    	try{
    		$jobDuration = $this->getJobDuration($employee->as_doj, $request->status_date);
            // $salaryDate = $request->salary_date??$request->effective_date;
            $salaryDate = (date('d', strtotime($request->status_date)) > 1?$request->salary_date:$request->status_date)??$request->status_date;
    		
    		$lastMonthLastDate = Carbon::now()->subMonth()->endOfMonth()->toDateString();

    		$params['month'] = date('m', strtotime($salaryDate));
	        $params['year'] = date('Y', strtotime($salaryDate));
	        $params['unit_id'] = $employee->as_unit_id;
	        $lockActivity = monthly_activity_close($params);
	        if($lockActivity == 0){
                $empStatus = $this->getEmployeeStatus($request);
                $partialSalary = $this->partialSalaryRepository->process(
                    $employee, 
                    $salaryDate, 
                    $empStatus
                );

                if($partialSalary->status == 0){
                    $response->status = 'error';
                    $response->message = 'Something went wrong! Please try Again!';
                    return $response;
                }

                $getBenefit = $this->storeBenefits($employee, $request, $jobDuration);
                
                if($getBenefit['status'] == 'error'){
                    $response->status = 'error';
                    $response->message = 'Something went wrong! Please try Again!';
                    return $response;
                }

                $benefits = $getBenefit['value'];
                $response->benefit = view('hr.common.end_of_job_final_pay', compact('employee','benefits','jobDuration'))->render();
	    		if($partialSalary->status == 1){
	    			$salary = $partialSalary->salary;
	    			$response->salary = view('hr.common.partial_salary_sheet', compact('salary','employee'))->render();
                    $response->message = 'Successfully Process';

	    		}else if($partialSalary->status == 2){
	    			$response->salary = '<div class="text-center alert alert-primary"><p class="iq-alert-text">Sorry! This employee has no salary in this month!</p></div>';
                    $response->message = 'This employee has no salary in this month';
	    		}
                
	    	}else{
                $response->salary = '<div class="text-center alert alert-primary"><p class="iq-alert-text">Salary of this employee already recorded in active salary sheet!</p></div>';
                $response->message = 'Salary of this employee already recorded in active salary sheet!';
            }

    		return (array) $response;
	    }catch(\Exception $e){
    		DB::rollback();
            $response->status = 'error';
            $response->message = $e->getMessage();
	    	return $response;
    	}
    }


    public function storeBenefits($employee, $request, $jobDuration)
    {
        $response = [];
        DB::beginTransaction();
    	try {
            $perDayBasic = round($employee->ben_basic/30,2);
            $perDayGross = round($employee->ben_current_salary/30,2);
            $designationGrade = $employee->hr_designation_grade;

            $serviceBenefit = $this->serviceBenefit($jobDuration, $perDayBasic, $designationGrade);
            $earnedLeave = $this->earnleaveBenefits($employee->associate_id, $request->status_date, $perDayGross);
            $earnedAndServiceBenefit = $earnedLeave->earned_leave_payment + $serviceBenefit->service_benefit;


            $benefits = new HrAllGivenBenefits();
            $benefits->associate_id = $request->associate_id;  
            $benefits->status_date = $request->status_date;
            $benefits->earned_leave = $earnedLeave->earned_leave;
            $benefits->earn_leave_amount = $earnedLeave->earned_leave_payment;
            $benefits->service_days = $serviceBenefit->service_days;
            $benefits->service_benefits = $serviceBenefit->service_benefit; 
            $benefits->suspension_days = $request->suspension_days??0;  

            $subsistance_allowance = 0;
            $notice_pay = 0;
            $total_payment = 0;
            $termination_benefits = 0;
            $notice_pay_month = 0;

         
            if($request->benefit_on == 'on_resign'){
                if($request->notice_pay == 1){
                    $notice_pay_month = 2;
                    $notice_pay = 2*$employee->ben_basic;
                }
                $total_payment = $earnedAndServiceBenefit - $notice_pay;
                $status = 2;
            }else if($request->benefit_on == 'on_left'){
                // notice pay qill deducted
                $notice_pay_month = 2;
                $notice_pay = 2*$employee->ben_basic;
                $total_payment = $earnedAndServiceBenefit - $notice_pay;
                $status = 5;
            }else if($request->benefit_on == 'on_dismiss') {
                $subsistance_allowance = ($request->suspension_days*$perDayBasic) + 1850;
                $total_payment = $earnedLeave->earned_leave_payment + $subsistance_allowance;
                $status = 4;
            }
            else if($request->benefit_on == 'on_terminate') {
                // notice pay will be added
                if($request->notice_pay == 1){
                    $notice_pay = 4*$employee->ben_basic;
                    $notice_pay_month = 4;
                }
                $total_payment = $earnedAndServiceBenefit + $notice_pay;
                $status = 3;
            }
            else if($request->benefit_on == 'on_death') {
                $deathBenefit = $this->deathBenefit($jobDuration, $request->death_reason, $perDayBasic);

                $benefits->death_days               = round($deathBenefit->death_days??0,2);  
                $benefits->death_reason             = $request->death_reason; 
                $benefits->death_benefits           = round($deathBenefit->death_benefit??0,2);
                $status = 7;
                $total_payment = $earnedAndServiceBenefit +$deathBenefit->death_benefit;
            }else if($request->benefit_on == 'on_retirement') {
                $total_payment = $earnedAndServiceBenefit;
                $status = 8;
            }

            $benefits->benefit_on = $status; 
            $benefits->subsistance_allowance = round($subsistence_allowance??0,2); 
            $benefits->notice_pay_month  = $notice_pay_month; 
            $benefits->notice_pay = round($notice_pay??0,2);
            $benefits->termination_benefits = $termination_benefits??0;  
            $benefits->total_amount = ceil($total_payment); 
            $benefits->salary_date = $request->salary_date; // salary will calculate based on this
            $benefits->created_by = auth()->user()->id;
            $benefits->save();

            //if($benefits){
                $empBasic = Employee::findOrFail($employee->as_id);
                $empBasic->update([
                    'as_status' => $status,  
                    'as_status_date' => $request->status_date 
                ]);
                // DB::table('hr_as_basic_info')
                // ->where('associate_id', $request->associate_id)
                // ->update([
                //      'as_status' => $status,  
                //      'as_status_date' => $request->status_date 
                // ]);
            //}

            DB::commit();
            $response['status'] = 'success';
            $response['value'] = $benefits;
            return $response;
        } catch (\Exception $e) {
            DB::rollback();
            $response['status'] = 'error';
            $response['message'] = $e->getMessage();
            return $response;

        }
    }

    public function getEmployeeStatus($request)
    {
        if($request->benefit_on == 'on_resign'){
            $status = 2;
        }else if($request->benefit_on == 'on_left'){
            $status = 5;
        }else if($request->benefit_on == 'on_dismiss') {
            $status = 4;
        }
        else if($request->benefit_on == 'on_terminate') {
            $status = 3;
        }
        else if($request->benefit_on == 'on_death') {
            $status = 7;
        }else if($request->benefit_on == 'on_retirement') {
            $status = 8;
        }
        return $status;
    }

    public function serviceBenefit($jobDuration, $perDayBasic, $designationGrade)
    {
    	$serviceDays = 0;
    	$years = $jobDuration->years;
        $months = $jobDuration->months;
        if( 5 <= $years && $years < 10){
            if($months >= 8){
                $years++;
            }
            $serviceDays =  (14*$years);
            // if employee assistant manager to above
            if(in_array($designationGrade, $this->assistantManagerToAbove)){
            	$serviceDays =  (7*$years);
            }
        }else if($years >= 10){
            if($months >= 8){
                $years++;
            }
            $serviceDays =  (30*$years);
            // if employee assistant manager to above
            if(in_array($designationGrade, $this->assistantManagerToAbove)){
            	$serviceDays =  (15*$years);
            }
        }
        $benefits = round($serviceDays * $perDayBasic, 2);

        return (object) [
        	'service_days' => $serviceDays,
        	'service_benefit' => $benefits
        ];
    }

    public function earnleaveBenefits($associateId, $statusDate, $perDayGross)
    {
    	$year = Carbon::parse($statusDate)->format('Y');
    	$earnleaveDays =  EarnedLeave::where('associate_id', $associateId)
                ->where('leave_year', $year)
                ->first()->remaining??0;

        return (object)[
        	'earned_leave' => $earnleaveDays,
        	'earned_leave_payment' => round($earnleaveDays * $perDayGross, 2)
        ];
    }


    public function deathBenefit($jobDuration, $reason, $perDayBasic)
    {
    	$death_days = 0;
        $death_benefit = 0;
        $years = $jobDuration->years;
        $months = $jobDuration->months;
        if($years >= 2){
            if($months > 4 && $months < 8) {
                $years += 0.5;
            }else if($months > 8){
                $years += 1;
            }
            if($reason == 'natural_death'){
                $death_days = (30*$years);

            }else if($reason  == 'duty_accidental_death'){
                $death_days = (45*$years);
            }
        }
        return (object)[
        	'death_days' => $death_days,
        	'death_benefit' => round($death_days * $perDayBasic, 2)
        ];
    }


    public function getJobCardofLastWorking($associateId, $unit)
    {
    	$yearMonth = Carbon::now()->subMonth()->format('Y-m');
    	$status = $this->getSalaryLockStatus($yearMonth, $unit);
    	$yearMonth = $status == 1?date('Y-m'):$yearMonth;

    	$result = $this->getMonthlyAttendance($associateId, $yearMonth);


    	$lastLeave = collect($result['leaveDate'])->keys()->last();
        $lastPresent = collect($result['presentKeyDate'])->last();

        $lastDate = max($lastPresent, $lastLeave);
        $flag = 0;
        if($lastDate){
	        $holidays = collect($result['holidayDate'])->keys();
	    	$lastDate = $this->getDateIfHoliday($lastDate, $holidays);	
        }else{
            if(date('Y-m', strtotime($result['info']['as_doj'])) == $yearMonth){
                $flag = 1;
                $lastDate = date('Y-m-d', strtotime($result['info']['as_doj']));
            }else if(date('Y-m', strtotime($result['info']['as_status_date'])) == $yearMonth){
                $flag = 1;
                $lastDate = date('Y-m-d', strtotime($result['info']['as_status_date']));
            }else{
        	   $lastDate = Carbon::parse($yearMonth.'-01')->subMonth()->endOfMonth()->toDateString();
            }
        }
        if($flag == 1){
            $effectiveDate = $lastDate;
        }else{
            $effectiveDate = Carbon::parse($lastDate)->addDay()->toDateString();
        }

        $lastOfLastDate = Carbon::parse($lastDate)->endOfMonth()->toDateString();
    	$firstDayCurrent = Carbon::now()->startOfMonth()->toDateString();
    	$leftDate = $lastDate >= date('Y-m').'-01'?date('Y-m-d'):$lastOfLastDate;

        $jobcard = '<div class="alert alert-danger " role="alert" style="background-color: #fff5f4 !important;"><p class="iq-alert-text">Last working day of this employee  was <b> '.$lastDate .'</b>. Effective date will be <b>'.$effectiveDate.'</b> <br>If left, effective date will be <b>'.$leftDate.'</b> <br><span style="font-size:10px;">*Including holiday & leave</span></p></div>';
        $jobcard .= view('hr/reports/job_card/report', $result)->render();

    	return (object) [
    		'job_card' => $jobcard,
    		'last_date' => $lastDate,
    		'left_date' => $leftDate,
    		'effective_date' => $effectiveDate
    	];
    }

    protected function getDateIfHoliday($date, $holidays)
    {
        $nDate = Carbon::parse($date)->addDay()->toDateString();
        if(in_array($nDate, $holidays->toArray())){
            $date = $nDate;
            return $this->getDateIfHoliday($date, $holidays);
        }
        return $date;
    }


    public function getSalaryLockStatus($yearMonth, $unit)
    {
    	$exp = explode('-',$yearMonth);
    	$params = [
    		'unit_id' => $unit,
    		'month' => $exp[1],
    		'year' => $exp[0]
    	];
    	return monthly_activity_close($params);
    }


    public function getSalaryVoucher($employee, $yearMonth)
    {
    	$voucher = '';
    	$exp = explode("-",$yearMonth);
    	$salary = DB::table('hr_monthly_salary')
    		->where('as_id', $employee->associate_id)
    		->where('year', $exp[0])
    		->where('month', $exp[1])
    		->first();

    	if($salary){

    		$voucher = view('hr.common.partial_salary_sheet', compact('salary','employee'))->render();
    	}
    	return $voucher;
    }


    public function getMonthlyAttendance($associateId, $yearMonth)
    {
    	$params = [
    		'associate' => $associateId,
    		'month_year' => $yearMonth
    	];

    	return $this->jobCardRepository->jobCardByMonth($params);
    }

    public function fetchBenefits($associateId)
    {

    }

    public function getJobDuration($joinDate, $lastDate)
    {
    	$days = Carbon::parse($lastDate)->diffInDays(Carbon::parse($joinDate));

    	$years = (int) ($days/365);
    	$days  = $days%365;
    	$months = (int) ($days/30);
    	$days = $days%30;
    	return (object)[
    		'years' => $years,
    		'months' => $months,
    		'days' => $days
    	];
    }
}