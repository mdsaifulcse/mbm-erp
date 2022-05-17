<?php

namespace App\Repository\Hr;

use App\Contracts\Hr\SalaryInterface;
use App\Models\Employee;
use App\Models\Hr\AttendanceBonusConfig;
use App\Models\Hr\Benefits;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\PartialSalary;
use App\Models\Hr\SalaryAddDeduct;
use App\Models\Hr\SalaryAdjustDetails;
use App\Models\Hr\SalaryAdjustMaster;
use App\Repository\Hr\AttendanceProcessRepository;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SalaryRepository implements SalaryInterface
{
    public $attProcess;
    public function __construct(AttendanceProcessRepository $attProcess)
    {
        ini_set('zlib.output_compression', 1);
        $this->attProcess = $attProcess;
    }
    public function getSalaryReport($input, $data)
    {
        $result['summary']      = $this->makeSummarySalary($data);
        if(isset($input['salary_report_type']) && $input['salary_report_type'] == 'all' && $input['report_format'] == 0){
            $list = collect($data)->groupBy('ot_status',true);;
        }else{
            $list = collect($data)
                ->groupBy($input['report_group'],true);
        }
        
        if(!empty($input['selected'])){
            $input['report_format'] = 0;
        }
        if($input['report_format'] == 1){
            $list = $list->map(function($q){
                $q = collect($q);
                $sum  = (object)[];
                $sum->ot            = $q->where('ot_status', 1)->count();
                
                $sum->nonot         = $q->where('ot_status', 0)->count();
                $sum->nonotAmount   = $q->where('ot_status', 0)->sum('total_payable');
                $sum->otHour        = $q->where('ot_status', 1)->sum('ot_hour');
                $sum->otHourAmount  = $q->sum(function ($s) {
                                        return ($s->ot_hour * $s->ot_rate);
                                    });
                $sum->otAmount      = $q->where('ot_status', 1)->sum('total_payable') - $sum->otHourAmount;
                $sum->cashPayable   = $q->sum('cash_payable');
                $sum->bankPayable   = $q->sum('bank_payable');
                $sum->stamp         = $q->sum('stamp');
                $sum->tds           = $q->sum('tds');
                $sum->advanceAmount = $q->sum('partial_amount');
                $sum->salaryPayable = $q->sum('salary_payable');
                $sum->grossPayable = $q->sum('gross');
                $sum->totalPayable  = $q->sum('total_payable');
                $sum->foodAmount    = $q->sum('food_deduct');
                return $sum;
            })->all();
        }

        $result['uniqueGroup'] = $list;
        $result['input']       = $input->all();
        $result['format']      = $input['report_group'];
        $result['unit']        = unit_by_id();
        $result['location']    = location_by_id();
        $result['line']        = line_by_id();
        $result['floor']       = floor_by_id();
        $result['department']  = department_by_id();
        $result['designation'] = designation_by_id();
        $result['section']     = section_by_id();
        $result['subSection']  = subSection_by_id();
        $result['area']        = area_by_id();
        return $result;
    }

    public function getSalaryByFilter($input, $dataRow, $employee)
    {
        
        $subSection = subSection_by_id();
        $getFoodDeduct = $this->getFoodDeductList($input['year_month']);
        $getGrade = designation_grade_by_id();
        $getDesignation = designation_by_id();

        return collect($dataRow)->map(function($q) use ($subSection, $employee, $getFoodDeduct, $getGrade, $getDesignation) {
            $designationGrade = $getDesignation[$q->designation_id]['grade_id']??'';

            if($designationGrade == ''){
                $gradeSequence = 0;
                $gradeName = $getDesignation[$q->designation_id]['hr_designation_grade']??'';
            }else{
                $gradeSequence = $getGrade[$designationGrade]->grade_sequence??0;
                $gradeName = $getGrade[$designationGrade]->grade_name??'';
            }

            $q->as_section_id = $subSection[$q->sub_section_id]['hr_subsec_section_id']??'';
            $q->as_department_id = $subSection[$q->sub_section_id]['hr_subsec_department_id']??'';
            $q->as_area_id = $subSection[$q->sub_section_id]['hr_subsec_area_id']??'';
            $q->as_name = $employee[$q->as_id]->as_name??'';
            $q->as_oracle_code = $employee[$q->as_id]->as_oracle_code??'';
            $q->as_oracle_sl = $employee[$q->as_id]->as_oracle_sl??'';
            $q->temp_id = $employee[$q->as_id]->temp_id??'';
            $q->as_line_id = $employee[$q->as_id]->as_line_id??'';
            $q->as_floor_id = $employee[$q->as_id]->as_floor_id??'';
            $q->as_contact = $employee[$q->as_id]->as_contact??'';
            $q->as_unit_id = $q->unit_id;
            $q->as_location = $q->location_id;
            $q->as_doj = $employee[$q->as_id]->as_doj??'';
            $q->as_subsection_id = $q->sub_section_id;
            $q->as_designation_id = $q->designation_id;
            $q->food_deduct = $getFoodDeduct[$q->as_id]??0;
            $q->serial = $q->as_oracle_sl.$q->temp_id;
            $q->service_length = Carbon::createFromFormat('Y-m-d', $q->as_doj)->diff(Carbon::now())->format('%y');

            $q->grade_sequence = $gradeSequence;
            $q->grade_name = $gradeName;
            unset($q->unit_id, $q->location_id, $q->sub_section_id, $q->designation_id);
            return $q;
        });

        // return collect($getSalary)->sortByDesc('grade_sequence');
    }

    protected function getFoodDeductList($yearMonth)
    {
        $yearMonthExp = explode('-', $yearMonth);
        return DB::table('hr_salary_add_deduct')
        ->where('year', $yearMonthExp[0])
        ->where('month', $yearMonthExp[1])
        ->pluck('food_deduct', 'associate_id');
    }

    public function getSalaryByMonth($params)
    {
        if(isset($params['emp_status']) && $params['emp_status'][0] == 25){
            $params['emp_status'] = [2,5];
        }else{
            $params['emp_status'] = $params['emp_status']??[1];
        }
        
        $yearMonthExp = explode('-', $params['year_month']);

        $data = DB::table('hr_monthly_salary')
        ->select('hr_monthly_salary.*', DB::raw('round(ot_hour * ot_rate,2) AS ot_amount'))
        ->whereIn('emp_status', $params['emp_status'])
        ->where('year', $yearMonthExp[0])
        ->where('month', $yearMonthExp[1])
        ->whereIn('unit_id', auth()->user()->unit_permissions())
        ->whereIn('location_id', auth()->user()->location_permissions())
        ->when(isset($params['as_id']) && !empty($params['as_id']), function ($query) use($params){
           return $query->whereIn('as_id',$params['as_id']);
        })
        ->when(isset($params['disbursed']) && !empty($params['disbursed']), function ($query) use($params){
           if($params['disbursed'] == 1){
                return $query->whereNotNull('disburse_date');
            }else{
                return $query->whereNull('disburse_date');
            }
        })
        ->orderBy('gross', 'desc')
        ->get();
        $benefit = $this->getBenefitData(['bank_no']);
        return collect($data)->map(function($q) use ($benefit){
            $q->bank_no = $benefit[$q->as_id]->bank_no??'';
            return $q;
        });
    }

    protected function getBenefitData($selected=''){
        $query = DB::table('hr_benefits')
        ->select('ben_as_id');
        if($selected != null){
            $query->addSelect($selected);
        }
        return $query->get()->keyBy('ben_as_id');

    }

    public function makeSummarySalary($data)
    {
        $data = collect($data);
        $sum  = (object)[];
        $sum->totalOt          = $data->where('ot_status', 1)->count();
        
        $sum->totalNonot       = $data->where('ot_status', 0)->count();
        $sum->totalNonotAmount = $data->where('ot_status', 0)->sum('total_payable');
        $sum->totalOtHour      = $data->where('ot_status', 1)->sum('ot_hour');
        $sum->totalGrossPay    = $data->sum('gross');
        $sum->totalSalary      = $data->sum('total_payable');
        $sum->tSalaryPayable   = $data->sum('salary_payable');
        $sum->totalCashSalary  = $data->sum('cash_payable');
        $sum->totalBankSalary  = $data->sum('bank_payable');
        $sum->totalStamp       = $data->sum('stamp');
        $sum->totalTax         = $data->sum('tds');
        $sum->totalAttBonus    = $data->sum('attendance_bonus');
        $sum->totalAdvanceAmount = $data->sum('partial_amount');
        $sum->totalEmployees   = $data->count();
        $sum->totalFood        = $data->sum('food_deduct');
        $sum->totalOTHourAmount   = $data->sum(function ($s) {
                                    return ($s->ot_hour * $s->ot_rate);
                                });
        $sum->totalOtAmount    = $data->where('ot_status', 1)->sum('total_payable') - $sum->totalOTHourAmount;
        return $sum;
    }

    public function makeEmployeeBenefitValue($value='')
    {
        $addDeduct = $this->getEmployeeSalaryAddDeduct($value);
        $stamp = $this->getEmployeeStampAmount($value);
        $attBonus = $this->getEmployeeAttendanceBonus($value);
        $salaryAdjust = $this->getEmployeeSalaryAdjust($value);
        $value = array_merge($value, $addDeduct, $stamp, $attBonus, $salaryAdjust);
        $salaryPayable = $this->getEmployeeSalaryPayable($value);

        $value = array_merge($value, $salaryPayable);
        $cashBank = $this->getEmployeeSalaryCashBank($value);
        return array_merge($value, $cashBank);
    }

    public function getEmployeeSalaryAddDeduct($value='')
    {
        $getAddDeduct = SalaryAddDeduct::
            where('associate_id', $value['associate_id'])
            ->where('month', '=', $value['month'])
            ->where('year', '=', $value['year'])
            ->first();
        if($getAddDeduct != null){
            $row['deductCost'] = ($getAddDeduct->advp_deduct + $getAddDeduct->cg_deduct + $getAddDeduct->food_deduct + $getAddDeduct->others_deduct);
            $row['deductSalaryAdd'] = $getAddDeduct->salary_add;
            $row['productionBonus'] = $getAddDeduct->bonus_add;
            $row['deductId'] = $getAddDeduct->id;
        }else{
            $row['deductCost'] = 0;
            $row['deductSalaryAdd'] = 0;
            $row['deductId'] = null;
            $row['productionBonus'] = 0;
        }
        return $row;
    }

    public function getEmployeeStampAmount($value='')
    {
        $stamp = 10;
        if($value['ben_cash_amount'] == 0 && $value['as_emp_type_id'] == 3){
            $stamp = 0;
        }
        return ['stamp'=>$stamp];
    }

    public function getEmployeeAttendanceBonus($value='')
    {         
        /*
         *get unit wise bonus rules 
         *if employee joined this month, employee will get bonus 
          only he/she joined at 1
        */ 
        $attBonus = 0;
        if(($value['empdojMonth'] == $value['yearMonth'] && date('d', strtotime($value['as_doj'])) > 1) || $value['partial'] == 1 ){
            $attBonus = 0;
        }else{
            $getBonusRule = AttendanceBonusConfig::
            where('unit_id', $value['as_unit_id'])
            ->first();
            if($getBonusRule != null){
                $lateAllow = $getBonusRule->late_count;
                $leaveAllow = $getBonusRule->leave_count;
                $absentAllow = $getBonusRule->absent_count;
            }else{
                $lateAllow = 3;
                $leaveAllow = 1;
                $absentAllow = 1;
            }
            
            if ($value['lateCount'] <= $lateAllow && $value['leaveCount'] <= $leaveAllow && $value['absentCount'] <= $absentAllow && $value['as_emp_type_id'] == 3) {
                $lastMonth = date('m', strtotime('-1 months', strtotime($value['year'].'-'.$value['month'].'-01')));
                if($lastMonth == '12'){
                    $value['year'] = $value['year'] - 1;
                }
                $getLastMonthSalary = HrMonthlySalary::
                    where('as_id', $value['associate_id'])
                    ->where('month', $lastMonth)
                    ->where('year', $value['year'])
                    ->first();
                if (($getLastMonthSalary != null) && ($getLastMonthSalary->attendance_bonus > 0)) {
                    if(isset($getBonusRule->second_month)) {
                        $attBonus = $getBonusRule->second_month;
                    }
                } else {
                    if(isset($getBonusRule->first_month)) {
                        $attBonus = $getBonusRule->first_month;
                    }
                }
            }
        }
        return ['attBonus'=>$attBonus];
    }

    public function getEmployeeSalaryAdjust($value='')
    {
        $salaryAdjust = SalaryAdjustMaster::getCheckEmployeeIdMonthYearWise($value['associate_id'], $value['month'], $value['year']);

        $leaveAdjust = 0;
        $incrementAdjust = 0;
        $salaryAdd = 0;
        $bonusAdjust = 0;
        if($salaryAdjust != null){
            $adj = DB::table('hr_salary_adjust_details')
                ->where('salary_adjust_master_id', $salaryAdjust->id)
                ->get();

            $leaveAdjust = collect($adj)->where('type',1)->sum('amount');
            $incrementAdjust = collect($adj)->where('type',3)->sum('amount');
            $bonusAdjust = collect($adj)->where('type',4)->sum('amount');
            $salaryAdd = collect($adj)->where('type',2)->sum('amount');
            
        }

        return [
            'leaveAdjust' => ceil((float) $leaveAdjust),
            'incrementAdjust' => ceil((float) $incrementAdjust),
            'salaryAdd' => ceil((float) $salaryAdd),
            'bonusAdjust' => ceil((float) $bonusAdjust)
        ];
        
    }

    public function getEmployeeSalaryPayable($value='')
    {
        $perDayBasic = $value['ben_basic'] / 30;
        $getAbsentDeduct = (int)($value['absentCount'] * $perDayBasic);
        $getHalfDeduct = (int)($value['halfCount'] * ($perDayBasic / 2));
        $overtime_rate = number_format((($value['ben_basic']/208)*2), 2, ".", "");
        $overtime_rate = ($value['as_ot']==1)?($overtime_rate):0;

        if(($value['empdojMonth'] == $value['yearMonth'] && date('d', strtotime($value['as_doj'])) > 1) || $value['monthDayCount'] > $value['totalDay'] || $value['partial'] == 1){
            $perDayGross   = $value['ben_current_salary']/$value['monthDayCount'];
            $totalGrossPay = ($perDayGross * $value['totalDay']);
            
        }else{
            $totalGrossPay = $value['ben_current_salary'];
        }

        $salaryPayable = $totalGrossPay - ($getAbsentDeduct + $getHalfDeduct + $value['deductCost'] + $value['stamp']);

        $otAmount = ((float)($overtime_rate) * ($value['otCount']));
        // check and include partial salary
        $partialAmount = PartialSalary::getEmployeeWisePartialAmount($value);
        
        $totalPayable = ceil((float)($salaryPayable + $otAmount + $value['deductSalaryAdd'] + $value['attBonus'] + $value['productionBonus'] + $value['leaveAdjust'] + $value['salaryAdd'] + $value['incrementAdjust'] + $value['bonusAdjust']) - $partialAmount);
        return [
            'salaryPayable' => $salaryPayable,
            'overtime_rate' => $overtime_rate,
            'totalPayable'  => $totalPayable,
            'absentDeduct'  => $getAbsentDeduct,
            'halfDeduct'    => $getHalfDeduct,
            'partialAmount' => $partialAmount
        ];
    }

    public function getEmployeeSalaryCashBank($value='')
    {

        $payStatus = 1; // cash pay
        if($value['ben_bank_amount'] > 0 && $value['ben_cash_amount'] > 0){
            $payStatus = 3; // partial pay
        }elseif($value['ben_bank_amount'] > 0){
            $payStatus = 2; // bank pay
        }

        $tds = $value['ben_tds_amount']??0;
        if($payStatus == 1){
            $tds = 0;
            $cashPayable = $value['totalPayable'];
            $bankPayable = 0; 
        }elseif($payStatus == 2){
            $areaAmount = isset($value['incrementAdjust'])?($value['incrementAdjust'] + $value['bonusAdjust']):0;
            if($areaAmount > 0){
                $cashPayable = $areaAmount;
                $bankPayable = $value['totalPayable'] - $areaAmount;
                $payStatus = 3;
            }else{
                $cashPayable = 0;
                $bankPayable = $value['totalPayable'];
            }
        }else{
            if(isset($value['partialAmount']) && $value['partialAmount'] > 0){
                $bankParcent = round(($value['ben_bank_amount']/$value['ben_current_salary'])*100, 2);
                $payBank = ceil(($value['totalPayable']/100)*$bankParcent);
                $value['ben_bank_amount'] = $payBank;
            }

            if($value['ben_bank_amount'] <= $value['totalPayable']){
                $cashPayable = $value['totalPayable'] - $value['ben_bank_amount'];
                $bankPayable = $value['ben_bank_amount'];
            }else{
                $cashPayable = 0;
                $bankPayable = $value['totalPayable'];
            }
        }

        if($bankPayable > 0 && $tds > 0 && $bankPayable > $tds){
            $bankPayable = $bankPayable - $tds;
        }else{
            $tds = 0;
        }

        return [
            'payStatus' => $payStatus,
            'cashPayable' => $cashPayable,
            'bankPayable' => $bankPayable,
            'tds' => $tds
        ];
    }

    public function slaryStore($value='')
    {
        try {
            HrMonthlySalary::updateOrCreate(
            [
                'as_id' => $value['associate_id'],
                'month' => $value['month'],
                'year' => $value['year']
            ],
            [
                'ot_status' => $value['as_ot'],
                'unit_id' => $value['as_unit_id'],
                'designation_id' => $value['as_designation_id'],
                'sub_section_id' => $value['as_subsection_id'],
                'location_id' => $value['as_location'],
                'pay_type' => ($value['payStatus'] != 1?$value['bank_name']:''),
                'gross' => $value['ben_current_salary'],
                'basic' => $value['ben_basic'],
                'house' => $value['ben_house_rent'],
                'medical' => $value['ben_medical'],
                'transport' => $value['ben_transport'],
                'food' => $value['ben_food'],
                'late_count' => $value['lateCount'],
                'present' => $value['presentCount'],
                'holiday' => $value['holidayCount'],
                'absent' => $value['absentCount'],
                'leave' => $value['leaveCount'],
                'absent_deduct' => $value['absentDeduct'],
                'half_day_deduct' => $value['halfDeduct'],
                'salary_add_deduct_id' => $value['deductId'],
                'salary_payable' => $value['salaryPayable'],
                'ot_rate' => $value['overtime_rate'],
                'ot_hour' => $value['otCount'],
                'attendance_bonus' => $value['attBonus'],
                'production_bonus' => $value['productionBonus'],
                'leave_adjust' => $value['leaveAdjust'],
                'stamp' => $value['stamp'],
                'pay_status' => $value['payStatus'],
                'emp_status' => $value['as_status'],
                'partial_amount' => $value['partialAmount'],
                'total_payable' => $value['totalPayable'],
                'cash_payable' => $value['cashPayable'],
                'bank_payable' => $value['bankPayable'],
                'tds' => $value['tds'],
                'roaster_status' => $value['shift_roaster_status']
            ]);
            return $value;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function employeeMonthlySalaryProcess($asId, $month, $year, $totalDay)
    {
        $data['type'] = 'error';
        $getEmployee = Employee::where('as_id', $asId)->where('as_status', 1)->first(['as_id', 'as_doj', 'as_unit_id', 'associate_id', 'as_status_date', 'as_ot', 'shift_roaster_status', 'as_emp_type_id', 'as_designation_id', 'as_subsection_id', 'as_location', 'as_status', 'as_gender']);
        
        $row['month'] = $month;
        $row['year'] = $year;
        $row['yearMonth'] = date('Y-m', strtotime($row['year'].'-'.$row['month']));
        if($getEmployee != null && date('Y-m', strtotime($getEmployee->as_doj)) == $row['yearMonth']){
            if($month == date('m')){
                $row['totalDay'] = date('d');
            }else{
                $row['totalDay'] = Carbon::parse($row['yearMonth'])->daysInMonth;
            }
        }else{
            $row['totalDay'] = $totalDay;
        }
        
        
        try {
            if($getEmployee != null && date('Y-m', strtotime($getEmployee->as_doj)) <= $row['yearMonth']){
                $row['tableName'] = get_att_table($getEmployee->as_unit_id);
                $row['unit_id'] = $getEmployee->as_unit_id;
                // check lock month
                $checkLock = monthly_activity_close($row);
                if($checkLock == 1){
                    return 'error';
                }
                // get employee benefit
                $getBenefit = Benefits::getEmployeeAssIdwise($getEmployee->associate_id);
                if($getBenefit == null){
                    return 'error';
                }

                $row = array_merge($row, $getEmployee->toArray());
                // employee basic info
                $startNendInfo = $this->attProcess->getEmployeeMonthStartNEndInfo($row);

                $row = array_merge($row, $startNendInfo);
                // extra gross payment for Full OT and festival Holiday Attendance and Only OT holder
                // only MBM, MBM-2, SRT unit 
                if(in_array($getEmployee->as_unit_id, [1,4,5])){
                    
                    if($getEmployee->as_ot == 1){
                       $rosterPlanner = $this->attProcess->getEmployeeRosterPlannerDateWithKey($row, [0,1,2,'festival']);
                        $presentOtDate = [];
                        $festivalDate = [];
                        $master = '';

                        if((isset($rosterPlanner['presentOt']) && count($rosterPlanner['presentOt']) > 0) || (isset($rosterPlanner['festival']) && count($rosterPlanner['festival']) > 0)){
                            $master = SalaryAdjustMaster::firstOrNew([
                                'associate_id' => $getEmployee->associate_id,
                                'month' => $month,
                                'year' => $year
                            ]);
                            $master->save();

                            // Full Day OT Present Payment
                            if(isset($rosterPlanner['presentOt']) && count($rosterPlanner['presentOt']) > 0){
                                $presentOtDate = array_keys($rosterPlanner['presentOt']);
                                // dd($presentOtDate);
                                $oneGross = ($getBenefit->ben_current_salary/date('t', strtotime($row['yearMonth'].'-01')));
                                foreach($rosterPlanner['presentOt'] as $date => $comment){
                                    
                                    SalaryAdjustDetails::updateOrCreate(
                                    [
                                        'salary_adjust_master_id' => $master->id,
                                        'date' => $date,
                                        'type' => 2,
                                    ],
                                    [
                                        'amount' => number_format((float)$oneGross, 2, '.', ''),
                                        'comment' => ''
                                    ]);

                                }
                                
                            }

                            // festival holiday payment
                            if(isset($rosterPlanner['festival']) && count($rosterPlanner['festival']) > 0){
                                $festivalDate = $rosterPlanner['festival'];
                                // dd($presentOtDate);
                                $twoGross = ($getBenefit->ben_current_salary/date('t', strtotime($row['yearMonth'].'-01')))*2;
                                foreach($rosterPlanner['festival'] as $fdate){
                                    
                                    SalaryAdjustDetails::updateOrCreate(
                                    [
                                        'salary_adjust_master_id' => $master->id,
                                        'date' => $fdate,
                                        'type' => 2
                                    ],
                                    [
                                        'amount' => number_format((float)$twoGross, 2, '.', ''),
                                        'comment' => 'festival'
                                    ]);

                                }
                            }
                        }
                        
                        if($master != null){
                            
                            $typeTwoDateM = array_merge($presentOtDate, $festivalDate);
                            $typeTwoDate = array_unique($typeTwoDateM);
                            
                            //remove another date salary adjust
                            SalaryAdjustDetails::
                            join('hr_salary_adjust_master', 'hr_salary_adjust_details.salary_adjust_master_id', 'hr_salary_adjust_master.id')
                            ->where('hr_salary_adjust_master.associate_id', $getEmployee->associate_id)
                            ->where('hr_salary_adjust_master.month', $month)
                            ->where('hr_salary_adjust_master.year', $year)
                            ->where('hr_salary_adjust_details.type', 2)
                            ->whereNotIn('hr_salary_adjust_details.date', $typeTwoDate)
                            ->delete();
                        }else{
                            SalaryAdjustDetails::
                            join('hr_salary_adjust_master', 'hr_salary_adjust_details.salary_adjust_master_id', 'hr_salary_adjust_master.id')
                            ->where('hr_salary_adjust_master.associate_id', $getEmployee->associate_id)
                            ->where('hr_salary_adjust_master.month', $month)
                            ->where('hr_salary_adjust_master.year', $year)
                            ->where('hr_salary_adjust_details.type', 2)
                            ->delete();
                        } 
                    }else{    
                        //remove another date salary adjust
                        SalaryAdjustDetails::
                        join('hr_salary_adjust_master', 'hr_salary_adjust_details.salary_adjust_master_id', 'hr_salary_adjust_master.id')
                        ->where('hr_salary_adjust_master.associate_id', $getEmployee->associate_id)
                        ->where('hr_salary_adjust_master.month', $month)
                        ->where('hr_salary_adjust_master.year', $year)
                        ->where('hr_salary_adjust_details.type', 2)
                        ->delete();  
                    }
                    
                }
                
                // attendance count like - present, holiday, leave, absent, late etc.
                $attCount = $this->attProcess->makeEmployeeAttendanceCount($row);
                $row = array_merge($row, $getBenefit->toArray(), $attCount);
                // all benefit calculate for pay
                $benefit = $this->makeEmployeeBenefitValue($row);
                // salary store
                $salaryInfo = $this->slaryStore($benefit);

                $data['value'] = $salaryInfo;
            }

            $data['type'] = 'success';
            $data['message'] = 'Successfully updated';
            return $data;
        } catch (\Exception $e) {
            DB::table('error')->insert(['msg' => $asId.' '.$e->getMessage()]);
            $data['message'] = $e->getMessage();
            return $data;
        }
    }
}