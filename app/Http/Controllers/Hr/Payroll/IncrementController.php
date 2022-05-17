<?php

namespace App\Http\Controllers\Hr\Payroll;

use App\Exports\Hr\IncrementExport;
use App\Helpers\Custom;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Employee;
use App\Models\Hr\Benefits;
use App\Models\Hr\Designation;
use App\Models\Hr\EmpType;
use App\Models\Hr\FixedSalary;
use App\Models\Hr\Increment;
use App\Models\Hr\IncrementType;
use App\Models\Hr\OtherBenefitAssign;
use App\Models\Hr\OtherBenefits;
use App\Models\Hr\Promotion;
use App\Models\Hr\SalaryAdjustDetails;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\SalaryStructure;
use App\Models\Hr\Unit;
use App\Packages\NumberLakh\NumberLakh;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Validator,DB, DataTables, ACL,Auth;

class IncrementController extends Controller
{
    public function index(Request $request)
    {
        if(auth()->user()->canany(['Increment Approval','Increment Process'])){
            return redirect('hr/payroll/increment-approval');
        }else if(auth()->user()->can('Manage Increment')){
            return redirect('hr/payroll/increment-process');
        }

        $unitList  = collect(unit_by_id())->pluck('hr_unit_name', 'hr_unit_id');
        $floorList= [];
        $lineList= [];
 
        $areaList  = collect(area_by_id())->pluck('hr_area_name', 'hr_area_id');

        $deptList= [];

        $sectionList= [];

        $subSectionList= [];

        $employeeTypes  = collect(emp_type_by_id())->pluck('hr_emp_type_name', 'emp_type_id');

        $data['salaryMin']      = 0;
        $data['salaryMax']      = Benefits::getSalaryRangeMax();


        return view('hr.payroll.increment.index', compact('unitList','floorList','lineList','areaList','deptList','sectionList','subSectionList', 'data','employeeTypes'));

    }

    public function incrementList()
    {
        $unitList  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_name', 'hr_unit_id');
        return view('hr/payroll/increment_list', compact('unitList'));
    }

    public function incrementListData(Request $request)
    {

        $year = $request->year??date('Y');
        $designation = designation_by_id();
        $department = department_by_id();
        $section = section_by_id();
        $unit = unit_by_id();

        $data= DB::table('hr_increment AS inc')
                ->select([
                    'inc.*',
                    'b.as_name',
                    'b.as_oracle_code',
                    'b.as_emp_type_id',
                    'b.as_gender',
                    'b.as_doj',
                    'b.as_section_id',
                    'b.as_department_id',
                    'b.as_designation_id',
                    'b.as_unit_id',
                    'bn.hr_bn_associate_name',
                    'ben.ben_current_salary',
                    'ben.ben_basic',
                    'ben.ben_house_rent',
                    'ben.ben_medical',
                    'ben.ben_transport',
                    'ben.ben_food'
                ])
                ->leftJoin('hr_as_basic_info AS b', 'b.associate_id', 'inc.associate_id')
                ->where('applied_date', '>=', $year.'-01-01')
                ->leftJoin('hr_employee_bengali AS bn', 'bn.hr_bn_associate_id', 'b.associate_id')
                ->leftJoin('hr_benefits AS ben', 'ben.ben_as_id', 'b.associate_id')
                ->where('applied_date', '<=', $year.'-12-31')
                ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                ->whereIn('b.as_location', auth()->user()->location_permissions())
                ->orderBy('inc.effective_date','desc')
                ->orderBy('inc.created_at','desc')
                ->get();

                // dd($data);
        $incrementHistory = DB::table('hr_increment_approval')
        ->where('effective_date', '>=', $year.'-01-01')
        ->where('effective_date', '<=', $year.'-12-31')
        ->where('status', 1)
        ->get()
        ->keyBy('associate_id');

        $promotionHistory = DB::table('hr_promotion')
        ->where('effective_date', '>=', $year.'-01-01')
        ->where('effective_date', '<=', $year.'-12-31')
        ->where('status', 1)
        ->get()
        ->keyBy('associate_id');

        $perm = check_permission('Manage Increment');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('hr_unit_name', function($data) use ($unit){
                return $unit[$data->as_unit_id]['hr_unit_name']??'';
            })
            ->addColumn('effective_date', function($data){
                return date('Y-m-d', strtotime($data->effective_date));
            })
            ->addColumn('action', function ($data) use ($perm, $designation,$section,$department, $incrementHistory, $promotionHistory) {
                $button = '<div class=\"btn-group\">';
                if($perm){

                    //$button .= "<a type=\"button\" href=".url('hr/payroll/increment_edit/'.$data->id)." class=\"btn btn-sm btn-primary\"><i class=\"fa fa-pencil\"></i></a>";
                    if($data->status == 1){
                        
                        $inType = $data->increment_type==2?'Yearly':'Special';
                        $dearStatus = $data->as_gender == 'Male'?'Mr.':'Mrs.';
                        $na = explode(' ', $data->as_name);
                        $pro_head = '';
                        $promotionContent = '';
                        $pre_desg_name = $designation[$data->as_designation_id]['hr_designation_name']??''; 
                        if(isset($incrementHistory[$data->associate_id]) && $incrementHistory[$data->associate_id]->designation_id != ''){
                            $pro_designation = $incrementHistory[$data->associate_id]->designation_id;

                            if(isset($promotionHistory[$data->associate_id]) && $promotionHistory[$data->associate_id]->current_designation_id == $pro_designation){
                                $pre_desg = $promotionHistory[$data->associate_id]->previous_designation_id;
                                $pre_desg_name = $designation[$pre_desg]['hr_designation_name']??'';
                                $cur_desg_name = $designation[$pro_designation]['hr_designation_name']??'';
                                $promotionContent = '& promotion from '.$pre_desg_name.' to '.$cur_desg_name;
                                $pro_head = '& Promotion';
                            }
                        }
                        $nLakh = new NumberLakh;
                        $letter = array(
                            'name' => $data->as_name,
                            'designation' => $pre_desg_name,
                            'salary' => bn_money($data->current_salary),
                            'increment_amount' => bn_money($data->increment_amount).'/-'.' (Taka '.$nLakh->numToWord($data->increment_amount).' Only)',
                            'new_salary' => bn_money(($data->current_salary + $data->increment_amount)),
                            'department' => $department[$data->as_department_id]['hr_department_name']??'',
                            'effective_date' => date('d-F-Y', strtotime($data->effective_date)),
                            'associate_id' => $data->associate_id,
                            'doj' => date('d-F-Y', strtotime($data->as_doj)),
                            'gender' => $data->as_gender,
                            'dear_name' => 'Dear '.$dearStatus.' '.end($na).',',
                            'prev_desg' => $designation[$data->as_designation_id]['hr_designation_name']??'',
                            'basic' => bn_money($data->ben_basic),
                            'house_rent' => bn_money($data->ben_house_rent),
                            'food_allowance' => bn_money($data->ben_food),
                            'medical_allowance' => bn_money($data->ben_medical),
                            'conveyance_allowance' => bn_money($data->ben_transport),
                            'salary_inword' => 'Taka '.$nLakh->numToWord($data->ben_current_salary).' Only',
                            'grand_total' => bn_money($data->ben_current_salary),
                            'inType' => $inType,
                            'pro_designation' => $promotionContent,
                            'pro_head' => $pro_head
                            
                        );
                        $cur_desg_name='';
                        $dearStatus = $data->as_gender == 'Male'?'জনাব,':'জনাবা,';
                        if(isset($promotionHistory[$data->associate_id]) && $promotionHistory[$data->associate_id]->effective_date == $data->effective_date){

                            $pre_desg = $promotionHistory[$data->associate_id]->previous_designation_id;

                            $cur_desg_id = $promotionHistory[$data->associate_id]->current_designation_id;
                            
                            $pre_desg_name=$designation[$pre_desg]['hr_designation_name_bn']??'';

                            $cur_desg_name=$designation[$cur_desg_id]['hr_designation_name_bn']??'';

                            // dd($cur_desg_name);

                            $type = 'both';

                        }else{
                            $type = 'single';
                        }


                        $letter2 = array(
                            'name' => $data->hr_bn_associate_name,
                            'Previous_salary' => eng_to_bn(bn_money($data->current_salary)),
                            'increment_amount' => eng_to_bn(bn_money($data->increment_amount)),
                            'new_salary' => eng_to_bn(bn_money(($data->current_salary + $data->increment_amount))),
                            'designation' => $designation[$data->as_designation_id]['hr_designation_name_bn']??'',
                            'section' => $section[$data->as_section_id]['hr_section_name_bn']??'',
                            'effective_date' => eng_to_bn($data->effective_date),
                            'associate_id' => $data->associate_id,
                            'pre_desg_name' => $pre_desg_name,
                            'cur_desg_name' => $cur_desg_name,
                            'dearStatus2' => $dearStatus,
                            'type' => $type
                        );


                        $button .=" <button type=\"button\" onclick='printEnLetter(".json_encode($letter).")' class=\"btn btn-sm btn-success\"data-toggle='tooltip' data-placement='top' title='' data-original-title='Letter English'><i class=\"fa fa-print\"></i></button> 
                        <button type=\"button\" onclick='printBnLetter(".json_encode($letter2).")' class=\"btn btn-sm btn-warning\"data-toggle='tooltip' data-placement='top' title='' data-original-title='Letter Bangla'>  <i class=\"fa fa-print\"></i></button>";

                    }
                }

                $button .= '</div>';

                return $button;
            })
            ->addColumn('designation', function ($data) use ($designation) {
                return $designation[$data->as_designation_id]['hr_designation_name']??'';
            })
            ->editColumn('increment_type', function($data){
                return $data->increment_type == 2?'Yearly':'Special';
            })
            ->rawColumns(['action','designation','increment_type'])
            ->make(true);  
                             
    }

