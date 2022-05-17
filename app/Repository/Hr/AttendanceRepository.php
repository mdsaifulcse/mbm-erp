<?php

namespace App\Repository\Hr;

use App\Exports\Hr\AttendanceSummaryExport;
use App\Models\Employee;
use App\Models\Hr\Absent;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\Leave;
use App\Models\Hr\Shift;
use App\Repository\Hr\EmployeeRepository;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Repository\Hr\ShiftRepository;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use DB; 

class AttendanceRepository 
{
	protected $employeeRepository;

	protected $shiftRepository;

	protected $employees;

	protected $associate;

	protected $runningShift;

	protected $date;

	protected $unit;

	public function __construct(EmployeeRepository $employeeRepository, ShiftRepository $shiftRepository)
	{
		$this->shiftRepository 	  = $shiftRepository;
		$this->employeeRepository = $employeeRepository;
		ini_set('zlib.output_compression', 1);
	}

	protected function setDate($date)
	{
		$this->date = isset($date)?$date:date('Y-m-d');
	}


	protected function getEmployees($request)
	{
		$this->employees 	  = $this->shiftRepository->getShiftEmployeeByDate($request, $this->date);

		if(isset($request['shift'])){
			$filterShift = $request['shift'];
			$this->employees = collect($this->employees)->filter(function($q) use($filterShift){
				return in_array($q->as_shift_id, $filterShift);
			});
		}
	}
	/*  List of Operators
		--------------------------
	    138	- OPERATOR
		181	- LIFT OPERATOR
		187	- DATA ENTRY OPERATOR
		191	- BOIL & GEN. OPERATOR
		196	- LAB OPERATOR
		214	- BUTTON OPERATOR
		216	- REPAIRING OPERATOR
		219	- FINISHING OPERATOR
		229	- WASHING OPERATOR
		231	- DRYER OPERATOR
		306	- SPECIAL OPERATOR
		343	- DRY PROCESS OPERATOR
		349	- WASHING OPERATOR
		351	- DRY PROCESS OPERATOR
		353	- FINISHING OPERATOR
		356	- SAMPLE OPERATOR
		378 - LABEL JOIN OPERATOR
	*/
	protected function getOperator()
	{
		return [138,214,219,229,231,306,343,349,353,351,353,356,378];
	}


	public function getCurrentShiftForSelect($request = [])
	{
		$this->setDate($request['date']??null);
		$input = [
			'unit' => [],
			'location' => []
		];

		$this->getEmployees($input);

		$untraced = [];

		return collect($this->employees)
				->pluck('as_shift_id')
				->unique()
				->sort()
				->toArray();
	}



	public function getSummary($request)
	{

		$this->setDate($request['report_date']??null);
		$this->unit   = $request['unit'];

		$date = $this->date;
		$this->getEmployees($request);

		$untraced = [];

		$allShift = collect($this->employees)
				->pluck('as_shift_id')
				->unique()
				->toArray();
        
		if($this->date == date('Y-m-d')){

			$runningShift = $this->shiftRepository->getCurrentShift($this->date);

			$runningShift = collect($allShift)
				->intersect($runningShift)
				->toArray();

			$untraced = collect($allShift)
				->diff($runningShift)
				->unique()
				->toArray();

		}else{
			 $runningShift = $allShift;
		}		

		$att = $this->getEmployeeAttendanceStatus($untraced);

		// get operator

		$operator = $this->getOperator();

		$presentOp = collect($att)->whereIn('as_subsection_id', $operator)->where('status', 'P')->count();

		if(isset($request['report_format'])){

			// if status exist filter
			if(isset($request['status'])){
				$filterStatus = $request['status'];
				$att = collect($att)->filter(function($q) use ($filterStatus){
					return $q->status == $filterStatus;
				});
			}

			if(isset($request['export'])){
				$data['att'] = $att;
				return  Excel::download(new AttendanceSummaryExport($data, 'employee'), 'attendance-summary-employee-'.$date.'.xlsx');
			}
			return view('hr.reports.attendance.employee_view',compact('att','date','request'))->render();
		}

		$att = collect($att)->sortByDesc('as_ot')->groupBy('as_ot', true);



		$attSummary = $this->makeAttSummary($att);

		$attDetails = $this->makeGroupedAttendanceBySubsection($att);

		if(isset($request['export'])){
			$data['attDetails'] = $attDetails;
			return  Excel::download(new AttendanceSummaryExport($data, 'summary'), 'attendance-summary-'.$date.'.xlsx');
		}


		return view('hr.reports.attendance.summary',compact('attSummary','attDetails','runningShift', 'request','date', 'allShift','presentOp'))->render();
		
		return $response;

	}

