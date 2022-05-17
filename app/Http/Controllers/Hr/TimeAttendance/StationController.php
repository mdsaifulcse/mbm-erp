<?php

namespace App\Http\Controllers\Hr\TimeAttendance;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\Floor;
use App\Models\Hr\Line;
use App\Models\Hr\Shift;
use App\Models\Hr\Station;
use App\Models\Hr\Unit;
use DB, DataTables, Validator, Response, stdClass;
use Illuminate\Http\Request;

class StationController extends Controller

{
    public function showForm(){
        
        $data['unitList'] = Unit::where('hr_unit_status', '1')
                ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
                ->orderBy('hr_unit_name', 'desc')
                ->pluck('hr_unit_short_name', 'hr_unit_id');

        return view('hr/operation/line_change/index', $data);

    }

    //save form

    public function saveForm(Request $request){

        $validator= Validator::make($request->all(),[

            "associate_id" => "required|max:10",
            "floor_id" => "required|max:10",
            "line_id" => "required|max:10",
            "start_date" => "required",
            "end_date" => "required"
        ]);

        if($validator->fails()){
            return back()
                    ->withInput()
                    ->with("error", "Incorrect Input!");
        }
        else{

            $unit_id= Employee::where('associate_id', $request->associate_id)
                            ->pluck('as_unit_id')->first();

            $getStation = Station::checkDateRangeWiseStartDateExists($request->associate_id, $request->start_date, $request->end_date);
            if($getStation != null){
                return redirect()->back()->with('error', $request->associate_id.' is already assigned.');
            }
            $getStation = Station::checkDateRangeWiseEndDateExists($request->associate_id, $request->start_date, $request->end_date);
            if($getStation != null){
                return redirect()->back()->with('error', $as_id.' is already assigned.');
            }


            $station= new Station();

            $station->associate_id = $request->associate_id;

            $station->unit_id = $unit_id;

            $station->changed_floor = $request->floor_id;

            $station->changed_line = $request->line_id;

            $station->start_date = $request->start_date;

            $station->end_date = $request->end_date;

            $station->updated_at = date("Y-m-d H:i:s");

            $station->updated_by = Auth()->user()->associate_id;

            $station->save();
            //log file
            $this->logFileWrite("Station Card Created", $station->id);
            return back()
                ->with('success', 'Station Card saved successfully!');
        }

    }

    //get associate information of selected associate id

    public function stationAssInfo(Request $request)
    {
        $data= DB::table('hr_as_basic_info AS b')
                    ->where('b.associate_id', $request->associate_id)
                    ->select([
                        'b.*',
                        'b.as_shift_id',
                        "u.hr_unit_name",
                        "f.hr_floor_name",
                        "l.hr_line_name",
                        "dp.hr_department_name",
                        "dg.hr_designation_name"
                    ])

                    ->leftJoin('hr_floor AS f', 'f.hr_floor_id', 'b.as_floor_id')
                    ->leftJoin('hr_line AS l', 'l.hr_line_id', 'b.as_line_id')
                    ->leftJoin('hr_unit AS u', 'u.hr_unit_id', 'b.as_unit_id')
                    ->leftJoin('hr_department AS dp', 'dp.hr_department_id', '=', 'b.as_department_id')
                    ->leftJoin('hr_designation AS dg', 'dg.hr_designation_id', '=', 'b.as_designation_id')
                    ->first();

        //get floor list
        $floors = Floor::where('hr_floor_unit_id', $data->as_unit_id)
                        ->select([
                            "hr_floor_name",
                            "hr_floor_id"
                        ])
                        ->get();
        //generate floor list dropdown
        $floorList= '<option value="">Select Floor</option>';

        foreach($floors AS $floor){

            $floorList.= '<option value="'.$floor->hr_floor_id.'">'.$floor->hr_floor_name.'</option>';
        }
        $return["associate_id"]= $data->associate_id;
        $return["unit"]= $data->hr_unit_name;
        $return["floor"]= $data->hr_floor_name;
        $return["line"]= $data->hr_line_name;
        $return["shift"]= $data->as_shift_id;
        $return["floorList"]= $floorList;
        $return["as_pic"]= emp_profile_picture($data);
        $return["as_name"]= $data->as_name;
        $return["as_oracle_code"]= $data->as_oracle_code;
        $return["hr_department_name"]= $data->hr_department_name;
        $return["hr_designation_name"]= $data->hr_designation_name;

        return $return;
    }

