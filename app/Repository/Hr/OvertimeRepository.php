<?php

namespace App\Repository\Hr;

use App\Contracts\Hr\SalaryInterface;
use App\Models\Employee;
use App\Models\Hr\AttendanceBonusConfig;
use App\Models\Hr\Benefits;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\SalaryAddDeduct;
use App\Models\Hr\SalaryAdjustMaster;
use App\Repository\Hr\AttendanceProcessRepository;
use Carbon\Carbon;
use DB;

class OvertimeRepository
{

	protected $shiftRepository;

	protected $attendanceRepository;


	public function __construct(ShiftRepository $shiftRepository, AttendanceRepository $attendanceRepository)
	{
		$this->shiftRepository = $shiftRepository;
		$this->attendanceRepository = $attendanceRepository;
	}

	/**
     * calculate overtime based on rules
     *
     * @param  Object  $punch 
     * @param  string  $associateId
     * @return float   overtime amount in hours
     */

	public function calculateOvertime($punch, $employee = null, $shift = null)
	{
		$date      = $punch->in_date;
		$dateOut   = $punch->in_date;

		// if employee not exist, fetch employee information
		if($employee == null){
			$employee  = Employee::where('as_id', $punch->as_id)->first();
		}

		// get todays shift information, if shift not exist
		if($shift == null){
			$shift 	   = $this->shiftRepository->getShiftPropertiesByTodaysSingleCode($punch->hr_shift_code, $date);
		}

		$shiftIntime = date('Y-m-d H:i:s', strtotime($date.' '.$shift->time->hr_shift_start_time));

		/*
		 * check employee working status for today 
		 * output: Leave/General/Holiday/OT
		 */
		$otCheck = $this->attendanceRepository->getTodayEmployeeStatus($employee->associate_id,$employee->shift_roaster_status, $date);
		
		/* 
		 * Get default break properties
		 * Object
				->break
				->break_start
				->hr_shift_out_time
		 */
		$break_properties  = $this->getBreakProperties($shift, $date, $employee->as_designation_id);
		
		
		if($shift->time->hr_shift_night_flag == 1){
			$dateOut = Carbon::parse($date)->addDay()->toDateString();
		}
		$shiftOutTime = date('Y-m-d H:i:s', strtotime($dateOut.' '.$break_properties->hr_shift_out_time));

		// OT will count after 10 minutes
		$shiftOutWithOtLimit = Carbon::parse($shiftOutTime)->addMinutes(10)->format('Y-m-d H:i:s');

		// if employee has not assigned OT and punched before shift end
		if( $punch->out_time < $shiftOutWithOtLimit && $otCheck != 'OT'){
			return 0;
		}
		
		// add custom break 1 hour 
		$totalBreak = $this->getExtraBreakMinutes($shift, $punch, $date);

		$startTime  = $shiftOutTime;
		$endTime    = $punch->out_time;

		// if today is declared as Full Day OT
		if($otCheck == 'OT'){
			// break based on punch time
			$mainBreak = $this->getBreakForFullDayOt($shift->time->hr_shift_start_time, $punch, $break_properties);
			$totalBreak += $mainBreak;
			// start time will be in punch
			$startTime = $punch->in_time;
			$startTime = $startTime < $shiftIntime?$shiftIntime:$startTime;

		}
		$minutes  = $this->toOtSeconds($startTime, $endTime, $totalBreak);


		// please add default break also, if you want to pass break data
		return $this->toOtHours($minutes);
	}


	

	/**
     * get default break properties based on custom rules
     *
     * @param  Object  $shift 
     * @param  date    $date   | to extract day name
     * @param  int     $designationId   | employee designation id
     * @return Object
     */

	public function getBreakProperties($shift, $date, $designationId)
	{
		$day   = date('D', strtotime($date));
		$rules = collect($shift->break_rules)->filter(function($q) use ($day, $designationId){
			$days = ($q->days)?explode(",", $q->days):[];
			$designations = ($q->designations)?explode(",", $q->designations):[];
			return (in_array($day, $days)) && (empty($designations) || in_array($designationId, $designations));
		})->first();


		if($rules){
			$break = $rules->break_time ;
			return (object) [
				'break' => $break,
				'break_start' => ($rules->break_time_start != null? $rules->break_time_start: $shift->time->hr_break_start_time),
				'hr_shift_out_time' => $this->shiftRepository->calculateShiftOutTime($shift->time->hr_shift_end_time, $break)
			];
		}else{
			return (object) [
				'break' => $shift->time->hr_shift_break_time,
				'break_start' => $shift->time->hr_break_start_time,
				'hr_shift_out_time' => $shift->time->hr_shift_out_time
			];

		}

	}