    public function getAbsentismSummer($request)
	{

		$monthYear = '2021-09-';
		$totalDay  = date('t', strtotime($monthYear.'01'));
		$excel = [];
		$designation = designation_by_id();
		$department = department_by_id();
		$floors = floor_by_id();
		$section = section_by_id();
		for ($i=1; $i <= 27; $i++) { 
			$d = $monthYear.$i;
			$this->setDate($d??null);
			$this->unit   = $request['unit'];

			$date = $this->date;
			$this->getEmployees($request);

			$untraced = [];

			$allShift = collect($this->employees)
					->pluck('as_shift_id')
					->unique()
					->toArray();

			if($this->date == date('Y-m-d')){

				$runningShift = $this->shiftRepository->getCurrentShift($this->date);

				$runningShift = collect($allShift)
					->intersect($runningShift)
					->toArray();

				$untraced = collect($allShift)
					->diff($runningShift)
					->unique()
					->toArray();

			}else{
				 $runningShift = $allShift;
			}		

			$att = $this->getEmployeeAttendanceStatus($untraced);

			// get operator

			$operator = $this->getOperator();

			$presentOp = collect($att)->whereIn('as_subsection_id', $operator)->where('status', 'P')->count();

			if(isset($request['report_format'])){

				// if status exist filter
				if(isset($request['status'])){
					$filterStatus = $request['status'];
					$att = collect($att)->filter(function($q) use ($filterStatus){
						return $q->status == $filterStatus;
					});
				}

				if(isset($request['export'])){
					$data['att'] = $att;
					return  Excel::download(new AttendanceSummaryExport($data, 'employee'), 'attendance-summary-employee-'.$date.'.xlsx');
				}
				return view('hr.reports.attendance.employee_view',compact('att','date','request'))->render();
			}


			$att = collect($att);

			// dd($att[1]);

			//$attSummary = $this->makeAttSummary($att);
			//$attchunk = collect($att)->chunk(200);
			// dd($attchunk[0]);
			
// 			foreach($attchunk as $a){
// 				foreach($a as $e){
// 					$excel[] = [
// 						'Date' => $date,
// 						'Associate Id' => $e->associate_id,
// 						'Oracle Id' => $e->as_oracle_code,
// 						'OT Status' => $e->as_ot==1?'OT':'Non-OT',
// 						'Day Status' => $e->status,
// 						'Designation Name' => $designation[$e->as_designation_id]['hr_designation_name']??'',
// 						'Department Name' => $department[$e->as_department_id]['hr_department_name']??'',
// 						'Section Name' => $section[$e->as_section_id]['hr_section_name']??''
// 					];
// 				}
// 			}
			
			$attDetails = $this->makeGroupedAttendanceByDate($att);
			// dd($attDetails[0]);
			

			

			//dd($attDetails, $floors);

			$ot = isset($attDetails[1])?$attDetails[1]:null;
			$nonot = isset($attDetails[0])?$attDetails[0]:null;
			$excel[] = [
				'Date' => $date,
				'OT Employee' => $ot != null?$ot->t:0,
				'OT Absent' => $ot != null?$ot->a:0,
				'OT Percent' => $ot != null?$ot->a_per:0,
				'NonOT Employee' => $nonot != null?$nonot->t:0,
				'NonOT Absent' => $nonot != null?$nonot->a:0,
				'NonOT Percent' => $nonot != null?$nonot->a_per:0,
				'Total Employee' => collect($attDetails)->sum('t'),
				'Total Absent' => collect($attDetails)->sum('a'),
				'Total Percent' => round(collect($attDetails)->sum('a')/collect($attDetails)->sum('t')*100, 2)
			];
				
			
		}
		

		return (new FastExcel(collect($excel)))->download('attendance_report('.$monthYear.').xlsx');
	}
	public function getFloorWiseSummary($request)
	{
		$this->setDate($request['report_date']??null);
		$this->unit   = $request['unit'];

		$date = $this->date;
		$this->getEmployees($request);

		$untraced = [];

		$allShift = collect($this->employees)
				->pluck('as_shift_id')
				->unique()
				->toArray();

		if($this->date == date('Y-m-d')){

			$runningShift = $this->shiftRepository->getCurrentShift($this->date);

			$runningShift = collect($allShift)
				->intersect($runningShift)
				->toArray();

			$untraced = collect($allShift)
				->diff($runningShift)
				->unique()
				->toArray();

		}else{
			 $runningShift = $allShift;
		}		

		$att = $this->getEmployeeAttendanceStatus($untraced);

		// get operator

		$operator = $this->getOperator();

		$presentOp = collect($att)->whereIn('as_subsection_id', $operator)->where('status', 'P')->count();

		if(isset($request['report_format'])){

			// if status exist filter
			if(isset($request['status'])){
				$filterStatus = $request['status'];
				$att = collect($att)->filter(function($q) use ($filterStatus){
					return $q->status == $filterStatus;
				});
			}

			if(isset($request['export'])){
				$data['att'] = $att;
				return  Excel::download(new AttendanceSummaryExport($data, 'employee'), 'attendance-summary-employee-'.$date.'.xlsx');
			}
			return view('hr.reports.attendance.employee_view',compact('att','date','request'))->render();
		}


		$att = collect($att)->sortByDesc('as_floor_id')->groupBy('as_floor_id', true);



		//$attSummary = $this->makeAttSummary($att);

		$attDetails = $this->makeGroupedAttendanceByFloor($att);

		
		$excel = [];

		$department = department_by_id();
		$floors = floor_by_id();

		//dd($attDetails, $floors);

		foreach($attDetails as $key => $floor){
			foreach($floor as $key2 => $dept){
				$ot = isset($dept[1])?$dept[1]:null;
				$nonot = isset($dept[0])?$dept[0]:null;
				$excel[] = [
					'Department' => $department[$key2]['hr_department_name'],
					'Date' => $date,
					'Floor' => isset($floors[$key])?$floors[$key]['hr_floor_name']:'N/A',
					'OT Employee' => $ot != null?$ot->t:0,
					'OT Absent' => $ot != null?$ot->a:0,
					'OT Percent' => $ot != null?$ot->per:0,
					'NonOT Employee' => $nonot != null?$nonot->t:0,
					'NonOT Absent' => $nonot != null?$nonot->a:0,
					'NonOT Percent' => $nonot != null?$nonot->per:0,
					'Total Employee' => collect($dept)->sum('t'),
					'Total Absent' => collect($dept)->sum('a'),
					'Total Percent' => round(collect($dept)->sum('a')/collect($dept)->sum('t')*100, 2)
				];
			}
			
		}
		

		return (new FastExcel(collect($excel)))->download('attendance_summary'.$date.'.xlsx');
	}


