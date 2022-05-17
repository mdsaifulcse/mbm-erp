<?php

namespace App\Repository\Hr;

use Illuminate\Support\Collection;
use App\Models\Employee;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\Leave;
use App\Models\Hr\Unit;

use DB;

class EmployeeRepository
{
    
	public function getEmployees($input, $date = null)
	{
		$date = $date??date('Y-m-d');

		return DB::table('hr_as_basic_info')
            ->whereIn('as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('as_location', auth()->user()->location_permissions())
            ->when(!empty($input['unit']), function ($query) use($input){
                if($input['unit'] == 145){
                    return $query->whereIn('as_unit_id',[1, 4, 5]);
                }else{
                    return $query->where('as_unit_id',$input['unit']);
                }
            })
            ->when(!empty($input['location']), function ($query) use($input){
               return $query->where('as_location',$input['location']);
            })
            ->when(!empty($input['area']), function ($query) use($input){
               return $query->where('as_area_id',$input['area']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('as_department_id',$input['department']);
            })
            ->when(!empty($input['line_id']), function ($query) use($input){
               return $query->where('as_line_id', $input['line_id']);
            })
            ->when(!empty($input['floor_id']), function ($query) use($input){
               return $query->where('as_floor_id',$input['floor_id']);
            })
            ->when($input['otnonot']!=null, function ($query) use($input){
               return $query->where('as_ot',$input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use($input){
               return $query->where('as_subsection_id', $input['subSection']);
            })
            ->when(!empty($input['selected']), function ($query) use($input){
                if($input['report_group'] != 'as_line_id' && $input['report_group'] != 'as_floor_id'){
                    if($input['selected'] == 'null'){
                        return $query->whereNull($input['report_group']);
                    }else{
                        return $query->where($input['report_group'], $input['selected']);
                    }
                }
            })
            ->where('as_doj' , '<=', $date)
            ->where(function($q) use ($date){
                $q->where(function($qa) use ($date){
                    $qa->where('as_status',1);
                });
                $q->orWhere(function($qa) use ($date){
                    $qa->whereIn('as_status',[2,3,4,5,6,7,8]);
                    $qa->where('as_status_date' , '>=', $date);
                });

            })
            ->orderBy('temp_id', 'ASC')
            ->get();
	}

    /*
     * Exclude Employees Based on Parameter
     * Accepted 'as_unit_id', 'as_location','as_area_id','as_section_id', 'as_subsection_id', 'as_floor_id', 'as_line_id'
     * @params $exclude = ['as_department_id' => 67, 'as_location' => [12,13]]
     */
    public function getEmployeeBy($input, $date = null, $select = '*')
    {
        $excludes = isset($input['excludes'])?$input['excludes']:[];
        $input['otnonot'] = isset($input['otnonot'])?$input['otnonot']:null;

        $date = $date??date('Y-m-d');
   
        return DB::table('hr_as_basic_info')
            ->select($select)
            ->whereIn('as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('as_location', auth()->user()->location_permissions())
            ->when(!empty($input['unit']), function ($q) use ($input){
                $q->whereIn('as_unit_id', $input['unit']);
            })
            ->when(!empty($input['location']), function ($q) use ($input){
                $q->whereIn('as_location',$input['location']);
            })
            ->when(!empty($input['area']), function ($q) use ($input){
                $q->where('as_area_id',$input['area']);
            })
            ->when(!empty($input['department']), function ($q) use ($input){
                $q->where('as_department_id',$input['department']);
            })
            ->when(!empty($input['line_id']), function ($q) use ($input){
                $q->where('as_line_id', $input['line_id']);
            })
            ->when(!empty($input['floor_id']), function ($q) use ($input){
                $q->where('as_floor_id',$input['floor_id']);
            })
            ->when($input['otnonot'] != null, function ($q) use ($input){
                $q->where('as_ot',$input['otnonot']);
            })
            ->when(!empty($input['section']), function ($q) use ($input){
                $q->where('as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($q) use ($input){
                $q->where('as_subsection_id', $input['subSection']);
            })
            /*
             * Excludes Operation for Employees
             */
            ->when(count($excludes) > 0, function($q) use ($excludes){
                foreach($excludes as $key => $value){
                    if(count($value) > 0){
                        $q->whereNotIn($key, $value);
                    }
                }
            })
            ->where('as_doj' , '<=', $date)
            ->where(function($q) use ($date){
                $q->where(function($qa){
                    $qa->where('as_status',1);
                });
                $q->orWhere(function($qa) use ($date){
                    $qa->whereIn('as_status',[2,3,4,5,6,7,8]);
                    $qa->where('as_status_date' , '>', $date);
                });

            })
            ->get();
    }

    public function getEmployeesByStatus($input)
    {
        $input['area']        = isset($input['area'])?$input['area']:'';
        $input['department']  = isset($input['department'])?$input['department']:'';
        $input['line_id']     = isset($input['line_id'])?$input['line_id']:'';
        $input['floor_id']    = isset($input['floor_id'])?$input['floor_id']:'';
        $input['section']     = isset($input['section'])?$input['section']:'';
        $input['subSection']  = isset($input['subSection'])?$input['subSection']:'';
        $input['otnonot']     = isset($input['otnonot'])?$input['otnonot']:'';
        $input['min_salary']  = (double)(isset($input['min_salary'])?$input['min_salary']:'');
        $input['max_salary']  = (double)(isset($input['max_salary'])?$input['max_salary']:'');

        $query = DB::table('hr_as_basic_info AS b')
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('b.as_location', auth()->user()->location_permissions())
            ->when(!empty($input['unit']), function ($query) use($input){
                if($input['unit'] == 145){
                    return $query->whereIn('b.as_unit_id',[1, 4, 5]);
                }else{
                    return $query->where('b.as_unit_id',$input['unit']);
                }
            })
            ->when(!empty($input['location']), function ($query) use($input){
               return $query->where('b.as_location',$input['location']);
            })
            ->when(!empty($input['area']), function ($query) use($input){
               return $query->where('b.as_area_id',$input['area']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('b.as_department_id',$input['department']);
            })
            ->when(!empty($input['line_id']), function ($query) use($input){
               return $query->where('b.as_line_id', $input['line_id']);
            })
            ->when(!empty($input['floor_id']), function ($query) use($input){
               return $query->where('b.as_floor_id',$input['floor_id']);
            })
            ->when($input['otnonot']!=null, function ($query) use($input){
               return $query->where('b.as_ot',$input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('b.as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use($input){
               return $query->where('b.as_subsection_id', $input['subSection']);
            })
            ->when(!empty($input['selected']), function ($query) use($input){
                if($input['report_group'] != 'as_line_id' && $input['report_group'] != 'as_floor_id'){
                    if($input['selected'] == 'null'){
                        return $query->whereNull('b.'.$input['report_group']);
                    }else{
                        return $query->where('b.'.$input['report_group'], $input['selected']);
                    }
                }
            })
            ->when(!empty($input['status']), function ($query) use($input){
               return $query->where('b.as_status',$input['status']);
            });
        if(!empty($input['max_salary']) && !empty($input['max_salary'])){
            $getBenefit = DB::table('hr_benefits');
            $getBenefitSql = $getBenefit->toSql();
            $query->leftjoin(DB::raw('(' . $getBenefitSql. ') AS ben'), function($join) use ($getBenefit) {
              $join->on('ben.ben_as_id', '=', 'b.associate_id')->addBinding($getBenefit->getBindings());
            })
            ->whereBetween('ben.ben_current_salary',array($input['min_salary'],$input['max_salary']));

        }
        return $query->orderBy('temp_id', 'ASC')->get();
    }

    public function getEmployeeByAssociateId($selected = null){
        $query = DB::table('hr_as_basic_info');
            //->whereIn('as_unit_id', auth()->user()->unit_permissions())
            //->whereIn('as_location', auth()->user()->location_permissions());
            if($selected != null){
                $query->select($selected);
            }
            return $query->get();
    }

    /* Employee filter */
    public function getEmployeeByFilter($input, $dataRow)
    {
        $input['unit'] = collect($input['unit'])->filter()->toArray();
        $input['location'] = collect($input['location'])->filter()->toArray();
        
        $collection = collect($dataRow)->whereNotIn('as_id', config('base.ignore_salary'))->sortByDesc('gross');
        if(isset($input['employee']) && $input['employee'] != null && $input['report_format'] == 0){
            $collection = collect($collection)->where('as_id', 'LIKE', '%'.$input['employee'] .'%');
        }

        if(isset($input['min_sal']) && $input['min_sal'] != null){
            $collection = collect($collection)->whereBetween('gross', [$input['min_sal'], $input['max_sal']]);
        }

        if(isset($input['unit']) && count($input['unit']) > 0){
            $collection = collect($collection)->whereIn('as_unit_id', $input['unit']);
        }

        if(isset($input['location']) && count($input['location']) > 0){
            $collection = collect($collection)->whereIn('as_location', $input['location']);
        }
        if(isset($input['pay_status']) && $input['pay_status'] != null){
            if($input['pay_status'] == 'cash'){
                $collection = collect($collection)->where('cash_payable', '>', 0);
            }elseif($input['pay_status'] != 'all'){
                $collection = collect($collection)->where('pay_type', $input['pay_status']);
            }
        }

        if(isset($input['area']) && $input['area'] != null){
            $collection = collect($collection)->where('as_area_id', $input['area']);
        }

        if(isset($input['department']) && $input['department'] != null){
            $collection = collect($collection)->where('as_department_id', $input['department']);
        }

        if(isset($input['section']) && $input['section'] != null){
            $collection = collect($collection)->where('as_section_id', $input['section']);
        }

        if(isset($input['subSection']) && $input['subSection'] != null){
            $collection = collect($collection)->where('as_subsection_id', $input['subSection']);
        }
        if(isset($input['designation']) && $input['designation'] != null){
            $collection = collect($collection)->where('as_designation_id', $input['designation']);
        }

        if(isset($input['otnonot']) && $input['otnonot'] != null){
            $collection = collect($collection)->where('ot_status', $input['otnonot']);
        }

        if(isset($input['floor_id']) && $input['floor_id'] != null){
            $collection = collect($collection)->where('as_floor_id', $input['floor_id']);
        }

        if(isset($input['line_id']) && $input['line_id'] != null){
            $collection = collect($collection)->where('as_line_id', $input['line_id']);
        }

        if(isset($input['selected'])){
            if($input['selected'] == 'null'){
                $collection = collect($collection)->whereNull($input['report_group']);
            }else{
                $collection = collect($collection)->where($input['report_group'], $input['selected']);
            }
        }

        return $collection;
    }
    
    public function getEmployeeByFilterAll($input, $dataRow)
    {
        $input['unit'] = collect($input['unit'])->filter()->toArray();
        $input['location'] = collect($input['location'])->filter()->toArray();
        
        $collection = collect($dataRow);
        if(isset($input['employee']) && $input['employee'] != null && $input['report_format'] == 0){
            $collection = collect($collection)->where('as_id', 'LIKE', '%'.$input['employee'] .'%');
        }

        if(isset($input['min_sal']) && $input['min_sal'] != null){
            $collection = collect($collection)->whereBetween('gross', [$input['min_sal'], $input['max_sal']]);
        }

        if(isset($input['unit']) && count($input['unit']) > 0){
            $collection = collect($collection)->whereIn('as_unit_id', $input['unit']);
        }

        if(isset($input['location']) && count($input['location']) > 0){
            $collection = collect($collection)->whereIn('as_location', $input['location']);
        }
        if(isset($input['pay_status']) && $input['pay_status'] != null){
            if($input['pay_status'] == 'cash'){
                $collection = collect($collection)->where('cash_payable', '>', 0);
            }elseif($input['pay_status'] != 'all'){
                $collection = collect($collection)->where('pay_type', $input['pay_status']);
            }
        }

        if(isset($input['area']) && $input['area'] != null){
            $collection = collect($collection)->where('as_area_id', $input['area']);
        }

        if(isset($input['department']) && $input['department'] != null){
            $collection = collect($collection)->where('as_department_id', $input['department']);
        }

        if(isset($input['section']) && $input['section'] != null){
            $collection = collect($collection)->where('as_section_id', $input['section']);
        }

        if(isset($input['subSection']) && $input['subSection'] != null){
            $collection = collect($collection)->where('as_subsection_id', $input['subSection']);
        }
        if(isset($input['designation']) && $input['designation'] != null){
            $collection = collect($collection)->where('as_designation_id', $input['designation']);
        }

        if(isset($input['otnonot']) && $input['otnonot'] != null){
            $collection = collect($collection)->where('ot_status', $input['otnonot']);
        }

        if(isset($input['floor_id']) && $input['floor_id'] != null){
            $collection = collect($collection)->where('as_floor_id', $input['floor_id']);
        }

        if(isset($input['line_id']) && $input['line_id'] != null){
            $collection = collect($collection)->where('as_line_id', $input['line_id']);
        }

        if(isset($input['min_len']) && $input['min_len'] != null){
            $collection = collect($collection)->where('service_length', '>=', $input['min_len']);
        }

        if(isset($input['max_len']) && $input['max_len'] != null){
            $collection = collect($collection)->where('service_length', '<=', $input['max_len']);
        }
        
        if(isset($input['from_doj']) && $input['from_doj'] != null){
            $collection = collect($collection)->where('as_doj', '>=', $input['from_doj']);
        }

        if(isset($input['to_doj']) && $input['to_doj'] != null){
            $collection = collect($collection)->where('as_doj', '<=', $input['to_doj']);
        }
        
        if(isset($input['from_grade']) && $input['from_grade'] != null){
            $collection = collect($collection)->where('grade_sequence', '>=', $input['from_grade']);
        }

        if(isset($input['to_grade']) && $input['to_grade'] != null){
            $collection = collect($collection)->where('grade_sequence', '<=', $input['to_grade']);
        }

        if(isset($input['selected'])){
            if($input['selected'] == 'null'){
                $collection = collect($collection)->whereNull($input['report_group']);
            }else{
                $collection = collect($collection)->where($input['report_group'], $input['selected']);
            }
        }

        return $collection;
    }

    public function getEmployeeByFilterBonus($input, $dataRow)
    {
        $input['unit'] = collect($input['unit'])->filter()->toArray();
        $input['location'] = collect($input['location'])->filter()->toArray();
        
        $collection = collect($dataRow)->whereNotIn('as_id', config('base.ignore_salary'))->sortByDesc('gross');
        if(isset($input['employee']) && $input['employee'] != null && $input['report_format'] == 0){
            $collection = collect($collection)->where('as_id', 'LIKE', '%'.$input['employee'] .'%');
        }

        if(isset($input['min_sal']) && $input['min_sal'] != null){
            $collection = collect($collection)->whereBetween('gross', [$input['min_sal'], $input['max_sal']]);
        }

        if(isset($input['unit']) && count($input['unit']) > 0){
            $collection = collect($collection)->whereIn('as_unit_id', $input['unit']);
        }

        if(isset($input['location']) && count($input['location']) > 0){
            $collection = collect($collection)->whereIn('as_location', $input['location']);
        }

        if(isset($input['area']) && $input['area'] != null){
            $collection = collect($collection)->where('as_area_id', $input['area']);
        }

        if(isset($input['department']) && $input['department'] != null){
            $collection = collect($collection)->where('as_department_id', $input['department']);
        }

        if(isset($input['section']) && $input['section'] != null){
            $collection = collect($collection)->where('as_section_id', $input['section']);
        }

        if(isset($input['subSection']) && $input['subSection'] != null){
            $collection = collect($collection)->where('as_subsection_id', $input['subSection']);
        }
        if(isset($input['designation']) && $input['designation'] != null){
            $collection = collect($collection)->where('as_designation_id', $input['designation']);
        }

        if(isset($input['otnonot']) && $input['otnonot'] != null){
            $collection = collect($collection)->where('ot_status', $input['otnonot']);
        }

        if(isset($input['floor_id']) && $input['floor_id'] != null){
            $collection = collect($collection)->where('as_floor_id', $input['floor_id']);
        }

        if(isset($input['line_id']) && $input['line_id'] != null){
            $collection = collect($collection)->where('as_line_id', $input['line_id']);
        }

        if(isset($input['selected'])){
            if($input['selected'] == 'null'){
                $collection = collect($collection)->whereNull($input['report_group']);
            }else{
                $collection = collect($collection)->where($input['report_group'], $input['selected']);
            }
        }

        return $collection;
    }

    public function QueryBuild($input, $date = null)
    {
        $date = $date??date('Y-m-d');

        return DB::table('hr_as_basic_info')
            ->whereIn('as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('as_location', auth()->user()->location_permissions())
            ->when(!empty($input['unit']), function ($query) use($input){
                return $query->whereIn('as_unit_id', $input['unit']);
            })
            ->when(!empty($input['location']), function ($query) use($input){
               return $query->whereIn('as_location', $input['location']);
            })
            ->when(!empty($input['area']), function ($query) use($input){
               return $query->where('as_area_id',$input['area']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('as_department_id',$input['department']);
            })
            ->when(!empty($input['line_id']), function ($query) use($input){
               return $query->where('as_line_id', $input['line_id']);
            })
            ->when(!empty($input['floor_id']), function ($query) use($input){
               return $query->where('as_floor_id',$input['floor_id']);
            })
            ->when($input['otnonot']!=null, function ($query) use($input){
               return $query->where('as_ot',$input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use($input){
               return $query->where('as_subsection_id', $input['subSection']);
            })
            ->when(!empty($input['selected']), function ($query) use($input){
                if($input['report_group'] != 'as_line_id' && $input['report_group'] != 'as_floor_id'){
                    if($input['selected'] == 'null'){
                        return $query->whereNull($input['report_group']);
                    }else{
                        return $query->where($input['report_group'], $input['selected']);
                    }
                }
            })
            ->where('as_doj' , '<=', $date)
            ->where(function($q) use ($date){
                $q->where(function($qa) use ($date){
                    $qa->where('as_status',1);
                });
                $q->orWhere(function($qa) use ($date){
                    $qa->whereIn('as_status',[2,3,4,5,6,7,8]);
                    $qa->where('as_status_date' , '>=', $date);
                });

            });
    }


    
}
