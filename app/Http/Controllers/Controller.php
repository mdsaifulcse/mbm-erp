<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Models\UserLog;
use Image;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    # Write Every Events in Log File
    public function logFileWrite($message, $event_id){
        $log_message = date("Y-m-d H:i:s")." \"".Auth()->user()->associate_id."\" ".$message." ".$event_id.PHP_EOL;
        $log_message .= file_get_contents("assets/log.txt");
        file_put_contents("assets/log.txt", $log_message);

        $associate_id=Auth()->user()->associate_id;
        $logs=UserLog::where('log_as_id',$associate_id)->orderBy('updated_at','ASC')->get();
        if(count($logs)<3){
            $user_log= new UserLog;
        }else{
            $user_log =$logs->first();
            $user_log->id = $logs->first()->id;
        }
            $user_log->log_as_id = $associate_id;
            $user_log->log_message = $message;
            $user_log->log_table = '';
            $user_log->log_row_no = $event_id;
            $user_log->save();

    }

    // write every events in log file process queue procedu
    public function logFileWriteJobs($message, $event_id)
    {
        $filePath = url('/assets\log.txt');
    	$job = (new ProcessLogFile(auth()->user()->associate_id, $message, $event_id, $filePath))
        ->delay(Carbon::now()->addSeconds(10));
        dispatch($job);
    }

    public function quoteReplaceHtmlEntry($data)
    {
        if(strpos($data, "'") !== FALSE){
          return str_replace("'", "&#39;", $data);
        }elseif(strpos($data, '"') !== FALSE){
          return str_replace('"', "&#34;", $data);
        }else{
          return $data;
        }


    }

    public function getTableNameUnit($unit){
      if($unit ==1 || $unit==4 || $unit==5 || $unit==9){
          $tableName="hr_attendance_mbm AS a";
      }else if($unit ==2){
          $tableName="hr_attendance_ceil AS a";
      }else if($unit ==3){
          $tableName="hr_attendance_aql AS a";
      }else if($unit ==6){
          $tableName="hr_attendance_ho AS a";
      }else if($unit ==8){
          $tableName="hr_attendance_cew AS a";
      }else{
          $tableName="hr_attendance_mbm AS a";
      }

      return $tableName;

  }


  //From Bizz solution
   public function backWithError($message)
    {
        $notification = [
            'message' => $message,
            'alert-type' => 'error'
        ];
        return back()->with($notification);
    }

    public function backWithSuccess($message)
    {
        $notification = [
            'message' => $message,
            'alert-type' => 'success'
        ];
        return back()->with($notification);
    }

    public function backWithWarning($message)
    {
        $notification = [
            'message' => $message,
            'alert-type' => 'warning'
        ];
        return back()->with($notification);
    }

    public function redirectBackWithWarning($message, $route)
    {
        $notification = [
            'message' => $message,
            'alert-type' => 'warning'
        ];
        return redirect()->route($route)->with($notification);
    }

    public function redirectBackWithSuccess($message, $route)
    {
        $notification = [
            'message' => $message,
            'alert-type' => 'success'
        ];
        return redirect()->route($route)->with($notification);
    }

    function photoUpload($photoData,$folderName,$width=null,$height=null)
    {

        $photoOrgName = $photoData->getClientOriginalName();
        $photoType = $photoData->getClientOriginalExtension();

        //$fileType = $photoData->getClientOriginalName();
        $fileName = substr($photoOrgName, 0, -4) . date('d-m-Y-i-s') . '.' . $photoType;
        $path2 = $folderName . date('/Y/m/d/');
        //return $path2;
        if (!is_dir(public_path($path2))) {
            mkdir(public_path($path2), 0777, true);
        }


        $photoData->move(public_path($path2), $fileName);

        if ($width != null && $height != null) { // width & height mention-------------------
            $img = \Image::make(public_path($path2 . $fileName));
            $img->encode('webp', 75)->resize($width, $height);
            $img->save(public_path($path2 . $fileName));
            return $photoUploadedPath = $path2 . $fileName;

        } elseif ($width != null) { // only width mention-------------------

            $img = \Image::make(public_path($path2 . $fileName));
            $img->encode('webp', 75)->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save(public_path($path2 . $fileName));

            return $photoUploadedPath = $path2 . $fileName;

        } else {
            $img = \Image::make(public_path($path2 . $fileName));
            $img->save(public_path($path2 . $fileName));
            return $photoUploadedPath = $path2 . $fileName;
        }
    }

    function fileUpload($filedata,$folderName){

        $fileType = $filedata->getClientOriginalExtension();
        $fileName = rand(1, 1000) . date('dmyhis') . "." . $fileType;
        $path2 = $folderName. date('/Y/m/d/');
        //return $path2;
        if (!file_exists(public_path($path2))) {
            mkdir(public_path($path2), 0777, true);
        }
        $img =$filedata->move(public_path($path2),$fileName);

        return $photoUploadedPath=$path2 . $fileName;

    }


    public function paginate($items, $perPage, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

}
