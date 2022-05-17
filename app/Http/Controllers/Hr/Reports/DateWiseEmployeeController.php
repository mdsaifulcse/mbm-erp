<?php

namespace App\Http\Controllers\Hr\Reports;
use App\Exports\Hr\SalarySheetExport;
use App\Http\Controllers\Controller;
use App\Models\Hr\Benefits;
use App\Repository\Hr\EmployeeRepository;
use App\Repository\Hr\SalaryRepository;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Carbon\Carbon;
use DB, DataTables;
use Illuminate\Http\Request;

class DateWiseEmployeeController extends Controller
{



   public function __construct()
    {
        ini_set('zlib.output_compression', 1);
    }



    public function index(Request $request)
    {
        $unitList=collect(unit_by_id())->pluck('hr_unit_name','hr_unit_id');


// dd('ddd');
        return view('hr.reports.date_wise_employee', compact('unitList'));
    }



    public function showfilterdata(Request $request)
    {
       $unit=$request->unit;
       $otnonot=$request->otnonot;
       $from_date=$request->from_date;
       $to_date=$request->to_date;
       $Status=$request->Status;
       $Type=$request->Type;
     // dd($unitss);

       $unitList=collect(unit_by_id())->pluck('hr_unit_name','hr_unit_id');

       $hr_basic_info_details=[];
       $hr_basic_info_status_Group=[];
       $hr_basic_info_dept=[];
       $hr_basic_info_dept_Group=[];
       $hr_basic_info_date=[];
       $hr_basic_info_date_Group=[];
         // $cat_id =collect(DB::select("select test(2,1,'2021-10-01','2021-10-01','dddd')  as id"))->first();

       if ($Type==1) {

//
//        $hr_basic_info_details = DB::select('select hr_unit_name,count(associate_id)  Employee_Count,sum(salary) salary,as_status_NAME
//         from hr_basic_info_view
//         where hr_unit_id ="'.$unit.'"
//         and as_ot like "%'.$otnonot.'%"
//         and active_left_date between "'.$from_date.'" and "'.$to_date.'"
//         and as_status like "%'.$Status.'%"
//         group by hr_unit_name,as_status_NAME
//         order by as_status_NAME asc,hr_unit_name asc
//         ');
//
//        $hr_basic_info_status_Group = collect($hr_basic_info_details)
//        ->groupBy('as_status_NAME')
//        ->sortBy('as_status_NAME');
// $unit='';
            // if($unit == null && $unit == ''){
            //     unset($unit);
            // }
            // dd($unit);

                $unit_query = DB::table('hr_unit');
                if($unit != '' && $unit != null){
                $unit_query->where('hr_unit_id',$unit);
                  }   
                $unit_query= $unit_query->pluck('hr_unit_id')->toArray();
                $unit =implode(',',$unit_query);

           // dd($otnonot);
                if($otnonot==null){
                $otnonot="0,1";
                  }   
                

                // $otnonot_query= $otnonot;
                
// dd($otnonot);


           $hr_basic_info_status_Group =  DB::select('call hr_date_wise_employee_prc
            ("'.$unit.'"  
           ,"'.$otnonot.'" 
            ,"'.$from_date.'"  
            ,"'.$to_date.'"  
            ,"'.$Type.'"  
            )');
   // dd($unit);


    }




    if ($Type==2) {
        
        // $hr_basic_info_date = DB::select('select concat(as_status_NAME,hr_unit_name) as_status_NAME,substr(active_left_date,1,7) as_status_date,count(associate_id)  Employee_Count,sum(salary) salary
        //  from hr_basic_info_view
        //  where hr_unit_id like "%'.$unit.'%"
        //  and as_ot like "%'.$otnonot.'%"
        //  and active_left_date between "'.$from_date.'" and "'.$to_date.'"
        //  and as_status like "%'.$Status.'%"
        //  group by concat(as_status_NAME,hr_unit_name) , substr(active_left_date,1,7)
        //  order by concat(as_status_NAME,hr_unit_name)  asc ,substr(active_left_date,1,7) asc
        //  ');

        // $hr_basic_info_date_Group = collect($hr_basic_info_date)
        // ->groupBy('as_status_NAME')
        // ->sortBy('as_status_NAME');

         $unit_query = DB::table('hr_unit');
                if($unit != '' && $unit != null){
                $unit_query->where('hr_unit_id',$unit);
                  }   
                $unit_query= $unit_query->pluck('hr_unit_id')->toArray();
                $unit =implode(',',$unit_query);
        
           // dd($otnonot);
                if($otnonot==null){
                $otnonot="0,1";
                  }   
             
        $hr_basic_info_date =  DB::select('call hr_date_wise_employee_prc
            ("'.$unit.'"  
           ,"'.$otnonot.'" 
            ,"'.$from_date.'"  
            ,"'.$to_date.'"  
            ,"'.$Type.'"  
            )');
            
           $hr_basic_info_date_Group = collect($hr_basic_info_date)
            ->groupBy('active_left_date')
            ->sortBy('active_left_date');
            // dd($hr_basic_info_date_Group);


    }


    if ($Type==3) {
        // $hr_basic_info_dept = DB::select('select hr_department_name,count(associate_id)  Employee_Count,sum(salary) salary,as_status_NAME
        //  from hr_basic_info_view
        //  where hr_unit_id like "%'.$unit.'%"
        //  and as_ot like "%'.$otnonot.'%"
        //  and active_left_date between "'.$from_date.'" and "'.$to_date.'"
        //  and as_status like "%'.$Status.'%"
        //  group by hr_department_name,as_status_NAME
        //  order by as_status_NAME asc,hr_department_name asc
        //  ');

        // $hr_basic_info_dept_Group = collect($hr_basic_info_dept)
        // ->groupBy('as_status_NAME')
        // ->sortBy('as_status_NAME');


        $unit_query = DB::table('hr_unit');
                if($unit != '' && $unit != null){
                $unit_query->where('hr_unit_id',$unit);
                  }   
                $unit_query= $unit_query->pluck('hr_unit_id')->toArray();
                $unit =implode(',',$unit_query);

           // dd($otnonot);
                if($otnonot==null){
                $otnonot="0,1";
                  }   
              
        $hr_basic_info_date =  DB::select('call hr_date_wise_employee_prc
            ("'.$unit.'"  
           ,"'.$otnonot.'" 
            ,"'.$from_date.'"  
            ,"'.$to_date.'"  
            ,"'.$Type.'"  
            )');
 
// dd($hr_basic_info_date);

           $hr_basic_info_dept_Group = $hr_basic_info_date;
           
            // dd($hr_basic_info_dept_Group);


    }

   

    // $hr_basic_info_unit_group_total = $hr_basic_info_unit_group_total[0];
    //  $hr_basic_info_unit_group_total_left= $hr_basic_info_unit_group_total_left[0];
    // dd($hr_basic_info_view_unit_group);
    return view('hr.reports.date_wiseloaddata', compact('hr_basic_info_details','hr_basic_info_status_Group','unitList','hr_basic_info_dept_Group','hr_basic_info_date_Group','Type'))->render();

}


// public function xceldownload(request $request)
//     {


//         $dd = DB::select("select * from hr_area ha ");


//         return (new FastExcel(collect($dd)))->download('file.csv');
//         // return (new FastExcel(collect($dd)))->download('file.xlsx');
//     }



}
