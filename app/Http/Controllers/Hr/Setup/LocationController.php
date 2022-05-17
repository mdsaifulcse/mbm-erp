<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Location;
use App\Models\Hr\Unit;
Use Validator, Image,ACL,DB, Cache;

class LocationController extends Controller
{
    public function location()
    {
        $unitList  = collect(unit_authorization_by_id())->pluck('hr_unit_name', 'hr_unit_id');
        $locations= Location::whereIn('hr_location_unit_id', array_keys($unitList->toArray()))
                ->get();
    	return view('hr/setup/location', compact('locations','unitList'));
    }

    public function locationStore(Request $request)
    {
    	$validator= Validator::make($request->all(),[
            'hr_location_name'=>'required|max:128|unique:hr_location',
    		'hr_location_short_name'=>'required|max:64|unique:hr_location',
            'hr_location_unit_id'=>'required|max:11',
            'hr_location_name_bn'=>'max:255',
            'hr_location_code'=>'max:10',
            'hr_location_address'=>'max:255',
    		'hr_location_address_bn'=>'max:512',
            'hr_location_logo' => 'image|mimes:jpeg,png,jpg|max:200|dimensions:min_width=248,min_height=148',
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
            Location::create($input);
            Cache::forget('location_by_id');
            toastr()->success('Successfully Location created');
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }
    public function locationDelete($id){
        try {
            $location = Location::findOrFail($id);
            $location->delete();
            Cache::forget('location_by_id');
            toastr()->success('Successfully Location deleted');
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }
    public function locationUpdate($id){
        $locations= Location::all();
        $location= DB::table('hr_location')->where('hr_location_id','=',$id)->first(); //dd($location);
        $unitList= Unit::pluck('hr_unit_name', 'hr_unit_id');
        return view('/hr/setup/location_update',compact('location','unitList','locations'));
    }
    public function locationUpdateStore(Request $request){
        $validator= Validator::make($request->all(),[
            'hr_location_name'=>'required|max:128',
            'hr_location_short_name'=>'required|max:64',
            'hr_location_unit_id'=>'required|max:11',
            'hr_location_name_bn'=>'max:255',
            'hr_location_code'=>'max:10',
            'hr_location_address'=>'max:255',
            'hr_location_address_bn'=>'max:512',
            'hr_location_logo' => 'image|mimes:jpeg,png,jpg|max:200|dimensions:min_width=248,min_height=148',
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
            $location = Location::findOrFail($request->hr_location_id);
            $location->update($input);
            Cache::forget('location_by_id');
            toastr()->success('Successfully Location updated');
            return redirect('hr/setup/location');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }

    public function locationListUnit(Request $request)
    {
        $unitId = $request->unit;
        try {
            $getLocation = Location::where('hr_location_unit_id', $unitId)->orderBy('hr_location_name', 'desc')->get();
            $data= '<option value="">Select Location</option>';
            foreach ($getLocation as $location) {
                $data.='<option value="'.$location->hr_location_id.'">'.$location->hr_location_name.'</option>';
            }
            return $data;
        } catch (\Exception $e) {
            $data= '<option value="">Select Location</option>';
            return $data;
        }
    }

}
