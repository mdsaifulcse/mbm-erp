<?php

namespace App\Http\Controllers\Pms\Import;

use App\Http\Controllers\Controller;
use App\Imports\SupplierImport;
use App\Models\PmsModels\Suppliers;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SupplierUploadController extends Controller
{
    public function showSupplierImportForm(){

        $title='Import Suppliers Information';
        return view('pms.backend.pages.suppliers.excel-import',compact('title'));
    }

    public function storeSupplierData(Request $request){

        $this->validate($request, [
            'supplier_file' => 'required|mimes:xls,xlsx'
        ]);

        $path = $request->file('supplier_file')->getRealPath();

        try {
            Excel::import(new SupplierImport(), $path);

            return $this->backWithSuccess('Excel Data Imported successfully.');

        }catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

            $errorMessage='';
            $rowNumber=1;
            $rowNumber+=$e->failures()[0]->row();
            $column=$e->failures()[0]->attribute();

            $errorMessage.=$e->failures()[0]->errors()[0].' for row '.$rowNumber.' on Column '.$column;

            return $this->backWithError($errorMessage);
        }

    }

}
