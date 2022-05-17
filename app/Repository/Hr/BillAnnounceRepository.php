<?php

namespace App\Repository\Hr;

use App\Models\Employee;
use App\Models\Hr\Benefits;
use App\Models\Hr\BillSettings;
use App\Models\Hr\BillSpecialSettings;
use App\Models\Hr\Bills;
use App\Models\Hr\Shift;
use App\Repository\Hr\ShiftRepository;
use DB;
use Illuminate\Support\Collection;

class BillAnnounceRepository
{
    protected $shiftRepository;

    public function __construct(ShiftRepository $shiftRepository)
    {
        ini_set('zlib.output_compression', 1);
        $this->shiftRepository = $shiftRepository;
    }
    
    public function billAnnounceStoreProcess($value='')
    {
        $value['code'] = $this->getBillCode($value);
        // update preview bill unit base
        BillSettings::updatePreviousBillUnitWiseStatus($value);
        // bill store
        $value['bill_setup_id'] = $this->billStore($value);
        // bill special
        $this->billSpecialStore($value);
    }

    public function billStore($input)
    {
        $input['created_by'] = auth()->user()->id;
        return BillSettings::create($input)->id;
    }

    public function billSpecialStore($input)
    {
        $specialData = [];
        if(isset($input['special_rule'])){
            $special['bill_setup_id'] = $input['bill_setup_id'];
            foreach ($input['special_rule'] as $key => $value) {
                $special['adv_type'] = $key;
                $totalKey = count($value);
                for ($s=0; $s < $totalKey; $s++) { 
                    $keyValue = $value[$s];
                    if($keyValue['id'] != ''){
                        
                        if($key == 'out_time' || $key == 'working_hour'){
                            $keyValue['pay_type'] = 0;
                            $keyValue['duration'] = 0;
                        }
                        $special['parameter'] = $keyValue['id'];
                        $special['amount'] = $keyValue['amount'];
                        $special['pay_type'] = $keyValue['pay_type'];
                        $special['duration'] = $keyValue['duration'];
                        $special['start_date'] = $input['start_date'];
                        $special['end_date']  = $input['end_date'];
                        $special['created_by']  = auth()->user()->id;
                        $specialData[] = $special;
                    }
                }
            }
            if(count($specialData) > 0){
                BillSpecialSettings::insert($specialData);
            }
        }
    }

    protected function getBillCode($value='')
    {
        $lastCode = BillSettings::checkUnitTypeWiseExistsCode($value);
        $unitTypeMix = $value['unit_id'].$value['bill_type_id'];
        if($lastCode != null){
            $billCode = explode($unitTypeMix, $lastCode);
            $adjustNo = ((int)$billCode[1]+1)??1;
        }else{
            $adjustNo = 1;
        }
        return $this->getCheckUniqueCode($unitTypeMix, $adjustNo);
    }

    protected function getCheckUniqueCode($value, $no)
    {
        $code = $value.$no;
        $checkCode = BillSettings::checkExistsCode($code);
        if(empty($checkCode)){
            return $code;
        }else{
            $no = ($no+1);
            return $this->getCheckUniqueCode($value, $no);
        }
    }

    public function processBillAnncement($value='')
    {
        $employee = Employee::getEmployeeAsIdWiseSelectedField($value['as_id'], ['associate_id', 'as_unit_id', 'as_location', 'as_department_id', 'as_designation_id', 'as_section_id', 'as_subsection_id']);
        $value = array_merge($value, (array) $employee);
        return $this->makeBillProcess($value);
    }

    public function makeBillProcess($value='', $shiftBill='')
    {

        try {
            if($shiftBill == ''){
                $shiftBill = $this->shiftRepository->getShiftPropertiesByTodaysSingleCode($value['hr_shift_code'], $value['in_date']);
            }
            
            $shift = $shiftBill->time;
            $billTypeId = $shiftBill->bills;

            // remove extra bill
            $this->removeUndeclearBill($billTypeId, $value);
            // bill entry
            $d = $this->getBillData($billTypeId, $value, $shift);
            return 'success';
        } catch (\Exception $e) {
            DB::table('error')->insert(['msg' => $value['as_id'].' - bill - '.$e->getMessage()]);
            return 'error';
        }
    }

    protected function getBillTypes($value='')
    {
        return DB::table('hr_shift_bills')
        ->select('hr_shift_id', 'hr_bill_type_id')
        ->where('hr_shift_id', $value['hr_shift_id'])
        ->where('start_date', '<=', $value['in_date'])
        ->whereNull('end_date')
        ->orWhere('end_date', '>=', $value['in_date'])
        ->orderBy('id', 'desc')
        ->groupBy(['hr_shift_id', 'hr_bill_type_id'])
        ->pluck('hr_bill_type_id');
    }

