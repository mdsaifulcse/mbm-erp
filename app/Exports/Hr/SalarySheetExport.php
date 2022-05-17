<?php

namespace App\Exports\Hr;

use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SalarySheetExport implements FromView, WithHeadingRow
{
	use Exportable;

    public function __construct($data, $page_type)
    {
        $this->data = $data;
        $this->page_type = $page_type;
    }
    
    public function view(): View
    {
    	$fields = $this->data;
    	
        if($this->page_type == 'bank'){
            return view('hr.payroll.bank_part.excel',$fields);
        }elseif($this->page_type == 'bank-report'){
            return view('hr.payroll.bank_part.reports',$fields);
        }elseif($this->page_type == 'report'){
            return view('hr.reports.salary.excel',$fields);
        }elseif($this->page_type == 'summery_report'){
            return view('hr.reports.salary_summery.excel',$fields);
        }

    }
    public function headingRow(): int
    {
        return 3;
    }
}