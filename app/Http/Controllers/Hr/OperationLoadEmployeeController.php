<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class OperationLoadEmployeeController extends Controller
{
    public function getShiftEmployee(Request $request)
    {
    	$input = $request->all();
    	try {
            
	        if($request->searchDate != null) {
	            $year = date('Y', strtotime($request->searchDate));
	            $month = date('n', strtotime($request->searchDate));
	            $day = date('j', strtotime($request->searchDate));
	        }else{
	        	$year  = date('Y');
		        $month = date('n');
		        $day   = date('j');
	        }
    		$queryData = DB::table('hr_as_basic_info AS emp')
    		->where('emp.as_status', 1)
    		->where('emp.as_unit_id', $input['unit'])
    		->whereNotNull('emp.as_shift_id')
            ->whereIn('emp.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('emp.as_location', auth()->user()->location_permissions())
    		->when(!empty($input['area']), function ($query) use($input){
               return $query->where('emp.as_area_id',$input['area']);
            })
            /*->when(!empty($input['shift']), function ($query) use($input){
               return $query->where('emp.as_shift_id',$input['shift']);
            })*/
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('emp.as_department_id',$input['department']);
            })
            ->when(!empty($input['emp_type']), function ($query) use($input){
               return $query->where('emp.as_emp_type_id',$input['emp_type']);
            })
            ->when($request['otnonot']!=null, function ($query) use($input){
               return $query->where('emp.as_ot',$input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('emp.as_section_id', $input['section']);
            })
            ->when(!empty($input['line']), function ($query) use($input){
               return $query->where('emp.as_line_id', $input['line']);
            })
            ->when(!empty($input['floor']), function ($query) use($input){
               return $query->where('emp.as_floor_id', $input['floor']);
            })
            ->when(!empty($input['subsection']), function ($query) use($input){
               return $query->where('emp.as_subsection_id', $input['subsection']);
            });
            
            $getEmployee = $queryData->select('emp.as_id', 'emp.associate_id', 'emp.as_name', 'emp.as_oracle_code', 'emp.as_shift_id', 'emp.as_gender', 'emp.as_pic')->get();

            // today shift roster
            $employees = $queryData->select('emp.as_id')->pluck('emp.as_id')->toArray();
            // return $employees;
            $todayShift = DB::table('hr_shift_roaster')
        	->select('shift_roaster_user_id','day_'.$day)
            ->where('shift_roaster_year', $year)
            ->where('shift_roaster_month', $month)
            ->whereIn('shift_roaster_user_id', $employees)
            ->get()->keyBy('shift_roaster_user_id')->toArray();

            // combined array

            $data['filter'] = "<input type=\"text\" id=\"AssociateSearch\" placeholder=\"Search an Associate\" autocomplete=\"off\" class=\"form-control\"/>";
	        $data['result'] = "";

	        $data['shiftRosterCount'] = [];
	        $data['shiftDefaultCount'] = [];
	        $data['total'] = 0;
	        $today = 'day_'.$day;

	        foreach($getEmployee as $employee)
        	{
        		$checkShift = $todayShift[$employee->as_id]??'';
        		if(($checkShift != '') && ($todayShift[$employee->as_id]->$today != '')){

        			$shiftCode = $todayShift[$employee->as_id]->$today;
                    $shiftstatus = $shiftCode.' - <span class="text-success">Change</span>';
        		}else{
        			$shiftCode = $employee->as_shift_id;
                    $shiftstatus = $shiftCode.' - Default';
        		}


                if($input['shift'] == '' || $input['shift'] == $shiftCode){

                    $image = emp_profile_picture($employee);
            		$data['total'] += 1;
                    $data['result'].= "
                            <tr class='add'>
                                <td>
                                    <input type='checkbox' value='$employee->associate_id' name='assigned[$employee->as_id]'/>
                                    </td>
                                <td>
                                    <span class=\"lbl\"> 
                                        <img src='".$image."' class='small-image' style='height:40px;width:auto'> 
                                    </span>
                                </td>
                                <td><b> $employee->associate_id </b><br>$employee->as_oracle_code</td><td>$employee->as_name </td><td>$shiftstatus </td></tr>";
                }
        	}

            return $data;
    	} catch (\Exception $e) {
    		$bug = $e->getMessage();
    		return 'error';	
    	}
    }

    public function getHolidayRosterEmployee(Request $request)
    {
        $input = $request->all();
        // return $input;
        try {
            if($request->dates != ''){
                $year  = date('Y', strtotime($request->dates));
                $month = date('n', strtotime($request->dates));
                $day   = date('j', strtotime($request->dates));
            }else{
                $year  = date('Y');
                $month = date('n');
                $day   = date('j');
            }
            

            // holiday roster sql binding
            $holidayData = DB::table('holiday_roaster');
            $holidayData_sql = $holidayData->toSql();

            $queryData = DB::table('hr_as_basic_info AS emp')
            ->where('emp.as_status', 1)
            ->whereNotNull('emp.as_shift_id')
            ->whereIn('emp.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('emp.as_location', auth()->user()->location_permissions())
            ->when(!empty($input['unit']), function ($query) use($input){
               return $query->where('emp.as_unit_id',$input['unit']);
            })
            ->when(!empty($input['location']), function ($query) use($input){
               return $query->where('emp.as_location',$input['location']);
            })
            ->when(!empty($input['area']), function ($query) use($input){
               return $query->where('emp.as_area_id',$input['area']);
            })
            ->when(!empty($input['shift_roster_status']), function ($query) use($input){
               return $query->where('emp.shift_roaster_status',$input['shift_roster_status']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('emp.as_department_id',$input['department']);
            })
            ->when(!empty($input['emp_type']), function ($query) use($input){
               return $query->where('emp.as_emp_type_id',$input['emp_type']);
            })
            ->when($request['otnonot']!=null, function ($query) use($input){
               return $query->where('emp.as_ot',$input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('emp.as_section_id', $input['section']);
            })
            ->when(!empty($input['subsection']), function ($query) use($input){
               return $query->where('emp.as_subsection_id', $input['subsection']);
            });
            if($input['dates'] != null && $input['type'] != null){
                $queryData->where('ho.date', $input['dates'])->where('ho.remarks', $input['type']);
                $queryData->leftjoin(DB::raw('(' . $holidayData_sql. ') AS ho'), function($join) use ($holidayData) {
                    $join->on('ho.as_id','emp.associate_id')->addBinding($holidayData->getBindings());
                });
            }
            if($input['doj_to'] != null){
                if($input['doj_from'] != null){
                    $queryData->whereBetween('emp.as_doj', [$input['doj_to'], $input['doj_from']]);
                }else{
                    $queryData->where('emp.as_doj', $input['doj_to']);
                }
            }
            $getEmployee = $queryData->select('emp.as_id', 'emp.associate_id', 'emp.as_oracle_code', 'emp.as_name', 'emp.as_shift_id', 'emp.as_gender', 'emp.as_pic')->get();

            // today shift roster
            $employees = $queryData->select('emp.as_id')->pluck('emp.as_id')->toArray();

            $todayShift = DB::table('hr_shift_roaster')
            ->select('shift_roaster_user_id','day_'.$day)
            ->where('shift_roaster_year', $year)
            ->where('shift_roaster_month', $month)
            ->whereIn('shift_roaster_user_id', $employees)
            // ->pluck('day_'.$day)
            ->get()->keyBy('shift_roaster_user_id')->toArray();
            
            $data['filter'] = "<input type=\"text\" id=\"AssociateSearch\" placeholder=\"Search an Associate\" autocomplete=\"off\" class=\"form-control\"/>";
            $data['result'] = "";

            $data['shiftRosterCount'] = [];
            $data['shiftDefaultCount'] = [];
            // $data['shiftRosterCount2'] = [];
            $data['total'] = 0;
            $today = 'day_'.$day;
            foreach($getEmployee as $employee)
            {
                $checkShift = $todayShift[$employee->as_id]??'';
                if(($checkShift != '') && ($todayShift[$employee->as_id]->$today != '')){
                    $shiftCode = $todayShift[$employee->as_id]->$today.' - Change';
                }else{
                    $shiftCode = $employee->as_shift_id.' - Default';
                }
                $image = emp_profile_picture($employee);
                $data['total'] += 1;
                $data['result'].= "<tr class='add'><td><input type='checkbox' value='$employee->associate_id' name='assigned[$employee->as_id]'/></td><td><span class=\"lbl\"> <img src='".$image."' class='small-image' style='height:40px;width:auto'> </span></td><td><span class=\"lbl\"> $employee->associate_id <br>$employee->as_oracle_code</span></td><td>$employee->as_name </td><td><span class=\"lbl\"> $shiftCode</span></td></tr>";
            }

            return $data;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug; 
        }
    }
}