    public function multipleAsInfo(Request $request)
    {
        $input = $request->all();
        $data = array();
        foreach ($input['associate_id'] as $key => $value) {
            $getEmployee = Employee::getEmployeeAssociateIdWise($value);
            $date = date('Y-m-d 00:00:00');
            $getStation = Station::checkDateWiseExists($date, $getEmployee->associate_id);
            if($getStation != null){
                $getEmployee->floor['hr_floor_name'] = $getStation->floor['hr_floor_name'];
                $getEmployee->line['hr_line_name'] = $getStation->line['hr_line_name'];
            }
            $data[] = $getEmployee;
        }
        //$name=$data['as_name'];
        return view('hr.timeattendance.station_multiple_as_info', compact('data'));
        return $data;
    }


    //get line list of selected floor
    public function stationLineInfo(Request $request){

        $lines= Line::where('hr_line_floor_id', $request->floor_id)

                        ->select([

                            "hr_line_id",

                            "hr_line_name"

                        ])

                        ->get();

        $data= '<option value="">Select Line</option>';

        foreach ($lines as $line) {

            $data.= '<option value="'.$line->hr_line_id.'">'.$line->hr_line_name.'</option>';

        }

        return $data;

    }


    # select unit and send the units employee data for multiple

    public function unitEmployees(Request $request){

            $data=Employee::where('as_unit_id',$request->unit_id)
                                ->select('as_name', 'associate_id', 'as_pic', 'as_oracle_code')
                                ->where('as_ot', 1)
                                ->get()
                                ->toArray();
            // dd($data);exit;
        return Response::json($data);
    }

    #select multiple section unit and get floor

    public function getFloor(Request $request){
        //get floor list
        $floors = Floor::where('hr_floor_unit_id', $request->unit_id)
                        ->select([
                            "hr_floor_name",
                            "hr_floor_id"
                        ])
                        ->get();
        //generate floor list dropdown

        $floorList= '<option value="">Select Floor</option>';

        foreach($floors AS $floor){

            $floorList.= '<option value="'.$floor->hr_floor_id.'">'.$floor->hr_floor_name.'</option>';
        }

        //dd($floorList);exit;

        return Response::json($floorList);
    }

    # multiple save

