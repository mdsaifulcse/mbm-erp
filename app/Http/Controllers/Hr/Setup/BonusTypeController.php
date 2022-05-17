<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\BonusType;
use App\Models\Hr\EmployeeBonusSheet;
use Illuminate\Support\Facades\Cache;
use Validator,Response;

class BonusTypeController extends Controller
{
    public function index()
    {
    	$bonus_types = BonusType::orderBy('id', 'DESC')->get();
    	return view('hr.setup.bonus_type', compact('bonus_types'));
    }

    public function entrySave(Request $request)
    {
    	$validator = Validator::make($request->all(),[
        'bonus_type_name' => 'required|max:50',
        'eligible_month' => 'required'
      ]);

      if($validator->fails()){
          foreach ($validator->errors()->all() as $message){
              toastr()->error($message);
          }
          return back()->withInput();
      }

      $input = $request->all();  
      try{
          $input['bonus_type_name'] = $this->quoteReplaceHtmlEntry($request->bonus_type_name);
          BonusType::create($input);
          Cache::pull('bonus_type_by_id');
          toastr()->success('Successfully bonus type created');
          return back();
      }catch(\Exception $e){
          $bug = $e->getMessage();
          toastr()->error($bug);
          return back();
      }
    }

    //Edit
    public function editDataFetch(Request $req){
      $data = BonusType::where('id',$req->bt_id)->first();
      // dd($data);
      return Response::json($data);
    }

    public function entryUpdate(Request $req){

        $validator = Validator::make($req->all(),[
          'bonus_type_name' => 'required|max:50',
          'eligible_month' => 'required'
        ]);
        if($validator->fails()){
            foreach ($validator->errors()->all() as $message){
                toastr()->error($message);
            }
            return back()->withInput();
        }

        $input = $request->all();
        $input['updated_by'] = auth()->user()->id;
        $input['bonus_type_name'] = $this->quoteReplaceHtmlEntry($req->id);
        unset($input['_token']);
        try {
            $bonus = BonusType::findOrFail($request->hr_floor_id);
            $bonus->update($input);
            Cache::forget('bonus_type_by_id');
            toastr()->success('Successfully BonusType updated');
            return redirect('hr/setup/bonus_type');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }

    public function entryDelete($id)
    {
      try {
          $bonus = BonusType::findOrFail($id);
          $bonus->delete();
          Cache::forget('bonus_type_by_id');
          toastr()->success('Successfully Bonus Type deleted');
          return back();
      } catch (\Exception $e) {
          $bug = $e->getMessage();
          toastr()->error($bug);
          return back();
      }
    }
}
