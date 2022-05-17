<?php

namespace App\Http\Controllers\Merch\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merch\Buyer;
use App\Models\Merch\Season;
use App\Models\Merch\SeasonInput;
use Illuminate\Support\Facades\Cache;
use Validator, DB;

class SeasonController extends Controller
{
    public function showForm()
    {
    	$buyerList = Buyer::pluck("b_name", "b_id");
    	$seasons = DB::table("mr_season")
    		->select("mr_season.*", "mr_buyer.b_name")
    		->leftJoin("mr_buyer", "mr_season.b_id", "=", "mr_buyer.b_id")
    		->get();
    	return view('merch/setup/season', compact('buyerList', 'seasons'));
    }

    // Save Data
    public function store(Request $request)
    {
    	$validator = Validator::make($request->all(),[
            'b_id'    => 'required|max:11',
            'se_name' => 'required|max:50',
            'se_start' =>'required',
            'se_end'   =>'required'
    	]);

        if($validator->fails()){
            foreach ($validator->errors()->all() as $message){
                toastr()->error($message);
            }
            return back();
        }
        $input = $request->all();

        try {
            $store = new Season;
            $store->b_id     = $request->b_id;
            $store->se_name  = $this->quoteReplaceHtmlEntry($request->se_name);
            $store->se_start = $request->se_start;
            $store->se_end   = $request->se_end;
            $store->created_by = auth()->user()->id;

            if ($store->save()) {
                $this->logFileWrite("Season Saved", $store->se_id );
                toastr()->success('Season Save Successfully');
                if (Cache::has('season_by_id')){
                    Cache::forget('season_by_id');
                }
                return back();
            } else {
                toastr()->error('Something Wrong!, Please try again');
                return back();
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();

        }
    }

    // Show Edit From
    public function edit(Request $request)
    {
        #-----------------------------------------------------------#
    	$buyerList = Buyer::pluck("b_name", "b_id");
    	$season = DB::table("mr_season")->where("se_id", $request->id)->first();
    	return view('merch/setup/season_edit', compact('buyerList', 'season'));
    }

    // Save Data
    public function update(Request $request)
    {
        if($request->has('season_state')){
            $season_status=1;
        }
        else{
            $season_status=0;
        }

        //dd($season_status);

        #-----------------------------------------------------------#
    	$validator = Validator::make($request->all(),[
            'b_id'    => 'required|max:11',
            'se_name' => 'required|max:50',
            'se_mm_start' =>'required',
            'se_mm_end'   =>'required',
    	]);

        if ($validator->fails())
        {
            return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Please fillup all required fields!');
        }
        else
        {
            $date_s = $request->se_mm_start;
            $date_e = $request->se_mm_end;
            $date_start=date('Y-m-d', strtotime($date_s));
            $date_end=date('Y-m-d', strtotime($date_e));


            $update = DB::table("mr_season")->where('se_id', $request->se_id)
            ->update([
            	'b_id'        => $request->b_id,
            	'se_name'     => $this->quoteReplaceHtmlEntry($request->se_name),
            	'se_start'    => $date_start,
            	'se_end'      => $date_end,
                'season_status' =>$season_status
            ]);

            //dd($update);


            if ($update)
            {
                $this->logFileWrite("Season updated", $request->se_id);
                if (Cache::has('season_by_id')){
                    Cache::forget('season_by_id');
                }
                return redirect("merch/setup/season")
                    ->with('success', 'Update Successful.');
            }
            else
            {
                return back()
                    ->withInput()->with('error', 'Please try again.');
            }
        }
    }

    // Delete
    public function destroy(Request $request)
    {
        Season::where('se_id',  $request->id)->delete();

        $this->logFileWrite("Season Deleted", $request->id );
        if (Cache::has('season_by_id')){
            Cache::forget('season_by_id');
        }
        toastr()->error('Season Delete Successfully');
        return back();
    }
    public function searchSeason(Request $request)
    {
       $result= season::where('se_name', 'LIKE', '%'.$request->name_startsWith.'%')
                ->where(function($q) use ($request){
                    if($request->b_id != ""){
                        $q->where('b_id',$request->b_id);
                    }
                })
                ->get();

        $data = array();
        foreach ($result as $season) {
            $data[] = $season->se_name.'|'.'yes';
        }
        if (Cache::has('season_by_id')){
            Cache::forget('season_by_id');
        }


        return $data;
    }
    //Write Every Events in Log File
    public function logFileWrite($message, $event_id){
        $log_message = date("Y-m-d H:i:s")." ".Auth()->user()->associate_id." \"".$message."\" ".$event_id.PHP_EOL;
        $log_message .= file_get_contents("assets/log.txt");
        file_put_contents("assets/log.txt", $log_message);
    }
}