    public function saveFormMultiple(Request $request){

        //dd($request->all());
        $validator= Validator::make($request->all(),[

            "multiple_associate_id" => "required",
            "floor_id_multiple" => "required",
            "line_id_multiple" => "required|max:10",
            "start_date_multiple" => "required",
            "unit" => "required"
        ]);

        if($validator->fails()){
            return back()
                    ->withInput()
                    ->with("error", "Incorrect Input!");
        }
        $input = $request->all();
        // return $input;
        try {
            $as_ids=$request->multiple_associate_id;
            $startDate = date('Y-m-d H:i', strtotime($request->start_date_multiple));
            if($request->end_date_multiple){
                $endDate = date('Y-m-d H:i', strtotime($request->end_date_multiple));
            }else{
                $endDate = null;
            }
            foreach($as_ids as $as_id){
                if($endDate != null){
                    $getStation = Station::checkDateRangeWiseStartDateExists($as_id, $startDate, $endDate);
                    if($getStation != null){
                        return redirect()->back()->with('error', $as_id.' is already assigned.');
                    }
                    $getStation = Station::checkDateRangeWiseEndDateExists($as_id, $startDate, $endDate);
                    if($getStation != null){
                        return redirect()->back()->with('error', $as_id.' is already assigned.');
                    }
                }else{
                    $getStation = Station::checkDateWiseExists($startDate, $as_id);
                    if($getStation != null){
                        return redirect()->back()->with('error', $as_id.' is already assigned.');
                    }
                }
                
                $station= new Station();
                $station->associate_id = $as_id;
                $station->unit_id = $request->unit;
                $station->changed_floor = $request->floor_id_multiple;
                $station->changed_line = $request->line_id_multiple;
                $station->start_date = $startDate;
                $station->end_date = $endDate;
                $station->created_by = Auth()->user()->id;
                $station->save();

                $this->logFileWrite("$as_id - Employee Line Change Successfully", $station->id);
            }
            //log file
            toastr()->success('Employee Line Change Successfully');
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }



    public function showList(){
        $unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())
                        ->pluck('hr_unit_name');
        $floorList= Floor::pluck('hr_floor_name');
        $lineList= Line::pluck('hr_line_name');
        return view('hr/timeattendance/station_card_list', compact('unitList', 'floorList', 'lineList'));
    }
    //get list data
    public function listData(){
        $data= DB::table('hr_station AS s')
                ->select([
                    "s.*",
                    "b.as_name",
                    "ff.hr_floor_name",
                    "ll.hr_line_name",
                    "f.hr_floor_name AS changed_floor",
                    "l.hr_line_name AS changed_line",
                    "u.hr_unit_name"
                ])
                ->leftJoin('hr_as_basic_info As b', 'b.associate_id', 's.associate_id')
                ->leftJoin('hr_floor AS f', 'f.hr_floor_id', 's.changed_floor')
                ->leftJoin('hr_line AS l', 'l.hr_line_id', 's.changed_line')
                ->leftJoin('hr_floor AS ff', 'ff.hr_floor_id', 'b.as_floor_id')
                ->leftJoin('hr_line AS ll', 'll.hr_line_id', 'b.as_line_id')
                ->leftJoin('hr_unit AS u', 'u.hr_unit_id', 'b.as_unit_id')
                ->get();

        return DataTables::of($data)->addIndexColumn()
                            ->addColumn('action', function($data){
                                $action_buttons= "<div class=\"btn-group\">  
                                    <a href=".url('hr/timeattendance/station_card/'.$data->station_id.'/edit')." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Edit\" style=\"height:25px; width:26px;\">
                                        <i class=\"ace-icon fa fa-pencil bigger-120\"></i>

                                    </a> 

                                    <a href=".url('hr/timeattendance/station_card/'.$data->station_id.'/delete')." class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Delete\" style=\"height:25px; width:26px;\">

                                        <i class=\"ace-icon fa fa-trash bigger-120\"></i>

                                    </a> ";

                                $action_buttons.= "</div>";
                                return $action_buttons;
                            })
                            ->rawColumns(["action"])
                            ->toJson();
    }

    //station card delete

    public function stationDelete($id){
        Station::where('station_id', $id)->delete();
        //log file
        $this->logFileWrite("Station Card Deleted", $id);
        return redirect("hr/timeattendance/station_card")->with('success', 'Station Card deleted successfully!');
    }

    //station cadr edit

    public function stationEdit($id){
        $station= DB::table('hr_station AS st')
                    ->where('station_id', $id)
                    ->select([
                        "st.*",
                        'b.as_unit_id',
                        'b.as_shift_id',
                        "u.hr_unit_name",
                        "f.hr_floor_name",
                        "l.hr_line_name"
                    ])
                    ->leftJoin('hr_as_basic_info AS b', 'b.associate_id', 'st.associate_id')
                    ->leftJoin('hr_floor AS f', 'f.hr_floor_id', 'b.as_floor_id')
                    ->leftJoin('hr_line AS l', 'l.hr_line_id', 'b.as_line_id')
                    ->leftJoin('hr_unit AS u', 'u.hr_unit_id', 'b.as_unit_id')
                    ->first();

        $floorList= Floor::where('hr_floor_unit_id', $station->unit_id)
                            ->pluck('hr_floor_name', 'hr_floor_id');
        $lineList= Line::where('hr_line_floor_id', $station->changed_floor)
                        ->pluck('hr_line_name', 'hr_line_id');
        return view('hr/timeattendance/station_card_edit', compact('station', 'floorList', 'lineList'));
    }

    //update station card
    public function stationUpdate(Request $request){
        $validator= Validator::make($request->all(),[
            "associate_id" => "required|max:10",
            "floor_id" => "required|max:10",
            "line_id" => "required|max:10",
            "start_date" => "required",
            "end_date" => "required"
        ]);
        if($validator->fails()){
            return back()
                    ->withInput()
                    ->with("error", "Incorrect Input!");
        }
        else{
            Station::where('station_id', $request->station_id)
            ->update([
                "associate_id" => $request->associate_id,
                "unit_id" => $request->unit_id,
                "changed_floor" => $request->floor_id,
                "changed_line" => $request->line_id,
                "start_date" => $request->start_date,
                "end_date" => $request->end_date,
                "updated_by" => Auth()->user()->associate_id
            ]);
            //log file
            $this->logFileWrite("Station Card updated", $request->station_id);
            return redirect("hr/timeattendance/station_card")
                ->with('success', 'Station Card updated successfully!');

        }

    }

