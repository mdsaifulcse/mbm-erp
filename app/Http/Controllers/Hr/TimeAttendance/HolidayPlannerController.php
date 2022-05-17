<?php

namespace App\Http\Controllers\Hr\TimeAttendance;

use App\Http\Controllers\Controller;
use App\Models\Hr\YearlyHolyDay;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables, DB, Auth;
class HolidayPlannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $months = [];
        $unit = collect(unit_authorization_by_id())->pluck('hr_unit_name');
        $month = $request->year_month??date('Y-m');
        $date = Carbon::parse($month);
        $now = Carbon::now();
        if($date->diffInMonths($now) <= 6 ){
            $max = Carbon::now()->addMonth(3);
        }else{
            $max = $date->addMonths(6);
            $months[date('Y-m')] = 'Current';
        }

        for ($i=1; $i <= 12 ; $i++) { 
            $months[$max->format('Y-m')] = $max->format('M, y');
            $max = $max->subMonth(1);
        }
        if($request->view && $request->view == 'calendar'){
            $unitHoliday = collect($this->getDateRangeWisePlanner($month))->groupBy('hr_yhp_unit');
            $defaultDate = date('Y-m-d', strtotime($month.'-01'));
            $unit = collect(unit_authorization_by_id())->pluck('hr_unit_name', 'hr_unit_id');
            return view('hr.operation.holiday_planner.calendar', compact('unit','month','months', 'unitHoliday', 'defaultDate'));
        }
        // return $months;
        return view('hr.operation.holiday_planner.index', compact('unit','month','months'));
    }

    public function getDateRangeWisePlanner($yearMonth='', $unit='')
    {
        $yearMonth = $yearMonth??date('Y-m');
        $startDate = date('Y-m-d', strtotime($yearMonth.'-01'));
        $endDate = date('Y-m-t', strtotime($startDate));
        if($unit == ''){
            $unitId = collect(unit_authorization_by_id())->pluck('hr_unit_id');
        }else{
            $unitId[] = $unit;
        }
        
        $data = YearlyHolyDay::
            whereBetween('hr_yhp_dates_of_holidays', [$startDate, $endDate])
            ->whereIn('hr_yhp_unit', $unitId)
            ->orderBy('hr_yhp_dates_of_holidays', 'asc')
            ->get();
        return $data;
    }

    /**
     * Show the form for listing resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $unit = unit_by_id();
        $data = $this->getDateRangeWisePlanner($request->year_month);

        return Datatables::of($data)
            ->addIndexColumn()
            ->editColumn('hr_unit_name', function($data) use ($unit){
                return $unit[$data->hr_yhp_unit]['hr_unit_name']??'';
            })
            ->editColumn('open_status', function($data){
                $status = '';
                if($data->hr_yhp_open_status == 1){
                    $status = 'General';
                }elseif($data->hr_yhp_open_status == 2){
                    $status = 'OT';
                }else{
                    $status = 'Holiday';
                }
                return $status;
            })
            ->addColumn('action', function ($data) {
                $button = '<div class="btn-group">';
                $button .= '<a href='.url("hr/operation/holiday-planner/$data->hr_yhp_id/edit").' class="btn btn-sm btn-outline-primary btn-round" data-toggle="tooltip" title="Edit Day"><i class="ace-icon fa fa-edit bigger-120"></i></a>
                    &nbsp;';
                if($data->flag == '1'){
                    $button .= '<a href='.url("hr/operation/holiday-planner-delete/$data->hr_yhp_id").' class="btn btn-sm btn-outline-danger btn-round" onclick=\'return confirm("Are you sure you want to delete this record?");\' data-toggle="tooltip" title="Delete Day"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
                }
                $button .= '</div>';
                return $button;
            })
            ->rawColumns(['hr_unit_name','open_status', 'action','date','reference_comment', 'reference_date'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $unitList  = collect(unit_authorization_by_id())->pluck('hr_unit_short_name', 'hr_unit_id');

        return view('hr.operation.holiday_planner.create', compact('unitList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data['type'] = 'error';
        $input = $request->all();
        $totalUnit = count($input['unit']);
        if($totalUnit == 0){
            $data['message'] = 'Unit not select';
            return $data;
        }

        DB::beginTransaction();
        try {
            $anotherDate = [];
            if(isset($input['another_date'])){
                $anotherDate = array_filter($input['another_date']);
            }
            $holidays = [];
            $remaks = [];
            if(isset($input['hr_yhp_dates_of_holidays'])){
                $holidays = $input['hr_yhp_dates_of_holidays'];
                $remaks = $input['hr_yhp_comments'];
            }
            
            $totalWeekend = count($holidays);
            $totalOtherWeekend = count($anotherDate);
            // total date merge
            
            $weekDays = array_combine($holidays, $remaks);
            // return $weekDays;
            if($totalOtherWeekend > 0){
                $holidays = array_merge($holidays, $anotherDate);
            }
            sort($holidays);
            $holidays = collect($holidays)->groupBy(function($q, $key){
                return date('Y-m', strtotime($q));
            });
            
            for ($u=0; $u < $totalUnit; $u++) { 
                $unitId = $input['unit'][$u];
                // check activity 
                foreach ($holidays as $key => $dates) {
                    $check['month'] = date('m', strtotime($key));
                    $check['year'] = date('Y', strtotime($key));
                    $check['unit_id'] = $unitId;
                    if(monthly_activity_close($check) == 1){
                        $data['message'] = $key.' monthly activity close!';
                        DB::rollback();
                        return $data;
                    }
                }

                // weekend
                for ($w=0; $w < $totalWeekend; $w++) {
                    $weekendDate = $input['hr_yhp_dates_of_holidays'][$w];
                    $holidayType = 1;
                    if(isset($input['holiday_type'][$weekendDate])){
                        $holidayType = 2;
                    }
                    $weekendData = [
                        'hr_yhp_unit' => $unitId,
                        'hr_yhp_dates_of_holidays' => $weekendDate,
                        'hr_yhp_comments' => $input['hr_yhp_comments'][$w],
                        'hr_yhp_status' => 1,
                        'hr_yhp_open_status' => 0,
                        'flag' => 0,
                        'holiday_type' => $holidayType,
                        'created_by' => auth()->user()->id
                    ];
                    YearlyHolyDay::insertOrIgnore($weekendData);
                }

                // other weekend
                for ($o=0; $o < $totalOtherWeekend; $o++) { 
                    $otherDate = $input['another_date'][$o];
                    $otherName = $input['name'][$o];
                    if(($otherDate != null && $otherDate != '') && ($otherName != null && $otherName != '') )
                    {
                        $otherData = [
                            'hr_yhp_unit' => $unitId,
                            'hr_yhp_dates_of_holidays' => $otherDate,
                            'hr_yhp_comments' => $otherName,
                            'hr_yhp_status' => 1,
                            'hr_yhp_open_status' => 0,
                            'reference_comment' => $input['ref_comment'][$o],
                            'reference_date' => $input['ref_date'][$o],
                            'flag' => 1,
                            'holiday_type' => $input['another_type'][$o],
                            'created_by' => auth()->user()->id
                        ];
                        YearlyHolyDay::insertOrIgnore($otherData);
                    }
                }
            }
            
            DB::commit();
            $data['type'] = 'success';
            $data['message'] = 'Successfully save holiday';
            $data['url'] = url('/hr/operation/holiday-planner');
            return $data;
        } catch (\Exception $e) {
            DB::rollback();
            $data['message'] = $e->getMessage();
            return $data;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $day = YearlyHolyDay::findOrFail($id);
            $check['month'] = date('m', strtotime($day->hr_yhp_dates_of_holidays));
            $check['year'] = date('Y', strtotime($day->hr_yhp_dates_of_holidays));
            $check['unit_id'] = $day->hr_yhp_unit;
            if(monthly_activity_close($check) == 1){
                $message = date('Y-m', strtotime($day->hr_yhp_dates_of_holidays)).' monthly activity close!';
                toastr()->error($message);
                return back();
            }
            // check permission
            if(unit_authorization_check($day->hr_yhp_unit) == 'no'){
                toastr()->error("Edit Access Permission Denied");
                return back();
            }
            // month close or not 
            $check['unit_id'] = $day->hr_yhp_unit;
            $check['month'] = date('m', strtotime($day->hr_yhp_dates_of_holidays));
            $check['year'] = date('Y', strtotime($day->hr_yhp_dates_of_holidays));
            if(monthly_activity_close($check) == 1){
                toastr()->error("This month activity close");
                return back();
            }

            return view('hr.operation.holiday_planner.edit', compact('day'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data['type'] = 'error';
        $input = $request->all();
        DB::beginTransaction();
        try {
            $holiday = YearlyHolyDay::findOrFail($id);
            $check['month'] = date('m', strtotime($holiday->hr_yhp_dates_of_holidays));
            $check['year'] = date('Y', strtotime($holiday->hr_yhp_dates_of_holidays));
            $check['unit_id'] = $holiday->hr_yhp_unit;
            if(monthly_activity_close($check) == 1){
                $data['message'] = date('Y-m', strtotime($holiday->hr_yhp_dates_of_holidays)).' monthly activity close!';
                DB::rollback();
                return $data;
            }
            $input['updated_by'] = auth()->user()->id;
            $holiday->update($input);
            DB::commit();
            $data['type'] = 'success';
            $data['message'] = 'Successfully save holiday';
            $data['url'] = url('/hr/operation/holiday-planner');
            return $data;
        } catch (\Exception $e) {
            DB::rollback();
            $data['message'] = $e->getMessage();
            return $data;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data['type'] = 'error';
        DB::beginTransaction();
        try {
            $holiday = YearlyHolyDay::findOrFail($id);
            $check['month'] = date('m', strtotime($holiday->hr_yhp_dates_of_holidays));
            $check['year'] = date('Y', strtotime($holiday->hr_yhp_dates_of_holidays));
            $check['unit_id'] = $holiday->hr_yhp_unit;
            if(monthly_activity_close($check) == 1){
                $data['message'] = date('Y-m', strtotime($holiday->hr_yhp_dates_of_holidays)).' monthly activity close!';
                DB::rollback();
                return $data;
            }
            $holiday->delete();
            DB::commit();
            toastr()->success('Successfully delete record');
            return back();
        } catch (\Exception $e) {
            DB::rollback();
            toastr()->error($e->getMessage());
            return back();
        }
    }

    public function getDayWiseDate(Request $request)
    {
        $input = $request->all();
        $data['type'] = 'error';
        try {
            if(!isset($request->weekdays) || count($request->weekdays) == 0){
                $data['message'] = 'No Day Selected!';
                return $data;
            }

            $weekends= $request->weekdays;
            if($input['type'] == 'yearly'){
                $yearAllMonth = '';
                $start = 01;
                if($input['year'] == date('Y')){
                    $start = date('m');
                }
                for ($i=$start; $i <= 12; $i++) { 
                    $yearMonth = $input['year'].'-'.$i;
                    $yearMonth = date('Y-m', strtotime($yearMonth));
                    $yearAllMonth .= $this->monthWiseDate($yearMonth, $weekends);
                }
                $process = $yearAllMonth;
            }else{
                $yearMonth = date('Y-m', strtotime($input['year_month']));
                $process = $this->monthWiseDate($yearMonth, $weekends);
            }
            $data['type'] = 'success';
            $data['value'] = $process;
            return $data;
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();
            return $data;
        }
        
    }

    public function monthWiseDate($yearMonth, $weekends)
    {
        $month = date('m', strtotime($yearMonth));
        $year = date('Y', strtotime($yearMonth));
        $endDate = date('t', strtotime($yearMonth.'-01'));
        $weekends= $weekends;
        
        if(isset($weekends) && count($weekends) > 0){
            $totalDay = count($weekends);
            $dayDates = [];
            for ($i = 1; $i <= $endDate; $i++) {
                $date = $year.'/'.$month.'/'.$i;
                $date= date('Y-m-d', strtotime($date));
                $get_name = date('l', strtotime($date));
                $dayDates[$get_name][] = $date; 
            }
            return view('hr.operation.holiday_planner.dates', compact('totalDay', 'weekends', 'dayDates', 'yearMonth'))->render();
        }
        return '';
    }
}
