<?php

namespace App\Imports;

use App\Models\PmsModels\Suppliers;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SupplierImport implements ToModel, WithStartRow,WithHeadingRow,WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Suppliers([
            'name'  => $row['name'],
            'email'   => $row['email'],
            'mobile_no'   => $row['mobile_no'],
            'phone'   => $row['phone'],
            'address'   => $row['address'],
            'city'   => $row['city'],
            'state'   => $row['state'],
            'country'   => $row['country'],
            'zipcode'   => $row['zipcode'],
            'created_at'=>date('Y-m-d h:i'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:200'],
            'email' => ['required','string','unique:suppliers'],
            'mobile_no' => ['required','unique:suppliers'],
            'phone' => ['required','unique:suppliers'],
            'address' => ['required','string','max:250'],
            'city' => ['required','string','max:200'],
            'state' => ['required','string','max:200'],
            'zipcode' => ['required','max:100'],
            'country' => ['required','string','max:200'],
        ];
    }

    public function startRow(): int
    {
        return 2;
    }
}
