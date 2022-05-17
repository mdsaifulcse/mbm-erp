<?php

namespace App\Repository\Hr;

use App\Models\Employee;
use App\Models\Hr\Shift;
use App\Models\Hr\ShiftBill;
use App\Models\Hr\ShiftHistory;
use App\Models\Hr\ShiftRoaster;
use Carbon\Carbon;
use DB, Cache;


class ShiftRepository 
{

	protected $employeeRepository;

	protected $date;

	public function __construct()
	{
		$this->employeeRepository = new EmployeeRepository;
	}


	// get current shift by unit & name
	public function all($date = null, $unit = [])
	{
		$date 	= $date??date('Y-m-d');
		$shift  = Shift::with(['bills','breaks','customBreaks','histories'])
			->when(count($unit) > 0, function($q) use ($unit){
				$unit = is_array($unit)?$unit:[$unit];
				$q->whereIn('hr_shift_unit_id', $unit);
			})
			->get();

	}

	public function find($id, $date = null)
	{
		$date 	= $date??date('Y-m-d');
		$shift  = Shift::with([
					'unit',
					'bills',
					'breaks',
					'customBreaks',
					'histories'
				])->findOrFail($id);

		$shift->current_bills = [];
		$shift->current_break_rules = [];
		$shift->current_shift_time = $shift->default_shift_time;

		// get active shift bills
		if(count($shift->bills)){
			$shift->current_bills = $this->filterCurrentBill($shift->bills, $date);
		}

		if(count($shift->breaks) > 0){
			$shifts[$k]->current_extra_breaks = $this->filterByCurrentDate($shift->breaks, $date);
		}

		if(count($shift->customBreaks) > 0){
			$shift->current_break_rules = $this->filterByCurrentDate($shift->customBreaks, $date);
		}

		// get active shift time 
		if(count($shift->histories) > 0){
			$current = $this->filterCurrentShiftTime($shift->histories, $date);
			$shift->current_shift_time = $current == null?$shift->default_shift_time:$current;
		}


		return $shift;
	}


	public function getShiftForSelectByDate($date = null)
	{
		$date 	= $date??date('Y-m-d');
		$month  = date('m', strtotime($date));
		$year  = date('Y', strtotime($date));
		$day  = 'day_'.(int) date('d', strtotime($date));

		$shift = Shift::whereIn('hr_shift_unit_id', auth()->user()->unit_permissions())
			->pluck('hr_shift_name')
			->unique();

		$default = Employee::where('as_status', 1)->pluck('as_shift_id')->unique()->toArray();

		$changed = ShiftRoaster::where('shift_roaster_month', $month )
			->where('shift_roaster_year', $year )
			->pluck($day)
			->unique()
			->merge($default)
			->toarray();

		return $shift->intersect($changed);


		

	}


	public function get($date = null, $unit = [])
	{

		$unitList = auth()->user()->unit_permissions();
		$date 	= $date??date('Y-m-d');
		$unit   = !empty($unit)?collect($unitList)->intersect($unit):$unitList;

		$getShifts  = Shift::with(['unit','bills','breaks','customBreaks','histories'])
					->whereIn('hr_shift_unit_id', $unit)
					->where('hr_shift_status', 1)
					->orderBy('hr_shift_id', 'desc')
					->get();

		$shifts = [];
		//get information based on current day
		foreach($getShifts as $k => $shift){
			$shifts[$k] = $shift;
			$shifts[$k]->current_break_rules = [];
			$shifts[$k]->current_shift_time = $shift->default_shift_time;

			if(count($shift->customBreaks) > 0){
				$shifts[$k]->current_break_rules = $this->filterByCurrentDate($shift->customBreaks, $date);
			}

			if(count($shift->breaks) > 0){
				$shifts[$k]->current_extra_breaks = $this->filterByCurrentDate($shift->breaks, $date);
			}

			// get active shift time 
			if(count($shift->histories) > 0){
				$current = $this->filterCurrentShiftTime($shift->histories, $date);
				$shifts[$k]->current_shift_time = $current == null?$shift->default_shift_time:$current;
			}


		}

		return $shifts;
	}

	
	// need to work
	public function getCurrentShift($date = null, $unit = [])
	{ 
		$date = $date??date('Y-m-d');

		$shift = collect(shift_by_code())
			->where('ot_status',0)
			->all();

		if($date == date('Y-m-d')){
			$shift = collect($shift)
				->where('hr_shift_start_time', '<=', date('H:i:s'))
				->all();
		}

		if(count($unit) > 0){
			$shift = collect($shift)
				->whereIn('hr_shift_unit_id', $unit)
				->all();

		}

		return collect($shift)->pluck('hr_shift_name','hr_shift_name');
					
	}

