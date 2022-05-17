<?php

namespace App\Exports\Merch;

use Maatwebsite\Excel\Concerns\FromCollection;

// class SampleExport implements FromCollection
// {
//     *
//     * @return \Illuminate\Support\Collection
    
//     public function collection()
//     {
//         // FromView
//     }
// }

use DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
 
class SampleExport implements FromView , WithHeadingRow,ShouldAutoSize
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
 
        
        // dd($fields);
        
        
            return view('merch.sample.samplerequisition_downlod',$fields);
        
    }
    public function headingRow(): int
    {
        return 10;
    }
    
}
