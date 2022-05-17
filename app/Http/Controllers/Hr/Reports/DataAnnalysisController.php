<?php

namespace App\Http\Controllers\Hr\Reports;
use App\Exports\Hr\SalarySheetExport;
use App\Http\Controllers\Controller;
use App\Models\Hr\Benefits;
use App\Repository\Hr\EmployeeRepository;
use App\Repository\Hr\SalaryRepository;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use DB, DataTables;
use Illuminate\Http\Request;

class DataAnnalysisController extends Controller

{

	   public function __construct()
    {
        ini_set('zlib.output_compression', 1);
    }



				public function index(Request $request)
				{
			      // dd('dsfdsf');
					$unitList=collect(unit_by_id())->pluck('hr_unit_name','hr_unit_id');



					$section = collect(DB::select('select hs.hr_section_name,hs.hr_section_id 
						from hr_section hs 
						where hs.hr_section_department_id =65
						order by hs.hr_section_name  asc'))->pluck('hr_section_name','hr_section_id');

					return view('hr.reports.data_annalysis', compact('unitList','section'));

			        // 

			 ############array push start#################
			//                 $days=DB::select('select habi.associate_id ,habi.as_name 
			//     from hr_as_basic_info habi 
			//     where habi.as_ot =1
			//              ');

			// $from = '2021-05-01'; $to = '2021-05-05';
			//         DB::statement("SET @from='".$from."', @to='".$to."'");
			//         $data =  DB::table('hr_leave')
			//             ->select(
			//                 'leave_ass_id',
			//                 DB::raw('
			//                     SUM((CASE
			//                         WHEN (leave_from <= @from &&  leave_to <= @to)
			//                             THEN  DATEDIFF(leave_to, @from)+1
			//                         WHEN (leave_from <= @from && leave_to >= @to)
			//                             THEN DATEDIFF(@to, @from)+1
			//                         WHEN (leave_from >= @from && leave_to >= @to)
			//                             THEN DATEDIFF(@to, leave_from)+1
			//                         ELSE
			//                             DATEDIFF(leave_to, leave_from)+1
			//                     END)) AS days'
			//                 )
			//             )
			//             ->whereIn('leave_ass_id', collect($days)->pluck('associate_id')->toArray())
			//             ->groupBy('leave_ass_id')
			//             ->get()
			//             ->keyBy('leave_ass_id');

			//         $days  = collect($days)->map(function($q) use ($data){
			//             $q->total = isset($data[$q->associate_id])?$data[$q->associate_id]->days:0;
			//             return $q;
			//         });

			//         dd($days[0]);

			        ############array push start#################

			        ############array group by practics start#################
			        // $unit=[1,2,3,4,5,6];
			        // $hr_basic_info_details = DB::select('select hr_unit_name,hr_department_name,associate_id
			        //  from hr_basic_info_view 
			        //  where hr_unit_id in ('.implode(",",$unit).')
			        //  order by hr_department_name asc,hr_unit_name asc
			        //  ');

			        //  $hr_basic_info_details =collect($hr_basic_info_details)
			        //  ->groupBy('hr_unit_name')
			        //  ->map(function($q){
			        //     return collect($q)->groupBy('hr_department_name');
			        //  });
			        ###############array group by practics end#################
			        // dd($hr_basic_info_details);

				}



    public function showfilterdata(Request $request)
    {
				    	$unit=$request->unit;
				    	$section=$request->section;
				    	$subsection=$request->subsection;
				    	$from_date=$request->from_date;
				    	$to_date=$request->to_date;
				    	$Status=$request->Status;
				    	$Type=$request->Type;

				    	$unitList=collect(unit_by_id())->pluck('hr_unit_name','hr_unit_id');


				    	$hr_basic_info_as_id = DB::table('hr_as_basic_info')
				    	->where('as_unit_id',$unit)
				    	->when($section != null, function($q) use ($section){
				    		$q->where('as_section_id', $section);
				    	})
				    		->when($subsection != null, function($q) use ($subsection){
				    		$q->where('as_subsection_id', $subsection);
				    	})
				    	->where('as_ot',1)   
				    	->pluck('as_id')
				    	->toArray();       
				    	// dd($hr_basic_info_as_id);

				    	$hr_basic_info_as_id_ALL = DB::select('select habi.as_id 
				    		from hr_as_basic_info habi where habi.as_unit_id ="'.$unit.'" ');


				                    // $hr_basic_info_as_id=implode(',',collect($hr_basic_info_as_id)->pluck('as_id')->toArray());
				    	$hr_basic_info_as_id=implode(',',$hr_basic_info_as_id);
				    	$hr_basic_info_as_id_ALL=implode(',',collect($hr_basic_info_as_id_ALL)->pluck('as_id')->toArray());
				    // dd($hr_basic_info_as_id_ALL);

				    	$hr_basic_info_details = 
				    	DB::select('call hr_data_annalysis_prc(4 ,"'.$hr_basic_info_as_id.'"
				    		,"'.$hr_basic_info_as_id_ALL.'"
				    		,"'.$from_date.'"
				    		,"'.$to_date.'"
				    		,"'.$unit.'"
				    	)');


				    	return view('hr.reports.data_analysisloaddata', compact('hr_basic_info_details'))->render();

}


			public function sub_section_call1 (Request $request)
			{
				$section=$request->section;

				// dd($section);
				$sub_section = collect(DB::select('select hs.hr_subsec_name as text ,hr_subsec_id as id 
					from hr_subsection hs 
					where hr_subsec_section_id="'.$section.'"
					order by hs.hr_subsec_name'));


				return $sub_section;
			}

}