	protected function makeGroupedAttendanceByFloor($att)
	{
		return collect($att)->map(function($floor){
			  return collect($floor)->groupBy('as_department_id', true)->map(function($ot){

					return collect($ot)->sortBy('as_ot')
						->groupBy('as_ot', true)
						->map(function($department){
							// group by status present, absent, leave, holiday
							return (object)[
								't' => collect($department)->count(),
								'p' => collect($department)->where('status', 'P')->count(),
								'a' => collect($department)->where('status', 'A')->count(),
								'h' => collect($department)->where('status', 'H')->count(),
								'l' => collect($department)->where('status', 'L')->count(),
								'u' => collect($department)->where('status', 'U')->count(),
								'per' => round(collect($department)->where('status', 'A')->count()/collect($department)->count()*100, 2)
							];

						});
				});

			});
	}
    protected function makeGroupedAttendanceByDate($att)
	{
		
			return collect($att)
				->groupBy('as_ot', true)
				->map(function($q){
					// group by status present, absent, leave, holiday
					return (object)[
						't' => collect($q)->count(),
						'p' => collect($q)->where('status', 'P')->count(),
						'a' => collect($q)->where('status', 'A')->count(),
						'h' => collect($q)->where('status', 'H')->count(),
						'l' => collect($q)->where('status', 'L')->count(),
						'u' => collect($q)->where('status', 'U')->count(),
						'a_per' => round(collect($q)->where('status', 'A')->count()/collect($q)->count()*100, 2),
						'p_per' => round(collect($q)->where('status', 'P')->count()/collect($q)->count()*100, 2)
					];

				});
	}
	protected function getEmployeeAttendanceStatus($untraced = [])
	{
		$this->associate = collect($this->employees)->pluck('associate_id', 'as_id')->toArray();

		$present = $this->getPresent($this->date, $this->unit); 	// get present by as_id

		//dd($present);
		$present = collect($present)->map(function($q, $i) {
			return $this->associate[$q];
		})->toArray();
		// get absent by associate_id
		$absent = $this->getAbsent($this->date)->toArray();
		// get holiday by associate_id	
		$holidayOT = $this->getHoliday($this->date);	
		// get leave by associate_id
		$leave = $this->getLeave($this->date)->toArray();	
		$otButHoliday = collect($holidayOT['ot'])->diff($present);
		$holiday    = collect($holidayOT['holiday'])->merge($otButHoliday)->toArray();


		return collect($this->employees)->map(function($q) use ($present, $absent, $leave, $holiday, $untraced){
				// update area
				if($q->as_area_id == 1){
					$q->as_area_id = 'Office';
				}else if($q->as_area_id == 2){
					$q->as_area_id = 'Factory';
				}else if($q->as_area_id == 3){
					$q->as_area_id = 'General Utilities';
				}

				// make present
				if(in_array($q->associate_id, $present)){
					$q->status = 'P';
				}else if(in_array($q->associate_id, $holiday)){
					$q->status = 'H';
				}else if(in_array($q->associate_id, $leave)){
					$q->status = 'L';
				}else if(in_array($q->as_shift_id, $untraced)){
					$q->status = 'U';
				}else{
					$q->status = 'A';
				}
				return $q;
			});
	}