	/**
     * filter current bill items of an shift by date
     *
     * @param  object   $items  
     * @param  date     $date
     * @return collection
     */


	public function filterCurrentBill($bills, $date)
	{
		return collect($bills)->filter(function($bill) use ($date) {
						return ($bill->end_date >= $date || is_null($bill->end_date)) && (is_null($bill->start_date) || $bill->start_date <= $date);
					})
					->pluck('hr_bill_type_id')
					->unique()
					->toArray();
	}


	/**
     * filter current shift itesm[break, break rules] of an shift by date
     *
     * @param  object   $items  
     * @param  date     $date
     * @return collection
     */

	public function filterByCurrentDate($items, $date)
	{
		return collect($items)->filter(function($shift) use ($date) {
						return ($shift->end_date >= $date || is_null($shift->end_date)) && ($shift->start_date <= $date || is_null($shift->start_date));
					})->all();
	}

	

	/**
     * filter current shift time of an shift by date
     *
     * @param  object   $shifts  
     * @param  date     $date
     * @return collection
     */

	public function filterCurrentShiftTime($shifts, $date)
	{
		return collect($shifts)->filter(function($shift) use ($date) {
						return ($shift->end_date >= $date || is_null($shift->end_date)) && ($shift->start_date <= $date || is_null($shift->start_date));
					})->first();
	}


	/**
     * get shift information of a single employee by date
     *
     * @param  date     $date
     * @param  string   $associateId
     * @return collection
     */


	public function getEmployeeShiftByDate($associateId, $date = null)
	{
		$date 	= $date??date('Y-m-d');
		$employee 	= $this->getEmployeeWithCurrentShiftName($associateId, $date);

		$shiftName  = $employee->as_shift_id; // current shift

		return $this->getTodaysShiftPropertiesByName($employee->as_unit_id, $date, $shiftName);

	}


	/**
     * get employeewise shift name by date
     *
     * @param  Request  $request 
     * @param  date     $date
     * @param  array    $select  [select columns from hr_as_basic_info table]
     * @return collection
     */


	public function getShiftEmployeeByDate($request = [], $date = null, $select = null)
	{

		$roaster 	= $this->getShiftRoaster($date);

		if($select){
			$employees 	= $this->employeeRepository->getEmployeeBy($request, $date, $select);
		}else{

			$employees 	= $this->employeeRepository->getEmployeeBy($request, $date);
		}

		return collect($employees)->map(function($q) use ($roaster){
				if(isset($roaster[$q->associate_id])){
					$q->as_shift_id = $roaster[$q->associate_id]?? $q->as_shift_id;
				}
				return $q;
			});

	}
	

	/**
     * get todays shift code by shift name and unit 
     *
     * @param  int      $unit 
     * @param  date     $date
     * @param  string   $name
     * @return collection
     */

	public function getTodaysShiftPropertiesByName($unit, $date = null, $name = null)
	{
		$date 	= $date??date('Y-m-d');
		
		$shifts  = Shift::with(['unit','bills','breaks','customBreaks','histories'])
			->when($name != null, function($q) use ($name){
				$q->where('hr_shift_name', $name);
			})
			->where('hr_shift_unit_id', $unit)
			->get();

		$output = [];
		foreach($shifts as $k => $shift){
			$key = $shift->hr_shift_name;


			$bill = []; $break_rules = null; $breaks = null;

			if(count($shift->bills) > 0){
				$bill = $this->filterCurrentBill($shift->bills, $date);
			}
			if(count($shift->breaks) > 0){
				$breaks = $this->filterByCurrentDate($shift->breaks, $date);
			}
			if(count($shift->customBreaks) > 0){
				$break_rules = $this->filterByCurrentDate($shift->customBreaks, $date);
			}

			$output[$key] = (object)[];
			$output[$key]->time = $shift->default_shift_time;
			$output[$key]->bills = $bill;
			$output[$key]->break_rules = $break_rules;
			$output[$key]->breaks = $breaks;

			if(count($shift->histories) > 0){
				$current = $this->filterCurrentShiftTime($shift->histories, $date);
				$output[$key]->time = $current == null?$shift->default_shift_time:$current;
			}

		}

		// return shift information by shift name
		if($name != null){
			return $output[$name]??null;
		}

		return $output;

	}


