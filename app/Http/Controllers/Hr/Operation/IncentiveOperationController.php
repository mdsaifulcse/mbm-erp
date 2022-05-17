<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Exports\Hr\IncentiveExport;
use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\Department;
use App\Models\Hr\Location;
use App\Models\Hr\Unit;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class IncentiveOperationController extends Controller
{
    public function __construct()
    {
        ini_set('zlib.output_compression', 1);
    }
    public function index()
    {
        try {
            $unit = unit_by_id();
            $data['unitList'] = collect($unit)->pluck('hr_unit_name', 'hr_unit_id');
            $location = location_by_id();
            $data['locationList']  = collect($location)->pluck('hr_location_name', 'hr_location_id');
            $area = area_by_id();
            $data['areaList']      = collect($area)->pluck('hr_area_name', 'hr_area_id');
            $department = department_by_id();
            $data['departmentList'] = collect($department)->pluck('hr_department_name', 'hr_department_id');
            
            return view('hr.operation.incentive.index', $data);
        } catch(\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function filterWise(Request $request)
    {
        $input = $request->all();
        
        $input['department'] = $input['department']??'';
        $input['section'] = $input['section']??'';
        $input['subSection'] = $input['subSection']??'';
        $input['area'] = $input['area']??'';
        $input['location'] = $input['location']??'';
        
        try {
            if($input['date_type'] == 'month'){
                $input['from_date'] = $input['month_year'].'-01';
                $input['to_date'] = Carbon::parse($input['from_date'])->endOfMonth()->toDateString();
            }

            $input['month'] = date('m', strtotime($input['to_date']));
            $input['year'] = date('Y', strtotime($input['to_date']));
            // dd($input);
            // employee info
            $employeeData = DB::table('hr_as_basic_info');
            $employeeDataSql = $employeeData->toSql();

            // employee benefit sql binding
            $benefitData = DB::table('hr_benefits');
            $benefitData_sql = $benefitData->toSql();
            
            // employee bangla info
            $employeeBanData = DB::table('hr_employee_bengali');
            $employeeBanDataSql = $employeeBanData->toSql();

            // salary info
            $salaryData = DB::table('hr_monthly_salary');
            $salaryDataSql = $salaryData->toSql();

            $queryData = DB::table('hr_incentive_bonus as s')
            ->whereIn('sa.unit_id', auth()->user()->unit_permissions())
            ->whereIn('sa.location_id', auth()->user()->location_permissions())
            ->where('sa.month', $input['month'])
            ->where('sa.year', $input['year'])
            ->whereBetween('s.date', [$input['from_date'],$input['to_date']])
            ->when(!empty($input['as_id']), function ($query) use($input){
                return $query->whereIn('emp.associate_id', $input['as_id']);
            })
            ->when(!empty($input['unit']), function ($query) use($input){
                if($input['unit'] == 145){
                    return $query->whereIn('sa.unit_id',[1, 4, 5]);
                }else{
                    return $query->where('sa.unit_id',$input['unit']);
                }
            })
            ->when(!empty($input['location']), function ($query) use($input){
               return $query->where('sa.location_id',$input['location']);
            })
            ->when(!empty($input['area']), function ($query) use($input){
               return $query->where('emp.as_area_id',$input['area']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('emp.as_department_id',$input['department']);
            })
            ->when(!empty($input['line']), function ($query) use($input){
               return $query->where('emp.as_line_id', $input['line']);
            })
            ->when(!empty($input['floor']), function ($query) use($input){
               return $query->where('emp.as_floor_id',$input['floor']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('emp.as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use($input){
               return $query->where('emp.as_subsection_id', $input['subSection']);
            })
            ->when(!empty($input['pay_status']), function ($query) use($input){
                if($input['pay_status'] == 'cash'){
                    return $query->where('ben.ben_cash_amount', '>', 0);
                }elseif($input['pay_status'] != 'all'){
                    return $query->where('ben.ben_bank_amount', '>', 0)->where('ben.bank_name', $input['pay_status']);
                }
            });
            if(isset($input['otnonot']) && $input['otnonot'] != null){
                $queryData->where('sa.ot_status',$input['otnonot']);
            }
            if(isset($input['as_status']) && $input['as_status'] != null){
                if($input['as_status'] == 1){
                    $queryData->whereIn('emp.as_status', [1,6]);
                }else{
                    $queryData->where('emp.as_status', $input['as_status']);
                }
            }
            if(isset($input['pay_status']) && $input['pay_status'] != null){
                $queryData->where('s.pay_status', $input['pay_status']);
            }
            
            $queryData->leftjoin(DB::raw('(' . $employeeDataSql. ') AS emp'), function($join) use ($employeeData) {
                $join->on('emp.as_id','s.as_id')->addBinding($employeeData->getBindings());
            })
            ->leftjoin(DB::raw('(' . $salaryDataSql. ') AS sa'), function($join) use ($salaryData) {
                $join->on('sa.as_id','emp.associate_id')->addBinding($salaryData->getBindings());
            });
            $queryData->leftjoin(DB::raw('(' . $benefitData_sql. ') AS ben'), function($join) use ($benefitData) {
                $join->on('ben.ben_as_id','emp.associate_id')->addBinding($benefitData->getBindings());
            });

            $queryData->leftjoin(DB::raw('(' . $employeeBanDataSql. ') AS bemp'), function($join) use ($employeeBanData) {
                $join->on('bemp.hr_bn_associate_id','emp.associate_id')->addBinding($employeeBanData->getBindings());
            });
            
            if(isset($input['output']) && $input['output'] == 'excel'){
                
                $queryData->select('ben.bank_no', 'ben.ben_cash_amount', 'ben.ben_bank_amount','emp.as_name','emp.as_doj', 'sa.ot_status AS as_ot', 'emp.as_designation_id', 'emp.as_section_id', 'sa.location_id AS as_location', 'sa.unit_id AS as_unit_id','emp.as_id','emp.associate_id','emp.as_oracle_code', DB::raw('sum(amount) as totalAmount'), DB::raw('count(*) as totalDay'), DB::raw("SUM(IF(s.pay_status=0,1,0)) AS dueDay"), DB::raw("SUM(IF(s.pay_status=0,amount,0)) AS dueAmount"), 'sa.pay_status as spaystatus')->groupBy('emp.as_id');
                $totalAmount =  array_sum(array_column($queryData->get()->toArray(),'dueAmount'));
                $getBillList = $queryData->orderBy('emp.as_oracle_sl', 'asc')->get();
                return Excel::download(new IncentiveExport($getBillList, $input, 'operation'), 'incentive.xlsx');

            }
            $listData = clone $queryData;
            $queryData->select('emp.as_doj', 'sa.ot_status AS as_ot', 'emp.as_designation_id', 'emp.as_section_id', 'sa.location_id AS as_location', 'bemp.hr_bn_associate_name', 'emp.as_oracle_code', 'sa.unit_id AS as_unit_id','emp.as_id','emp.associate_id', DB::raw('sum(amount) as totalAmount'), DB::raw('count(*) as totalDay'), DB::raw("SUM(IF(s.pay_status=0,1,0)) AS dueDay"), DB::raw("SUM(IF(s.pay_status=0,amount,0)) AS dueAmount"), 'sa.pay_status AS spaystatus')->groupBy('emp.as_id');
            $getBillList = $queryData->orderBy('emp.as_oracle_sl', 'asc')->orderBy('emp.temp_id', 'asc')->get();
            // dd($getBillList);
            $totalAmount =  $getBillList->sum('totalAmount');
            $employeeKey = array_column($getBillList->toArray(), 'as_id');

            $getBillLists = $listData->select('s.*')->orderBy('s.date', 'asc')->get()->groupBy('as_id',true);
            $totalEmployees = count($getBillLists);
            // dd($totalEmployees);
            // attendance info
            if(!empty($input['as_id'])){
                $unitId = 1;
                if(count($getBillList) > 0){
                    $unitId = $getBillList[0]->as_unit_id;
                }
                $tableName = get_att_table($unitId);
            }else{
                $tableName = get_att_table($request['unit']);
            }

            $attData = DB::table($tableName)
            ->select('in_date','as_id', 'in_time', 'out_time')
            ->whereIn('as_id',$employeeKey)
            ->whereBetween('in_date', [$input['from_date'],$input['to_date']])
            ->get()->toArray();


            $attendance = collect($attData)->groupBy('as_id',true)->map(function($row) {
                        return collect($row)->keyBy('in_date');
                    });

            // employee designation
            $designation = designation_by_id();
            $section = section_by_id();
            // return $designation;

            $unitDataSet = $getBillList->toArray();
            $unitList = array_column($unitDataSet, 'as_unit_id');
            $uniqueUnit = array_unique($unitList);
            $getBillDataSet = array_chunk($unitDataSet, 23, true);
            $pageHead['totalBill'] = $totalAmount;
            $pageHead['totalEmployees'] = $totalEmployees;
            // dd($getBillDataSet);
            return view('hr.operation.incentive.report', compact('getBillList', 'designation', 'section', 'uniqueUnit', 'input', 'getBillDataSet', 'getBillLists', 'pageHead','attendance'));
        } catch (\Exception $e) {
            // return 'error';
            return $e->getMessage();
        }
    }
    // public function filterWise(Rfp $request)
    // {
    //     $input = $request->all();
    //     $input['department'] = $input['department']??'';
    //     $input['section'] = $input['section']??'';
    //     $input['subSection'] = $input['subSection']??'';
    //     $input['area'] = $input['area']??'';
    //     $input['location'] = $input['location']??'';
        
    //     try {
    //         if($input['date_type'] == 'month'){
    //             $input['from_date'] = $input['month_year'].'-01';
    //             $input['to_date'] = Carbon::parse($input['from_date'])->endOfMonth()->toDateString();
    //         }

    //         // employee info
    //         $employeeData = DB::table('hr_as_basic_info');
    //         $employeeDataSql = $employeeData->toSql();

    //         // employee benefit sql binding
    //         $benefitData = DB::table('hr_benefits');
    //         $benefitData_sql = $benefitData->toSql();
            
    //         // employee bangla info
    //         $employeeBanData = DB::table('hr_employee_bengali');
    //         $employeeBanDataSql = $employeeBanData->toSql();

    //         $queryData = DB::table('hr_incentive_bonus as s')
    //         ->whereBetween('s.date', [$input['from_date'],$input['to_date']])
    //         ->whereIn('emp.as_unit_id', auth()->user()->unit_permissions())
    //         ->whereIn('emp.as_location', auth()->user()->location_permissions())

    //         ->when(!empty($input['as_id']), function ($query) use($input){
    //             return $query->whereIn('emp.associate_id', $input['as_id']);
    //         })
    //         ->when(!empty($input['unit']), function ($query) use($input){
    //             if($input['unit'] == 145){
    //                 return $query->whereIn('emp.as_unit_id',[1, 4, 5]);
    //             }else{
    //                 return $query->where('emp.as_unit_id',$input['unit']);
    //             }
    //         })
    //         ->when(!empty($input['location']), function ($query) use($input){
    //           return $query->where('emp.as_location',$input['location']);
    //         })
    //         ->when(!empty($input['area']), function ($query) use($input){
    //           return $query->where('emp.as_area_id',$input['area']);
    //         })
    //         ->when(!empty($input['department']), function ($query) use($input){
    //           return $query->where('emp.as_department_id',$input['department']);
    //         })
    //         ->when(!empty($input['line']), function ($query) use($input){
    //           return $query->where('emp.as_line_id', $input['line']);
    //         })
    //         ->when(!empty($input['floor']), function ($query) use($input){
    //           return $query->where('emp.as_floor_id',$input['floor']);
    //         })
    //         ->when(!empty($input['section']), function ($query) use($input){
    //           return $query->where('emp.as_section_id', $input['section']);
    //         })
    //         ->when(!empty($input['subSection']), function ($query) use($input){
    //           return $query->where('emp.as_subsection_id', $input['subSection']);
    //         })
    //         ->when(!empty($input['pay_status']), function ($query) use($input){
    //             if($input['pay_status'] == 'cash'){
    //                 return $query->where('ben.ben_cash_amount', '>', 0);
    //             }elseif($input['pay_status'] != 'all'){
    //                 return $query->where('ben.ben_bank_amount', '>', 0)->where('ben.bank_name', $input['pay_status']);
    //             }
    //         });
    //         if(isset($input['otnonot']) && $input['otnonot'] != null){
    //             $queryData->where('emp.as_ot',$input['otnonot']);
    //         }
    //         if(isset($input['as_status']) && $input['as_status'] != null){
    //             if($input['as_status'] == 1){
    //                 $queryData->whereIn('emp.as_status', [1,6]);
    //             }else{
    //                 $queryData->where('emp.as_status', $input['as_status']);
    //             }
    //         }
    //         if(isset($input['pay_status']) && $input['pay_status'] != null){
    //             $queryData->where('s.pay_status', $input['pay_status']);
    //         }
            
    //         $queryData->leftjoin(DB::raw('(' . $employeeDataSql. ') AS emp'), function($join) use ($employeeData) {
    //             $join->on('emp.as_id','s.as_id')->addBinding($employeeData->getBindings());
    //         });
    //         $queryData->leftjoin(DB::raw('(' . $benefitData_sql. ') AS ben'), function($join) use ($benefitData) {
    //             $join->on('ben.ben_as_id','emp.associate_id')->addBinding($benefitData->getBindings());
    //         });

    //         $queryData->leftjoin(DB::raw('(' . $employeeBanDataSql. ') AS bemp'), function($join) use ($employeeBanData) {
    //             $join->on('bemp.hr_bn_associate_id','emp.associate_id')->addBinding($employeeBanData->getBindings());
    //         });
            
    //         if(isset($input['output']) && $input['output'] == 'excel'){
                
    //             $queryData->select('ben.bank_no', 'ben.ben_cash_amount', 'ben.ben_bank_amount','emp.as_name','emp.as_doj', 'emp.as_ot', 'emp.as_designation_id', 'emp.as_section_id', 'emp.as_location', 'emp.as_unit_id','emp.as_id','emp.associate_id','emp.as_oracle_code', DB::raw('sum(amount) as totalAmount'), DB::raw('count(*) as totalDay'), DB::raw("SUM(IF(pay_status=0,1,0)) AS dueDay"), DB::raw("SUM(IF(pay_status=0,amount,0)) AS dueAmount"))->groupBy('emp.as_id');
    //             $totalAmount =  array_sum(array_column($queryData->get()->toArray(),'dueAmount'));
    //             $getBillList = $queryData->orderBy('emp.as_oracle_sl', 'asc')->get();
    //             return Excel::download(new IncentiveExport($getBillList, $input, 'operation'), 'incentive.xlsx');

    //         }
    //         $listData = clone $queryData;
    //         $queryData->select('emp.as_doj', 'emp.as_ot', 'emp.as_designation_id', 'emp.as_section_id', 'emp.as_location', 'bemp.hr_bn_associate_name', 'emp.as_oracle_code', 'emp.as_unit_id','emp.as_id','emp.associate_id', DB::raw('sum(amount) as totalAmount'), DB::raw('count(*) as totalDay'), DB::raw("SUM(IF(pay_status=0,1,0)) AS dueDay"), DB::raw("SUM(IF(pay_status=0,amount,0)) AS dueAmount"))->groupBy('emp.as_id');
    //         $getBillList = $queryData->orderBy('emp.as_oracle_sl', 'asc')->orderBy('emp.temp_id', 'asc')->get();
    //         $totalAmount =  $getBillList->sum('totalAmount');
    //         $employeeKey = array_column($getBillList->toArray(), 'as_id');

    //         $getBillLists = $listData->select('s.*')->orderBy('s.date', 'asc')->get()->groupBy('as_id',true);
    //         $totalEmployees = count($getBillLists);

    //         // attendance info
    //         if(!empty($input['as_id'])){
    //             $unitId = 1;
    //             if(count($getBillList) > 0){
    //                 $unitId = $getBillList[0]->as_unit_id;
    //             }
    //             $tableName = get_att_table($unitId);
    //         }else{
    //             $tableName = get_att_table($request['unit']);
    //         }

    //         $attData = DB::table($tableName)
    //         ->select('in_date','as_id', 'in_time', 'out_time')
    //         ->whereIn('as_id',$employeeKey)
    //         ->whereBetween('in_date', [$input['from_date'],$input['to_date']])
    //         ->get()->toArray();


    //         $attendance = collect($attData)->groupBy('as_id',true)->map(function($row) {
    //                     return collect($row)->keyBy('in_date');
    //                 });

    //         // employee designation
    //         $designation = designation_by_id();
    //         $section = section_by_id();
    //         // return $designation;

    //         $unitDataSet = $getBillList->toArray();
    //         $unitList = array_column($unitDataSet, 'as_unit_id');
    //         $uniqueUnit = array_unique($unitList);
    //         $getBillDataSet = array_chunk($unitDataSet, 23, true);
    //         $pageHead['totalBill'] = $totalAmount;
    //         $pageHead['totalEmployees'] = $totalEmployees;
    //         // dd($getBillDataSet);
    //         return view('hr.operation.incentive.report', compact('getBillList', 'designation', 'section', 'uniqueUnit', 'input', 'getBillDataSet', 'getBillLists', 'pageHead','attendance'));
    //     } catch (\Exception $e) {
    //         // return 'error';
    //         return $e->getMessage();
    //     }
    // }

    public function review(Request $request)
    {
        $input =$request->all();
        if(count($input['pay_id']) == 0){
            return 'warning';
        }
        try {
            if($input['date_type'] == 'month'){
                $input['from_date'] = $input['month_year'].'-01';
                $input['to_date'] = Carbon::parse($input['from_date'])->endOfMonth()->toDateString();
            }

            $employeeData = DB::table('hr_as_basic_info');
            $employeeDataSql = $employeeData->toSql();
            
            // employee bangla info
            $employeeBanData = DB::table('hr_employee_bengali');
            $employeeBanDataSql = $employeeBanData->toSql();

            // employee benefit info
            $benefitData = DB::table('hr_benefits');
            $benefitData_sql = $benefitData->toSql();

            $queryData = DB::table('hr_incentive_bonus as s')
            ->whereBetween('s.date', [$input['from_date'],$input['to_date']])
            ->whereIn('emp.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('emp.as_location', auth()->user()->location_permissions())
            ->where('s.pay_status', 0)
            
            ->whereIn('s.as_id', $input['pay_id']);
            $queryData->leftjoin(DB::raw('(' . $employeeDataSql. ') AS emp'), function($join) use ($employeeData) {
                $join->on('emp.as_id','s.as_id')->addBinding($employeeData->getBindings());
            });

            $queryData->leftjoin(DB::raw('(' . $employeeBanDataSql. ') AS bemp'), function($join) use ($employeeBanData) {
                $join->on('bemp.hr_bn_associate_id','emp.associate_id')->addBinding($employeeBanData->getBindings());
            });
            $listData = clone $queryData;
            $queryData->leftjoin(DB::raw('(' . $benefitData_sql. ') AS ben'), function($join) use ($benefitData) {
                $join->on('ben.ben_as_id','emp.associate_id')->addBinding($benefitData->getBindings());
            });
            
            $queryData->select('ben.bank_no','emp.as_doj', 'emp.as_ot', 'emp.as_designation_id', 'emp.as_section_id', 'emp.as_location', 'bemp.hr_bn_associate_name', 'emp.as_oracle_code', 'emp.as_unit_id','emp.as_id','emp.associate_id', DB::raw('sum(amount) as totalAmount'), DB::raw('count(*) as totalDay'), DB::raw("SUM(IF(pay_status=0,1,0)) AS dueDay"), DB::raw("SUM(IF(pay_status=0,amount,0)) AS dueAmount"))->groupBy('emp.as_id');
            $totalAmount =  array_sum(array_column($queryData->get()->toArray(),'dueAmount'));
            $getBillList = $queryData->orderBy('emp.as_oracle_sl', 'asc')->orderBy('emp.temp_id', 'asc')->get();

            $getBillLists = $listData->select('s.*')->orderBy('s.date', 'asc')->get()->groupBy('as_id',true);
            $totalEmployees = count($getBillLists);

            // employee designation
            $designation = designation_by_id();
            $section = section_by_id();
            // return $designation;

            $unitDataSet = $getBillList->toArray();
            $unitList = array_column($unitDataSet, 'as_unit_id');
            $uniqueUnit = array_unique($unitList);
            $getBillDataSet = array_chunk($unitDataSet, 23, true);
            $pageHead['totalBill'] = $totalAmount;
            $pageHead['totalEmployees'] = $totalEmployees;

            $department = department_by_id();
            $subSection = subSection_by_id();
            $area = area_by_id();
            // dd($getBillDataSet);
            return view('hr.operation.incentive.review', compact('getBillList', 'designation', 'section', 'uniqueUnit', 'input', 'getBillDataSet', 'getBillLists', 'pageHead', 'department', 'subSection', 'area'));
        } catch (\Exception $e) {
            return 'error';
            $data['msg'] = $e->getMessage();
            return $data;
        }
    }

    public function pay(Request $request)
    {
        $data['type'] = 'error';
        $input =$request->all();
        if(count($input['pay_id']) == 0){
            $data['msg'] = 'No Employee Found, Please Select Employee and try again';
            return $data;
        }
        DB::beginTransaction();
        try {
            $queryData = DB::table('hr_incentive_bonus')
            ->whereBetween('date', [$input['from_date'],$input['to_date']])
            ->where('pay_status', 0)
            ->whereIn('as_id', $input['pay_id'])
            ->update([
                'pay_date' => date('Y-m-d'),
                'pay_status' => 1
            ]);

            DB::commit();
            $data['type'] = 'success';
            $data['msg'] = 'Successfully Payment Done';
            return $data;
        } catch (\Exception $e) {
            DB::rollback();
            $data['msg'] = $e->getMessage();
            return $data;
        }
    }
    
}
