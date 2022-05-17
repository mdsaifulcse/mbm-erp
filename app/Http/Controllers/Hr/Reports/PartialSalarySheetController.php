<?php

namespace App\Http\Controllers\Hr\Reports;
use App\Helpers\Custom;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\Area;
use App\Models\Hr\Benefits;
use App\Models\Hr\Department;
use App\Models\Hr\Floor;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Location;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;
use App\Models\Hr\Unit;
use App\Models\Hr\YearlyHolyDay;
use Rap2hpoutre\FastExcel\FastExcel;
use Attendance2;
use Attendance;
use Carbon\Carbon;
use DB, auth; 
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Validator;




class PartialSalarySheetController extends Controller
{
    public function __construct()
    {
        ini_set('zlib.output_compression', 1);
    }

    
    public function convertMonthNameToNumber($month)
    {
        return Carbon::parse("1 $month")->month;
    }

    public function index()
    {

// $x= DB::select('call get_unit_name_by_id()');
// // return $x;
//              $x=$x[0];
//              $x=collect($x)->pluck('hr_unit_name')->first();
//              // ->first();
               
//                 // ->pluck('hr_unit_name')
//                 // ->first();
// dd($x);
        // dd('ddd');
        if(auth()->user()->hasRole('Buyer Mode')){
            return redirect('hrm/operation/salary-sheet');
        }

        try {
            //$data['getEmployees']  = Employee::getSelectIdNameEmployee();
            $data['unitList']      = Unit::where('hr_unit_status', '1')
                ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
                ->orderBy('hr_unit_name', 'desc')
                ->pluck('hr_unit_name', 'hr_unit_id');

            $data['locationList']  = Location::where('hr_location_status', '1')
            ->whereIn('hr_location_id', auth()->user()->location_permissions())
            ->orderBy('hr_location_name', 'desc')
            ->pluck('hr_location_name', 'hr_location_id');

            $data['areaList']      = Area::where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
            $data['floorList']     = Floor::getFloorList();
            $data['deptList']      = Department::getDeptList();
            $data['sectionList']   = Section::getSectionList();
            $data['subSectionList'] = Subsection::getSubSectionList();
            $data['salaryMin']      = Benefits::getSalaryRangeMin();
            $data['salaryMax']      = Benefits::getSalaryRangeMax();
            $data['getYear']       = HrMonthlySalary::select('year')->distinct('year')->orderBy('year', 'desc')->pluck('year');

             $data['monthyear']= DB::table('hr_partial_salary_master')
                   ->select(DB::raw('DATE_FORMAT(salary_from_date,"%Y-%b") as salary_from_date,DATE_FORMAT(salary_from_date,"%Y-%m") as salary_from_date1'))
                   ->distinct('salary_from_date')
                   ->orderBy('salary_from_date1', 'desc')
                   ->pluck('salary_from_date');


                
               $data_query = DB::table('hr_partial_salary_process_view')
               ->whereIn('unit_id', auth()->user()->unit_permissions());
                $data['process_paramiter']= $data_query->get();
            


             // dd($data);
            return view('hr.operation.salary.partial_salary_index', $data);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }


public function create(Request $request)
    {
       $unit=$request->unit;
       $otnonot=$request->otnonot;
       $from_date=$request->from_date;
       $to_date=$request->to_date;
       $location=$request->location;
       $area=$request->area;
       $otgiven=$request->otgiven;
       $from_date1=$request->from_date1;
       $to_date1=$request->to_date1;
       $salary=$request->salary;  
       $user=auth()->user()->id;
       $start=date_create($from_date);
       $end=date_create($to_date);
       $diff=date_diff($start,$end);
       $start1=date_create($from_date1);
       $end1=date_create($to_date1);
       $diff1=date_diff($start1,$end1);

       // dd($diff->days);
       $from_date_month=date('Y-m', strtotime($from_date));
       $to_date_month=date('Y-m', strtotime($to_date));
       $sysmonth=date('Y-m');
        // dd($sysmonth);
        
      // dd($request->all());
############# conition check start ################


         $month_cheque = DB::table('hr_partial_salary_master')
                        ->where('unit_id',$unit)
                        ->where('as_status',$otnonot)
                        ->where('salary_from_date','like',$from_date_month.'%')
                        ->count();


                        if ( $month_cheque>0){
                            toastr()->error('Process can not complite .This unit data process complite. please check view');
                            return redirect('hr/operation/partial-salary-sheet');
                        };

                        if ( $from_date_month<$sysmonth or $to_date_month<$sysmonth){
                            toastr()->error('Process can not complite . Salary month is less then current month.');
                            return redirect('hr/operation/partial-salary-sheet');
                        };

                        if ( $from_date_month>$sysmonth or $to_date_month>$sysmonth){
                            toastr()->error('Process can not complite . Salary month is gater then current month.');
                            return redirect('hr/operation/partial-salary-sheet');
                        };

                        if ($from_date>$to_date){
                            toastr()->error('From date is gater then to date. Please correct');
                            return redirect('hr/operation/partial-salary-sheet');
                        };

                        if ($diff->days<=8 ){
                            toastr()->error('From date to date difference should be 10 days. Please correct');
                            return redirect('hr/operation/partial-salary-sheet');
                        };
                        if ($otnonot=='1' ){
                           $salary=50000;
                       };
                       if ($otnonot=='0' ){
                        $otgiven='NO';
                        $from_date1='0000-00-00';
                        $to_date1='0000-00-00';
                    };

                    if ($otgiven=='NO' ){
                        $from_date1='0000-00-00';
                        $to_date1='0000-00-00';
                    };

                    if ($otgiven=='YES' ){
                        if ($diff1->days<=8 ){
                            toastr()->error('OT From date and to date difference should be 10 days. Please correct');
                            return redirect('hr/operation/partial-salary-sheet');
                        };
                    }
       
############# conition check end ##############

       $unit_query = DB::table('hr_unit')
       ->whereIn('hr_unit_id', auth()->user()->unit_permissions());
        if($unit != '' && $unit != null){
        $unit_query->where('hr_unit_id',$unit);
          }   
        $unit= $unit_query->pluck('hr_unit_id')->toArray();

        $location_query = DB::table('hr_location')
       ->whereIn('hr_location_id', auth()->user()->location_permissions());
        if($location != '' && $location != null){
        $location_query->where('hr_location_id',$location);
          }   
        $location= $location_query->pluck('hr_location_id')->toArray();

        $area_query = DB::table('hr_area');
        $area= $area_query->pluck('hr_area_id')->toArray();
      

       // dd( $unit );

          DB::beginTransaction();
          try {
             foreach ($unit as $key => $value) { 
            // dd()
                DB::table('hr_partial_salary_master')->insert([
                    [
                        'unit_id' => $value,
                        'as_status' =>$otnonot,
                        'salary_from_date' => $from_date,
                        'salary_to_date' => $to_date,
                        'ot_give_status' =>$otgiven,
                        'ot_from_date' => $from_date1,
                        'ot_to_date' => $to_date1,
                        'salary_below' => $salary,
                        'create_by' => $user,
                        'last_update_by' => $user,
                        'last_update_date' => Carbon::now()->toDateString()
                    ]
                ]);

       // dd($area);
                $hr_partial_m_id=DB::getPdo()->lastInsertId();

                foreach ($location as $key => $value) { 

                    DB::table('hr_partial_salary_location_child')->insert([
                        [
                            'hr_partial_m_id' => $hr_partial_m_id,
                            'hr_location_id' =>$value,
                            'create_by' => $user,
                            'last_update_by' => $user
                        ]
                    ]);
                }


                foreach ($area as $key => $value) { 
                    DB::table('hr_partial_salary_area_child')->insert([
                        [
                            'hr_partial_m_id' => $hr_partial_m_id,
                            'hr_area_id' =>$value,
                            'create_by' => $user,
                            'last_update_by' => $user
                        ]
                    ]);
                }

            }

            DB::commit();

        } catch (\Exception $e) {
            // dd($e->getMessage())
            dd($e->getMessage());
            // toastr()->warning(dd($e->getMessage()));
            DB::rollback();
            return back()->withInput();
        }
toastr()->success('Save success....');
return redirect('hr/operation/partial-salary-sheet');

  
       
    }

public function getupdatedata($id)
    {
            $partial_salary_master_data = DB::table('hr_partial_salary_master')
                                        ->where('id',$id)
                                        ->first();
                                        

            $partial_salary_location_data = DB::table('hr_partial_salary_location_child')
                                            ->where('hr_partial_m_id',$id)
                                            ->pluck('hr_location_id')
                                            ->toArray();
            $partial_salary_area_data = DB::table('hr_partial_salary_area_child')
                                            ->where('hr_partial_m_id',$id)
                                            ->pluck('hr_area_id')
                                            ->toArray();
// dd($partial_salary_master_data);

        return [
            'master' => $partial_salary_master_data,
            'location'  => $partial_salary_location_data,
            'area'  => $partial_salary_area_data
        ];
    }
    

public function updatedata(Request $request)
    {

        // dd($request->all());
       $id=$request->id;
       $unit=$request->unit;
       $otnonot=$request->otnonot;
       $from_date=$request->from_date;
       $to_date=$request->to_date;
       $location=$request->location;
       $area=$request->area;
       $otgiven=$request->otgiven;
       $from_date1=$request->from_date1;
       $to_date1=$request->to_date1;
       $salary=$request->salary;  
       $user=auth()->user()->id;
       $start=date_create($from_date);
       $end=date_create($to_date);
       $diff=date_diff($start,$end);
       $start1=date_create($from_date1);
       $end1=date_create($to_date1);
       $diff1=date_diff($start1,$end1);

       // dd($diff->days);
       $from_date_month=date('Y-m', strtotime($from_date));
       $to_date_month=date('Y-m', strtotime($to_date));
       $sysmonth=date('Y-m');
       $from_date1_month=date('Y-m', strtotime($from_date1));
       $to_date1_month=date('Y-m', strtotime($to_date1));
        // dd($sysmonth);
        
      // dd($request->all());
############# conition check start ################

                           $validator = Validator::make($request->all(), [
                            'unit' => 'required',
                            'otnonot' => 'required',
                            'from_date' => 'required',
                            'to_date' => 'required',
                            'otgiven' => 'required',
                            'salary' => 'required|numeric|min:10000'  
                        ]);


                           if($validator->fails()){
                            return [
                                'type' => 'error',
                                'msg' => $validator->errors()->first()
                            ];
                        };

                        $month_cheque = DB::table('hr_partial_salary_master')
                        ->where('unit_id',$unit)
                        ->where('as_status',$otnonot)
                        ->where('salary_from_date','like',$from_date_month.'%')
                        ->where('id','<>',$id)
                        ->count();


                        if ( $month_cheque>0){
                            return [
                                'type' => 'error',
                                'msg' => 'Already exist data this condition..'
                            ];
                        };

                        if ( $location=='' or $area==''){
                            return [
                                'type' => 'error',
                                'msg' => 'Please type location and area'
                            ];
                        };



                                    if ( $from_date_month<$sysmonth or $to_date_month<$sysmonth){
                                        return [
                                            'type' => 'error',
                                            'msg' => 'Process can not complite . Salary month is less then current month.'
                                        ];
                                    };

                                    if ( $from_date_month>$sysmonth or $to_date_month>$sysmonth){
                                      return [
                                        'type' => 'error',
                                        'msg' => 'Process can not complite . Salary month is gater then current month.'
                                    ];
                                };

                                if ($from_date>$to_date){
                                    return [
                                        'type' => 'error',
                                        'msg' => 'From date is gater then to date. Please correct'
                                    ];
                                };

                                if ($diff->days<=8 ){
                                    return [
                                        'type' => 'error',
                                        'msg' => '10 days below salary are not allow. Please correct'
                                    ];
                                };

                                if ($otnonot=='1' ){
                                 $salary=50000;
                             };

                             if ($otnonot=='0' ){
                                $otgiven='NO';
                                $from_date1='0000-00-00';
                                $to_date1='0000-00-00';
                            };

                            if ($otgiven=='NO' ){
                                $from_date1='0000-00-00';
                                $to_date1='0000-00-00';
                            };

                            if ($otgiven=='YES' ){

                                if ($from_date1>$to_date1){
                                    return [
                                        'type' => 'error',
                                        'msg' => 'OT From date is gater then OT to-date. Please correct'
                                    ];
                                };

                                if ($diff1->days<=8 ){
                                    return [
                                        'type' => 'error',
                                        'msg' => '10 days below OT are not allow. Please correct'
                                    ];
                                };

                                if ( $from_date1_month<$sysmonth or $to_date1_month<$sysmonth){
                                    return [
                                        'type' => 'error',
                                        'msg' => 'Process can not complite . Salary month is less then current month.'
                                    ];
                                };

                                if ( $from_date1_month>$sysmonth or $to_date1_month>$sysmonth){
                                    return [
                                        'type' => 'error',
                                        'msg' => 'Process can not complite . Salary month is gater then current month.'
                                    ];
                                };
                            }
       
############# conition check end ##############

                

          DB::beginTransaction();
          try {

            // dd();
                     DB::table('hr_partial_salary_master')
                        ->where('id', $id)
                        ->update([
                                    'unit_id' => $unit,
                                    'as_status' =>$otnonot,
                                    'salary_from_date' => $from_date,
                                    'salary_to_date' => $to_date,
                                    'ot_give_status' =>$otgiven,
                                    'ot_from_date' => $from_date1,
                                    'ot_to_date' => $to_date1,
                                    'salary_below' => $salary,
                                    'create_by' => $user,
                                    'last_update_by' => $user,
                                    'last_update_date' => Carbon::now()->toDateString()
                        ]);

        $delete=DB::table('hr_partial_salary')
            ->where('partial_master_id',$id)
            ->delete();

        $affected1=DB::table('hr_partial_salary_location_child')->where('hr_partial_m_id',$id)->delete();
        $affected2=DB::table('hr_partial_salary_area_child')->where('hr_partial_m_id',$id)->delete();
     

                foreach ($location as $key => $value) { 
     // dd($id);
                    DB::table('hr_partial_salary_location_child')->insert([
                        [
                            'hr_partial_m_id' => $id,
                            'hr_location_id' =>$value,
                            'create_by' => $user,
                            'last_update_by' => $user
                        ]
                    ]);

                }


                foreach ($area as $key => $value) { 

                    DB::table('hr_partial_salary_area_child')->insert([
                        [
                            'hr_partial_m_id' => $id,
                            'hr_area_id' =>$value,
                            'create_by' => $user,
                            'last_update_by' => $user
                        ]
                    ]);

                }

       
            DB::commit();
             return [
                    'type' => 'success',
                    'msg' => 'update Successful'
                    ];

        } catch (\Exception $e) {
            // dd($e->getMessage())
            // dd($e->getMessage());
            DB::rollback();
            // return back()->withInput();
             return [
                    'type' => 'error',
                    'msg' => $e->getMessage()
             ];
        }


                   

  
       
    }



    public function partdataload(Request $request)
    {
       $unit=$request->unit1;
       $month=$request->month;
      
        $month=date('m-Y', strtotime($month));
         // dd($month);00-00-0000
        if($unit ==145){

        $unit =implode(',',str_split($unit));
          }   
        $process_paramiter =  DB::select('select * 
            from hr_partial_salary_process_view
        where find_in_set(unit_id  ,"'.$unit.'")
        and substr(salary_from_date,4,8)="'.$month.'"
        ');

// dd($process_paramiter);
return view('hr.operation.salary.partial_salary_loadtabledata', compact('process_paramiter'))->render();
    }


 public function unitdtl($id)

    {
                    $find_partial_paramiter = DB::table('hr_partial_salary_master')
                    ->where('id',$id)
                    ->first();
                            

                    $partial_salary_group_data =  DB::select('select a.hr_unit_name ,count(id) total_employee,round(sum(b.total_payable)) total_salary ,round((sum(b.ot_amount))) ot_amount,a.as_ot_name
                        from hr_basic_info_view a,hr_partial_salary b 
                        where a.as_id =b.as_id
                        and b.`month` ="'.date('m', strtotime($find_partial_paramiter->salary_from_date )).'"
                        and b.`year` ="'.date('Y', strtotime($find_partial_paramiter->salary_from_date )).'"
                        and a.hr_unit_id ="'.$find_partial_paramiter->unit_id.'"
                        group by a.hr_unit_name,b.ot_status,a.as_ot_name
                        ');
                                    // and b.ot_status ="'.$find_partial_paramiter->as_status.'"
                            // dd($partial_salary_group_data);

                    return view('hr.operation.salary.partial_salary_loadtabledata2', compact('partial_salary_group_data','find_partial_paramiter'))->render();


    }



 public function delete($id)
    {

        // dd($id);

      DB::beginTransaction();
      try {
         $delete=DB::table('hr_partial_salary')
            ->where('partial_master_id',$id)
            ->delete();

        $affected1=DB::table('hr_partial_salary_location_child')->where('hr_partial_m_id',$id)->delete();
        $affected2=DB::table('hr_partial_salary_area_child')->where('hr_partial_m_id',$id)->delete();
        $affected3=DB::table('hr_partial_salary_master')->where('id',$id)->delete();
         DB::commit();
        return 'success';
    } catch (\Exception $e) {
            // dd($e->getMessage())
        dd($e->getMessage());
        // toastr()->warning(dd($e->getMessage()));
        DB::rollback();
        return back()->withInput();
    }
//        toastr()->error('DELETE success');


    }



 public function salaryemp($id)
    {
$find_emp_list = DB::table('hr_partial_salary_master')
                            ->where('id',$id)
                            ->first();

                            $find_location = DB::table('hr_partial_salary_location_child')
                            ->where('hr_partial_m_id',$id)
                            ->pluck('hr_location_id');
               
                            $find_area = DB::table('hr_partial_salary_area_child')
                            ->where('hr_partial_m_id',$id)
                            ->pluck('hr_area_id');



        $basic_employee = DB::table('hr_basic_info_view')
                            ->select('as_id')
                            ->where('as_ot', $find_emp_list->as_status)
                            ->where('hr_unit_id', $find_emp_list->unit_id)
                            ->where('salary','<=', $find_emp_list->salary_below)
                            ->where('as_status',1)
                            ->whereIn('as_location_id',$find_location)
                            ->whereIn('as_area_id',$find_area)
                            ->get();

                // dd($basic_employee);            

            $delete=DB::table('hr_partial_salary')
            ->where('month', date('m', strtotime($find_emp_list->salary_from_date )))
            ->where('year', date('Y', strtotime($find_emp_list->salary_from_date )))
            ->where('unit_id', $find_emp_list->unit_id) 
            ->where('ot_status', $find_emp_list->as_status)
            ->delete();

 $basic_employee222=collect($basic_employee)->chunk(200)->toArray();

return view('hr.operation.salary.partial_salary_loadtabledata1', compact('basic_employee222','find_emp_list'))->render();

    }




    public function process(Request $request)
    {
         
         // dd($request->rules['unit_id']);

        $data=$request->data;
        $rules=$request->rules;
        $salary_from_date=$request->rules['salary_from_date'];
        $salary_to_date=$request->rules['salary_to_date'];
        $ot_give_status=$request->rules['ot_give_status'];
        $ot_from_date=$request->rules['ot_from_date'];
        $ot_from_date=$request->rules['ot_from_date'];
        $ot_to_date=$request->rules['ot_to_date'];
        $unit_id=$request->rules['unit_id'];
        $partial_master_id=$request->rules['id'];
        $start=date_create($salary_from_date);
        $end=date_create($salary_to_date);
        // $salary_date_difference=date_diff($start,$end);

        $salary_date_difference= Carbon::parse($salary_from_date)->diffInDays($salary_to_date)+1;

        
        // dd($salary_date_difference);

         $for_emp_insert = DB::table('hr_partial_salary')
                        ->where('month', date('m', strtotime($salary_from_date )))
                        ->where('year', date('Y', strtotime($salary_from_date )))
                        ->pluck('as_id')
                        ->toArray();   
    
            // $delete=DB::table('hr_partial_salary')
            // ->where('month', date('m', strtotime($salary_from_date )))
            // ->where('year', date('Y', strtotime($salary_from_date )))
            // ->where('unit_id', $unit_id) 
            // ->delete();

 $arr=[];
        foreach ($data as $key => $value) {
             
                            // dd($for_emp_insert);
              if (!in_array($value['as_id'], $for_emp_insert)) {
                  $arrr =  DB::select('call hr_partial_salary_prc
                    (
                    "'.$value['as_id'].'"
                    ,"'.$salary_from_date.'"
                    ,"'.$salary_to_date.'"
                    ,"'.$ot_give_status.'"
                    ,"'.$ot_from_date.'"
                    ,"'.$ot_to_date.'"
                    ,"'.$unit_id.'"
                    )
                    ');

                              if(isset($arrr[0])){

        $tot_holiday=($arrr[0]->general_holiday +$arrr[0]->emergency_holiday_ot+$arrr[0]->holiday_roaster);
        $tot_present=($arrr[0]->present+$arrr[0]->general_holiday +$arrr[0]->emergency_holiday_ot+$arrr[0]->holiday_roaster +$arrr[0]->leaves);
        $tot_ot_amount= (($arrr[0]->ben_basic/104) * $arrr[0]->ot_hour);
                            
                            if ($arrr[0]->as_ot==1){
                                    $ot_rate1=$arrr[0]->ben_basic/104;
                                }else{
                                    $ot_rate1=0;
                                }
        $last_day=date('t', strtotime($salary_from_date ));
        $salary_payable=($arrr[0]->ben_current_salary/$last_day) *($tot_present) ;
        $total_payable=ceil((((($arrr[0]->ben_current_salary/$last_day) *($tot_present) +$tot_ot_amount)) -0));

        // $cash_pay=0;
        // $bank_pay=0;

        if ($arrr[0]->ben_current_salary==$arrr[0]->ben_cash_amount)
        {
            $cash_pay=$total_payable;
            $bank_pay=0;
            $pay_type='C';
        }

        elseif ($arrr[0]->ben_current_salary==$arrr[0]->ben_bank_amount)
        {
            $bank_pay=$total_payable;
            $cash_pay=0;
            $pay_type='B';
        }
        else
        {
            // if bank part palary is  less then total payble salary  
            if ( (($arrr[0]->ben_bank_amount/$last_day)*$salary_date_difference) <=$total_payable )
            {
                // $bank_pay=$total_payable;
                $bank_pay=(($arrr[0]->ben_bank_amount/$last_day)*$salary_date_difference);
                $cash_pay=$total_payable-$bank_pay;
                $pay_type='P';
                // dd('hi1');
            }else
            {
                // if bank part palary is  gater then total payble salary  
                // $bank_pay=(($arrr[0]->ben_bank_amount/$last_day)*$salary_date_difference);
                $bank_pay=$total_payable;
                // $cash_pay=$total_payable-$bank_pay;
                $cash_pay=$total_payable-$bank_pay;
                $pay_type='P';
                 // dd('hi222');
            }

            
        }
        


$arr[] = [
    'as_id' => $arrr[0]->as_id,
    'partial_master_id' => $partial_master_id,
    'ot_status' => $arrr[0]->as_ot,
    'month' => date('m', strtotime($salary_from_date )),
    'year' => date('Y', strtotime($salary_from_date )),
    'gross' => $arrr[0]->ben_current_salary,
    'basic' => $arrr[0]->ben_basic,
    'house' => $arrr[0]->ben_house_rent,
    'medical' => $arrr[0]->ben_medical,
    'transport' => $arrr[0]->ben_transport,
    'food' => $arrr[0]->ben_food,
    'late_count' => $arrr[0]->late,
    'present' => $arrr[0]->present,
    'holiday' => $tot_holiday,
    'absent' => $arrr[0]->absent,
    'leave' => $arrr[0]->leaves,
    'absent_deduct' => 0 ,//($arrr[0]->ben_basic/30)*$arrr[0]->absent,
    'salary_payable' => $salary_payable ,
    'ot_rate' => $ot_rate1,
    'ot_hour' => $arrr[0]->ot_hour,
    'ot_amount' => $tot_ot_amount,
    'stamp' => 0,
    'emp_status' => $arrr[0]->as_status,
    'total_payable' => $total_payable,
    'cash_payable' => $cash_pay,
    'bank_payable' => $bank_pay,
    'bank_part_salary' => $arrr[0]->ben_bank_amount,
    'cash_part_salary' => $arrr[0]->ben_cash_amount,
    'unit_id' => $arrr[0]->as_unit_id,
    'designation_id' => $arrr[0]->as_designation_id,
    'sub_section_id' => $arrr[0]->as_section_id,
    'location_id' => $arrr[0]->as_location,
    'pay_status' => $pay_type,
    'pay_type' => $arrr[0]->bank_name,
    'created_by' => auth()->user()->id,
    'roaster_status' => $arrr[0]->shift_roaster_status,
    'updated_by' => auth()->user()->id,

];

                                                }

               
                // end if insert
            }
            // end insert  else  
            // else{  // update   statrelse  

            //    // dd('ddd');

            //       $arrrq =  DB::select('call hr_partial_salary_prc
            //         (
            //         "'.$value['as_id'].'"
            //         ,"'.$salary_from_date.'"
            //         ,"'.$salary_to_date.'"
            //         ,"'.$ot_give_status.'"
            //         ,"'.$ot_from_date.'"
            //         ,"'.$ot_to_date.'"
            //         )
            //         ');

            //      if(isset($arrrq[0])){
            //       $tot_holiday=($arrrq[0]->general_holiday +$arrrq[0]->emergency_holiday_ot+$arrrq[0]->holiday_roaster);
            //         $tot_present=($arrrq[0]->present+$arrrq[0]->general_holiday +$arrrq[0]->emergency_holiday_ot+$arrrq[0]->holiday_roaster +$arrrq[0]->leaves);
            //         $tot_ot_amount= (($arrrq[0]->ben_basic/104) * $arrrq[0]->ot_hour);

            //         $arrq = [
            //             'ot_status' => $arrrq[0]->as_ot,
            //             'month' => date('m', strtotime($salary_from_date )),
            //             'year' => date('Y', strtotime($salary_from_date )),
            //             'gross' => $arrrq[0]->ben_current_salary,
            //             'basic' => $arrrq[0]->ben_basic,
            //             'house' => $arrrq[0]->ben_house_rent,
            //             'medical' => $arrrq[0]->ben_medical,
            //             'transport' => $arrrq[0]->ben_transport,
            //             'food' => $arrrq[0]->ben_food,
            //             'late_count' => $arrrq[0]->late,
            //             'present' => $arrrq[0]->present,
            //             'holiday' => $tot_holiday,
            //             'absent' => $arrrq[0]->absent,
            //             'leave' => $arrrq[0]->leaves,
            //             'absent_deduct' => ($arrrq[0]->ben_basic/30)*$arrrq[0]->absent,
            //             'salary_payable' => ($arrrq[0]->ben_current_salary/30) *($tot_present) ,
            //             'ot_rate' => $arrrq[0]->ben_basic/104,
            //             'ot_hour' => $arrrq[0]->ot_hour,
            //             'stamp' => 10,
            //             'emp_status' => $arrrq[0]->as_status,
            //             'total_payable' => (($arrrq[0]->ben_current_salary/30) *($tot_present) +$tot_ot_amount),
            //             'cash_payable' => 0,
            //             'bank_payable' => 0,
            //             'unit_id' => $arrrq[0]->as_unit_id,
            //             'designation_id' => $arrrq[0]->as_designation_id,
            //             'sub_section_id' => $arrrq[0]->as_section_id,
            //             'location_id' => $arrrq[0]->as_location,
            //             'pay_status' => 0,
            //             'pay_type' => 0,
            //             'created_by' => auth()->user()->id,
            //             'roaster_status' => $arrrq[0]->shift_roaster_status,
            //             'updated_by' => auth()->user()->id,

            //         ];
            //                          }           
            //     DB::table('hr_partial_salary')
            //     ->where('month', date('m', strtotime($salary_from_date )))
            //     ->where('year', date('Y', strtotime($salary_from_date )))
            //     ->where('as_id', $arrrq[0]->as_id)
            //     ->update($arrq);
                 
            // } // update   end else  
        }

         DB::table('hr_partial_salary')->insert($arr);
        // dd('ccc');
    }


public function print(Request $request)
    {
        try
        {
            $data['unitList']      = Unit::where('hr_unit_status', '1')
                ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
                ->orderBy('hr_unit_name', 'desc')
                ->pluck('hr_unit_name', 'hr_unit_id');
            $data['locationList']  = Location::where('hr_location_status', '1')
            ->whereIn('hr_location_id', auth()->user()->location_permissions())
            ->orderBy('hr_location_name', 'desc')
            ->pluck('hr_location_name', 'hr_location_id');

            $data['areaList']      = Area::where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
            $data['floorList']     = Floor::getFloorList();
            $data['deptList']      = Department::getDeptList();
            $data['sectionList']   = Section::getSectionList();
            $data['subSectionList'] = Subsection::getSubSectionList();
            $data['salaryMin']      = Benefits::getSalaryRangeMin();
            $data['salaryMax']      = Benefits::getSalaryRangeMax();
            $data['getYear']       = HrMonthlySalary::select('year')->distinct('year')->orderBy('year', 'desc')->pluck('year');


            
               $data_query = DB::table('hr_partial_salary_process_view')
               ->whereIn('unit_id', auth()->user()->unit_permissions());
                $data['process_paramiter']= $data_query->get();



             // dd($data);
            return view('hr.operation.salary.partial_salary_print', $data);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
      

    }



public function printload(Request $request)
{
    try
    {
        // dd($request->all());
            $input=$request->all();
            $unit=$request->unit;
            $location=$request->location;
            $area=$request->area;
            $department=$request->department;
            $section=$request->section;
            $subSection=$request->subSection;
            $floor=$request->floor;
            $line=$request->line;
            $otnonot=$request->otnonot;
            $min_sal=$request->min_sal;
            $max_sal=$request->max_sal;
            $perpage=$request->perpage;
            $paymentType=$request->paymentType;
            $month=date('m', strtotime($request->month));
            $year=date('Y', strtotime($request->month));
            $estatus=$request->estatus;
// dd($input);
            if($unit ==145 or $unit ==14 or $unit ==15){
            $unit =implode(',',str_split($unit));
            } 

            $location = $location??auth()->user()->location_permission;
            $input=$request->all();

            
             $month_year= eng_to_bn(date('F-Y', strtotime($request->month)));
             $unit_name= DB::table('hr_unit')
                ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
                ->where('hr_unit_id', $unit)
                ->pluck('hr_unit_name_bn')
                ->first();
            $department_by_id= DB::table('hr_department')
            ->where('hr_department_id', $department)
            ->pluck('hr_department_name_bn')
            ->first();

              $area_by_id= DB::table('hr_area')
            ->where('hr_area_id', $area)
            ->pluck('hr_area_name_bn')
            ->first();

               $section_by_id= DB::table('hr_section')
            ->where('hr_section_id', $section)
            ->pluck('hr_section_name_bn')
            ->first();

             $subsection_by_id= DB::table('hr_subsection')
            ->where('hr_subsec_id', $subSection)
            ->pluck('hr_subsec_name_bn')
            ->first();


            $location_name = location_by_id()
          ->pluck('hr_location_name', 'hr_location_id');

            //    $floor_name = DB::table('hr_floor_name')
            // ->where('hr_floor_id', $request->floor)
            // ->pluck('hr_floor_name')
            // ->first();

            //      $line_name = DB::table('hr_line')
            // ->where('hr_line_id', $request->line)
            // ->pluck('hr_line_name')
            // ->first();



        $printload_date =  DB::select('call hr_partial_salary_print_prc
            ("'.$unit.'"
            ,"'.$otnonot.'"
            ,"'.$area.'"
            ,"'.$department.'"
            ,"'.$section.'"
            ,"'.$subSection.'"
            ,"'.$month.'"
            ,"'.$year.'"
            ,"'.$min_sal.'"
            ,"'.$max_sal.'"
            ,"'.$paymentType.'"
            ,"'.$location.'"
            ,"'.$request->printtype.'"
            ,"'.$request->floor.'"
            ,"'.$request->line.'"
            )
            ');
        // dd
   // dd($printload_date);
        $chank_size=$perpage;
        $printload_date=collect($printload_date)->chunk($chank_size)->toArray();

        

        if ($request->sheettype=='S') {
   
        return view('hr.operation.salary.partial_salary_printload',compact('printload_date','unit_name','month_year','input','location_name','department_by_id','area_by_id','section_by_id','subsection_by_id','input'))->render();
        }else{
        return view('hr.operation.salary.partial_salary_payslipprintload',compact('printload_date','unit_name','month_year','input','location_name','department_by_id','area_by_id','section_by_id','subsection_by_id','input'))->render();

        }


    } catch(\Exception $e) {
        return $e->getMessage();
    }


}

public function partsalaryexcel(Request $request)
{
    try
    {
        // dd($request->all());
            $unit=$request->unit;
            $location=$request->location;
            $area=$request->area;
            $department=$request->department;
            $section=$request->section;
            $subSection=$request->subSection;
            $floor=$request->floor;
            $line=$request->line;
            $otnonot=$request->otnonot;
            $min_sal=$request->min_sal;
            $max_sal=$request->max_sal;
            $perpage=$request->perpage;
            $paymentType=$request->paymentType;
            $month=date('m', strtotime($request->month));
            $year=date('Y', strtotime($request->month));
            $estatus=$request->estatus;

            if($unit ==145 or $unit ==14 or $unit ==15){
            $unit =implode(',',str_split($unit));
            } 

            $location = $location??auth()->user()->location_permission;

            $input=$request->all();
      

        $printload_date =  DB::select('call hr_partial_salary_print_prc
            ("'.$unit.'"
            ,"'.$otnonot.'"
            ,"'.$area.'"
            ,"'.$department.'"
            ,"'.$section.'"
            ,"'.$subSection.'"
            ,"'.$month.'"
            ,"'.$year.'"
            ,"'.$min_sal.'"
            ,"'.$max_sal.'"
            ,"'.$paymentType.'"
            ,"'.$location.'"
            ,"'.$request->printtype.'"
            ,"'.$floor.'"
            ,"'.$line.'"
            )
            ');


        // $printload_date=$printload_date
        //                 ->select('associate_id')
        //                 ->get()
        //                 ->toArray();
        // dd($printload_date);


    } catch(\Exception $e) {
        return $e->getMessage();
    }
return (new FastExcel($printload_date))->download('Part Salary.csv');

}




    public function submitforapprove($id)

    {

       try {
                $approve_type=DB::table('hr_partial_salary_master')
                         ->Select('approve_status')
                         ->where('id',$id)
                         ->pluck('approve_status')
                         ->first();

                $process_count=DB::table('hr_partial_salary_process_view')
                         ->Select('Process_emp')
                         ->where('id',$id)
                         ->pluck('Process_emp')
                         ->first();
                // dd($process_count);

                if ($approve_type=='S'){
                    return [
                        'type' => 'error',
                        'msg' => 'Already Submited..'
                    ];

                }else if($process_count==0){
                        return [
                        'type' => 'error',
                        'msg' => 'Please Process Salary first..'
                    ];

                }else{


                        $affected=DB::table('hr_partial_salary_master')
                            ->where('id', $id)
                            ->update([
                                'approve_status'=>'S',
                                'approval_submit_date'=>Carbon::now()->toDateString(),
                                'last_update_by'=>auth()->id(),
                                'last_update_date'=>Carbon::now()->toDateTimeString()
                            ]);

                        return [
                                'type' => 'success',
                                'msg' => 'Update successful'
                            ];
                    }


             } catch (\Exception $e) {
         
                    return [
                        'type' => 'error',
                        'msg' => $e->getMessage()
                    ];
                 }

    }



     public function locksalary($id)

    {

       try {
                $approve_type=DB::table('hr_partial_salary_master')
                         ->Select('audit_status')
                         ->where('id',$id)
                         ->pluck('audit_status')
                         ->first();

                $process_count=DB::table('hr_partial_salary_process_view')
                         ->Select('Process_emp','Active_emp')
                         ->where('id',$id)
                         ->get()
                         ->toArray();


                // dd($process_count);

                if ($approve_type=='S'){
                    return [
                        'type' => 'error',
                        'msg' => 'Already Submited..'
                    ];

                }else if($process_count[0]->Process_emp==0){
                        return [
                        'type' => 'error',
                        'msg' => 'Please Process Salary first..'
                    ];

                }else if($process_count[0]->Active_emp!=$process_count[0]->Process_emp){
                        return [
                        'type' => 'error',
                        'msg' => 'Process employeee and Active employee are not same. please check'
                    ];

                }else{

// dd($process_count);
                        $affected=DB::table('hr_partial_salary_master')
                            ->where('id', $id)
                            ->update([
                                'audit_status'=>'S',
                                'audit_submit_date'=>Carbon::now()->toDateString(),
                                'last_update_by'=>auth()->id(),
                                'last_update_date'=>Carbon::now()->toDateTimeString()
                            ]);

                        return [
                                'type' => 'success',
                                'msg' => 'Update successful'
                            ];
                    }


             } catch (\Exception $e) {
         
                    return [
                        'type' => 'error',
                        'msg' => $e->getMessage()
                    ];
                 }

    }





public function approveview()
    {

        try {

            $process_paramiter=DB::table('hr_partial_salary_process_view')
                            ->where('approve_status','S')
                            // ->where('salary_from_date','like','%'.date('m-Y'))
                            ->orderBy('hr_unit_name','asc','Employee_status','asc')
                            ->get()
                            ->toArray();

           $approve_paramiter=DB::table('hr_partial_salary_process_view')
                        ->where('approve_status','Y')
                        // ->where('salary_from_date','like','%'.date('m-Y'))
                        ->orderBy('hr_unit_name','asc','Employee_status','asc')
                        ->get()
                        ->toArray();


             // dd($process_paramiter);
            return view('hr.operation.salary.partial_salary_approval',compact('process_paramiter','approve_paramiter'));
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }





public function approveflag(Request $request ,$id)


    {
// dd($id,$request->all());
       try {
                $approve_type=DB::table('hr_partial_salary_master')
                         ->Select('approve_status')
                         ->where('id',$id)
                         ->pluck('approve_status')
                         ->first();

                if ($approve_type=='Y'){
                    return [
                        'type' => 'error',
                        'msg' => 'Already Approved..'
                    ];

                }else{
                    
                        if($request->redo_flag=='N'){
                        $affected=DB::table('hr_partial_salary_master')
                            ->where('id', $id)
                            ->update([
                                'approve_status'=>'Y',
                                'approval_date'=>Carbon::now()->toDateString(),
                                'last_update_by'=>auth()->id(),
                                'last_update_date'=>Carbon::now()->toDateTimeString()
                            ]);
                        }else{
                        $affected=DB::table('hr_partial_salary_master')
                        ->where('id', $id)
                        ->update([
                            'approve_status'=>'N',
                            'approval_date'=>'',
                            'coment'=>Str::upper($request->coment),
                            'last_update_by'=>auth()->id(),
                            'last_update_date'=>Carbon::now()->toDateTimeString()
                        ]);
                        }
                        return [
                                'type' => 'success',
                                'msg' => 'Update successful'
                            ];
                    }


             } catch (\Exception $e) {
         
                    return [
                        'type' => 'error',
                        'msg' => $e->getMessage()
                    ];
                 }

    }



}
