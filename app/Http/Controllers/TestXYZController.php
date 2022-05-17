<?php

namespace App\Http\Controllers;
use App\Jobs\ProcessAttendanceIntime;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\ProcessUnitWiseSalary;
use App\Jobs\ProcessBuyerSalary;
use App\Jobs\ProcessAttendanceOuttime;
use App\Models\Employee;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Leave;
use App\Models\Hr\Bills;
use App\Models\Hr\SalaryAddDeduct;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\YearlyHolyDay;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Jobs\ProcessDailyActivity;
use App\Helpers\EmployeeHelper;
use Carbon\Carbon;
use DB;

class TestXYZController extends Controller
{
    public function rfidUpdate()
    {
        // $queue = (new ProcessDailyActivity())
        //         ->delay(Carbon::now()->addSeconds(2));
        //         dispatch($queue);
        // return "success";
        
    	return $this->billRemove();
        return "";
    	$data = array();
    	$getBasic = DB::table('hr_as_basic_info')
    	->select('as_id', 'as_rfid_code', 'as_oracle_code', 'as_unit_id')
    	->whereIn('as_unit_id', [3,8])
    	//->where('as_rfid_code', 'LIKE', '#%')
    	->whereRaw('LENGTH(as_rfid_code) < 10')
    	->get();
    	//->pluck('as_oracle_code');
    	return ($getBasic);
    	foreach ($getBasic as $emp) {
    	    $rfid = ltrim($emp->as_rfid_code,'#');
    		//$rfid = str_pad($emp->as_rfid_code, 10, "0", STR_PAD_LEFT); 
	        //if($rfid == '0000000000'){
	        	//$rfid = null;
	        //}
	        $check = DB::table('hr_as_basic_info')->where('as_rfid_code', $rfid)->first();
	        if($check == null){
	            $data[$emp->as_id] = DB::table('hr_as_basic_info')
    	        ->where('as_id', $emp->as_id)
    	        ->update([
    	        	'as_rfid_code' => $rfid
    	        ]);
	        }
    	}
    	
    	return $data;
    }
    