	/**
     * get extra break time, this break will act after shift ending 
     *
     * @param  Object  $break   | extra breaks from hr_shift_extra_break
     * @param  datetime $outPunch | break properties
     * @return int              | total break in minutes
     */
	public function getExtraBreakMinutes($shift, $punch, $date)
	{
		
		$break = 0;
		// if unit 2 & 3 add 1 hour extra break after shift+break+8 hour
		if(($shift->time->hr_shift_unit_id == 2 || $shift->time->hr_shift_unit_id == 3) && $punch->out_time ){
			if($shift->time->hr_shift_night_flag == 1){
				$date = Carbon::parse($date)->addDay()->toDateString();
			}
			$time = $date.' '.$shift->time->hr_shift_out_time;
			$shiftOutTime = Carbon::parse($time)->format('Y-m-d H:i:s');

			// Extra break properties
			$shiftBreakStart = Carbon::parse($shiftOutTime)->addHours(8)->format('Y-m-d H:i:s');
			//breakMinute 60
			$shiftBreakEnd = Carbon::parse($shiftBreakStart)->addMinutes(60)->format('Y-m-d H:i:s');
			// now check if $punch outtime crossed the shift break

			
			if($punch->out_time > $shiftBreakStart){

				$break = $punch->out_time > $shiftBreakEnd ? 60: $this->toOtMinutes($punch->out_time, $shiftBreakStart);
			}

		}
		return $break;
	}

	/**
     * get default break time for full day ot duty
     *
     * @param  time    $shiftStart  | shift start time 
     * @param  Object  $punch   | punch record from attendance table
     * @param  Object  $break   | break properties
     * @return int              | total break in minutes
     */

	public function getBreakForFullDayOt($shiftStart, $punch, $break)
	{
		$date = $punch->in_date;
		if($break->break_start == null){
			$break->break_start = Carbon::parse($date.' '.$shiftStart)->addHours(6)->format('H:i:s');
		}
		if($shiftStart > $break->break_start){
			// add one day
			$date = Carbon::parse($date)->addDay()->toDateString();
		}
		$breakStart = Carbon::parse($date.' '.$break->break_start)->format('Y-m-d H:i:s');
		$breakEnd   = Carbon::parse($breakStart)->addMinutes($break->break)->format('Y-m-d H:i:s');

		// in punch after break or out punch before break
		if($punch->in_time <= $breakStart && $punch->out_time >= $breakEnd){
			return $break->break;
		}

		// if in punch and out punch both in break period | break = out_punch - in_punch
		if($punch->in_time > $breakStart && $punch->out_time < $breakEnd){
			return $this->toOtMinutes($punch->out_time, $punch->in_time);
		}

		// if in punch is middle of break time    break = break_end - in_punch
		if($punch->in_time >= $breakStart && $punch->in_time <= $breakEnd){
			return $this->toOtMinutes($breakEnd, $punch->in_time);
		}

		// if out punch is middle of break time   break = out_punch - break_start
		if($punch->out_time >= $breakStart && $punch->out_time <= $breakEnd){
			return $this->toOtMinutes($punch->out_time, $breakStart);
		}

		return 0;
	}

	/**
     * convert ot hour into minutes based on rules
     *
     * @param  int     $minutes   | total ot minute
     * @return float   overtime amount in hours
     */

	public function toOtHours($seconds)
	{
		$minexp  = explode('.', ($seconds/3600));
		$minutes = (isset($minexp[1]) ? $minexp[1] : 0);
		$hour    = floatval('0.'.$minutes);

		if($hour > 0.16667 && $hour <= 0.75) $hour = $hour;
	    else if($hour >= 0.75) $hour = 1;
	    else $hour = 0;

	    
		// if($hour >= 0.75) $hour = 1;
	    
	    if($hour > 0 && $hour < 1){
	    	$inMin = (int)round($hour*60);
	    	$min_to_ot = min_to_ot();
	    	$hour = $min_to_ot[$inMin]??0;
	    }

	    $overtime = $minexp[0]+$hour;

	    return number_format((float)$overtime, 3, '.', '');
	}

	/**
     * difference between to date in minutes
     *
     * @param  datetime     $startTime   
     * @param  datetime     $endTime 
     * @return int    in minute
     */

	public function toOtMinutes($startTime, $endTime)
	{
		$startTime = Carbon::parse($startTime);
		$endTime   = Carbon::parse($endTime);

		return $endTime->diffInMinutes($startTime);
	}

	/**
     * difference between to date in minutes
     *
     * @param  datetime     $startTime   
     * @param  datetime     $endTime 
     * @return int    in minute
     */

	public function toOtSeconds($startTime, $endTime, $break=null)
	{
		$startTime = Carbon::parse($startTime);
		if($break != null){
			$startTime = Carbon::parse($startTime)->addMinutes($break);
		}
		$endTime   = Carbon::parse($endTime);

		return $endTime->diffInSeconds($startTime);
	}
}