    public function listOf(Request $request)
    {
        $input = $request->all();
        if(!isset($input['month_year'])){
            $input['month_year'] = date('Y-m');
        }
        // return $input;
        return view('hr.reports.line_changes', compact('input'));
    }

    public function listOfData(Request $request)
    {
        $input = $request->all();
        // employee basic sql binding
        $employeeData = DB::table('hr_as_basic_info');
        $employeeDataSql = $employeeData->toSql();
        $data = Station::
        where('start_date', 'LIKE', $input['month_year'].'%')
        ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
        ->leftjoin(DB::raw('(' . $employeeDataSql. ') AS b'), function($join) use ($employeeData) {
            $join->on('b.associate_id','hr_station.associate_id')->addBinding($employeeData->getBindings());
        })
        ->orderByRaw('ISNULL(end_date), end_date ASC')
        ->get();
     // return $data;
        $getLine = line_by_id();
        $getFloor = floor_by_id();
        $getUnit = unit_by_id();
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('pic', function($data){
                return '<img src="'.emp_profile_picture($data).'" class="small-image min-img-file">';
            })
            ->addColumn('associate_id', function($data) use ($input){
                $month = $input['month_year'];
                $jobCard = url("hr/operation/job_card?associate=$data->associate_id&month_year=$month");
                return '<a href="'.$jobCard.'" target="_blank">'.$data->associate_id.'</a>';
            })
            ->addColumn('hr_unit_name', function($data) use ($getUnit){
                return $getUnit[$data->as_unit_id]['hr_unit_short_name']??'';
            })
            ->addColumn('as_name', function($data){
                return $data->as_name. ' - '.$data->as_contact;
            })
            ->addColumn('current_line', function($data) use ($getFloor, $getLine){
                $cFloor = $getFloor[$data->as_floor_id]['hr_floor_name']??'';
                $cLine = $getLine[$data->as_line_id]['hr_line_name']??'';
                return $cFloor.' - '.$cLine;
            })
            ->addColumn('changed_floor', function($data) use ($getFloor){
                return $getFloor[$data->changed_floor]['hr_floor_name']??'';
            })
            ->addColumn('changed_line', function($data) use ($getLine){
                return $getLine[$data->changed_line]['hr_line_name']??'';
            })
            ->addColumn('start_date', function($data){
                return date('Y-m-d', strtotime($data->start_date));
            })
            ->addColumn('end_date', function($data){
                return $data->end_date != ''?date('Y-m-d', strtotime($data->end_date)):'';
            })
            ->addColumn('action', function($data) use ($input, $getFloor, $getLine){
                $floor = $getFloor[$data->changed_floor]['hr_floor_name']??'';
                $line = $getLine[$data->changed_line]['hr_line_name']??'';
                if($data->end_date != '' || $data->end_date != null){
                    $dir = '';
                }else{
                    // $dir = '<a class="btn btn-sm btn-success text-white" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back to the line"><i class="las la-undo"></i></a> &nbsp; &nbsp;';
                    $dir = '';
                }
                $action = $dir.' <a class="btn btn-sm btn-success text-white changed-action" data-toggle="tooltip" data-id="'.$data->station_id.'" data-asid="'.$data->associate_id.'" data-ename="'.$data->as_name.'" data-line="'.$line.'" data-floor="'.$floor.'"  data-placement="top" title="" data-original-title="Back to the line modification time"><i class="las la-history"></i></a>';
                return $action;
            })
            ->rawColumns([
                'pic', 'associate_id', 'hr_unit_name', 'as_name', 'current_line', 'changed_floor', 'changed_line', 'start_date', 'end_date', 'action'
            ])
            ->make(true);
    }