    public function otNewCheck()
    {
        $date = '2021-08-14';
        $tb = 'hr_attendance_aql';
        $get = DB::table($tb)
        ->where('in_date', $date)
        ->where('ot_hour', '>', 0)
        ->get();

        $da = [];
        foreach($get as $d){
            $ex = explode('.', $d->ot_hour);
            if($ex[1] > 0 && $ex[1] < 170){
                
                    $queue = (new ProcessAttendanceOuttime($tb, $d->id, 2))
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);
                
            }
        }
        return 'success';
    }

    public function shiftUpdate()
    {
    	$data[] = DB::table('hr_as_basic_info')
    	->where('as_unit_id', 8)
    	->whereIn('as_oracle_code', [])
    	->update([
    		'as_shift_id' => 'Day'
    	]);

    	return $data;
    }
    public function monthlyCheck(){
        

        // $user = DB::table('hr_as_basic_info')->where('as_doj', '>=','2021-11-01')->get();
        //     $data = [];
        // foreach ($user as $key => $e) {
        //     $query = DB::table('holiday_roaster')
        //           ->where('as_id', $e->associate_id)
        //           ->whereDate('date','<',$e->as_doj)
        //           ->get()->toArray();
            
        // }
        // return ($query);
        // $user = DB::table('hr_as_basic_info')->where('as_doj', '>=','2021-11-01')->get();
        //     $data = [];
        // foreach ($user as $key => $e) {
        //     $query = DB::table('hr_absent')
        //                               ->where('date', 'like', '2021-11%')
        //                               ->where('associate_id', $e->associate_id)
        //                               ->where('date','<',$e->as_doj)
        //                               ->delete();
        //     // if(count($query) > 0){
        //     //     $data[$e->associate_id] = $query;
        //     // }
        // }
        return ($data);
        // $leave_array = [];
        //         $absent_array = [];
        //         for($i=1; $i<=31; $i++) {
        //         $date = date('Y-m-d', strtotime('2021-10-'.$i));
        //         $leave = DB::table('hr_attendance_mbm AS a')
        //                 ->where('a.in_date', $date)
        //                 // ->where('a.as_id', 8958)
        //                 ->leftJoin('hr_as_basic_info AS b', function($q){
        //                     $q->on('b.as_id', 'a.as_id');
        //                 })
        //                 ->pluck('b.associate_id');
        //         $leave_array[] = $leave;
        //         $absent_array[] = DB::table('hr_absent')
        //                 ->whereDate('date', $date)
        //                 ->whereIn('associate_id', $leave)
        //                 ->get();
        //         }
        //         return $absent_array;
        //         dump($leave_array,$absent_array);
        //         dd('end');

        //         $leave_array = [];
        //         $absent_array = [];
        //         for($i=1; $i<=31; $i++) {
        //         $date = date('Y-m-d', strtotime('2021-03-'.$i));
        //         $leave = DB::table('hr_attendance_ceil AS a')
        //                 ->whereIn('a.in_date',  ['2021-03-05','2021-03-12','2021-03-19','2021-03-26'])
        //                 ->whereIn('b.as_unit_id', [2])
        //                 ->where('b.shift_roaster_status', 1)
        //                 ->leftJoin('hr_as_basic_info AS b', function($q){
        //                     $q->on('a.in_date', 'a.as_id');
        //                 })
        //                 ->pluck('b.as_id', 'b.associate_id');
        //         $leave_array[] = $leave;
                
                
        //         }
        //         dump($leave_array);
        //         dd('end');

                // $leave_array = [];
                // $absent_array = [];
                // for($i=1; $i<=31; $i++) {
                // $date = date('Y-m-d', strtotime('2020-11-'.$i));
                // $leave = DB::table('hr_absent AS a')
                //         ->where('a.date', '=', $date)
                //         ->whereIn('b.as_unit_id', [1, 4, 5])
                //         ->leftJoin('hr_as_basic_info AS b', function($q){
                //             $q->on('b.associate_id', 'a.associate_id');
                //         })
                //         ->pluck('b.as_id', 'b.associate_id');
                // $leave_array[] = $leave;
                // $absent_array[] = DB::table('hr_attendance_mbm')
                //         ->whereDate('in_time', $date)
                //         ->whereIn('as_id', $leave)
                //         ->get()->toArray();
                // }
                
                // dump($leave_array,$absent_array);
                // dd('end');
            // $leave_array = [];
            // $absent_array = [];
            // for($i=1; $i<=30; $i++) {
            // $date = date('Y-m-d', strtotime('2021-09-'.$i));
            // $leave = DB::table('hr_leave AS l')
            //         ->where('l.leave_from', '<=', $date)
            //         ->where('l.leave_to',   '>=', $date)
            //         ->where('l.leave_status', '=', 1)
            //         ->whereIn('b.as_unit_id', [2])
            //         ->leftJoin('hr_as_basic_info AS b', function($q){
            //             $q->on('b.associate_id', 'l.leave_ass_id');
            //         })
            //         ->pluck('b.as_id', 'b.associate_id');
            // $leave_array[] = $leave;
            // $absent_array[] = DB::table('hr_attendance_ceil')
            //         ->whereDate('in_time', $date)
            //         ->whereIn('as_id', $leave)
            //         ->get()->toArray();
            // }
            // return $absent_array;
            // dump($leave_array,$absent_array);
            // dd('end');

            $leave_array = [];
            $absent_array = [];
            for($i=1; $i<=31; $i++) {
            $date = date('Y-m-d', strtotime('2021-11-'.$i));
            $leave = DB::table('hr_leave AS l')
                    ->where('l.leave_from', '<=', $date)
                    ->where('l.leave_to',   '>=', $date)
                    ->where('l.leave_status', '=', 1)
                    ->leftJoin('hr_as_basic_info AS b', function($q){
                        $q->on('b.associate_id', 'l.leave_ass_id');
                    })
                    ->pluck('b.associate_id','b.as_id');
            $leave_array[] = $leave;
            $absent_array[] = DB::table('hr_absent')
                    ->whereDate('date', $date)
                    ->whereIn('associate_id', $leave)
                    //->whereIn('hr_unit', [1,4,5])
                    ->delete();
            }
            return $absent_array;
            

    }
    public function otHourCheck()
    {
    	$section = section_by_id();
        $department = department_by_id();
        $d = [];
        
        $aunit = 1;
        
        if($aunit == 1){
            $uniti = [1,4,5];
        }elseif($aunit == 2){
            $uniti = [2];
        }elseif($aunit == 3){
            $uniti = [3];
        }elseif($aunit == 8){
            $uniti = [8];
        }
        
        $tb = get_att_table($aunit);
        $tbm =  $tb.' AS m';
        
        
    	$getBasic = DB::table('hr_as_basic_info')
    	->where('as_ot', 1)
    	->whereIn('as_unit_id', $uniti)
    	->where('as_status', 1)
    	->pluck('as_id');
    	$getat = [];
    	for($i=1; $i<=31; $i++) {
	    	$getData = DB::table($tbm)
	    	->select('ba.associate_id', 'ba.as_oracle_code', 'ba.as_department_id', 'ba.as_section_id', 'ba.as_name','m.*', 'b.hr_shift_end_time', 'b.hr_shift_break_time')
	    	->where('m.in_date', '2021-11-'.$i)
	    	->whereIn('m.as_id', $getBasic)
	    	->leftJoin('hr_shift AS b', function($q){
	            $q->on('b.hr_shift_code', 'm.hr_shift_code');
	        })
            ->leftJoin('hr_as_basic_info AS ba', function($q){
                $q->on('ba.as_id', 'm.as_id');
            })
	        ->whereNotNull('m.out_time')
	        ->whereNotNull('m.in_time')
	        ->where('m.remarks', '!=', 'DSI')
            // ->where('m.ot_hour', 0)
	        ->get();

	        
	        foreach ($getData as $data) {
	        	$punchOut = $data->out_time;
	        	$shiftOuttime = date('Y-m-d', strtotime($punchOut)).' '.$data->hr_shift_end_time;
	        	$otDiff = ((strtotime($punchOut) - (strtotime($shiftOuttime) + (($data->hr_shift_break_time + 10) * 60))))/3600;
	        	if($otDiff > 0 && $data->ot_hour <= 0){
	        		$getat[$data->as_id.' '.$data->in_date] = $data;
	        	}
	        }
	    }

        foreach ($getat as $att) {
            if($att->in_time && $att->out_time){
                $queue = (new ProcessAttendanceOuttime($tb, $att->id, $aunit))
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);
            }
            
            $d[] = array(
                'Oracle Id' => $att->as_oracle_code,
                'Associate Id' => $att->associate_id,
                'Name' => $att->as_name,
                'Department' => $department[$att->as_department_id]['hr_department_name']??'',
                'Section' => $section[$att->as_section_id]['hr_section_name']??'',
                'Date' =>  date('m/d/Y', strtotime($att->in_date)),
                'In Time' => date('H:i:s', strtotime($att->in_time)),
                'Out Time' => date('H:i:s', strtotime($att->out_time)),
            );
        }
        return $d;
        return (new FastExcel(collect($d)))->download($aunit.'-Ot missing.xlsx');
        
    }
    public function shiftProblemCheck()
    {
    	$section = section_by_id();
        $department = department_by_id();
        $d = [];
        
        $aunit = 1;
        
        if($aunit == 1){
            $uniti = [1,4,5];
        }elseif($aunit == 2){
            $uniti = [2];
        }elseif($aunit == 3){
            $uniti = [3];
        }elseif($aunit == 8){
            $uniti = [8];
        }
        
        $tb = get_att_table($aunit);
        $tbm =  $tb.' AS m';
        
    	$getBasic = DB::table('hr_as_basic_info')
    	//->where('as_ot', 1)
    	->whereIn('as_unit_id', $uniti)
    	->where('as_status', 1)
    	->pluck('as_id');
    	$getat = [];
    	for($i=1; $i<=31; $i++) {
	    	$getData = DB::table($tbm)
	    	->select('ba.associate_id', 'ba.as_oracle_code', 'ba.as_department_id', 'ba.as_section_id', 'ba.as_name','m.*', 'b.hr_shift_end_time', 'b.hr_shift_start_time', 'b.hr_shift_break_time')
	    	->where('m.in_date', '2021-11-'.$i)
	    	->whereIn('m.as_id', $getBasic)
	    	->leftJoin('hr_shift AS b', function($q){
	            $q->on('b.hr_shift_code', 'm.hr_shift_code');
	        })
            ->leftJoin('hr_as_basic_info AS ba', function($q){
                $q->on('ba.as_id', 'm.as_id');
            })
	        ->whereNotNull('m.out_time')
	        ->whereNotNull('m.in_time')
	        ->where('m.remarks', '!=', 'DSI')
            // ->where('m.ot_hour', 0)
	        ->get();

	        
	        foreach ($getData as $data) {
	            
	        	$punchIn = $data->in_time;
	        	$shiftIntime = date('Y-m-d', strtotime($punchIn)).' '.$data->hr_shift_start_time;
	        	$otDiff = (strtotime($punchIn)) - (strtotime($shiftIntime));
	        	if($otDiff > 18000){
	        	    $getat[$data->as_id.' '.$data->in_date] = $data;
	        	}
	        }
	       
	    }

        foreach ($getat as $att) {
            
            
            $d[] = array(
                'Oracle Id' => $att->as_oracle_code,
                'Associate Id' => $att->associate_id,
                'Name' => $att->as_name,
                'Department' => $department[$att->as_department_id]['hr_department_name']??'',
                'Section' => $section[$att->as_section_id]['hr_section_name']??'',
                'Date' =>  date('m/d/Y', strtotime($att->in_date)),
                'In Time' => date('H:i:s', strtotime($att->in_time)),
                'Out Time' => date('H:i:s', strtotime($att->out_time)),
            );
        }
        return $d;
        return (new FastExcel(collect($d)))->download($aunit.'-Shift Problem.xlsx');
        
    }
    public function lateCheck()
    {
        $section = section_by_id();
        $department = department_by_id();
        $d = [];
        $aunit = 1;
        
        if($aunit == 1){
            $uniti = [1,4,5];
        }elseif($aunit == 2){
            $uniti = [2];
        }elseif($aunit == 3){
            $uniti = [3];
        }elseif($aunit == 8){
            $uniti = [8];
        }
        
        $tb = get_att_table($aunit);
        $tableName =  $tb.' AS m';
        $yearMonth = '2021-11-';
        $getat = [];
        for($i=1; $i<=31; $i++) {
            $getData = DB::table($tableName)
            ->select('ba.associate_id', 'ba.as_oracle_code', 'ba.as_department_id', 'ba.as_section_id', 'ba.as_name','m.*', 'b.hr_shift_start_time')
            ->leftJoin('hr_shift AS b', function($q){
                $q->on('b.hr_shift_code', 'm.hr_shift_code');
            })
            ->leftJoin('hr_as_basic_info AS ba', function($q){
                $q->on('ba.as_id', 'm.as_id');
            })
            ->where('m.in_date', $yearMonth.$i)
            ->where('m.late_status', 1)
            ->where('ba.as_ot', 1)
            ->get();

            
            foreach ($getData as $data) {
                    
                $queue = (new ProcessAttendanceIntime($tb, $data->id, $aunit))
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);
                

            }
        }
        
        return "success";
        
    }
    public function nolateCheck()
    {
        $section = section_by_id();
        $department = department_by_id();
        $d = [];
        $aunit = 1;
        
        if($aunit == 1){
            $uniti = [1,4,5];
        }elseif($aunit == 2){
            $uniti = [2];
        }elseif($aunit == 3){
            $uniti = [3];
        }elseif($aunit == 8){
            $uniti = [8];
        }
        
        $tb = get_att_table($aunit);
        $tableName =  $tb.' AS m';
        $yearMonth = '2021-11-';
        $getat = [];
        for($i=1; $i<=31; $i++) {
            $getData = DB::table($tableName)
            ->select('ba.associate_id', 'ba.as_oracle_code', 'ba.as_department_id', 'ba.as_section_id', 'ba.as_name','m.*', 'b.hr_shift_start_time')
            ->leftJoin('hr_shift AS b', function($q){
                $q->on('b.hr_shift_code', 'm.hr_shift_code');
            })
            ->leftJoin('hr_as_basic_info AS ba', function($q){
                $q->on('ba.as_id', 'm.as_id');
            })
            ->where('m.in_date', $yearMonth.$i)
            ->where('m.late_status', 0)
            ->get();

            
            foreach ($getData as $data) {
                $flag = 0;
                $shiftIntime = date('Y-m-d H:i:s', strtotime('+3 minute', strtotime($data->in_date.' '.$data->hr_shift_start_time)));
                $intime = date('Y-m-d H:i:s', strtotime($data->in_time));
                if(strtotime($intime) > strtotime($shiftIntime)){
                    $flag = 1;
                    $getat[$data->as_id.' '.$data->in_date] = $data;
                }else if($data->remarks == 'DSI'){
                    $flag = 1;
                    $getat[$data->as_id.' '.$data->in_date] = $data;
                }
                
                

                if($flag == 1){
                    
                    $queue = (new ProcessAttendanceIntime($tb, $data->id, $aunit))
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
                }

            }
        }

        foreach ($getat as $att) {
            $d[] = array(
                'Oracle Id' => $att->as_oracle_code,
                'Associate Id' => $att->associate_id,
                'Name' => $att->as_name,
                'Department' => $department[$att->as_department_id]['hr_department_name']??'',
                'Section' => $section[$att->as_section_id]['hr_section_name']??'',
                'Date' =>  date('m/d/Y', strtotime($att->in_date)),
                'In Time' => date('H:i:s', strtotime($att->in_time)),
                'Out Time' => date('H:i:s', strtotime($att->out_time)),
                'late' => $att->late_status
            );
        }
        return $d;
        return (new FastExcel(collect($d)))->download('late missing.xlsx');
        
    }
    public function earlyarPunchCheck()
    {
        $getBasic = DB::table('hr_as_basic_info')
        ->where('as_ot', 1)
        ->whereIn('as_unit_id', [2])
        ->where('as_status', 1)
        ->pluck('as_id');
        $getat = [];
        for($i=1; $i<=28; $i++) {
            $getData = DB::table('hr_attendance_ceil AS m')
            ->select('m.*', 'b.hr_shift_start_time', 'b.hr_shift_break_time')
            ->where('m.in_date', '2021-02-'.$i)
            ->whereIn('m.as_id', $getBasic)
            ->leftJoin('hr_shift AS b', function($q){
                $q->on('b.hr_shift_code', 'm.hr_shift_code');
            })
            // ->whereNotNull('m.out_time')
            // ->whereNotNull('m.in_time')
            ->get();
            // dd($getData);
            
            foreach ($getData as $data) {
                $punchIn = $data->in_time;
                $shiftIntime = date('Y-m-d', strtotime($punchIn)).' '.$data->hr_shift_start_time;
                $earlyTime = date('Y-m-d H:i:s', strtotime('-2 hours', strtotime($shiftIntime)));
                
                if(strtotime($punchIn) < strtotime($earlyTime)){
                    $getat[$data->as_id.' - '.$data->in_date] = $data;
                }
            }
        }
        return ($getat);
        
    }
    public function monthlyLeftCheck()
    {
    	$data = DB::table('hr_monthly_salary')
    	->where('month', '01')
    	->where('year', '2021')
    	->where('emp_status', 2)
    	->get();

    	$current = DB::table('hr_monthly_salary')
    	->select('as_id')
    	->where('month', '02')
    	->where('year', '2021')
    	->where('emp_status', 1)
    	->get()
    	->keyBy('as_id')
    	->toArray();

    	$ge = array();
    	foreach ($data as $value) {
    		if(isset($current[$value->as_id])){
    			$ge[] = $value->as_id;
    		}
    	}
    	return ($ge);
    }
    public function indiviBillEntry(){
        $date = '2021-05-';
        $getEmp = DB::table('hr_as_basic_info')
        ->whereIn('associate_id', [])->get();

        foreach($getEmp as $emp){
            DB::table('hr_bill')
            ->insertOrIgnore([
                'as_id' => $emp->as_id,
                'bill_date' => '2021-05-07',
                'bill_type' => 4,
                'amount' => 30
            ]);
        }
        return 'success';
        for($i=7; $i<=7; $i++){
            DB::table('hr_bill')
            ->insertOrIgnore([
                'as_id' => 12182,
                'bill_date' => $date.$i,
                'bill_type' => 1,
                'amount' => 20,
                'pay_status' => 0
            ]);
        }
        return 'success';
    }
    public function billEntry()
    {
        $data = [];
        $date = '2021-06-01';
        // $asId= DB::table('hr_outside')
        //     ->select('hr_as_basic_info.as_id', 'hr_outside.start_date', 'hr_outside.end_date', 'hr_as_basic_info.as_unit_id')
        //     ->where('start_date','>',$date)
        //     //->where('status',1)
        //     ->where('requested_location','WFHOME')
        //     ->join('hr_as_basic_info', 'hr_outside.as_id', 'hr_as_basic_info.associate_id')
        //     //->where('as_unit_id', 2)
        //     ->pluck('as_id');
        
        $getTable = DB::table('hr_attendance_mbm AS m')
        ->select('m.as_id', 'm.in_date', 'm.out_time', 'm.hr_shift_code', 'b.as_unit_id', 'b.as_location')
        ->join('hr_as_basic_info AS b', 'm.as_id', 'b.as_id')
        //->where('m.in_date', $date)
        ->whereBetween('m.in_date', ['2021-05-01', '2021-05-31'])
        ->where('b.as_designation_id', 230)
        ->where('b.as_location', '!=', 12)
        //->whereNotIn('m.as_id', $asId)
        ->get();
        DB::beginTransaction();
        $insert = [];
        try {
            foreach ($getTable as $key => $value) {
                $outTime = $value->out_time;
                $d = $value->in_date; 
                if($outTime > $d.' 20:45:00'){
                    $insert[] = [
                        'as_id' => $value->as_id,
                        'bill_date' => $d,
                        'bill_type' => 1,
                        'amount' => 20,
                        'pay_status' => 0
                    ];
                }
                
            }
            //return $insert;
            if(count($insert) > 0){
                $chunk = collect($insert)->chunk(200);
                foreach ($chunk as $key => $n) {        
                    DB::table('hr_bill')->insertOrIgnore(collect($n)->toArray());
                }
            }
            
            DB::commit();
            return 'success';
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }

    }
    public function manualBillEntry($value='')
    {
        $as_id = 971;
        $date = '2021-10-';
        $data = [];
        $insert = [];
        $getBill = DB::table('hr_bill')
            ->select(DB::raw("CONCAT(bill_date,as_id) AS asdate"), 'bill_date', 'bill_type')
            ->whereBetween('bill_date', ['2021-10-18', '2021-10-31'])
            ->where('bill_type', 1)
            ->where('as_id', $as_id)
            ->get()
            ->keyBy('asdate')
            ->toArray();

        for ($i=18; $i <= 31; $i++) { 
            $day = date('Y-m-d', strtotime($date.$i));
            $addEndDate = date('Y-m-d', strtotime("+1 day", strtotime($day)));
            $getatt = DB::table('hr_attendance_mbm AS m')
            ->select(DB::raw("CONCAT(m.in_date, m.as_id) AS asdate"), 'm.in_date', 'm.in_time', 'm.out_time', 'm.as_id', 'b.as_location')
            ->join('hr_as_basic_info AS b', function($q){
                $q->on('b.as_id', 'm.as_id');
            })
            ->join('hr_shift AS s', function($q){
                $q->on('s.hr_shift_code', 'm.hr_shift_code');
            })
            ->where('m.as_id', $as_id)
            ->where('m.in_date', $day)
            ->where('m.out_time', '>', $day.' 20:44:59')
            ->where('in_time', '<', $day.' 11:00:00')
            ->where('s.hr_shift_night_flag', 0)
            ->get()
            ->keyBy('asdate')
            ->toArray();

            foreach ($getatt as $att) {
                if(!isset($getBill[$att->in_date.$att->as_id]) || (isset($getBill[$att->in_date.$att->as_id]) && $getBill[$att->in_date.$att->as_id]->bill_type != 1)){

                     $data[] = $att;
                    $insert[] = [
                        'as_id' => $att->as_id,
                        'bill_date' => $att->in_date,
                        'bill_type' => 1,
                        'amount' => 20,
                        'pay_status' => 0
                    ];
                
                }
                elseif(isset($getBill[$att->in_date.$att->as_id]) && $getBill[$att->in_date.$att->as_id]->bill_type == 1){
                   // $data[] = $att;
                    // DB::table('hr_bill')
                    // ->where('bill_date', $att->in_date)
                    // ->where('as_id', $att->as_id)
                    // ->delete();
                    
                }
            }

        }

        //return ($insert);
        if(count($insert) > 0){
            $chunk = collect($insert)->chunk(200);
            foreach ($chunk as $key => $n) {        
                DB::table('hr_bill')->insertOrIgnore(collect($n)->toArray());
            }
        }
        return 'success';
    }
    public function mbmBillProcess()
    {
        $date = '2021-11-';
        $data = [];
        $insert = [];
        $getBill = DB::table('hr_bill')
            ->select(DB::raw("CONCAT(bill_date,as_id) AS asdate"), 'bill_date', 'bill_type')
            ->whereBetween('bill_date', ['2021-11-16', '2021-11-30'])
            ->where('bill_type', 1)
            ->get()
            ->keyBy('asdate')
            ->toArray();
        for ($i=16; $i <= 30; $i++) { 
            $day = date('Y-m-d', strtotime($date.$i));
            $addEndDate = date('Y-m-d', strtotime("+1 day", strtotime($day)));
            $getatt = DB::table('hr_attendance_mbm AS m')
            ->select(DB::raw("CONCAT(m.in_date, m.as_id) AS asdate"), 'm.in_date', 'm.in_time', 'm.out_time', 'm.as_id', 'b.as_location', 'b.associate_id')
            ->join('hr_as_basic_info AS b', function($q){
                $q->on('b.as_id', 'm.as_id');
            })
            ->join('hr_shift AS s', function($q){
                $q->on('s.hr_shift_code', 'm.hr_shift_code');
            })
            ->where('m.in_date', $day)
            //->where('m.out_time', '>', $addEndDate.' 00:00:59')
            //->where('m.out_time', '>', $day.' 20:29:59')
            ->where('m.out_time', '>', $day.' 20:44:59')
            ->where('m.in_time', '<', $day.' 11:00:00')
            ->where('s.hr_shift_night_flag', 0)
            //->whereIn('b.as_location', [9])
            ->whereNotIn('b.as_location', [12,13])
            ->where('b.as_subsection_id', '!=', 108)
            //->where('m.remarks', '!=', 'DSI')
            ->whereNotIn('b.as_subsection_id', [185,108])
            ->whereNotIn('b.as_designation_id', [408,397,218,229,204,211,356,470,407,221,293,375,449,196,454,402,463])
            ->whereNotIn('b.as_department_id', [53,56])
            ->get()
            ->keyBy('asdate')
            ->toArray();
            //return $getatt;
            foreach ($getatt as $att) {
                if(!isset($getBill[$att->in_date.$att->as_id]) || (isset($getBill[$att->in_date.$att->as_id]) && $getBill[$att->in_date.$att->as_id]->bill_type != 1)){

                    $data[] = $att;
                    $insert[] = [
                        'as_id' => $att->as_id,
                        'bill_date' => $att->in_date,
                        'bill_type' => 1,
                        'amount' => 20,
                        'pay_status' => 0
                    ];
                
                }
                elseif(isset($getBill[$att->in_date.$att->as_id]) && $getBill[$att->in_date.$att->as_id]->bill_type == 1){
                    //$data[] = $att;
                    // DB::table('hr_bill')
                    // ->where('bill_date', $att->in_date)
                    // ->where('as_id', $att->as_id)
                    // ->delete();
                    // DB::table('hr_bill')
                    // ->where('bill_date', $att->in_date)
                    // ->where('as_id', $att->as_id)
                    // ->update([
                    //     'bill_type' => 2,
                    //     'amount' => 70
                    // ]);
                }
            }

        }
        
        //return ($data);
        if(count($insert) > 0){
            $chunk = collect($insert)->chunk(200);
            foreach ($chunk as $key => $n) {        
                DB::table('hr_bill')->insertOrIgnore(collect($n)->toArray());
            }
        }
        return 'success';
        // dd($data);

        for ($i=14; $i <= 30; $i++) { 
            $getBill = DB::table('hr_bill')
            ->select('b.as_id as bas_id', 'hr_bill.as_id', DB::raw("CONCAT(hr_bill.bill_date,hr_bill.as_id) AS asdate"), 'hr_bill.bill_date', 'hr_bill.bill_type')
            ->where('hr_bill.bill_date', date('Y-m-d', strtotime($date.$i)))
            ->join('hr_as_basic_info AS b', 'hr_bill.as_id', 'b.as_id')
            ->whereIn('b.as_unit_id', [1,4,5])
            ->where('hr_bill.bill_type', 4)
            ->get()
            ->keyBy('asdate')
            ->toArray();

            $getatt = DB::table('hr_attendance_mbm')
            ->select(DB::raw("CONCAT(in_date,as_id) AS asdate"), 'in_date', 'in_time', 'out_time', 'as_id')
            ->where('in_date', date('Y-m-d', strtotime($date.$i)))
            ->get()
            ->keyBy('asdate')
            ->toArray();
            
            foreach ($getatt as $att) {
                if(!isset($getBill[$att->in_date.$att->as_id])){
                    $data[] = $att;
                }
            }
        }
        return ($data);
    }
    public function aqlBillCheck()
    {
        $date = '2021-10-';
        $data = [];
        $insert = [];
        $getBill = DB::table('hr_bill')
            ->select(DB::raw("CONCAT(bill_date,as_id) AS asdate"), 'bill_date', 'bill_type')
            ->whereBetween('bill_date', ['2021-10-11', '2021-10-31'])
            ->where('bill_type', 1)
            ->get()
            ->keyBy('asdate')
            ->toArray();
        for ($i=11; $i <= 31; $i++) { 
            $day = date('Y-m-d', strtotime($date.$i));
            $addEndDate = date('Y-m-d', strtotime("+1 day", strtotime($day)));
            $getatt = DB::table('hr_attendance_aql AS m')
            ->select(DB::raw("CONCAT(m.in_date, m.as_id) AS asdate"), 'm.in_date', 'm.in_time', 'm.out_time', 'm.as_id', 'b.as_location')
            ->join('hr_as_basic_info AS b', function($q){
                $q->on('b.as_id', 'm.as_id');
            })
            ->join('hr_shift AS s', function($q){
                $q->on('s.hr_shift_code', 'm.hr_shift_code');
            })
            ->where('m.in_date', $day)
            //->where('m.out_time', '<', $addEndDate.' 00:59:59')
            ->where('m.out_time', '>', $day.' 22:29:59')
            ->where('m.out_time', '<', $day.' 23:29:59')
            //->where('m.in_time', '>', $day.' 11:00:00')
            ->where('s.hr_shift_night_flag', 0)
            ->whereIn('b.as_location', [9])
            //->whereNotIn('b.as_location', [12,13])
            ->where('b.as_subsection_id', '!=', 108)
            ->whereIn('s.hr_shift_code', ['THREEDETHREEDE']) // THREEDETHREEDE THREEDA THREEDS
            ->get()
            ->keyBy('asdate')
            ->toArray();
            //return $getatt;
            foreach ($getatt as $att) {
                if(!isset($getBill[$att->in_date.$att->as_id]) || (isset($getBill[$att->in_date.$att->as_id]) && $getBill[$att->in_date.$att->as_id]->bill_type != 1)){

                    $data[] = $att;
                    $insert[] = [
                        'as_id' => $att->as_id,
                        'bill_date' => $att->in_date,
                        'bill_type' => 1,
                        'amount' => 30,
                        'pay_status' => 0
                    ];
                
                }
                elseif(isset($getBill[$att->in_date.$att->as_id]) && $getBill[$att->in_date.$att->as_id]->bill_type == 1){
                    // $data[] = $att;
                    // DB::table('hr_bill')
                    // ->where('bill_date', $att->in_date)
                    // ->where('as_id', $att->as_id)
                    // ->delete();
                    
                }
            }

        }
        
        return ($data);
        if(count($insert) > 0){
            $chunk = collect($insert)->chunk(200);
            foreach ($chunk as $key => $n) {        
                DB::table('hr_bill')->insertOrIgnore(collect($n)->toArray());
            }
        }
        return 'success';
        // dd($data);

        for ($i=14; $i <= 30; $i++) { 
            $getBill = DB::table('hr_bill')
            ->select('b.as_id as bas_id', 'hr_bill.as_id', DB::raw("CONCAT(hr_bill.bill_date,hr_bill.as_id) AS asdate"), 'hr_bill.bill_date', 'hr_bill.bill_type')
            ->where('hr_bill.bill_date', date('Y-m-d', strtotime($date.$i)))
            ->join('hr_as_basic_info AS b', 'hr_bill.as_id', 'b.as_id')
            ->whereIn('b.as_unit_id', [1,4,5])
            ->where('hr_bill.bill_type', 4)
            ->get()
            ->keyBy('asdate')
            ->toArray();

            $getatt = DB::table('hr_attendance_mbm')
            ->select(DB::raw("CONCAT(in_date,as_id) AS asdate"), 'in_date', 'in_time', 'out_time', 'as_id')
            ->where('in_date', date('Y-m-d', strtotime($date.$i)))
            ->get()
            ->keyBy('asdate')
            ->toArray();
            
            foreach ($getatt as $att) {
                if(!isset($getBill[$att->in_date.$att->as_id])){
                    $data[] = $att;
                }
            }
        }
        return ($data);
    }
    public function ceilBill(){
        $date = '2021-10-';
        $data = [];
        $insert = [];
        $getBill = DB::table('hr_bill')
            ->select(DB::raw("CONCAT(bill_date,as_id) AS asdate"), 'bill_date', 'bill_type')
            ->whereBetween('bill_date', ['2021-10-01', '2021-10-31'])
            ->where('bill_type', 1)
            ->get()
            ->keyBy('asdate')
            ->toArray();
        for ($i=01; $i <= 31; $i++) { 
            $day = date('Y-m-d', strtotime($date.$i));
            $addEndDate = date('Y-m-d', strtotime("+1 day", strtotime($day)));
            $getatt = DB::table('hr_attendance_ceil AS m')
            ->select(DB::raw("CONCAT(m.in_date, m.as_id) AS asdate"), 'm.in_date', 'm.in_time', 'm.out_time', 'm.as_id', 'b.as_location')
            ->join('hr_as_basic_info AS b', function($q){
                $q->on('b.as_id', 'm.as_id');
            })
            ->join('hr_shift AS s', function($q){
                $q->on('s.hr_shift_code', 'm.hr_shift_code');
            })
            ->where('m.in_date', $day)
            //->where('m.out_time', '>', $addEndDate.' 04:44:59')
            ->where('m.out_time', '>', $day.' 20:29:59')
            ->where('m.out_time', '<', $day.' 22:29:59')
            //->where('in_time', '<', $day.' 11:00:00')
            ->where('s.hr_shift_night_flag', 0)
            ->where('b.as_location', 7)
            //->whereNotIn('b.as_location', [12,13])
            ->where('b.as_subsection_id', '!=', 108)
            // ->whereIn('b.as_designation_id', [436, 432, 225, 354])
            ->where('s.hr_shift_code', 'TWODETWODE')  // TWODETWODE C1
            ->get()
            ->keyBy('asdate')
            ->toArray();
            //return $getatt;
            foreach ($getatt as $att) {
                if(!isset($getBill[$att->in_date.$att->as_id]) || (isset($getBill[$att->in_date.$att->as_id]) && $getBill[$att->in_date.$att->as_id]->bill_type != 1)){

                    $data[] = $att;
                    $insert[] = [
                        'as_id' => $att->as_id,
                        'bill_date' => $att->in_date,
                        'bill_type' => 1,
                        'amount' => 15,
                        'pay_status' => 0
                    ];
                
                }
                elseif(isset($getBill[$att->in_date.$att->as_id]) && $getBill[$att->in_date.$att->as_id]->bill_type == 1){
                    //$data[] = $getBill[$att->in_date.$att->as_id];
                    // DB::table('hr_bill')
                    // ->where('bill_date', $att->in_date)
                    // ->where('as_id', $att->as_id)
                    // ->delete();
                    // DB::table('hr_bill')
                    // ->where('bill_date', $att->in_date)
                    // ->where('as_id', $att->as_id)
                    // ->update([
                    //     'bill_type' => 2,
                    //     'amount' => 70
                    // ]);
                }
            }

        }
        //return ($data);
        if(count($insert) > 0){
            $chunk = collect($insert)->chunk(100);
            foreach ($chunk as $key => $n) {        
                DB::table('hr_bill')->insertOrIgnore(collect($n)->toArray());
            }
        }
        return 'success';
    }
    public function tiffinBillCheck()
    {
        $date = '2021-11-';
        $data = [];
        for ($i=16; $i <= 30; $i++) { 
            $getBill = DB::table('hr_bill')
            ->select('hr_bill.id', 'hr_bill.as_id', 'hr_bill.bill_type', 'hr_bill.bill_date', 'b.as_unit_id')
            ->where('bill_date', date('Y-m-d', strtotime($date.$i)))
            ->join('hr_as_basic_info AS b', 'hr_bill.as_id', 'b.as_id')
            ->whereIn('b.as_unit_id', [1,4,5])
            ->where('bill_type', 1)
            ->get()
            ->toArray();
            
            $getatt = DB::table('hr_attendance_mbm')
            ->select(DB::raw("CONCAT(in_date,as_id) AS asdate"), 'in_date', 'in_time', 'out_time', 'remarks')
            ->where('in_date', date('Y-m-d', strtotime($date.$i)))
            //->where('hr_shift_code', 'HH3')
            ->get()
            ->keyBy('asdate')
            ->toArray();
            
            foreach ($getBill as $value) {
                if(isset($getatt[$value->bill_date.$value->as_id])){
                    //$data[] = $value;
                    $attendance = $getatt[$value->bill_date.$value->as_id];
                    if($attendance->out_time == '' || $attendance->out_time == null || $attendance->remarks == 'DSI'){
                        DB::table('hr_bill')->where('id', $value->id)->delete();
                        $data[] = $value;
                    }
                    // $outPunch = $attendance->out_time;
                    // $eligibleTime = $attendance->in_date.' 20:45:00';
                    // if(strtotime($outPunch) < strtotime($eligibleTime)){
                    //     //$data[] = $attendance;
                    //     $data[] = DB::table('hr_bill')->where('id', $value->id)->delete();
                    // }
                    
                    // $inPunch = $attendance->in_time;
                    // $eligibleInTime = $attendance->in_date.' 11:00:00';
                    // if(strtotime($inPunch) > strtotime($eligibleInTime)){
                    //     $data[] = $attendance;
                    //     //$data[] = DB::table('hr_bill')->where('id', $value->id)->delete();
                    // }
                    
                    
                }
                else{
                    $data[] = $value;
                    //DB::table('hr_bill')->where('id', $value->id)->delete();
                }
            }
        }
        
        return ($data);
    }
    public function workFromHomeBill()
    {
        // $data = [];
        // $outsideCheck= DB::table('hr_outside')
        //     ->select('hr_as_basic_info.as_id', 'hr_outside.start_date', 'hr_outside.end_date', 'hr_as_basic_info.as_unit_id')
        //     ->where('start_date','>','2021-04-13')
        //     ->where('requested_location','WFHOME')
        //     ->join('hr_as_basic_info', 'hr_outside.as_id', 'hr_as_basic_info.associate_id')
        //     ->get();
        // foreach($outsideCheck as $outside){
        //     $tableName = get_att_table($outside->as_unit_id);
        //     $data[] = DB::table($tableName)
        //     ->where('as_id', $outside->as_id)
        //     ->whereNotNull('in_unit')
        //     ->whereBetween('in_date', [$outside->start_date, $outside->end_date])
        //     ->get();
        // }
        // return ($data);
        $data = [];
        $check = [];
        $outsideCheck= DB::table('hr_outside')
            ->select('hr_as_basic_info.as_id', 'hr_outside.start_date', 'hr_outside.end_date')
            ->where('start_date','>','2021-05-01')
            //->where('status',1)
            ->where('requested_location','WFHOME')
            ->join('hr_as_basic_info', 'hr_outside.as_id', 'hr_as_basic_info.associate_id')
            ->get();
        foreach($outsideCheck as $outside){
            $data = DB::table('hr_bill')
            ->where('as_id', $outside->as_id)
            ->whereBetween('bill_date', [$outside->start_date, $outside->end_date])
            ->get();
            
            foreach($data as $v){
                $check[] = DB::table('hr_bill')
                ->where('id', $v->id)
                ->delete();
            }
        }
        return ($check);
    }
    public function hoLocationbillEntry()
    {
        $data = [];
        $date = '2021-04-14';
        $getEmployee = DB::table('hr_as_basic_info AS b')
        ->select('b.as_id', 'b.as_unit_id', 'b.as_name')
        ->whereIn('b.as_unit_id', [2])
        ->where('b.as_location', 12)
        ->where('b.as_status', 1)
        ->pluck('as_id');

        $getTable = DB::table('hr_attendance_aql AS m')
        ->select('m.as_id', 'm.in_date', 'm.out_time', 'm.hr_shift_code', 's.hr_shift_night_flag')
        ->join('hr_shift AS s', 'm.hr_shift_code', 's.hr_shift_code')
        ->where('m.in_date','>', '2021-04-13')
        ->whereIn('m.as_id', $getEmployee)
        ->where('s.hr_shift_night_flag',0)
        ->get();

        DB::beginTransaction();
        $insert = [];
        try {
            foreach ($getTable as $key => $value) {
                $insert[] = [
                    'as_id' => $value->as_id,
                    'bill_date' => $value->in_date,
                    'bill_type' => 4,
                    'amount' => 30,
                    'pay_status' => 0
                ];
            }
            
            if(count($insert) > 0){
                $chunk = collect($insert)->chunk(200);
                foreach ($chunk as $key => $n) {        
                    DB::table('hr_bill')->insertOrIgnore(collect($n)->toArray());
                }
            }
            DB::commit();
            return 'success';
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }

    }
    public function billSummaryReport()
    {
        $getEmployee = DB::table('hr_as_basic_info AS b')
        ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
        ->whereIn('b.as_location', auth()->user()->location_permissions())
        ->where('b.as_status', 1)
        ->get()
        ->keyBy('as_id');

        $empId = collect($getEmployee)->pluck('as_id');

        $getBill = DB::table('hr_bill_extra')
        ->select(DB::raw('sum(amount) AS empTotal'), 'as_id')
        ->whereIn('as_id', $empId)
        ->whereBetween('bill_date', ['2021-04-15', '2021-04-22'])
        ->where('bill_type', 4)
        ->groupBy('as_id')
        ->get();
       
        $getB = collect($getBill)->map(function($q) use ($getEmployee){
            $q->as_ot      = $getEmployee[$q->as_id]->as_ot;
            return $q;
        });

        $data = collect($getB);
        $sum  = (object)[];
        $sum->totalAmount      = $data->sum('empTotal');
        $sum->totalEmp      = $data->count();
        $sum->otEmpAmount      = $data->where('as_ot', 1)->sum('empTotal');
        $sum->nonOtEmpAmount      = $data->where('as_ot', 0)->sum('empTotal');
        $sum->otEmp      = $data->where('as_ot', 1)->count();
        $sum->nonOtEmp      = $data->where('as_ot', 0)->count();
        print_r($sum);exit;
    }
    public function billRemove()
    {
        // $data = DB::table("hr_bill AS t")
        // ->select('t.*', 'b.as_designation_id', 'b.as_location', 'b.as_subsection_id', 'b.as_department_id', 'b.as_unit_id')
        // ->leftJoin('hr_as_basic_info AS b', function($q){
        //     $q->on('b.as_id', 't.as_id');
        // })
        // //->where('t.bill_date', '2021-04-13')
        // //->whereNotIn('b.as_department_id', [67])
        // ->whereIn('b.as_unit_id', [1,4,5])
        // //->whereIn('b.as_location', [11])
        // ->where('t.bill_type', 4)
        // ->delete();

        // return ($data);
        $getBill = DB::table("hr_bill AS t")
        ->select('t.*', 'b.as_designation_id', 'b.as_location', 'b.as_subsection_id', 'b.as_department_id', 'b.as_unit_id', 'b.associate_id')
        ->leftJoin('hr_as_basic_info AS b', function($q){
            $q->on('b.as_id', 't.as_id');
        })
        //->whereIn('b.associate_id', ['10L000398A','10M000397A','15H000395A','17L090029M','18K000392A','21A090031M','19J000390A','18L000391A','18K000393A','21A090030M'])
        ->whereIn('t.bill_type', [1,2])
        ->whereIn('b.as_unit_id', [1,4,5])
        //->where('b.as_subsection_id', 108)
        //->whereIn('b.as_location', [7])
        //->whereIn('b.as_location', [12,13])
        ->where('t.bill_date', '>=', '2021-11-01')
        ->where('t.bill_date', '<=', '2021-11-31')
        //->where('t.created_at', '>', '2021-06-02 15:00:00')
        ->whereIn('b.as_subsection_id', [185,108])
        //->whereIn('b.as_designation_id', [408,397,218,229,204,211,356,470,407,221,293,375,449,196,454,402,463])
        //->whereIn('b.as_department_id', [53,56]) //mbm
        //->whereIn('b.as_department_id', [53,64])// ceil
        ->delete();
        return ($getBill);

    }
    public function billUpdate()
    {
        
        // $getBill = DB::table("hr_bill AS t")
        // ->select('t.*', 'b.as_designation_id', 'b.as_location', 'b.as_subsection_id', 'b.as_department_id', 'b.as_section_id', 'b.as_ot', 'b.associate_id')
        // ->leftJoin('hr_as_basic_info AS b', function($q){
        //     $q->on('b.as_id', 't.as_id');
        // })
        // ->where('t.bill_type', 1)
        // ->whereBetween('t.bill_date', ['2021-08-01', '2021-08-31'])
        // ->whereIn('b.as_unit_id', [2])
        // //->where('b.as_ot', 1)
        // ->whereNotIn('b.as_section_id', [127,171])
        // //->whereIn('b.as_designation_id', [255,256, 271, 272, 302, 315, 355, 371])
        // ->whereNotIn('b.as_section_id', [124,170])
        // //->whereIn('b.as_designation_id', [225,354, 432, 436])
        // //->where('t.pay_status', 1)
        // ->whereIn('b.as_location', [7])
        // ->whereIn('t.amount', [25, 40])
        // ->get();
        
        // return $getBill;
        
        
        $getBill = DB::table("hr_bill AS t")
        ->select('t.*', 'b.as_designation_id', 'b.as_location', 'b.as_subsection_id', 'b.as_department_id', 'b.as_section_id', 'b.as_ot')
        ->leftJoin('hr_as_basic_info AS b', function($q){
            $q->on('b.as_id', 't.as_id');
        })
        ->where('t.bill_type', 1)
        ->whereBetween('t.bill_date', ['2021-10-01', '2021-10-31'])
        ->whereIn('b.as_unit_id', [2])
        //->where('b.as_ot', 1)
        ->whereIn('b.as_section_id', [127,171])
        ->whereIn('b.as_designation_id', [255,256, 271, 272, 302, 315, 355, 371])
        //->whereIn('b.as_section_id', [124,170])
        //->whereIn('b.as_designation_id', [225,354, 432, 436])
        //->where('t.pay_status', 1)
        //->groupBy('as_id', true);
        ->whereIn('b.as_location', [7])
        ->where('t.amount', 15)
        //->get();
        ->update(['amount' => 25]);
        return ($getBill);

    }
    public function employeeCheck()
    {
        $location = location_by_id();
        $department = department_by_id();
        $designation = designation_by_id();
        
        $getEmployee = DB::table('hr_as_basic_info AS b')
        ->select('b.as_oracle_code', 'b.associate_id', 'ben.hr_bn_associate_name', 'b.as_status', 'b.as_name')
        ->leftJoin('hr_employee_bengali AS ben', 'b.associate_id', 'ben.hr_bn_associate_id')
        ->whereIn('b.as_unit_id', [2])
        ->whereNull('ben.hr_bn_associate_name')
        ->whereIn('b.as_status', [1])
        ->get();
        $d = [];
        foreach ($getEmployee as $emp) {
            $d[] = array(
                'Associate Id' => $emp->associate_id,
                'Oracle Id' => $emp->as_oracle_code,
                'Name' => $emp->as_name,
                'Status' =>  $emp->as_status == 1?'Active':'Maternity'
                
            );
        }
        return (new FastExcel(collect($d)))->download('Employee Bangla info missing.xlsx');
        
        $getEmployee = DB::table('hr_as_basic_info AS b')
        //->select('b.as_oracle_code', 'b.associate_id', 'ben.hr_bn_associate_name', 'b.as_status', 'b.as_name')
        ->leftJoin('hr_employee_bengali AS ben', 'b.associate_id', 'ben.hr_bn_associate_id')
        ->whereIn('b.as_unit_id', [1,4,5])
        //->whereNull('ben.hr_bn_associate_name')
        ->whereIn('b.as_status', [1])
        ->where('as_location', 12)
        ->where('as_shift_id', '!=', 'Day Head Office')
        ->get();
        $d = [];
        foreach ($getEmployee as $emp) {
            $d[] = array(
                'Associate Id' => $emp->associate_id,
                'Oracle Id' => $emp->as_oracle_code,
                'Location' => $location[$emp->as_location]['hr_location_name'],
                'Shift' => $emp->as_shift_id,
                'Name' => $emp->as_name,
                'Department' => $department[$emp->as_department_id]['hr_department_name'],
                'Designation' => $designation[$emp->as_designation_id]['hr_designation_name'],
                'Status' =>  $emp->as_status == 1?'Active':'Maternity'
                
            );
        }
        return (new FastExcel(collect($d)))->download('Employee info HO missing.xlsx');
        dd($getEmployee);
    }
    
    public function incrementHistory()
    {
        $getData = [];
        $getBasic = DB::table('hr_as_basic_info AS b')
        ->select('as_oracle_code', 'associate_id', 'as_status', 'as_doj', 'as_name')
        ->whereIn('b.as_unit_id', [3])
        ->whereIn('b.as_location', [9])
        ->get();

        // $getIncrement = DB::table('hr_increment')
        // ->get()
        // ->keyBy('associate_id')
        // ->toArray();

        $count = 0;
        $macth = [];
        foreach ($getBasic as $key => $info) {
            foreach ($getData as $key1 => $value) {
                if($info->as_oracle_code == $value['PID']){
                    $getIncrement = DB::table('hr_increment')->where('associate_id', $info->associate_id)->where('effective_date', date('Y-m-d', strtotime($value['L_INCR_DT'])))->first();
                    // ++$count;
                    if($getIncrement != null){
                            // $macth[$info->associate_id] = $value;
                            ++$count;
                        
                        $macth[] = DB::table('hr_increment')
                        ->where('id', $getIncrement->id)
                        ->update([
                            'associate_id' => $info->associate_id,
                            'current_salary' => ($value['CURRENT_SALARY'] - $value['L_INCR_AMT']),
                            'increment_type' => 2,
                            'increment_amount' => $value['L_INCR_AMT'],
                            'amount_type' => 1,
                            'applied_date' => date('Y-m-d', strtotime($value['L_INCR_DT'])),
                            'eligible_date' => date('Y-m-d', strtotime($value['L_INCR_DT'])),
                            'effective_date' => date('Y-m-d', strtotime($value['L_INCR_DT'])),
                            'status' => 1,
                        ]);

                    }
                }
            }
        }

        // return $count;
        return count($macth);
    }
    public function benefitUpdate()
    {
        $getBasic = DB::table('hr_as_basic_info AS b')
        ->select('b.as_oracle_code', 'b.associate_id', 'b.as_status', 'b.as_doj', 'b.as_name', 'b.as_unit_id', 'a.ben_current_salary')
        // ->whereIn('b.as_unit_id', [8])
        ->leftJoin('hr_benefits AS a', function($q){
            $q->on('a.ben_as_id', 'b.associate_id');
        })
        ->where('as_status', '!=', 0)
        ->get();
        // return $getBasic;
        $getIncrement = DB::table('hr_increment')
        ->get()
        ->keyBy('associate_id')
        ->toArray();

        $count = 0;
        $macth = [];
        foreach ($getBasic as $key => $info) {
            foreach ($getIncrement as $key => $value) {
                if($info->associate_id == $value->associate_id && (($value->current_salary+$value->increment_amount) > $info->ben_current_salary)){

                    $value->ben_current_salary = $info->ben_current_salary;
                    $value->as_unit_id = $info->as_unit_id;
                    $macth[] = $value;

                }
            }
        }

        $tomacth = [];
        return $macth;
        foreach ($macth as $key1 => $val) {
            $ben = DB::table('hr_benefits as b')
                            ->leftJoin('hr_as_basic_info as a','a.associate_id','b.ben_as_id')
                            ->where('a.associate_id', $val->associate_id)
                            ->first();
            if($ben != null){
                $up['ben_current_salary'] = ($val->current_salary + $val->increment_amount);
                $up['ben_basic'] = ceil(($up['ben_current_salary']-1850)/1.5);
                $up['ben_house_rent'] = $up['ben_current_salary'] -1850 - $up['ben_basic'];

                if($ben->ben_bank_amount > 0){
                    $up['ben_bank_amount'] = $up['ben_current_salary'];
                    $up['ben_cash_amount'] = 0;
                }else{
                    $up['ben_cash_amount'] = $up['ben_current_salary'];
                    $up['ben_bank_amount'] = 0;
                }
                $tomacth[] = $up;
                //$exist[$key1] = DB::table('hr_benefits')->where('ben_id', $ben->ben_id)->update($up);
            }
        }
        return ($exist);
    }
    
    public function incrementMarge()
    {
        $getIncrement = DB::table('hr_increment')
        ->select('associate_id', 'increment_type', 'applied_date', 'eligible_date', DB::raw('COUNT(*) AS count'))
        ->groupBy(['associate_id', 'increment_type', 'applied_date', 'eligible_date'])
        ->having('count', '>', 1)
        ->get();
        $increment = [];
        foreach ($getIncrement as $key => $value) {
            $increment[] = DB::table('hr_increment')
            ->select('associate_id', 'applied_date', DB::raw('sum(increment_amount) as amount'), DB::raw('MAX(id) AS maxid'), DB::raw('MIN(id) AS minid'))
            ->where('associate_id', $value->associate_id)
            ->where('applied_date', $value->applied_date)
            ->groupBy('associate_id')
            ->first();
        }

        foreach ($increment as $key1 => $va) {
            DB::table("hr_increment")
            ->where('associate_id', $va->associate_id)
            ->where('id', $va->maxid)
            ->update([
                'increment_amount' => $va->amount
            ]);

            DB::table('hr_increment')
            ->where('id', $va->minid)
            ->delete();
        }
        return 'success';
    }
    public function checkHoliday(){
        $getEmployee = DB::table('hr_as_basic_info')->where('associate_id', '21C700686P')->first();
        $firstDateMonth = '2021-04-01';
        $lastDateMonth = '2021-04-30';
        $month = '04';
        $year = 2021;
        $yearMonth = $year.'-'.$month;
        $empdoj = $getEmployee->as_doj;
        $empdojMonth = date('Y-m', strtotime($getEmployee->as_doj));
        $empdojDay = date('d', strtotime($getEmployee->as_doj));
        $tableName = get_att_table($getEmployee->as_unit_id);
        $rosterOTCount = HolidayRoaster::where('year', $year)
        ->where('month', $month)
        ->where('as_id', $getEmployee->associate_id)
        ->where('date','>=', $firstDateMonth)
        ->where('date','<=', $lastDateMonth)
        ->where('remarks', 'OT')
        ->get();
        
        $rosterOtData = $rosterOTCount->pluck('date');

        $otDayCount = 0;
        $totalOt = count($rosterOTCount);
        // return $rosterOTCount;
        foreach ($rosterOTCount as $otc) {
            $checkAtt = DB::table($tableName)
            ->where('as_id', $getEmployee->as_id)
            ->where('in_date', $otc->date)
            ->first();
            if($checkAtt != null){
                $data[] = $checkAtt; 
                $otDayCount += 1;
            }
        }
        if($getEmployee->shift_roaster_status == 1){
            // check holiday roaster employee
            $getHoliday = HolidayRoaster::where('year', $year)
            ->where('month', $month)
            ->where('as_id', $getEmployee->associate_id)
            ->where('date','>=', $firstDateMonth)
            ->where('date','<=', $lastDateMonth)
            ->where('remarks', 'Holiday')
            ->count();
            $getHoliday = $getHoliday + ($totalOt - $otDayCount);
        }else{
            // check holiday roaster employee
            $RosterHolidayCount = HolidayRoaster::where('year', $year)
            ->where('month', $month)
            ->where('as_id', $getEmployee->associate_id)
            ->where('date','>=', $firstDateMonth)
            ->where('date','<=', $lastDateMonth)
            ->where('remarks', 'Holiday')
            ->count();
            return $RosterHolidayCount;
            // check General roaster employee
            $RosterGeneralCount = HolidayRoaster::where('year', $year)
            ->where('month', $month)
            ->where('as_id', $getEmployee->associate_id)
            ->where('date','>=', $firstDateMonth)
            ->where('date','<=', $lastDateMonth)
            ->where('remarks', 'General')
            ->count();
            
            // check holiday shift employee
            $query = YearlyHolyDay::
                where('hr_yhp_unit', $getEmployee->as_unit_id)
                ->where('hr_yhp_dates_of_holidays','>=', $firstDateMonth)
                ->where('hr_yhp_dates_of_holidays','<=', $lastDateMonth)
                ->where('hr_yhp_open_status', 0);
                if($empdojMonth == $yearMonth){
                    $query->where('hr_yhp_dates_of_holidays','>=', $empdoj);
                }

                if(count($rosterOtData) > 0){
                    $query->whereNotIn('hr_yhp_dates_of_holidays', $rosterOtData);
                }
            $shiftHolidayCount = $query->count();
            // OT check 
            $queryOt = YearlyHolyDay::
                where('hr_yhp_unit', $getEmployee->as_unit_id)
                ->where('hr_yhp_dates_of_holidays','>=', $firstDateMonth)
                ->where('hr_yhp_dates_of_holidays','<=', $lastDateMonth)
                ->where('hr_yhp_open_status', 2);
                if($empdojMonth == $yearMonth){
                    $query->where('hr_yhp_dates_of_holidays','>=', $empdoj);
                }
                
                if(count($rosterOtData) > 0){
                    $queryOt->whereNotIn('hr_yhp_dates_of_holidays', $rosterOtData);
                }
            $getShiftOt = $queryOt->get();
            $shiftOtCount = $getShiftOt->count();
            $shiftOtDayCout = 0;
            foreach ($getShiftOt as $shiftOt) {
                $checkAtt = DB::table($tableName)
                ->where('as_id', $getEmployee->as_id)
                ->where('in_date', $shiftOt->hr_yhp_dates_of_holidays)
                ->first();
                if($checkAtt != null){
                    $shiftOtDayCout += 1;
                }
            }
            
            $shiftHolidayCount = $shiftHolidayCount + ($totalOt - $otDayCount) + ($shiftOtCount - $shiftOtDayCout);

            if($RosterHolidayCount > 0 || $RosterGeneralCount > 0){
                $getHoliday = ($RosterHolidayCount + $shiftHolidayCount) - $RosterGeneralCount;
            }else{
                $getHoliday = $shiftHolidayCount;
            }
        }
        $getHoliday = $getHoliday < 0 ? 0:$getHoliday;
        return $getHoliday;
    }
    
    public function getAttCheck()
    {
       
        $getData = [];

        $getEmployee = DB::table('hr_as_basic_info')
        ->whereIn('as_id', $getData)
        ->select('as_name', 'as_oracle_code', 'as_id')
        ->get()
        ->keyBy('as_id');

        $getAtt = DB::table('hr_attendance_ceil AS b')
        ->select('b.*', DB::raw('(TIMESTAMPDIFF(minute, in_time, out_time) - s.hr_shift_break_time) as hourDuration'))
        ->leftJoin('hr_shift AS s', 'b.hr_shift_code', 's.hr_shift_code')
        ->whereIn('b.as_id', $getData)
        ->where('b.in_date', '>=', '2021-03-20')
        ->where('b.in_date', '<=', '2021-03-25')
        ->orderBy('b.in_date', 'asc')
        ->get();

        $d = [];
        foreach ($getAtt as $att) {
            $d[] = array(
                'Oracle Id' => $getEmployee[$att->as_id]->as_oracle_code,
                'Name' => $getEmployee[$att->as_id]->as_name,
                'Date' =>  date('m/d/Y', strtotime($att->in_date)),
                'In Time' => date('H:i:s', strtotime($att->in_time)),
                'Out Time' => date('H:i:s', strtotime($att->out_time)),
                'Working Hour' => round($att->hourDuration/60, 2)
            );
        }
        return (new FastExcel(collect($d)))->download('Attendance History.xlsx');
        return $getAtt;
    }
    
    public function holidayAttCheck()
    {

        $getEmployee = DB::table('hr_as_basic_info')
        ->whereIn('as_unit_id', [8])
        ->where('as_status', 1)
        // ->where('as_location', 9)
        ->where('shift_roaster_status', 1)
        ->select('associate_id', 'as_id', 'as_unit_id')
        ->get();
        $employeeKey = collect($getEmployee)->pluck('associate_id');
        $HolidayRoaster = HolidayRoaster::
        where('year', 2021)
        ->where('month', '04')
        ->whereIn('as_id', $employeeKey)
        ->where('remarks', 'Holiday')
        ->get()
        ->groupBy('as_id', true);
        // return $employeeKey;
        $data = [];
        // return $HolidayRoaster;
        foreach ($getEmployee as $key => $va) {
            //if(isset($HolidayRoaster[$va->associate_id])){
            foreach ($HolidayRoaster[$va->associate_id] as $key => $value) {
                // return $va->as_id;
                $dat = DB::table('hr_attendance_cew')
                    ->where('in_date', $value->date)
                    ->where('as_id', $va->as_id)
                    ->first();
                    
                if($dat != null){
                    
                    $data[$va->associate_id] = $dat;
                    DB::table('hr_attendance_cew')
                    ->where('id', $dat->id)
                    ->delete();
                }
            }
            //}
        }
        // }
        return $data;
        
        $getEmployee = DB::table('hr_as_basic_info')
        ->whereIn('as_unit_id', [2])
        ->where('as_status', 1)
        ->where('shift_roaster_status', 0)
        ->select('associate_id', 'as_id', 'as_unit_id')
        ->get();

        $data = [];
        $roasterData = YearlyHolyDay::
        whereIn('hr_yhp_unit', [2])
        ->where('hr_yhp_dates_of_holidays','>=', '2021-04-01')
        ->where('hr_yhp_dates_of_holidays','<=', '2021-04-31')
        ->where('hr_yhp_open_status', 0)
        ->get();
            foreach ($getEmployee as $key => $va) {

                if(count($roasterData) > 0){
                    foreach ($roasterData as $key => $value) {
                        // return $va->as_id;
                        $dat = DB::table('hr_attendance_aql')
                            ->where('in_date', $value->hr_yhp_dates_of_holidays)
                            ->where('as_id', $va->as_id)
                            ->first();
                            
                        if($dat != null){
                            $data[$va->associate_id] = $dat;
                        }
                    }
                    
                }
                
            }
        // }
        //return $data;
            $fj = [];
            foreach ($data as $key => $value) {
                $fd = HolidayRoaster::select('date','remarks')
                ->where('as_id', $key)
                ->where('date', $value->in_date)
                ->where('remarks', 'Holiday')
                ->first();
                
                
                if($fd != null){
                    $fj[$key] = $value;
                }
            }
        return $fj;
        $roasterData = HolidayRoaster::select('date','remarks')
                ->where('year', $year)
                ->where('month', $month)
                ->where('as_id', $getEmployee->associate_id)
                ->where('date','>=', $firstDateMonth)
                ->where('date','<=', $lastDateMonth)
                ->get();

        $rosterOtData = collect($roasterData)
            ->where('remarks', 'OT')
            ->pluck('date');

        $otDayCount = 0;
        $totalOt = count($rosterOtData);
        // return $rosterOTCount;
        $otDayCount = DB::table($this->tableName)
            ->where('as_id', $getEmployee->as_id)
            ->whereIn('in_date', $rosterOtData)
            ->count();


        if($getEmployee->shift_roaster_status == 1){
            // check holiday roaster employee
            $getHoliday = collect($roasterData)
                ->where('remarks', 'Holiday')
                ->count();
            $getHoliday = $getHoliday + ($totalOt - $otDayCount);
        }else{
            // check holiday roaster employee
            $RosterHolidayCount = collect($roasterData)
                ->where('remarks', 'Holiday')
                ->count();
            // check General roaster employee
            $RosterGeneralCount = collect($roasterData)
                ->where('remarks', 'General')
                ->count();
            
            // check holiday shift employee
            $query = YearlyHolyDay::
                where('hr_yhp_unit', $getEmployee->as_unit_id)
                ->where('hr_yhp_dates_of_holidays','>=', $firstDateMonth)
                ->where('hr_yhp_dates_of_holidays','<=', $lastDateMonth)
                ->where('hr_yhp_open_status', 0);
                if($empdojMonth == $yearMonth){
                    $query->where('hr_yhp_dates_of_holidays','>=', $empdoj);
                }

                if(count($rosterOtData) > 0){
                    $query->whereNotIn('hr_yhp_dates_of_holidays', $rosterOtData);
                }
            $shiftHolidayCount = $query->count();
            // OT check 
            $queryOt = YearlyHolyDay::
                where('hr_yhp_unit', $getEmployee->as_unit_id)
                ->where('hr_yhp_dates_of_holidays','>=', $firstDateMonth)
                ->where('hr_yhp_dates_of_holidays','<=', $lastDateMonth)
                ->where('hr_yhp_open_status', 2);
                if($empdojMonth == $yearMonth){
                    $query->where('hr_yhp_dates_of_holidays','>=', $empdoj);
                }
                
                if(count($rosterOtData) > 0){
                    $queryOt->whereNotIn('hr_yhp_dates_of_holidays', $rosterOtData);
                }
            $getShiftOt = $queryOt->get();
            $shiftOtCount = $getShiftOt->count();
            $shiftOtDayCout = 0;

            foreach ($getShiftOt as $shiftOt) {
                $checkAtt = DB::table($this->tableName)
                ->where('as_id', $getEmployee->as_id)
                ->where('in_date', $shiftOt->hr_yhp_dates_of_holidays)
                ->first();
                if($checkAtt != null){
                    $shiftOtDayCout += 1;
                }
            }
            
            $shiftHolidayCount = $shiftHolidayCount + ($totalOt - $otDayCount) + ($shiftOtCount - $shiftOtDayCout);

            if($RosterHolidayCount > 0 || $RosterGeneralCount > 0){
                $getHoliday = ($RosterHolidayCount + $shiftHolidayCount) - $RosterGeneralCount;
            }else{
                $getHoliday = $shiftHolidayCount;
            }
        }

        $getHoliday = $getHoliday < 0 ? 0:$getHoliday;
    }
    public function extraCheck($value='')
    {
        $section = section_by_id();
        $department = department_by_id();
        
        $getEmployee = DB::table('hr_attendance_mbm AS m')
        ->join('hr_as_basic_info AS b', 'm.as_id', 'b.as_id')
        ->whereIn('m.in_date', ['2021-04-09', '2021-04-23'])
        //->where('b.as_doj', '>', '2021-02-21')
        ->where('b.as_ot', 1)
        ->where('m.ot_hour', 0)
        ->get();
        $d = [];
        foreach ($getEmployee as $att) {
            $d[] = array(
                'Associate Id' => $att->associate_id,
                'Name' => $att->as_name,
                'DOJ' => $att->as_doj,
                'Date' => $att->in_date,
                'Department' => $department[$att->as_department_id]['hr_department_name']??'',
                'Section' => $section[$att->as_section_id]['hr_section_name']??''
            );
            
        }

        return (new FastExcel(collect($d)))->download('general 23,09.xlsx');
        return $getEmployee;
        
        // $check = DB::table('hr_attendance_mbm AS a')
        // ->select('a.as_id', 'b.associate_id', 'b.as_name', 'b.as_section_id', 'b.as_department_id', DB::raw('COUNT(*) AS count'))
        // ->whereIn('a.in_date', ['2021-03-29', '2021-03-30'])
        // // ->where('a.as_id', 8958)
        // ->leftJoin('hr_as_basic_info AS b', function($q){
        //     $q->on('b.as_id', 'a.as_id');
        // })
        // ->groupBy('a.as_id')
        // ->get();

        // $check = DB::table('hr_attendance_mbm AS a')
        // ->select('a.as_id', 'b.associate_id', 'b.as_name', 'b.as_section_id', 'b.as_department_id', DB::raw('COUNT(*) AS count'))
        // ->where('a.hr_shift_code', 'N')
        // ->whereIn('a.in_date', ['2021-03-28'])
        // // ->where('b.as_department_id', 67)
        // ->leftJoin('hr_as_basic_info AS b', function($q){
        //     $q->on('b.as_id', 'a.as_id');
        // })
        // ->groupBy('a.as_id')
        // ->pluck('a.as_id');
        // $acheck = DB::table('hr_attendance_mbm AS a')
        // ->select('a.as_id', 'b.associate_id', 'b.as_name', 'b.as_section_id', 'b.as_department_id', DB::raw('COUNT(*) AS count'))
        // ->whereIn('a.in_date', ['2021-03-29'])
        // ->whereIn('a.as_id',$check)
        // ->leftJoin('hr_as_basic_info AS b', function($q){
        //     $q->on('b.as_id', 'a.as_id');
        // })
        // ->groupBy('a.as_id')
        // ->get();
        $check = DB::table('holiday_roaster AS a')
        ->select('a.as_id', 'b.associate_id', 'b.as_name', 'b.as_section_id', 'b.as_department_id', DB::raw('COUNT(*) AS count'))
        // ->select('as_id', DB::raw('COUNT(*) AS count'))
        ->whereIn('a.date', ['2021-03-29', '2021-03-30'])
        ->where('a.comment', 'Shab-e-Barat')
        ->leftJoin('hr_as_basic_info AS b', function($q){
            $q->on('b.associate_id', 'a.as_id');
        })
        ->groupBy('as_id')
        ->get();

        $d = [];
        foreach ($check as $att) {
            if($att->count == 2){
                $d[] = array(
                    'Associate Id' => $att->associate_id,
                    'Name' => $att->as_name,
                    'Department' => $department[$att->as_department_id]['hr_department_name']??'',
                    'Section' => $section[$att->as_section_id]['hr_section_name']??''
                );
            }
        }

        return (new FastExcel(collect($d)))->download('two days holiday(29,30).xlsx');
        return ($check);
    }
    
    public function addRocket(){
        
            
        
        $data = [];
        $emp = DB::table('hr_as_basic_info as b')
                ->select('b.as_id','b.associate_id','ben.ben_current_salary','b.as_oracle_code', 'ben.bank_no')
                ->leftJoin('hr_benefits as ben','b.associate_id','ben.ben_as_id')
                ->whereIn('b.as_unit_id', [2])
                ->where('b.as_location', 7)
                ->where('b.as_status',1)
                ->whereIn('b.associate_id', array_keys($data))
                ->get()->keyBy('associate_id');
        $dp = [];
        foreach($data as $key => $d){
            //$key = isset($emp[$key])?$key:'A'.$key;
            if(isset($emp[$key])){
                    DB::table('hr_benefits')
                    ->where('ben_as_id', $emp[$key]->associate_id)
                    ->update([
                        'ben_bank_amount' => $emp[$key]->ben_current_salary,
                        'ben_cash_amount' => 0,
                        'bank_name' => 'rocket',
                        'bank_no' => $d['Account']
                      ]);
                  
                $queue = (new ProcessUnitWiseSalary('hr_attendance_ceil', '11', 2021, $emp[$key]->as_id, 30))
                    ->onQueue('salarygenerate')
                    ->delay(Carbon::now()->addSeconds(2));
                    dispatch($queue);
            }else{
                // $dp[] = $key;
                $dp[] = array(
                    'Associate Id' => $key
                );
            }
        }
        //return $dp;
        return (new FastExcel(collect($dp)))->download('Rocket account issue.xlsx');
    }
    
    public function attSpecialCheck($value='')
    {
        $getEmployee = DB::table('hr_as_basic_info AS b')
        ->where('b.as_status', 1)
        ->where('b.as_unit_id', 8)
        ->where('b.as_ot', 0)
        ->pluck('b.as_id');
        
        $getAtt = DB::table('hr_att_special')
        ->whereIn('as_id', $getEmployee)
        ->get();
        foreach($getAtt as $att){
            DB::table('hr_att_special')
            ->where('id', $att->id)
            ->update([
                    'ot_hour' => 0
                ]);
            
        }
        
        foreach($getEmployee as $emp){
            $queue = (new ProcessUnitWiseSalary('hr_attendance_cew', '03', 2021, $emp, 31))
                            ->onQueue('salarygenerate')
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
        }

        return 'success';
    }
    
    public function employeeInfo()
    {
        $getData = [];
        $getEmployee = DB::table('hr_as_basic_info AS b')
        ->whereIn('b.as_oracle_code', $getData)
        ->leftjoin('hr_benefits AS ben', 'b.associate_id', 'ben.ben_as_id')
        ->orderBy('b.as_department_id', 'asc')
        ->get();    

        $designation = designation_by_id();
        $department = department_by_id();
        $location = location_by_id();
        $section = section_by_id();

        $d = [];
        $i = 0;
        foreach ($getEmployee as $emp) {
            $departmentName = $department[$emp->as_department_id]['hr_department_name']??'';
            $sectionName = $section[$emp->as_section_id]['hr_section_name'];
            $d[] = array(
                'Sl' => ++$i,
                'Oracle ID' => $emp->as_oracle_code,
                'Associate ID' => $emp->associate_id,
                'Name' => $emp->as_name,
                'OT Status' => ($emp->as_ot == 1?'OT':'Non OT'),
                'Designation' => $designation[$emp->as_designation_id]['hr_designation_name']??'',
                'Department' => $departmentName.' - '.$sectionName,
                'DOJ' => date('m/d/Y', strtotime($emp->as_doj)),
                'Gross' => $emp->ben_current_salary,
                'Basic' => $emp->ben_basic,
                'House Rent' => $emp->ben_house_rent,
                'Other Part' => ($emp->ben_medical + $emp->ben_transport + $emp->ben_food),
                'Present' => 0,
                'Absent' => 0,
                'OT Hour' => 0,
                'OT Amount' => 0,
                'Attendance Bonus' => 0,
                'Payable Salary' => 0,
                'Bank Amount' => 0,
                'Tax Amount' => 0,
                'Cash Amount' => 0,
                'Stamp Amount' => 0,
                'Net Pay' => 0,
                'Payment Method' => '',
                'Account No.' => '',
                'Location' => $location[$emp->as_location]['hr_location_short_name']
            );
        }
        return (new FastExcel(collect($d)))->download('Employee History(CEW).xlsx');
    }
    
    public function bonusUploadExcel()
    {
        $getData = array(
        	'16K101440N' => 21715,
        	'16D101397N' => 10215,
        	'15J075019L' => 18500,
        	'15J075024L' => 12786,
        	'16E075022L' => 12429,
        	'16K065037K' => 12786,
        	'17L000121A' => 8858,
        	'18K000107A' => 7786,
        );
    
        $getId = array_keys($getData);
        $getEmployee = DB::table('hr_as_basic_info AS b')
        ->join('hr_benefits AS ben', 'b.associate_id', 'ben.ben_as_id')
        ->whereIn('b.associate_id', $getId)
        ->get();
        $insert = [];
        foreach ($getEmployee as $emp) {
            $bonus_amount = $getData[$emp->associate_id]??0;
            $from = '2021-05-14';
            $month = Carbon::parse($emp->as_doj)->diffInMonths($from);
            $bonus_month = $month > 12?12:$month;
            $stamp = 10;
            $netPayable = $bonus_amount - $stamp;
            $insert[$emp->associate_id] = [
                'unit_id' => $emp->as_unit_id,
                'location_id' => $emp->as_location,
                'bonus_rule_id' => 1,
                'associate_id' => $emp->associate_id,
                'bonus_amount' => $bonus_amount,
                'type' => 'normal',
                'gross_salary' => $emp->ben_current_salary,
                'basic' => $emp->ben_basic,
                'medical' => $emp->ben_medical,
                'transport' => $emp->ben_transport,
                'food' => $emp->ben_food,
                'duration' => $bonus_month,
                'stamp' => $stamp,
                'pay_status' => 1,
                'emp_status' => 1,
                'net_payable' => $netPayable,
                'cash_payable' => $netPayable,
                'bank_payable' => 0,
                'override' => 1,
                'bank_name' => null,
                'subsection_id' => $emp->as_subsection_id,
                'designation_id' => $emp->as_designation_id,
                'ot_status' => $emp->as_ot
            ];
        }
        if(count($insert) > 0){
            $chunk = collect($insert)->chunk(200);
            foreach ($chunk as $key => $n) {        
                DB::table('hr_bonus_sheet')->insertOrIgnore(collect($n)->toArray());
            }
        }

        return 'success';
    }
    
    public function jobcardupdate()
    {
        
        $tb = 'hr_attendance_mbm';
        $data = DB::table($tb)
            ->whereIn('in_date',['2021-11-28'])
            ->where('remarks', '!=', 'DSI')
            ->whereNotNull('in_time')
            ->whereNotNull('out_time')
            ->get();
    
        foreach ($data as $key => $v) 
        {
            if($v->in_time && $v->out_time){
                $queue = (new ProcessAttendanceOuttime($tb, $v->id, 8))
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);
            } 
        }
        return count($data);

    }
    
    public function shiftAssigned()
    {
        $year = 2021;
        $month = '07';
        $getEmployee = DB::table('hr_as_basic_info')
        ->where('as_unit_id', 2)
        ->where('as_location', 7)
        ->where('as_status', 1)
        ->whereIn('as_floor_id', [92, 93, 88, 87, 85, 90, 91, 89, 82])
        ->get();
        $empIds = collect($getEmployee)->pluck('associate_id');

        $roster = DB::table('hr_shift_roaster')
        ->whereIn('shift_roaster_associate_id', $empIds)
        ->where('shift_roaster_year', $year)
        ->where('shift_roaster_month', $month)
        ->get()
        ->keyBy('shift_roaster_associate_id');
        // return ($roster);
        $insert = [];
        $update = [];
        foreach ($getEmployee as $key => $emp) {
            $shift = 'Ramadan Day Early 6';
            
            if(isset($roster[$emp->associate_id])){
                $update[$emp->associate_id] = DB::table('hr_shift_roaster')
                ->where('shift_roaster_id', $roster[$emp->associate_id]->shift_roaster_id)
                // ->first();
                ->update(['day_19' => $shift]);
            }else{
                $insert[$emp->associate_id] = [
                    'shift_roaster_associate_id' => $emp->associate_id,
                    'shift_roaster_user_id' => $emp->as_id,
                    'shift_roaster_year' => $year,
                    'shift_roaster_month' => $month,
                    'day_19' => $shift
                ];
                
            }
        }
        // return $update;
        if(count($insert) > 0){
            $chunk = collect($insert)->chunk(200);
            foreach ($chunk as $key => $n) {        
                DB::table('hr_shift_roaster')->insertOrIgnore(collect($n)->toArray());
            }
        }

        return 'success';
        
    }
    public function holidayRoasterUpload(){
        $data = '2021-08-15';

        $getEmployee = DB::table('hr_as_basic_info')
        ->where('as_unit_id', 8)
        ->where('as_location', 11)
        ->where('as_subsection_id', '!=', 108)
        ->where('as_doj', '<', '2021-08-16')
        ->where('as_status', 1)
        ->get()
        ->keyBy('associate_id');
        //return $getEmployee;
        $a = array();
        foreach($getEmployee as $emp){
            $flag = 0;
                $date = date('Y-m-d', strtotime($data));
                if(date('m', strtotime($date)) == '08'){
                    //$a[] = $date; //$getEmployee[$d['associate_id']];
                    // check att
    
                    $checkAtt = DB::table('hr_attendance_cew')
                    ->where('as_id', $emp->as_id)
                    ->where('in_date', $date)
                    ->first();
                    if($checkAtt != null){
                        $flag = 1;
                        $a[] = 'att '.$emp->associate_id.' - '.$date;
                    }
    
                    // check leave
                    $getLeave = DB::table('hr_leave')
                    ->where('leave_ass_id', $emp->associate_id)
                    ->where('leave_from', '<=', $date)
                    ->where('leave_to', '>=', $date)
                    ->where('leave_status',1)
                    ->first();
                    if($getLeave != null){
                        $a[] = $flag.'leave- '.$emp->associate_id;
                    }
                    // check holiday
                    $getHoliday = HolidayRoaster::getHolidayYearMonthAsIdDateWise(2021, '08', $emp->associate_id, $date);
                    if($getHoliday != null && $getHoliday->remarks == 'Holiday'){
                        $flag = 1;
                        $a[] = $flag.'holiday- '.$emp->associate_id;
                    }else if($getHoliday == null){
                        if($emp->shift_roaster_status == 0){
                            $getYearlyHoliday = YearlyHolyDay::getCheckUnitDayWiseHoliday($emp->as_unit_id, $date);
                             
                            if($getYearlyHoliday != null && $getYearlyHoliday->hr_yhp_open_status == 0){
                                $flag = 1;
                                $a[] = $flag.'holiday roster- '.$emp->associate_id;
                            }
                        }
                    }
                    
                    // insert holiday roaster
                    if($flag == 0){
                        DB::table('hr_absent')
                        ->where('associate_id', $emp->associate_id)
                        ->where('date', $date)
                        ->delete();
                        // salary generate
                        DB::table('holiday_roaster')
                        ->insert([
                            'year' => 2021,
                            'month' => '08',
                            'as_id' => $emp->associate_id,
                            'date' => $date,
                            'remarks' => 'Holiday',
                            'comment' => 'National Mourning Day',
                            'status' => 1
                        ]);
    
                        // salary process
                        $queue = (new ProcessUnitWiseSalary('hr_attendance_cew', '08', 2021, $emp->as_id, 31))
                                ->onQueue('salarygenerate')
                                ->delay(Carbon::now()->addSeconds(2));
                                dispatch($queue);
                    }
                    
                }
        }
        // foreach($data as $d){
        //     if(isset($getEmployee[$d['associate_id']])){
        //         $emp = $getEmployee[$d['associate_id']];
        //         $flag = 0;
        //         $date = date('Y-m-d', strtotime($d));
        //         if(date('m', strtotime($date)) == '08'){
        //             //$a[] = $date; //$getEmployee[$d['associate_id']];
        //             // check att
    
        //             $checkAtt = DB::table('hr_attendance_cew')
        //             ->where('as_id', $emp->as_id)
        //             ->where('in_date', $date)
        //             ->first();
        //             if($checkAtt != null){
        //                 $flag = 1;
        //                 $a[] = 'att '.$emp->associate_id.' - '.$date;
        //             }
    
        //             // check leave
        //             $getLeave = DB::table('hr_leave')
        //             ->where('leave_ass_id', $emp->associate_id)
        //             ->where('leave_from', '<=', $date)
        //             ->where('leave_to', '>=', $date)
        //             ->where('leave_status',1)
        //             ->first();
        //             if($getLeave != null){
        //                 $a[] = $flag.'leave- '.$emp->associate_id;
        //             }
        //             // check holiday
        //             $getHoliday = HolidayRoaster::getHolidayYearMonthAsIdDateWise(2021, '08', $emp->associate_id, $date);
        //             if($getHoliday != null && $getHoliday->remarks == 'Holiday'){
        //                 $flag = 1;
        //                 $a[] = $flag.'holiday- '.$emp->associate_id;
        //             }else if($getHoliday == null){
        //                 if($emp->shift_roaster_status == 0){
        //                     $getYearlyHoliday = YearlyHolyDay::getCheckUnitDayWiseHoliday($emp->as_unit_id, $date);
                             
        //                     if($getYearlyHoliday != null && $getYearlyHoliday->hr_yhp_open_status == 0){
        //                         $flag = 1;
        //                         $a[] = $flag.'holiday roster- '.$emp->associate_id;
        //                     }
        //                 }
        //             }
                    
        //             // insert holiday roaster
        //             if($flag == 0){
        //                 DB::table('hr_absent')
        //                 ->where('associate_id', $emp->associate_id)
        //                 ->where('date', $date)
        //                 ->delete();
        //                 // salary generate
        //                 DB::table('holiday_roaster')
        //                 ->insert([
        //                     'year' => 2021,
        //                     'month' => '08',
        //                     'as_id' => $emp->associate_id,
        //                     'date' => $date,
        //                     'remarks' => 'Holiday',
        //                     'comment' => 'National Mourning Day',
        //                     'status' => 1
        //                 ]);
    
        //                 // salary process
        //                 $queue = (new ProcessUnitWiseSalary('hr_attendance_cew', '08', 2021, $emp->as_id, 31))
        //                         ->onQueue('salarygenerate')
        //                         ->delay(Carbon::now()->addSeconds(2));
        //                         dispatch($queue);
        //             }
                    
        //         }


        //     }
        // }
        return $a;
    }
    
    public function checkNoData(){
        $getEmployee = DB::table('hr_as_basic_info')
        ->where('as_unit_id', 8)
        ->where('as_status', 1)
        ->where('as_doj','<', '2021-05-18')
        ->where('associate_id', '21A000535A')
        ->get();
        $date = '2021-05-16';
        $a = [];
        foreach($getEmployee as $emp){
            $flag = 1;
            $checkAtt = DB::table('hr_attendance_cew')
            ->where('as_id', $emp->as_id)
            ->where('in_date', $date)
            ->first();
            if($checkAtt){
                $flag = 0;
                return 'att'.$emp->associate_id;
            }
            // 
            $getLeave = DB::table('hr_leave')
            ->where('leave_ass_id', $emp->associate_id)
            ->where('leave_from', '<=', $date)
            ->where('leave_to', '>=', $date)
            ->where('leave_status',1)
            ->first();
            if($getLeave){
                $flag = 0;
                return 'leave'.$emp->associate_id;
            }

            $getHoliday = HolidayRoaster::getHolidayYearMonthAsIdDateWise(2021, '05', $emp->associate_id, $date);
            if($getHoliday){
                return 'holi'.$emp->associate_id;
                $flag = 0;
            }
            
            $absent = DB::table('hr_absent')
                        ->where('associate_id', $emp->associate_id)
                        ->where('date', $date)
                        ->first();
            if($absent){
                return $emp->associate_id;
                $flag = 0;
            }

            if($flag == 0){
                $a[] = $emp->associate_id;
            }
        }

        return $a;
    }
    public function unitTransfer(){
        $getData = [];
        $unit = 1;
        $location = 6;
        $da = [];
        foreach($getData as $key=> $data){
            $associate = $key;
            $line = $data['Line'];
            // get Floor
            $getfloor = '';
            // if($floor != '' && $floor != null){
            //     $getfloor = DB::table('hr_floor')
            //     ->where('hr_floor_unit_id', 1)
            //     ->where('hr_floor_name', $floor)
            //     ->pluck('hr_floor_id')
            //     ->first();
            // }

            // get Line
            $getline = '';
            if($line != '' && $line != null){
                $getline = DB::table('hr_line')
                ->where('hr_line_unit_id', $unit)
                ->where('hr_line_name', $line)
                ->first();
                if($getline != null){
                    $getfloor = $getline->hr_line_floor_id;
                    $getline = $getline->hr_line_id;
                }
            }
            if($getfloor != '' && $getline != ''){
                $d = [
                    'as_unit_id' => $unit,
                    'as_location' => $location,
                    'as_floor_id' => $getfloor,
                    'as_line_id' => $getline
                ];

                DB::table('hr_as_basic_info')
                ->where('associate_id', $associate)
                ->update($d);
            }else{
                $da[] = $key;
            }
            
        }
        return $da;
    }
    
    public function attendanceEmployeeList()
    {
        $getatt = DB::table('hr_attendance_mbm AS a')
            ->leftJoin('hr_shift AS s', 'a.hr_shift_code', 's.hr_shift_code')
            ->where('a.in_date', '2021-06-03')
            ->whereNotNull('a.out_time')
            ->where('a.out_time', '>', '2021-06-04 00:59:59')
            ->where('s.hr_shift_night_flag', 0)
            ->get();
        $getEmployee = DB::table('hr_as_basic_info')
        ->whereIn('as_unit_id', [1,4,5])
        ->where('as_status', 1)
        ->get()
        ->keyBy('as_id');
        $department = department_by_id();
        $designation = designation_by_id();
        $d = [];
        foreach ($getatt as $att) {
            $emp = $getEmployee[$att->as_id];
            $d[] = array(
                'Associate Id' => $emp->associate_id,
                'Oracle Id' => $emp->as_oracle_code,
                'Name' => $emp->as_name,
                'Designation' => $designation[$emp->as_designation_id]['hr_designation_name']??'',
                'Department' => $department[$emp->as_department_id]['hr_department_name']??'',
                'Employee Type' => $emp->as_emp_type_id==3?'Worker':'Staff',
                'In Time' => $att->in_time,
                'Out Time' =>$att->out_time
                
            );
        }
        return (new FastExcel(collect($d)))->download('Employee.xlsx');
        
        $getatt = DB::table('hr_attendance_mbm AS a')
            ->leftJoin('hr_as_basic_info AS b', 'a.as_id', 'b.as_id')
            ->leftJoin('hr_shift AS s', 'a.hr_shift_code', 's.hr_shift_code')
            ->whereBetween('a.in_date', ['2021-05-01', '2021-05-11'])
            //->whereNotNull('a.out_time')
            //->where('a.out_time', '>', '2021-06-03 00:59:59')
            ->where('b.as_ot', 1)
            ->where('s.hr_shift_night_flag', 0)
            ->where('b.as_subsection_id', '!=', 108)
            ->where('b.as_department_id','!=', 67 )
            //->where('b.as_section_id', '!=', 124)
            ->get();
        // $getEmployee = DB::table('hr_as_basic_info')
        // ->whereIn('as_unit_id', [1,4,5])
        // ->where('as_status', 1)
        // ->get()
        // ->keyBy('as_id');
        $department = department_by_id();
        $designation = designation_by_id();
        $d = [];
        foreach ($getatt as $att) {
            
            $d[] = array(
                'Associate Id' => $att->associate_id,
                'Oracle Id' => $att->as_oracle_code,
                'Name' => $att->as_name,
                'Designation' => $designation[$att->as_designation_id]['hr_designation_name']??'',
                'Department' => $department[$att->as_department_id]['hr_department_name']??'',
                'Employee Type' => $att->as_emp_type_id==3?'Worker':'Staff',
                'Date' => $att->in_date,
                'In Time' => $att->in_time,
                'Out Time' =>$att->out_time,
                'Shift' => $att->hr_shift_name
                
            );
        }
        return (new FastExcel(collect($d)))->download('Employee.xlsx');
    }
    
    public function lateCountUpdate(){
        $getatt = DB::table('hr_attendance_cew')
            ->where('late_status', 1)
            ->whereBetween('in_date', ['2021-05-01', '2021-05-31'])
            ->where('in_time', 'LIKE', '%04:%')
            ->update([
                'late_status'=> 0
            ]);
        return $getatt;
    }
    public function auditHistory()
    {
        $month = '05';
        $year = '2021';
        $unitId = 1;
        $getEmployee = DB::table("hr_as_basic_info")
        //->where('as_unit_id', $unitId)
        ->get()
        ->keyBy('as_id');

        $getAudit = DB::table('salary_audit_individual')
        //->where('unit_id', $unitId)
        ->where('month', $month)
        ->where('year', $year)
        ->get();

        $department = department_by_id();
        $designation = designation_by_id();
        $unit = unit_by_id();
        $d = [];
        foreach ($getAudit as $att) {
            $emp = $getEmployee[$att->as_id];
            $d[] = array(
                'Associate Id' => $emp->associate_id,
                'Oracle Id' => $emp->as_oracle_code,
                'Name' => $emp->as_name,
                'Designation' => $designation[$emp->as_designation_id]['hr_designation_name']??'',
                'Department' => $department[$emp->as_department_id]['hr_department_name']??'',
                'Unit' => $unit[$emp->as_unit_id]['hr_unit_name']??'',
                'Year' => $year,
                'Month' => $month
            );
        }
        $unitName = $unit[$unitId]['hr_unit_short_name']??'';
        $file = 'Salary Audit History - ('.$year.'-'.$month.')';
        return (new FastExcel(collect($d)))->download($file.'.xlsx');
    }
    
    public function manualLateRemove()
    {
        $startDate = '2021-06-01';
        $endDate = '2021-06-01';
        $month = '06';
        $year = 2021;
        $tb = 'hr_attendance_mbm';
        $getAtt = DB::table('hr_attendance_mbm AS m')
        ->join('hr_as_basic_info AS b', 'm.as_id', 'b.as_id')
        ->join('hr_monthly_salary AS sa', 'b.associate_id', 'sa.as_id')
        ->join('hr_shift AS s', 's.hr_shift_code', 'm.hr_shift_code')
        ->select('m.*', 's.hr_shift_start_time', 's.hr_shift_night_flag', 'sa.ot_status')
        ->whereBetween('m.in_date', [$startDate, $endDate])
        ->where('s.hr_shift_night_flag', 0)
        ->where('m.late_status', 1)
        ->where('sa.ot_status', 1)
        ->get();
        $d = [];
        return $getAtt;
        foreach ($getAtt as $v) {
            
            DB::table('hr_attendance_mbm')
            ->where('id', $v->id)
            ->update([
                'late_status' => 0,
                'remarks' => 'BM'
            ]);
            
            if($v->out_time){
                $queue = (new ProcessAttendanceOuttime($tb, $v->id, 1))
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);
            } 
        }
        return 'success';
    }
    public function manualInPunch()
    {
        $startDate = '2021-10-16';
        $endDate = '2021-10-31';
        $month = '10';
        $year = 2021;
        $tb = 'hr_attendance_mbm';
        $getAtt = DB::table('hr_attendance_mbm AS m')
        ->join('hr_as_basic_info AS b', 'm.as_id', 'b.as_id')
        ->join('hr_monthly_salary AS sa', 'b.associate_id', 'sa.as_id')
        ->join('hr_shift AS s', 's.hr_shift_code', 'm.hr_shift_code')
        ->select('m.*', 's.hr_shift_start_time')
        ->whereBetween('m.in_date', [$startDate, $endDate])
        ->where(function($query) {
            $query->where('m.remarks', 'DSI')
                  ->orWhereNull('m.in_time');
        })
        ->where('sa.month', $month)
        ->where('sa.year', $year)
        ->where('sa.ot_status', 1)
        ->whereNotIn('b.as_subsection_id', [185,108])
        ->get();
        //return $getAtt;
        $d = [];
        foreach ($getAtt as $v) {
            $inDate = $v->in_date.' '.$v->hr_shift_start_time;
            $inTime = date('Y-m-d H:i:s', strtotime('+5 minutes', strtotime($inDate)));
            $d[] = $inTime;
            DB::table('hr_attendance_mbm')
            ->where('id', $v->id)
            ->update([
                'in_time' => $inTime,
                'late_status' => 1,
                'remarks' => 'BM'
            ]);
            
            if($v->out_time){
                $queue = (new ProcessAttendanceOuttime($tb, $v->id, 1))
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);
            } 
        }
        return $d;
    }
    
    public function incentiveBonusAql($value='')
    {
        $getData = [];
        $d = [];
        $ud = [];
        foreach($getData as $data){
            $query = DB::table('hr_as_basic_info')
            ->where('as_unit_id', 3)
            ->where('as_status', 1);
            if(strlen($data['ID']) > 4){
                $query->where('associate_id', 'LIKE', '%'.$data['ID'].'%');
            }else{
                $query->where('as_oracle_code', 'LIKE', '%'.$data['ID'].'%');
            }
            $employee = $query->first();
            
            $date = '2021-07-10';
            $line = 'A04';
            $amount = $data['10-Jul'];
            if($employee != '' && $employee->as_id != '' && $amount != ''){
                $d[] = [
                    'as_id' => $employee->as_id,
                    'date' => date('Y-m-d', strtotime($date)),
                    'amount' => $amount,
                    'line_id' => $line
                ];
            }else{
                $ud[] = $data;
            }
        }
        //return $ud;
        if(count($d) > 0){
            $chunk = collect($d)->chunk(200);
            foreach ($chunk as $key => $n) {        
                DB::table('hr_incentive_bonus')->insertOrIgnore(collect($n)->toArray());
            }
        }
        return ($ud);
        return count($d);
    }
    
    public function inactiveEmpSalaryRemove()
    {
        $getEmployee = DB::table('hr_as_basic_info')
        ->where('as_status', '!=', 1)
        ->get();
        $month = '07';
        $year = '2021';
        $d = [];
        foreach($getEmployee as $emp){
            $b = DB::table('hr_monthly_salary')
            ->where('month', $month)
            ->where('year', $year)
            ->where('as_id', $emp->associate_id)
            ->where('emp_status', 1)
            ->get();
            if(count($b) > 0){
                $d[] = $b;
            }
        }

        return $d;
    }
    public function holidayEntry()
    {
        $getEmployee = DB::table('hr_as_basic_info')
        ->whereIn('as_unit_id', [1,4,5])
        ->where('as_department_id', '!=', 67)
        ->where('as_subsection_id', '!=', 108)
        ->where('as_status', 1)
        ->get();
        foreach($getEmployee as $emp){
            if($emp->shift_roaster_status == 0){
                DB::table('holiday_roaster')
                ->where('as_id', $emp->associate_id)
                ->whereIn('date', ['2021-07-24', '2021-07-25', '2021-07-26', '2021-07-27'])
                ->delete();
            }else{
                DB::table('holiday_roaster')
                ->updateOrInsert([
                    'as_id' => $emp->associate_id,
                    'date'  => '2021-07-24'
                ],
                [
                    'year' => '2021',
                    'month' => '07',
                    'remarks' => 'Holiday',
                    'comment' => 'Eid UL Adha Replace Holiday(04-06-2021)',
                    'status' => 1
                ]);

                DB::table('holiday_roaster')
                ->updateOrInsert([
                    'as_id' => $emp->associate_id,
                    'date'  => '2021-07-25'
                ],
                [
                    'year' => '2021',
                    'month' => '07',
                    'remarks' => 'Holiday',
                    'comment' => 'Eid UL Adha Replace Holiday(18-06-2021)',
                    'status' => 1
                ]);

                DB::table('holiday_roaster')
                ->updateOrInsert([
                    'as_id' => $emp->associate_id,
                    'date'  => '2021-07-26'
                ],
                [
                    'year' => '2021',
                    'month' => '07',
                    'remarks' => 'Holiday',
                    'comment' => 'Eid UL Adha Replace Holiday(02-07-2021)',
                    'status' => 1
                ]);

                DB::table('holiday_roaster')
                ->updateOrInsert([
                    'as_id' => $emp->associate_id,
                    'date'  => '2021-07-27'
                ],
                [
                    'year' => '2021',
                    'month' => '07',
                    'remarks' => 'Holiday',
                    'comment' => 'Eid UL Adha Replace Holiday(16-07-2021)',
                    'status' => 1
                ]);
            }
        }
        return 'success';
    }
    public function holidayRosterEntry()
    {
        $getEmployee = DB::table('hr_as_basic_info')
        ->whereIn('as_unit_id', [2])
        ->where('as_location', 7)
        // ->where('as_department_id', '!=', 67)
        // ->where('as_subsection_id', '!=', 108)
        ->where('as_status', 1)
        ->get()
        ->keyBy('associate_id');

        $getData = [];
        $d = [];
        foreach($getData as $key=> $value){
            if(isset($getEmployee[$key])){
                $date = date('Y-m-d', strtotime($value['Leave Date']));
                $comment = $value['Comment'];
                $alt = date('Y-m-d', strtotime($value['Al Date']));
                // return $date.' '.$comment.' '.$alt;
                // check leave
                $l = DB::table('hr_leave')
                ->where('leave_ass_id', $getEmployee[$key]->associate_id)
                ->where('leave_from','<=', $date)
                ->where('leave_to','>=', $date)
                ->first();
                if($l != null){
                    $d[] = $getEmployee[$key]->associate_id;
                    continue;
                }else{
                    // check Attendance
                    DB::table('hr_attendance_ceil')
                    ->where('as_id', $getEmployee[$key]->as_id)
                    ->where('in_date', $date)
                    ->delete();

                    // check absent
                    DB::table('hr_absent')
                    ->where('associate_id', $getEmployee[$key]->associate_id)
                    ->where('date', $date)
                    ->delete();

                    // insert or update holiday
                    DB::table('holiday_roaster')
                        ->updateOrInsert([
                            'as_id' => $getEmployee[$key]->associate_id,
                            'date'  => $date
                        ],
                        [
                            'year' => '2021',
                            'month' => '09',
                            'remarks' => 'Holiday',
                            'comment' => $comment,
                            'reference_date' => $alt,
                            'status' => 1
                        ]);

                    // process salary
                    $queue = (new ProcessUnitWiseSalary('hr_attendance_ceil', '09', 2021, $getEmployee[$key]->as_id, 30))
                        ->onQueue('salarygenerate')
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);
                }

                

                // $date = '2021-07-27';
                // $val = $value[4];
                // if($val != ''){
                //     $remarks = date('d-m-Y', strtotime($val));
                //     $msg = 'Eid UL Adha Replace Holiday('.$remarks.')';
                //     DB::table('holiday_roaster')
                //     ->updateOrInsert([
                //         'as_id' => $getEmployee[$key]->associate_id,
                //         'date'  => $date
                //     ],
                //     [
                //         'year' => '2021',
                //         'month' => '09',
                //         'remarks' => 'Holiday',
                //         'comment' => $msg,
                //         'status' => 1
                //     ]);
                // }else{
                //     DB::table('holiday_roaster')
                //     ->updateOrInsert([
                //         'as_id' => $getEmployee[$key]->associate_id,
                //         'date'  => $date
                //     ],
                //     [
                //         'year' => '2021',
                //         'month' => '09',
                //         'remarks' => 'Holiday',
                //         'comment' => null,
                //         'status' => 1
                //     ]);
                // }
                
            }
            
        }
        return $d;
    }
    public function absentListBeforeHoliday()
    {
        $getEmployee = DB::table('hr_as_basic_info')
        ->whereIn('as_unit_id', [2])
        ->where('as_location', 7)
        // ->where('as_department_id', '!=', 67)
        // ->where('as_subsection_id', '!=', 108)
        ->where('as_status', 1)
        ->get()
        ->keyBy('associate_id');

        $getData = [];
        $d = [];
        foreach($getData as $key=> $value){
            if(isset($getEmployee[$key])){
                $date = date('Y-m-d', strtotime('-1 days',strtotime($value['Leave Date'])));
                
                $a = DB::table('hr_absent')
                    ->where('associate_id', $getEmployee[$key]->associate_id)
                    ->where('date', $date)
                    ->first();
                if($a != null){
                    $d[] = [
                        'Name' => $getEmployee[$key]->as_name,
                        'Associate id' => $getEmployee[$key]->associate_id,
                        'Date' => $date
                        ];
                }
               
                
            }
            
        }
        return (new FastExcel(collect($d)))->download('absent.xlsx');
        return $d;
    }
    
    public function designationGradeUpdate()
    {
        
        $getData = [];
        $getDesignation = DB::table('hr_designation')
        ->whereNotIn('hr_designation_name', $getData)
        ->update(['hr_designation_status'=> 0]);
        // ->get();
        dd($getDesignation);
        
        $getData = [];
        $r = [];
        $m = [];
        foreach($getData as $d){
            $getDesignation = DB::table("hr_designation")
            ->where('hr_designation_name', $d['CORRECTED DESIGNATION'])
            ->update(['hr_designation_grade'=>$d['Grade']]);
            // ->first();
            // if($getDesignation != null){
            //     $r[] = $d;
            // }else{
            //     $m[] = $d;
            // }
        }
        return $m;
    }
    
    public function employeeDesignationUpdate()
    {
        
        $getEmployee = DB::table('hr_as_basic_info AS b')
        ->whereIn('b.associate_id', [])
        ->pluck('as_id', 'associate_id');
        // return $getEmployee;
        $getDesignation = [];
        $d = [];
        foreach($getEmployee as $key => $emp){
            $designation = DB::table('hr_designation')
            ->where('hr_designation_name', $getDesignation[$key]['designation'])
            ->pluck('hr_designation_id')
            ->first()??'';
            $d[] = $designation;
            if($designation != ''){
                DB::table('hr_as_basic_info')->where('as_id', $emp)->update(['as_designation_id'=>$designation]);
            }
        }
        return $d;
        
        $getDesignationD = [];
        
        $getEmployee = DB::table('hr_as_basic_info AS b')
        ->select('b.as_id', 'b.associate_id', 'b.as_designation_id', 'd.hr_designation_name')
        ->join('hr_designation AS d', 'b.as_designation_id', 'd.hr_designation_id')
        ->where('d.hr_designation_status', 0)
        ->get();
        //->limit(10)->get();
        //return ($getEmployee);
        $m = [];
        foreach($getEmployee as $emp){
            if(isset($getDesignationD[$emp->hr_designation_name])){
                $designation = DB::table('hr_designation')
                ->where('hr_designation_name', $getDesignationD[$emp->hr_designation_name]['new_des'])
                ->pluck('hr_designation_id')
                ->first()??'';
                if($designation != ''){
                    DB::table('hr_as_basic_info')->where('as_id', $emp->as_id)->update(['as_designation_id'=>$designation]);
                }

            }else{
                $m[] = $emp->as_id;
            }
        }
        return count($m);
        $getData = [];
        $getEmployee = DB::table('hr_as_basic_info')
        ->select('as_id', 'associate_id', 'as_designation_id')
        ->get()
        ->keyBy('associate_id');
        $m =[];
        $dd = [];
        foreach($getData as $d){
            $designation = DB::table('hr_designation')
            ->where('hr_designation_name', $d['CORRECTED DESIGNATION'])
            ->pluck('hr_designation_id')
            ->first()??'';
            if($designation != ''){
                //$dd[] = $designation;
                DB::table('hr_as_basic_info')
                ->where('associate_id', $d['Associate ID'])
                ->update(['as_designation_id'=>$designation]);
            }else{
                $m[] = $d['CORRECTED DESIGNATION'];
            }
        }
        return $m;
    }
    
    public function empAbsentStart()
    {
        $getData = [];
        
        $getAtt = DB::table('hr_attendance_aql AS m')
        ->select('m.*', 'b.associate_id', 'b.as_department_id', 'b.as_section_id', 'b.as_subsection_id', 'b.as_oracle_code', 'b.as_name', 'b.as_designation_id', DB::raw('max(m.in_date) AS max_date'))
        ->join('hr_as_basic_info AS b', 'm.as_id', 'b.as_id')
        ->whereIn('b.associate_id', $getData)
        ->where('m.in_date', '>', '2021-07-31')
        ->orderBy(DB::raw('max(m.in_date)'), 'desc')
        ->groupBy('m.as_id')
        ->get();

        $designation = designation_by_id();
        $department = department_by_id();
        $location = location_by_id();
        $section = section_by_id();
        $subSection = subSection_by_id();

        $d = [];
        $i = 0;
        foreach ($getAtt as $emp) {
            $d[] = array(
                'Sl' => ++$i,
                'Oracle ID' => $emp->as_oracle_code,
                'Associate ID' => $emp->associate_id,
                'Name' => $emp->as_name,
                'Designation' => $designation[$emp->as_designation_id]['hr_designation_name']??'',
                'Department' => $department[$emp->as_department_id]['hr_department_name']??'',
                'Section' => $section[$emp->as_section_id]['hr_section_name'],
                'Sub section' => $subSection[$emp->as_subsection_id]['hr_subsec_name'],
                'Last Present Date' => $emp->max_date
            );
        }
        return (new FastExcel(collect($d)))->download('last present list.xlsx');

    }
    
    public function desUpdateEmp(){
        $getValue = [];
        
        foreach($getValue as $key => $value){
            $designation = DB::table('hr_designation')
                ->where('hr_designation_name', $value['designation'])
                ->pluck('hr_designation_id')
                ->first()??'';
                if($designation != ''){
                    DB::table('hr_as_basic_info')->where('associate_id', $key)->update(['as_designation_id'=>$designation]);
                }
        }
        
        return 'success';
    }
    
    public function benefitCheck($value='')
    {
        $get = DB::table('hr_benefits as ben')
        ->join('hr_as_basic_info as b', 'ben.ben_as_id', 'b.associate_id')
        ->where('b.as_ot', 1)
        ->where('b.as_doj', '>', '2021-07-31')
        //->whereIn('b.as_unit_id', [1,4,5])
        ->where('ben.bank_name', 'dbbl')
        // ->update([
        //         'bank_no' => '',
        //         'bank_name' => ''
        //     ]);
        ->get();
        return $get;
    }
    
    public function attRemove($value='')
    {
        $get = DB::table('hr_attendance_ceil AS m')
        ->join('hr_as_basic_info AS b', 'm.as_id', 'b.as_id')
        ->where('b.as_subsection_id', '!=', 108)
        ->whereIn('m.in_date', ['2021-08-06', '2021-08-13', '2021-08-20', '2021-08-27'])
        ->delete();
        return $get;
    }
    
    public function getEmployee(Request $request)
    {
        
        try {
            $query = Employee::select('as_id', 'as_name', 'as_unit_id', 'as_location', 'as_ot')->where('as_status',1);

            if(count($request->all()) > 0){
                foreach ($request->all() as $key => $value) {
                    $values = explode(',', $value);
                    $query->whereIn($key, $values);
                }
            }
            $getEmployee = $query->get();
            return response()->json([
                    'success' => true,
                    'data' => $getEmployee
                ], 200);
            
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return response()->json([
                'success' => false,
                'message' => 'Sorry, '.$bug
            ], 400);
        }
    }
    
    public function getExtraGrossList(){
        $getData = DB::table('hr_salary_adjust_master AS m')
        ->select('m.associate_id', 'b.as_status', 'm.month', 'm.year', 'd.date', 'd.amount', 'b.as_unit_id')
        ->join('hr_as_basic_info AS b', 'm.associate_id', 'b.associate_id')
        ->join('hr_salary_adjust_details AS d', 'm.id', 'd.salary_adjust_master_id')
        ->where('m.month', '11')
        ->where('m.year', 2021)
        ->where('b.as_status', 1)
        ->where('d.type', 2)
        ->whereIn('b.as_unit_id', [1,4,5])
        ->get();
        $d = [];
        foreach ($getData as $data) {
            
            $d[] = array(
                'Associate Id' => $data->associate_id,
                'Date' => $data->date,
                'amount' => $data->amount,
                'Status' => $data->as_status,
            );
        }
        return (new FastExcel(collect($d)))->download('extra payment.xlsx');

        return $getData;
    }
    
    public function getExtraGrossAttCheck(){
        $getData = DB::table('hr_salary_adjust_master AS m')
        ->select('m.associate_id', 'b.as_status', 'm.month', 'm.year', 'd.date', 'd.amount', 'b.as_unit_id', 'b.as_id')
        ->join('hr_as_basic_info AS b', 'm.associate_id', 'b.associate_id')
        ->join('hr_salary_adjust_details AS d', 'm.id', 'd.salary_adjust_master_id')
        ->where('m.month', '10')
        ->where('m.year', '2021')
        ->where('d.type', 2)
        ->whereIn('b.as_unit_id', [1,4,5])
        ->get();
        $d = [];
        foreach ($getData as $data) {
            
            // $d[] = array(
            //     'As Id' => $data->as_id,
            //     'Associate Id' => $data->associate_id,
            //     'Date' => $data->date,
            //     'amount' => $data->amount,
            //     'Status' => $data->as_status,
            // );
            $a = DB::table('hr_attendance_mbm')
            ->select('id')
            ->where('as_id', $data->as_id)
            ->where('in_date', $data->date)
            ->first();
            if($a == null) {
                // $d[] = array(
                //     'As Id' => $data->as_id,
                //     'Associate Id' => $data->associate_id,
                //     'Date' => $data->date,
                //     'amount' => $data->amount,
                //     'Status' => $data->as_status,
                // );
                $d[] = $data;
            }
        }
        return $d;
        return (new FastExcel(collect($d)))->download('extra payment issue.xlsx');

        return $getData;
    }
    
    public function extraGrossAmount($value='')
    {
        $getEmployee = DB::table('hr_as_basic_info')
        ->join('hr_benefits', 'hr_as_basic_info.associate_id', 'hr_benefits.ben_as_id')
        ->whereIn('hr_as_basic_info.associate_id', [])
        ->get();
        
        foreach($getEmployee as $emp){
            $oneGross = $emp->ben_current_salary/31;
            $gete = DB::table('hr_salary_adjust_details')
            ->join('hr_salary_adjust_master', 'hr_salary_adjust_details.salary_adjust_master_id', 'hr_salary_adjust_master.id')
            ->where('hr_salary_adjust_master.associate_id', $emp->associate_id)
            ->where('hr_salary_adjust_master.month', '08')
            ->where('hr_salary_adjust_master.year', 2021)
            ->update([
                'amount' => number_format((float)$oneGross, 2, '.', '')
            ]);

            $queue = (new ProcessUnitWiseSalary('hr_attendance_mbm', '08', 2021, $emp->as_id, 31))
                ->onQueue('salarygenerate')
                ->delay(Carbon::now()->addSeconds(2));
                dispatch($queue);

        }

        return 'success';
    }
    
    public function specialOTCheck()
    {
        $getAtt = DB::table('hr_att_special')
        ->select('hr_as_basic_info.associate_id')
        ->join('hr_as_basic_info', 'hr_att_special.as_id', 'hr_as_basic_info.as_id')
        ->where('hr_as_basic_info.as_department_id', '!=', 67)
        ->where('hr_as_basic_info.as_subsection_id', '!=', 108)
        ->where('hr_att_special.in_date', '>=', '2021-08-01')
        ->where('hr_att_special.in_date', '<=', '2021-08-31')
        ->whereIn('hr_as_basic_info.as_unit_id', [1,4,5])
        ->pluck('associate_id');

        return $getAtt;
    }
    public function specialOTCheckAbsent()
    {
        $getAtt = DB::table('hr_att_special')
        ->select('hr_att_special.*','hr_as_basic_info.associate_id','hr_as_basic_info.as_unit_id','hr_as_basic_info.shift_roaster_status')
        ->join('hr_as_basic_info', 'hr_att_special.as_id', 'hr_as_basic_info.as_id')
        //->where('hr_as_basic_info.as_department_id', '!=', 67)
        // ->where('hr_as_basic_info.as_subsection_id', '!=', 108)
        ->whereIn('hr_att_special.in_date', ['2021-10-01', '2021-10-08', '2021-10-15', '2021-10-22', '2021-10-29'])
        ->where('hr_as_basic_info.as_status', 1)
        ->whereIn('hr_as_basic_info.as_unit_id', [1,4,5])
        ->where('hr_as_basic_info.as_ot', 1)
        ->get();
        $d = [];
        foreach ($getAtt as $key => $value) {
            $dayStatus = EmployeeHelper::employeeDateWiseStatus($value->in_date, $value->associate_id, $value->as_unit_id, $value->shift_roaster_status);

            if($dayStatus == 'OT'){
               $getAtt = DB::table('hr_attendance_mbm')
                ->where('as_id', $value->as_id)
                ->where('in_date', $value->in_date)
                ->first();
                
                if($getAtt == null){
                    $d[] = [
                        'Associate' => $value->associate_id,
                        'date' =>$value->in_date
                    ];
                } 
            }

            
        }
        return (new FastExcel(collect($d)))->download('full ot.xlsx');
        return $d;
    }
    
    public function billExtra($value='')
    {
        $getSalary = DB::table('hr_monthly_salary')
        ->select('hr_as_basic_info.as_id')
        ->join('hr_as_basic_info', 'hr_monthly_salary.as_id', 'hr_as_basic_info.associate_id')
        ->whereIn('hr_monthly_salary.unit_id', [1,4,5])
        ->where('hr_monthly_salary.month', '08')
        ->where('hr_monthly_salary.year', 2021)
        ->where('hr_monthly_salary.emp_status', 1)
        ->pluck('as_id');

        // return $getSalary;
        $get = DB::table('hr_bill AS b')
        //->whereNotIn('b.as_id', $getSalary)
        ->whereBetween('b.bill_date', ['2021-08-01', '2021-08-31'])
        ->where('b.bill_type', 1)
        ->get()->chunk(100);
        return ($get);
        foreach($get as $bill){
            DB::table('hr_bill')->whereIn('id', $bill)->delete();
        }
        return 'success';
    }
    
    public function buyerSalaryProcess($value='')
    {
        $buyerId = 4; //3 = ceil2, 4 = ceil4
        
        $insert = [
            16829
        ];
        $date = '2021-10-01';

        $buyer = DB::table('hr_buyer_template')->where('id', $buyerId)->first();

        $queue = (new ProcessBuyerSalary($buyer, date('m', strtotime($date)), date('Y', strtotime($date)), ($insert)))
        ->onQueue('buyersalary')
        ->delay(Carbon::now()->addSeconds(2));
        dispatch($queue);
    }
    public function otIncreNonOtEmp($value='')
    {
        $getEmployee = DB::table('hr_monthly_salary')
        ->select('as_id', 'month', 'ot_status', DB::raw("CONCAT_WS('-', year, month) as month_year"))
        ->whereIn('unit_id', [1,4,5])
        ->get()
        ->groupBy('as_id', true);
        $excel = [];
        // dd($getEmployee);
        $data = [];
        foreach ($getEmployee as $key => $value) {
            $otStatus = collect($value)->pluck('ot_status', 'month_year')->toArray();
            // dd(($otStatus));
            if(count(array_unique($otStatus)) > 1) {
                // dd(array_unique($otStatus));
                $data[$key] = $value;
                foreach (array_unique($otStatus) as $k => $v) {
                    if($v == 0){
                        $excel[] = [
                            'associate' => $key,
                            'year month' => date('Y-m', strtotime($k))
                        ];
                    }
                }
            }
        }
        // dd($excel);
        return (new FastExcel(collect($excel)))->download('ot-to-nonot.xlsx');
    }
    
    public function getEmployeeListWithService(){
        $request = (object)[];
        $request->emp_type =3;
        $request->as_status =1;
        $data = DB::table('hr_as_basic_info AS b')
            ->select([
                DB::raw('b.as_id AS serial_no'),
                'b.associate_id',
                'b.as_name',
                'e.hr_emp_type_name AS hr_emp_type_name',
                'u.hr_unit_short_name',
                'f.hr_floor_name',
                'l.hr_line_name',
                'lc.hr_location_name',
                'dp.hr_department_name',
                'dg.hr_designation_name',
                'dg.hr_designation_position',
                'dg.hr_designation_grade',
                'b.as_gender',
                'b.as_ot',
                'b.as_doj',
                'b.as_dob',
                'b.as_status',
                'b.as_oracle_code',
                'b.as_rfid_code',
                'b.as_contact',
                'sec.hr_section_name',
                'subsec.hr_subsec_name',
                'b.as_shift_id',
                'ben.ben_current_salary',
                'adv.emp_adv_info_per_vill',
                'adv.emp_adv_info_per_po',
                'adv.emp_adv_info_pres_house_no',
                'adv.emp_adv_info_pres_road',
                'adv.emp_adv_info_pres_po',
                'adv.emp_adv_info_per_dist',
                'adv.emp_adv_info_per_upz',
                'adv.emp_adv_info_spouse',
                'adv.emp_adv_info_nid',
                'adv.emp_adv_info_fathers_name',
                'adv.emp_adv_info_mothers_name',
                'adv.emp_adv_info_religion'
            ])
            ->leftJoin('hr_area AS a', 'a.hr_area_id', '=', 'b.as_area_id')
            ->leftJoin('hr_emp_type AS e', 'e.emp_type_id', '=', 'b.as_emp_type_id')
            ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'b.as_unit_id')
            ->leftJoin('hr_floor AS f', 'f.hr_floor_id', '=', 'b.as_floor_id')
            ->leftJoin('hr_line AS l', 'l.hr_line_id', '=', 'b.as_line_id')
            ->leftJoin('hr_location AS lc', 'lc.hr_location_id', '=', 'b.as_location')
            ->leftJoin('hr_department AS dp', 'dp.hr_department_id', '=', 'b.as_department_id')
            ->leftJoin('hr_designation AS dg', 'dg.hr_designation_id', '=', 'b.as_designation_id')
            ->leftJoin('hr_section AS sec', 'sec.hr_section_id', '=', 'b.as_section_id')
            ->leftJoin('hr_subsection AS subsec', 'subsec.hr_subsec_id', '=', 'b.as_subsection_id')
            ->leftJoin('hr_benefits AS ben', 'b.associate_id', '=', 'ben.ben_as_id')
            ->leftJoin('hr_as_adv_info AS adv', 'b.associate_id', '=', 'adv.emp_adv_info_as_id')
            ->whereIn('b.as_location', auth()->user()->location_permissions())
            ->where('b.as_status', '=', 1)
            ->where('b.as_emp_type_id', '=', 3)
            ->whereNotIn('as_id', auth()->user()->management_permissions())
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->orderBy('ben.ben_current_salary','DESC')
            ->get();
            
            foreach($data as $d){
                $excel[] = [
                    'Associate ID' => $d->associate_id,
                    'Oracle ID' => $d->as_oracle_code,
                    'status' => '',
                    'Name' => $d->as_name,
                    'Unit' => $d->hr_unit_short_name,
                    'Location' => $d->hr_location_name,
                    'Salary' => $d->ben_current_salary,
                    'Department' => $d->hr_department_name,
                    'Section' => $d->hr_section_name,
                    'Subsection' => $d->hr_subsec_name,
                    'Designation' => $d->hr_designation_name,
                    'DOJ' => $d->as_doj,
                    'Service Length' => Carbon::createFromFormat('Y-m-d', $d->as_doj)->diff(Carbon::now())->format('%y years')
                ];
            }
            
            return (new FastExcel(collect($excel)))->download('Employee List.xlsx');
            
    }
    
    public function lineChangeDefult()
    {
        // $getEmployee = DB::table('hr_as_basic_info')
        // ->whereIn('as_unit_id', [1,4,5])
        // //->where('as_location', 7)
        // // ->where('as_department_id', '!=', 67)
        // // ->where('as_subsection_id', '!=', 108)
        // ->where('as_status', 1)
        // ->get()
        // ->keyBy('associate_id');

        $getData = [];
        $d = [];
        $e=[];
        foreach($getData as $key=> $value){
            
            $query = DB::table('hr_as_basic_info')
            ->whereIn('as_unit_id', [1,4,5]);
            if(strlen($key) > 4){
                $query->where('associate_id', 'LIKE', '%'.$key.'%');
            }else{
                $query->where('as_oracle_code', 'LIKE', '%'.$key.'%');
            }
            $emp = $query->first();
            
            if($emp != null){
                
                // check line and floor
                
                $line = DB::table('hr_line')
                ->where('hr_line_unit_id', $emp->as_unit_id)
                ->where('hr_line_name', $value['Line'])
                ->first();
                if($line == null){
                    $d[] = $key;
                }
                else{
                    DB::table('hr_as_basic_info')
                    ->where('as_id', $emp->as_id)
                    ->update([
                        'as_line_id' => $line->hr_line_id,
                        'as_floor_id' => $line->hr_line_floor_id
                    ]);
                    
                    DB::table('hr_station')
                    ->where('associate_id', $emp->associate_id)
                    ->whereNull('end_date')
                    ->update(['end_date' => '2021-10-03 00:00:00']);
                    
                    DB::table('hr_attendance_mbm')
                    ->where('as_id', $emp->as_id)
                    ->where('in_date','>', '2021-10-03')
                    ->update(['line_id'=>$line->hr_line_id]);
                }
            }else{
                $e[] = $key;
            }
            
        }
        return $d;
    }
    
    public function afterLeftSalaryCheck($value='')
    {
        $yearMonth = '2021-11';
        $yearMonthExp = explode('-', $yearMonth);
        $year = $yearMonthExp[0];
        $month = $yearMonthExp[1];

        $currentYear = '2021';
        $currentMonth = '11';

        $getEmployee = DB::table('hr_as_basic_info')
        ->where(DB::raw('DATE_FORMAT(as_status_date, "%Y-%m")'), $yearMonth)
        ->whereIn('as_status', [2,5])
        ->pluck('as_status', 'associate_id');
        
        // status change
        
        // $getEmployee = DB::table('hr_as_basic_info')
        // ->where('as_status_date','<=', '2021-11-01')
        // ->whereIn('as_status', [2,5])
        // //->whereIn('associate_id', [])
        // ->pluck('associate_id');
        //return $getEmployee;
        // if(count($getEmployee) > 0){
        //     $d = DB::table('hr_monthly_salary')
        //         ->whereIn('as_id', $getEmployee)
        //         ->where('month', '11')
        //         ->where('year', 2021)
        //         //->where('unit_id', 3)
        //         //->where('emp_status', '!=', 1)
        //         ->pluck('as_id');
        //         //->delete();
        //     //return $d;
        // }
        //return 'hi';
        // before end still list
        // $getSalary = DB::table('hr_monthly_salary')
        // ->select('id', 'as_id', 'emp_status', 'month', 'year', 'unit_id')
        // ->where('year', $currentYear)
        // ->where('month', '10')
        // ->whereNotIn('emp_status', [1,6])
        // ->pluck('as_id');
        // if(count($getSalary) > 0){
        //     $getSalary = DB::table('hr_monthly_salary')
        //     ->select('id', 'as_id', 'emp_status', 'month', 'year', 'unit_id')
        //     ->where('year', $currentYear)
        //     ->where('month', $currentMonth)
        //     ->whereIn('emp_status', [2,5])
        //     ->whereIn('as_id', $getSalary)
        //     ->get();
            
        //     return $getSalary;
        // }
        // return 'hi';
        
        
        // $getEmployee = DB::table('hr_all_given_benefits')
        // ->select('hr_as_basic_info.associate_id', 'hr_all_given_benefits.benefit_on')
        // ->leftJoin('hr_as_basic_info', 'hr_all_given_benefits.associate_id', 'hr_as_basic_info.associate_id')
        // ->where('hr_all_given_benefits.salary_date', '>', '2021-09-31')
        // ->where('hr_as_basic_info.as_status_date', '<', '2021-10-31')
        // ->whereIn('hr_as_basic_info.as_status', [2,5])
        // ->pluck('benefit_on', 'associate_id');
        //return $getEmployee;
        $empIds = array_keys($getEmployee->toArray());
        //return $getEmployee;
        $getSalary = DB::table('hr_monthly_salary')
        ->select('id', 'as_id', 'emp_status', 'month', 'year', 'unit_id')
        ->where('year', $currentYear)
        ->where('month', $currentMonth)
        ->whereIn('emp_status', [1])
        ->whereIn('as_id', $empIds)
        ->get();
        return $getSalary;
        $d = [];
        foreach($getSalary as $salary){
            $empStatus = $getEmployee[$salary->as_id]??'';

            if($empStatus != ''){
                //$d[] = $salary->as_id;
                DB::table('hr_monthly_salary')
                ->where('id', $salary->id)
                ->update([
                    'emp_status' => $empStatus
                ]);
            }else{
                $d[] = $salary;
            }
            
        }
        return $d;


    }
    public function exlkd($value='')
    {
        $data = [];


         $getEmployee = DB::table('hr_as_basic_info')
        ->select('associate_id', 'as_id')
        ->whereIn('associate_id', array_keys($data))
        //->where('as_status', 1)
        ->get()
        ->keyBy('associate_id')
        ->toArray();
        
        
        $d = [];
        foreach ($data as $key => $value) {
            $emp = $getEmployee[$key]??'';
            //print_r( $value['Date']);exit;
            if($emp != ''){
                $att = DB::table('hr_attendance_mbm')
                ->where('as_id', $emp->as_id)
                ->where('in_date', $value['Date'])
                ->first();
                // $att = DB::table('hr_leave')
                // ->where('leave_ass_id', $key)
                // ->where('leave_from','>=', $value['Date'])
                // ->where('leave_to','<=', $value['Date'])
                // ->first();
                //print_r($att);exit;
                if($att == null){
                    
                    $d[] = $emp->associate_id.'-'.$value['Date'];
                }
                //return $d;
            }else{
                //$d[] = $key;
            }
            
        }
        return $d;

    }
    
    public function designationGradeOnUpdate($value='')
    {
        
        $getData = [];
        
        //$getDesignation = designation_by_id();
        $d = [];
        foreach($getData as $key=>$desg){
            
            $designation = DB::table('hr_designation')
            ->where('hr_designation_id', $key)
            ->first();
            if($designation != null){
                DB::table('hr_designation')
                ->where('hr_designation_id', $key)
                ->update(['grade_id' => $desg['hr_grade_id']]);
            }else{
                $d[] = $key;
            }
        }
        
        return $d;
    } 
    
    public function bonusArea()
    {
        $month = '11';
        $year = '2021';
        // $data = array(
        //     '16F075016L' => array('amount' => '11424'),
        // );
        $ruleId = 7;
        $associateId = [
                '17D065044K',
                '09G000097A',
                '16G101393N',
                '18G101413N',
                '17G101425N',
                '16G101433N',
                '16G101450N',
                '12G000381A',
                '19G000525A'
            ];
        $data = DB::table('hr_bonus_sheet')
            ->select('associate_id', 'net_payable as amount')
            ->where('bonus_rule_id', $ruleId)
            ->whereIn('associate_id', $associateId)
            ->get()
            ->keyBy('associate_id')
            ->toArray();

        $getEmpId = array_keys($data);
        // dd($getEmpId);
        
        // get benefit data
        $getBenefit = DB::table('hr_benefits')
        ->whereIn('ben_as_id', $getEmpId)
        ->select('ben_basic', 'ben_as_id')
        ->get()
        ->keyBy('ben_as_id');

        // store data bonus area
        $insert = [];
        $e = [];
        foreach($data as $key=>$a){
            $currentBasic = $getBenefit[$key]->ben_basic??0;
            $remaining = ((int)$currentBasic - ((int)$a->amount + 10));

            if($remaining > 0){
                $master = SalaryAdjustMaster::firstOrNew([
                    'associate_id' => $key,
                    'month' => $month,
                    'year' => $year
                ]);
                $master->save();
                $insert[] = [
                    'salary_adjust_master_id' => $master->id,
                    'date' => '2021-07-01',
                    'type' => 4,
                    'amount' => number_format((float)$remaining, 2, '.', ''),
                    'comment' => 'Bonus Area'
                ];
            }else{
                $e[$key] = $currentBasic.' '.$a->amount;
            }
        }
        
        //return ($insert);
        if(count($insert) > 0){
            $chunk = collect($insert)->chunk(10);
            foreach ($chunk as $key => $n) {        
                DB::table('hr_salary_adjust_details')->insertOrIgnore(collect($n)->toArray());
            }
        }
        return $e;
        return "success";
        
    }
    
    public function joinDateAbsent()
    {
        $startDate = '2021-10-01';
        $endDate = '2021-10-31';
        $getEmployee = DB::table('hr_as_basic_info')
        //->where(DB::raw('DATE_FORMAT(as_status_date, "%Y-%m")'), $startDate)
        ->whereBetween('as_doj', [$startDate, $endDate])
        ->pluck('as_doj', 'associate_id');
        $d = [];
        
        foreach($getEmployee as $key => $date){
            $a = DB::table('hr_absent')
            ->where('associate_id', $key)
            ->where('date', $date)
            ->first();
            if($a != null){
                $d[] = array(
                    'Associate ID' => $a->associate_id,
                    'Date' => $a->date,
                    'Unit' => $a->hr_unit
                );
            }
        }

        return (new FastExcel(collect($d)))->download('Joining date Absent.xlsx');
        return $d;
    }
    
    public function salaryBankCashStatus($value='')
    {
        $data = DB::table('hr_monthly_salary')
        ->where('month', '09')
        ->where('year', '2021')
        ->where('pay_status', 2)
        ->where('gross', '<', 35000)
        ->get();

        $current = DB::table('hr_monthly_salary')
        ->select('as_id', 'unit_id', 'location_id', 'sub_section_id', 'designation_id')
        ->where('month', '10')
        ->where('year', '2021')
        ->where('pay_status', 3)
        ->where('gross', '<', 35000)
        ->get()
        ->keyBy('as_id')
        ->toArray();

        $department = department_by_id();
        $designation = designation_by_id();
        $location = location_by_id();
        $section = section_by_id();
        $subsection = subSection_by_id();
        $unit = unit_by_id();

        $ge = array();
        foreach ($data as $value) {
            if(isset($current[$value->as_id])){
                $s = $current[$value->as_id];
                $getBenefit = DB::table('hr_benefits')
                ->select('ben_current_salary','ben_cash_amount', 'ben_bank_amount')
                ->where('ben_as_id', $value->as_id)
                ->where('ben_cash_amount','>', 0)
                ->first();
                if($getBenefit != null){
                    $subSection = $subsection[$s->sub_section_id];
                    $ge[] = array(
                        'Associate ID' => $s->as_id,
                        'Gross' => $getBenefit->ben_current_salary,
                        'Bank' => $getBenefit->ben_bank_amount,
                        'Cash' => $getBenefit->ben_cash_amount,
                        'Unit' => $unit[$s->unit_id]['hr_unit_name'],
                        'Location' => $location[$s->location_id]['hr_location_name'],
                        'Designation' => $designation[$s->designation_id]['hr_designation_name'],
                        'Department' => $department[$subSection['hr_subsec_department_id']]['hr_department_name'],
                        'Section' => $section[$subSection['hr_subsec_section_id']]['hr_section_name'],
                        'Sub Section' => $subSection['hr_subsec_name']
                    );
                }
            }
        }
        return (new FastExcel(collect($ge)))->download('Partial Salary.xlsx');
    }
    
    public function bloodGroupUpdate($value='')
    {
        $getData = [];
        
        foreach($getData as $k => $d){
            DB::table('hr_med_info')
            ->where('med_as_id', $k)
            ->update(['med_blood_group'=>$d['Blood Group']]);
        }
        
        return "success";
            
    }
    public function salaryAbsentIssue($value='')
    {
        $startDate = '2021-11-01';
        $endDate = date('Y-m-t', strtotime($startDate));
        $year = date('Y', strtotime($startDate));
        $month = date('m', strtotime($startDate));
        $getSalary = DB::table('hr_monthly_salary AS s')
        ->select('s.as_id', 's.year', 's.month', 'b.as_doj', 'b.as_status_date', 's.present', 's.absent', 's.leave', 's.holiday')
        ->join('hr_as_basic_info AS b', 's.as_id', 'b.associate_id')
        ->where('s.year', $year)
        ->where('s.month', $month)
        ->where('s.absent', '>', 0)
        ->where('s.emp_status', 1)
        //->whereIn('s.unit_id', [1,4,5])
        //->where('b.as_doj', '>=', '2021-09-01')
        ->get()
        ->keyBy('as_id');

        $getAbsent = DB::table('hr_absent')
        ->whereBetween('date', [$startDate, $endDate])
        ->get()
        ->groupBy('associate_id');

        $absenta = [];
        foreach($getSalary as $key=> $salary){
            if(!isset($getAbsent[$key])){
                $absenta[] = $key;
            }
        }
        return $absenta;
    }
    public function absentProcess($value='')
    {
        
        $getEmp = DB::table('hr_as_basic_info')
            ->select('as_id')
            ->where('as_status', 1)
            ->whereIn('as_unit_id', [1,4,5])
            ->pluck('as_id')
            ->toArray();  
        $dates = ['2021-11-19'];
        $tableName = 'hr_attendance_mbm';
        $getYearMonth = [];
        foreach($dates as $date){
            $year = Carbon::parse($date)->format('Y');
            $month = Carbon::parse($date)->format('m');
            $getYearMonth[] = $year.'-'.$month;
            $getData = DB::table($tableName)
            ->select('as_id')
            ->where('in_date', $date)
            ->pluck('as_id')
            ->toArray(); 
            $arrayDiff = array_diff($getEmp, $getData);
            foreach ($arrayDiff as $key => $value) {
                $getEmployee = Employee::where('as_id', $value)->first();
                if($getEmployee != null){
                    $flag = 0;
                    $eligible = 1;
                    $shiftFlag = 0;

                    // check rejoin date for maternity/left employee
                    if($getEmployee->as_status_date != null){
                        $sDate = $getEmployee->as_status_date;
                        $sYear = Carbon::parse($sDate)->format('Y');
                        $sMonth = Carbon::parse($sDate)->format('m');

                        if($sYear == $year && $month == $sMonth){
                            if($date < $sDate){
                                $eligible = 0;
                            }
                        }
                    }
                    if($eligible  == 1){

                        if($date >= $getEmployee->as_doj && $shiftFlag == 0){
                            $getHoliday = HolidayRoaster::getHolidayYearMonthAsIdDateWiseRemarkMulti($year, $month, $getEmployee->associate_id, $date, ['Holiday', 'OT']);
                            if($getHoliday == null && $getEmployee->shift_roaster_status == 0){
                                $getHoliday = YearlyHolyDay::getCheckUnitDayWiseHolidayStatusMulti($getEmployee->as_unit_id, $date, [0, 2]);
                            }
                            
                            if($getHoliday == null){
                                $getLeave = DB::table('hr_leave')
                                ->where('leave_ass_id', $getEmployee->associate_id)
                                ->where('leave_from', '<=', $date)
                                ->where('leave_to', '>=', $date)
                                ->where('leave_status',1)
                                ->first();
                                //
                                $getAbsent = DB::table('hr_absent')
                                ->where('associate_id', $getEmployee->associate_id)
                                ->where('hr_unit', $getEmployee->as_unit_id)
                                ->where('date', $date)
                                ->first();

                                if($getLeave == '' && $getAbsent == ''){
                                    
                                    DB::table('hr_absent')
                                    ->insertOrIgnore([
                                        'associate_id' => $getEmployee->associate_id,
                                        'hr_unit'  => $getEmployee->as_unit_id,
                                        'date'  => $date
                                    ]);
                                    $yearMonth = $year.'-'.$month; 
                                    if($month == date('m')){
                                        $totalDay = date('d');
                                    }else{
                                        $totalDay = Carbon::parse($yearMonth)->daysInMonth;
                                    }
                                    $queue = (new ProcessUnitWiseSalary($tableName, $month, $year, $getEmployee->as_id, $totalDay))
                                                ->onQueue('salarygenerate')
                                                ->delay(Carbon::now()->addSeconds(2));
                                                dispatch($queue);
                                    
                                }
                            }
                        } 
                    }

                }
            }
        }
    }
    
    public function outsidePresentIssue($value='')
    {
        $startDate = '2021-11-01';
        $endDate = date('Y-m-t', strtotime($startDate));
        $year = date('Y', strtotime($startDate));
        $month = date('m', strtotime($startDate));
        $getOutSide = DB::table('hr_outside')
        ->where('start_date', '>=', $startDate)
        // ->where('end_date', '<=', $endDate)
        ->get();
        // dd($getOutSide);
        foreach($getOutSide as $outside){
            $start_date = $outside->start_date;
            $end_date = $outside->end_date;
            $employee = Employee::where('associate_id',$outside->as_id)->first();
            $table = get_att_table($employee->as_unit_id);

            $totalDays  = (date('d', strtotime($end_date))-date('d', strtotime($start_date)));

            if($employee->shift_roaster_status == 1){
                // check holiday roaster employee
                $getHoliday = HolidayRoaster::where('as_id', $employee->associate_id)
                ->where('date','>=', $start_date)
                ->where('date','<=', $end_date)
                ->where('remarks', 'Holiday')
                ->pluck('date','date')->toArray();
            }else{
                // check holiday roaster employee
                $RosterHolidayCount = HolidayRoaster::where('as_id', $employee->associate_id)
                ->where('date','>=', $start_date)
                ->where('date','<=', $end_date)
                ->where('remarks', 'Holiday')
                ->pluck('date','date')->toArray();
                // check General roaster employee
                $RosterGeneralCount = HolidayRoaster::where('as_id', $employee->associate_id)
                ->where('date','>=', $start_date)
                ->where('date','<=', $end_date)
                ->where('remarks', 'General')
                ->pluck('date','date')->toArray();
                 // check holiday shift employee
                
                $shiftHolidayCount = YearlyHolyDay::
                    where('hr_yhp_unit', $employee->as_unit_id)
                    ->where('hr_yhp_dates_of_holidays','>=', $start_date)
                    ->where('hr_yhp_dates_of_holidays','<=', $end_date)
                    ->where('hr_yhp_open_status', 0)
                    ->pluck('hr_yhp_dates_of_holidays','hr_yhp_dates_of_holidays')->toArray();
                
                
                if(count($RosterHolidayCount) > 0 || count($RosterGeneralCount) > 0){
                    $all = array_merge($RosterHolidayCount,$shiftHolidayCount);

                    $getHoliday = array_diff($all, $RosterGeneralCount);
                }else{
                    $getHoliday = $shiftHolidayCount;
                }
            }

            $attendance = DB::table($table)->where('in_date', '>=', $start_date)
                            ->where('in_date','<=', $end_date)
                            ->where('as_id', $employee->as_id)
                            ->pluck('in_date','in_date')->toArray();

            $leave = DB::table('hr_leave')
                      ->where('leave_ass_id', $employee->associate_id)
                      ->where('leave_status',1)
                      ->whereYear('leave_from',date('Y', strtotime($start_date)))
                      ->get();
            $leave_date = [];
            foreach ($leave as $key => $l) {
                $l_date =  $this->generateDateRange(Carbon::parse($l->leave_from),Carbon::parse($l->leave_to));
                $leave_date = array_merge($leave_date, $l_date);
            }

            for($j=0; $j<=$totalDays; $j++) {

                $date = date('Y-m-d', strtotime("+".$j." day", strtotime($start_date)));

                if(!in_array($date, $getHoliday) && !in_array($date, $attendance) && !in_array($date, $leave_date)){
                    $outtime = date('H:i:s',strtotime($employee->shift['hr_shift_end_time'])+($employee->shift['hr_shift_break_time']*60));

                    $attData = array(
                         'as_id' => $employee->as_id,
                         'in_date' => $date,
                         'hr_shift_code' => $employee->shift['hr_shift_code'],
                         'ot_hour' => 0,
                         'late_status' => 0,
                         'remarks'=>'BM',
                         'updated_by' => auth()->user()->associate_id,
                         'updated_at' => NOW()
                    );

                    $attData['in_time'] = $date.' '.$employee->shift['hr_shift_start_time'];
                    $attData['out_time'] = $date.' '.$outtime;
                    
                    $lastPunchId = DB::table($table)
                                     ->insertGetId($attData);

                    $queue = (new ProcessAttendanceOuttime($table, $lastPunchId, $employee->as_unit_id))
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);
                }

            }
            
        }
        
    }
    
    private function generateDateRange(Carbon $start_date, Carbon $end_date)
    {
        $dates = [];

        for($date = $start_date->copy(); $date->lte($end_date); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }
    
} 