<?php

namespace App\Http\Controllers\Hr\TimeAttendance;

use App\Helpers\Custom;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessAttendanceIntime;
use App\Jobs\ProcessAttendanceOuttime;
use App\Jobs\ProcessEmployeeAbsent;
use App\Models\Employee;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\YearlyHolyDay;
use Carbon\Carbon;
use App\Helpers\EmployeeHelper;
use App\Models\Hr\Bills;
use DB, Validator, Input, FastExcel, File;
use Illuminate\Http\Request;

class AttendanceFileProcessController extends Controller
{
    public function importFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unit' => 'required|min:1|max:11',
            'file' => 'required',
            'device' => 'required_if:unit,==,3'
        ]);
        $input = $request->all();
        if ($validator->fails())
        {
            return back()
            ->withErrors($validator)
            ->withInput();
        }

        try {
            $input = $request->all();
            $data['unit'] = $input['unit'];
            $data['device'] = $input['device'];
            $fileData = file_get_contents($request->file('file'));
            $dataResult = explode(PHP_EOL, $fileData);
            $dataResult = mb_convert_encoding($dataResult, 'UTF-8', 'UTF-8');
            $checkData = json_encode($dataResult);
            if(empty($checkData)){
                toastr()->error('There is error in your file');
                return back();
            }
            $dataChunk = array_chunk($dataResult, 50);
            $data['arrayDataCount'] = count($dataResult);
            $data['chunkValues'] = $dataChunk;
            // dd($dataChunk);
            return view('hr.timeattendance.att_status', $data);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return redirect()->back();
        }
    }

    public function attFileProcess(Request $request)
    {
        $data = array();
        $data['status'] = 'success';
        $fileDate = array();
        $msg = array();
        $input = $request->all();
        $unit = $input['unit'];
        try {
            foreach($input['getdata'] as $key => $value) {
                $lineData = $value;
                $rfid="";
                $checktime = null;
                if(($unit==1 || $unit==4 || $unit==5 || $unit==9) && !empty($lineData) && (strlen($lineData)>1)){
                    $sl = substr($lineData, 0, 2);
                    $date   = substr($lineData, 3, 8);
                    $time   = substr($lineData, 12, 6);
                    $rfid = substr($lineData, 19, 10);
                    $checktime = ((!empty($date) && !empty($time))?date("Y-m-d H:i:s", strtotime("$date $time")):null);
                }
                else if($unit==2 && !empty($lineData) && (strlen($lineData)>1)){
                    $sl = substr($lineData, 0, 2);
                    $date   = substr($lineData, 2, 8);
                    $rfid = substr($lineData, 16, 10);
                    $time   = substr($lineData, 10, 6);
                    $checktime = ((!empty($date) && !empty($time))?date("Y-m-d H:i:s", strtotime("$date $time")):null);
                }
                else if($unit==8  &&  !empty($lineData) && (strlen($lineData)>1)){
                    // if(strlen($lineData)>0){
                    $lineData = explode(" ", $lineData);
                    $rfid = $lineData[0];
                    $date = $lineData[1];
                    $time = $lineData[2];
                    $checktime = ((!empty($date) && !empty($time))?date("Y-m-d H:i:s", strtotime("$date $time")):null);
                }
                else if($unit==3  &&  !empty($lineData) && (strlen($lineData)>1)){
                    if($input['device'] == 1){
                        $sl = substr($lineData, 0, 2);
                        $date   = substr($lineData, 2, 8);
                        $rfid = substr($lineData, 16, 10);
                        $time   = substr($lineData, 10, 6);
                        $checktime = ((!empty($date) && !empty($time))?date("Y-m-d H:i:s", strtotime("$date $time")):null);
                    }elseif($input['device'] == 2){
                        $rfid = '0'; // only unit 3 device automation
                        $lineData = preg_split("/[\t]/", $lineData);
                        $asId = $lineData[0];
                        $checktime = explode(" ", $lineData[1]);
                        $date = $checktime[0];
                        $time = $checktime[1];
                        $checktime = ((!empty($date) && !empty($time))?date("Y-m-d H:i:s", strtotime("$date $time")):null);
                    }else{
                        $msg[] = $value." - AQL device mismatch.";
                        break;
                    }
                }
                else if($unit==1001 && !empty($lineData) && (strlen($lineData)>1)){
                    $lineData = preg_replace('/\s+/', ' ', $lineData);
                    $valueExloade = explode(',', $lineData);
                    $dateExp = explode('/', $valueExloade[1]);
                    if(count($dateExp) > 1 && count($valueExloade) > 2){
                        $dateTimeFormat = $dateExp[2].'-'.$dateExp[1].'-'.$dateExp[0].' '.$valueExloade[2];
                        $date  =  date("Y-m-d H:i:s", strtotime(str_replace("/", "-", $dateTimeFormat)));
                        $rfidNameExloade = explode('-', $valueExloade[4]);
                        $rfid = $rfidNameExloade[0];
                        $checktime = (!empty($date)?date("Y-m-d H:i:s", strtotime($date)):null);
                    }
                    
                }else{
                    if($value != null){
                        $msg[] = $value." - Unit do not match, issue data ";
                    }
                }

                $today = Carbon::parse($checktime)->format('Y-m-d');
                $month = date('m', strtotime($checktime));
                $year = date('Y', strtotime($checktime));

                //get Employee Information from as_basic_info table according to the RFID
                if(strlen($rfid)>0){
                    if($unit == 3 && $input['device'] == 2){
                        $as_info = Employee::
                        where('as_id', $asId)
                        ->where('as_status', 1)
                        ->first();
                    }else{
                        $as_info = Employee::
                        where('as_rfid_code', $rfid)
                        ->where('as_status', 1)
                        ->first();
                    }

                    
                    if(!empty($as_info) && strlen($rfid)>0 && $checktime != null && $as_info->as_status == 1){
                        // check other unit ot employee
                        
                        if($as_info->as_ot == 1 && in_array($unit, [1, 4, 5])){
                           if(!in_array($as_info->as_unit_id, [1,4,5])){
                                $msg[] = $value." - ".$today." This employee assign other unit";
                                continue;
                            }
                        }else if($as_info->as_ot == 1 && $as_info->as_unit_id != $unit){
                            $msg[] = $value." - ".$today." This employee assign other unit";
                            continue;
                        }
                        // check lock month
                        $checkL['month'] = $month;
                        $checkL['year'] = $year;
                        $checkL['unit_id'] = $as_info->as_unit_id;
                        $checkLock = monthly_activity_close($checkL);
                        if($checkLock == 1){
                            $msg[] = $value." - ".$today." Month Activity Lock";
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
                            $msg[] = $value." - ".$today." Leave for this employee";
                            continue;
                        }
                        $checkHolidayFlag = 0;
                        // check holiday individual
                        $getHoliday = HolidayRoaster::getHolidayYearMonthAsIdDateWise($year, $month, $as_info->associate_id, $today);
                         //dd($getHoliday);exit;
                        if($getHoliday != null && $getHoliday->remarks == 'Holiday'){
                            $checkHolidayFlag = 1;
                            // $msg[] = $value." - ".$today." Holiday for roster this employee";
                            // continue;
                        }else if($getHoliday == null){
                            if($as_info->shift_roaster_status == 0){
                                $getYearlyHoliday = YearlyHolyDay::getCheckUnitDayWiseHoliday($as_info->as_unit_id, $today);
                                 //dd($getYearlyHoliday);exit;
                                if($getYearlyHoliday != null && $getYearlyHoliday->hr_yhp_open_status == 0){
                                    $checkHolidayFlag = 1;
                                    // $msg[] = $value." - ".$today." Holiday for this employee";
                                    // continue;
                                }
                            }
                        }

                        //Select table Name as associates Unit ID
                        if($as_info->as_unit_id ==1 || $as_info->as_unit_id ==4 || $as_info->as_unit_id ==5 || $as_info->as_unit_id ==9){
                            $tableName="hr_attendance_mbm";
                        }
                        else if($as_info->as_unit_id ==2){
                            $tableName="hr_attendance_ceil";
                        }
                        else if($as_info->as_unit_id ==3){
                            $tableName="hr_attendance_aql";
                        }
                        else if($as_info->as_unit_id ==8){
                            $tableName="hr_attendance_cew";
                        }
                        else{
                            $tableName="hr_attendance_mbm";
                        }
                        //get shift Code
                        $shift_code = null;
                        $shift_start = null;
                        $shift_end = null;
                        $unitId = $as_info->as_unit_id;

                        $day_of_date = date('j', strtotime($checktime));
                        $day_num = "day_".$day_of_date;

                        $shift= DB::table("hr_shift_roaster")
                        ->where('shift_roaster_month', $month)
                        ->where('shift_roaster_year', $year)
                        ->where("shift_roaster_user_id", $as_info->as_id)
                        ->select([
                            $day_num,
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
                            $shift_code= $shift->hr_shift_code;
                            $shift_start= $shift->hr_shift_start_time;
                            $shift_end= $shift->hr_shift_end_time;
                            $shift_break= $shift->hr_shift_break_time;
                            $nightFlag = $shift->hr_shift_night_flag;
                            $billEligible = $shift->bill_eligible;
                        }
                        else{
                            $shift_code= $as_info->shift['hr_shift_code'];
                            $shift_start= $as_info->shift['hr_shift_start_time'];
                            $shift_end= $as_info->shift['hr_shift_end_time'];
                            $shift_break= $as_info->shift['hr_shift_break_time'];
                            $nightFlag = $as_info->shift['hr_shift_night_flag'];
                            $billEligible = $as_info->shift['bill_eligible'];
                        }
                        // return $checktime.' '.$shift_code.' '.$shift_start.' '.$shift_end;
                        if($shift_code != null && $shift_start != null && $shift_end !=null){

                            $att = $this->attendanceCrud($checktime, $shift_start, $shift_end, $shift_break, $shift_code, $tableName, $as_info, $checkHolidayFlag, $unit, $day_of_date, $month, $year, $unitId, $nightFlag, $billEligible);

                            if($fileDate == ''){
                                $fileDate[] = $today;
                            }else{
                                array_push($fileDate, $today);
                            }
                            $data['date'] = array_unique($fileDate);
                        }else{
                            if($value != null){
                                $msg[] = $value." - shift Name/shift start/shift end null ";
                            }
                        }
                    }else{
                        if($value != null){
                            $msg[] = $value." - Basic info/rfid/checktime null or employee not active";
                        }
                    }
                }else{
                    if($value != null){
                        $msg[] = $value." - rfid null";
                    }
                    //break;
                }
            }
            $data['msg'] = $msg;

            return $data;
        } catch (\Exception $e) {
            $data['status'] = 'error';
            $data['result'] = $e->getMessage();
            //return $e->getMessage();
            return $data;
        }
    }

    public function attendanceCrud($checktime, $shift_start_time, $shift_end_time, $shift_break_time, $shift_code, $tableName, $as_info, $checkHolidayFlag, $unit, $day_of_date, $month, $year, $unitId, $shift_night_flag, $billEligible)
    {

        try {
            $queueName = 'default';
            if($unit == 2){
                $queueName = 'ceilatt';
            }
            $data = [];
            $punch_date = date('Y-m-d', strtotime($checktime));

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
            $otAllow = 86400 - $totalShiftDiff; //86400 = 24 hour

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
                    DB::table($tableName)->where('id', $last_punch->id)->update([
                            'in_time' => null, 'ot_hour' => 0, 'late_status' => 0]);
                    $last_punch = DB::table($tableName)->where('id', $last_punch->id)->first();


                }
                if(($shift_end_begin >= $last_punch->out_time || $last_punch->out_time >= $shift_end_end ) && $last_punch->out_time != null){
                    DB::table($tableName)->where('id', $last_punch->id)->update([
                            'out_time' => null, 'ot_hour' => 0]);
                    $last_punch = DB::table($tableName)->where('id', $last_punch->id)->first();

                }
            }
            if($shift_start_begin <= $check_time && $check_time <= $shift_start_end  && $checkHolidayFlag == 0){
                $checkInTimeFlag = 0;
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

                    $checkInTimeFlag = 1;

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
                        
                        $checkInTimeFlag = 2;
                    }

                }

                if($checkInTimeFlag == 1){
                    // ProcessAttendanceIntime queue run
                    $queue = (new ProcessAttendanceIntime($tableName, $punchId, $unit))
                    ->onQueue($queueName)
                    ->delay(Carbon::now()->addSeconds(2));
                    dispatch($queue);
                }else if($as_info->as_ot == 1 && $checkInTimeFlag == 2 && !empty($last_punch->out_time)){
                    $queue = (new ProcessAttendanceOuttime($tableName, $last_punch->id, $unit))
                        ->onQueue($queueName)
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);
                }

            }
            else if(($shift_end_begin <= $check_time) && ($check_time <= $shift_end_end)  && $checkHolidayFlag == 0){
                if(!empty($last_punch)){
                    $checkOutTimeFlag = 0;
                    if($last_punch->out_time == null){
                        DB::table($tableName)
                        ->where('id', $last_punch->id)
                        ->where('as_id', $as_info->as_id)
                        ->update([
                            'out_time' => $checktime,
                            'out_unit' => $unit
                        ]);



                        $checkOutTimeFlag = 1;
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

                            $checkOutTimeFlag = 1;
                        }
                    }


                    if($as_info->as_ot == 1 && $checkOutTimeFlag == 1){
                        // ProcessAttendanceOuttime queue run
                        $queue = (new ProcessAttendanceOuttime($tableName, $last_punch->id, $unit))
                        ->onQueue($queueName)
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);
                    }
                }
                else{
                    $lastPunchId = DB::table($tableName)
                    ->insertGetId([
                        'as_id' => $as_info->as_id,
                        'in_date' => date('Y-m-d', strtotime($shift_start)),
                        'in_time' => date('Y-m-d H:i:s', strtotime($shift_start)),
                        'out_time' => $checktime,
                        'hr_shift_code' => $shift_code,
                        'remarks'       => "DSI",
                        'in_unit' => $unit,
                        'out_unit' => $unit
                    ]);

                    // ProcessAttendanceIntime queue run  because this user not found in time punch
                    $queue = (new ProcessAttendanceIntime($tableName, $lastPunchId, $unit))
                    ->onQueue($queueName)
                    ->delay(Carbon::now()->addSeconds(2));
                    dispatch($queue);
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
                    $shift_start_time= $shift->hr_shift_start_time;
                    $shift_end_time= $shift->hr_shift_end_time;
                    $shift_night_flag= $shift->hr_shift_night_flag;
                    $shift_break_time = $shift->hr_shift_break_time;
                    $billEligible = $shift->bill_eligible;
                }
                else{
                    $defaultshift = $as_info->shift;
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
                $otAllow = 86400 - $totalShiftDiffNew; //86400 = 24 hour
                // $otAllow = 46000 - ($shift_break_time*60);// 13- hour 00 minute 00 second
                $shift_end_end_new = $shift_out_time_new->copy()->addSeconds($otAllow);
                
                //check time
                $check_time = Carbon::createFromFormat('Y-m-d H:i:s', $checktime);

                if($last_punch){
                    if((($shift_start_begin_new >= $last_punch->in_time || $last_punch->in_time >= $shift_start_end_new)  && $last_punch->in_time != null) || $last_punch->remarks == 'DSI'){
                        DB::table($tableName)->where('id', $last_punch->id)->update([
                                'in_time' => null, 'ot_hour' => 0, 'late_status' => 0]);
                        $last_punch = DB::table($tableName)->where('id', $last_punch->id)->first();


                    }
                    if(($shift_end_begin_new >= $last_punch->out_time || $last_punch->out_time >= $shift_end_end_new ) && $last_punch->out_time != null){
                        DB::table($tableName)->where('id', $last_punch->id)->update([
                                'out_time' => null, 'ot_hour' => 0]);
                        $last_punch = DB::table($tableName)->where('id', $last_punch->id)->first();

                    }
                }
                if($shift_end_begin_new <= $check_time && $check_time <= $shift_end_end_new){

                    if(!empty($last_punch)){
                        $checkOutTimeFlag = 0;
                        if($last_punch->out_time == null){
                            $hi = DB::table($tableName)
                            ->where('id', $last_punch->id)
                            ->update([
                                'out_time' => $checktime,
                                'out_unit' => $unit
                            ]);


                            $checkOutTimeFlag = 1;
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

                                $checkOutTimeFlag = 1;
                            }
                        }
                        if($as_info->as_ot == 1 && $checkOutTimeFlag == 1){
                            // ProcessAttendanceOuttime queue run
                            $queue = (new ProcessAttendanceOuttime($tableName, $last_punch->id, $unit))
                            ->onQueue($queueName)
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
                        }
                    }else{
                        if(!empty($shift_code_new)){
                            $defaultInTime = date("Y-m-d H:i:s", strtotime($shift_start));
                            $lastPunchId = DB::table($tableName)
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

                            // ProcessAttendanceIntime queue run  because this user not found in time punch
                            $queue = (new ProcessAttendanceIntime($tableName, $lastPunchId, $unit))
                            ->onQueue($queueName)
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
                        }
                    }
                }

            }

            // extra calculation
            if($last_punch != null){
                $last_punch = DB::table($tableName)
                                ->where('id', $last_punch->id)
                                ->where('as_id', $as_info->as_id)
                                ->first();
                if($last_punch != null && $last_punch->out_time != null){
                    // OT hour 
                    // if($as_info->as_ot == 1){
                    //     if($last_punch->remarks != 'DSI' && $last_punch->in_time != null){
                    //         $otHour = EmployeeHelper::daliyOTCalculation($last_punch->in_time, $last_punch->out_time, $shift_start_time, $shift_end_time, $shift_break_time, $shift_night_flag, $as_info->associate_id, $as_info->shift_roaster_status, $as_info->as_unit_id);
                    //     }else{
                    //         $otHour = 0;
                    //     }
                    //     DB::table($tableName)
                    //     ->where('id', $last_punch->id)
                    //     ->update(['ot_hour' => $otHour]);
                    // }
                    // bill
                    if($billEligible != null){
                        $cOut = strtotime(date("H:i", strtotime($last_punch->out_time)));
                        if($cOut > strtotime(date("H:i", strtotime($billEligible)))){

                            $bill = EmployeeHelper::dailyBillCalculation($as_info->as_ot, $as_info->as_unit_id, $last_punch->in_date, $last_punch->as_id, $shift_night_flag, $as_info->as_designation_id);
                        }else{
                            $getBill = Bills::where('as_id', $last_punch->as_id)->where('bill_date', $last_punch->in_date)->first();
                            if($getBill != null){
                                Bills::where('id', $getBill->id)->delete();
                            }
                        }
                    }
                }
                
            }

            return 'success';

        } catch (\Exception $e) {
            /*$bug = $e->errorInfo[1];
            // $bug1 = $e->errorInfo[2];
            if($bug == 1062){
                return 'duplicate';
            }*/
            // return $e->getMessage();
            return 'error';
        }
    }

    public function unitAbsent(Request $request)
    {
        $input = $request->all();
        if($input['unit'] ==1 || $input['unit'] ==4 || $input['unit'] ==5 || $input['unit'] ==9){
            $tableName="hr_attendance_mbm";
        }
        else if($input['unit'] ==2){
            $tableName="hr_attendance_ceil";
        }
        else if($input['unit'] ==3){
            $tableName="hr_attendance_aql";
        }
        else if($input['unit'] ==8){
            $tableName="hr_attendance_cew";
        }else{
            $tableName = '';
        }

        try {
                // process absent queue run
            $queue = (new ProcessEmployeeAbsent($tableName, $input['fileDate'], $input['unit']))
            ->delay(Carbon::now()->addSeconds(2));
            dispatch($queue);

            return "success";
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
        }
    }
}