	protected function makeAttSummary($att)
	{
		return collect($att)->map(function($q){
			return (object)[
				't' => collect($q)->count(),
				'p' => collect($q)->where('status', 'P')->count(),
				'a' => collect($q)->where('status', 'A')->count(),
				'h' => collect($q)->where('status', 'H')->count(),
				'l' => collect($q)->where('status', 'L')->count(),
				'u' => collect($q)->where('status', 'U')->count(),
			];
		});		
	}

	protected function makeGroupedAttendanceBySubsection($att)
	{
		return collect($att)->map(function($ot){
				return collect($ot)->sortBy('as_area_id')->groupBy('as_area_id', true)
					->map(function($area){
						// group by department
						return collect($area)->sortBy('as_department_id')
							->groupBy('as_department_id', true)
							->map(function($department){
								// group by section
								return collect($department)->sortBy('as_section_id')
									->groupBy('as_section_id', true)
									->map(function($section){
										// group by subsection
										return collect($section)->sortBy('as_subsection_id')
											->groupBy('as_subsection_id', true)
											->map(function($subsection){
												// group by status present, absent, leave, holiday
												return (object)[
													't' => collect($subsection)->count(),
													'p' => collect($subsection)->where('status', 'P')->count(),
													'a' => collect($subsection)->where('status', 'A')->count(),
													'h' => collect($subsection)->where('status', 'H')->count(),
													'l' => collect($subsection)->where('status', 'L')->count(),
													'u' => collect($subsection)->where('status', 'U')->count(),
												];
											});
									});

							});
					});

			});
	}


