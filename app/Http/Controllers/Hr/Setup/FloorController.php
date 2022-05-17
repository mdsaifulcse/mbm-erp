<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Hr\Floor;
Use Validator,DB, ACL, Cache;

class FloorController extends Controller
{
    public function floor()
    {
        $unitList  = collect(unit_authorization_by_id())->pluck('hr_unit_name', 'hr_unit_id');
        $floors= Floor::Select(
                    'hr_floor.hr_floor_id',
                    'hr_floor.hr_floor_name',
                    'hr_floor.hr_floor_name_bn',
                    'u.hr_unit_name'
                )
                ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'hr_floor.hr_floor_unit_id')
                ->whereIn('hr_floor_unit_id', array_keys($unitList->toArray()))
                ->get();

    	return view('hr/setup/floor', compact('unitList', 'floors'));
    }

    public function floorStore(Request $request)
    {
    	$validator= Validator::make($request->all(),[
    		'hr_floor_unit_id'=>'required|max:11',
    		'hr_floor_name'=>'required|max:128',
            'hr_floor_name_bn'=>'max:255'
    	]);

        if($validator->fails()){
            foreach ($validator->errors()->all() as $message){
                toastr()->error($message);
            }
            return back()->withInput();
        }

        $input = $request->all();
        $input['created_by'] = auth()->user()->id;
        try {
            Floor::create($input);
            Cache::forget('floor_by_id');
            toastr()->success('Successfully Floor created');
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }

    public function getFloorListByUnitID(Request $request)
    {
        $list = "<option value=\"\">Select Floor Name </option>";
        if (!empty($request->unit_id))
        {
            $floorList  = Floor::where('hr_floor_unit_id', $request->unit_id)
                    ->where('hr_floor_status', '1')
                    ->pluck('hr_floor_name', 'hr_floor_id');

            foreach ($floorList as $key => $value)
            {
                $list .= "<option value=\"$key\">$value</option>";
            }
        }
        return $list;
    }
    public function floorDelete($id){
        try {
            $floor = Floor::findOrFail($id);
            $floor->delete();
            Cache::forget('floor_by_id');
            toastr()->success('Successfully Floor deleted');
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }
    public function floorUpdate(Request $request){
        $unitList  = Unit::where('hr_unit_status', '1')->pluck('hr_unit_name', 'hr_unit_id');
        $floor= DB::table('hr_floor AS f')->where('f.hr_floor_id','=', $request->hr_floor_id)->first();

        $floors= DB::table('hr_floor as f')
                    ->Select(
                        'f.hr_floor_id',
                        'f.hr_floor_name',
                        'f.hr_floor_name_bn',
                        'u.hr_unit_name'
                    )
                    ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'f.hr_floor_unit_id')
                    ->whereIn('f.hr_floor_unit_id', auth()->user()->unit_permissions())
                    ->get();
        return view('hr/setup/floor_update', compact('floor','unitList','floors'));
    }

    public function floorUpdateStore(Request $request){
        $validator= Validator::make($request->all(),[
            'hr_floor_unit_id'=>'required|max:11',
            'hr_floor_name'=>'required|max:128',
            'hr_floor_name_bn'=>'max:255'
        ]);

        if($validator->fails()){
            foreach ($validator->errors()->all() as $message){
                toastr()->error($message);
            }
            return back()->withInput();
        }

        $input = $request->all();
        $input['updated_by'] = auth()->user()->id;
        try {
            $floor = Floor::findOrFail($request->hr_floor_id);
            $floor->update($input);
            Cache::forget('line_by_id');
            toastr()->success('Successfully Floor updated');
            return redirect('hr/setup/floor');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }
}