    public function updateLine(Request $request)
    {
        $input = $request->all();
        DB::beginTransaction();
        try {
            if($input['station_id'] != null && $input['end_date'] != null){
                $end_date = date('Y-m-d H:i', strtotime($input['end_date']));
                $station = Station::where('station_id', $input['station_id'])->first();
                if($station != null && $station->start_date <= $input['end_date']){
                    $checkNull = Station::where('station_id', $station->station_id)->whereNull('end_date')->first();

                    if($checkNull != null){
                        $employee = Employee::select('as_id','as_line_id', 'as_unit_id')->where('associate_id', $checkNull->associate_id)->first();
                        $table = get_att_table($employee->as_unit_id);
                        $att = DB::table($table)
                        ->where('as_id', $employee->as_id)
                        ->where('in_date','>', $input['end_date'])
                        ->update(['line_id' => $employee->as_line_id]);
                    }
                    $station = Station::where('station_id', $input['station_id'])
                    ->update([
                        'end_date' => $end_date
                    ]);
                }else{
                    $msg = 'Something wrong';
                }
                
                $msg = 'Successfully Done';
            }else{
                $msg = 'Something wrong, please try again';
            }
            toastr()->success($msg);
            DB::commit();
            return back();
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }

    //Write Every Events in Log File

    public function logFileWrite($message, $event_id){
        $log_message = date("Y-m-d H:i:s")." ".Auth()->user()->associate_id." \"".$message."\" ".$event_id.PHP_EOL;
        $log_message .= file_get_contents("assets/log.txt");
        file_put_contents("assets/log.txt", $log_message);
    }

    public function lineGetEmployee(Request $request)
    {
        $input = $request->all();
        $data = array();
        if(!empty($input['keyvalue'])){
            
            $queryData = DB::table('hr_as_basic_info AS b')
            ->select('b.associate_id','b.as_id','b.as_name','b.as_designation_id', 'b.as_unit_id','b.as_line_id', 'b.as_floor_id','f.hr_floor_name', 'l.hr_line_name')
            ->where('b.as_status', 1)
            ->when(!empty($input['type']), function ($query) use($input){
                if($input['type'] == 'associateid'){
                    return $query->where('b.associate_id','LIKE','%'.$input['keyvalue'].'%')->orWhere('b.as_oracle_code','LIKE','%'.$input['keyvalue'].'%');
                }else{
                    return $query->where('b.as_name','LIKE','%'.$input['keyvalue'].'%');
                }
            })
            ->whereIn('b.as_location', auth()->user()->location_permissions())
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->join('hr_floor AS f', 'b.as_floor_id', 'f.hr_floor_id')
            ->join('hr_line AS l', 'b.as_line_id', 'l.hr_line_id');
            
            $getEmployee = $queryData->limit(10)->get();
            $employees = array_column($getEmployee->toArray(), 'associate_id');
            $today = date('Y-m-d');
            $getLine = DB::table('hr_station AS s')
                ->select('s.*', 'f.hr_floor_name', 'l.hr_line_name')
                ->whereIn('s.associate_id', $employees)
                 ->whereDate('start_date','<=',$today)
                ->where(function ($q) use($today) {
                  $q->whereDate('end_date', '>=', $today);
                  $q->orWhereNull('end_date');
                })
                ->join('hr_floor AS f', 's.changed_floor', 'f.hr_floor_id')
                ->join('hr_line AS l', 's.changed_line', 'l.hr_line_id')
                ->orderBy('s.station_id', 'desc')
                ->get()
                ->keyBy('associate_id');
                // dd($getLine);
            $getDesignation = designation_by_id();
            foreach ($getEmployee as $emp) {
                $empLine = new stdClass();
                
                $empLine->as_id = $emp->as_id;
                $empLine->associate = $emp->associate_id;
                $empLine->name = $emp->as_name;
                $empLine->default_line = $emp->as_line_id;
                $empLine->designation = $getDesignation[$emp->as_designation_id]['hr_designation_name']??'';
                if(isset($getLine[$emp->associate_id]) && $getLine[$emp->associate_id] != null){
                    $line = $getLine[$emp->associate_id];
                    $empLine->exist_id = $line->station_id;
                    $empLine->unit_id = $line->unit_id;
                    $empLine->line_id = $line->changed_line;
                    $empLine->line_name = $line->hr_line_name;
                    $empLine->floor_id = $line->changed_floor;
                    $empLine->floor_name = $line->hr_floor_name;
                    $empLine->start_date = $line->start_date != null?(date('Y-m-d', strtotime($line->start_date))):'';
                    $empLine->end_date = $line->end_date != null?(date('Y-m-d', strtotime($line->end_date))):'';
                }else{
                    $empLine->exist_id = '';
                    $empLine->unit_id = $emp->as_unit_id;
                    $empLine->line_id = $emp->as_line_id;
                    $empLine->line_name = $emp->hr_line_name;
                    $empLine->floor_id = $emp->as_floor_id;
                    $empLine->floor_name = $emp->hr_floor_name;
                    $empLine->start_date = date('Y-m-d');
                    $empLine->end_date = '';
                }
                $data[] = $empLine;
            }
        }
        
        return $data;
    }

    public function dateLineFloor(Request $request)
    {
        $input = $request->all();
        $today = $input['date'];
        $data['type'] = 'error';

        try {
            $line = DB::table('hr_station AS s')
            ->select('s.*', 'f.hr_floor_name', 'l.hr_line_name')
            ->where('s.associate_id', $input['associate'])
            ->whereDate('start_date','<=',$today)
            ->where(function ($q) use($today) {
              $q->whereDate('end_date', '>=', $today);
              $q->orWhereNull('end_date');
            })
            ->join('hr_floor AS f', 's.changed_floor', 'f.hr_floor_id')
            ->join('hr_line AS l', 's.changed_line', 'l.hr_line_id')
            ->orderBy('s.station_id', 'desc')
            ->first();
            $employee = DB::table('hr_as_basic_info AS b')
                ->select('b.associate_id','b.as_name','b.as_designation_id', 'b.as_unit_id','b.as_line_id', 'b.as_floor_id','f.hr_floor_name', 'l.hr_line_name')
                ->where('b.associate_id', $input['associate'])
                ->join('hr_floor AS f', 'b.as_floor_id', 'f.hr_floor_id')
                ->join('hr_line AS l', 'b.as_line_id', 'l.hr_line_id')
                ->first();
                
            $empLine = [];
            if($line != null){
                $empLine['exist_id'] = $line->station_id;
                $empLine['unit_id'] = $line->unit_id;
                $empLine['line_id'] = $line->changed_line;
                $empLine['line_name'] = $line->hr_line_name;
                $empLine['floor_id'] = $line->changed_floor;
                $empLine['floor_name'] = $line->hr_floor_name;
                $empLine['start_date'] = $line->start_date != null?(date('Y-m-d', strtotime($line->start_date))):'';
                $empLine['end_date'] = $line->end_date != null?(date('Y-m-d', strtotime($line->end_date))):'';
            }else{
                $empLine['exist_id'] = '';
                $empLine['unit_id'] = $employee->as_unit_id;
                $empLine['line_id'] = $employee->as_line_id;
                $empLine['line_name'] = $employee->hr_line_name;
                $empLine['floor_id'] = $employee->as_floor_id;
                $empLine['floor_name'] = $employee->hr_floor_name;
                $empLine['start_date'] = $today;
                $empLine['end_date'] = '';
            }
            $data['value'] = $empLine;
            $data['type'] = 'success';
            return $data; 
        } catch (\Exception $e) {
            $data['msg'] = $e->getMessage();
            return $data;   
        }
    }

    public function getLineFloor(Request $request)
    {
        $input = $request->all();
        $data = [];
        if(!empty($input['keyvalue'])){
            $data = DB::table('hr_line')
            ->select('hr_line.hr_line_id', 'hr_line.hr_line_name', 'hr_floor.hr_floor_id', 'hr_floor.hr_floor_name')
            ->where('hr_line.hr_line_unit_id', $input['unit'])
            ->where('hr_line.hr_line_name','LIKE','%'.$input['keyvalue'].'%')
            ->join('hr_floor', 'hr_line.hr_line_floor_id', 'hr_floor.hr_floor_id')
            ->limit(10)
            ->get();
        }
        
        return $data;
    }

    public function ajaxLineChange(Request $request)
    {
        $data['type'] = 'error';
        $input = $request->all();
        // return $input;
        DB::beginTransaction();
        try {
            $totalCount = count($input['name']);
            for ($i=0; $i < $totalCount ; $i++) { 
                $as_id = $input['asid'][$i];
                $defaultLine = $input['default_line'][$i];
                $value['associate_id'] = $input['associate'][$i];
                $value['start_date'] = $input['start_date'][$i];
                $value['end_date'] = $input['end_date'][$i];
                $value['changed_line'] = $input['lineid'][$i];
                $value['changed_floor'] = $input['floorid'][$i];
                $value['unit_id']= $input['unit'][$i];
                $stationId = $input['exist_id'][$i];
                $table = get_att_table($value['unit_id']);
                $flag = 0;
                $newIn = 0;
                if($input['name'][$i] != null && $value['changed_line'] != null){
                    if($value['end_date'] != null){
                        if($value['start_date'] > $value['end_date']){
                            $data['message'][] = $value['associate_id'].' start date greater than end date.';
                            continue;
                        }
                    }

                    if($stationId != null){
                        $station = Station::where('station_id', $stationId)->first();
                        // return $station;
                        if($station != null){
                            $checkStation = Station::where('associate_id', $value['associate_id'])->where('changed_line', $value['changed_line'])->where('start_date', $value['start_date'])->where('end_date', $value['end_date'])->first();
                            
                            if($checkStation != null){
                                $data['message'][] = $value['associate_id'].' is already assigned.';
                                continue;
                            }

                            if($station->end_date == '' && strtotime(date('Y-m-d', strtotime($station->start_date))) < strtotime(date('Y-m-d', strtotime($value['start_date'])))){
                                
                                $previousDate = date('Y-m-d', strtotime('-1 day', strtotime($value['start_date'])));
                                Station::where('station_id', $station->station_id)->update([
                                    'end_date' => $previousDate
                                ]);
                                $newIn = 1;
                            }else{
                                
                                $checkNull = Station::where('station_id', $station->station_id)->whereNull('end_date')->first();
                                
                                if($checkNull != null){
                                    if($value['end_date'] == ''){
                                        $att = DB::table($table)
                                        ->where('as_id', $as_id)
                                        ->where('in_date','>=', date('Y-m-d', strtotime($value['start_date'])))
                                        ->update(['line_id' => $defaultLine]);
                                    }else{
                                        $att = DB::table($table)
                                        ->where('as_id', $as_id)
                                        ->where('in_date','>=', date('Y-m-d', strtotime($value['start_date'])))
                                        ->where('in_date','<=', date('Y-m-d', strtotime($value['end_date'])))
                                        ->update(['line_id' => $defaultLine]);
                                    }
                                    
                                }

                                $value['updated_by'] = auth()->user()->id;
                                
                                $getStation = Station::where('station_id', $station->station_id)->update($value);
                                $flag = 1;
                                
                            }
                            
                        }
                    }else{
                        $newIn = 1;

                    }

                    if($newIn == 1){
                        if($value['end_date'] != null){
                            $getStation = Station::checkDateRangeWiseStartDateExistsLine($value['associate_id'], $value['start_date'], $value['end_date'], $value['changed_line']);
                            
                            if($getStation != null){
                                $data['message'][] = $value['associate_id'].' is already assigned.';
                                continue;
                            }
                            $getStation = Station::checkDateRangeWiseEndDateExistsLine($value['associate_id'], $value['start_date'], $value['end_date'], $value['changed_line']);
                            
                            if($getStation != null){
                                $data['message'][] = $value['associate_id'].' is already assigned.';
                                continue;
                            }
                        }else{
                            $getStation = Station::checkDateWiseLineExists($value['start_date'], $value['associate_id'], $value['changed_line']);
                            if($getStation != null){
                                $data['message'][] = $value['associate_id'].' is already assigned.';
                                continue;
                            }
                        }

                        if($getStation == null){
                            $value['created_by'] = auth()->user()->id;
                            $stationId = Station::insertGetId($value);
                            $flag = 1;
                           
                        }
                    }
                    // attendance update
                    if($flag == 1){
                        
                        $att = DB::table($table)
                        ->where('as_id', $as_id)
                        ->where('in_date','>=', $value['start_date']);
                        if($value['end_date'] != null){
                            $att->where('in_date', '<=', $value['end_date']);
                        }
                        $att->update(['line_id' => $value['changed_line']]);
                    }
                }
            }

            DB::commit();
            $data['msg'] = 'Successfully Done';
            $data['type'] = 'success';
            return $data;

        } catch (\Exception $e) {
            DB::rollback();
            $data['msg'] = $e->getMessage();
            return $data;
        }
    }
}