	public function getPresent($date, $unit)
	{
		$attTable = collect(attendance_table())
			->filter(function($q, $i) use ($unit){
				return in_array($i, $unit);
			})->unique();

		
		$query = DB::table(collect($attTable)->first())
			->select('as_id')
			->where('in_date', $date)
			->whereIn('as_id', collect($this->associate)->keys());


		// union all atttendance table
		foreach($attTable as $key => $table){
			if(collect($attTable)->first() != $table){
				$q = DB::table($table)
					->select('as_id')
					->where('in_date', $date)
					->whereIn('as_id', collect($this->associate)->keys());
				$query->union($q);
			}
		}

		// fetch and return employees from query
		return $query->pluck('as_id');
	}


	public function getAbsent($date)
	{
		return DB::table('hr_absent')
			->where('date', $date)
			->whereIn('associate_id', $this->associate)
			->pluck('associate_id');
	}


	public function getHoliday($date)
	{

		// get holidays form holiday roaster
		$roaster = $this->getHolidayRoaster($date);

		$holiday = collect($roaster)
			->where('remarks','Holiday')
			->pluck('as_id');

		$ot = collect($roaster)
			->where('remarks','OT')
			->pluck('as_id');

		// check is there any global holiday
		$holidayUnits = $this->getHolidayPlanner($date);
		if(count($holidayUnits) > 0){
			$shiftEmployee = collect($this->employees)
				->where('shift_roaster_status', 0)
				->whereIn('as_unit_id', $holidayUnits)
				->pluck('associate_id', 'as_id');

			$general = collect($roaster)
				->whereIn('as_id', $shiftEmployee )
				->where('remarks','General')
				->pluck('as_id');

			$holiday = collect($holiday)
				->merge($shiftEmployee) // merge global holiday employees
				->diff($general) // remove general employees
				->diff($ot)
				->unique();  
		}

		return [
			'ot' => $ot,
			'holiday' => $holiday
		];
	}

	public function getLeave($date)
	{
		return DB::table('hr_leave')
			->where('leave_from','<=', $date)
			->where('leave_to','>=', $date)
			->whereIn('leave_ass_id', $this->associate)
			->pluck('leave_ass_id');
	}


	public function getHolidayRoaster($date)
	{
		return DB::table('holiday_roaster')
			->select('remarks','as_id')
			->where('date', $date)
			->whereIn('as_id', $this->associate)
			->get()
			->keyBy('as_id');
	}

	public function getHolidayPlanner($date)
	{
		return DB::table('hr_yearly_holiday_planner')
			->where('hr_yhp_dates_of_holidays', $date)
			->where('hr_yhp_open_status', 0)
			->pluck('hr_yhp_unit');
	}


	// for single employee


	public function makeParams($employee, $startDate, $endDate)
    {
        return [
           'as_id'          => $employee->as_id,
           'associate_id'   => $employee->associate_id,
           'start_date'     => $startDate,
           'end_date'       => $endDate,
           'as_doj'         => $employee->as_doj->format('Y-m-d'),
           'shift_roaster_status' => $employee->shift_roaster_status,
           'as_unit_id'     => $employee->as_unit_id
        ];
    }


	/**
     * Handle an incoming request.
     *
     * @param  array  $params 
     * $params = [
     * 		'as_id' => 
     *      'associate_id' =>
     *      'start_date' =>
     *      'end_date' =>
     *      'as_doj' =>
     *      'shift_roaster_status' =>
     *      'as_unit_id' =>
     *  ];
     * 
     * @return array
     */

