<?php

namespace App\Repository\Hr;


use App\Helpers\EmployeeHelper;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\District;
use App\Models\Employee;
use App\Models\Hr\Absent;
use App\Models\Hr\EarnedLeave;
use App\Models\Hr\Leave;
use App\Models\Hr\MaternityLeave;
use App\Models\Hr\MaternityMedical;
use App\Models\Hr\MaternityMedicalRecord;
use App\Models\Hr\MaternityNominee;
use App\Models\Hr\MaternityPayment;
use App\Models\Hr\SalaryAdjustDetails;
use App\Models\Hr\SalaryAdjustMaster;
use App\Repository\BaseRepository;
use Carbon\Carbon;
use DB,Response,DataTables,Validator, Cache;


class MaternityRepository extends BaseRepository
{

	public function set()
	{
		$data = MaternityLeave::where('status','Approved')->get();

		foreach($data as $k => $leave){
			$leaveMonth = date('m',strtotime($leave->leave_from)); 
			$leaveYear = date('Y',strtotime($leave->leave_from));
			$salary_history = $this->getLast3MonthSalaryByLeave($leave);
			$employee = get_employee_by_id($leave->associate_id);



			$salary = DB::table('hr_monthly_salary')
				->where('as_id', $leave->associate_id)
				->where('month', $leaveMonth)
				->where('year', $leaveYear)
				->first();


			if($salary){
				$subSection = subSection_by_id();
				$sub = $subSection[$salary->sub_section_id];
				$empHis = (object)[
					'as_unit_id' => $salary->unit_id,
					'as_designation_id' => $salary->designation_id,
					'as_area_id' => $sub['hr_subsec_area_id'],
					'as_department_id' => $sub['hr_subsec_department_id'],
					'as_section_id' => $sub['hr_subsec_section_id'],
					'as_subsection_id' => $salary->sub_section_id
				];

				$benefits = (object) [
					'ben_current_salary' => $salary->gross,
					'ben_basic' => $salary->basic,
					'ben_house_rent' => $salary->house,
					'medical' => $salary->medical,
					'transport' => $salary->transport,
					'food' => $salary->food
				];
			}else{
				$benefits = $this->getCurrentSalaryStructure($employee);
				$empHis = $this->getCurrentEmployeeInformationStructure($employee);
			}
			$leave->employee_history = json_encode($empHis);
			$leave->unit_id = $empHis->as_unit_id;
			$leave->save();

			$payment = MaternityPayment::where('hr_maternity_leave_id', $leave->id)->first();
			$payment->salary_history = json_encode($salary_history);
			$payment->salary = json_encode($benefits);
			$payment->save();
		}
	}

