<?php

namespace App\Exports\Hr;

use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DailyReportExport implements FromView, WithHeadingRow
{
	use Exportable;

    protected $data;

    protected $report;

    public function __construct($data, $report)
    {
        $this->data = $data;
        $this->report = $report;
    }
    
    public function view(): View
    {
    	$fields = $this->data;
    	
        if($this->report == 'special_ot'){
            return view('hr.reports.daily_activity.export.special_ot', $fields);
        }
        
        if($this->report == 'employee'){
            return view('hr.reports.daily_activity.attendance.employee', $fields);
        }

        if($this->report == 'present'){
            return view('hr.reports.daily_activity.attendance.present', $fields);
        }

    }
    public function headingRow(): int
    {
        return 3;
    }
}