	/**
     * get bill type id by shift code 
     *
     * @param  date     $date 
     * @param  array    $code
     * @return collection
     */

	public function getBillByTodaysCode($date = null, $code = [])
	{
		$date 	= $date??date('Y-m-d');
		$properties = $this->getShiftPropertiesByTodaysCode($date, $code);

		return collect($properties)->map(function($q){
			return $q->bills;
		})->all();
	}

	/**
     * get a days shift properties with bill information by code 
     *
     * @param  date     $date 
     * @param  array    $code
     * @return collection
     */

	public function getShiftPropertiesByTodaysCode($date = null, $code = [])
	{
		$date 	= $date??date('Y-m-d');
		$shifts  = Shift::with(['bills','breaks','customBreaks','histories'])
					->when(!empty($code), function($q) use ($code){
						$q->whereIn('hr_shift_code',$code)
						  ->orWhereHas('histories', function($p) use ($code){
							$p->whereIn('hr_shift_code',$code);
						});
					})->get();

		$output = [];
		foreach($shifts as $k => $shift)
		{

			$code = $shift->hr_shift_code;
			$bill = []; $break_rules = null; $breaks = null;

			if(count($shift->bills) > 0){
				$bill = $this->filterCurrentBill($shift->bills, $date);
			}
			if(count($shift->customBreaks) > 0){
				$break_rules = $this->filterByCurrentDate($shift->customBreaks, $date);
			}
			if(count($shift->breaks) > 0){
				$breaks = $this->filterByCurrentDate($shift->breaks, $date);
			}

			$output[$code] = (object)[];
			$output[$code]->time = $shift->default_shift_time;
			$output[$code]->bills = $bill;
			$output[$code]->break_rules = $break_rules;
			$output[$code]->breaks = $breaks;

			if(count($shift->histories) > 0 ){

				foreach($shift->histories as $k1 => $history){
					$ncode = $history->hr_shift_code;
					$output[$ncode] = (object)[];
					$output[$ncode]->time = $history;
					$output[$ncode]->bills = $bill;
					$output[$ncode]->break_rules = $break_rules;
					$output[$ncode]->breaks = $breaks;
				}
			}
		}

		return $output;

	}

	/**
     * get a  shift properties with bill information by a single code 
     *
     * @param  string    $code
     * @param  date     $date 
     * @return collection
     */

	public function getShiftPropertiesByTodaysSingleCode($code, $date = null)
	{
		$date 	= $date??date('Y-m-d');
		$shift  = Shift::with(['bills','breaks','customBreaks','histories'])
					->when($code != null, function($q) use ($code){
						$q->where('hr_shift_code',$code)
						  ->orWhereHas('histories', function($p) use ($code){
							$p->where('hr_shift_code',$code);
						});
					})->first();

		$output = (object)[];
		if($shift){
			$bill = []; $break_rules = null; $breaks = null;

			if(count($shift->bills) > 0){
				$bill = $this->filterCurrentBill($shift->bills, $date);
			}
			if(count($shift->customBreaks) > 0){
				$break_rules = $this->filterByCurrentDate($shift->customBreaks, $date);
			}
			if(count($shift->breaks) > 0){
				$breaks = $this->filterByCurrentDate($shift->breaks, $date);
			}

			if($shift->hr_shift_code != $code){
				$history = $shift->histories->where('hr_shift_code', $code)->first();
				if($history){
					$output->time = $history;
					$output->bills = $bill;
					$output->break_rules = $break_rules;
					$output->breaks = $breaks;
				}
			}else{
				$output->time = $shift->default_shift_time;
				$output->bills = $bill;
				$output->break_rules = $break_rules;
				$output->breaks = $breaks;
			}


		}

		return $output;

	}