	public function get($input)
	{
	    
		$unit = $this->getAuthUnit($input['unit']);
		$location = $this->getAuthLocation($input['location']);

		return MaternityLeave::with([
				'payment',
				'audit', 
				'employee'
			])
			->whereHas('employee', function($p) use ($input, $location) {
				$p->whereIn('as_location', $location)
		            ->when(!empty($input['line_id']), function ($query) use($input){
		                $query->where('as_line_id', $input['line_id']);
		            })
		            ->when(!empty($input['floor_id']), function ($query) use($input){
		                $query->where('as_floor_id',$input['floor_id']);
		            })
		           ->when($input['otnonot']!=null, function ($query) use($input){
		                $query->where('as_ot',$input['otnonot']);
		            });
			})
            ->where('leave_from','>=', $input['from_date'])
            ->where('leave_from','<=', $input['to_date'])
            ->whereIn('unit_id', $unit)
            ->when(!empty($input['area']), function ($query) use($input){
                $query->whereJsonContains('employee_history->as_area_id',$input['area']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
                $query->whereJsonContains('employee_history->as_department_id',$input['department']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
                $query->whereJsonContains('employee_history->as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use($input){
                $query->whereJsonContains('employee_history->as_subsection_id', $input['subSection']);
            })
            ->orderBy('id', 'DESC')
            ->get();
	}



	public function processMaternityPayment($leave, $employee, $request = null)
	{
		$salary_history = null;
		$benefits = $this->getCurrentSalaryStructure($employee);
		$payment = new MaternityPayment();
		$payment->hr_maternity_leave_id = $leave->id;
		$payment->wages_day = 112;
		$payment->benefits_day = 0;

		$payment->salary = json_encode($benefits);
		
		// per day gross
		$payment->per_day_wages = round(($employee->ben_current_salary/30),2);

		// calculation for first baby
		if(($leave->no_of_son + $leave->no_of_daughter) >= 2){
			$payment->maternity_for_3 = 1;
			// per day benefits
			$payment->per_day_benefit = round(($employee->ben_current_salary - $employee->ben_basic)/30,2);
			$payment->earned_leave = $request->earned_leave;
			$payment->sick_leave = $request->sick_leave;

			$total_leave = $request->earned_leave + $request->sick_leave;
			$payment->wages_day = $total_leave;
			$leave_payment = $payment->per_day_wages*$total_leave;

			$payment->benefits_day = 112 - $total_leave;
			$first_pay_day = 56 - $total_leave;
			$first_ben_payment = $payment->per_day_benefit*$first_pay_day;

			$payment->first_payment = ceil($leave_payment + $first_ben_payment);
			$payment->second_payment = ceil($payment->per_day_benefit * 56);

			// need to update earn leave
		}else{
			$salary_history = $this->getLast3MonthSalaryByLeave($leave);
			$payment->salary_history = json_encode($salary_history);

			$totalSalary = $salary_history->total;
			$totalPresent = $totalSalary->present == 0 ? 1: $totalSalary->present; // infinity error

			// update per day wages
			$payment->per_day_wages  = round($totalSalary->total_amount / $totalPresent,2);

			$payment->first_payment = ceil($payment->per_day_wages * 56);
			$payment->second_payment = $payment->first_payment;
		}
		
		$payment->save();

		return view('hr.operation.maternity.payment_slip', compact('leave','employee','payment','salary_history','benefits'))->render();

	}


	public function getCurrentSalaryStructure($employee)
	{
		return (object) [
			'ben_current_salary' => $employee->ben_current_salary,
			'ben_basic' => $employee->ben_basic,
			'ben_house_rent' => $employee->ben_house_rent,
			'medical' => 600,
			'transport' => 350,
			'food' => 900
		];
	}

	public function getCurrentEmployeeInformationStructure($employee)
	{
		return (object) [
			'as_unit_id' => $employee->as_unit_id,
			'as_designation_id' => $employee->as_designation_id,
			'as_area_id' => $employee->as_area_id,
			'as_department_id' => $employee->as_department_id,
			'as_section_id' => $employee->as_section_id,
			'as_subsection_id' => $employee->as_subsection_id,
		];
	}


	public function getLast3MonthSalaryByLeave($leave)
	{

		$salary = [];
		$lastmonth = (clone $leave->leave_from)->subMonth()->format('Y-m');

		// get last 3 months salary
		for ($i= 1; $i <= 3 ; $i++) { 
			$salary[$lastmonth] = $this->getSalaryBonusOfEmployee($leave->associate_id, $lastmonth);
			$lastmonth =  Carbon::parse($lastmonth)->subMonths()->format('Y-m');
		}

		$total = (object)[];
		$total->present = collect($salary)->sum('present');
		$total->absent = collect($salary)->sum('absent');
		$total->leave = collect($salary)->sum('leave');
		$total->salary_payable = collect($salary)->sum('salary_payable');
		$total->attendance_bonus = collect($salary)->sum('attendance_bonus');
		$total->ot_payment = collect($salary)->sum('ot_payment');
		$total->others = collect($salary)->sum('others');
		$total->eid_bonus = collect($salary)->sum('eid_bonus');
		$total->total_amount = collect($salary)->sum('total_payable');


		return (object)[
			'salary' => $salary,
			'total' => $total
		];
	}

	public function getSalaryBonusOfEmployee($associateId, $yearMonth)
	{
		$monthYear = $monthyear = explode('-', $yearMonth);
		$salary  = DB::table('hr_monthly_salary')
			->select('as_id','month','year','present','absent','leave','ot_hour','ot_rate','attendance_bonus','salary_payable','total_payable')
			->where([
				'as_id' => $associateId,
				'year' => $monthYear[0],
				'month' => $monthYear[1]
			])
			->first();

		if($salary){
			$salary->ot_payment = round(($salary->ot_hour*$salary->ot_rate),2);
			$salary->others = $salary->total_payable - ($salary->salary_payable + $salary->ot_payment + $salary->attendance_bonus);
			$salary->others = $salary->others > 2?$salary->others:0;
			// get eid bonus
			$salary->eid_bonus = $this->getEidBonus($associateId, $yearMonth);	
		}

		return $salary;
	}


	public function getEidBonus($associateId, $yearMonth)
	{
		$yearMonth = Carbon::parse($yearMonth.'-01');
		$firstDate = $yearMonth->startOfMonth()->format('Y-m-d');
		$lastDate = $yearMonth->endOfMonth()->format('Y-m-d');;

		return DB::table('hr_bonus_sheet')
			->where('associate_id')
			->where('created_at', '>=' ,$firstDate)
			->where('created_at', '<=' ,$lastDate)
			->first()->bonus_amount??0;
	}
}