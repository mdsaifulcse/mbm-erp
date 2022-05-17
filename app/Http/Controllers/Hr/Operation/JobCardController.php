<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Helpers\EmployeeHelper;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessAttendanceFile;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Employee;
use App\Models\Hr\Absent;
use App\Models\Hr\AttendanceHistory;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\HrLateCount;
use App\Models\Hr\ShiftRoaster;
use App\Models\Hr\YearlyHolyDay;
use App\Repository\Hr\AttDataProcessRepository;
use App\Repository\Hr\AttendanceProcessRepository;
use App\Repository\Hr\BillAnnounceRepository;
use App\Repository\Hr\OvertimeRepository;
use App\Repository\Hr\SalaryRepository;
use App\Repository\Hr\ShiftRepository;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class JobCardController extends Controller
{
    protected $billProcess;
    protected $shiftRepository;
    protected $overtimeRepository;
    protected $salaryRepository;
    protected $attendanceProcessRepository;
    protected $attDataProcessRepository;
    public function __construct(BillAnnounceRepository $billProcess, ShiftRepository $shiftRepository, OvertimeRepository $overtimeRepository, SalaryRepository $salaryRepository, AttendanceProcessRepository $attendanceProcessRepository, AttDataProcessRepository $attDataProcessRepository)
    {
        ini_set('zlib.output_compression', 1);
        $this->billProcess = $billProcess;
        $this->shiftRepository = $shiftRepository;
        $this->overtimeRepository = $overtimeRepository;
        $this->salaryRepository = $salaryRepository;
        $this->attendanceProcessRepository = $attendanceProcessRepository;
        $this->attDataProcessRepository = $attDataProcessRepository;
    }
    public function singleUpdate(Request $request)
    {
        $data['type'] = 'error';

        DB::beginTransaction();
        try {
            $value = [];
            $input = $request->all();
            $date = $input['date'];
            $activity = $input['activity'];
            // return $activity;
            $info = $activity['info'];
            $shift = $input['shift_bill']['time'];

            if($input['manual'] == true){
                $value['remarks'] = 'BM';
            }
            $value['as_id'] = $info['as_id'];
            $value['hr_shift_code'] = $shift['hr_shift_code'];
            $value['line_id'] = $activity['lineId'];
            $value['ot_hour'] = 0;
            // set in, out time
            $intime = $input['intime'];
            $outtime = $input['outtime'];
            $attModel = $info['modelName'];
            $checkDay = 'open';
            $status = 1; //present
            // check OT day
            if($activity['otDate'] && array_key_exists($date, $activity['otDate'])){
                $checkDay = 'OT';
                $status = 2; //ot
            }
            if($activity['otPresentDate'] && array_key_exists($date, $activity['otPresentDate'])){
                $checkDay = 'OT';
                $status = 2; //ot
            }
            // in time
            if (strpos($intime, ':') !== false) {
                list($one,$two,$three) = array_pad(explode(':',$intime),3,0);
                if((int)$one+(int)$two+(int)$three == 0) {
                    $intime = null;
                }
            }else{
                $intime = null;
            }
            // out time
            if (strpos($outtime, ':') !== false) {
                list($one,$two,$three) = array_pad(explode(':',$outtime),3,0);
                if((int)$one+(int)$two+(int)$three == 0) {
                    $outtime = null;
                }
            }else{
                $outtime = null;
            }

            $absentData = [
                'associate_id' => $info['associate_id'],
                'date' => $date
            ];
            $empData = [
                'as_id' => $info['as_id'],
                'in_date' => $date
            ];
            $value['late_status'] = 1;
            if($input['id'] != null){
                $attendance = $attModel::findOrFail($input['id']);
            }
            if($intime == null && $outtime == null){                
                if($checkDay == 'open'){
                    Absent::insertOrIgnore($absentData);
                }
                // remove attendance
                if($input['id'] != null){
                    $attendance->delete();
                }
                // remove bill
                //$this->billProcess->removeBillAnncement($empData);
                $status = 0; //absent
            }else{
                $empIntime = $intime;
                if($intime == null){
                    $empIntime = $shift['hr_shift_start_time'];
                    $value['remarks'] = 'DSI';
                }

                $value['in_time'] = $date.' '.$empIntime;
                $value['out_time'] = $date.' '.$outtime;
                if($outtime == null){
                    $value['out_time'] = null;
                }

                // late status
                if($checkDay == 'OT'){
                    $value['late_status'] = 0;
                }else if($intime != null){
                    $value['in_unit'] = $info['as_unit_id'];
                    $value['late_status'] = $this->getLateStatus($info['as_unit_id'], $shift['hr_shift_name'], $date, $intime, $shift['hr_shift_start_time']);
                }

                // out time check
                if($outtime != null){
                    $value['out_unit'] = $info['as_unit_id'];
                    if($intime != null) {
                        // out time is tomorrow
                        if(strtotime(date("Y-m-d H:i:s", strtotime("+2 hours", strtotime($intime)))) > strtotime($outtime)) {
                            $dateModify = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                            $value['out_time'] = $dateModify.' '.$outtime;
                        }
                    }
                }

                $value['in_date'] = $date;

                //check OT hour if out time exist
                $shiftBill = json_decode(json_encode($input['shift_bill']));
                if($intime != null && $outtime != null && $info['as_ot'] != 0){
                    $punch = (object)$value;
                    $employee = (object)$info;
                    $value['ot_hour'] = $this->overtimeRepository->calculateOvertime($punch, $employee, $shiftBill);
                }
                
                // attendance process
                $att = DB::table($info['tableName'])->where($empData)->first();
                if($input['id'] != null){
                    $value['updated_by'] = auth()->user()->associate_id;
                    // DB::table($info['tableName'])->where($empData)->update($value);
                    $attendance->update($value);
                }else{
                    $value['created_by'] = auth()->user()->associate_id;
                    // DB::table($info['tableName'])->insert($value);
                    $input['id'] = $attModel::create($value)->id;

                }
                // bill process
                $employeeValue = array_merge($info, $value);
                //$this->billProcess->makeBillProcess($employeeValue, $shiftBill);
                // absent delete
                Absent::where($absentData)->delete();
            }
            if($input['type'] == 'individual'){
                $data = $this->salaryRepository->employeeMonthlySalaryProcess($info['as_id'], $info['month'], $info['year'], $info['totalDay']);
            }else{
                $data['type'] = 'success';
                $data['message'] = 'Successfully store';
            }
            DB::commit();
            $data['ot'] = $value['ot_hour'];
            $data['late'] = $value['late_status'];
            $data['status'] = $status;
            $data['intime'] = $intime;
            $data['outtime'] = $outtime;
            $data['id'] = $status == 0?'':$input['id'];
            return Response()->json($data);
        } catch (\Exception $e) {
            DB::rollback();
            $data['message'] = $e->getMessage();
            return Response()->json($data);
        }
    }

    public function getLateStatus($unit,$shift_name,$date,$intime,$shift_start)
    {
        $getLateCount = HrLateCount::getUnitShiftIdWiseCheckExists($unit, $shift_name);
        
        if($getLateCount != null){
            if(date('Y-m-d', strtotime($date))>= $getLateCount->date_from && date('Y-m-d', strtotime($date)) <= $getLateCount->date_to){
                $lateTime = $getLateCount->value;
            }else{
                $lateTime = $getLateCount->default_value;
            }
        }else{
            $lateTime = 2;
        }
        $addMin = '+'.$lateTime.' minute';
        $shiftinTime = strtotime(date('H:i:s', strtotime($addMin, strtotime($shift_start))));
        if(strtotime(date('H:i:s', strtotime($intime))) > $shiftinTime){
            $late = 1;
        }else{
            $late = 0;
        }
        return $late;
    }

    public function individualSalaryProcess(Request $request)
    {
        $input = $request->all();
        $info = $input['info'];
        try {
            $data = $this->salaryRepository->employeeMonthlySalaryProcess($info['as_id'], $info['month'], $info['year'], $info['totalDay']);
            return $data;
        } catch (\Exception $e) {
            $data['type'] = 'error';
            $data['message'] = $e->getMessage();
            return $data;
        }
    }

    public function unitWiseShift(Request $request)
    {
        try {
            return DB::select("SELECT
                    s1.hr_shift_id,
                    s1.hr_shift_name,
                    s1.hr_shift_code,
                    s1.hr_shift_start_time,
                    s1.hr_shift_end_time,
                    s1.hr_shift_break_time
                    FROM hr_shift s1
                    LEFT JOIN hr_shift s2
                    ON (s1.hr_shift_unit_id = s2.hr_shift_unit_id AND s1.hr_shift_name = s2.hr_shift_name AND s1.hr_shift_id < s2.hr_shift_id)
                    LEFT JOIN hr_unit AS u
                    ON u.hr_unit_id = s1.hr_shift_unit_id
                    WHERE s2.hr_shift_id IS NULL AND s1.hr_shift_unit_id= $request->as_unit_id");
        } catch (\Exception $e) {
            return $e->getMessage();
            return 'error';
        }
    }

    public function otStore(Request $request)
    {
        $input = $request->all();
        $data['type'] = 'error';
        DB::beginTransaction();
        try {
            $shiftProperty = $this->shiftRepository->getTodaysShiftPropertiesByName($input['as_unit_id'], $input['in_date'], $input['shift']['ot_shift']);

            $shift = $shiftProperty->time;
            $dt = array(
                'in_date' => $input['in_date'],
                'as_id' => $input['as_id'],
                'hr_shift_code' => $shift->hr_shift_code
            );

            $start =  $input['in_date']." ".$shift->hr_shift_start_time;
            $intime =  $input['in_date']." ".($input['in_time']??'');
            $outtime =  $input['in_date']." ".($input['out_time']??'');

            $otHour = 0;
            if($intime != '' && $outtime != ''){
                $otHour = $this->fullot($intime, $start, $outtime,  $shift->hr_shift_break_time);
            }
            $dt['ot_hour'] = $otHour;
            $dt['in_time'] = $intime;
            $dt['out_time'] = $outtime;
            
            DB::table('hr_att_special')->insert($dt);
            $tableName = get_att_table($input['as_unit_id']);
            if($otHour > 0){
                $month = date('m', strtotime($input['in_date']));
                $year = date('Y', strtotime($input['in_date']));
                $totalDay = date('t', strtotime($input['in_date']));
                $data = $this->salaryRepository->employeeMonthlySalaryProcess($input['as_id'], $month, $year, $totalDay); 
            }
            $data['type'] = 'success';
            $data['value'] = $dt;
            $data['url'] = url()->previous();
            DB::commit();
            return Response()->json($data);
        } catch (\Exception $e) {
            DB::rollback();
            $data['message'] = $e->getMessage();
            return Response()->json($data);
        }
    }

    public function spOtUpdate(Request $request)
    {
        $input = $request->all();
        // return $input;
        $data['type'] = 'error';
        DB::beginTransaction();
        try {
            $shiftProperty = $this->shiftRepository->getShiftPropertiesByTodaysSingleCode($input['hr_shift_code'], $input['in_date']);
            $shift = $shiftProperty->time;

            $dt = array(
                'in_date' => $input['in_date'],
                'as_id' => $input['as_id'],
                'hr_shift_code' => $shift->hr_shift_code
            );

            $start =  $input['in_date']." ".$shift->hr_shift_start_time;
            $intime =  $input['in_date']." ".($input['in_time']??'');
            $outtime =  $input['in_date']." ".($input['out_time']??'');

            $otHour = 0;
            if($intime != '' && $outtime != ''){
                $otHour = $this->fullot($intime, $start, $outtime,  $shift->hr_shift_break_time);
            }
            $dt['ot_hour'] = $otHour;
            $dt['in_time'] = $intime;
            $dt['out_time'] = $outtime;
            
            if($input['in_time'] == null && $input['out_time'] == null){
                DB::table('hr_att_special')->where('id', $input['id'])->delete();
            }else{
                DB::table('hr_att_special')->where('id', $input['id'])->update($dt);
            }
            $month = date('m', strtotime($input['in_date']));
            $year = date('Y', strtotime($input['in_date']));
            $totalDay = date('t', strtotime($input['in_date']));
            $data = $this->salaryRepository->employeeMonthlySalaryProcess($input['as_id'], $month, $year, $totalDay);
            $data['ot'] = $otHour;
            DB::commit();
            return Response()->json($data);
        } catch (\Exception $e) {
            DB::rollback();
            $data['message'] = $e->getMessage();
            return Response()->json($data);
        }
    }

    public function fullot($start, $shift_start, $end, $break)
    {
        $start = $start < $shift_start? $shift_start:$start;
        $diff = (strtotime($end) - (strtotime($start) + ($break*60)))/3600;
        $diff = $diff < 0 ? 0:$diff;

        $part    = explode('.', $diff);
        $minutes = (isset($part[1]) ? $part[1] : 0);
        $minutes = floatval('0.'.$minutes);
        // return $minutes;
        if($minutes > 0.16667 && $minutes <= 0.75) $minutes = $minutes;
        else if($minutes >= 0.75) $minutes = 1;
        else $minutes = 0;
        
        if($minutes > 0 && $minutes != 1){
            $min = (int)round($minutes*60);
            $minOT = min_to_ot();
            $minutes = $minOT[$min]??0;
        }

        $overtimes = $part[0] + $minutes;
        $overtimes = number_format((float)$overtimes, 3, '.', '');

        return  $overtimes;
    }

    public function singleShiftChange(Request $request)
    {
        $data['type'] = 'error';
        $input = $request->all();
        $input['shift'] = (array)$input['shift'];
        if(count($input['shift']) == 1){
            $data['message'] = 'No Shift Change!';
            return $data;
        }
        $shiftCode = $input['shift']['hr_shift_code'];
        
        DB::beginTransaction();
        try {
            $year = date('Y', strtotime($input['date']));
            $month = date('n', strtotime($input['date']));
            $day= date('j', strtotime($input['date']));
            $day= "day_".$day;
            $shiftProperty = $this->shiftRepository->getShiftPropertiesByTodaysSingleCode($shiftCode, $input['date']);
            $shift = $shiftProperty->time;
            if($shift != null){
                $shiftEndTime = $shift->hr_shift_end_time;
                $shifttime2 = intdiv($shift->hr_shift_break_time, 60).':'. ($shift->hr_shift_break_time % 60);

                $secsShift = strtotime($shifttime2)-strtotime("00:00:00");
                $hrShiftEnd = date("H:i",strtotime($shiftEndTime)+$secsShift); 
                $shift->startout = date('H:i', strtotime($shift->hr_shift_start_time)).' - '.$hrShiftEnd;
                $data['shift'] = $shift;
                // return $shift;
                $roster = ShiftRoaster::where('shift_roaster_user_id', $input['as_id'])
                ->where('shift_roaster_year', $year)
                ->where('shift_roaster_month', $month)
                ->first();
                if($roster != null){
                    $roster->update([$day => $shift->hr_shift_name]);
                    $getId = $roster->shift_roaster_id;
                }else{
                    $getId = ShiftRoaster::create([
                        'shift_roaster_associate_id' => $input['shift_roaster_associate_id'],
                        'shift_roaster_user_id' => $input['as_id'],
                        'shift_roaster_year' => $year,
                        'shift_roaster_month' => $month,
                        $day => $shift->hr_shift_name
                    ])->shift_roaster_id;
                }
                $data['value'] = $this->attDataProcessRepository->attendanceReCallHistory($input['as_id'], $input['date']);

                if($data['value'] == 'error'){
                    $data['value'] = [];
                }
            }
            DB::commit();
            $data['type'] = 'success';
            $data['msg'] = 'Successfully Change Shift';
            return $data;
        } catch (\Exception $e) {
            DB::rollback();
            $data['msg'] = $e->getMessage();
            return $data;
        }
    }

    public function absentReason(Request $request)
    {
        $input = $request->all();
        $data['type'] = 'error';
        try {
            Absent::updateOrCreate(
                [
                    'associate_id' => $input['associate_id'],
                    'date' => $input['date'],
                ],
                [
                    'hr_unit' => $input['hr_unit'],
                    'comment' => $input['comment'],
                ]
            );
            $data['type'] = 'success';
            $data['message'] = 'Successfully change absent reason';
            return Response()->json($data);
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();
            return Response()->json($data);
        }
    }

    public function attendanceUndo(Request $request)
    {
        $input = $request->all();
        try {
            $this->attDataProcessRepository->attendanceReCallHistory($input['as_id'], $input['date']);
            return 'success';
        } catch (\Exception $e) {
            return 'error';
        }
    }
}
