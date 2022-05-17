<?php

namespace App\Http\Controllers\Hr\Setup;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hr\BillAnnounceRequest;
use App\Models\Hr\BillSettings;
use App\Models\Hr\BillSpecialSettings;
use App\Models\Hr\BillType;
use App\Models\Hr\Designation;
use App\Models\Hr\Unit;
use App\Repository\Hr\BillAnnounceRepository;
use Illuminate\Http\Request;
use Validator, DB;

class BillAnnounceSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['billType']  = BillType::get()->keyBy('id');
        $data['billTypeList']  = collect($data['billType'])->pluck('name', 'id');
        $data['unit'] = unit_by_id();
        $data['unitList']  = collect($data['unit'])->pluck('hr_unit_short_name', 'hr_unit_id');
        $data['billList'] = BillSettings::with('available_special')->whereIn('unit_id', auth()->user()->unit_permissions())->where('status', 1)->orderBy('unit_id', 'desc')->orderBy('start_date', 'desc')->get();
        return view('hr.setup.bill.index', $data);
    }

    public function history()
    {
        $data['billType']  = BillType::get()->keyBy('id');
        $data['billTypeList']  = collect($data['billType'])->pluck('name', 'id');
        $data['unit'] = unit_by_id();
        $data['billList'] = BillSettings::with('available_special')->whereIn('unit_id', auth()->user()->unit_permissions())->orderBy('unit_id', 'desc')->orderBy('start_date', 'desc')->get();
        return view('hr.setup.bill.list_table', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(BillAnnounceRequest $request, BillAnnounceRepository $bill)
    {
        $data['type'] = 'error';
        $input = $request->all();
        DB::beginTransaction();
        try {
            $totalUnit = count($input['unit']);
            for ($i=0; $i < $totalUnit; $i++) { 
                $input['unit_id'] = $input['unit'][$i];
                $bill->billAnnounceStoreProcess($input);
            }

            $data['url'] = url()->current();
            DB::commit();
            $data['type'] = 'success';
            $data['message'][] = 'Successfully Created';
            return response($data);
        } catch (\Exception $e) {
            DB::rollback();
            $data['message'][] = $e->getMessage();
            return response($data);
        }
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bill = BillSettings::findOrFail($id);
        $billGroup = [];
        if(count($bill->available_special) > 0){
            $billGroup = collect($bill->available_special)->sortBy('pay_type')->groupBy('adv_type', true);
        }
        $data['billGroup'] = $billGroup;
        $data['bill'] = $bill;
        $data['unit'] = unit_by_id();
        $data['location']    = location_by_id();
        $data['department']  = department_by_id();
        $data['designation'] = designation_by_id();
        $data['section']     = section_by_id();
        $data['subSection']  = subSection_by_id();
        return view('hr.setup.bill.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