    public function storeIncrement(Request $request)
    {
        $created_by= Auth::user()->associate_id;

        if(empty($request->associate_id) || !is_array($request->associate_id))
        {
            return back()
                ->withInput()
                ->with('error', 'Please select at least one associate.');
        }
        
        // Declare and define two dates 
        $date1 = strtotime($request->applied_date);  
        $date2 = strtotime($request->effective_date);  
        //extract years
        $year1 = date('Y', $date1);
        // $year2 = date('Y', $date2);
        $year2 = date('Y');
        //extract month
        $month1 = date('m', $date1);
        // $month2 = date('m', $date2);
        $month2 = date('m');
        //month difference
        $month_diff = (($year2 - $year1) * 12) + ($month2 - $month1);

        for($i=0; $i<sizeof($request->associate_id); $i++)
        {
            $salary= DB::table('hr_benefits')
                            ->where('ben_as_id', $request->associate_id[$i])
                            ->pluck('ben_current_salary')
                            ->first();

            $doj= DB::table('hr_as_basic_info')
                    ->where('associate_id',$request->associate_id[$i] )
                    ->pluck('as_doj')
                    ->first();
            $eligible_at = date("Y-m-d", strtotime("$doj + 1 year"));
            // $eligible_at = $request->elligible_date;        

            $increment= new Increment();
            $increment->associate_id = $request->associate_id[$i] ;
            $increment->current_salary = $salary;
            $increment->increment_type = $request->increment_type;
            $increment->increment_amount = $request->increment_amount ;
            $increment->amount_type = $request->amount_type ;
            $increment->applied_date = $request->applied_date ;
            $increment->eligible_date = $eligible_at ;
            $increment->effective_date = $request->effective_date ;
            $increment->status = 0 ;
            $increment->created_by = $created_by;
            $increment->created_at = date('Y-m-d H:i:s') ;
            $increment->save();

            log_file_write("Increment Entry Saved", $increment->id);


            //Keeping the not given increment amount---- SalaryAdjustMaster, SalaryAdjustDetails
             $basic = DB::table('hr_benefits')
                            ->where('ben_as_id', $request->associate_id[$i])
                            ->pluck('ben_basic')
                            ->first();
             if($request->amount_type == 1){
                    $_amount = $request->increment_amount;
                }
             else{
                    $_amount = ($basic/100)*$increment->increment_amount;
                }

             $y = (int)$year1;
             $m = (int)$month1;

             for($j=0; $j<$month_diff; $j++ ){
                        $master = new SalaryAdjustMaster();
                        $master->associate_id = $request->associate_id[$i];
                        if($m > 12){
                            $m=1;
                            $y+=1;
                            $master->month    = $m;
                            $master->year     = $y;
                            $m+=1;
                        }
                        else{
                            $master->month    = $m;
                            $master->year     = $y;
                            $m+=1;
                        }
                        
                        $master->save();

                        $detail = new SalaryAdjustDetails();
                        $detail->salary_adjust_master_id = $master->id;
                        $detail->date                    = date('Y-m-d');
                        $detail->amount                  = $_amount;
                        $detail->type                    = 3;
                        $detail->save();
                    
             }   
            
        }


        return back()
            ->with('success', "Increment Saved Successfully!");
    }

    //Edit Increment
    public function editIncrement($id){

        $unitList = Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_name', 'hr_unit_id');
        $employeeTypes  = EmpType::where('hr_emp_type_status', '1')->pluck('hr_emp_type_name', 'emp_type_id');
        $typeList= IncrementType::pluck('increment_type', 'id');
        $increment= DB::table('hr_increment AS inc')
                        ->where('id', $id)
                        ->select([
                            'inc.*',
                            'b.as_emp_type_id',
                            'b.as_unit_id'
                        ])
                        ->leftJoin('hr_as_basic_info AS b', 'b.associate_id', 'inc.associate_id')
                        ->first();
        return view('hr/payroll/increment_edit', compact('unitList', 'employeeTypes', 'typeList', 'increment'));
    }

    //Update Increment
    public function updateIncrement(Request $request){

        Increment::where('id', $request->increment_id)
                    ->update([
                          "increment_type"      => $request->increment_type,
                          "applied_date"        => $request->applied_date,
                          "effective_date"      => $request->effective_date,
                          "increment_amount"    => $request->increment_amount,
                          "amount_type"         => $request->amount_type
                    ]);

        log_file_write("Increment Updated", $request->increment_id);
        return back()
            ->with('success', "Increment updated Successfully!");
    }

    //Increment corn job
    public function incrementJobs()
    {
        $data = DB::table('hr_increment as ic')
                ->select('ic.*','a.as_id','a.as_unit_id','a.as_status','b.*')
                ->leftJoin('hr_as_basic_info as a','a.associate_id','ic.associate_id')
                ->leftJoin('hr_benefits as b','b.ben_as_id','ic.associate_id')
                ->where('ic.effective_date','<=',date('Y-m-d'))
                ->where('ic.status', 0)
                ->get();
                
        

        foreach ($data as $key => $d) {
            $gross = $d->current_salary + $d->increment_amount;
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
            DB::table('hr_increment')->where('id', $d->id)->where('associate_id', $d->associate_id)->update(['status' => 1]);

            $tableName = get_att_table($d->as_unit_id);

            if($d->as_status == 1){

                $queue = (new ProcessUnitWiseSalary($tableName, date('m'), date('Y'), $d->as_id, date('d')))
                            ->onQueue('salarygenerate')
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
            }

        }
        
    }


    public function getGazzeteEmployee()
    {

    }

