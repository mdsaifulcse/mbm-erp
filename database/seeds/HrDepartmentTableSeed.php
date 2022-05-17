<?php

use App\Models\PmsModels\HrDepartment;
use Illuminate\Database\Seeder;

class HrDepartmentTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            [
                'hr_department_name' => 'salse',
                'hr_department_code' => 123,
                'hr_department_min_range' => 20000,
                'hr_department_max_range' => 50000,
            ],
            [
                'hr_department_name' => 'ISD',
                'hr_department_code' => 456,
                'hr_department_min_range' => 20000,
                'hr_department_max_range' => 50000,
            ],
            [
                'hr_department_name' => 'Finance & Accounts',
                'hr_department_code' => 789,
                'hr_department_min_range' => 50000,
                'hr_department_max_range' => 100000,
            ],
            [
                'hr_department_name' => 'HR',
                'hr_department_code' => '098',
                'hr_department_min_range' => 50000,
                'hr_department_max_range' => 100000,
            ],
            [
                'hr_department_name' => 'IT',
                'hr_department_code' => '765',
                'hr_department_min_range' => 50000,
                'hr_department_max_range' => 100000,
                'hr_department_status' => 1,
            ],
            [
                'hr_department_name' => 'MBD',
                'hr_department_code' => '432',
                'hr_department_min_range' => 50000,
                'hr_department_max_range' => 100000,
            ],
            [
                'hr_department_name' => 'Administration',
                'hr_department_code' => '108',
                'hr_department_min_range' => 50000,
                'hr_department_max_range' => 100000,
            ],
            [
                'hr_department_name' => 'Managing director',
                'hr_department_code' => '107',
                'hr_department_min_range' => 50000,
                'hr_department_max_range' => 100000,
            ],
            [
                'hr_department_name' => 'International Business',
                'hr_department_code' => '106',
                'hr_department_min_range' => 50000,
                'hr_department_max_range' => 100000,
            ],
        ];
        foreach ( $array as $index => $item) {
            HrDepartment::create($item);
        }
    }
}
