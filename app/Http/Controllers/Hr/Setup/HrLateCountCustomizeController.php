<?php

namespace App\Http\Controllers\Hr\Setup;

use App\Http\Controllers\Controller;
use App\Models\Hr\HrLateCount;
use App\Models\Hr\Shift;
use App\Models\Hr\Unit;
use App\Models\Hr\hrLateCountCustomize;
use DB,Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HrLateCountCustomizeController extends Controller
{
    public function showForm($lateCountCustomizeSingle='')
    {
        try {
            if(empty($lateCountCustomizeSingle)) {
                $lateCountCustomizeSingle = (object)[
                    'id'            => '',
                    'hr_unit_id'    => '',
                    'shift_id'      => '',
                    'date_from'     => '',
                    'date_to'       => '',
                    'time'          => '',
                    'comment'       => ''
                ];
            }
            $unit_list = collect(unit_authorization_by_id())->pluck('hr_unit_name', 'hr_unit_id');
            $lateCountCustomize_list = hrLateCountCustomize::whereIn('hr_unit_id', array_keys($unit_list->toArray()))->get();
        	return view('hr/setup/hr_late_count_customize', compact('unit_list','lateCountCustomize_list','lateCountCustomizeSingle'));
        } catch(\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    public function getShiftsByUnit(Request $request){
        $data = DB::table('hr_shift')
                ->where('hr_shift_unit_id','=', $request->unit_id)
                ->where('hr_shift_status', 1)
                ->select('hr_shift_id', 'hr_shift_name', 'hr_shift_code', 'hr_shift_start_time', 'hr_shift_end_time')
                ->get()
                ->toArray();

        return Response::json($data);
    }

    public function roles()
    {
        return $role = [
            'hr_unit_id'    => 'required',
            'hr_shift_name' => 'required',
            'date_from'     => 'required',
            'date_to'       => 'required',
            'time'          => 'required|numeric'
        ];
    }
    public function saveLateCountCustomize(Request $request)
    {
        // check validation
        $validator = Validator::make($request->all(), $this->roles());
        if ($validator->fails()) {
            return redirect()
                        ->back()
                        ->withErrors($validator)
                        ->withInput();
        }
        $input = $request->all();
        unset($input['_token']);
        // return $input;
        try {
            if($input['hr_shift_name'] == 'all'){
                $getShifts = Shift::getShiftsByUnitIdWiseUqiue($input['hr_unit_id']);
                foreach ($getShifts as $shift) {
                    $input['hr_shift_name'] = $shift['hr_shift_name'];
                    $data = $this->lateCountCreateUpdate($input);
                }
                if($data['type']){
                    $getUnitName = Custom::unitIdWiseName($input['hr_unit_id']);
                    $msg = "Unit: ".$getUnitName." & Shift: All Customize Late Count Assign ".$input['time'];
                }
            }else{
                $getLateCustomize = hrLateCountCustomize::checkExistsAlreadyHaving($input);
                $data = $this->lateCountCreateUpdate($input);
                if($data['type']){
                    $msg = $data['value'];
                }
            }
            return redirect()->back()->with('success', $msg);
        } catch(\Exception $e) {
            
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function lateCountCreateUpdate($input)
    {
        DB::beginTransaction();
        try {
            $getLateCustomize = hrLateCountCustomize::checkExistsAlreadyHaving($input);
            if(empty($getLateCustomize)) {
                $id = hrLateCountCustomize::create($input)->id;
                $getLateCount = HrLateCount::getUnitShiftIdWiseCheckExists($input['hr_unit_id'], $input['hr_shift_name']);
                
                if($getLateCount != null){
                    $defultData = [
                        'date_from' => $input['date_from'],
                        'date_to'   => $input['date_to'],
                        'value'     => $input['time'],
                        'updated_by' => auth()->user()->id
                    ];
                    $lateCount = HrLateCount::findOrFail($getLateCount->id);
                    $lateCount->update($defultData);
                    
                }else{
                    $defultData = [
                        'hr_unit_id'    => $input['hr_unit_id'],
                        'hr_shift_name' => $input['hr_shift_name'],
                        'default_value' => $input['time'],
                        'date_from'     => $input['date_from'],
                        'date_to'       => $input['date_to'],
                        'value'         => $input['time'],
                        'created_by'    => auth()->user()->id
                    ];
                    HrLateCount::create($defultData);
                }
                $msg = 'Late Count Customize Insert Success';
                $result['type'] = 'success';
            } else {
                $id = $getLateCustomize->id;
                $result['type'] = 'warning';
                $msg = 'Time exist in this date range.';
            }

            $result['value'] = $msg;
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            $result['type'] = 'error';
            $result['value'] = $bug;
            return $result;
        }
    }

    public function editLateCountCustomize($id)
    {
        try {
            $lateCountCustomizeSingle = hrLateCountCustomize::where('id',$id)->first();
            if(!empty($lateCountCustomizeSingle)){
                return $this->showForm($lateCountCustomizeSingle);
            } else {
                return redirect()->back()->with('error','Data not found.');
            }
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateLateCountCustomize(Request $request, $id)
    {
        // check validation
        $validator = Validator::make($request->all(), $this->roles());
        if ($validator->fails()) {
            return redirect()
                        ->back()
                        ->withErrors($validator)
                        ->withInput();
        }
        // check if exist
        $getLateCountCustomize = hrLateCountCustomize::findOrFail($id);
        if(empty($getLateCountCustomize)) {
            return redirect()->back()->with('error','Data not found.');
        }
        $input = $request->all();
        $input['updated_by'] = auth()->user()->id;
        DB::beginTransaction();
        try {
            $getLateCount = HrLateCount::getUnitShiftWiseCheckExists($input['unit_id'], $input['shift_id']);
            $defultData = [
                'date_from' => $input['date_from'],
                'date_to'   => $input['date_to'],
                'value'     => $input['time'],
                'updated_by' => auth()->user()->id
            ];
            $late = HrLateCount::findOrFail($getLateCount->id);
            $late->update($defultData);

            $getLateCountCustomize->update($input);
            toastr()->success('Late Count Customize Update Successfully');
            DB::commit();
            return back();
        } catch(\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }

    public function deleteLateCountCustomize($id)
    {
        DB::beginTransaction();
        try {
            // check if exist
            $getLateCountCustomize = hrLateCountCustomize::where('id',$id)->first();

            if(!empty($getLateCountCustomize)) {
                $getLateCount = HrLateCount::getUnitShiftWiseCheckExists($getLateCountCustomize->hr_unit_id, $getLateCountCustomize->hr_shift_name);
                $getCheckLate = HrLateCount::getCheckExistsLateCount($getLateCountCustomize);
                if($getCheckLate != null){
                    $defultData = [
                        'date_from' => '',
                        'date_to'   => '',
                        'value'     => ''
                    ];
                    $late = HrLateCount::findOrFail($getLateCount->id);
                    $late->update($defultData);
                }
                $lateCountCustomize = hrLateCountCustomize::findOrFail($id);
                $lateCountCustomize->delete();
                DB::commit();
                toastr()->success('Delete Successfully');
                return redirect('hr/setup/late_count_customize');
            } else {
                toastr()->error('Data not found.');
                return back();
            }
        } catch(\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return $bug;
            toastr()->error($bug);
            return back();
        }
    }

}