    protected function removeUndeclearBill($billTypes, $value)
    {
        return DB::table('hr_bill')
        ->where('as_id', $value['as_id'])
        ->where('bill_date', $value['in_date'])
        ->whereNotIn('bill_type', $billTypes)
        ->delete();
    }

    public function removeBillAnncement($value)
    {
        return DB::table('hr_bill')
        ->where('as_id', $value['as_id'])
        ->where('bill_date', $value['in_date'])
        ->delete();
    }

    protected function getBillData($billTypes, $value, $shift)
    {
        $bills = BillSettings::with('available_special')
        ->where('unit_id', $value['as_unit_id'])
        ->whereIn('bill_type_id', $billTypes)
        ->where('start_date', '<=', $value['in_date'])
        ->whereNull('end_date')
        ->orWhere('end_date', '>=', $value['in_date'])
        ->where('status', 1)
        ->groupBy('bill_type_id')
        ->orderBy('id', 'desc')
        ->get();
        foreach($bills as $bill){
            $prePriority = 0;
            $payAmount = $this->billProcessRuleBaseAmount($bill, $value, $shift);
            foreach($bill->available_special as $special){
                $amount = 0;
                $priority = 0;
                if($special->adv_type == 'out_time'){
                    if(strtotime(date('H:i:s', strtotime($value['out_time']))) >= strtotime($special->parameter)){
                        $priority = 1;
                        $payBill = $special->amount;
                    }
                }

                if($special->adv_type == 'working_hour'){
                    if($value['out_time'] != null && $value['out_time'] != ''){
                        $shiftStart = $value['in_date'].' '.$shift->hr_shift_start_time;
                        $timestamp1 = strtotime($shiftStart);
                        $timestamp2 = strtotime($value['out_time']);
                        $hour = (abs($timestamp2 - $timestamp1)/(60*60));
                        if((float)$hour >= (float)$special->parameter){
                            $priority = 2;
                            $amount = $special->amount;
                        }
                    }
                }

                if($special->adv_type == 'as_location'){
                    if($special->parameter == $value['as_location']){
                        $priority = 3;
                        $amount = $this->billProcessRuleBaseAmount($special, $value, $shift);
                    }
                }

                if($special->adv_type == 'as_department_id'){
                    if($special->parameter == $value['as_department_id']){
                        $priority = 4;
                        $amount = $this->billProcessRuleBaseAmount($special, $value, $shift);
                    }
                }

                if($special->adv_type == 'as_designation_id'){
                    if($special->parameter == $value['as_designation_id']){
                        $priority = 5;
                        $amount = $this->billProcessRuleBaseAmount($special, $value, $shift);
                    }
                }

                if($special->adv_type == 'as_section_id'){
                    if($special->parameter == $value['as_section_id']){
                        $priority = 6;
                        $amount = $this->billProcessRuleBaseAmount($special, $value, $shift);
                    }
                }

                if($special->adv_type == 'as_subsection_id'){
                    if($special->parameter == $value['as_subsection_id']){
                        $priority = 7;
                        $amount = $this->billProcessRuleBaseAmount($special, $value, $shift);
                    }
                }
                if($priority > 0 && $prePriority < $priority){
                    $payAmount = $amount;
                    $prePriority = $priority;
                }
            }
            
            // insert/update/delete data
            if($payAmount > 0){
                Bills::updateOrCreate([
                    'as_id' => $value['as_id'],
                    'bill_date' => $value['in_date'],
                    'bill_type' => $bill->bill_type_id
                ],
                [
                    'amount' => $payAmount
                ]);
            }else{
                DB::table('hr_bill')
                ->where('as_id', $value['as_id'])
                ->where('bill_date', $value['in_date'])
                ->where('bill_type', $bill->bill_type_id)
                ->delete();
            } 

        }
        return 'success';
    }

    protected function billProcessRuleBaseAmount($bill, $value='', $shift)
    {
        $payBill = 0;
        // 1 = present
        if($bill->pay_type == 1){
            if(($value['in_time'] != null && $value['in_time'] != '') || ($value['out_time'] != null && $value['out_time'] != '')){
                $payBill = $bill->amount;
            }
        }

        // 2 = Working Hour
        if($bill->pay_type == 2){
            if($value['out_time'] != null && $value['out_time'] != ''){
                $shiftStart = $value['in_date'].' '.$shift->hr_shift_start_time;
                $timestamp1 = strtotime($shiftStart);
                $timestamp2 = strtotime($value['out_time']);
                $hour = abs($timestamp2 - $timestamp1)/(60*60);
                if((float)$hour >= (float)$bill->duration){
                    $payBill = $bill->amount;
                }
            }
            
        }

        // 3 = OT Hour
        if($bill->pay_type == 3){
            if($value['ot_hour'] >= $bill->duration){
                $payBill = $bill->amount;
            }
        }

        // 4 = Out-time
        if($bill->pay_type == 4){
            if(strtotime(date('H:i:s', strtotime($value['out_time']))) >= strtotime($bill->duration)){
                $payBill = $bill->amount;
            }
        }
        return $payBill;
    }