    public function getEmployeeSpecialList(Request $request)
    {

        $data = DB::table('hr_as_basic_info as b')
                ->select(
                    'b.as_id',
                    'b.as_name',
                    'b.associate_id',
                    'b.as_oracle_code',
                    'b.as_emp_type_id',
                    'b.as_unit_id',
                    'b.as_location',
                    'b.as_department_id',
                    'b.as_area_id',
                    'b.as_floor_id',
                    'b.as_line_id',
                    'b.as_section_id',
                    'b.as_subsection_id',
                    'b.as_designation_id', 
                    'b.as_gender',
                    'b.as_doj',
                    'b.as_pic',
                    'ben.ben_current_salary',
                    'i.month',
                    'i.remarks'
                )
                ->leftJoin('hr_benefits as ben','ben.ben_as_id','b.associate_id')
                ->leftJoin('hr_increment_month as i','i.associate_id','b.associate_id')
                ->whereIn('b.associate_id', $request->associate_id)
                ->orderBy('as_oracle_sl','ASC')
                ->get();

        $unit = unit_by_id();
        $location = location_by_id();
        $line = line_by_id();
        $floor = floor_by_id();
        $department = department_by_id();
        $designation = designation_by_id();
        $section = section_by_id();
        $subSection = subSection_by_id();
        $area = area_by_id();

        $date = date('Y-m-01');
        $effective_date = Carbon::parse(date('Y-m-01'));

        $un = 'Employee wise Increment';
        $last_increment = DB::table('hr_increment')
            ->select('associate_id',DB::raw('max(effective_date) as effective_date'),'increment_amount')
            ->whereIn('associate_id', $request->associate_id)
            ->where('status',1)
            ->groupBy('associate_id')
            ->get()
            ->keyBy('associate_id');
        $designations = DB::table('hr_designation')
            ->where('hr_designation_status',1)
            ->get();

        $management = collect($designations)
            ->where('hr_designation_emp_type', 1)
            ->pluck('hr_designation_name', 'hr_designation_id');

        $worker = collect($designations)
            ->whereIn('hr_designation_emp_type', [2,3])
            ->pluck('hr_designation_name', 'hr_designation_id');

        $gazette = [];

        $input = $request->all();
        $input['select_category'] = 'individual';
        return view('hr.payroll.increment.eligible-list', compact('data','unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area','effective_date','date','request','un','management','worker','last_increment','input'))->render();

    }

