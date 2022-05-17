<?php

namespace App\Repository\Hr;

use App\Exports\Hr\AttendanceSummaryExport;
use App\Jobs\ProcessAttendanceFile;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Employee;
use App\Models\Hr\Absent;
use App\Models\Hr\AttendanceHistory;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\Leave;
use App\Models\Hr\Shift;
use App\Models\Hr\YearlyHolyDay;
use App\Repository\Hr\AttendanceRepository;
use App\Repository\Hr\EmployeeRepository;
use App\Repository\Hr\ShiftRepository;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Facades\Excel; 

class AttDataProcessRepository 
{

	protected $shiftRepository;
	protected $attendanceRepository;
	protected $overtimeRepository;

	public function __construct(AttendanceRepository $attendanceRepository, ShiftRepository $shiftRepository, OvertimeRepository $overtimeRepository)
	{
		ini_set('zlib.output_compression', 1);
		$this->shiftRepository 	  = $shiftRepository;
		$this->attendanceRepository   = $attendanceRepository;
		$this->overtimeRepository 	  = $overtimeRepository;
	}

	protected function setDate($date)
	{
		$this->date = isset($date)?$date:date('Y-m-d');
	}

	public function initiate($punch)
	{
		try {
			$startDate = date('Y-m-d', strtotime('-1 day', strtotime($punch->punch_time)));
			$endDate = date('Y-m-d', strtotime('+1 day', strtotime($punch->punch_time)));

			//$getLeave = 
			
			$getHoliday = $this->attendanceRepository->getHolidays($punch->employee, $startDate, $endDate);

		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	public function attendanceReCallHistory($asId, $recordDate)
    {
        // get two days attendance 

        try {
            $today = date('Y-m-d', strtotime($recordDate));
            $tomorrow  = date('Y-m-d', strtotime($today. ' +1 day'));
            $getAttendance = AttendanceHistory::where('as_id', $asId)
            ->whereBetween('att_date', [$today, $tomorrow])
            ->get();
            $as_info = Employee::where('as_id', $asId)->first();
            $unit = $as_info->as_unit_id;
            $getAtt = '';
            $unitId = $as_info->as_unit_id;
            $tableName = get_att_table($unitId);
            $queueName = 'default';
            // return ($getAttendance);
            if(count($getAttendance) > 0 && $as_info != null){
                
                if($as_info->as_unit_id == 2){
                    $queueName = 'ceilatt';
                }
                $data = [];
                foreach ($getAttendance as $att) {
                    $checktime = $att->raw_data;
                    $punch_date = date('Y-m-d', strtotime($checktime));
                    $day_of_date = date('j', strtotime($checktime));
                    $day_num = "day_".$day_of_date;
                    $month = date('m', strtotime($checktime));
                    $year = date('Y', strtotime($checktime));

                    if($as_info->as_ot == 1 && in_array($unitId, [1, 4, 5])){
                       if(!in_array($as_info->as_unit_id, [1,4,5])){
                            $msg[] = $as_info->associate_id." - ".$today." This employee assign other unit";
                            continue;
                        }
                    }else if($as_info->as_ot == 1 && $as_info->as_unit_id != $unitId){
                        $msg[] = $as_info->associate_id." - ".$today." This employee assign other unit";
                        continue;
                    }
                    // check lock month
                    $checkL['month'] = $month;
                    $checkL['year'] = $year;
                    $checkL['unit_id'] = $as_info->as_unit_id;
                    $checkLock = monthly_activity_close($checkL);
                    if($checkLock == 1){
                        $msg[] = $as_info->associate_id." - ".$today." Month Activity Lock";
                        continue;
                    }
                    $today = date("Y-m-d", strtotime($checktime));

                     //dd($today);exit;
                    // leave check individual
                    $getLeave = DB::table('hr_leave')
                    ->where('leave_ass_id', $as_info->associate_id)
                    ->where('leave_from', '<=', $today)
                    ->where('leave_to', '>=', $today)
                    ->where('leave_status',1)
                    ->first();

                    if($getLeave != null){
                        $msg[] = $as_info->associate_id." - ".$today." Leave for this employee";
                        continue;
                    }
                    $checkHolidayFlag = 0;
                    // check holiday individual
                    $getHoliday = HolidayRoaster::getHolidayYearMonthAsIdDateWise($year, $month, $as_info->associate_id, $today);
                     //dd($getHoliday);exit;
                    if($getHoliday != null && $getHoliday->remarks == 'Holiday'){
                        $checkHolidayFlag = 1;
                        // $msg[] = $as_info->associate_id." - ".$today." Holiday for roster this employee";
                        // continue;
                    }else if($getHoliday == null){
                        if($as_info->shift_roaster_status == 0){
                            $getYearlyHoliday = YearlyHolyDay::getCheckUnitDayWiseHoliday($as_info->as_unit_id, $today);
                             //dd($getYearlyHoliday);exit;
                            if($getYearlyHoliday != null && $getYearlyHoliday->hr_yhp_open_status == 0){
                                $checkHolidayFlag = 1;
                                // $msg[] = $as_info->associate_id." - ".$today." Holiday for this employee";
                                // continue;
                            }
                        }
                    }

                    $shift= DB::table("hr_shift_roaster")
                    ->where('shift_roaster_month', $month)
                    ->where('shift_roaster_year', $year)
                    ->where("shift_roaster_user_id", $as_info->as_id)
                    ->select([
                        $day_num,
                        'hr_shift.hr_shift_name',
                        'hr_shift.hr_shift_id',
                        'hr_shift.hr_shift_start_time',
                        'hr_shift.hr_shift_end_time',
                        'hr_shift.hr_shift_code',
                        'hr_shift.hr_shift_break_time',
                        'hr_shift.hr_shift_night_flag',
                        'hr_shift.bill_eligible'

                    ])
                    ->leftJoin('hr_shift', function($q) use($day_num, $unitId) {
                        $q->on('hr_shift.hr_shift_name', 'hr_shift_roaster.'.$day_num);
                        $q->where('hr_shift.hr_shift_unit_id', $unitId);
                    })
                    ->orderBy('hr_shift.hr_shift_id', 'desc')
                    ->first();

                    if(!empty($shift) && $shift->$day_num != null){
                        $shift_name= $shift->hr_shift_name;
                        $shift_code= $shift->hr_shift_code;
                        $shift_start_time= $shift->hr_shift_start_time;
                        $shift_end_time= $shift->hr_shift_end_time;
                        $shift_break_time= $shift->hr_shift_break_time;
                        $shift_night_flag = $shift->hr_shift_night_flag;
                        $billEligible = $shift->bill_eligible;
                    }
                    else{
                        $shift_name= $as_info->shift['hr_shift_name'];
                        $shift_code= $as_info->shift['hr_shift_code'];
                        $shift_start_time= $as_info->shift['hr_shift_start_time'];
                        $shift_end_time= $as_info->shift['hr_shift_end_time'];
                        $shift_break_time= $as_info->shift['hr_shift_break_time'];
                        $shift_night_flag = $as_info->shift['hr_shift_night_flag'];
                        $billEligible = $as_info->shift['bill_eligible'];
                    }

                    $shift_start = $punch_date." ".$shift_start_time;
                    $shift_end = $punch_date." ".$shift_end_time;
                    $shift_in_time = Carbon::createFromFormat('Y-m-d H:i:s', $shift_start);
                    $shift_out_time = Carbon::createFromFormat('Y-m-d H:i:s', $shift_end);
                
                    if($shift_out_time < $shift_in_time){
                        $shift_out_time = $shift_out_time->copy()->addDays(1);
                    }

                    //shift start range
                    $shift_start_begin = $shift_in_time->copy()->subHours(2); // 2 hour
                    $shift_start_end = $shift_in_time->copy()->addHours(4); //4 hour
                    //shift end rage
                    $shift_end_begin = $shift_start_end->copy()->addSeconds(1); // after 4 hour
                    // $shift_end_end= $shift_out_time+28800; // 8 hour OT calculate in previous system
                    // $shift_end_end= $shift_end_begin+68399; // 18 hour 59 minute 59 second

                    $shiftDiff = strtotime($shift_out_time) - strtotime($shift_in_time); //total shift time
                    $totalShiftDiff = $shiftDiff + ($shift_break_time*60) + 7500; // 7500 = 2:05 extra allow time 
                    $otAllow = 90000 - $totalShiftDiff; //86400 = 24 hour

                    // $otAllow = 46000 - ($shift_break_time*60);// 13- hour 00 minute 00 second
                    $shift_end_end = $shift_out_time->copy()->addSeconds($otAllow);
         
                    //check time
                    $check_time = Carbon::createFromFormat('Y-m-d H:i:s', $checktime);
                    //get existing punch
                    $last_punch= DB::table($tableName)
                    ->where('as_id', $as_info->as_id)
                    ->where('in_date', '=', $check_time->format('Y-m-d'))
                    ->first();
                    if($last_punch){
                        if((($shift_start_begin >= $last_punch->in_time || $last_punch->in_time >= $shift_start_end)  && $last_punch->in_time != null) || $last_punch->remarks == 'DSI'){
                            if($last_punch->out_time != null){   
                                DB::table($tableName)->where('id', $last_punch->id)->update([
                                        'in_time' => null, 'ot_hour' => 0, 'late_status' => 0]);
                            }else{
                                DB::table($tableName)->where('id', $last_punch->id)->delete();
                            }
                            $last_punch = DB::table($tableName)->where('id', $last_punch->id)->first();


                        }
                        if(($shift_end_begin >= $last_punch->out_time || $last_punch->out_time >= $shift_end_end ) && $last_punch->out_time != null){
                            if($last_punch->in_time != null){ 
                                DB::table($tableName)->where('id', $last_punch->id)->update([
                                    'out_time' => null, 'ot_hour' => 0]);
                            }else{
                                DB::table($tableName)->where('id', $last_punch->id)->delete();
                            }
                            $last_punch = DB::table($tableName)->where('id', $last_punch->id)->first();

                        }
                    }
                    if($shift_start_begin <= $check_time && $check_time <= $shift_start_end  && $checkHolidayFlag == 0){
                        // $checkInTimeFlag = 0;
                        if(empty($last_punch)){
                            $punchId = DB::table($tableName)
                            ->insertGetId([
                                'as_id' => $as_info->as_id,
                                'in_date' => date('Y-m-d', strtotime($checktime)),
                                'in_time' => $checktime,
                                'hr_shift_code' => $shift_code,
                                'in_unit' => $unit,
                                'remarks' => ''
                            ]);

                            // $checkInTimeFlag = 1;

                        }else{
                            $lastInTime = $last_punch->in_time;
                            $newInTime = $checktime;
                            if($newInTime <= $lastInTime || $last_punch->remarks == 'DSI' || $last_punch->in_time == null){
                                $punchId = $last_punch->id;
                                DB::table($tableName)
                                ->where('id', $last_punch->id)
                                ->where('as_id', $as_info->as_id)
                                ->update([
                                    'in_time' => $newInTime,
                                    'in_unit' => $unit,
                                    'hr_shift_code' => $shift_code,
                                    'remarks' => ''
                                ]);
                                
                                // $checkInTimeFlag = 2;
                            }

                        }

                    }
                    else if(($shift_end_begin <= $check_time) && ($check_time <= $shift_end_end)  && $checkHolidayFlag == 0){
                        if(!empty($last_punch)){
                            $punchId = $last_punch->id;
                            // $checkOutTimeFlag = 0;
                            if($last_punch->out_time == null){
                                DB::table($tableName)
                                ->where('id', $last_punch->id)
                                ->where('as_id', $as_info->as_id)
                                ->update([
                                    'out_time' => $checktime,
                                    'out_unit' => $unit
                                ]);



                                // $checkOutTimeFlag = 1;
                            }else{
                                $lastOutTime = $last_punch->out_time;
                                $newOutTime = $checktime;
                                if($newOutTime >= $lastOutTime ){
                                    DB::table($tableName)
                                    ->where('id', $last_punch->id)
                                    ->where('as_id', $as_info->as_id)
                                    ->update([
                                        'out_time' => $newOutTime,
                                        'out_unit' => $unit
                                    ]);

                                    // $checkOutTimeFlag = 1;
                                }
                            }

                        }
                        else{
                            $punchId = DB::table($tableName)
                            ->insertGetId([
                                'as_id' => $as_info->as_id,
                                'in_date' => date('Y-m-d', strtotime($shift_start)),
                                'in_time' => date('Y-m-d H:i:s', strtotime('+4 minutes', strtotime($shift_start))),
                                'out_time' => $checktime,
                                'hr_shift_code' => $shift_code,
                                'remarks'       => "DSI",
                                'in_unit' => $unit,
                                'out_unit' => $unit
                            ]);
                        }
                    }
                    else{
                        $shift_code_new=null;
                        if($day_of_date == 1){
                            $day_of_date = $check_time->copy()->subDays(1);
                            $day_of_date= $day_of_date->format('j');
                            if($month ==1){
                                $month=12;
                                $year= $year-1;
                            }else{
                                $month= $month-1;
                            }
                            $day_num= "day_".($day_of_date);
                        }else{
                            $day_num= "day_".($day_of_date-1);
                        }

                        // check lock month
                        $checkLP['month'] = $month;
                        $checkLP['year'] = $year;
                        $checkLP['unit_id'] = $as_info->as_unit_id;
                        $checkLockP = monthly_activity_close($checkLP);
                        if($checkLockP == 1){
                            $msg[] = $as_info->associate_id." - ".$today." Month Activity Lock";
                            continue;
                        }
                            
                        // return $day_num;
                        $shift= DB::table("hr_shift_roaster")
                        ->where('shift_roaster_month', $month)
                        ->where('shift_roaster_year', $year)
                        ->where("shift_roaster_user_id", $as_info->as_id)
                        ->orderBy('shift_roaster_id', "DESC")
                        ->select([
                            $day_num,
                            'hr_shift.hr_shift_start_time',
                            'hr_shift.hr_shift_end_time',
                            'hr_shift.hr_shift_name',
                            'hr_shift.hr_shift_code',
                            'hr_shift.hr_shift_night_flag',
                            'hr_shift.hr_shift_break_time',
                            'hr_shift.bill_eligible'
                        ])
                        ->leftJoin('hr_shift', function($q) use($day_num, $unitId) {
                            $q->on('hr_shift.hr_shift_name', 'hr_shift_roaster.'.$day_num);
                            $q->where('hr_shift.hr_shift_unit_id', $unitId);
                        })
                        ->orderBy('hr_shift.hr_shift_id', 'desc')
                        ->first();

                        if(!empty($shift) && $shift->$day_num != null){
                            $shift_name= $shift->hr_shift_name;
                            $shift_code_new= $shift->hr_shift_code;
                            $shift_start_time= $shift->hr_shift_start_time;
                            $shift_end_time= $shift->hr_shift_end_time;
                            $shift_night_flag= $shift->hr_shift_night_flag;
                            $shift_break_time = $shift->hr_shift_break_time;
                            $billEligible = $shift->bill_eligible;
                        }
                        else{
                            $defaultshift = $as_info->shift;
                            $shift_name= $defaultshift['hr_shift_name'];
                            $shift_code_new= $defaultshift['hr_shift_code'];
                            $shift_start_time= $defaultshift['hr_shift_start_time'];
                            $shift_end_time= $defaultshift['hr_shift_end_time'];
                            $shift_night_flag= $defaultshift['hr_shift_night_flag'];
                            $shift_break_time = $defaultshift['hr_shift_break_time'];
                            $billEligible = $defaultshift['bill_eligible'];
                        }

                        
                        $last_punch= DB::table($tableName)
                        ->where('as_id', $as_info->as_id)
                        ->where('in_date', $check_time->copy()->subDays(1)->format('Y-m-d'))
                        ->orderBy('id', "DESC")
                        ->first();
                        // return $check_time->copy()->subDays(1)->format('Y-m-d');
                        $outPunchDate = $punch_date;
                        if($last_punch != null){
                            $punch_date = date('Y-m-d', strtotime($last_punch->in_date));
                        }else{
                            if($shift_night_flag == 1){
                                $punch_date = $check_time->copy()->subDays(1)->format('Y-m-d');
                            }
                        }

                        $shift_start = $punch_date." ".$shift_start_time;
                        $shift_end = $punch_date." ".$shift_end_time;

                        $shift_in_time_new = Carbon::createFromFormat('Y-m-d H:i:s', $shift_start);

                        $shift_out_time_new = Carbon::createFromFormat('Y-m-d H:i:s', $shift_end);
                        // return $shift_in_time_new.' '.$shift_out_time_new;
                    
                        if($shift_out_time_new < $shift_in_time_new){
                            $shift_out_time_new = $shift_out_time_new->copy()->addDays(1);
                        }

                        //shift start range
                        $shift_start_begin_new = $shift_in_time_new->copy()->subHours(2); // 2 hour
                        $shift_start_end_new = $shift_in_time_new->copy()->addHours(4); //4 hour
                        //shift end rage
                        $shift_end_begin_new = $shift_start_end_new->copy()->addSeconds(1); // after 4 hour
                        // $shift_end_end= $shift_out_time_new+28800; // 8 hour OT calculate in previous system
                        // $shift_end_end= $shift_end_begin+68399; // 18 hour 59 minute 59 second
                        $shiftDiffNew = strtotime($shift_out_time_new) - strtotime($shift_in_time_new); //total shift time
                        $totalShiftDiffNew = $shiftDiffNew + ($shift_break_time*60) + 7500; // 7500 = 2:05 extra allow time 
                        $otAllow = 90000 - $totalShiftDiffNew; //86400 = 25 hour
                        // $otAllow = 46000 - ($shift_break_time*60);// 13- hour 00 minute 00 second
                        $shift_end_end_new = $shift_out_time_new->copy()->addSeconds($otAllow);
                        if($checkHolidayFlag == 1){
                            $shift_end_end_new = $shift_end_end_new->copy()->addHours(6);
                        }
                        
                        //check time
                        $check_time = Carbon::createFromFormat('Y-m-d H:i:s', $checktime);

                        if($last_punch){
                            if((($shift_start_begin_new >= $last_punch->in_time || $last_punch->in_time >= $shift_start_end_new)  && $last_punch->in_time != null) || $last_punch->remarks == 'DSI'){
                                if($last_punch->out_time != null){ 
                                    DB::table($tableName)->where('id', $last_punch->id)->update([
                                        'in_time' => null, 'ot_hour' => 0, 'late_status' => 0]);
                                }else{
                                    DB::table($tableName)->where('id', $last_punch->id)->delete();
                                }

                                $last_punch = DB::table($tableName)->where('id', $last_punch->id)->first();


                            }
                            if(($shift_end_begin_new >= $last_punch->out_time || $last_punch->out_time >= $shift_end_end_new ) && $last_punch->out_time != null){
                                if($last_punch->in_time != null){ 
                                    DB::table($tableName)->where('id', $last_punch->id)->update([
                                        'out_time' => null, 'ot_hour' => 0]);
                                }else{
                                    DB::table($tableName)->where('id', $last_punch->id)->delete();
                                }

                                $last_punch = DB::table($tableName)->where('id', $last_punch->id)->first();

                            }
                        }
                        if($shift_end_begin_new <= $check_time && $check_time <= $shift_end_end_new){
                            if(!empty($last_punch)){
                                $punchId = $last_punch->id;
                                // $checkOutTimeFlag = 0;
                                if($last_punch->out_time == null){
                                    $hi = DB::table($tableName)
                                    ->where('id', $last_punch->id)
                                    ->update([
                                        'out_time' => $checktime,
                                        'out_unit' => $unit
                                    ]);


                                    // $checkOutTimeFlag = 1;
                                }else{
                                    $lastOutTime = $last_punch->out_time;
                                    $newOutTime = $checktime;
                                    if($newOutTime >= $lastOutTime){
                                        DB::table($tableName)
                                        ->where('id', $last_punch->id)
                                        ->where('as_id', $as_info->as_id)
                                        ->update([
                                            'out_time' => $newOutTime,
                                            'out_unit' => $unit
                                        ]);

                                        // $checkOutTimeFlag = 1;
                                    }
                                }
                            }else{
                                if(!empty($shift_code_new) && $checkHolidayFlag == 0 && $checkLeaveFlag == 0){
                                    $defaultInTime = date('Y-m-d H:i:s', strtotime('+4 minutes', strtotime($shift_start)));
                                    $punchId = DB::table($tableName)
                                    ->insertGetId([
                                        'as_id' => $as_info->as_id,
                                        'in_date' => date('Y-m-d', strtotime($defaultInTime)),
                                        'in_time' => $defaultInTime,
                                        'out_time'      => $checktime,
                                        'hr_shift_code' => $shift_code_new,
                                        'remarks'       => "DSI",
                                        'in_unit' => $unit,
                                        'out_unit' => $unit
                                    ]);
                                }
                            }
                        }elseif(!empty($last_punch) && $check_time <= $shift_end_end_new && ($checkHolidayFlag == 1 || $checkLeaveFlag == 1)){
                            $punchId = $last_punch->id;
                            DB::table($tableName)
                            ->where('id', $last_punch->id)
                            ->where('as_id', $as_info->as_id)
                            ->update([
                                'out_time' => $checktime,
                                'out_unit' => $unit
                            ]);
                        }

                    }
                }
            }

            if($as_info != null){
                $getAtt = DB::table($tableName)
                ->select('id','as_id', 'in_date', 'in_time', 'out_time', 'ot_hour', 'hr_shift_code')
                ->where('as_id', $asId)
                ->where('in_date', $recordDate)
                ->first();

                if(count($getAttendance) == 0 && $getAtt != null){
                    $checktime = $recordDate;
                    $punch_date = date('Y-m-d', strtotime($checktime));
                    $day_of_date = date('j', strtotime($checktime));
                    $day_num = "day_".$day_of_date;
                    $month = date('m', strtotime($checktime));
                    $year = date('Y', strtotime($checktime));

                    $shift= DB::table("hr_shift_roaster")
                    ->where('shift_roaster_month', $month)
                    ->where('shift_roaster_year', $year)
                    ->where("shift_roaster_user_id", $as_info->as_id)
                    ->orderBy('shift_roaster_id', "DESC")
                    ->select([
                        $day_num,
                        'hr_shift.hr_shift_start_time',
                        'hr_shift.hr_shift_end_time',
                        'hr_shift.hr_shift_name',
                        'hr_shift.hr_shift_code',
                        'hr_shift.hr_shift_night_flag',
                        'hr_shift.hr_shift_break_time',
                        'hr_shift.bill_eligible'
                    ])
                    ->leftJoin('hr_shift', function($q) use($day_num, $unitId) {
                        $q->on('hr_shift.hr_shift_name', 'hr_shift_roaster.'.$day_num);
                        $q->where('hr_shift.hr_shift_unit_id', $unitId);
                    })
                    ->orderBy('hr_shift.hr_shift_id', 'desc')
                    ->first();

                    if(!empty($shift) && $shift->$day_num != null){
                        $shift_code_new= $shift->hr_shift_code;
                    }
                    else{
                        $shift_code_new= $as_info->shift['hr_shift_code'];
                    }

                    DB::table($tableName)
                        ->where('as_id', $asId)
                        ->where('in_date', $recordDate)
                        ->update(['hr_shift_code' => $shift_code_new]);

                    $getAtt->hr_shift_code = $shift_code_new;
                }

                $otHour = 0;
                if($getAtt != null){
                    $getShift = $this->shiftRepository->getEmployeeShiftByDate($as_info->associate_id, $getAtt->in_date);
                    $shift = $getShift->time;
                    
                    
                    if($shift != null){
                        if($as_info->as_ot == 1 && $getAtt->out_time != null && $getAtt->in_time != null){
                            // $otHour = EmployeeHelper::daliyOTCalculation($getAtt->in_time, $getAtt->out_time, $shift->hr_shift_start_time, $shift->hr_shift_end_time, $shift->hr_shift_break_time, $shift->hr_shift_night_flag, $as_info->associate_id, $as_info->shift_roaster_status, $as_info->as_unit_id);

                            
                            $punch = (object)$getAtt;
                            $otHour = $this->overtimeRepository->calculateOvertime($punch, $as_info, $getShift);
                        }

                        DB::table($tableName)
                        ->where('id', $getAtt->id)
                        ->update(['ot_hour' => $otHour]);

                        // 
                        $queue = (new ProcessAttendanceFile($tableName, $getAtt->id, $shift->hr_shift_name, $shift->hr_shift_start_time, '', $shift->hr_shift_night_flag))
                        ->onQueue($queueName)
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);

                        $firstDateMonth = Carbon::parse($recordDate)->startOfMonth()->toDateString();
                        $lastDateMonth = Carbon::parse($recordDate)->endOfMonth()->toDateString();
                        $getOt = DB::table($tableName)
                        ->select([
                            DB::raw('SUM(ot_hour) as ot')
                        ])
                        ->where('as_id', $asId)
                        ->where('in_date','>=',$firstDateMonth)
                        ->where('in_date','<=', $lastDateMonth)
                        ->first();
                        
                        $getAtt->ot_hour = numberToTimeClockFormat($otHour);
                        $getAtt->in_time = $getAtt->in_time != null?date('H:i:s', strtotime($getAtt->in_time)):'';
                        $getAtt->out_time = $getAtt->out_time != null?date('H:i:s', strtotime($getAtt->out_time)):'';
                        $getAtt->totalOt = numberToTimeClockFormat($getOt->ot??0);
                    }else{
                        $getAtt = '';
                    }
                }
                
                // salary process
                $yearMonth = date('Y-m', strtotime($recordDate));
                $month = date('m', strtotime($recordDate)); 
                $year = date('Y', strtotime($recordDate)); 
                if($month == date('m')){
                    $totalDay = date('d');
                }else{
                    $totalDay = Carbon::parse($yearMonth)->daysInMonth;
                }
                $queue = (new ProcessUnitWiseSalary($tableName, $month, $year, $as_info->as_id, $totalDay))
                ->onQueue('salarygenerate')
                ->delay(Carbon::now()->addSeconds(2));
                dispatch($queue);
            }
            return $getAtt;
        } catch (\Exception $e) {
            return $e->getMessage();
            return 'error';
        }
    }
}