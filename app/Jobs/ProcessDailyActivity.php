<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\Hr\EarnedLeave;
use App\Repository\Hr\AttendanceRepository;
use App\Repository\Hr\LeaveRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Packages\QueryExtra\QueryExtra;

use DB;

class ProcessDailyActivity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->processEarnLeave();
    }



    public function processEarnLeave()
    {
        $year          = date('Y');
        $employees     = Employee::getActiveEmployeeAsId();
        $employeeAtt   = $this->getThisYearAttendance(collect($employees)->keys());




        $previousLeave = $this->getEarnedLeaveBalance();

        $insert = [];

        $update = [];

        foreach($employeeAtt as $key => $att){
            if(isset($employees[$key])){
                $associateId = $employees[$key];

                $EL = round($att/18, 2);

                // check previous record 
                if(isset($previousLeave[$associateId])){
                    // organise data for bulk update
                    $update[$associateId] = [
                        'data'  => [
                            'earned' => $EL
                        ],
                        'keyval' => $previousLeave[$associateId]
                    ];
                }else{
                    // bulk insert
                    $insert[$associateId] = [
                        'associate_id' => $associateId,
                        'leave_year' => $year,
                        'carried' => 0,
                        'earned' => $EL,
                        'enjoyed' => 0
                    ];
                }
            }

        }

        // bulk update query
        DB::table('error')->insert(['msg' => count($update).'-'.count($insert)]);

        $update = collect($update)->chunk(200)->toArray();

        foreach($update as $key => $up){
            (new QueryExtra)
                ->table('hr_earned_leave') 
                ->whereKey('id')  
                ->bulkup($up); 
        }

        // bulk insett
        $insert = collect($insert)->chunk(200)->toArray();

        foreach($insert as $key => $ins){
            DB::table('hr_earned_leave')->insertOrIgnore($ins);
        }

    }

    protected function getThisYearAttendance($employees)
    {
        $attTable = collect(attendance_table())->unique();

        
        $query = DB::table(collect($attTable)->first())
            ->where('in_date','>=', date('Y-01-01'))
            ->select(DB::raw('count(*) as present'),'as_id')
            ->whereIn('as_id', $employees)
            ->groupBy('as_id');


        // union all atttendance table
        foreach($attTable as $key => $table){
            if(collect($attTable)->first() != $table){
                $q = DB::table($table)
                    ->select(DB::raw('count(*) as present'),'as_id')
                    ->where('in_date','>=', date('Y-01-01'))
                    ->whereIn('as_id', $employees)
                    ->groupBy('as_id');
                $query->union($q);
            }
        }

        // fetch and return employees with present count from query
        return $query->get()
                ->pluck('present','as_id');
    }

    protected function getEarnedLeaveBalance()
    {

        return EarnedLeave::where('leave_year', date('Y'))
                ->pluck('id','associate_id')
                ->toArray();
    }


}