	public function getHolidays($employee, $startDate, $endDate)
	{
		// generates params array to fetch holidays
        $params = $this->makeParams($employee, $startDate, $endDate);


		$roaster = $this->getHolidayRoasterByEmployee(
						$params['associate_id'], 
						$params['start_date'], 
						$params['end_date']
					);

		$holidays = isset($roaster['Holiday'])?$roaster['Holiday']:[];
		$otDays   = isset($roaster['OT'])?$roaster['OT']:[];


		
		if($params['shift_roaster_status'] == 0){
			// get holiday planner data
			$globalHolidays = $this->getHolidayPlannerByUnit(
								$params['as_unit_id'], 
								$params['start_date'], 
								$params['end_date']
							);

			// get general duty
			$generalDays    = isset($roaster['General'])?$roaster['General']:[];


			$filterHolidays = collect($globalHolidays)->diff($generalDays)->toArray();
			$holidays = collect($holidays)->merge($filterHolidays)->unique();

			// get global OT
			$globalOT = $this->getOtPlannerByUnit(
								$params['as_unit_id'], 
								$params['start_date'], 
								$params['end_date']
							);
			$filterOtDays = collect($globalOT)->diff($generalDays)->toArray();

			$otDays = collect($otDays)->merge($filterOtDays);
			$holidays = collect($holidays)->merge($otDays)->unique();
			
		}
        
		// if otDays Exist check present
		if(count($otDays) > 0){
			$present = $this->getPresentDateByEmployee(
						$params['as_id'], 
						$params['as_unit_id'], 
						$params['start_date'], 
						$params['end_date']
					);
            $holidays = collect($holidays)->diff($present)->unique();
            return $holidays;
		}

		return $holidays;
	}


	/**
     * Get Holiday Roaster Information a single Employee
     *
     * @param  string  $associate_id 
     * @param  date    $start_date 
     * @param  date    $end_date 
     * @return array
     */

	public function getHolidayRoasterByEmployee($associate_id, $start_date, $end_date = null)
	{
		return HolidayRoaster::where('as_id', $associate_id)
				->select('date','remarks')
				->when($end_date != null, function($q) use ($start_date, $end_date) {
					$q->where('date','>=', $start_date)
					  ->where('date','<=', $end_date);
				})
				->when($end_date == null, function($q) use ($start_date){
					$q->where('date', $start_date);
				})
				->get()
				->groupBy('remarks', true)
				->map(function($q){
					return collect($q)->pluck('date');
				});
	}


	/**
     * Get present date of a single Employee
     *
     * @param  int     $as_id 
     * @param  int     $unit_id 
     * @param  date    $start_date 
     * @param  date    $end_date 
     * @return array
     */

	public function getPresentDateByEmployee($as_id, $unit_id, $start_date, $end_date = null)
	{
		$table = get_att_table($unit_id);

		return DB::table($table)
				->where('as_id', $as_id)
				->when($end_date != null, function($q) use ($start_date, $end_date) {
					$q->where('in_date','>=', $start_date)
					  ->where('in_date','<=', $end_date);
				})
				->when($end_date == null, function($q) use ($start_date){
					$q->where('in_date', $start_date);
				})
				->pluck('in_date');
	}


	/**
     * Get Holiday Planner date of a unit by given reange or date
     *
     * @param  string  $associate_id 
     * @param  date    $start_date 
     * @param  date    $end_date 
     * @return array
     */

