<?php

namespace App\Imports;

use App\Models\PmsModels\Category;
use App\Models\PmsModels\CategoryDepartment;
use App\Models\PmsModels\HrDepartment;
use App\Models\PmsModels\RequisitionType;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class CategoryImport implements ToCollection, WithStartRow, WithHeadingRow,WithValidation
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function Collection(Collection $rows)
    {
        foreach ($rows as $values)
        {
            $departmentNames=explode(',', $values['hr_department_name']);

            $requisitionType=RequisitionType::where('name',$values['requisition_type'])->first(['id']);

            $parentCategory=Category::where('code',$values['parent_category_code'])->first(['id']);

            $category='';
            if (!empty($requisitionType)){
                $category = Category::create([
                    'code'=>$values['code'],
                    'name'=>$values['name'],
                    'parent_id'=>$parentCategory??'NULL',
                    'requisition_type_id'=>$requisitionType->id,
                    'created_at'=>date('Y-m-d h:i'),
                ]);
            }

            foreach($departmentNames as $departmentName){
                $department=HrDepartment::where(['hr_department_name'=>$departmentName])->first(['hr_department_id']);

                if (!empty($department) && !empty($category)) {

                    CategoryDepartment::create([
                        'category_id'=>$category->id,
                        'hr_department_id'=>$department->hr_department_id,
                        'created_at'=>date('Y-m-d h:i'),
                    ]);
                }
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
            'code' => ['required', 'string','max:255','unique:categories'],
            'name' => ['required','max:255'],
            //'parent_id' => ['required', 'max:255'],
            'requisition_type' => ['required'],
            'hr_department_code' => ['required'],
            'hr_department_name' => ['required','string'],

        ];
    }
}