	/**
     * get shift of a sinfle employee by date 
     *
     * @param  string  $employee 
     * @param  date $date 
     * @return array  
     */
	public function getEmployeeWithCurrentShiftName($employee, $date)
	{
		$date = $date??date('Y-m-d');
		$col  = 'day_'.((int) date('d', strtotime($date)));
		$data = DB::table('hr_as_basic_info as b')
			->select('b.*', 'r.'.$col.' as shift') 
			->leftJoin('hr_shift_roaster as r', function($join) use ($date){
				$join->on('b.associate_id', 'r.shift_roaster_associate_id')
					->where('r.shift_roaster_month', date('n', strtotime($date)))
					->where('r.shift_roaster_year',date('Y', strtotime($date)));
			})
			->where('b.associate_id', $employee)
			->first();

		// update current shift 
		if($data->shift != null){
			$data->as_shift_id = $data->shift;
		}

		return $data;
	}

	/**
     * get shift roaster information by date and employee
     *
     * @param  date $date 
     * @param  string|array|null  $employee 
     * @return array  
     */

	public function getShiftRoaster($date = null, $employee = null)
	{
		$date = $date??date('Y-m-d');
		$col  = 'day_'.((int) date('d', strtotime($date)));
		return  ShiftRoaster::select($col.' as shift','shift_roaster_associate_id')
			->where('shift_roaster_month', date('n', strtotime($date)))
			->where('shift_roaster_year',date('Y', strtotime($date)))
			->when($employee != null, function($q) use ($employee){
				$employee = is_array($employee)?$employee:[$employee];
				$q->whereIn('shift_roaster_associate_id', $employee);
			})
			->get()
			->filter(function($q){
				return $q->shift != null;
			})
			->pluck('shift','shift_roaster_associate_id');
	}



	/**
     * get todays shift code by shift name and unit 
     *
     * @param  int      $unit 
     * @param  date     $date
     * @param  string   $name
     * @return collection
     */

	public function getMonthlyShiftPropertiesByEmployee($associateId, $monthYear)
	{
		$month = date('n', strtotime($monthYear));
		$year  = date('Y', strtotime($monthYear));
		$firstDate = date('Y-m-d', strtotime($monthYear.'-01'));
		$lastDate  = date('Y-m-t', strtotime($firstDate));

		$employee = Employee::where('associate_id', $associateId)->first();

		if(date('Y-m', strtotime($monthYear)) != date('Y-m')){
			$salaryUnit = DB::table('hr_monthly_salary')
			->select('unit_id')
			->where('as_id', $employee->associate_id)
			->where('month', date('m', strtotime($monthYear)))
			->where('year', $year)
			->pluck('unit_id')
			->first();

			$unit = $salaryUnit != null?$salaryUnit:$employee->as_unit_id;
		}else{
			$unit = $employee->as_unit_id;
		}
		
		$shifts  = Shift::with(['unit','bills','breaks','customBreaks','histories'])
					->where('hr_shift_unit_id', $unit)
					->get()
					->keyBy('hr_shift_name');

		$roaster = ShiftRoaster::where('shift_roaster_month', $month)
			->where('shift_roaster_year', $year)
			->where('shift_roaster_associate_id', $associateId)
			->first();

		$dates = []; $i = $firstDate;

		while($i <= $lastDate){
			$dates[$i] = (object)[];
			$shiftName = $employee->as_shift_id;

			if($roaster){
				$col  = 'day_'.((int) date('d', strtotime($i)));
				$shiftName = $roaster->{$col}??$shiftName;
			}
			$shift = $shifts[$shiftName]??null;
			
			$bill = []; $break_rules = null; $breaks = null;

			if(count($shift->bills) > 0){
				$bill = $this->filterCurrentBill($shift->bills, $i);
			}
			if(count($shift->breaks) > 0){
				$breaks = $this->filterByCurrentDate($shift->breaks, $i);
			}
			if(count($shift->customBreaks) > 0){
				$break_rules = $this->filterByCurrentDate($shift->customBreaks, $i);
			}

			$dates[$i] = (object)[];
			$dates[$i]->time = $shift->default_shift_time;
			$dates[$i]->bills = $bill;
			$dates[$i]->break_rules = $break_rules;
			$dates[$i]->breaks = $breaks;

			if(count($shift->histories) > 0){
				$current = $this->filterCurrentShiftTime($shift->histories, $i);
				$dates[$i]->time = $current == null?$shift->default_shift_time:$current;
			}

			$i = Carbon::parse($i)->addDay()->toDateString();

		}
		return $dates;

	}

