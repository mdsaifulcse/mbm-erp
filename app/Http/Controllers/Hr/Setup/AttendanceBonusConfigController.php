<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Hr\AttendanceBonusConfig;
Use Validator,DB, ACL, Exception, Response;

class AttendanceBonusConfigController extends Controller
{
    public function index(){
        
        $unitList  = collect(unit_authorization_by_id())->pluck('hr_unit_name', 'hr_unit_id');
        $attBonusData = DB::table('hr_attendance_bonus_dynamic as b')
        ->select([
            'b.*',
            'c.hr_unit_name'
        ])
        ->leftJoin('hr_unit as c', 'c.hr_unit_id', 'b.unit_id')
        ->whereIn('unit_id', array_keys($unitList->toArray()))
        ->orderBy('b.id', 'DESC')
        ->get();
        return view('hr.setup.attendance_bonus', compact('unitList','attBonusData'));
    }

    public function saveData(Request $request){
        $validator= Validator::make($request->all(),[
            'unit_id'      => 'required|max:11',
            'late_count'   => 'required|max:11',
            'leave_count'  => 'required|max:11',
            'absent_count' => 'required|max:11',
            'first_month'  => 'required',
            'second_month' => 'required'
        ]);

        if($validator->fails()){
            foreach ($validator->errors()->all() as $message){
                toastr()->error($message);
            }
            return back()->withInput();
        }

        try{
            $input = $request->all();
            $units = AttendanceBonusConfig::pluck('unit_id')->toArray();
            // dd($units);
            if(in_array($request->unit_id, $units)){
                // dd('Yes');
                DB::table('hr_attendance_bonus_dynamic')
                ->where('unit_id',$request->unit_id)
                ->update([
                    'late_count'   => $request->late_count,
                    'leave_count'  => $request->leave_count,
                    'absent_count' => $request->absent_count,
                    'first_month'  => $request->first_month,
                    'second_month' => $request->second_month,
                    'updated_by'   => auth()->user()->id
                ]);
            }
            else{
                $input['created_by'] = auth()->user()->id;
                AttendanceBonusConfig::create($input);
            }

            toastr()->success("Attendance Bonus Updated");
            return back();
        }catch(\Exception $e){
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }

    public function getData(Request $req){
        $data = AttendanceBonusConfig::where('unit_id', $req->unit_id)->first();
        return Response::json($data);
    }
}
