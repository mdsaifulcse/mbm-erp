<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Hr\Line;
use Validator,DB, ACL, Cache;
use App\Models\Hr\Floor;

class LineController extends Controller
{
    public function line()
    {
        $unitList  = collect(unit_authorization_by_id())->pluck('hr_unit_name', 'hr_unit_id');

        $lines= Line::Select(
                'hr_line.hr_line_id',
                'hr_line.hr_line_name',
                'hr_line.hr_line_name_bn',
                'u.hr_unit_name',
                'f.hr_floor_name'
            )
            ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'hr_line.hr_line_unit_id')
            ->leftJoin('hr_floor AS f', 'f.hr_floor_id', '=', 'hr_line.hr_line_floor_id')
            ->whereIn('hr_line_unit_id', array_keys($unitList->toArray()))
            ->get();
        $trashed = [];
    	return view('hr/setup/line', compact('unitList', 'lines', 'trashed'));
    }

    public function lineStore(Request $request)
    {
    	$validator= Validator::make($request->all(),[
    		'hr_line_unit_id'=>'required|max:11',
    		'hr_line_floor_id'=>'required|max:11',
            'hr_line_name'=>'required|max:128',
    		'hr_line_name_bn'=>'max:255'
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
            Line::create($input);
            Cache::forget('line_by_id');
            toastr()->success('Successfully Line created');
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }

    public function getLineListByFloorID(Request $request)
    {
        $list = "<option value=\"\">Select Line Name </option>";
        if (!empty($request->unit_id) && !empty($request->floor_id))
        {
            $lineList  = Line::where('hr_line_unit_id', $request->unit_id)
                    ->where('hr_line_floor_id', $request->floor_id)
                    ->where('hr_line_status', '1')
                    ->pluck('hr_line_name', 'hr_line_id');

            foreach ($lineList as $key => $value)
            {
                $list .= "<option value=\"$key\">$value</option>";
            }
        }
        return $list;
    }

    public function lineDelete($id){
        try {
            $line = Line::findOrFail($id);
            $line->delete();
            Cache::forget('line_by_id');
            toastr()->success('Successfully Line deleted');
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }

    public function lineUpdate($id){
        $unitList  = Unit::where('hr_unit_status', '1')->pluck('hr_unit_name', 'hr_unit_id');
        $line= DB::table('hr_line')->where('hr_line_id', '=', $id)->first();
        
        $floorList= Floor::where('hr_floor_unit_id', $line->hr_line_unit_id)->pluck('hr_floor_name', 'hr_floor_id');

        $lines= DB::table('hr_line AS l')
                    ->Select(
                        'l.hr_line_id',
                        'l.hr_line_name',
                        'l.hr_line_name_bn',
                        'u.hr_unit_name',
                        'f.hr_floor_name'
                    )
                    ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'l.hr_line_unit_id')
                    ->leftJoin('hr_floor AS f', 'f.hr_floor_id', '=', 'l.hr_line_floor_id')
                    ->whereIn('hr_line_unit_id', auth()->user()->unit_permissions())
                    ->get();
        // dd($floorList);
        $trashed = [];           
        return view('/hr/setup/line_update',compact('line', 'unitList', 'floorList','lines', 'trashed'));
    }

    public function lineUpdateStore(Request $request){
        $validator= Validator::make($request->all(),[
            'hr_line_unit_id'=>'required|max:11',
            'hr_line_floor_id'=>'required|max:11',
            'hr_line_name'=>'required|max:128',
            'hr_line_name_bn'=>'max:255'
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
            $line = Line::findOrFail($request->hr_line_id);
            $line->update($input);
            Cache::forget('line_by_id');
            toastr()->success('Successfully Line updated');
            return redirect('hr/setup/line');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }
}