	public function getHolidayPlannerByUnit($unit_id, $start_date, $end_date = null)
	{
		return DB::table('hr_yearly_holiday_planner')
				->when($end_date != null, function($q) use ($start_date, $end_date) {
					$q->where('hr_yhp_dates_of_holidays','>=', $start_date)
					  ->where('hr_yhp_dates_of_holidays','<=', $end_date);
				})
				->when($end_date == null, function($q) use ($start_date){
					$q->where('hr_yhp_dates_of_holidays', $start_date);
				})
				->where('hr_yhp_open_status', 0)
				->where('hr_yhp_unit', $unit_id)
				->pluck('hr_yhp_dates_of_holidays');
	}

	public function getOtPlannerByUnit($unit_id, $start_date, $end_date = null)
	{
		return DB::table('hr_yearly_holiday_planner')
				->when($end_date != null, function($q) use ($start_date, $end_date) {
					$q->where('hr_yhp_dates_of_holidays','>=', $start_date)
					  ->where('hr_yhp_dates_of_holidays','<=', $end_date);
				})
				->when($end_date == null, function($q) use ($start_date){
					$q->where('hr_yhp_dates_of_holidays', $start_date);
				})
				->where('hr_yhp_open_status', 2)
				->where('hr_yhp_unit', $unit_id)
				->pluck('hr_yhp_dates_of_holidays');
	}




	// summary
	public function getThisYearAttendance($employees)
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

	/**
     * Remove employee attendance
     *
     * @param  int     $as_id 
     * @param  int     $unit_id 
     * @param  date|array    $date 
     * @return boolean
     */

	public function removePresent($unit_id, $as_id, $date)
	{
		$table = get_att_table($unit_id);
		$data  = is_array($date)?$date:[$date];
		return DB::table($table)
			->where('as_id', $as_id)
			->whereIn('in_date', $date)
			->delete();
	}


	/**
     * Remove employee absent
     *
     * @param  int     $associate_id 
     * @param  date|array    $date 
     * @return boolean
     */

	public function removeAbsent($associate_id, $date)
	{
		$data  = is_array($date)?$date:[$date];
		return Absent::where('associate_id', $associate_id)
			->whereIn('date', $date)
			->delete();
	}

	/**
     * get today working status 
     *
     * @param  string     $associateId 
     * @param  int     $rosterStatus 
     * @param  date|null    $date 
     * @return string  ['Leave', 'General', 'Holiday', 'OT']
     */

	public function getTodayEmployeeStatus($associateId, $rosterStatus, $date = null)
	{
		$date = ($date == null)?date('Y-m-d'):$date;

		if($this->getEmployeeLeave($associateId, $date)){
			return 'Leave';
		}

		//now check roaster
		$roaster = $this->getEmployeeRoasterStatus($associateId, $date);

		if($roaster){
			return $roaster;
		}

		// now check for holiday planner
		if($rosterStatus == 0){
			return $this->getEmployeeHolidayPlan($associateId, $date);
		}else{
			return '';
		}

	}

	public function getEmployeeRoasterStatus($associateId, $date = null)
	{
		$date = ($date == null)?date('Y-m-d'):$date;

		return HolidayRoaster::where('as_id', $associateId)
			->where('date', $date)
			->first()
			->remarks??null;
	}


	/**
     * get today holiday plan data by id
     *
     * @param  string     $associateId 
     * @param  date|null    $date 
     * @return string
     */

	public function getEmployeeHolidayPlan($associateId, $date = null)
	{
		$date = ($date == null)?date('Y-m-d'):$date;

		$plan = DB::table('hr_yearly_holiday_planner as p')
			->leftJoin('hr_as_basic_info as b','p.hr_yhp_unit', 'b.as_unit_id')
			->where('b.associate_id', $associateId)
			->where('p.hr_yhp_dates_of_holidays', $date)
			->first()->hr_yhp_open_status??null;

		if($plan == 1){
			return 'Holiday';
		}else if($plan == 2){
			return 'OT';
		}else{
			return 'General';
		}
	}


	public function getEmployeeLeave($associateId, $date)
	{
		return Leave::where('leave_ass_id', $associateId)
			->where('leave_from','>=', $date)
		   ->where('leave_to','<=', $date)
			->first();

	}


}