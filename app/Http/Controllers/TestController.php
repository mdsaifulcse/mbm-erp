<?php

namespace App\Http\Controllers;
use App\Helpers\EmployeeHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Hr\IDGenerator as IDGenerator;
use App\Jobs\ProcessAttendanceInOutTime;
use App\Jobs\ProcessAttendanceIntime;
use App\Jobs\ProcessAttendanceOuttime;
use App\Jobs\ProcessBuyerSalary;
use App\Jobs\ProcessUnitWiseSalary;
use App\Mail\TestMail;
use App\Models\Employee;
use App\Models\Hr\Absent;
use App\Models\Hr\AdvanceInfo;
use App\Models\Hr\Attendace;
use App\Models\Hr\AttendaceManual;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\HrLateCount;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Leave;
use App\Models\Hr\MedicalInfo;
use App\Models\Hr\SalaryAddDeduct;
use App\Models\Hr\SalaryAdjustDetails;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\Shift;
use App\Models\Hr\Unit;
use App\Models\Hr\YearlyHolyDay;
use App\Repository\Hr\PartialSalaryRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use PDF, Validator, Auth, ACL, DB, DataTables, Cache;
use Rap2hpoutre\FastExcel\FastExcel;


class TestController extends Controller
{
    public $timeout = 500;
    public $buyer;
    public $month;
    public $year;
    public $asId;
    public $attTable;
    public $salaryTable;

    protected $partialSalaryRepository;



    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(PartialSalaryRepository $partialSalaryRepository)
    {
        $this->partialSalaryRepository = $partialSalaryRepository;
    }


    public function test()
    {
        

        //return $this->processLeftSalary();
        return '';
        
    }
    
    public function activeMaternity()
    {
        //return '';
        // $firstDate = date('Y-m').'-01';
        $firstDate = '2021-11-01';
        $today = date('Y-m-d');
        
        $leaveEnd = DB::table('hr_maternity_leave')
            ->where('leave_to','>=',$firstDate)
            ->where('leave_to','<=',$today)
            ->get();
        
        foreach($leaveEnd as $k => $lv){
            DB::table('hr_as_basic_info')
             ->where('associate_id', $lv->associate_id)
             ->where('as_status', 6)
             ->update([
                 'as_status' => 1,
                 'as_status_date' => \Carbon\Carbon::parse($lv->leave_to)->addDay()->toDateString()
            ]);
        }
        return $leaveEnd;
    }
    
    public function processLeftSalary()
    {
        $data = DB::table('hr_attendance_aql as a')
        ->select('b.associate_id', DB::raw('max(in_date) as in_date'))
        ->leftJoin('hr_as_basic_info as b','b.as_id','a.as_id')
        ->where('in_date', '>=', '2021-11-01')
        ->where('in_date', '<=', '2021-11-31')
        ->whereIn('b.as_status',[2,5])
        ->where('b.as_status_date', '<=', '2021-11-31')
        //->whereIn('b.associate_id',['21H115043N'])
        ->groupBy('a.as_id')
        ->pluck('in_date','associate_id');
        
        //return $data;
        //$data = ['20F025016D' =>  '2021-08-31'];
        
        $leave = DB::table('hr_leave')
         ->select('leave_ass_id', DB::raw('max(leave_to) as in_date'))
         ->where('leave_to','>=', '2021-11-01')
         ->whereIn('leave_ass_id', collect($data)->keys()->toArray())
         ->groupBy('leave_ass_id')
         ->pluck('in_date', 'leave_ass_id');
         
        
        $datas = Employee::whereIn('associate_id', collect($data)->keys()->toArray())
            ->leftJoin('hr_benefits as ben', 'hr_as_basic_info.associate_id','ben.ben_as_id')
            ->get();
            
            
        $ids = collect($datas)->pluck('associate_id');

        $ben = DB::table('hr_all_given_benefits')
                ->whereIn('associate_id',$ids)
                ->groupBy('associate_id')
                ->get()
                ->keyBy('associate_id');
        $h = [];
        foreach ($datas as $key => $v) {
            $dt = $data[$v->associate_id];
            if(isset($leave[$v->associate_id])){
                if($leave[$v->associate_id] >  $dt ){
                     $dt = $leave[$v->associate_id];
                }
            }
            // check holiday
            $dt = $this->isHoliday($v->associate_id, $dt);
            
            if($v->shift_roaster_status == 0){
                $dt = $this->isGlobalHoliday($v->as_unit_id, $dt);
            }
            
            //return $dt;
            $sp = clone $this->partialSalaryRepository;
            $pq = $sp->process($v, $dt ,$v->as_status);
             $h[$v->associate_id] = $pq->status.'-'.$dt;
            //dd($pq);

            if(isset($ben[$v->associate_id])){
                $ldt = $ben[$v->associate_id]->status_date??$dt;

                if($dt >=  $ldt || $v->as_status == 2){

                    $ldt = Carbon::parse($dt)->addDay()->toDateString();
    

                }
                DB::table('hr_as_basic_info')
                    ->where('as_id', $v->as_id)
                    ->update(['as_status_date' => $ldt]);

               
               DB::table('hr_all_given_benefits')
                    ->where('associate_id', $v->associate_id)
                    ->update(
                        [
                            'status_date' => $ldt,
                            'salary_date' => $dt 
                        ]
                    );

            }
        }
        
        return ( $h);
    }
    
    public function makeHoliday()
    {
        $first_day = date('Y-12-31');
        $year = date('Y', strtotime($first_day));
        $yearMonth = date('Y/m', strtotime($first_day));
        
        $month = date('m', strtotime($first_day));
        $day_count = 31;
        $date_by_day = [];
        for ($i = 1; $i <= $day_count; $i++) {
            $date = $yearMonth.'/'.$i;
            
            $date = date('Y-m-d', strtotime($date));
            $day = date('D', strtotime($date));
            $date_by_day[$day][] = $date;
            //$date_by_day[] = $date;
        }

           
        $holiday = DB::table('hr_roaster_holiday as r')
            ->select('r.*','b.associate_id')
            ->where('b.shift_roaster_status', 1)
            //->whereIn('b.as_unit_id', [2])
            ->leftJoin('hr_as_basic_info as b','b.as_id','r.as_id')
            ->get();

        $holidays = collect($holiday)
                        ->groupBy('day');
       
                        
        //return $holidays;
        $exists = DB::table('holiday_roaster')
            ->select(
                DB::raw("CONCAT(date,as_id) AS pp"),
                'remarks'
            )
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->keyBy('pp');

        foreach ($holidays as $key => $emp) {
            $ins = [];
            foreach ($emp as $k1 => $h) {
                if(isset($date_by_day[$key])){ 
                    
                    foreach ($date_by_day[$key] as $k => $v) {
                        
                        if(!isset($exists[$v.$h->associate_id])){

                            $ins[$v.$h->associate_id] = array(
                                'year'  => $year,
                                'month' => $month,
                                'as_id' => $h->associate_id,
                                'date'  =>  $v,
                                'remarks'   => 'Holiday',
                                'status' => 1
                            );
                            
                            DB::table('hr_absent')->where('associate_id',$h->associate_id)->where('date', $v)->delete();
                        }
                    }
                }
            }
            DB::table('holiday_roaster')->insertOrIgnore($ins);
        }

        return 'done';
    }
    
    public function makeholidayE()
    {
        $data = [];
                
            foreach($data as $d){
                DB::table('holiday_roaster')
                ->insertOrIgnore([
                    'year'  => 2021,
                    'month' => '02',
                    'as_id' => $d,
                    'date'  =>  '2021-04-02',
                    'remarks'   => 'Holiday',
                    'status' => 1
                ]);
            }
    }
    
    public function makeatt()
    {
        $data =  DB::table('hr_buyer_att_ceil4')
            ->where('in_date','>=','2021-02-01')
            ->where('in_date','<=','2021-02-28')
            ->where('in_time', 'like', '%08:02%')
            ->where('late_status',1)
            ->get();
            
        
            
        foreach($data as $k => $v){
            $in = Carbon::parse($v->in_time)->addMinute()->format('Y-m-d H:i:s');
            DB::table('hr_buyer_att_ceil4')
                ->where('id', $v->id)
                ->update(['in_time' => $in]);
        }
        
        return '';
        
        
    }
    
    public function leaveHoliday()
    {
        $roaster = ['2021-02-05','2021-02-12','2021-02-12'];
        $lv = [];
        foreach($roaster as $k => $v){
            $c = DB::table('hr_leave')
                ->whereIn('leave_ass_id', $v->associate_id)
                ->where('leave_from','>=',$v->date)
                ->where('leave_to','<=',$v->date)
                ->first();
                
            if($c){
                $lv[] = $c;
            }
        }
        
        return $lv;
    }
    
    

