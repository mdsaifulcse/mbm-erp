<?php

namespace App\Imports;

use App\Models\PmsModels\Brand;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class BrandsImport implements ToModel, WithStartRow, WithHeadingRow,WithValidation
{   
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Brand([
            'code'  => $row['code'],
            'name'   => $row['name'],
            'created_at'=>date('Y-m-d h:i'),
        ]);

    }

    public function rules(): array
    {
        return [
            'code' => ['required','string','unique:brands'],
            'name' => ['required','string'],
        ];
    }

    public function startRow(): int
    {
        return 2;
    }
}
