<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Helpers\EmployeeHelper;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessAttendanceOuttime;
use App\Models\Employee;
use App\Models\Hr\Shift;
use App\Models\Hr\ShiftRoaster;
use App\Repository\Hr\AttDataProcessRepository;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class ShiftRosterController extends Controller
{
    protected $attDataProcessRepository;

    public function __construct(AttDataProcessRepository $attDataProcessRepository)
    {
        ini_set('zlib.output_compression', 1);
        $this->attDataProcessRepository = $attDataProcessRepository;
    }

    public function assignMulti(Request $request)
    {
    	$input = $request->all();
    	$data['type'] = 'error';
    	DB::beginTransaction();
    	try {
    		$shift = $input['target_shift'];
            if (isset($request->month)) {
                 $year = date('Y', strtotime($request->month));
                 $month = date('n', strtotime($request->month));
                 $m = date('Y-m', strtotime($request->month));
             } else{
                $year = date('Y');
                $month = date('n');
                $m = date('Y-m');
             }
    		foreach ($input['associate'] as $key => $ass_id) {
                $emp = DB::table('hr_as_basic_info')
                        ->where('associate_id',$ass_id)
                        ->first();
                // check lock month
                $checkL['month'] = $month;
                $checkL['year'] = $year;
                $checkL['unit_id'] = $emp->as_unit_id;
                $checkLock = monthly_activity_close($checkL);
                if($checkLock == 0){
                    $att_table = get_att_table($emp->as_unit_id);
                    for($j=$input['start_day']; $j<=$input['end_day']; $j++)
                    {
                        $day= "day_".$j;
                        $roster = ShiftRoaster::where('shift_roaster_associate_id', $ass_id)
                        ->where('shift_roaster_year', $year)
                        ->where('shift_roaster_month', $month)
                        ->first();

                        
                        if($roster != null){
                            $roster->update([$day => $shift]);
                            $getId = $roster->shift_roaster_id;
                        }else{
                            $getBasic = Employee::getEmployeeAssociateIdWise($ass_id);
                            $getId = ShiftRoaster::create([
                                'shift_roaster_associate_id' => $ass_id,
                                'shift_roaster_user_id' => $getBasic->as_id,
                                'shift_roaster_year' => $year,
                                'shift_roaster_month' => $month,
                                $day => $shift
                            ])->shift_roaster_id;

                        }

                        $date = date('Y-m-d', strtotime($m.'-'.$j));
                        
                        $this->attDataProcessRepository->attendanceReCallHistory($emp->as_id, $date);
                        // if($date <= date('Y-m-d')){
                        //     $att = DB::table($att_table)->where('as_id',$emp->as_id)->where('in_date',$date)->whereNotNull('out_time')->whereNotNull('in_time')->where('remarks', '!=', 'DSI')->first();
                        //     if($att){

                        //         $queue = (new ProcessAttendanceOuttime($att_table, $att->id, $emp->as_unit_id))
                        //             ->delay(Carbon::now()->addSeconds(2));
                        //             dispatch($queue);
                        //     }
                        // }

                        log_file_write("Shift Roster Day Wise Updated", $getId);
                    }
                }
                
            }
            DB::commit();
            $data['type'] = 'success';
            $data['message'] = "Shift Roster Assign Successfully Done";
    		return $data;
    	} catch (\Exception $e) {
    		DB::rollback();
    		$data['message'] = $e->getMessage();
    		return $data;
    	}
    }

    public function singleDateAssign(Request $request)
    {
        $input = $request->all();
        $data['type'] = 'error';
        try {
            $year = date('Y', strtotime($input['date']));
            $month = date('n', strtotime($input['date']));
            $day= date('j', strtotime($input['date']));
            $day= "day_".$day;
            $employee = Employee::getEmployeeAssociateIdWise($input['associateid']);
            $shift = Shift::getCheckUniqueUnitIdShiftName($employee->as_unit_id, $input['shift']);
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
                    $roster->update([$day => $input['shift']]);
                    $getId = $roster->shift_roaster_id;
                }else{
                    $getId = ShiftRoaster::create([
                        'shift_roaster_associate_id' => $input['associateid'],
                        'shift_roaster_user_id' => $input['as_id'],
                        'shift_roaster_year' => $year,
                        'shift_roaster_month' => $month,
                        $day => $input['shift']
                    ])->shift_roaster_id;
                }
                log_file_write("Shift Roster Day Wise Updated", $getId);

                $data['value'] = $this->attDataProcessRepository->attendanceReCallHistory($input['as_id'], $input['date']);
            }
            $data['type'] = 'success';
            $data['msg'] = 'Successfully Change Shift';
            return $data;
        } catch (\Exception $e) {
            $data['msg'] = $e->getMessage();
            return $data;
        }
    }
}