    // report
    public function getBillByRange($value='')
    {
        $query = DB::table('hr_bill')
        ->whereBetween('bill_date', [$value->from_date, $value->to_date]);
        if($value->bill_type != null && $value->bill_type != ''){
            $query->where('bill_type', $value->bill_type);
        }
        if($value->pay_type != null && $value->pay_type != ''){
            $query->where('pay_status', $value->pay_type);
        }
        return $query->get();
    }

    public function getBillGroupByEmpId($value='')
    {
        $getBill = $this->getBillByRange($value);
        // group by and employee
        $getBillGroup = collect($getBill)->groupBy('as_id')->map(function($q) {
            $b = $q->first();
            return (object)[
                'as_id' => $b->as_id,
                'bill_count' => $q->count(),
                'amount' => $q->sum('amount'),
                'paid_amount' => $q->where('pay_status', 1)->sum('amount')
            ];
        });
        return $getBillGroup;
    }

    public function getBillByFilter($input, $dataRow, $employee)
    {   
        // get benefit
        $getBenefit = Benefits::getBenefitDataByFields(['ben_current_salary', 'bank_name', 'bank_no', 'ben_bank_amount', 'ben_cash_amount']);
        return collect($dataRow)->map(function($q) use ($employee, $getBenefit) {
            $emp = $employee[$q->as_id];
            if($emp != ''){
                $benefit = $getBenefit[$emp->associate_id]??'';
                $q->as_department_id = $emp->as_department_id??'';
                $q->as_designation_id = $emp->as_designation_id??'';
                $q->as_section_id = $emp->as_section_id??'';
                $q->as_subsection_id = $emp->as_subsection_id??'';
                $q->as_line_id = $emp->as_line_id??'';
                $q->as_floor_id = $emp->as_floor_id??'';
                $q->as_location = $emp->as_location??'';
                $q->associate_id = $emp->associate_id??'';
                $q->as_name = $emp->as_name??'';
                $q->as_unit_id = $emp->as_unit_id??'';
                $q->ot_status = $emp->as_ot??'';
                $q->emp_status = $emp->as_status??'';
                $q->gross = $benefit->ben_current_salary??0;
                $q->cash_payable = $benefit->ben_cash_amount??0;
                $q->bank_payable = $benefit->ben_bank_amount??0;
                $q->bank_no = $benefit->bank_no??0;
                $q->bank_name = $benefit->bank_name??'';
                
                return $q;
            }
        });
    }

    public function getBillSummerReport($input, $data)
    {
        $result['summary']      = $this->makeSummaryBill($data);

        $list = collect($data)
            ->groupBy($input['report_group'],true);
        if(!empty($input['selected'])){
            $input['report_format'] = 0;
        }

        if($input['report_format'] == 1){
            $list = $list->map(function($q){
                $q = collect($q);
                $sum  = (object)[];
                $sum->ot            = $q->where('ot_status', 1)->count();
                $sum->nonot         = $q->where('ot_status', 0)->count();
                $sum->nonotAmount   = $q->where('ot_status', 0)->sum('amount');
                $sum->otAmount      = $q->where('ot_status', 1)->sum('amount');
                $sum->cashPayable   = $q->where('bank_payable', 0)->where('cash_payable', '>', 0)->sum('amount');
                $sum->bankPayable   = $q->where('bank_payable', '>', 0)->sum('amount');
                $sum->paidAmount    = $q->sum('paid_amount');
                $sum->billAmount    = $q->sum('amount');
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
        $result['billType']    = bill_type_by_id();
        return $result;
    }

    protected function makeSummaryBill($data)
    {
        $data = collect($data);
        $sum  = (object)[];
        $sum->totalOt          = $data->where('ot_status', 1)->count();
        $sum->totalNonot       = $data->where('ot_status', 0)->count();
        $sum->totalNonotAmount = $data->where('ot_status', 0)->sum('amount');
        $sum->totalBill        = $data->sum('amount');
        $sum->totalCash        = $data->where('bank_payable', 0)->where('cash_payable', '>', 0)->sum('amount');
        $sum->totalBank        = $data->where('bank_payable','>', 0)->sum('amount');
        $sum->totalEmployees   = $data->count();
        $sum->totalOtAmount    = $data->where('ot_status', 1)->sum('amount');
        $sum->totalPaidAmount  = $data->sum('paid_amount');;
        return $sum;
    }

}