	/**
     * calculate shift out time
     *
     * @param  time $shift_time 
     * @param  int  $break 
     * @return time  
     */

	public function calculateShiftOutTime($shift_time, $break)
	{
		return Carbon::parse($shift_time)->addMinutes($break)->format('H:i:s');
	}

	/**
     * generate unique shift code 
     * U = unit, second value is unit code, and next is shift
     *
     * @param  int     $unit 
     * @param  string  $name 
     * @return string  
     */

	protected function shiftCodeGenerator($unit, $name)
	{

		$words = preg_split("/\s+/", $name);
        $acronym = '';
        foreach ($words as $w) {
            $acronym .= $w[0];
        }
        
        $code = strtoupper('U'.$unit.$acronym);
        return $this->getShiftCodeExist($code, $code);

	}

	/**
     * check shift code exist or not, if exist increment 
     *
     * @param  string     $code 
     * @param  string     $newcode modified code 
     * @param  int        $count  this will added to the code for unique code 
     * @return string
     */

	protected function getShiftCodeExist($code, $newCode = null, $count = 0)
	{
		$shiftCode = cached_shift_code();

		if(in_array($newCode, $shiftCode->keys()->toArray())){
			$count++;
			$newCode = $code.'-'.$count; 
			return $this->getShiftCodeExist($code, $newCode, $count);
		}

		return $newCode;
	}

	public function store($request)
	{
		
		foreach($request->hr_shift_unit_id as $key => $unit)
        {
        	DB::beginTransaction();
        	try{
        		// store shift information
	            $shift = $this->storeShift($request, $unit);

	            if($shift->hr_shift_id){

	                // entry bill
	                $this->attachBill($request->bill_type, $shift->hr_shift_id);
	                
	                // enter extra rules
	                if(isset($request->break_rule) && count($request->break_rule) > 0){
	                    $break_rule = [];
	                    foreach($request->break_rule as $k => $br){
	                        $break_rule[$k] = [
	                            'hr_shift_id' => $shift->hr_shift_id,
	                            'days' => isset($br['days'])?$br['days']:null,
	                            'designations' => isset($br['designations'])?$br['designations']:null,
	                            'break_time' => isset($br['break_time'])?$br['break_time']:null,
	                            'break_time_start' => isset($br['break_start'])?$br['break_start']:null,
	                            'start_date' => isset($br['start_date'])?$br['start_date']:null,
	                            'end_date' => isset($br['end_date'])?$br['start_date']:null
	                        ];
	                    }
	                    DB::table('hr_shift_custom_break')->insert($break_rule);
	                    
	                }

	                // enter extra break

	            }
				DB::commit();
				Cache::forget('cached_shift_code');

				return 'success';
			}catch(\Exception $e){
				DB::rollback();
				return $e->getMessage();
			}
        }

		
	}


