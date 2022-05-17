<?php

namespace App\Exports\Hr;

use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IncrementExport implements FromView, WithHeadingRow
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
    	
        if($this->page_type == 'eligible'){
            return view('hr.payroll.increment.excel_eligible',$fields);
        }else if($this->page_type == 'onapproval'){
            return view('hr.payroll.increment.excel_on_approval',$fields);
        }

    }
    public function headingRow(): int
    {
        return 3;
    }
}