    public function getEligibleList(Request $request)
    {
        $input = $request->all();
        $input['area']       = isset($request['area'])?$request['area']:'';
        $input['location']   = isset($request['location'])?$request['location']:'';
        $input['otnonot']    = isset($request['otnonot'])?$request['otnonot']:'';
        $input['department'] = isset($request['department'])?$request['department']:'';
        $input['line_id']    = isset($request['line_id'])?$request['line_id']:'';
        $input['floor_id']   = isset($request['floor_id'])?$request['floor_id']:'';
        $input['section']    = isset($request['section'])?$request['section']:'';
        $input['subSection'] = isset($request['subSection'])?$request['subSection']:'';
        $input['as_ot'] = isset($request['as_ot'])?$request['as_ot']:'';
        $input['emp_type'] = isset($request['emp_type'])?$request['emp_type']:'';

        ini_set('zlib.output_compression', 1);

        $inc_month = $request->month;
        $date = $request->month.'-01';
        $date_end = date('Y-m-t', strtotime($date));
        $inc_year = date('Y', strtotime($date));
        $effective_date = Carbon::parse($date);
        $range_start = $effective_date->copy()->subMonths(11)->toDateString();
        $range_end = $effective_date->copy()->endOfMonth()->toDateString();

        $get_from_process = DB::table('hr_increment_approval')
                ->where('effective_date','>=', $date)
                ->pluck('associate_id')->toArray();

        $increment = DB::table('hr_increment')
                     //->where('increment_type', 2)
                     ->where('applied_date','>=', $date)/*
                     ->where('applied_date','<=', $date_end)*/
                     ->pluck('associate_id')->toArray();

        $ignore = array_merge($get_from_process, $increment);

        if(isset($input['select_category'])){
            $data = DB::table('hr_as_basic_info as b')
            ->select(
                'b.as_id',
                'b.as_name',
                'b.associate_id',
                'b.as_oracle_code',
                'b.as_emp_type_id',
                'b.as_unit_id',
                'b.as_location',
                'b.as_department_id',
                'b.as_area_id',
                'b.as_pic',
                'b.as_gender',
                'b.as_floor_id',
                'b.as_line_id',
                'b.as_section_id',
                'b.as_subsection_id',
                'b.as_designation_id', 
                'b.as_gender',
                'b.as_pic',
                'b.as_doj',
                'ben.ben_current_salary',
                'i.month',
                'i.remarks'
            )
            ->leftJoin('hr_benefits as ben','ben.ben_as_id','b.associate_id')
            ->leftJoin('hr_increment_month as i','i.associate_id','b.associate_id')
            ->whereIn('b.as_status',[1,6])
            // ->whereNotIn('b.associate_id', $ignore)
            // ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            // ->whereIn('b.as_location', auth()->user()->location_permissions())
            // ->when(!empty($input['unit']), function ($query) use($input){
            //     if($input['unit'] == 145){
            //         return $query->whereIn('b.as_unit_id',[1, 4, 5]);
            //     }else{
            //         return $query->where('b.as_unit_id',$input['unit']);
            //     }
            // })
            ->whereIn('b.associate_id', $request->associate_id)
            ->get()
            ->keyBy('associate_id');
        }else{
            $data = DB::table('hr_as_basic_info as b')
            ->select(
                'b.as_id',
                'b.as_name',
                'b.associate_id',
                'b.as_oracle_code',
                'b.as_emp_type_id',
                'b.as_unit_id',
                'b.as_location',
                'b.as_department_id',
                'b.as_area_id',
                'b.as_pic',
                'b.as_gender',
                'b.as_floor_id',
                'b.as_line_id',
                'b.as_section_id',
                'b.as_subsection_id',
                'b.as_designation_id', 
                'b.as_gender',
                'b.as_pic',
                'b.as_doj',
                'ben.ben_current_salary',
                'i.month',
                'i.remarks'
            )
            ->leftJoin('hr_benefits as ben','ben.ben_as_id','b.associate_id')
            ->leftJoin('hr_increment_month as i','i.associate_id','b.associate_id')
            ->whereIn('b.as_status',[1,6])
            ->whereNotIn('b.associate_id', $ignore)
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('b.as_location', auth()->user()->location_permissions())
            ->when(!empty($input['unit']), function ($query) use($input){
                if($input['unit'] == 145){
                    return $query->whereIn('b.as_unit_id',[1, 4, 5]);
                }else{
                    return $query->where('b.as_unit_id',$input['unit']);
                }
            })
            ->when(!empty($input['area']), function ($query) use($input){
               return $query->where('b.as_area_id',$input['area']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('b.as_department_id',$input['department']);
            })
            ->when(!empty($input['line_id']), function ($query) use($input){
               return $query->where('b.as_line_id', $input['line_id']);
            })
            ->when(!empty($input['floor_id']), function ($query) use($input){
               return $query->where('b.as_floor_id',$input['floor_id']);
            })
            ->when($input['as_ot']!=null, function ($query) use($input){
               return $query->where('b.as_ot',$input['as_ot']);
            })
            ->when($input['emp_type']!=null, function ($query) use($input){
               if($input['emp_type'] == 12){
                    return $query->whereIn('b.as_emp_type_id',[1,2]);
                }else{
                    return $query->where('b.as_emp_type_id',$input['emp_type']);
                }
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('b.as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use($input){
               return $query->where('b.as_subsection_id', $input['subSection']);
            })
            ->when((isset($input['min_salary']) && isset($input['max_salary'])), function ($query) use($input){
               return $query->where('ben.ben_current_salary','>=' ,$input['min_salary'])->where('ben.ben_current_salary','<=' ,$input['max_salary']);
            })
            ->where('b.as_doj', '<', $range_start)
            ->get()
            ->keyBy('associate_id');
        }
        
        if($request->type == 'running' ){

            $data = collect($data)->filter(function ($item) use ($inc_month) {
                    if($item->month == date('M', strtotime($inc_month))){
                        return $item;
                    }
            })->values()->toArray();
            
        }else if($request->type == 'pending'){
            $prevIncrements = DB::table('hr_increment')
                     //->where('increment_type', 2)
                     ->where('applied_date','>=', $range_start)
                     ->where('applied_date','<=', $range_end)
                     ->pluck('associate_id')->toArray();

            $data = collect($data)->filter(function ($item) use ($inc_month, $prevIncrements) {
                if($item->month != date('M', strtotime($inc_month)) && (!in_array($item->associate_id, $prevIncrements))){
                    return $item;
                }
            })->values()->toArray();

        }else{
            $prevIncrements = DB::table('hr_increment')
                     //->where('increment_type', 2)
                     ->where('applied_date','>=', $range_start)
                     ->where('applied_date','<=', $range_end)
                     ->pluck('associate_id')->toArray();
                     
            $data = collect($data)->filter(function ($item) use ($inc_month, $prevIncrements) {
                if(!in_array($item->associate_id, $prevIncrements)){
                    return $item;
                }
            })->values()->toArray();
        }

        $final_as_id = collect($data)->pluck('associate_id');

        $last_increment = DB::table('hr_increment')
            ->select('associate_id', 'effective_date','increment_amount')
            ->whereIn('associate_id', $final_as_id)
            ->where('status', 1)
            ->orderBy('effective_date','DESC')
            ->get();

       $last_increment = collect($last_increment)
            ->groupBy('associate_id')
            ->map(function($q){
                return  collect($q)
                    ->sortByDesc('effective_date', true)
                    ->first();
            });



        $unit = unit_by_id();
        $location = location_by_id();
        $line = line_by_id();
        $floor = floor_by_id();
        $department = department_by_id();
        $designation = designation_by_id();
        $section = section_by_id();
        $subSection = subSection_by_id();
        $area = area_by_id();
        $un = '';
        if(isset($input['unit'])){
           if($input['unit'] == 145){
                $un = 'MBM Garments Limited';
            }else{
                $un = $unit[$input['unit']]['hr_unit_name']??'';
            } 
        }
        

        $designations = DB::table('hr_designation')
            ->where('hr_designation_status',1)
            ->get();

        $management = collect($designations)
            ->where('hr_designation_emp_type', 1)
            ->pluck('hr_designation_name', 'hr_designation_id');

        $worker = collect($designations)
            ->whereIn('hr_designation_emp_type', [2,3])
            ->pluck('hr_designation_name', 'hr_designation_id');
        $input['main_url'] = 'hr/payroll/increment-eligible';
        $variables = array(
            'data' => $data,
            'unit' => $unit,
            'location' => $location,
            'line' => $line,
            'floor' => $floor,
            'department' => $department,
            'designation' => $designation,
            'section' => $section,
            'subSection' => $subSection,
            'area' => $area,
            'effective_date' => $effective_date,
            'inc_year' => $inc_year,
            'date' => $date,
            'request' => $request,
            'un' => $un,
            'last_increment' => $last_increment,
            'management' => $management,
            'worker' => $worker,
            'input' => $input
        );

        if(isset($request->export)){
            $filename = 'Increment Eligible List - '.$un;
            $filename .= '.xlsx';
            return Excel::download(new IncrementExport($variables, 'eligible'), $filename);
        }

        return view('hr.payroll.increment.eligible-list', $variables)->render();

    }

    public function getEligibleFilter(Request $request)
    {
        $input = $request->all();
        $input['area']       = isset($request['area'])?$request['area']:'';
        $input['location']   = isset($request['location'])?$request['location']:'';
        $input['otnonot']    = isset($request['otnonot'])?$request['otnonot']:'';
        $input['department'] = isset($request['department'])?$request['department']:'';
        $input['line_id']    = isset($request['line_id'])?$request['line_id']:'';
        $input['floor_id']   = isset($request['floor_id'])?$request['floor_id']:'';
        $input['section']    = isset($request['section'])?$request['section']:'';
        $input['subSection'] = isset($request['subSection'])?$request['subSection']:'';
        $input['as_ot'] = isset($request['as_ot'])?$request['as_ot']:'';
        $input['emp_type'] = isset($request['emp_type'])?$request['emp_type']:'';

        ini_set('zlib.output_compression', 1);

        $inc_month = $request->month;
        $date = $request->month.'-01';
        $date_end = date('Y-m-t', strtotime($date));
        $inc_year = date('Y', strtotime($date));
        $effective_date = Carbon::parse($date);
        $range_start = $effective_date->copy()->subMonths(11)->toDateString();
        $range_end = $effective_date->copy()->endOfMonth()->toDateString();

        $get_from_process = DB::table('hr_increment_approval')
                ->where('effective_date','>=', $date)
                ->pluck('associate_id')->toArray();

        $increment = DB::table('hr_increment')
                     //->where('increment_type', 2)
                     ->where('applied_date','>=', $date)/*
                     ->where('applied_date','<=', $date_end)*/
                     ->pluck('associate_id')->toArray();

        $ignore = array_merge($get_from_process, $increment);

        if(isset($input['select_category'])){
            $data = DB::table('hr_as_basic_info as b')
            ->select(
                'b.as_id',
                'b.as_name',
                'b.associate_id',
                'b.as_oracle_code',
                'b.as_emp_type_id',
                'b.as_unit_id',
                'b.as_location',
                'b.as_department_id',
                'b.as_area_id',
                'b.as_pic',
                'b.as_gender',
                'b.as_floor_id',
                'b.as_line_id',
                'b.as_section_id',
                'b.as_subsection_id',
                'b.as_designation_id', 
                'b.as_gender',
                'b.as_pic',
                'b.as_doj',
                'ben.ben_current_salary',
                'i.month',
                'i.remarks'
            )
            ->leftJoin('hr_benefits as ben','ben.ben_as_id','b.associate_id')
            ->leftJoin('hr_increment_month as i','i.associate_id','b.associate_id')
            ->whereIn('b.as_status',[1,6])
            // ->whereNotIn('b.associate_id', $ignore)
            // ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            // ->whereIn('b.as_location', auth()->user()->location_permissions())
            // ->when(!empty($input['unit']), function ($query) use($input){
            //     if($input['unit'] == 145){
            //         return $query->whereIn('b.as_unit_id',[1, 4, 5]);
            //     }else{
            //         return $query->where('b.as_unit_id',$input['unit']);
            //     }
            // })
            ->whereIn('b.associate_id', $request->associate_id)
            ->get()
            ->keyBy('associate_id');
        }else{
            
            $data = DB::table('hr_as_basic_info as b')
                ->select(
                    'b.as_id',
                    'b.as_name',
                    'b.associate_id',
                    'b.as_oracle_code',
                    'b.as_emp_type_id',
                    'b.as_unit_id',
                    'b.as_location',
                    'b.as_department_id',
                    'b.as_area_id',
                    'b.as_pic',
                    'b.as_gender',
                    'b.as_floor_id',
                    'b.as_line_id',
                    'b.as_section_id',
                    'b.as_subsection_id',
                    'b.as_designation_id', 
                    'b.as_gender',
                    'b.as_pic',
                    'b.as_doj'
                )
                ->whereIn('b.as_status',[1,6])
                ->whereNotIn('b.associate_id', $ignore)
                ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                ->whereIn('b.as_location', auth()->user()->location_permissions())
                ->when(!empty($input['unit']), function ($query) use($input){
                    return $query->whereIn('b.as_unit_id',$input['unit']);
                })
                ->when(!empty($input['location']), function ($query) use($input){
                    return $query->whereIn('b.as_location',$input['location']);
                })
                ->when(!empty($input['area']), function ($query) use($input){
                   return $query->where('b.as_area_id',$input['area']);
                })
                ->when(!empty($input['department']), function ($query) use($input){
                   return $query->where('b.as_department_id',$input['department']);
                })
                ->when(!empty($input['line_id']), function ($query) use($input){
                   return $query->where('b.as_line_id', $input['line_id']);
                })
                ->when(!empty($input['floor_id']), function ($query) use($input){
                   return $query->where('b.as_floor_id',$input['floor_id']);
                })
                ->when($input['as_ot']!=null, function ($query) use($input){
                   return $query->where('b.as_ot',$input['as_ot']);
                })
                ->when($input['emp_type']!=null, function ($query) use($input){
                   if($input['emp_type'] == 12){
                        return $query->whereIn('b.as_emp_type_id',[1,2]);
                    }else{
                        return $query->where('b.as_emp_type_id',$input['emp_type']);
                    }
                })
                ->when(!empty($input['section']), function ($query) use($input){
                   return $query->where('b.as_section_id', $input['section']);
                })
                ->when(!empty($input['subSection']), function ($query) use($input){
                   return $query->where('b.as_subsection_id', $input['subSection']);
                })
                
                ->where('b.as_doj', '<', $range_start)
                ->get()
                ->keyBy('associate_id');

            $benefit = DB::table('hr_benefits')
                ->select('ben_as_id', 'ben_current_salary')
                ->get()->keyBy('ben_as_id');

            $incrementMonth = DB::table('hr_increment_month')
                ->select('associate_id', 'month', 'remarks')
                ->get()->keyBy('associate_id');

            $data = collect($data)->map(function($q) use ($benefit, $incrementMonth){
                $q->ben_current_salary = $benefit[$q->associate_id]->ben_current_salary??0;
                $q->month = $incrementMonth[$q->associate_id]->month??'';
                $q->remarks = $incrementMonth[$q->associate_id]->remarks??'';
                return $q;
            });
            

        }
        
        if($request->type == 'running' ){

            $data = collect($data)->filter(function ($item) use ($inc_month) {
                    if($item->month == date('M', strtotime($inc_month))){
                        return $item;
                    }
            })->values()->toArray();
            
        }else if($request->type == 'pending'){
            $prevIncrements = DB::table('hr_increment')
                     //->where('increment_type', 2)
                     ->where('applied_date','>=', $range_start)
                     ->where('applied_date','<=', $range_end)
                     ->pluck('associate_id')->toArray();

            $data = collect($data)->filter(function ($item) use ($inc_month, $prevIncrements) {
                if($item->month != date('M', strtotime($inc_month)) && (!in_array($item->associate_id, $prevIncrements))){
                    return $item;
                }
            })->values()->toArray();

        }else{
            $prevIncrements = DB::table('hr_increment')
                     //->where('increment_type', 2)
                     ->where('applied_date','>=', $range_start)
                     ->where('applied_date','<=', $range_end)
                     ->pluck('associate_id')->toArray();
                     
            $data = collect($data)->filter(function ($item) use ($inc_month, $prevIncrements) {
                if(!in_array($item->associate_id, $prevIncrements)){
                    return $item;
                }
            })->values()->toArray();
        }

        $final_as_id = collect($data)->pluck('associate_id');

        $last_increment = DB::table('hr_increment')
            ->select('associate_id', 'effective_date','increment_amount')
            ->whereIn('associate_id', $final_as_id)
            ->where('status', 1)
            ->orderBy('effective_date','DESC')
            ->get();

       $last_increment = collect($last_increment)
            ->groupBy('associate_id')
            ->map(function($q){
                return  collect($q)
                    ->sortByDesc('effective_date', true)
                    ->first();
            });



        $unit = unit_by_id();
        $location = location_by_id();
        $line = line_by_id();
        $floor = floor_by_id();
        $department = department_by_id();
        $designation = designation_by_id();
        $section = section_by_id();
        $subSection = subSection_by_id();
        $area = area_by_id();
        $un = '';
        if(isset($input['unit'])){
           if(count($input['unit']) > 1){
                $un = 'MBM Group';
            }else{
                $un = $unit[$input['unit'][0]]['hr_unit_name']??'';
            } 
        }
        
        $designations = collect($designation)->where('hr_designation_status',1);

        $management = collect($designations)
            ->where('hr_designation_emp_type', 1)
            ->pluck('hr_designation_name', 'hr_designation_id');

        $worker = collect($designations)
            ->whereIn('hr_designation_emp_type', [2,3])
            ->pluck('hr_designation_name', 'hr_designation_id');
        $input['process'] = 'disabled';
        $input['main_url'] = 'hr/payroll/increment-eligible-filter';
        $variables = array(
            'data' => $data,
            'unit' => $unit,
            'location' => $location,
            'line' => $line,
            'floor' => $floor,
            'department' => $department,
            'designation' => $designation,
            'section' => $section,
            'subSection' => $subSection,
            'area' => $area,
            'effective_date' => $effective_date,
            'inc_year' => $inc_year,
            'date' => $date,
            'request' => $request,
            'un' => $un,
            'last_increment' => $last_increment,
            'management' => $management,
            'worker' => $worker,
            'input' => $input
        );

        if(isset($request->export)){
            $filename = 'Increment Eligible List - '.$un;
            $filename .= '.xlsx';
            return Excel::download(new IncrementExport($variables, 'eligible'), $filename);
        }

        return view('hr.payroll.increment.eligible-list', $variables)->render();

    }

    public function incrementAction(Request $request)
    {
        $created_by= Auth::user()->associate_id;

        if(empty($request->increment) || !is_array($request->increment))
        {
            return response([
                'msg' => 'Please select at least one associate.',
                'status' => 'failed'
            ]);
        }
        
        $increment = $request->increment;

        $count = 0;
        $benefits = DB::table('hr_benefits as b')
                    ->select('a.as_id','a.as_unit_id','a.as_status','b.*','i.month')
                    ->leftJoin('hr_as_basic_info as a','a.associate_id','b.ben_as_id')
                    ->leftJoin('hr_increment_month as i','i.associate_id','a.associate_id')
                    ->get()
                    ->keyBy('ben_as_id');

        foreach ($increment as $key => $v) {

            if(isset($v['status']) && $v['amount'] > 0){
                $count++;
                $ben = $benefits[$key];

                $eligible = date('Y-m-d',strtotime(date('Y').'-'.$ben->month.'-01'));
                $inc = new Increment();
                $inc->associate_id = $key;
                $inc->current_salary = $v['salary'];
                $inc->increment_type = $request->increment_type;
                $inc->increment_amount = $v['amount'] ;
                $inc->amount_type = 1 ;
                $inc->eligible_date =  $eligible;
                $inc->effective_date = $request->effective_date;
                $inc->applied_date = $request->effective_date ;
                $inc->status = 0 ;
                $inc->created_by = auth()->id();
                $inc->save();

                $amount = $v['salary'] + $v['amount'];
                if($inc->effective_date <= date('Y-m-d')){
                    $this->reflectBenefit($ben, $amount, $key, $request->effective_date);
                    $inc->status = 1 ;
                    $inc->save();
                }

                if($v['desgn']){
                    $this->reflectPromotion($key, $v['prev_desgn'] ,$v['desgn'], $request->effective_date);
                }
            }
        }

        return response([
                'msg' => 'Increment information saved successfully!',
                'status' => 'success'
        ]);
    }

    public function reflectBenefit($b, $amount, $associate, $date)
    {
        $ss =  \Cache::remember('salary_structure', 10000000, function () {
            return DB::table('hr_salary_structure AS s')
                    ->where('status', 1)
                    ->select('s.*')
                    ->orderBy('id', 'DESC')
                    ->first();
        }); 
        

       
        $allowance = $ss->medical + $ss->transport + $ss->food;
        $ben_basic= ceil(($amount - $allowance) / $ss->basic);
        //$ben_basic= (($amount/100)*$ss->basic);
        $ben_house_rent = $amount - ($ben_basic + $allowance);

        if($b->ben_bank_amount > 0 && $b->ben_cash_amount > 0){
            $ben_bank_amount = $b->ben_bank_amount;
            $ben_cash_amount = $amount - $ben_bank_amount;
        }else if($b->ben_bank_amount > 0 && $b->ben_cash_amount == 0){
            if($b->bank_name == 'dbbl'){
                $ben_bank_amount = $b->ben_bank_amount;
                $ben_cash_amount = $amount - $ben_bank_amount;
            }else{
                $ben_bank_amount = $amount;
                $ben_cash_amount = 0; 
            }
        }else{
            $ben_bank_amount = 0;
            $ben_cash_amount = $amount;
        }

        DB::table('hr_benefits')
            ->where('ben_as_id', $associate)
            ->update([
                'ben_current_salary' => $amount,
                'ben_basic' => $ben_basic,
                'ben_house_rent' => $ben_house_rent,
                'ben_cash_amount' => $ben_cash_amount,
                'ben_bank_amount' => $ben_bank_amount
            ]);

        $tableName = get_att_table($b->as_unit_id);

        if($b->as_status == 1){
            
            
                // check monthly salary lock
              $month = date('m', strtotime($date));
              $year = date('Y', strtotime($date));
              $t = date('t', strtotime($date));
              
              $queue = (new ProcessUnitWiseSalary($tableName, $month, $year, $b->as_id, $t))
                            ->onQueue('salarygenerate')
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
              
            
        }
    }


    public function process(Request $request)
    {
        /*if(auth()->user()->canany(['Increment Approval','Increment Process'])){
            return redirect('hr/payroll/increment-approval');
        }*/

        $unitList  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_name', 'hr_unit_id');
        $floorList= [];
        $lineList= [];
 
        $areaList  = DB::table('hr_area')->where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');

        $deptList= [];

        $sectionList= [];

        $subSectionList= [];

        $employeeTypes  = EmpType::where('hr_emp_type_status', '1')->pluck('hr_emp_type_name', 'emp_type_id');
        $employeeTypes[12] = 'Management & Staff';

        $data['salaryMin']      = 0;
        $data['salaryMax']      = Benefits::getSalaryRangeMax();

        return view('hr.payroll.increment.process', compact('unitList','floorList','lineList','areaList','deptList','sectionList','subSectionList', 'data','employeeTypes'));

    }

    public function incrementActionInitial(Request $request)
    {
        if(empty($request->increment) || !is_array($request->increment)){
            return response([
                'msg' => 'Please select at least one associate.',
                'status' => 'failed'
            ]);
        }
        
        $increment = $request->increment;

        $insert = [];
        foreach ($increment as $key => $v) {
            if(isset($v['status'])){
                $insert[$key] = array(
                    'associate_id' => $key,
                    'current_salary' => $v['salary'],
                    'increment_type' => $request->increment_type,
                    'increment_amount' => $v['amount'],
                    'prepared_by' => auth()->id(),
                    'status' => 0,
                    'effective_date' => $request->effective_date,
                    'designation_id' => $v['desgn'],
                    'initial_designation_id' => $v['desgn']
                );

            }
        }
        if(count($insert) > 0){
            DB::table('hr_increment_approval')->insertOrIgnore($insert);
        }

        return response([
                'msg' => 'Successfully Proceeded for Approval!',
                'status' => 'success'
        ]);


    }

    public function approval(Request $request)
    {
        if(auth()->user()->canany(['Increment Approval','Increment Process'])){


            $unitList  = collect(unit_by_id())->pluck('hr_unit_name', 'hr_unit_id');
            $floorList= [];
            $lineList= [];
     
            $areaList  = collect(area_by_id())->pluck('hr_area_name', 'hr_area_id');

            $deptList= [];

            $sectionList= [];

            $subSectionList= [];

            $employeeTypes  = collect(emp_type_by_id())->pluck('hr_emp_type_name', 'emp_type_id');
            $employeeTypes[12] = 'Management & Staff';

            $salaryMin = 0;
            $salaryMax = Benefits::getSalaryRangeMax();

            $unit_status = $this->getUnitMsg();
        
            return view('hr.payroll.increment.approval', compact('unitList','floorList','lineList','areaList','deptList','sectionList','subSectionList', 'salaryMin', 'salaryMax', 'employeeTypes','unit_status'));
        }else{
            return back();
        }
    }

    public function getUnitMsg()
    {
        $increment = [];
        if(auth()->user()->canany(['Increment Process 1', 'Increment Process 2', 'Increment Approval'])){
            $query = DB::table('hr_increment_approval as i')
                    ->select(DB::raw('count(*) as count'),'b.as_unit_id')
                    ->leftJoin('hr_as_basic_info as b','i.associate_id', 'b.associate_id')
                    ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                    ->whereIn('b.as_location', auth()->user()->location_permissions())
                    ->where('i.status', 0);
                if(auth()->user()->can('Increment Process 1')){
                    $query->whereNull('level_1_approval')
                    ->whereNull('level_2_approval')
                    ->whereNull('level_3_approval');
                }elseif(auth()->user()->can('Increment Process 2')){
                    $query->whereNotNull('level_1_approval')
                    ->whereNull('level_2_approval')
                    ->whereNull('level_3_approval');
                }elseif(auth()->user()->can('Increment Approval')){
                    $query->whereNotNull('level_1_approval')
                    ->whereNotNull('level_2_approval')
                    ->whereNull('level_3_approval');
                }else{
                    $query->where('status', 3); // not accessible
                }
            $increment = $query->groupBy('b.as_unit_id')->get()->pluck('count','as_unit_id');
        }
        
        $unit_data = [];
        $unit_data[1] = 0;
        foreach ($increment as $key => $v) {
            if($key == 1 || $key == 4 || $key == 5){
                $unit_data[1] += $v;
            }else{
                $unit_data[$key] = $v;
            }
        }
        if(count($unit_data) == 1 && $unit_data[1] == 0){
            $unit_data = [];
        }


        $unit = unit_by_id();
        return view('hr.payroll.increment.approval_stage',compact('unit','unit_data'))->render();
    }

    public function viewOnApproval(Request $request)
    {
        $input = $request->all();
        $set = [];
        $set['type'] = 'Management';
        $set['field'] = 'level_3';
        $set['exfield'] = 'increment_amount';
        $set['extype'] = 'HR Proposed';
        $set['next'] = 'Approve';




        $unit = unit_by_id();
        $location = location_by_id();
        $department = department_by_id();
        $designation = designation_by_id();
        $section = section_by_id();

        // $increment = DB::table('hr_increment_approval as i')
        //             ->leftJoin('hr_as_basic_info as b','i.associate_id', 'b.associate_id')
        //             ->where('i.status', 0)
        //             ->whereNull('level_3_approval')
        //             ->when(isset($request->unit), function($q) use ($request){
        //                 if(in_array($request->unit, [1,4,5])){
        //                     $q->whereIn('b.as_unit_id',[1,4,5] );
        //                 }else{
        //                     $q->where('b.as_unit_id', $request->unit);
        //                 }
        //             })
        //             ->whereIn('b.as_unit_id',auth()->user()->unit_permissions())
        //             ->whereIn('b.as_location',auth()->user()->location_permissions())
        //             ->get();
        $query = DB::table('hr_increment_approval as i')
            ->leftJoin('hr_as_basic_info as b','i.associate_id', 'b.associate_id')
            ->where('i.status', 0)
            ->whereNull('level_3_approval')
            ->when(isset($request->unit), function($q) use ($request){
                if(in_array($request->unit, [1,4,5])){
                    $q->whereIn('b.as_unit_id',[1,4,5] );
                }else{
                    $q->where('b.as_unit_id', $request->unit);
                }
            })
            ->whereIn('b.as_unit_id',auth()->user()->unit_permissions())
            ->whereIn('b.as_location',auth()->user()->location_permissions());
            // if(in_array(auth()->user()->id, [12,40,53,59,84,127,41,139])){
            //     $query->whereIn('b.as_emp_type_id',[1,2,3]);
            // }else{
            //     $query->whereIn('b.as_emp_type_id',[3]);
            // }    
        $increment = $query->get();

        $un = 'Increment on Approval';
        if(in_array($request->unit, [1,4,5])){
            $un = 'MBM Garments Ltd.';
        }else{
            if($request->unit){
                $un = $unit[$request->unit]['hr_unit_name'];
            }
        }

        $final_as_id = collect($increment)->pluck('associate_id');

        $last_increment = DB::table('hr_increment')
            ->select('associate_id', 'effective_date','increment_amount')
            ->whereIn('associate_id', $final_as_id)
            ->where('status', 1)
            ->orderBy('effective_date','DESC')
            ->get();

       $last_increment = collect($last_increment)
            ->groupBy('associate_id')
            ->map(function($q){
                return  collect($q)
                    ->sortByDesc('effective_date', true)
                    ->first();
            });

        $variables = array(
            'input' => $input,
            'unit' => $unit,
            'location' => $location,
            'department' => $department,
            'designation' => $designation,
            'section' => $section,
            'increment' => $increment,
            'set' => $set,
            'last_increment' => $last_increment,
            'un' => $un
        );

        if(isset($request->export)){
            $filename = 'Increment on Approval Eligible - '.$un;
            $filename .= '.xlsx';
            return Excel::download(new IncrementExport($variables, 'onapproval'), $filename);
        }

        return view('hr.payroll.increment.on_approval', $variables);
    }

    public function getApprovalData(Request $request)
    {
        $input = $request->all();
        // dd($input);
        $set = [];

        $input['employee_type'] = isset($request->employee_type)?$request->employee_type:'';
        
        if(auth()->user()->can('Increment Process 1')){
            $set['type'] = ' HR Head';
            $set['field'] = 'level_1';
            $set['exfield'] = 'increment_amount';
            $set['extype'] = 'HR Proposed';
            $set['next'] = 'Proceed 2';
            $set['step'] = 1;
        }else if(auth()->user()->can('Increment Process 2')){
            $set['type'] = 'Senior Management';
            $set['field'] = 'level_2';
            $set['exfield'] = 'level_1_amount';
            $set['extype'] = 'Management 1 Proposed';
            $set['next'] = 'Proceed to Approve';
            $set['step'] = 2;
        }else if(auth()->user()->canany(['Increment Approval'])){
            $set['type'] = 'Top Management';
            $set['field'] = 'level_3';
            $set['exfield'] = 'increment_amount';
            $set['extype'] = 'HR Proposed';
            $set['next'] = 'Approve';
            $set['step'] = 3;
        }else{

            return back();
        }

        // dd($set);
        $users = DB::table('users as u')
            ->select('u.id','u.name', 'b.as_designation_id')
            ->leftJoin('hr_as_basic_info as b', 'u.associate_id', 'b.associate_id')
            ->get()
            ->keyBy('id');
        
        $unit = unit_by_id();
        $location = location_by_id();
        $department = department_by_id();
        $designation = designation_by_id();
        $section = section_by_id();

        $query = DB::table('hr_increment_approval as i')
            ->leftJoin('hr_as_basic_info as b','i.associate_id', 'b.associate_id')
            ->where('i.status', 0);
            // if(in_array(auth()->user()->id, [12,40,53,59,84,127,41,139])){
            //     $query->whereIn('b.as_emp_type_id',[1,2,3]);
            // }else{
            //     $query->whereIn('b.as_emp_type_id',[3]);
            // }
            $query->when(isset($request->unit), function($q) use ($request){
                if(in_array($request->unit, [1,4,5])){
                    $q->whereIn('b.as_unit_id',[1,4,5] );
                }else{
                    $q->where('b.as_unit_id', $request->unit);
                }
            })
            ->when(isset($request->employee_type), function($q) use($request){
                if($request->employee_type == 'worker'){
                    return $q->where('b.as_emp_type_id',3);
                }else if($request->employee_type == 'management'){
                    return $q->whereIn('b.as_emp_type_id',[1,2]);
                }
            });

        $incrementStage =  $query->get();
        
        // 
        $benefit = DB::table('hr_benefits')
                ->select('ben_as_id', 'ben_current_salary')
                ->get()->keyBy('ben_as_id');
        $basic = DB::table('hr_as_basic_info')
            ->select('associate_id')
            ->whereIn('as_status', [1,6])
            ->when(isset($request->unit), function($q) use ($request){
                if(in_array($request->unit, [1,4,5])){
                    $q->whereIn('as_unit_id',[1,4,5] );
                }else{
                    $q->where('as_unit_id', $request->unit);
                }
            })
            ->get();

        $salary = collect($basic)->map(function($q) use($benefit){
            $p = (object)[];
            $p->salary = $benefit[$q->associate_id]->ben_current_salary??0;
            return $p;
        });
        $totalSalary = collect($salary)->sum('salary');

        $level1 = collect($incrementStage);
        $level1Data['count'] = $level1->count();
        $level1Data['salary'] = $level1->sum('increment_amount');

        $level2 = collect($incrementStage)->whereNotNull('level_1_approval');
        $level2Data['count'] = $level2->count();
        $level2Data['salary'] = $level2->sum('level_1_amount');

        $level3 = collect($incrementStage)->whereNotNull('level_1_approval')->whereNotNull('level_2_approval');
        $level3Data['count'] = $level3->count();
        $level3Data['salary'] = $level3->sum('level_2_amount');

        if($set['field'] == 'level_1'){
            $increment = collect($incrementStage)->whereNull('level_1_approval')->whereNull('level_2_approval')->whereNull('level_3_approval');
        }elseif($set['field'] == 'level_2'){
            $increment = collect($incrementStage)->whereNotNull('level_1_approval')->whereNull('level_2_approval')->whereNull('level_3_approval');
        }elseif($set['field'] == 'level_3'){
            $increment = collect($incrementStage)->whereNotNull('level_1_approval')->whereNotNull('level_2_approval')->whereNull('level_3_approval');
        }else{
            $increment = [];
        }

        $un = 'Increment Approval';
        if(in_array($request->unit, [1,4,5])){
            $un = 'MBM Garments Ltd.';
        }else{
            if($request->unit){
                $un = $unit[$request->unit]['hr_unit_name'];
            }
        }

        $final_as_id = collect($increment)->pluck('associate_id');

        $last_increment = DB::table('hr_increment')
            ->select('associate_id', 'effective_date','increment_amount')
            ->whereIn('associate_id', $final_as_id)
            ->where('status', 1)
            ->orderBy('effective_date','DESC')
            ->get();

       $last_increment = collect($last_increment)
            ->groupBy('associate_id')
            ->map(function($q){
                return  collect($q)
                    ->sortByDesc('effective_date', true)
                    ->first();
            });

        $designations = collect(designation_by_id())->where('hr_designation_status',1);

        $management = collect($designations)
            ->where('hr_designation_emp_type', 1)
            ->pluck('hr_designation_name', 'hr_designation_id');

        $worker = collect($designations)
            ->whereIn('hr_designation_emp_type', [2,3])
            ->pluck('hr_designation_name', 'hr_designation_id');

        return view('hr.payroll.increment.process_approval', compact('unit','location','department','designation','section','increment','set','last_increment','management','worker','un','input', 'users', 'designations', 'level1Data', 'level2Data', 'level3Data', 'totalSalary'))->render();
        
    }


    public function incrementActionApproval(Request $request)
    {
        if(empty($request->increment) || !is_array($request->increment)){
            return response([
                'msg' => 'Please select at least one associate.',
                'status' => 'failed'
            ]);
        }
        
        $increment = $request->increment;
        // dd($request->all());
        $update = [];
        foreach ($increment as $key => $v) {
            if(isset($v['status'])){
                
                if($request->level == 'level_2'){
                    $request->level = 'level_3';
                }
                
                if(($v['amount'] >= 0 && $request->level == 'level_3') || $request->level != 'level_3'){
                    $update[$key]['data'] = array(
                        $request->level.'_approval' => auth()->id(),
                        $request->level.'_amount' => $v['amount'],
                        $request->level.'_date' => date('Y-m-d'),
                    );
                    if($v['desgn']){
                        $update[$key]['data']['designation_id'] = $v['desgn'];
                    }
                    if($request->level == 'level_3'){
                        $update[$key]['data']['status'] = 1;
                    }
                    $update[$key]['id'] = $key; 
                }
                
            }
        }

        

        // update approval progress
        if(count($update) > 0){

            $this->buildUpdateQuery('hr_increment_approval', $update);
            // reflect increment
            $up = '';
            if($request->level == 'level_3'){
                $this->reflectIncrement((collect($update)->pluck('id')->toArray()));
                $up = $this->getUnitMsg();
            }

            return response([
                    'msg' => 'Successfully Proceeded for Approval!',
                    'status' => 'success',
                    'data' => $up
            ]);
        }


        return response([
                'msg' => 'Could not approve!',
                'status' => 'failed'
        ]);


    }


    public function reflectIncrement($data)
    {
        $increment = DB::table('hr_increment_approval')
            ->whereIn('id', $data)
            ->get();

        $as_id = collect($increment)->pluck('associate_id');

        $benefits = DB::table('hr_benefits as b')
            ->select('a.as_id','a.as_unit_id','a.as_status','a.as_designation_id','b.*','i.month')
            ->leftJoin('hr_as_basic_info as a','a.associate_id','b.ben_as_id')
            ->leftJoin('hr_increment_month as i','i.associate_id','a.associate_id')
            ->whereIn('b.ben_as_id', $as_id)
            ->get()
            ->keyBy('ben_as_id');


        foreach ($increment as $key => $v) {
            $ben = $benefits[$v->associate_id];
            $eligible = date('Y-m-d',strtotime(date('Y').'-'.$ben->month.'-01'));

            $inc = new Increment();
            $inc->associate_id = $v->associate_id;
            $inc->current_salary = $v->current_salary;
            $inc->increment_type = $v->increment_type;
            $inc->increment_amount = $v->level_3_amount;
            $inc->amount_type = 1 ;
            $inc->eligible_date =  $eligible;
            $inc->effective_date = $v->effective_date;
            $inc->applied_date = $v->effective_date;
            $inc->status = 0 ;
            $inc->created_by = auth()->id();
            $inc->save();

            $amount = $v->current_salary + $v->level_3_amount;
            
            if($inc->effective_date <= date('Y-m-d')){
                $this->reflectBenefit($ben, $amount, $v->associate_id, $inc->effective_date);
                $inc->status = 1 ;
                $inc->save();

                // reflect arear
                if(date('m',strtotime($inc->effective_date)) != date('m')){
                    $this->reflectArear($v->associate_id,$inc->effective_date,$v->level_3_amount,$ben->as_unit_id);
                }
                
            }

            if($v->designation_id){
                $this->reflectPromotion($v->associate_id, $ben->as_designation_id, $v->designation_id, $v->effective_date);
            }
        }

        return 'done';

    }

    public function reflectPromotion($as_id, $prev_id, $curr_id, $date)
    {
        $store = new Promotion;
        $store->associate_id = $as_id;
        $store->previous_designation_id = $prev_id;
        $store->current_designation_id  = $curr_id;
        $store->effective_date          = $date;
        if($date <= date('Y-m-d')){
            $store->status          = 1;  
        }

        if( $store->save()){
            if($store->status == 1){
                DB::table('hr_as_basic_info')
                    ->where('associate_id',$as_id)
                    ->update(['as_designation_id' => $curr_id]);
            }
        }
        return 'done';
    }

    public function buildUpdateQuery($table, $update)
    {
        if(count($update) > 0){
            $chunked = array_chunk($update, 300);

            foreach ($chunked as $key => $part) {
                # code...
                $qr = "update ".$table." set ";
                $cases = []; 
                $ids = [];
                foreach ($part as $key => $val) {
                    $ids[] = $val['id'];
                    foreach ($val['data'] as $k => $v) {
                        // if($v){
                            $cases[$k][] =  "when ".$val['id']." then '".$v."'";
                        // }
                    }
                }

                foreach ($cases as $k => $vl) {
                    if(collect($cases)->keys()->last() == $k){
                        $qr .= $k.' = case id '.implode(" ",$vl).' end ';
                    }else{
                        $qr .= $k.' = case id '.implode(" ",$vl).' end ,';
                    }
                }
                $qr .= " where id in (".implode(', ',$ids).")";

                DB::statement($qr);
            }
        }

        return 'success';

    }

    public function reflectArear( $as_id, $effective_date, $amount , $unit)
    {
        $to = \Carbon\Carbon::now()->subMonth();
        $from = \Carbon\Carbon::parse($effective_date);
        $arearMonths = ($to->diffInMonths($from)) + 1;

        $date = \Carbon\Carbon::now();
        $checkL['month'] = $date->copy()->subMonth()->format('m');
        $checkL['year'] = $date->copy()->subMonth()->format('Y');
        $checkL['unit_id'] = $unit;

        $checkLastLock = monthly_activity_close($checkL);
        $arearMonths = ($checkLastLock == 1)?$arearMonths:($arearMonths - 1);
        if($checkLastLock == 0){
            $month = $checkL['month'];
            $year = $checkL['year'];
        }else{
            $month = date('m');
            $year = date('Y');
        }

       if($arearMonths > 0){
            $master = SalaryAdjustMaster::firstOrNew([
                'associate_id' => $as_id,
                'month' => $month,
                'year' => $year
            ]);
            $master->save();

            $detail = new SalaryAdjustDetails();
            $detail->salary_adjust_master_id = $master->id;
            $detail->date                    = date('Y-m-d');
            $detail->amount                  = ($amount * $arearMonths);
            $detail->type                    = 3;
            $detail->save();
       }

    }


    public function rollbackIncr()
    {
        $increment = DB::table('hr_increment_approval as i')
                    ->leftJoin('hr_as_basic_info as a','a.associate_id','i.associate_id')
                    ->select('i.*')
                    ->where('i.status', 1)
                    ->whereIn('a.as_emp_type_id',[1,2])
                    ->get();

        $ids = collect($increment)->pluck('associate_id');

        $benefits = DB::table('hr_benefits as b')
            ->select('a.as_id','a.as_unit_id','a.as_status','a.as_designation_id','b.*','i.month')
            ->leftJoin('hr_as_basic_info as a','a.associate_id','b.ben_as_id')
            ->leftJoin('hr_increment_month as i','i.associate_id','a.associate_id')
            ->whereIn('b.ben_as_id', $ids)
            ->get()
            ->keyBy('ben_as_id');


        $ss =  \Cache::remember('salary_structure', 10000000, function () {
            return DB::table('hr_salary_structure AS s')
                    ->where('status', 1)
                    ->select('s.*')
                    ->orderBy('id', 'DESC')
                    ->first();
        }); 
            

           
        $allowance = $ss->medical + $ss->transport + $ss->food;

        foreach($increment as $key => $i){
            $amount = $i->current_salary;
            $b = $benefits[$i->associate_id];
            $ben_basic= ceil(($amount - $allowance) / $ss->basic);
            //$ben_basic= (($amount/100)*$ss->basic);
            $ben_house_rent = $amount - ($ben_basic + $allowance);

            if($b->ben_bank_amount > 0 && $b->ben_cash_amount > 0){
                $ben_bank_amount = $b->ben_bank_amount;
                $ben_cash_amount = $amount - $ben_bank_amount;
            }else if($b->ben_bank_amount > 0 && $b->ben_cash_amount == 0){
                if($b->bank_name == 'dbbl'){
                    $ben_bank_amount = $b->ben_bank_amount;
                    $ben_cash_amount = $amount - $ben_bank_amount;
                }else{
                    $ben_bank_amount = $amount;
                    $ben_cash_amount = 0; 
                }
            }else{
                $ben_bank_amount = 0;
                $ben_cash_amount = $amount;
            }

            DB::table('hr_benefits')
                ->where('ben_as_id', $i->associate_id)
                ->update([
                    'ben_current_salary' => $amount,
                    'ben_basic' => $ben_basic,
                    'ben_house_rent' => $ben_house_rent,
                    'ben_cash_amount' => $ben_cash_amount,
                    'ben_bank_amount' => $ben_bank_amount
                ]);


        }
        DB::table('hr_increment_approval')
            ->whereIn('associate_id',$ids)
            ->update([
                'level_3_approval' => null,
                'level_3_date' => null,
                'level_3_amount' => null,
                'status' => 0
            ]);
        // delete increment
        DB::table('hr_increment')
            ->whereIn('associate_id',$ids)
            ->where('created_at','>=','2021-04-02')
            ->delete();

        $pr = DB::table('hr_promotion')
            ->whereIn('associate_id',$ids)
            ->where('created_at','>=','2021-03-25')
            ->get();

        foreach ($pr as $key => $value) {
            DB::table('hr_as_basic_info')
                ->where('associate_id', $value->associate_id)
                ->update(['as_designation_id' => $value->previous_designation_id]);

        }
        $pr = DB::table('hr_promotion')
            ->whereIn('associate_id', $ids)
            ->where('created_at','>=','2021-03-25')
            ->delete();      
    }
}