	public function updateShiftTime($id, $request)
	{
		$shift = Shift::with(['histories'])->findOrFail($id);

		// unique check
		if($shift->hr_shift_start_time == $request->hr_shift_start_time && $shift->hr_shift_end_time == $request->hr_shift_end_time && $shift->hr_shift_break_time == $request->hr_shift_break_time){
			// already exist
			return [
				'status' => 'failed',
				'message' => 'Information already exist in this shift! Please set as default.'
			];
		}

		$code = $this->shiftCodeGenerator($shift->hr_shift_unit_id, $shift->hr_shift_name);

		DB::beginTransaction();
		try{
			$history = new ShiftHistory;
			$history->hr_shift_id  = $shift->hr_shift_id;
	        $history->hr_shift_code = $code;
	        $history->hr_shift_start_time = $request->hr_shift_start_time;
	        $history->hr_shift_end_time = $request->hr_shift_end_time;
	        $history->hr_shift_break_time = $request->hr_shift_break_time;
	        $history->hr_break_start_time = $request->hr_default_break_start;
	        $history->start_date = $request->hr_shift_start_date;
	        $history->end_date = $request->hr_shift_end_date;
	        $history->ot_status = $request->ot_status??0;
	        $history->ot_shift = $request->ot_shift??null;
	        $history->created_by = auth()->id();


	        $history->save();
	        // end previous history
	        if(count($shift->histories) > 0){
	        	$lastDate = Carbon::parse($request->hr_shift_start_date)->subDay()->toDateString();
	        	$exhistory = $shift->histories->first();
	        	if($exhistory->start_date < $lastDate && $exhistory->end_date == null){
		        	$exhistory->end_date = $lastDate;
		        	$exhistory->save();
	        	}
	        }

	        // add end date of previous shift history
	    	DB::commit();
	        Cache::forget('cached_shift_code');

	        return [
	        	'status' => 'success',
	        	'data' => $shift
	        ];
	    }catch(\Exception $e){

			DB::rollback();
			return [
				'status' => 'failed',
				'message' => 'Something went wrong! Please try again'
			];
		}
	}

	/**
     * sync bill with a shift, if not exist add 
     *
     * @param  int     $id  shift_id 
     * @param  Request $request
     * 
     */

	public function syncBill($id, $request)
	{
		$shift = Shift::with(['bills'])->findOrFail($id);

		$bills = $shift->bills->pluck('hr_bill_type_id');

		$reqBills = collect($request->bill_type);

		DB::beginTransaction();
		try{
			// close shift
			$closeBill = $bills->diff($reqBills);
			$lastDate  = Carbon::now()->subDay()->toDateString();
			
			ShiftBill::where('hr_shift_id',$id)
				->whereIn('hr_bill_type_id', $closeBill)
				->whereNull('end_date')
				->update([
					'end_date' => $lastDate
				]);

			// attatch new bill shift
			$attach = $reqBills->diff($bills);
			if(count($attach) > 0){
				$this->attachBill($attach, $shift->hr_shift_id);
			}

			// add end date of previous shift history
	    	DB::commit();

	        return [
	        	'status' => 'success'
	        ];
		}catch(\Exception $e){
			DB::rollback();
			return [
				'status' => 'failed',
				'message' => 'Something went wrong! Please try again'
			];
		}

	}


	/**
     * store shift data 
     *
     * @param  Request  $request 
     * @param  integer  $unit
     * @return collection
     */


	public function storeShift($request, $unit)
	{
		$shift = new Shift(); 
        $shift->hr_shift_unit_id  = $unit;
        $shift->hr_shift_name     = $request->hr_shift_name;
        $shift->hr_shift_name_bn  = $request->hr_shift_name_bn??null;
        $shift->hr_shift_code     = $this->shiftCodeGenerator($unit, $request->hr_shift_name);
        $shift->hr_shift_start_time = $request->hr_shift_start_time;
        $shift->hr_shift_end_time = $request->hr_shift_end_time;
        $shift->hr_shift_break_time = $request->hr_shift_break_time;
        $shift->hr_default_break_start = $request->hr_default_break_start;
        $shift->hr_shift_night_flag = ($request->hr_shift_start_time > $request->hr_shift_end_time)?1:0;
        $shift->hr_shift_default = $request->hr_shift_default??0;
        $shift->ot_status = $request->ot_status??0;
        $shift->ot_shift = $request->ot_shift??null;
        $shift->created_by = auth()->id();
        $shift->save();

        Cache::forget('cached_shift_code');

        return $shift;

	}

	/**
     * add bill to a shift
     *
     * @param  array    $bills  bill_type_id 
     * @param  int      $shift_id
     * 
     */

	public function attachBill($bills, $shift_id)
	{

		if(isset($bills) && count($bills) > 0){
            $bill = [];
            foreach($bills as $k => $bl){
                $bill[$k]['hr_shift_id'] = $shift_id;
                $bill[$k]['hr_bill_type_id'] = $bl;
            }
            DB::table('hr_shift_bills')->insert($bill);
        }

	}

}