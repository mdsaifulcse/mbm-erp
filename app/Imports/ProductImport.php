<?php

namespace App\Imports;

use App\Models\PmsModels\Product;
use App\Models\PmsModels\Brand;
use App\Models\PmsModels\Category;
use App\Models\PmsModels\Suppliers;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductImport implements ToCollection, WithStartRow, WithHeadingRow,WithValidation
{   
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {      
       
        foreach ($rows as $values) 
        {
            $category_id=Category::where('code',$values['category_code'])->first(['id']);
            $brand_id=Brand::where('code',$values['brand_code'])->first(['id']);
            $suppliers=explode(',', $values['supplier_mobile']);

            $supplier_array=[];
            foreach($suppliers as $phone){
                $supplier=Suppliers::where('mobile_no',$phone)->first(['id']);
                if (!empty($supplier)) {
                   array_push($supplier_array,$supplier->id); 
                }
            }

            if(isset($category_id) && isset($brand_id)){

                $product = Product::create([
                    'category_id'=>$category_id->id,
                    'brand_id'=>$brand_id->id,
                    'name'=>$values['name'],
                    'tax'=>$values['tax'],
                    'unit'=>$values['unit_price'],
                    'created_at'=>date('Y-m-d h:i'),
                ]);
                $product->suppliers()->sync($supplier_array);
            }
        }
    }

    public function startRow(): int
    {
        return 2;
    }

    public function rules(): array
    {
        return [
            'category_code' => ['required', 'string','max:255'],
            'brand_code' => ['required','max:255'],
            'name' => ['required', 'string', 'max:255'],
            'tax' => ['required', 'numeric'],
            'unit_price' => ['required', 'numeric'],
            'supplier_mobile' => ['required','string'],
        ];
    }
}
