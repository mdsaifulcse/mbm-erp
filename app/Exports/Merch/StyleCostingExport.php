<?php

namespace App\Exports\Merch;

use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use  Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StyleCostingExport implements FromView , WithHeadingRow,ShouldAutoSize
{
    public function __construct($data)
    {
        $this->data = $data;
        
    }
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $fields = $this->data;

        
       //dd($fields);
    	
        
            return view('merch.style_costing.excel',$fields);
        
    }
    public function headingRow(): int
    {
        return 10;
    }
}
