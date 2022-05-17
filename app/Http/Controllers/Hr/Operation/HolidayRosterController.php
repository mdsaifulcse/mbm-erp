<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Helpers\Custom;
use App\Helpers\EmployeeHelper;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Employee;
use App\Models\Hr\EmpType;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\Shift;
use App\Models\Hr\YearlyHolyDay;
use App\Repository\Hr\AttDataProcessRepository;
use App\Repository\Hr\SalaryRepository;
use Carbon\Carbon;
use DB,DataTables;
use Illuminate\Http\Request;

class HolidayRosterController extends Controller
{

    protected $salaryRepository;
    protected $attDataProcessRepository;

    public function __construct(SalaryRepository $salaryRepository, AttDataProcessRepository $attDataProcessRepository)
    {
        ini_set('zlib.output_compression', 1);
        $this->salaryRepository = $salaryRepository;
        $this->attDataProcessRepository = $attDataProcessRepository;
    }

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
        
        // return $months;
        return view('hr.operation.holiday_roster.index', compact('unit','month','months'));
	}
	/**
     * Show the form for listing resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
    	$unit = unit_by_id();
    	$designation = designation_by_id();
    	$section = section_by_id();
    	$subSection = subSection_by_id();
    	$floor = floor_by_id();
        $unitId = collect(unit_authorization_by_id())->pluck('hr_unit_id');
        $yearMonth = $request->year_month??date('Y-m');
        $startDate = date('Y-m-d', strtotime($yearMonth.'-01'));
        $endDate = date('Y-m-t', strtotime($startDate));

        $employeeData = DB::table('hr_as_basic_info');
        $employeeDataSql = $employeeData->toSql();

        $queryData = DB::table('holiday_roaster AS h')
        	->select('h.*', 'b.as_unit_id', 'b.as_designation_id', 'b.as_section_id', 'b.as_subsection_id', 'b.as_floor_id', 'b.as_name', 'b.as_oracle_code')
        	->whereBetween('h.date', [$startDate, $endDate])
        	->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
        	->whereIn('b.as_location', auth()->user()->location_permissions());
        $queryData->leftjoin(DB::raw('(' . $employeeDataSql. ') AS b'), function($join) use ($employeeData) {
            $join->on('h.as_id', 'b.associate_id')->addBinding($employeeData->getBindings());
        });

        $data = $queryData->orderBy('h.date', 'asc')->orderby('b.associate_id')->get();

        return Datatables::of($data)
            ->addIndexColumn()
            ->editColumn('hr_unit_name', function($data) use ($unit){
                return $unit[$data->as_unit_id]['hr_unit_short_name']??'';
            })
            ->editColumn('hr_designation_name', function($data) use ($designation){
                return $designation[$data->as_designation_id]['hr_designation_name']??'';
            })
            ->editColumn('hr_section_name', function($data) use ($section){
                return $section[$data->as_section_id]['hr_section_name']??'';
            })
            ->editColumn('hr_subsec_name', function($data) use ($subSection){
                return $subSection[$data->as_subsection_id]['hr_subsec_name']??'';
            })
            ->editColumn('hr_floor_name', function($data) use ($floor){
                return $floor[$data->as_floor_id]['hr_floor_name']??'';
            })
            ->editColumn('emp_id', function($data){
                return $data->as_id.' <br> '.$data->as_oracle_code;
            })
            ->editColumn('date', function($data){
                return date('d', strtotime($data->date));
            })
            ->editColumn('day', function($data){
                return date('D', strtotime($data->date));
            })
            ->addColumn('action', function ($data) {
                $button = '<div class="btn-group">';
                $button .= '<a class="btn btn-sm btn-outline-primary btn-round holiday-edit" data-toggle="tooltip" title="Edit Day" data-id="'.$data->id.'"  data-as_id="'.$data->as_id.'" data-type="'.$data->remarks.'" data-comment="'.$data->comment.'" data-ref-date="'.$data->reference_date.'" data-ref-comment="'.$data->reference_comment.'" data-holiday-type="'.$data->type.'" data-date="'.$data->date.'" data-unit="'.$data->as_unit_id.'"><i class="ace-icon fa fa-edit bigger-120"></i></a>
                    &nbsp;';
                $button .= '<a href='.url("hr/operation/holiday-roster-delete/$data->id").' class="btn btn-sm btn-outline-danger btn-round" onclick=\'return confirm("Are you sure you want to delete this record?");\' data-toggle="tooltip" title="Delete Day"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
                
                $button .= '</div>';
                return $button;
            })
            ->rawColumns(['hr_unit_name','hr_designation_name', 'hr_section_name','date','hr_subsec_name', 'hr_floor_name', 'as_name', 'emp_id', 'date', 'day', 'comment', 'remarks', 'action'])
            ->make(true);
    }

	public function create()
	{
		$employeeTypes  = EmpType::where('hr_emp_type_status', '1')->pluck('hr_emp_type_name', 'emp_type_id');
	    $unitList  = collect(unit_authorization_by_id())->pluck('hr_unit_name', 'hr_unit_id');
	    $locationList  = collect(location_by_id())->pluck('hr_location_name', 'hr_location_id');
	    $shiftList = Shift::where('hr_shift_status', 1)->pluck("hr_shift_name", "hr_shift_id");
	    $areaList = collect(area_by_id())->pluck('hr_area_name','hr_area_id');

	    return view('hr/operation/holiday_roster/create', compact('shiftList', 'employeeTypes', 'unitList', 'locationList','areaList'));
	}
	
    public function store(Request $request)
    {
    	$data['type'] = 'error';
    	$input = $request->all();

    	DB::beginTransaction();
	    $results = array();
	    try {
	        $assignDates = !empty($input['assignDates']) ? explode(',', $input['assignDates']): '';
	        foreach ($request->assigned as $associate_id) {
	          	if($assignDates != ''){
	            	$value = $this->employeeWiseRosterSave($associate_id, $request);
	            	$results = array_merge($results, $value);
	          	}
	        }

	        DB::commit();
	        $data['type'] = 'success';
	        $data['message'] = $results;
	        return $data;
	    } catch (Exception $e) {
	        DB::rollback();
	        $data['message'][] = $e->getMessage();
	        return $data;
	    }
    }

    public function employeeWiseRosterSave($associate_id, $params)
    {
    	$type = $params->type;
    	if($type == 'Substitute-Holiday'){
    		$type = 'Holiday';
    		$params->comment = 'Substitute';
    		if($params->holiday_type == 1){
    			$params->holiday_type = 4;
    		}else{
    			$params->holiday_type = 3;
    		}
    	}
    	$selectedDates = explode(',', $params->assignDates);
    	sort($selectedDates);
	    try {
	    	$results = array();
        	$getEmployee = Employee::select('as_id','shift_roaster_status', 'as_unit_id', 'as_ot')->where('associate_id', $associate_id)->first();

        	$yearMonth = date('Y-m', strtotime($selectedDates[0]));
            $lock['month'] = date('m', strtotime($yearMonth));
            $lock['year'] = date('Y', strtotime($yearMonth));
            $lock['unit_id'] = $getEmployee->as_unit_id;
            $lockActivity = monthly_activity_close($lock);
            if($lockActivity == 0){
		        foreach ($selectedDates as $selectedDate) {
		        	$flag = 1;
		        	if($getEmployee != null){

		        		$dayCheck = EmployeeHelper::employeeDateWiseStatus($selectedDate, $associate_id, $getEmployee->as_unit_id, $getEmployee->shift_roaster_status);
		        		if($type == 'OT' && $getEmployee->as_ot == 0){
	        				$type = 'Holiday';
	        			}
		        		if($type == 'Holiday'){
	        				if(in_array($dayCheck, ['open','OT'])){
	        					$flag = 0;
	        				}
	        			}elseif($type == 'General'){
	        				if(in_array($dayCheck, ['Holiday','OT'])){
	        					$flag = 0;
	        				}
	        			}else{
	        				if(in_array($dayCheck, ['Holiday','open'])){
	        					$flag = 0;
	        				}
	        			}
		        		
		        		if($flag == 0){
		        			$year = date('Y',strtotime($selectedDate));
					        $month = date('m',strtotime($selectedDate));
		        			// check shift employee and already holiday
		        			$exFlag = 0;
		        			if($getEmployee->shift_roaster_status == 0 && $type == 'Holiday'){
					        	$getDayStatus = YearlyHolyDay::getCheckUnitDayWiseHolidayStatusMulti($getEmployee->as_unit_id, date('Y-m-d', strtotime($selectedDate)), [0]);
		        				
					        	if($getDayStatus != null){
					        		
					        		DB::table('holiday_roaster')->where('date',$selectedDate)->where('as_id',$associate_id)->delete();
					        		$exFlag = 1;
					        	}
					        }
					        if($exFlag == 0){
					        	HolidayRoaster::updateOrCreate(
					        		[
					        			'as_id' => $associate_id,
					        			'date'  => $selectedDate
					        		],
					        		[
					        			'year'=>$year,
					             		'month'=>$month,
					             		'remarks'=>$type,
					             		'comment'=>$params->comment,
					             		'reference_comment'=>$params->reference_comment,
					             		'reference_date'=>$params->reference_date,
					             		'type'=>$params->holiday_type,
					             		'status'=>1
					        		]
					        	);
					        	
					        }
		        			

				          	$today = date('Y-m-d');
				          	$yearMonth = $year.'-'.$month;
				          	if($today >= $selectedDate){
				            	// if type holiday then employee absent delete
				            	if($type == 'Holiday'){
				              		$getStatus = EmployeeHelper::employeeAttendanceAbsentDelete($associate_id, $selectedDate);
				            	}

				            	if($type == 'General'){
					              $getStatus = EmployeeHelper::employeeDayStatusCheckActionAbsent($associate_id, $selectedDate);
					              
					            }
				            	// if type OT then employee attendance OT count change
					            if($type == 'OT' || $type == 'General'){
					            	// re check attendance
              						$history = $this->attDataProcessRepository->attendanceReCallHistory($getEmployee->as_id, $selectedDate);
					              	// check exists attendance
					              	$undecr = DB::table('hr_attendance_undeclared')
						              ->where('as_id', $getEmployee->as_id)
						              ->where('punch_date', $selectedDate)
						              ->update([
						              	'flag' => 1
						            ]);
					            }
					              	
				              	$tableName = get_att_table($getEmployee->as_unit_id);
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
				          	$results[] = $associate_id.' - '.$selectedDate.' - '.$type.' - Assign Successfully ';
		        		}else{
		        			$results[] = $associate_id.' - '.$selectedDate.' - Already '.$type;
		        		}
		        	}
			          	
		        }
		    }else{
		    	$results[] = 'Monthly salary has been locked!';
		    }

	        return $results;
	    } catch (\Exception $e) {
	        
	        $bug = $e->getMessage();
	        // return $bug;
	        return ["error"];
	    }

    }

    public function update(Request $request, $id)
    {
    	$data['type'] = 'error';
    	$input = $request->all();

    	DB::beginTransaction();
	    try {
	    	$holiday = HolidayRoaster::findOrFail($id);

	    	$getEmployee = Employee::select('as_unit_id', 'as_id')->where('associate_id', $holiday->as_id)->first();
	    	$yearMonth = date('Y-m', strtotime($input['date']));
            $lock['month'] = date('m', strtotime($yearMonth));
            $lock['year'] = date('Y', strtotime($yearMonth));
            $lock['unit_id'] = $getEmployee->as_unit_id;
            $lockActivity = monthly_activity_close($lock);
            if($lockActivity == 0){
            	if($input['remarks'] == 'Substitute-Holiday'){
		    		$input['remarks'] = 'Holiday';
		    		$input['comment'] = 'Substitute';
		    		if(isset($input['type']) && $input['type'] == 1){
		    			$input['type'] = 4;
		    		}else{
		    			$input['type'] = 3;
		    		}
		    	}
                unset($input['_method']);
		    	$holiday->update($input);
		    	// date change
		    	if($input['date'] != $holiday->date){
		    		// old date attendance re call
		    		$this->attDataProcessRepository->attendanceReCallHistory($getEmployee->as_id, $holiday->date);
		    	}

		    	// remark change
		    	if($input['remarks'] != $holiday->remarks){
		    		// new date attendance re call
		    		$this->attDataProcessRepository->attendanceReCallHistory($getEmployee->as_id, $input['date']);
		    	}

		    	
		    	$message = 'Successfully Updated';
            }else{
            	$message = 'Monthly activity lock';
            }

	        DB::commit();
	        $data['type'] = 'success';
	        $data['message'] = $message;
	        return $data;
	    } catch (Exception $e) {
	        DB::rollback();
	        $data['message'] = $e->getMessage();
	        return $data;
	    }
    }

    public function undecrlarEmployee(Request $request)
    {
    	$data['type'] = 'error';
    	$input = $request->all();

    	DB::beginTransaction();
	    $results = array();
	    try {
	        $assignDates = !empty($input['assignDates']) ? explode(',', $input['assignDates']): '';

	        foreach ($request->assigned as $associate_id) {

	          	if($assignDates != ''){
	            	$value = $this->employeeWiseRosterSave($associate_id, $request);
	            	$results = array_merge($results, $value);
	          	}

	        }

	        DB::commit();
	        $data['type'] = 'success';
	        $data['message'] = $results;
	        return $data;
	    } catch (Exception $e) {
	        DB::rollback();
	        $data['message'][] = $e->getMessage();
	        return $data;
	    }
    }

    public function rosterSave($value='')
    {
    	// code...
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
            $holiday = HolidayRoaster::findOrFail($id);
            $check['month'] = $holiday->month;
            $check['year'] = $holiday->year;
            $check['unit_id'] = $holiday->employee->as_unit_id;
            if(monthly_activity_close($check) == 1){
                $data['message'] = date('Y-m', strtotime($holiday->hr_yhp_dates_of_holidays)).' monthly activity close!';
                DB::rollback();
                return $data;
            }
            $holiday->delete();
            // 
            $totalDay = date('t', strtotime($holiday->date));
            $data = $this->salaryRepository->employeeMonthlySalaryProcess($holiday->employee->as_id, $holiday->month, $holiday->year, $totalDay);
            DB::commit();
            toastr()->success('Successfully delete record');
            return back();
        } catch (\Exception $e) {
            DB::rollback();
            toastr()->error($e->getMessage());
            return back();
        }
    }
}