    public function jobcardupdate()
    {
        /*$id = DB::table('hr_monthly_salary as s')
                ->leftJoin('hr_as_basic_info as b','s.as_id','b.associate_id')
                ->where('s.month', '03')
                ->where('s.year', 2021)
                ->where('b.as_ot', 1)
                //->where('s.ot_hour','>', 0)
                ->pluck('b.as_id');*/
                
        $id = DB::table('hr_as_basic_info')
            ->whereIn('associate_id', [])->pluck('as_unit_id','as_id');
    foreach($id as $k => $i)   {   
        $tb = get_att_table($i);
        $data = DB::table($tb)
            ->where('in_date','>=','2021-03-01')
            ->where('in_date','<=','2021-03-10')
            ->where('as_id', $k)
            ->get();
    
        foreach ($data as $key => $v) 
        {
            if($v->in_time && $v->out_time){
                $queue = (new ProcessAttendanceOuttime($tb, $k, $i))
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);
            }

            

            
        }
    }
        return count($id);

    } 

    public function updateSalary()
    {
        $in = DB::table('hr_as_basic_info as b')
                ->leftJoin('hr_benefits as bn','bn.ben_as_id', 'b.associate_id')
                /*->where('as_unit_id', 2)
                ->where('as_location', 7)*/
                ->get();

        foreach ($in as $k => $val) {
            # code...
            DB::table('hr_monthly_salary')
                ->where('as_id', $val->associate_id)
                ->update([
                    'unit_id' => $val->as_unit_id,
                    'designation_id' => $val->as_designation_id,
                    'sub_section_id' => $val->as_subsection_id,
                    'location_id' => $val->as_location,
                    'pay_type' => $val->bank_name
                ]);
        }
        return 'done';
    }
    
    public function otHourCheck()
    {
        $getBasic = DB::table('hr_as_basic_info')
        ->where('as_ot', 1)
        ->whereIn('as_unit_id', [2])
        ->where('as_status', 1)
        ->pluck('as_id');
        $getat = [];
            $getData = DB::table('hr_attendance_ceil AS m')
            ->select('m.*', 'b.hr_shift_end_time', 'b.hr_shift_break_time')
            ->where('m.in_date', '>=','2021-02-01')
            ->where('m.in_date', '<=','2021-02-28')
            ->whereIn('m.as_id', $getBasic)
            ->leftJoin('hr_shift AS b', function($q){
                $q->on('b.hr_shift_code', 'm.hr_shift_code');
            })
            ->whereNotNull('m.out_time')
            ->whereNotNull('m.in_time')
            ->get();
            // dd($getData);
           
            foreach ($getData as $data) {
                $punchOut = $data->out_time;
                $shiftOuttime = date('Y-m-d', strtotime($punchOut)).' '.$data->hr_shift_end_time;
                $otDiff = ((strtotime($punchOut) - (strtotime($shiftOuttime) + (($data->hr_shift_break_time + 10) * 60))))/3600;
                if($otDiff > 0 && $data->ot_hour <= 0){
                    $getat[$data->as_id] = $data;
                    /*$queue = (new ProcessAttendanceOuttime('hr_attendance_ceil', $data->id, 2))
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);*/
                }
            }
            
            
        
        return ($getat);
       
    }
    public function lineAtt()
    {
        /*$user = DB::table('hr_as_basic_info')
            ->where('as_unit_id', 3)
            ->where('as_location', 9)
            ->pluck('as_line_id', 'as_id');

        foreach ($user as $key => $v) {
            DB::table('hr_attendance_aql')
                ->where('as_id', $key)
                ->update(['line_id' => $v]);
        }*/

        return 'hi';
    }
    public function lineUpdate()
    {
        $line = DB::table('hr_line')
                    ->select('hr_line_name','hr_line_id','hr_line_floor_id')
                    ->where('hr_line_unit_id', 3)
                    ->get()->keyBy('hr_line_name');

        $data =         [];
    $nf= [];
        foreach ($data as $key => $v) {
            if(isset($line[$v['Line']])){
                DB::table('hr_as_basic_info')
                    ->where('as_oracle_code', $key)
                    ->where('as_unit_id', 3)
                    ->update([
                        'as_line_id' => $line[$v['Line']]->hr_line_id,
                        'as_floor_id' => $line[$v['Line']]->hr_line_floor_id
                    ]);
            }else{
                $nf = [$v['Line']];
            }

        }
        return array_unique($nf);

    }


    

    public function processSalaryLeft()
    {
        $datas = DB::table('hr_as_basic_info as b')
                ->leftJoin('hr_monthly_salary as s', 's.as_id','b.associate_id')
                ->leftJoin('hr_benefits as ben', 'ben.ben_as_id', 'b.associate_id')
                ->whereIn('b.associate_id', ["17E100162N","14M100304N","17K700090P","17E100177N","18G101898N","17A500248O","18G100329N","18K100860N","18C100792N","18G100629N","15L100425N","08B100534N","11A100451N","08A100501N","17D100524N","18A100737N","19K106074N","18C101175N"
                ])
                ->where('s.month',12)
                ->where('s.year',2020)
                ->get();

        foreach ($datas as $key => $data) {
            if(isset($data->total_payable)){


                $payable = $data->present + $data->holiday + $data->absent +$data->leave;
                $perDayBasic = $data->ben_basic / 30;
                $perDayGross   = $data->ben_current_salary/ 31;
                $absent_deduct = (int) ($data->absent * $perDayBasic);

                $salaryPayable = $perDayGross*$payable - ($absent_deduct + $data->stamp);
                if($data->as_ot == 1){
                    $overtime_rate = number_format((($data->ben_basic/208)*2), 2, ".", "");
                } else {
                    $overtime_rate = 0;
                }
                $ot_payable = $overtime_rate * $data->ot_hour;

                $total_payable = ceil($salaryPayable + $ot_payable +$data->attendance_bonus + $data->production_bonus);
                $sal = [
                    'gross' => $data->ben_current_salary,
                    'basic' => $data->ben_basic,
                    'house' => $data->ben_house_rent,
                    'ot_rate' => $overtime_rate,
                    'salary_payable' => $salaryPayable,
                    'total_payable' => $total_payable,
                    'cash_payable' => $total_payable,
                    'absent_deduct' => $absent_deduct
                ];

                DB::table('hr_monthly_salary')->where('id',$data->id)->update($sal);
            }
        }

        return 'hi';
    }

   


    public function migrateemployee()
    {


    }

    public function exportReport(Request $request)
    {

        if(isset($request->date)){
            $date = $request->date;
            $data = DB::table('hr_as_basic_info AS b')
                     ->leftJoin('hr_benefits as c','b.associate_id','c.ben_as_id')
                     ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                    ->whereIn('b.as_location', auth()->user()->location_permissions())
                    ->where(function($q) use ($date){
                        $q->where(function($qa) use ($date){
                            $qa->where('b.as_status',1);
                            $qa->where('b.as_doj' , '<=', $date);
                        });
                        $q->orWhere(function($qa) use ($date){
                            $qa->whereIn('b.as_status',[2,3,4,5,6,7,8]);
                            $qa->where('b.as_status_date' , '>', $date);
                        });

                    })->get();

            $data = collect($data)->keyBy('associate_id');
            $units = auth()->user()->unit_permissions();
        
            $filename = 'Employee record -'.$date.'.xlsx';
            
            $designation = designation_by_id();
            $department = department_by_id();
            $section = section_by_id();
            $subsection = subSection_by_id();
            $unit = unit_by_id();
            $excel = [];
            foreach ($units as $key => $u) {
                
                $table = get_att_table($u).' AS a';
                $att = DB::table($table)
                        ->leftJoin('hr_as_basic_info as b','b.as_id','a.as_id')
                        ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                        ->whereIn('b.as_location', auth()->user()->location_permissions())
                        ->leftJoin('hr_benefits as c','b.associate_id','c.ben_as_id')
                        ->where('a.in_date', $date)
                        ->get();
                
                foreach ($att as $key => $a) {
                    $excel[$a->associate_id] = array(
                        'Associate ID' => $a->associate_id,
                        'Oracle ID' => $a->as_oracle_code,
                        'Name' => $a->as_name,
                        'RF ID' => $a->as_rfid_code??0,
                        'DOJ' => date('d-M-Y', strtotime($a->as_doj)),
                        'Current Salary' => $a->ben_current_salary,
                        'Basic Salary' => $a->ben_basic,
                        'House Rent' => $a->ben_house_rent??0,
                        'Cash Amount' => $a->ben_cash_amount??0,
                        'Bank/Rocket' => $a->ben_bank_amount??0,
                        'Designation' => $designation[$a->as_designation_id]['hr_designation_name']??'',
                        'Department' => $department[$a->as_department_id]['hr_department_name']??'',
                        'Section' => $section[$a->as_section_id]['hr_section_name']??'',
                        'Sub Section' => $subsection[$a->as_subsection_id]['hr_subsec_name']??'',
                        'Unit' => $unit[$a->as_unit_id]['hr_unit_short_name']??'',
                        'OT/NONOT' => $a->as_ot == 1?'OT':'NonOT',
                        'Status' => 'Present',
                        'Late' => $a->late_status,
                        'OT Hour' => numberToTimeClockFormat($a->ot_hour),
                        'Date' => $date
                    );
                    $excel[$a->associate_id]['In Time'] = '';
                    $excel[$a->associate_id]['Out Time'] = '';
                    if($a->in_time != null && $a->remarks != 'DSI'){
                        $excel[$a->associate_id]['In Time'] = date('H.i', strtotime($a->in_time));
                    }
                    if($a->out_time != null){
                        if(date('H:i', strtotime($a->out_time)) != '00:00'){
                            $excel[$a->associate_id]['Out Time'] = date('H.i', strtotime($a->out_time));
                        }
                    }
                    $excel[$a->associate_id]['AsStatus'] = $a->as_status;
                    $excel[$a->associate_id]['AsStatusDate'] = $a->as_status_date;
                }
                
                
            }
            
            $ab = DB::table('hr_absent as a')
                    ->leftJoin('hr_as_basic_info as b','b.associate_id','a.associate_id')
                    ->leftJoin('hr_benefits as c','b.associate_id','c.ben_as_id')
                    ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                    ->whereIn('b.as_location', auth()->user()->location_permissions())
                    ->where('a.date', $date)
                    ->whereIn('b.as_unit_id', $units)
                    ->get();

            $lv = DB::table('hr_leave as a')
                    ->leftJoin('hr_as_basic_info as b','b.associate_id','a.leave_ass_id')
                    ->leftJoin('hr_benefits as c','b.associate_id','c.ben_as_id')
                    ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                    ->whereIn('b.as_location', auth()->user()->location_permissions())
                    ->where('a.leave_from', "<=", $date)
                    ->where('a.leave_to', ">=", $date)
                    ->whereIn('b.as_unit_id', $units)
                    ->get();

            $do = DB::table('holiday_roaster as a')
                    ->leftJoin('hr_as_basic_info as b','b.associate_id','a.as_id')
                    ->leftJoin('hr_benefits as c','b.associate_id','c.ben_as_id')
                    ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                    ->whereIn('b.as_location', auth()->user()->location_permissions())
                    ->where('a.date', $date)
                    ->whereIn('b.as_unit_id', $units)
                    ->where('a.remarks', 'Holiday')
                    ->get();

            

            

                

            foreach ($ab as $key => $a) {
                $excel[$a->associate_id] = array(
                    'Associate ID' => $a->associate_id,
                    'Oracle ID' => $a->as_oracle_code,
                    'Name' => $a->as_name,
                    'RF ID' => $a->as_rfid_code??0,
                    'DOJ' => date('d-M-Y', strtotime($a->as_doj)),
                    'Current Salary' => $a->ben_current_salary,
                    'Basic Salary' => $a->ben_basic,
                    'House Rent' => $a->ben_house_rent??0,
                    'Cash Amount' => $a->ben_cash_amount??0,
                    'Bank/Rocket' => $a->ben_bank_amount??0,
                    'Designation' => $designation[$a->as_designation_id]['hr_designation_name']??'',
                    'Department' => $department[$a->as_department_id]['hr_department_name']??'',
                    'Section' => $section[$a->as_section_id]['hr_section_name']??'',
                    'Sub Section' => $subsection[$a->as_subsection_id]['hr_subsec_name']??'',
                    'Unit' => $unit[$a->as_unit_id]['hr_unit_short_name']??'',
                    'OT/NONOT' => $a->as_ot == 1?'OT':'NonOT',
                    'Status' => 'Absent',
                    'Late' => '',
                    'OT Hour' => '',
                    'Date' => $date,
                    'In Time' =>  '',
                    'Out Time' => '',
                    'AsStatus' => $a->as_status,
                        'AsStatusDate' => $a->as_status_date

                );
            }

            foreach ($lv as $key => $a) {
                $excel[$a->associate_id] = array(
                    'Associate ID' => $a->associate_id,
                    'Oracle ID' => $a->as_oracle_code,
                    'Name' => $a->as_name,
                    'RF ID' => $a->as_rfid_code??0,
                    'DOJ' => date('d-M-Y', strtotime($a->as_doj)),
                    'Current Salary' => $a->ben_current_salary,
                    'Basic Salary' => $a->ben_basic,
                    'House Rent' => $a->ben_house_rent??0,
                    'Cash Amount' => $a->ben_cash_amount??0,
                    'Bank/Rocket' => $a->ben_bank_amount??0,
                    'Designation' => $designation[$a->as_designation_id]['hr_designation_name']??'',
                    'Department' => $department[$a->as_department_id]['hr_department_name']??'',
                    'Section' => $section[$a->as_section_id]['hr_section_name']??'',
                    'Sub Section' => $subsection[$a->as_subsection_id]['hr_subsec_name']??'',
                    'Unit' => $unit[$a->as_unit_id]['hr_unit_short_name']??'',
                    'OT/NONOT' => $a->as_ot == 1?'OT':'NonOT',
                    'Status' => 'Leave',
                    'Late' => '',
                    'OT Hour' => '',
                    'Date' => $date,
                    'In Time' =>  '',
                    'Out Time' => '',
                    'AsStatus' => $a->as_status,
                        'AsStatusDate' => $a->as_status_date
                );
            }

            foreach ($do as $key => $a) {
                $excel[$a->associate_id] = array(
                    'Associate ID' => $a->associate_id,
                    'Oracle ID' => $a->as_oracle_code,
                    'Name' => $a->as_name,
                    'RF ID' => $a->as_rfid_code??0,
                    'DOJ' => date('d-M-Y', strtotime($a->as_doj)),
                    'Current Salary' => $a->ben_current_salary,
                    'Basic Salary' => $a->ben_basic,
                    'House Rent' => $a->ben_house_rent??0,
                    'Cash Amount' => $a->ben_cash_amount??0,
                    'Bank/Rocket' => $a->ben_bank_amount??0,
                    'Designation' => $designation[$a->as_designation_id]['hr_designation_name']??'',
                    'Department' => $department[$a->as_department_id]['hr_department_name']??'',
                    'Section' => $section[$a->as_section_id]['hr_section_name']??'',
                    'Sub Section' => $subsection[$a->as_subsection_id]['hr_subsec_name']??'',
                    'Unit' => $unit[$a->as_unit_id]['hr_unit_short_name']??'',
                    'OT/NONOT' => $a->as_ot == 1?'OT':'NonOT',
                    'Status' => 'Day Off',
                    'Late' => '',
                    'OT Hour' => '',
                    'Date' => $date,
                    'In Time' =>  '',
                    'Out Time' => '',
                    'AsStatus' => $a->as_status,
                        'AsStatusDate' => $a->as_status_date
                );
            }

            foreach ($data as $key => $a) {
                if(!isset($excel[$a->associate_id])){

                    $excel[$a->associate_id] = array(
                        'Associate ID' => $a->associate_id,
                        'Oracle ID' => $a->as_oracle_code,
                        'Name' => $a->as_name,
                        'RF ID' => $a->as_rfid_code??0,
                        'DOJ' => date('d-M-Y', strtotime($a->as_doj)),
                        'Current Salary' => $a->ben_current_salary,
                        'Basic Salary' => $a->ben_basic,
                        'House Rent' => $a->ben_house_rent??0,
                        'Cash Amount' => $a->ben_cash_amount??0,
                        'Bank/Rocket' => $a->ben_bank_amount??0,
                        'Designation' => $designation[$a->as_designation_id]['hr_designation_name']??'',
                        'Department' => $department[$a->as_department_id]['hr_department_name']??'',
                        'Section' => $section[$a->as_section_id]['hr_section_name']??'',
                        'Sub Section' => $subsection[$a->as_subsection_id]['hr_subsec_name']??'',
                        'Unit' => $unit[$a->as_unit_id]['hr_unit_short_name']??'',
                        'OT/NONOT' => $a->as_ot == 1?'OT':'NonOT',
                        'Status' => $a->as_status.' '.$a->as_status_date,
                        'Late' => '',
                        'OT Hour' => '',
                        'Date' => $date,
                        'In Time' =>  '',
                        'Out Time' => '',
                        'AsStatus' => $a->as_status,
                        'AsStatusDate' => $a->as_status_date

                    );
                }
            }

            

            return (new FastExcel(collect($excel)))->download($filename);
        }

        return view('common.employee-record');
    }





    public function bulkManualStore($request)
    {
        // dd($request->all());
        $unit=$request['as_unit_id'];
        $info = Employee::where('as_id',$request['as_id'])->first();
        $tableName= get_att_table($unit);
        $date = $request['in_date'];
        $month = '09';
        $year = '2020';

        if(strlen($request['in_time']) > 2){
            $intime = date('H:i:s', strtotime($request['in_time']));
        }else{
            $intime = date('H:i:s', strtotime($request['in_time'].'.0'));
        }

        if(strlen($request['out_time']) > 2){
            $outtime = date('H:i:s', strtotime($request['out_time']));
        }else{
            $outtime = date('H:i:s', strtotime($request['out_time'].'.0'));
        }

        
        

        $day_of_date = date('j', strtotime($date));
        $day_num = "day_".$day_of_date;
        $shift= DB::table("hr_shift_roaster")
        ->where('shift_roaster_month', $month)
        ->where('shift_roaster_year', $year)
        ->where("shift_roaster_user_id", $info->as_id)
        ->select([
            $day_num,
            'hr_shift.hr_shift_id',
            'hr_shift.hr_shift_start_time',
            'hr_shift.hr_shift_end_time',
            'hr_shift.hr_shift_code',
            'hr_shift.hr_shift_break_time',
            'hr_shift.hr_shift_night_flag'
        ])
        ->leftJoin('hr_shift', function($q) use($day_num, $unit) {
            $q->on('hr_shift.hr_shift_name', 'hr_shift_roaster.'.$day_num);
            $q->where('hr_shift.hr_shift_unit_id', $unit);
        })
        ->orderBy('hr_shift.hr_shift_id', 'desc')
        ->first();
        
        if(!empty($shift) && $shift->$day_num != null){
            $shift_start= $shift->hr_shift_start_time;
            $shift_end= $shift->hr_shift_end_time;
            $break= $shift->hr_shift_break_time;
            $nightFlag= $shift->hr_shift_night_flag;
            $shiftCode= $shift->hr_shift_code;
            $new_shift_id = $shift->hr_shift_id;
        }else{
            $shift_start= $info->shift['hr_shift_start_time'];
            $shift_end= $info->shift['hr_shift_end_time'];
            $break= $info->shift['hr_shift_break_time'];
            $nightFlag= $info->shift['hr_shift_night_flag'];
            $shiftCode= $info->shift['hr_shift_code'];
            $new_shift_id= $info->shift['hr_shift_id'];
        }

        DB::beginTransaction();
        try {
                    $checkDay = EmployeeHelper::employeeDateWiseStatus($date, $info->associate_id, $info->as_unit_id, $info->shift_roaster_status);
                    if($checkDay == 'open' || $checkDay == 'OT'){
                        $insert = [];
                        $insert['remarks'] = 'BM';
                        $insert['as_id'] = $info->as_id;
                        $insert['hr_shift_code'] = $shiftCode;

                        
                        if (strpos($intime, ':') !== false) {
                            list($one,$two,$three) = array_pad(explode(':',$intime),3,0);
                            if((int)$one+(int)$two+(int)$three == 0) {
                                $intime = null;
                            }
                        }

                        
                        if (strpos($outtime, ':') !== false) {
                            list($one,$two,$three) = array_pad(explode(':',$outtime),3,0);
                            if((int)$one+(int)$two+(int)$three == 0) {
                                $outtime = null;
                            }
                        }

                        

                        if($intime == null && $outtime == null){
                            $absentData = [
                                'associate_id' => $info->associate_id,
                                'date' => $date,
                                'hr_unit' => $info->as_unit_id
                            ];
                            $getAbsent = Absent::where($absentData)->first();
                            if($getAbsent == null && $checkDay == 'open'){
                                Absent::insert($absentData);
                            }
                        }else{
                            if($intime == '00:00:00' || $intime == null){
                                $empIntime = $shift_start;
                                $insert['remarks'] = 'DSI';
                            }else{
                                $empIntime = $intime;
                            }
                            $attInsert = 0;
                            $insert['in_time'] = $date.' '.$empIntime;
                            if($outtime == '00:00:00' || $outtime == null){
                                $insert['out_time'] = null;
                            }else{
                                $insert['out_time'] = $date.' '.$outtime;
                            }
                            if($checkDay == 'OT'){
                                $insert['late_status'] = 0;
                            }else if($intime != null){
                                $insert['in_unit'] = $unit;
                                $insert['late_status'] = $this->getLateStatus($unit, $new_shift_id,$date,$intime,$shift_start);
                            }else{
                                $insert['late_status'] = 1;
                            }
                            if($outtime != null){
                                $insert['out_unit'] = $unit;
                                $insert['out_time'] = $date.' '.$outtime;
                                if($intime != null) {
                                    // out time is tomorrow
                                    if(strtotime($intime) > strtotime($outtime)) {
                                        $dateModify = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                                        $insert['out_time'] = $dateModify.' '.$outtime;
                                    }
                                }
                            }

                            //check OT hour if out time exist
                            if($intime != null && $outtime != null && $info->as_ot != 0 && $insert['remarks'] != 'DSI'){
                                $overtimes = EmployeeHelper::daliyOTCalculation($insert['in_time'], $insert['out_time'], $shift_start, $shift_end, $break, $nightFlag, $info->associate_id, $info->shift_roaster_status, $unit);
                                $insert['ot_hour'] = $overtimes;
                            }else{
                                $insert['ot_hour'] = 0;
                            }
                            $insert['in_date'] = date('Y-m-d', strtotime($insert['in_time']));
                            DB::table($tableName)->insert($insert);
                            
                            //
                            $absentWhere = [
                                'associate_id' => $info->associate_id,
                                'date' => $date,
                                'hr_unit' => $info->as_unit_id
                            ];
                            Absent::where($absentWhere)->delete();
                            
                        }
                    }else{
                        $absentWhere = [
                            'associate_id' => $info->associate_id,
                            'date' => $date,
                            'hr_unit' => $info->as_unit_id
                        ];
                        Absent::where($absentWhere)->delete();
                    }

              

            //dd($year);exit;
            $yearMonth = $year.'-'.$month;
            if($month == date('m')){
                $totalDay = date('d');
            }else{
                $totalDay = Carbon::parse($yearMonth)->daysInMonth;
            }

            DB::commit();
            return 'success';
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
             
        }
    }



    public function getLateStatus($unit,$shift_id,$date,$intime,$shift_start)
    {
        $getLateCount = HrLateCount::getUnitShiftIdWiseCheckExists($unit, $shift_id);
        if($getLateCount != null){
            if(date('Y-m-d', strtotime($date))>= $getLateCount->date_from && date('Y-m-d', strtotime($date)) <= $getLateCount->date_to){
                $lateTime = $getLateCount->value;
            }else{
                $lateTime = $getLateCount->default_value;
            }
        }else{
            $lateTime = 180;
        }
        $shiftinTime = (strtotime(date("H:i:s", strtotime($shift_start)))+$lateTime);
        if(strtotime(date('H:i:s', strtotime($intime))) > $shiftinTime){
            $late = 1;
        }else{
            $late = 0;
        }
        return $late;
    }

    

    public function noMacth()
    {
        $nomatch = [];
        foreach ($getData as $key => $value) {
            $flag=0;
            $counter=0;
            foreach ($getEmployee as $emp) {
                ++$counter;
                if($emp->as_oracle_code == $value['PID']){
                    $flag=0;
                    break;
                }else{
                    $flag++;
                    continue;
                }
            }

            if($flag>0 || $counter==0 ){
                $nomatch[] = $value['PID'];
            }
        }


        return ($nomatch);
    }


    public function check()
    {

        
        $leave_array = [];
            $absent_array = [];
            for($i=1; $i<=31; $i++) {
            $date = date('Y-m-d', strtotime('2021-01-'.$i));
            $leave = DB::table('hr_leave AS l')
                    ->where('l.leave_from', '<=', $date)
                    ->where('l.leave_to',   '>=', $date)
                    ->where('l.leave_status', '=', 1)
                    ->leftJoin('hr_as_basic_info AS b', function($q){
                        $q->on('b.associate_id', 'l.leave_ass_id');
                    })
                    ->pluck('b.as_id', 'b.associate_id');
            $leave_array[] = $leave;
            $absent_array[] = DB::table('holiday_roaster')
                    ->whereDate('date', $date)
                    ->whereIn('as_id', $leave)
                    ->get()->toArray();
            }
            // return "done";
            dump($leave_array,$absent_array);
            dd('end');
        
    }
    public function monthlycheck($value='')
    {
        $user = DB::table('hr_as_basic_info')->where('as_doj', '>=','2021-01-01')->get();
        $data = [];
        foreach ($user as $key => $e) {
            $query[] = DB::table('holiday_roaster')
                                      ->where('as_id', $e->associate_id)
                                      ->whereDate('date','<',$e->as_doj)
                                      ->get()->toArray();
            
        }
        dd($query);
        
        $leave_array = [];
        $absent_array = [];
        for($i=1; $i<=31; $i++) {
            $date = date('Y-m-d', strtotime('2020-12-'.$i));
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
                    ->delete();
        }
        return $absent_array;
        dump($leave_array,$absent_array);
        dd('end');

            $leave_array = [];
            $absent_array = [];
            for($i=1; $i<=20; $i++) {
            $date = date('Y-m-d', strtotime('2020-11-'.$i));
            $leave = DB::table('hr_absent AS a')
                    ->where('a.date', '=', $date)
                    ->whereIn('b.as_unit_id', [1, 4, 5])
                    ->leftJoin('hr_as_basic_info AS b', function($q){
                        $q->on('b.associate_id', 'a.associate_id');
                    })
                    ->pluck('b.as_id', 'b.associate_id');
            $leave_array[] = $leave;
            $absent_array[] = DB::table('hr_attendance_mbm')
                    ->whereDate('in_time', $date)
                    ->whereIn('as_id', $leave)
                    ->get()->toArray();
            }
            dump($leave_array,$absent_array);
            dd('end');
    }

    public function getLeftEmployee()
    {

        $designation = designation_by_id();
        $department = department_by_id();
        $section = section_by_id();
        $subsection = subSection_by_id();
        $unit = unit_by_id();
        $disctrict = district_by_id();
        $upzilla = upzila_by_id();

        $data = DB::table('hr_as_basic_info as b')
                    ->whereIn('b.as_unit_id', [1,4,5])
                    ->whereIn('b.as_status', [2,3,4,5,7,8])
                    ->where('b.as_status_date', '>=', '2020-11-01')->where('b.as_status_date', '<=', '2020-11-30')->get();


       
           
        
        foreach ($data as $key => $e) {
            $sal[] = array(
                'Associate ID' =>  $e->associate_id,
                'Oracle ID' =>  $e->as_oracle_code,
                'RF ID' =>  $e->as_rfid_code,
                'Name' =>  $e->as_name,
                'DOJ' =>  $e->as_doj,
                'Designation' =>  $designation[$e->as_designation_id]['hr_designation_name'],
                'Section' =>  $section[$e->as_section_id]['hr_section_name'],
                'Department' =>  $department[$e->as_department_id]['hr_department_name'],
                'Unit' =>  $unit[$e->as_unit_id]['hr_unit_short_name'],
                'OT/NONOT' => $e->as_ot == 1?'OT':'NonOT',
                'Date' => $e->as_status_date,
                'Status' => emp_status_name($e->as_status)
            );
        }

        return (new FastExcel(collect($sal)))->download('Monthly Summary.xlsx');

    }
    
    
    public function getMonthlySalary(Request $request)
    {
        $month = $request->month??date('m');
        $year = $request->year??date('Y');
        $designation = designation_by_id();
        $department = department_by_id();
        $section = section_by_id();
        $subsection = subSection_by_id();
        $unit = unit_by_id();
        $disctrict = district_by_id();
        $upzilla = upzila_by_id();

        $data = DB::table('hr_monthly_salary AS s')
                    ->leftJoin('hr_as_basic_info AS b','b.associate_id','s.as_id' )
                    ->leftJoin('hr_benefits AS ben','ben.ben_as_id','b.associate_id' )
                    ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                    ->whereIn('b.as_location', auth()->user()->location_permissions())
                    ->where('s.emp_status', 1)
                    ->where('s.month',$month )
                    ->where('s.year', $year)
                    ->get();
        
        foreach ($data as $key => $e) {
            $sal[] = array(
                'Name' =>  $e->as_name,
                'Associate ID' =>  $e->associate_id,
                'Oracle ID' =>  $e->as_oracle_code,
                'OT/NONOT' => $e->ot_status == 1?'OT':'NonOT',
                'Present' => $e->present,
                'Leave' => $e->leave,
                'Absent' => $e->absent,
                'Holiday' => $e->holiday,
                'Total Day' => $e->present + $e->leave + $e->holiday ,
                'Late Count' => $e->late_count,
                'OT Hour' => $e->ot_hour,
                'OT Rate' => $e->ot_rate,
                'OT Amount' => round($e->ot_rate*$e->ot_hour,2),
                'Att Bonus' => $e->attendance_bonus,
                'Advance Salary' => $e->partial_amount,
                'Leave Adjust' => $e->leave_adjust,
                'Absent Deduct' => $e->absent_deduct,
                'Stamp' => $e->stamp,
                'Total Salary' => $e->total_payable,
                'Bank Amount' => $e->bank_payable,
                'Cash Amount' => $e->cash_payable,
                'TDS' => $e->tds,
                'Bank Name' => $e->bank_name??'',
                'Account Number' => $e->bank_no??'',
                'Current Salary' => $e->gross,
                'Basic' => $e->basic,
                'House Rent' => $e->house,
                'RF ID' =>  $e->as_rfid_code,
                'DOJ' =>  $e->as_doj,
                'Designation' =>  $designation[$e->as_designation_id]['hr_designation_name'],
                'Section' =>  $section[$e->as_section_id]['hr_section_name'],
                'Department' =>  $department[$e->as_department_id]['hr_department_name'],
                'Unit' =>  $unit[$e->as_unit_id]['hr_unit_short_name'],
            );
        }

        return (new FastExcel(collect($sal)))->download('Monthly Salary.xlsx');

    }

    public function incrementexcel()
    {
        $data = [];
        
        $exist = []; $not = [];
        foreach ($data as $key => $val) {

            $ben = DB::table('hr_benefits as b')
                            ->leftJoin('hr_as_basic_info as a','a.associate_id','b.ben_as_id')
                            ->where('a.as_oracle_code', $key)
                            ->first();
            $up['ben_current_salary'] = $val['New Gross'];
            $up['ben_basic'] = ceil(($val['New Gross']-1850)/1.5);
            $up['ben_house_rent'] = $val['New Gross'] -1850 - $up['ben_basic'];

            if($ben->ben_bank_amount > 0){
                $up['ben_bank_amount'] = $val['New Gross'];
                $up['ben_cash_amount'] = 0;
            }else{
                $up['ben_cash_amount'] = $val['New Gross'];
                $up['ben_bank_amount'] = 0;
            }

            $exist[$key] = DB::table('hr_benefits')->where('ben_id', $ben->ben_id)->update($up);

            $tableName = get_att_table($ben->as_unit_id);

            if($ben->as_status == 1){

                $queue = (new ProcessUnitWiseSalary($tableName, date('m'), date('Y'), $ben->as_id, date('d')))
                            ->onQueue('salarygenerate')
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
            }else{
                $not[]=$ben->associate_id;
            }

        }
        return $not;
    }
    
    public function increment()
    {
        $data = DB::table('hr_increment as ic')
                ->select('ic.*','a.as_id','a.as_unit_id','a.as_status','b.*')
                ->leftJoin('hr_as_basic_info as a','a.associate_id','ic.associate_id')
                ->leftJoin('hr_benefits as b','b.ben_as_id','ic.associate_id')
                ->where('ic.created_at','like','2021-02-01%')
                //->where('ic.status', 0)
                ->get();
                
        

        foreach ($data as $key => $d) {
           /* $gross = $d->current_salary + $d->increment_amount;
            $up['ben_current_salary'] = $gross;
            $up['ben_basic'] = ceil(($gross-1850)/1.5);
            $up['ben_house_rent'] = $gross -1850 - $up['ben_basic'];

            if($d->ben_bank_amount > 0 && $d->ben_cash_amount > 0){
                $up['ben_cash_amount'] = $gross - $d->ben_bank_amount;
            }else if ($d->ben_bank_amount > 0 && $d->ben_cash_amount == 0){
                $up['ben_bank_amount'] = $gross;
                $up['ben_cash_amount'] = 0;
            }else{
                $up['ben_bank_amount'] = 0;
                $up['ben_cash_amount'] = $gross;
            }

            DB::table('hr_benefits')->where('ben_id', $d->ben_id)->update($up);
            DB::table('hr_increment')->where('id', $d->id)->where('associate_id', $d->associate_id)->update(['status' => 1]);*/

            $tableName = get_att_table($d->as_unit_id);

            if($d->as_status == 1){
                
                $month = date('m', strtotime($d->effective_date));
              $year = date('Y', strtotime($d->effective_date));
              $t = date('t', strtotime($d->effective_date));

                $queue = (new ProcessUnitWiseSalary($tableName, $month, $year, $d->as_id, $t))
                            ->onQueue('salarygenerate')
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
            }

        }
        return count($data);
    }
    
    public function testMail()
    {
        $data = [];

        Mail::to('rakib@mbmdhaka.com')->send(new TestMail($data));
    }

    public function makeAbsent()
    {
        $data = DB::table('hr_as_basic_info')
                ->where('shift_roaster_status',1)
                ->whereIn('as_unit_id',[1,4,5])
                ->pluck('associate_id','as_id');
        $dates = ['2020-12-11','2020-01-16','2020-01-25','2020-01-17','2020-01-18'];

        foreach ($data as $key => $val) {
            $att = DB::table('hr_attendance_mbm')
                   ->whereIn('in_date', $dates)
                   ->pluck('in_date');

            $holiday = DB::table('holiday_roaster');
        }
    }

    public function getAttFile($date)
    {
        $outdate = Carbon::parse($date)->subDays(1)->toDateString();
        $outtime = DB::table('hr_attendance_mbm as a')
                    ->select('a.out_time','b.as_rfid_code')
                    ->leftJoin('hr_as_basic_info as b', 'a.as_id', 'b.as_id')
                    ->where('a.out_time', 'like', $outdate.'%')
                    ->get();
        
        foreach ($outtime as $key => $val) {
            
        }
    }

    public function setSalaryDate()
    {
        $data = DB::table('hr_monthly_salary')->whereIn('emp_status',[2,3,4,5,6,7])->get();

        foreach ($data as $key => $v) {
            $date = date('Y-m-d', strtotime($v->year.'-'.$v->month.'-'.($v->present+$v->absent+$v->holiday+$v->leave)));
            DB::table('hr_all_given_benefits')->where('associate_id', $v->as_id)->update([
                'salary_date' => $date
            ]);
        }

    }

    

    public function insertRoaster()
    {

        $json = '';

        $ex = collect(json_decode($json))->toArray();




        return 'success';
    }
    
    public function substitute()
    {
        $designation = designation_by_id();
        $department = department_by_id();
        $section = section_by_id();
        $subsection = subSection_by_id();
        $unit = unit_by_id();
        $disctrict = district_by_id();
        $upzilla = upzila_by_id();
        
        $data = DB::table('hr_as_basic_info AS b')
                ->select('r.as_id','r.in_date','b.associate_id','b.as_oracle_code','b.as_status','b.as_name','b.as_section_id','b.as_designation_id','b.as_department_id','b.as_unit_id','bn.ben_current_salary','b.as_doj')
                ->leftJoin('hr_attendance_mbm AS r','b.as_id','r.as_id')
                ->leftJoin('hr_benefits as bn','bn.ben_as_id','b.associate_id')
                ->whereIn('r.in_date',['2020-10-30','2020-10-02'])
                //->where('b.as_emp_type_id', 3)
                //->where('b.as_doj','>=','2020-08-09')
                ->get();
           
        $employees = collect($data)->groupBy('as_id');
        $sal=[];
        foreach ($employees as $key => $e) {
            $sal[$key] = array(
                'Associate ID' =>  $e[0]->associate_id,
                'Oracle ID' =>  $e[0]->as_oracle_code,
                'Name' =>  $e[0]->as_name,
                'DOJ' =>  $e[0]->as_doj,
                'Designation' =>  $designation[$e[0]->as_designation_id]['hr_designation_name'],
                'Section' =>  $section[$e[0]->as_section_id]['hr_section_name'],
                'Department' =>  $department[$e[0]->as_department_id]['hr_department_name'],
                'Unit' =>  $unit[$e[0]->as_unit_id]['hr_unit_short_name'],
                'Gross' =>  $e[0]->ben_current_salary,
                'Day' => count($e),
                'Per Day' =>  round($e[0]->ben_current_salary/31,2),
                'Total' => ceil(count($e)*round($e[0]->ben_current_salary/31,2)),
                'Status' => emp_status_name($e[0]->as_status)
                
            );
            $sal[$key]['Date1'] = '';
            $sal[$key]['Date2'] = '';
            if($e[0]->in_date == '2020-10-02'){
                $sal[$key]['Date1'] =  $e[0]->in_date;
            }else{
                $sal[$key]['Date2'] =  $e[0]->in_date;
            }
            if(isset($e[1])){
                if($e[1]->in_date == '2020-10-30'){
                    $sal[$key]['Date2'] =  $e[1]->in_date;
                }else{
                    $sal[$key]['Date1'] =  $e[1]->in_date;
                }
                  
            }
        }
        return (new FastExcel(collect($sal)))->download('Substitute Holiday Payment.xlsx');
    }
    
    public function newMigrate()
    {
        $section = subSection_by_id();
        $designation = designation_by_id();

        $emps =  [];

        $insert = [];
        foreach ($emps as $key => $v) {
            $insert[$key]['as_oracle_code'] = $key;
            $insert[$key]['worker_name'] = $v['NAME'];
            $insert[$key]['worker_doj'] = date('Y-m-d', strtotime($v['doj']));
            $insert[$key]['worker_dob'] = date('Y-m-d', strtotime($v['dob']));
            $insert[$key]['worker_ot'] = $v['OT'] == 'Y'?1:0;
            $insert[$key]['worker_gender'] = $v['sex'] == 'M'?'Male':'Female';
            $insert[$key]['worker_unit_id'] = 3;
            $insert[$key]['location_id'] = 9;
            $insert[$key]['worker_area_id'] = null;
            $insert[$key]['worker_department_id'] = null;
            $insert[$key]['worker_section_id'] = null;
            $insert[$key]['worker_subsection_id'] = null;
            $insert[$key]['worker_emp_type_id'] = null;
            $insert[$key]['worker_designation_id'] = null;
            $c = 0;
            if($v['SECTION'] != null){
                $k = $v['SECTION'];
                if(isset($section[$k])){

                    $insert[$key]['worker_area_id'] = $section[$k]['hr_subsec_area_id'];
                    $insert[$key]['worker_department_id'] = $section[$k]['hr_subsec_department_id'];
                    $insert[$key]['worker_section_id'] = $section[$k]['hr_subsec_section_id'];
                    $insert[$key]['worker_subsection_id'] = $k;
                }

            }
            if($v['DESIGNATION'] != null){

                $kd = $v['DESIGNATION'];
                if(isset($designation[$kd])){
                    $insert[$key]['worker_emp_type_id'] = $designation[$kd]['hr_designation_emp_type'];
                    $insert[$key]['worker_designation_id'] = $kd;
                }
            }

            $insert[$key]['worker_color_band_join'] = 1;
            $insert[$key]['worker_doctor_acceptance'] = 1;

        }

        return DB::table('hr_worker_recruitment')->insert($insert);

        return (count($insert));
    }
    
    public function migrateAll(){
        $data = DB::table('hr_worker_recruitment')
            ->where('worker_unit_id', 3)
            ->whereNotNull('worker_department_id')
            ->whereNotIn('as_oracle_code',['21B1060E','21B1333E','21B1421E','21B1424E','21B3240F','21B3241F','21B3430G','21B3657G','21B4655J','21B4656J'])
            ->take(15)
            ->get();
            $d = [];
        foreach ($data as $key => $worker) {
            DB::beginTransaction();
            try {
                if ( ($worker->worker_unit_id != null || $worker->worker_unit_id != ''))
                {
                    $location= DB::table('hr_location')->where('hr_location_unit_id', $worker->worker_unit_id)->orderBy('hr_location_id', 'asc')->first(['hr_location_id']); 
                    $shift_exist= DB::table('hr_shift')
                            ->where('hr_shift_unit_id', $worker->worker_unit_id)
                            ->where('hr_shift_default', 1)
                            ->pluck('hr_shift_name')
                            ->first();
                    
                    $IDGenerator = (new  \App\Http\Controllers\Hr\IDGenerator)->generator2(array(
                        'department' => $worker->worker_department_id,
                        'date' => $worker->worker_doj
                    ));

                    

                    if (!empty($IDGenerator['error']))
                    {
                    }
                    else if(strlen($IDGenerator['id']) != 10)
                    {
                    }
                    else if($shift_exist == null)
                    {
                    }
                    else
                    {
                        //Default Shift Code
                        $default_shift= DB::table('hr_shift')
                        ->where('hr_shift_unit_id', $worker->worker_unit_id)
                        ->where('hr_shift_default', 1)
                        ->pluck('hr_shift_name')
                        ->first();
                        /*---INSERT INTO BASIC INFO TABLE---*/
                        $check = Employee::insert(array(
                            'as_emp_type_id'  => $worker->worker_emp_type_id,
                            'as_unit_id'      => $worker->worker_unit_id,
                            'as_shift_id'     => $default_shift,
                            'as_area_id'      => $worker->worker_area_id,
                            'as_department_id' => $worker->worker_department_id,
                            'as_section_id'  => $worker->worker_section_id,
                            'as_subsection_id'  => $worker->worker_subsection_id,
                            'as_designation_id' => $worker->worker_designation_id,
                            'as_doj'         => (!empty($worker->worker_doj)?date('Y-m-d',strtotime($worker->worker_doj)):null),
                            'temp_id'        => $IDGenerator['temp'],
                            'associate_id'   => $IDGenerator['id'],
                            'as_name'        => $worker->worker_name,
                            'as_gender'      => $worker->worker_gender,
                            'as_dob'         => (!empty($worker->worker_dob)?date('Y-m-d',strtotime($worker->worker_dob)):null),
                            'as_contact'     => $worker->worker_contact??'',
                            'as_ot'          => $worker->worker_ot,
                            'as_oracle_code' => $worker->as_oracle_code,
                            'as_oracle_sl'   => ($worker->as_oracle_code != ''?substr($worker->as_oracle_code,3, -1):''),
                            'as_rfid_code'   => $worker->as_rfid,
                            'as_pic'         => null,
                            'created_at'     => date("Y-m-d H:i:s"),
                            'created_by'     => null,
                            'as_status'      => 1 ,
                            'as_location'    => $location->hr_location_id??''
                        ));

                        DB::table('hr_med_info')->insert(array(
                            'med_as_id'       => $IDGenerator['id'],
                            'med_height'      => $worker->worker_height,
                            'med_weight'      => $worker->worker_weight,
                            'med_tooth_str'   => $worker->worker_tooth_structure,
                            'med_blood_group' => $worker->worker_blood_group,
                            'med_ident_mark'  => (!empty($worker->worker_identification_mark)?$worker->worker_identification_mark:"N/A"),
                            'med_doct_comment'   => $worker->worker_doctor_comments,
                            'med_doct_conf_age'  => $worker->worker_doctor_age_confirm,
                            'med_doct_signature' => $worker->worker_doctor_signature
                        ));

                        DB::table('hr_as_adv_info')->insert(array(
                            'emp_adv_info_as_id' => $IDGenerator['id'],
                            'emp_adv_info_nid'   => $worker->worker_nid
                        ));


                        $t = DB::table('hr_worker_recruitment')->where('worker_id', $worker->worker_id)
                            ->delete();

                        // make default absent
                        DB::table('hr_absent')->insert([
                            'associate_id' => $IDGenerator['id'],
                            'date' => date('Y-m-d'),
                            'hr_unit' => $worker->worker_unit_id
                        ]);
                        $d[] = $IDGenerator['id'];
                        

                        Cache::forget('employee_count');
                        DB::commit();
                    }
                }
                
            } catch (\Exception $e) {
                $d[] = $e->getMessage();
                DB::rollback();
            }
        }
        Cache::forget('employee_count');

        return $d;
    }
    
    public function set_emp_type_id()
    {
        $designation = designation_by_id();
        $data = DB::table('hr_worker_recruitment')
                    ->whereIn('worker_unit_id',[3,8])
                    ->whereIn('location_id',[9,11])
                    ->get();

        foreach ($data  as $key => $v) {
            if($v->worker_designation_id != null){
                $kd = $v->worker_designation_id;
                if(isset($designation[$kd])){
                    DB::table('hr_worker_recruitment')
                        ->where('worker_id', $v->worker_id)
                        ->update([
                            'worker_emp_type_id' => $designation[$kd]['hr_designation_emp_type']
                        ]);
                }
            }
            # code...
        }
        
        return 'done';
    }
    
    public function updateRFID()
    {
        $data = [];

        foreach ($data as $key => $v) {
            DB::table('hr_as_basic_info')
                ->where('as_unit_id', 3)
                ->where('as_location', 9)
                ->where('as_oracle_code', $key)
                ->update([
                    'as_contact' => $v['as_contact']
                ]);
        }

        return 'done';
    }
    
    public function advBn()
    {
        $data = [];


        $as = DB::table('hr_as_basic_info')
            ->where('as_unit_id', 8)
            ->pluck('associate_id','as_oracle_code');

        $bn = DB::table('hr_employee_bengali')
                ->whereIn('hr_bn_associate_id', $as)
                ->pluck('hr_bn_associate_id')->toArray();
        $insert = [];
        foreach ($data as $key => $v) {
            if(isset($as[$key])){
                DB::table('hr_as_adv_info')
                    ->where('emp_adv_info_as_id', $as[$key])
                    ->update([
                        'emp_adv_info_nationality' => 'BANGLADESHI', 
                        'emp_adv_info_fathers_name' => $v['FNAME'], 
                        'emp_adv_info_mothers_name' => $v['MNAME'], 
                        'emp_adv_info_spouse' => $v['HNAME'], 
                        /*'emp_adv_info_children' => $v['CHILDREN'], */
                        //'emp_adv_info_religion' => $v['FNAME'], 
                        'emp_adv_info_per_vill' => $v['PAD1'],
                        'emp_adv_info_per_po' => $v['PPOST'],
                        /*'emp_adv_info_per_dist' => $v['PDIST'],*/
                        'emp_adv_info_pres_house_no' => $v['CAD1'],
                        'emp_adv_info_pres_road' => $v['CAD2'],
                        'emp_adv_info_pres_po' => $v['CPOST'],
                        'emp_adv_info_pres_dist' => $v['CDIST'],
                        'emp_adv_info_pres_upz' => $v['CTHANA'],
                    ]);

                if(in_array($as[$key], $bn)){
                    DB::table('hr_employee_bengali')
                        ->where('hr_bn_associate_id', $as[$key])
                        ->update([
                        'hr_bn_associate_name' => $v['Bn_name'], 
                        'hr_bn_father_name' => $v['BFATHER'], 
                        'hr_bn_mother_name' => $v['MOTHER'], 
                        'hr_bn_permanent_village' => $v['BGRAM'],
                        'hr_bn_permanent_po' => $v['BPOST'],
                        'hr_bn_present_road' => $v['ROAD_NO'],
                        'hr_bn_present_house' => $v['HOUSE_NO'],
                        'hr_bn_present_po' => $v['PO']
                    ]);

                }else{
                    $insert[$key] = [
                        'hr_bn_associate_id' => $as[$key],
                        'hr_bn_associate_name' => $v['Bn_name'], 
                        'hr_bn_father_name' => $v['BFATHER'], 
                        'hr_bn_mother_name' => $v['MOTHER'], 
                        'hr_bn_permanent_village' => $v['BGRAM'],
                        'hr_bn_permanent_po' => $v['BPOST'],
                        'hr_bn_present_road' => $v['ROAD_NO'],
                        'hr_bn_present_house' => $v['HOUSE_NO'],
                        'hr_bn_present_po' => $v['PO']
                    ];
                }
            }
        }
        DB::table('hr_employee_bengali')->insert($insert);

        return 'dd';
    }
    
    
    public function isHoliday($associateId, $date){
        $ndate = Carbon::parse($date)->addDay()->toDateString(); 
        $dt = DB::table('holiday_roaster')
          ->where('date', $ndate)
          ->where('as_id', $associateId)
          ->whereIn('remarks', ['Holiday', 'OT'])
          ->first();
          
        if($dt){
            return $this->isHoliday($associateId, $ndate);
        }else{
            return $date;
        }
    }
    
    public function isGlobalHoliday($unit, $date){
        $ndate = Carbon::parse($date)->addDay()->toDateString(); 
        $dt = DB::table('hr_yearly_holiday_planner')
          ->where('hr_yhp_dates_of_holidays', $ndate)
          ->where('hr_yhp_unit', $unit)
          ->whereIn('hr_yhp_open_status', [0,2])
          ->first();
          
        if($dt){
            return $this->isGlobalHoliday($unit, $ndate);
        }else{
            return $date;
        }
    }
    
    
    
    public function processBuyerLeftSalary()
    {
        $month = '09'; $table = 'hr_buyer_salary_aql'; $attTable = 'hr_buyer_att_aql';
        $unit = 3;
        $monthStart = '2021-09-01';
        $monthEnd = '2021-09-30';
        $dayCount = 30;
        $datas = DB::table('hr_monthly_salary as s')
            ->select('s.*','b.as_id as ass')
            ->leftJoin('hr_as_basic_info as b', 'b.associate_id','s.as_id')
            ->where('b.as_unit_id', $unit)
            ->where('s.month',$month)
            ->whereIn('s.emp_status',[2,5])
            ->whereNull('s.disburse_date')
            ->where('s.location_id',9)
            ->get();

        $as_id = collect($datas)->pluck('ass');


        $buyer_sal = DB::table($table.' as s')
                    ->select('s.*')
                    ->leftJoin('hr_as_basic_info as b','b.as_id','s.as_id')
                    ->whereIn('s.as_id', $as_id)
                    ->where('b.as_unit_id', $unit)
                    ->where('s.month', $month)
                    ->get()
                    ->keyBy('as_id');

        foreach ($datas as $key => $v) {
            if(isset($buyer_sal[$v->ass])){
                    $sl = $buyer_sal[$v->ass];
                    $deductCost = ($sl->adv_deduct + $sl->cg_deduct + $sl->food_deduct + $sl->others_deduct);
                    $ot = ($v->ot_rate*$sl->ot_hour);
                    $lvadjust = $sl->leave_adjust;
                    $deductSalaryAdd = $sl->salary_add;
            }else{
                    $adv_deduct = 0;
                        $cg_deduct = 0;
                        $food_deduct = 0;
                        $others_deduct = 0;
                        $salary_add = 0;
                        $bonus_add = 0;
                        $deductCost = 0;
                        $productionBonus = 0;

                        $getAddDeduct = DB::table('hr_salary_add_deduct')
                            ->where('associate_id', $v->as_id)
                            ->where('month', '=', $month)
                            ->where('year', '=', 2021)
                            ->first();

                        if($getAddDeduct != null){
                            $advp_deduct = $getAddDeduct->advp_deduct;
                            $cg_deduct = $getAddDeduct->cg_deduct;
                            $food_deduct = $getAddDeduct->food_deduct;
                            $others_deduct = $getAddDeduct->others_deduct;
                            $salary_add = $getAddDeduct->salary_add;
                            $deductSalaryAdd = $salary_add;

                            $deductCost = ($advp_deduct + $cg_deduct + $food_deduct + $others_deduct);
                            $productionBonus = $getAddDeduct->bonus_add;
                        }

                    $ot = DB::table($attTable)
                            ->where('as_id', $v->ass)
                            ->where('in_date','>=',$monthStart)
                            ->where('in_date','<=',$monthEnd)
                            ->sum('ot_hour');
                    $ot_hour = 0;
                    $ot_num_min = min_to_ot();

                        if($ot > 0){
                            $otfm = explode(".", $ot);

                            if(isset($otfm[1])){
                                $ot_min = round((('0.'.$otfm[1]) * 60));
                                $ot_hour = $otfm[0] + ($ot_min == 1? 1:($ot_num_min[$ot_min]));
                            }else{
                                $ot_hour = $ot;
                            }
                        }

                    $ot = $ot*$v->ot_rate;
                    $lvadjust = 0;


            }
            $at = [
                'present' => $v->present,
                'absent' => $v->absent,
                'holiday' => $v->holiday,
                'late_count' => $v->late_count,
                'leave' => $v->leave,
                'ot_rate' => $v->ot_rate,
                'stamp' => $v->stamp,
                'pay_type' => null,
                'emp_status' => $v->emp_status
            ];

            $attBonus = 0;
            $salary_date = $v->present + $v->holiday + $v->leave + $v->absent;
            $stamp = $v->stamp;
            

            $perDayBasic = round(($v->basic / 30),2);
            $perDayGross = round(($v->gross /  $dayCount),2);
            $getAbsentDeduct = (int)($v->absent * $perDayBasic);

            
            
            // get salary payable calculation
            $salaryPayable = round((($perDayGross*$salary_date) - ($getAbsentDeduct + $deductCost + $stamp)), 2);
            
            $partialAmount = $v->partial_amount;
            $totalPayable = ceil((float)($salaryPayable + $ot   + $v->production_bonus + $lvadjust - $partialAmount));
            $at['partial_amount'] = $partialAmount;
            $at['total_payable'] = $totalPayable;
            $at['cash_payable'] = $totalPayable;
            $at['bank_payable'] = 0;
            $at['salary_payable'] = $salaryPayable;
            $at['leave_adjust'] = $lvadjust;
            $at['absent_deduct'] = $getAbsentDeduct;

            if(isset($buyer_sal[$v->ass])){

                DB::table($table)
                    ->where('as_id', $v->ass)
                    ->where('id', $sl->id)
                    ->update($at);
            }else{
                $at['month'] = $month;
                $at['as_id'] = $v->ass;
                $at['year'] = 2021;

                $at['gross'] = $v->gross;
                $at['basic'] = $v->basic;
                $at['house'] = $v->house;
                $at['medical'] = $v->medical;
                $at['transport'] = $v->transport;
                $at['food'] = $v->food;
                $at['late_count'] = $v->late_count;
                $at['absent_deduct'] = $getAbsentDeduct;
                $at['adv_deduct'] = $adv_deduct;
                $at['cg_deduct'] = $cg_deduct;
                $at['food_deduct'] = $food_deduct;
                $at['others_deduct'] = $others_deduct;
                $at['salary_add'] = $salary_add;
                $at['bonus_add'] = $bonus_add;
                $at['leave_adjust'] = $v->leave_adjust;
                $at['ot_hour'] = $ot_hour;
                $at['attendance_bonus'] = 0;
                $at['production_bonus'] = $productionBonus;
                $at['stamp'] = $stamp;
                $at['salary_payable'] = $salaryPayable;
                $at['total_payable'] = $totalPayable;
                $at['cash_payable'] = $totalPayable;
                $at['bank_payable'] = 0;
                $at['tds'] = 0;
                $at['pay_status'] = $v->pay_status;
                $at['pay_type'] = $v->pay_type;
                $at['emp_status'] = $v->emp_status;
                $at['ot_status'] = $v->ot_status;
                $at['designation_id'] = $v->designation_id;
                $at['subsection_id'] = $v->sub_section_id;
                $at['location_id'] = $v->location_id;
                $at['unit_id'] = $v->unit_id;
                DB::table($table)->insert($at);
            }


            
            
        }


        $inv = DB::table($table)
        
            ->whereNotIn('as_id', $as_id)
            ->whereIn('emp_status', [2,5])
            ->where('month', $month)
            ->pluck('as_id','id');


        return $inv;
    }
    
    public function addRocket(){
        
            
        
        $data = [];     
        
        $emp = DB::table('hr_as_basic_info as b')
                ->select('b.as_id','b.associate_id','ben.ben_current_salary','b.as_oracle_code')
                ->leftJoin('hr_benefits as ben','b.associate_id','ben.ben_as_id')
                ->whereIn('b.as_unit_id', [1,4,5])
                ->whereIn('b.associate_id', array_keys($data))
                ->get()->keyBy('associate_id');
        $dp = [];
        foreach($data as $key => $d){
            //$key = isset($emp[$key])?$key:'A'.$key;
            if(isset($emp[$key])){
                
            DB::table('hr_benefits')
                ->where('ben_as_id', $key)
                ->update([
                    'ben_bank_amount' => $emp[$key]->ben_current_salary,
                    'ben_cash_amount' => 0,
                    'bank_name' => 'rocket',
                    'bank_no' => $d['Account']
                  ]);
                  
           $queue = (new ProcessUnitWiseSalary('hr_attendance_mbm', '05', 2021, $emp[$key]->as_id, 31))
                            ->onQueue('salarygenerate')
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
            }else{
                $dp[$key] = $d['Account'];
            }
        }
        return (new FastExcel(collect($dp)))->download('Rocket.xlsx');
    }
    
    public function addSalary()
    {
        $emp = DB::table('hr_as_basic_info')
                ->where('as_unit_id', 3)
                ->where('as_location',9)
                ->pluck('associate_id', 'as_oracle_code');

        $ben = DB::table('hr_benefits')
                ->whereIn('ben_as_id', $emp)
                ->get()
                ->keyBy('ben_as_id');
        $data = [];


        $up = []; $ins = [];

        foreach ($data as $key => $val) {

            if(isset($emp[$key])){
                $ass = $emp[$key];
                // check salary
                $up['ben_joining_salary'] = $val['salary'];
                $up['ben_current_salary'] = $val['salary'];
                $up['ben_basic'] = ceil(($val['salary']-1850)/1.5);
                $up['ben_house_rent'] = $val['salary'] -1850 - $up['ben_basic'];

                // bank 
              /*  if($val['dbbl'] != '#N/A'){
                    $up['ben_bank_amount'] = $val['bank'];
                    $up['ben_cash_amount'] = $val['salary'] - $val['bank'] ;
                    $up['ben_tds_amount'] = $val['tds'];
                    $up['bank_name'] = 'dbbl';
                    $up['bank_no'] = $val['dbbl'];

                }else if($val['rocket'] != '#N/A'){
                // rocket
                    $up['ben_bank_amount'] = $val['salary'];
                    $up['ben_cash_amount'] = 0 ;
                    $up['bank_name'] = 'rocket';
                    $up['bank_no'] = $val['rocket'];
                }else{*/
                // cash
                    $up['ben_bank_amount'] = 0;
                    $up['ben_cash_amount'] = $val['salary'] ;

               /* }*/


                if(isset($ben[$ass])){
                    DB::table('hr_benefits')->where('ben_as_id', $ass)->update($up);
                }else{
                    $ins[$ass]['ben_as_id'] = $ass;
                    $ins[$ass] = $up;
                    $ins[$ass]['ben_medical'] = 600;
                    $ins[$ass]['ben_transport'] = 350;
                    $ins[$ass]['ben_food'] = 900;
                    $ins[$ass]['ben_status'] = 1;
                    $ins[$ass]['ben_as_id'] = $ass;
                }
            }
        }
        
        DB::table('hr_benefits')->insert($ins);
    }



    
    public function gross2pay()
    {

        $date = '2021-08-15';
        $month =  date('m', strtotime($date));
        $year  = date('Y', strtotime($date));
        $gr = 2;
        $day_count = cal_days_in_month(CAL_GREGORIAN,$month,$year);
        
        $ex = DB::table('hr_salary_adjust_details as d')
                    ->select(DB::raw('concat(m.associate_id,d.date) as d'), 'd.amount')
                    ->leftJoin('hr_salary_adjust_master as m', 'm.id','d.salary_adjust_master_id')
                    ->where('m.month', date('n', strtotime($date)))
                    ->where('d.type', 2)
                    ->get()
                    ->pluck('amount','d');
        
        
        $data = DB::table('hr_attendance_mbm as a')
                 ->select(
                    'b.as_id','b.associate_id','b.as_unit_id', 
                    DB::raw('ben.ben_current_salary/'.$day_count.' as gs')
                )
                 ->leftJoin('hr_as_basic_info as b', 'b.as_id', 'a.as_id')
                 //->leftJoin('holiday_roaster as h', 'h.as_id', 'b.associate_id')
                 ->leftJoin('hr_benefits as ben', 'ben.ben_as_id', 'b.associate_id')
                 //->whereIn('b.associate_id',['19C101174N'])
                 //->where('b.as_subsection_id',108)
                 //->where('h.remarks','OT')
                 //->where('b.as_ot',1)
                 //->where('b.as_status', 1)
                 //->where('b.shift_roaster_status', 0)
                 //->where('b.as_doj','>','2021-05-17')
                 ->whereIn('b.as_unit_id',[1,4,5])
                 ->where('a.in_date', $date)
                 //->where('h.date', $date)
                 ->get();
                 
                 
        //return $data;
        //return count($data); 
        $chk = [];
        foreach($data as $d){
            $tableName = get_att_table($d->as_unit_id);
            
            $chk[] =  $d->associate_id.$date;
            
            $master = SalaryAdjustMaster::firstOrNew([
                'associate_id' => $d->associate_id,
                'month' => $month,
                'year' => $year
            ]);
            $master->save();
            $gor = $d->gs * $gr;
            SalaryAdjustDetails::updateOrCreate(
                [
                    'salary_adjust_master_id' => $master->id,
                    'date' => $date,
                    'type' => 2,
                ],
                [
                    'amount' => number_format((float)$gor, 2, '.', ''),
                    'comment' => ''
                ]   
            );
            
            $queue = (new ProcessUnitWiseSalary($tableName, $month, $year, $d->as_id, $day_count))
                ->onQueue('salarygenerate')
                ->delay(Carbon::now()->addSeconds(2));
                dispatch($queue);
        }
        
        return $chk;
        
    }
    
    public function giveRoaster()
    {
        $data = [];

        $emp = DB::table('hr_as_basic_info')
                ->whereIn('as_oracle_code', array_keys($data))
                ->where('as_unit_id', 8)
                ->pluck('as_oracle_code','as_id');

        foreach ($emp as $key => $v) {
            $ins[] = [
                'as_id' => $key,
                'day' => ucfirst(strtolower($data[$v]['ROSTER']))
            ];
        }
        DB::table('hr_roaster_holiday')
            ->insert($ins);

        return $emp;

    }
    
    public function makeAllDayHoliday()
    {
        return [];
        $first_day = '2021-07-31';
        $last_day  = '2021-07-31';
        
        $month = date('m', strtotime($first_day));
        
        /*$emp = DB::table('hr_as_basic_info as b')
                ->where('b.as_unit_id', 8)
                ->where('as_shift_id','!=','Night')
                ->where('as_status', 1)
                ->get();*/
                
        $emp = [];
        $emp =  DB::table('hr_as_basic_info as b')
                ->where('b.as_unit_id', 8)
                ->whereNotIn('associate_id',$emp)
                ->where('as_status', 1)
                ->pluck('associate_id')->toArray();
                return $emp;
                
        $exists = DB::table('holiday_roaster')
            ->select(
                DB::raw("CONCAT(date,as_id) AS pp"),
                'remarks'
            )
            ->where('month', $month)
            ->where('year', date('Y'))
            ->get()
            ->keyBy('pp');
        
        
            
        
        $dates = [];
        $date = $first_day;
        while($date <= $last_day){
            $dates[] = $date;
            
            $ins = [];
            foreach ($emp as $k1 => $h) {
                if(!isset($exists[$date.$h])){

                    $ins[$date.$h] = array(
                        'year'  => date('Y'),
                        'month' => $month,
                        'as_id' => $h,
                        'date'  =>  $date,
                        'remarks'   => 'Holiday',
                        'comment' => 'Special Leave',
                        'status' => 1
                    );
                    
                    DB::table('hr_absent')->where('associate_id',$h)->where('date', $date)->delete();
                }
                   
            }
            //return $ins;
            DB::table('holiday_roaster')->insertOrIgnore($ins);
            
            $date = \Carbon\Carbon::parse($date)->addDay()->toDateString();
        }
        
        return count($ins);
        
    }
    
    
    
    public function rfidpx(){
        
            $data = [];
            $errors = [];
            foreach($data as $k => $v){
                try{
                DB::table('hr_as_basic_info')
                    ->where('as_unit_id', 8)
                    ->where('as_oracle_sl', $k)
                    ->update(['as_rfid_code' =>  $v['rf']]);
                    
                }catch (\Exception $e) {
                  $errors[] = $v['rf'].$e->getMessage();
        
              }
            }
            
            return $errors;
    
}

    public function setIncrementMonth()
    {
        $data = DB::table('hr_as_basic_info')
            ->select('associate_id','as_doj','as_emp_type_id')
            ->get();

        $insert = [];

        foreach ($data as $key => $v) {
            if($v->as_doj){
                if($v->as_emp_type_id == 3 && $v->as_doj < '2018-12-01'){
                    $insert[$v->associate_id] = array(
                        'associate_id' => $v->associate_id,
                        'month' => 'Dec',
                        'remarks' => 'G'
                    );
                }else{
                    $insert[$v->associate_id] = array(
                        'associate_id' => $v->associate_id,
                        'month' => date('M', strtotime($v->as_doj)),
                        'remarks' => null
                    );
                }
            }
        }

        $d = array_chunk($insert, 300);

        foreach ($d as $key => $in) {
            DB::table('hr_increment_month')
                    ->insertOrIgnore($in);
        }

        return count($d);
    }
    
    public function updateDept()
    {
        $data = [];

        $sub = subSection_by_id();

        foreach ($data as $key => $v) {
            if(isset($sub[$v['sub_section_id']])){
            $ss = $sub[$v['sub_section_id']];
            DB::table('hr_as_basic_info')
                ->where('as_unit_id', 8)
                ->where('associate_id', $key)
                ->update([
                    'as_subsection_id' => $v['sub_section_id'],
                    'as_section_id' => $ss['hr_subsec_section_id'],
                    'as_department_id' => $ss['hr_subsec_department_id'],
                    'as_area_id' => $ss['hr_subsec_area_id']
                ]);
            }
        }

        return 'done';
    }
    
    public function checkDuplicate()
    {
        $p = [];
        $dt = DB::table('holiday_roaster as h')
            ->select('h.date','b.as_id','b.associate_id')
            ->leftJoin('hr_as_basic_info as b','b.associate_id','h.as_id')
            ->where('h.date','>=','2021-03-01')
            ->where('h.date','<=','2021-03-31')
            ->where('h.remarks', 'Holiday')
            ->whereIn('b.as_unit_id',[1,4,5])
            ->get();
            
        foreach($dt as $key => $d){
            /*$a = DB::table('hr_attendance_mbm')
                ->where('in_date', $d->date)
                ->where('as_id', $d->as_id)
                ->first();*/
                
            $a = DB::table('hr_absent')
                ->where('date',$d->date)
                ->where('associate_id', $d->associate_id)
                ->first();
            if($a){
                $p[] = $a ;
            }
        }
            
        return $p;
            
            
    }
    
    public function removefriday()
    {
        $dt = '2021-03-26';
        $data = DB::table('hr_shift_roaster')
            ->where('shift_roaster_month','03')
            ->where('day_26','Friday Day')
            ->pluck('shift_roaster_user_id')->toArray();

        $att = DB::table('hr_att_special')
                ->where('in_date', $dt)
                ->whereIn('as_id',$data)
                ->delete();
    }
    
    public function findFriday()
    {
        $dt = '2021-03-26';
        $data = DB::table('hr_shift_roaster as h')
            ->select('b.*')
            ->leftJoin('hr_as_basic_info as b','b.as_id','h.shift_roaster_user_id')
            ->where('h.shift_roaster_month','03')
            ->where('h.day_26','Friday Night')
            ->get()
            ->keyBy('as_id');

        $as = collect($data)->pluck('as_id')->toArray();


        $att = DB::table('hr_attendance_history')
                ->where('att_date', $dt)
                ->whereIn('as_id',$as)
                ->where('unit',8)
                ->get();

        $shift = DB::table('hr_shift')
                        ->where('hr_shift_name', 'Friday OT')
                        ->where('hr_shift_unit_id', 8)
                        ->where('ot_status', 1)
                        ->orderBy('hr_shift_id','DESC')
                        ->first();

        foreach ($att as $key => $value) {
            $this->extractSpecialOT($dt, $value->raw_data, $data[$value->as_id], $shift);
        }
        
        return 'done';

    }

    public function extractSpecialOT($date, $time, $emp, $shift)
    {
        $start = $date." ".$shift->hr_shift_start_time;

        $in_time = Carbon::createFromFormat('Y-m-d H:i:s', $start);
        $in_time_begin = $in_time->copy()->subHours(2);
        $in_time_end   = $in_time->copy()->addHours(1);



        $end   = $date." ".$shift->hr_shift_end_time;
        $out_time = Carbon::createFromFormat('Y-m-d H:i:s', $end);
        $out_time_begin = $in_time_end->copy()->addSecond();
        $out_time_end   = $out_time->copy()->addHours(4);

       

        if($time > $in_time_begin && $time < $out_time_end ){

            $last_punch = DB::table('hr_att_special')
                            ->where([
                                'in_date' => $date,
                                'as_id' => $emp->as_id
                            ])
                            ->first();
            $dt = [];

            if($last_punch){
                // check in
                if(($in_time_begin <= $time &&  $in_time_end >= $time) && ($time <= $last_punch->in_time  || $last_punch->in_time == null)){
                    $dt['in_time'] = $time;
                    $last_punch->in_time = $time;
                }else if(($out_time_begin <= $time &&  $out_time_end >= $time) && ($time >= $last_punch->out_time || $last_punch->out_time == null )){
                    $dt['out_time'] = $time;
                    $last_punch->out_time = $time;
                }

                // update ot
                if($last_punch->in_time != null && $last_punch->out_time != null){
                    $dt['ot_hour'] = $this->fullot($last_punch->in_time, $start, $last_punch->out_time,  $shift->hr_shift_break_time);

                }
                if($dt){

                    DB::table('hr_att_special')
                        ->where('id',$last_punch->id)
                        ->where('as_id',$emp->as_id)
                        ->update($dt);
                }

            }else{
                $dt = array(
                    'in_date' => $date,
                    'as_id' => $emp->as_id,
                    'hr_shift_code' => $shift->hr_shift_code
                );
                if($in_time_begin <= $time &&  $in_time_end >= $time){
                    $dt['in_time'] = $time;
                }else if($out_time_begin <= $time &&  $out_time_end >= $time){
                    $dt['out_time'] = $time;
                }else{
                    return 0;
                }
                DB::table('hr_att_special')
                    ->insert($dt);
            }
            return 1;
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
    
    public function floorUpdate()
    {
        $data = [];

        foreach ($data as $key => $v) {
            DB::table('hr_as_basic_info')
                ->where('associate_id',$key)
                ->update(['as_floor_id' => $v['fl']]);
        }
    }
    
    public function reflectArear()
    {
        return '';
        $data = DB::table('hr_increment as i')
            ->select('i.*','b.as_id','b.as_unit_id')
            ->leftJoin('hr_as_basic_info as b','b.associate_id','i.associate_id')
            ->where('i.created_at', '>', '2021-04-20')
            ->where('i.effective_date', '>', '2020-12-31')
            ->where('i.effective_date', '<', '2021-04-01')
            ->get();
            
        $k = [];
        
        foreach($data as $key => $d){
            $arearMonths = 4 - date('n', strtotime($d->effective_date));

            $k[$d->associate_id] = $arearMonths;

           if($arearMonths > 0){
                $master = SalaryAdjustMaster::firstOrNew([
                    'associate_id' => $d->associate_id,
                    'month' => '04',
                    'year' => date('Y')
                ]);
                $master->save();
    
                $detail = new SalaryAdjustDetails();
                $detail->salary_adjust_master_id = $master->id;
                $detail->date                    = '2021-04-26';
                $detail->amount                  = ($d->increment_amount * $arearMonths);
                $detail->type                    = 3;
                $detail->save();
           }
           $tablename = get_att_table($d->as_unit_id);
           $queue = (new ProcessUnitWiseSalary($tablename, '04', 2021, $d->as_id, 30))
                            ->onQueue('salarygenerate')
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
            
            
        }
        return $k;

    }

}
