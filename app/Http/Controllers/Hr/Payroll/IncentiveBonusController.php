<?php

namespace App\Http\Controllers\Hr\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\IncentiveBonus;
use App\Packages\QueryExtra\QueryExtra;
use DB;
use Illuminate\Http\Request;

class IncentiveBonusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('hr.payroll.incentive.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    public function preview(Request $request)
    {
        $input = $request->all();
        try {
            // dd($input['as_id']);
            // array_filter($input['as_id']);
            $employee = Employee::whereIn('as_id', $input['as_id'])->get()->keyBy('as_id');
            $totalAmount = 0;
            $data['input'] = $input;
            $data['department'] = department_by_id();
            $data['designation'] = designation_by_id();
            $data['floor'] = floor_by_id();
            $data['line'] = line_by_id();
            $data['employee'] = $employee;
            $data['totalAmount'] = $totalAmount;
            return view('hr.payroll.incentive.preview', $data)->render();
        } catch (\Exception $e) {
            return $e->getMessage();   
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $result['type'] = 'error';
        DB::beginTransaction();
        try {
            $deleteRow = [];
            $insertRow = [];
            $updateRow = [];
            array_filter($input['as_id']);
            // get incentive
            $getIncentive = IncentiveBonus::where('date', $input['date'])->whereIn('as_id', $input['as_id'])->get()->keyBy('as_id');
            $totalEmp = count($input['as_id']);
            for ($i=0; $i < $totalEmp; $i++) { 
                $asId = $input['as_id'][$i];
                $amount = $input['amount'][$i];
                if($asId != '' && $asId != null){
                    // delete employee incentive
                    if($amount == 0){
                        $deleteRow[] = $asId;
                    }
                    // update / insert employee incentive
                    if($amount > 0){
                        $data = [
                            'as_id' => $asId,
                            'amount' => $amount,
                            'date' => $input['date']
                        ];
                        if(isset($getIncentive[$asId]) && $getIncentive[$asId] != null){
                            $updateRow[] = [
                                'data' => $data,
                                'keyval' => $getIncentive[$asId]->id
                            ];
                        }else{
                            $insertRow[] = $data;
                        }
                    }
                    
                }
            }
            // delete
            if(count($deleteRow) > 0){
                IncentiveBonus::where('date', $input['date'])
                ->whereIn('as_id', $deleteRow)
                ->delete();
            }

            // update
            if(count($updateRow) > 0){
                (new QueryExtra)
                ->table('hr_incentive_bonus')
                ->whereKey('id')
                ->bulkup($updateRow);
            }

            // insert
            if(count($insertRow) > 0){
                $chunk = collect($insertRow)->chunk(50);
                foreach ($chunk as $key => $n) {        
                    DB::table('hr_incentive_bonus')->insertOrIgnore(collect($n)->toArray());
                }
            }

            DB::commit();
            $result['type'] = 'success';
            $result['url'] = url()->previous();
            $result['message'] = 'Incentive Bonus Save Successfully!';
            return $result;
        } catch (\Exception $e) {
            DB::rollback();
            $result['message'] = $e->getMessage();
            return $result;
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
        //
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

    public function employee(Request $request)
    {
        $input = $request->all();
        try {
            $queryData = DB::table('hr_as_basic_info AS b')
                ->select('b.as_id', 'b.associate_id','b.as_name','b.as_designation_id','b.as_department_id', 'b.as_line_id', 'b.as_floor_id', 'b.as_oracle_code')
                ->where('b.as_status', '!=', 0)
                ->when(!empty($input['keyvalue']), function ($query) use($input){
                    if($input['type'] == 'associateid'){
                        return $query->where('b.associate_id','LIKE','%'.$input['keyvalue'].'%');
                    }else if($input['type'] == 'oracleid'){
                        return $query->where('b.as_oracle_code','LIKE','%'.$input['keyvalue'].'%');
                    }else{
                        return $query->where('b.as_name','LIKE','%'.$input['keyvalue'].'%');
                    }
                })
                ->whereIn('b.as_location', auth()->user()->location_permissions())
                ->whereIn('b.as_unit_id', auth()->user()->unit_permissions());
            $getEmployee = $queryData->limit(10)->get();
            $empIds = collect($getEmployee)->pluck('as_id');
            $department = department_by_id();
            $designation = designation_by_id();
            $line = line_by_id();
            $floor = floor_by_id();
            $incentive = DB::table("hr_incentive_bonus")
            ->whereIn('as_id', $empIds)
            ->where('date', $input['date'])
            ->get()
            ->keyBy('as_id');
            $getData = collect($getEmployee)->map(function($q) use ($incentive, $department, $designation, $line, $floor){
                $p = (object)[];
                $p->amount = 0;
                if(isset($incentive[$q->as_id])){
                    $p->amount = $incentive[$q->as_id]->amount??0;
                }
                $p->as_id = $q->as_id;
                $p->associate = $q->associate_id;
                $p->as_oracle_code = $q->as_oracle_code;
                $p->name = $q->as_name;
                $p->designation = $designation[$q->as_designation_id]['hr_designation_name']??'';
                $p->department = $department[$q->as_department_id]['hr_department_name']??'';
                $p->line = $line[$q->as_line_id]['hr_line_name']??'';
                $p->floor = $floor[$q->as_floor_id]['hr_floor_name']??'';
                return $p;
            });
            return $getData;
        } catch (\Exception $e) {
            return 'error';
        }
    }
}
