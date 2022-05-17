<?php

namespace App\Exports\Hr;

use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BillExport implements FromView, WithHeadingRow
{
	use Exportable;

    public function __construct($data, $input, $type='')
    {
        $this->data  = $data;
        $this->input = $input;
        $this->type  = $type;
    }
    
    public function view(): View
    {
    	if($this->type == 'operation'){
        	$getBillList = $this->data;
        	$input = $this->input;
            return view('hr.operation.bill.excel', compact('getBillList', 'input'));
        }else{
            $fields = $this->data;
            return view('hr.reports.bill.excel', $fields);
        }
    }
    public function headingRow(): int
    {
        return 3;
    }
}