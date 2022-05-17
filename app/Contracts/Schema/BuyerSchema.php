<?php
namespace App\Contracts\Schema;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Carbon\Carbon;

class SchemaCreate
{
   protected  $table;


   /*
        ['name' => 'as_id', 'type' => 'integer'],
        ['name' => 'year', 'type' => 'integer'],
        ['name' => 'month', 'type' => 'string', 'length' => [2]],
        ['name' => 'gross', 'type' => 'float', 'deafult' => null],
        ['name' => 'basic', 'type' => 'float', 'deafult' => null],
        ['name' => 'house', 'type' => 'float', 'deafult' => null],
        ['name' => 'medical', 'type' => 'float', 'deafult' => null],
        ['name' => 'transport', 'type' => 'float', 'deafult' => null],
        ['name' => 'food', 'type' => 'float', 'deafult' => null],
        ['name' => 'late_count', 'type' => 'tinyInteger', 'null' => 1, 'deafult' => null],
        ['name' => 'present', 'type' => 'tinyInteger', 'null' => 1, 'deafult' => null],
        ['name' => 'holiday', 'type' => 'tinyInteger', 'null' => 1, 'deafult' => null],
        ['name' => 'absent', 'type' => 'tinyInteger', 'null' => 1, 'deafult' => null],
        ['name' => 'leave', 'type' => 'tinyInteger', 'null' => 1, 'deafult' => null],
        ['name' => 'absent_deduct', 'type' => 'float', 'deafult' => null],
        ['name' => 'half_day_deduct', 'type' => 'float', 'deafult' => null],
        ['name' => 'adv_deduct', 'type' => 'float', 'deafult' => null],
        ['name' => 'cg_deduct', 'type' => 'float', 'deafult' => null],
        ['name' => 'food_deduct', 'type' => 'float', 'deafult' => null],
        ['name' => 'others_deduct', 'type' => 'float', 'deafult' => null],
        ['name' => 'salary_add', 'type' => 'float', 'deafult' => null],
        ['name' => 'bonus_add', 'type' => 'float', 'deafult' => null],
        ['name' => 'leave_adjust', 'type' => 'float', 'deafult' => null],
        ['name' => 'ot_rate', 'type' => 'float', 'deafult' => null],
        ['name' => 'ot_hour', 'type' => 'float', 'length' => [8, 3], 'null' => 1, 'deafult' => null],
        ['name' => 'attendance_bonus', 'type' => 'float', 'deafult' => null],
        ['name' => 'production_bonus', 'type' => 'float', 'deafult' => null],
        ['name' => 'stamp', 'type' => 'float', 'deafult' => null],
        ['name' => 'salary_payable', 'type' => 'float', 'deafult' => null],
        ['name' => 'total_payable', 'type' => 'float', 'deafult' => null],
        ['name' => 'bank_payable', 'type' => 'float', 'deafult' => null],
        ['name' => 'cash_payable', 'type' => 'float', 'deafult' => null],
        ['name' => 'tds', 'type' => 'float', 'deafult' => null],
        ['name' => 'pay_status', 'type' => 'tinyInteger', 'null' => 1, 'deafult' => null],
        ['name' => 'pay_type', 'type' => 'char', 'length' => [10], 'null' => 1, 'deafult' => null],
        ['name' => 'emp_status', 'type' => 'tinyInteger', 'null' => 1, 'deafult' => null],
        ['name' => 'unit_id', 'type' => 'integer', 'null' => 1, 'deafult' => null],
        ['name' => 'designation_id', 'type' => 'integer', 'null' => 1, 'deafult' => null],
        ['name' => 'subsection_id', 'type' => 'integer', 'null' => 1, 'deafult' => null],
        ['name' => 'location_id', 'type' => 'integer', 'null' => 1, 'deafult' => null],
        ['name' => 'ot_status', 'type' => 'tinyInteger', 'null' => 1, 'deafult' => null],
        ['name' => 'created_by', 'type' => 'integer', 'null' => 1, 'deafult' => null]
   */

   public function table($table)
   {
      $this->table = $table;
      return $this;
   }

   public function salary()
   {
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('as_id');
                $table->integer('year', 4);
                $table->string('month', 2);
                $table->float('gross')->unsigned()->nullable();
                $table->float('basic')->unsigned()->nullable();
                $table->float('house')->unsigned()->nullable();
                $table->float('medical')->unsigned()->nullable();
                $table->float('transport')->unsigned()->nullable();
                $table->float('food')->unsigned()->nullable();
                $table->integer('late_count', 2)->unsigned()->nullable();
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            });
        }
    }
}