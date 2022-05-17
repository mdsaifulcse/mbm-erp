<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Contracts\Hr\EmployeeInterface;
use App\Exports\Hr\DailyReportExport;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\Section;
use App\Models\Hr\Absent;
use App\Models\Hr\Benefits;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Location;
use App\Models\Hr\Unit;
use App\Repository\Hr\EmployeeRepository;
use Box\Spout\Writer\Style\StyleBuilder;
use DB;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

// use Absent;

class DailyActivityReportController extends Controller
{
    protected $employee;

    protected $globalSelection = [
        'emp.as_id',
        'emp.as_gender',
        'emp.as_shift_id',
        'emp.as_oracle_code',
        'emp.associate_id',
        'emp.as_line_id',
        'emp.as_unit_id',
        'emp.as_location',
        'emp.as_designation_id',
        'emp.as_department_id',
        'emp.as_floor_id',
        'emp.as_pic',
        'emp.as_name',
        'emp.as_contact',
        'emp.as_section_id',
        'emp.as_subsection_id'
    ];

    protected $views = [
        'att_statistics'    => 'att_statistics',
        'employee'          => 'employee',
        'present'           => 'late_report',
        'absent'            => 'absent_report',
        'shift'             => 'assigned_shift',
        'before_absent_after_present'=> 'before_after_report',
        'in_out_missing'    => 'in_out_mis_report',
        'leave'             => 'leave_report',
        'special_ot'        => 'special_ot',
        'working_hour'      => 'working_hour_report',
        'executive_attendance' => '',
        'two_day_att'       => 'two_day_att',
        'late'              => 'late_report',
        'missing_token'     => '',
        'ot'                => 'ot_report'
    ];

    public function __construct(EmployeeRepository $employee)
    {
        $this->employee = $employee;
        ini_set('zlib.output_compression', 1);
    }

    public function beforeAfterStatus()
    {
        $unitList  = Unit::where('hr_unit_status', '1')
        ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
        ->pluck('hr_unit_name', 'hr_unit_id');
        $areaList  = DB::table('hr_area')->where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
        return view('hr/reports/daily_activity/before_after_status', compact('unitList', 'areaList'));
    }

    public function beforeAfterReport(Request $request)
    {
        $input = $request->all();

        try {
            $areaid       = isset($request['area']) ? $request['area'] : '';
            $otnonot      = isset($request['otnonot']) ? $request['otnonot'] : '';
            $departmentid = isset($request['department']) ? $request['department'] : '';
            $lineid       = isset($request['line_id']) ? $request['line_id'] : '';
            $florid       = isset($request['floor_id']) ? $request['floor_id'] : '';
            $section      = isset($request['section']) ? $request['section'] : '';
            $subSection   = isset($request['subSection']) ? $request['subSection'] : '';
            $absentDate   = $request['absent_date'];
            $presentDate   = $request['present_date'];

            // employee basic sql binding
            $employeeData = DB::table('hr_as_basic_info');
            $employeeData_sql = $employeeData->toSql();

            $queryData = Absent::select('emp.as_id')
            ->where('hr_unit', $request['unit'])
            ->where('date', $request['absent_date'])
            ->when(!empty($areaid), function ($query) use ($areaid) {
                return $query->where('emp.as_area_id', $areaid);
            })
            ->when(!empty($departmentid), function ($query) use ($departmentid) {
                return $query->where('emp.as_department_id', $departmentid);
            })
            ->when(!empty($lineid), function ($query) use ($lineid) {
                return $query->where('emp.as_line_id', $lineid);
            })
            ->when(!empty($florid), function ($query) use ($florid) {
                return $query->where('emp.as_floor_id', $florid);
            })
            ->when($request['otnonot']!=null, function ($query) use ($otnonot) {
                return $query->where('emp.as_ot', $otnonot);
            })
            ->when(!empty($section), function ($query) use ($section) {
                return $query->where('emp.as_section_id', $section);
            })
            ->when(!empty($subSection), function ($query) use ($subSection) {
                return $query->where('emp.as_subsection_id', $subSection);
            });
            $queryData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function ($join) use ($employeeData) {
                $join->on('emp.associate_id', 'hr_absent.associate_id')->addBinding($employeeData->getBindings());
            });

            $absentData = $queryData->pluck('emp.as_id')->toArray();
            $getEmployee = array();
            $format = $request['report_group'];
            $uniqueGroups = ['all'];
            if (count($absentData) > 0) {
                $tableName = get_att_table($request['unit']).' AS a';
                $attData = DB::table($tableName)
                    ->where('emp.as_unit_id', $request['unit'])
                    ->where('a.in_date', $request['present_date'])
                    ->whereIn('a.as_id', $absentData)
                    ->when(!empty($areaid), function ($query) use ($areaid) {
                        return $query->where('emp.as_area_id', $areaid);
                    })
                    ->when(!empty($departmentid), function ($query) use ($departmentid) {
                        return $query->where('emp.as_department_id', $departmentid);
                    })
                    ->when(!empty($lineid), function ($query) use ($lineid) {
                        return $query->where('emp.as_line_id', $lineid);
                    })
                    ->when(!empty($florid), function ($query) use ($florid) {
                        return $query->where('emp.as_floor_id', $florid);
                    })
                    ->when($request['otnonot']!=null, function ($query) use ($otnonot) {
                        return $query->where('emp.as_ot', $otnonot);
                    })
                    ->when(!empty($section), function ($query) use ($section) {
                        return $query->where('emp.as_section_id', $section);
                    })
                    ->when(!empty($subSection), function ($query) use ($subSection) {
                        return $query->where('emp.as_subsection_id', $subSection);
                    });
                $attData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function ($join) use ($employeeData) {
                    $join->on('a.as_id', '=', 'emp.as_id')->addBinding($employeeData->getBindings());
                });
                if ($input['report_format'] == 1 && $input['report_group'] != null) {
                    $attData->select('emp.'.$input['report_group'], DB::raw('count(*) as total'))->groupBy('emp.'.$input['report_group']);
                } else {
                    $attData->select('emp.as_id', 'emp.as_gender', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id');
                }
                $getEmployee = $attData->get();
                if ($format != null && count($getEmployee) > 0 && $input['report_format'] == 0) {
                    $getEmployeeArray = $getEmployee->toArray();
                    $formatBy = array_column($getEmployeeArray, $request['report_group']);
                    $uniqueGroups = array_unique($formatBy);
                }
            }

            return view('hr.reports.daily_activity.before_after_report', compact('uniqueGroups', 'format', 'getEmployee', 'input'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            // return $bug;
            return 'error';
        }
    }
    public function employeeActivity()
    {
        return view('hr.reports.yearly_activity.employee_wise_activity');
    }
    public function employeeActivityReport(Request $request)
    {
        $input = $request->all();
        try {
            if ($input['as_id'] == null) {
                return 'error';
            }
            if (isset($input['year']) && $input['year'] != null) {
                $year = $input['year'];
            } else {
                $year = date('Y');
            }
            $employee = Employee::getEmployeeAssociateIdWise($input['as_id']);
            // get yearly report
            $getData = HrMonthlySalary::getYearlyActivityMonthWise($input['as_id'], $year);
            return view('hr.reports.yearly_activity.employee_activity_result', compact('getData', 'employee', 'year'));
        } catch (\Exception $e) {
            return 'error';
        }
    }

    public function employeeActivityReportModal(Request $request)
    {
        $data['type'] = 'error';
        $input = $request->all();
        try {
            if ($input['as_id'] == null) {
                $data['message'] = 'Employee Id Not Found!';
                return $data;
            }
            if (isset($input['year']) && $input['year'] != null) {
                $year = $input['year'];
            } else {
                $year = date('Y');
            }
            // get yearly report
            $getData = HrMonthlySalary::getYearlyActivityMonthWise($input['as_id'], $year);
            $activity = '';
            if (count($getData) == 0) {
                $activity.= '<tr>';
                $activity.= '<td colspan="5" class="text-center"> No Data Found! </td>';
                $activity.= '</tr>';
            } else {
                foreach ($getData as $el) {
                    $otHourEx = explode('.', $el->ot_hour);
                    $minute = '00';
                    if (isset($otHourEx[1])) {
                        $minute = $otHourEx[1];
                        if ($minute == 5) {
                            $minute = 30;
                        }
                    }
                    $otHour = $otHourEx[0].'.'.$minute;
                    $activity.= '<tr>';
                    $activity.='<td>'.date("F", mktime(0, 0, 0, $el->month, 1)).'</td>';
                    $activity.='<td>'.$el->absent.'</td>';
                    $activity.='<td>'.$el->late_count.'</td>';
                    $activity.='<td>'.$el->leave.'</td>';
                    $activity.='<td>'.$el->holiday.'</td>';
                    $activity.='<td>'.$otHour.'</td>';
                    $activity.= '</tr>';
                }
            }
            $data['value'] = $activity;
            $data['type'] = 'success';
            return $data;
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();
            return $data;
        }
    }

    public function attendance()
    {
        $data['unitList']  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->orderBy('hr_unit_name', 'desc')
            ->pluck('hr_unit_name', 'hr_unit_id');

        $data['locationList']  = Location::where('hr_location_status', '1')
            ->whereIn('hr_location_id', auth()->user()->location_permissions())
            ->orderBy('hr_location_name', 'desc')
            ->pluck('hr_location_name', 'hr_location_id');

        $data['salaryMin'] = get_salary_min();
        $data['salaryMax'] = get_salary_max();
        $data['areaList']  = DB::table('hr_area')->where('hr_area_status', '1')
            ->pluck('hr_area_name', 'hr_area_id');

        $data['reportType'] = $this->getReportType();

        return view('hr.reports.daily_activity.attendance.index', $data);
    }

    /*
     * Fetch global report type for daily report selection
     */

    protected function getReportType()
    {
        $reportType = [];
        if (auth()->user()->hasRole('Planning')) {
            $reportType = [
                'att_statistics'=>'Attendance Statistics',
                'absent'=>'Absent',
                'ot'=>'Overtime(OT)',
                'working_hour'=>'Working Hour'
            ];
        } else {
            if (auth()->user()->can('Attendance Report')) {
                $reportType = [
                    //'att_statistics'=>'Attendance Statistics',
                    'employee' => 'Employee',
                    'present'=>'Present',
                    'absent'=>'Absent',
                    'shift'=>'Assigned Shift',
                    'before_absent_after_present'=>'Present After Being Absent',
                    'in_out_missing'=>'In/Out Missing',
                    'in_missing'=>'In Missing',
                    'out_missing'=>'Out Missing',
                    'leave'=>'Leave'
                ];
            }
            $reportType['ot'] = 'Overtime(OT)';
            $reportType['special_ot'] = 'Special OT';
            $reportType['working_hour'] = 'Working Hour';
            $reportType['executive_attendance'] = 'Executive Attendance';
            $reportType['management_attendance'] = 'Management Attendance';

            if (auth()->user()->can('Attendance Report') || auth()->user()->can('Attendance Upload')) {
                $reportType['late'] = 'Late';
                $reportType['missing_token'] = 'Punch Missing Token';
                $reportType['two_day_att'] = 'Two Day Attendance';
            }
        }

        return $reportType;
    }

    /*
     * Fetch global report views for daily report selection
     */


    protected function getReportView($report)
    {
        return $this->views[$report]??'';
    }

    /*
     * return report
     */


    protected function cookDailyReport()
    {
        $data['unit']           = unit_by_id();
        $data['location']       = location_by_id();
        $data['line']           = line_by_id();
        $data['floor']          = floor_by_id();
        $data['department']     = department_by_id();
        $data['designation']    = designation_by_id();
        $data['section']        = section_by_id();
        $data['subSection']     = subSection_by_id();
        $data['area']           = area_by_id();

        $data['uniqueGroupEmp'] = [];
        $data['format']         = $request['report_group'];
    }

    public function attendanceReport(Request $request)
    {
        $input = $request->all();

        try {
            ini_set('zlib.output_compression', 1);

            $input['area']       = isset($request['area']) ? $request['area'] : '';
            $input['otnonot']    = isset($request['otnonot']) ? $request['otnonot'] : '';
            $input['department'] = isset($request['department']) ? $request['department'] : '';
            $input['line_id']    = isset($request['line_id']) ? $request['line_id'] : '';
            $input['floor_id']   = isset($request['floor_id']) ? $request['floor_id'] : '';
            $input['section']    = isset($request['section']) ? $request['section'] : '';
            $input['subSection'] = isset($request['subSection']) ? $request['subSection'] : '';
            $input['location']   = isset($request['location']) ? $request['location'] : '';


            if ($input['report_type'] == 'missing_token') {
                return $this->getatttoken($input, $request);
            }

            if ($input['report_type'] == 'two_day_att' || $input['report_type'] == 'executive_attendance') {
                return $this->twoDayAtt($input, $request);
            }

            if ($input['report_type'] == 'att_statistics') {
                return $this->attStatistics($input, $request);
            }

            if ($input['report_type'] == 'shift') {
                return $this->getShiftEmployee($input, $request);
            }

            if ($input['report_type'] == 'special_ot') {
                return $this->getSpecialOt($input, $request);
            }

            if ($input['report_type'] == 'employee' || $input['report_type'] == 'present') {
                return $this->getReports($input, $request);
            }

            $unit = unit_by_id();
            $location = location_by_id();
            $line = line_by_id();
            $floor = floor_by_id();
            $department = department_by_id();
            $designation = designation_by_id();
            $section = section_by_id();
            $subSection = subSection_by_id();
            $area = area_by_id();
            $uniqueGroupEmp = [];

            $getEmployee = array();
            $data = array();
            $format = $request['report_group'];
            $uniqueGroups = ['all'];
            $totalValue = 0;

            // employee basic sql binding
            $employeeData = DB::table('hr_as_basic_info');
            $employeeData_sql = $employeeData->toSql();
            // shift
            if ($input['report_type'] == 'working_hour') {
                $shiftData = DB::table('hr_shift');
                $shiftDataSql = $shiftData->toSql();
            }

            $tableName = get_att_table($request['unit']).' AS a';

            if ($input['report_type'] == 'ot' || $input['report_type'] == 'present' || $input['report_type'] == 'working_hour' || $input['report_type'] == 'late' || $input['report_type'] == 'in_out_missing' || $input['report_type'] == 'in_missing' || $input['report_type'] == 'out_missing') {
                $attData = DB::table($tableName)
                ->where('a.in_date', $request['date']);
                if ($input['report_type'] == 'late') {
                    $attData->where('a.late_status', 1);
                }
                if ($input['report_type'] == 'in_out_missing') {
                    $attData->where(function ($q) use ($request) {
                        $q->whereNull('a.in_time');
                        if ($request['date'] != date('Y-m-d')) {
                            $q->orWhereNull('a.out_time');
                        }
                        $q->orWhere('a.remarks', 'DSI');
                    });
                }
                if ($input['report_type'] == 'in_missing') {
                    $attData->where(function ($q) use ($request) {
                        $q->whereNull('a.in_time');
                        $q->orWhere('a.remarks', 'DSI');
                    });
                }
                if ($input['report_type'] == 'out_missing') {
                    $attData->where(function ($q) use ($request) {
                        $q->whereNull('a.out_time');
                    });
                }
            } elseif ($input['report_type'] == 'absent') {
                $attData = DB::table('hr_absent AS a')
                ->where('a.date', $request['date']);
            } elseif ($input['report_type'] == 'leave') {
                $attData = DB::table('hr_leave AS l')
                ->whereRaw('? between leave_from and leave_to', [$request['date']])
                ->where('l.leave_status', 1);
            } elseif ($input['report_type'] == 'before_absent_after_present') {
                $absentData = $this->getAbsentEmployeeFromDate($input);
                $attData = DB::table($tableName)
                ->where('a.in_date', $request['present_date'])
                ->whereIn('a.as_id', $absentData);
            } elseif ($input['report_type'] == 'employee') {
                $emp_day = $request['date'];
                $attData = DB::table('hr_as_basic_info as emp');
                if ($emp_day == date('Y-m-d')) {
                    $attData->whereIn('emp.as_status', [1,6]);
                } else {
                    $attData->where('emp.as_doj', '<=', $emp_day)
                        ->where(function ($p) use ($emp_day) {
                            $p->where(function ($q) use ($emp_day) {
                                $q->whereIn('emp.as_status', [2,3,4,5,6,7,8]);
                                $q->where('emp.as_status_date', '>=', $emp_day);
                            });
                            $p->orWhere(function ($q) use ($emp_day) {
                                $q->where('emp.as_status', 1);
                                $q->where(function ($j) use ($emp_day) {
                                    $j->where('emp.as_status_date', '<=', $emp_day);
                                    $j->orWhereNull('emp.as_status_date');
                                });
                            });
                        });
                }
            }
            // employee check
            if ($input['report_format'] == 0 && !empty($input['employee'])) {
                $attData->where('emp.associate_id', 'LIKE', '%'.$input['employee'] .'%');
            }
            $attData->whereIn('emp.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('emp.as_location', auth()->user()->location_permissions())
            ->when(!empty($input['unit']), function ($query) use ($input) {
                if ($input['unit'] == 145) {
                    return $query->whereIn('emp.as_unit_id', [1, 4, 5]);
                } else {
                    return $query->where('emp.as_unit_id', $input['unit']);
                }
            })
            ->when(!empty($input['location']), function ($query) use ($input) {
                return $query->where('emp.as_location', $input['location']);
            })
            ->when(!empty($input['area']), function ($query) use ($input) {
                return $query->where('emp.as_area_id', $input['area']);
            })
            ->when(!empty($input['department']), function ($query) use ($input) {
                return $query->where('emp.as_department_id', $input['department']);
            })
            ->when(!empty($input['line_id']), function ($query) use ($input) {
                return $query->where('emp.as_line_id', $input['line_id']);
            })
            ->when(!empty($input['floor_id']), function ($query) use ($input) {
                return $query->where('emp.as_floor_id', $input['floor_id']);
            })
            ->when($request['otnonot']!=null, function ($query) use ($input) {
                return $query->where('emp.as_ot', $input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use ($input) {
                return $query->where('emp.as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use ($input) {
                return $query->where('emp.as_subsection_id', $input['subSection']);
            })
            ->when(!empty($input['selected']), function ($query) use ($input) {
                if ($input['selected'] == 'null') {
                    return $query->whereNull($input['report_group']);
                } else {
                    if ($input['report_group'] == 'ot_hour') {
                        return $query->where($input['report_group'], 'LIKE', $input['selected']);
                    } else {
                        return $query->where($input['report_group'], $input['selected']);
                    }
                }
            });

            if ($input['report_type'] == 'ot' || $input['report_type'] == 'present' || $input['report_type'] == 'working_hour' || $input['report_type'] == 'late' || $input['report_type'] == 'before_absent_after_present' || $input['report_type'] == 'in_out_missing' || $input['report_type'] == 'in_missing' || $input['report_type'] == 'out_missing') {
                $attData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function ($join) use ($employeeData) {
                    $join->on('a.as_id', '=', 'emp.as_id')->addBinding($employeeData->getBindings());
                });
            } elseif ($input['report_type'] == 'absent') {
                $attData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function ($join) use ($employeeData) {
                    $join->on('a.associate_id', '=', 'emp.associate_id')->addBinding($employeeData->getBindings());
                });
                $getEmployee = $attData->get();
                $absentEmpA = collect($getEmployee)->pluck('associate_id');
                $day= date('j', strtotime($request['date']));
                $month= date('m', strtotime($request['date']));
                $year= date('Y', strtotime($request['date']));

                $absentShift = DB::table('hr_shift_roaster')
                    ->where('shift_roaster_month', $month)
                    ->where('shift_roaster_year', $year)
                    ->whereIn('shift_roaster_associate_id', $absentEmpA)
                    ->pluck('day_'.$day, 'shift_roaster_associate_id');

                // map with current shift
                $getEmployee = collect($getEmployee)->map(function ($q) use ($absentShift) {
                    if (isset($absentShift[$q->associate_id])) {
                        if ($absentShift[$q->associate_id] != null) {
                            $q->as_shift_id = $absentShift[$q->associate_id];
                        }
                    }
                    return $q;
                });

                $selshift = collect($getEmployee)
                            ->unique('as_shift_id')
                            ->pluck('as_shift_id')->toArray();

                $input['filter_shift'] = $selshift;

                // if selected shift
                if (isset($request->filter_shift)) {
                    $input['filter_shift'] = $request->filter_shift;
                    $getEmployee = collect($getEmployee)->filter(function ($q) use ($input) {
                        return in_array($q->as_shift_id, $input['filter_shift']);
                    })->values();
                }

                $totalEmployees = collect($getEmployee)->count();
                //dd($input['report_format'], $format);
                if ($format != null && count($getEmployee) > 0) {
                    $uniqueGroupEmp = collect($getEmployee)->groupBy($request['report_group'], true);
                }

                return view('hr.reports.daily_activity.attendance.absent_report', compact('format', 'getEmployee', 'input', 'totalEmployees', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'absentShift', 'uniqueGroupEmp', 'selshift'));

            // return absent data
            } elseif ($input['report_type'] == 'leave') {
                $attData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function ($join) use ($employeeData) {
                    $join->on('l.leave_ass_id', '=', 'emp.associate_id')->addBinding($employeeData->getBindings());
                });
            }

            // $countEmployee = $attData->select('emp.as_id', DB::raw('count(*) as countEmp'))->pluck('countEmp')->first();
            if ($input['report_group'] == 'ot_hour') {
                $groupBy = 'a.'.$input['report_group'];
                $attData->orderBy('a.ot_hour', 'desc');
            } else {
                $groupBy = 'emp.'.$input['report_group'];
            }
            if ($input['report_type'] == 'ot') {
                $attData->where('a.ot_hour', '>', 0);
                if ($input['report_format'] == 1 && $input['report_group'] != null) {
                    $attData->select($groupBy, DB::raw('count(*) as total'), DB::raw('sum(ot_hour) as groupOt'))->groupBy($groupBy);
                    $totalOtHour =  array_sum(array_column($attData->get()->toArray(), 'groupOt'));
                } else {
                    $attData->select('emp.as_id', 'emp.as_unit_id', 'emp.as_gender', 'emp.as_shift_id', 'emp.as_oracle_code', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id', 'emp.as_subsection_id', 'a.in_time', 'a.remarks', 'a.out_time', 'a.ot_hour')->orderBy('a.ot_hour', 'desc');
                    $totalOtHour = $attData->sum("a.ot_hour");
                }

                $totalValue = numberToTimeClockFormat($totalOtHour);
            } elseif ($input['report_type'] == 'working_hour') {
                $attData->leftjoin(DB::raw('(' . $shiftDataSql. ') AS s'), function ($join) use ($shiftData) {
                    $join->on('a.hr_shift_code', '=', 's.hr_shift_code')->addBinding($shiftData->getBindings());
                });
                // $attData->whereNotNull('a.in_time');
                // $attData->whereNotNull('a.out_time');
                if ($input['report_format'] == 1 && $input['report_group'] != null) {
                    $attData->select($groupBy, DB::raw('count(*) as total'), DB::raw('sum((TIMESTAMPDIFF(minute, in_time, out_time) - s.hr_shift_break_time)) as groupHourDuration'))->groupBy($groupBy);

                    $totalWorkingMinute =  array_sum(array_column($attData->get()->toArray(), 'groupHourDuration'));
                } else {
                    $attData->select('emp.as_id', 'emp.as_unit_id', 'emp.as_gender', 'emp.as_shift_id', 'emp.as_oracle_code', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id', 'emp.as_subsection_id', 'a.in_time', 'a.remarks', 'a.out_time', 's.hr_shift_break_time', 'a.ot_hour');
                    $attData->addSelect(DB::raw('(TIMESTAMPDIFF(minute, in_time, out_time) - s.hr_shift_break_time) as hourDuration'));
                    $totalWorkingMinute =  array_sum(array_column($attData->get()->toArray(), 'hourDuration'));
                }

                $hours = $totalWorkingMinute == 0 ? 0 : floor($totalWorkingMinute / 60);
                $minutes = $totalWorkingMinute == 0 ? 0 : ($totalWorkingMinute % 60);
                $totalValue = sprintf('%02d Hours, %02d Minutes', $hours, $minutes);
            } elseif ($input['report_type'] == 'employee') {
                if ($input['report_format'] == 1 && $input['report_group'] != null) {
                    $attData->select(
                        'emp.'.$input['report_group'],
                        DB::raw("COUNT(CASE WHEN as_gender = 'Male' THEN 1 END) AS male"),
                        DB::raw("COUNT(CASE WHEN as_gender = 'Female' THEN 1 END) AS female"),
                        DB::raw("COUNT(CASE WHEN as_gender = 'Male' and as_ot = 1 THEN 1 END) AS male_ot"),
                        DB::raw("COUNT(CASE WHEN as_gender = 'Male' and as_ot = 0 THEN 1 END) AS male_nonot"),
                        DB::raw("COUNT(CASE WHEN as_gender = 'Female' and as_ot = 1 THEN 1 END) AS female_ot"),
                        DB::raw("COUNT(CASE WHEN as_gender = 'Female' and as_ot = 0 THEN 1 END) AS female_nonot"),
                        DB::raw("COUNT(*) AS total")
                    )->groupBy('emp.'.$input['report_group']);
                } else {
                    $attData->select('emp.as_id', 'emp.as_unit_id', 'emp.as_gender', 'emp.as_shift_id', 'emp.as_oracle_code', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_ot', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id', 'emp.as_subsection_id', 'emp.as_doj');
                }
            } else {
                if ($input['report_format'] == 1 && $input['report_group'] != null) {
                    $attData->select('emp.'.$input['report_group'], DB::raw('count(*) as total'))->groupBy('emp.'.$input['report_group']);
                } else {
                    $attData->select('emp.as_id', 'emp.as_unit_id', 'emp.as_gender', 'emp.as_shift_id', 'emp.as_oracle_code', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id', 'emp.as_subsection_id');
                    if ($input['report_type'] == 'leave') {
                        $attData->addSelect('l.leave_type');
                    }

                    if ($input['report_type'] == 'in_out_missing' || $input['report_type'] == 'present' || $input['report_type'] == 'late'  || $input['report_type'] == 'in_missing' || $input['report_type'] == 'out_missing') {
                        $attData->addSelect('a.in_time', 'a.out_time', 'a.remarks');
                    }
                }
            }
            if ($input['report_format'] == 0) {
                $attData->orderBy('emp.as_oracle_sl', 'asc')->orderBy('emp.temp_id', 'asc');
            } else {
                if ($input['report_group'] == 'as_section_id' || $input['report_group'] == 'as_subsection_id') {
                    $attData->orderBy('emp.as_department_id', 'asc');
                } else {
                    $attData->orderBy($groupBy, 'asc');
                }
                if ($input['report_group'] == 'as_subsection_id') {
                    $attData->orderBy('emp.as_section_id', 'asc');
                }
            }
            $getEmployee = $attData->get()->toArray();
            // dd($getEmployee);
            if ($input['report_format'] == 1 && $input['report_group'] != null) {
                $totalEmployees = array_sum(array_column($getEmployee, 'total'));
            } else {
                $totalEmployees = count($getEmployee);
            }
            if ($input['report_type'] == 'working_hour') {
                $avgMin = $totalWorkingMinute == 0 ? 0 : $totalWorkingMinute / $totalEmployees;
                $aHours = $avgMin == 0 ? 0 : floor($avgMin / 60);
                $aMinutes = $avgMin == 0 ? 0 : ($avgMin % 60);
                $totalAvgHour = sprintf('%02d Hours, %02d Minutes', $aHours, $aMinutes);
            }
            if($input['report_type'] == 'employee' && $input['report_format'] == 0){
                $getGrade = designation_grade_by_id();
                $employees = collect($getEmployee)->map(function($q) use ($getGrade, $designation) {
                    
                    $designationGrade = $designation[$q->as_designation_id]['grade_id']??'';
                    if($designationGrade == ''){
                        $gradeSequence = 0;
                        $gradeName = $designation[$q->as_designation_id]['hr_designation_grade']??'';
                    }else{
                        $gradeSequence = $getGrade[$designationGrade]->grade_sequence??0;
                        $gradeName = $getGrade[$designationGrade]->grade_name??'';
                    }
                    $q->grade_sequence = $gradeSequence;
                    $q->grade_name = $gradeName;
                    return $q;
                });
                
                $getEmployee = collect($employees)->sortByDesc(function ($d) {
                    return $d->grade_sequence;
                })->toArray();
            }
            if ($format != null && count($getEmployee) > 0 && $input['report_format'] == 0) {
                $getEmployeeArray = $getEmployee;
                $formatBy = array_column($getEmployeeArray, $request['report_group']);
                $uniqueGroups = array_unique($formatBy);
                if (!array_filter($uniqueGroups)) {
                    $uniqueGroups = ['all'];
                    // $format = '';
                }

                if ($request['report_group'] == 'ot_hour') {
                    $uniqueGroupEmp = collect($getEmployee)->groupBy(function ($item) {
                        return (string) $item->ot_hour;
                    }, true);
                } else {
                    $uniqueGroupEmp = collect($getEmployee)->groupBy($request['report_group'], true);
                }
            }
            $unitWiseEmp = [];
            if($input['report_type'] == 'employee' && $input['report_group'] != 'as_unit_id' && count($uniqueGroups) == 1 && $input['report_format'] == 0){
                $unitWiseEmp = collect($getEmployee)->groupBy('as_unit_id', true)->map(function($q){
                    return $q->count();
                });
            }
            
            if(isset($input['export']) && $input['report_type'] == 'employee'){

                $dataRe['uniqueGroups'] = $uniqueGroups;
                $dataRe['format'] = $format;
                $dataRe['getEmployee'] = $getEmployee;
                $dataRe['input'] = $input;
                $dataRe['totalEmployees'] = $totalEmployees;
                $dataRe['unit'] = $unit;
                $dataRe['location'] = $location;
                $dataRe['line'] = $line;
                $dataRe['floor'] = $floor;
                $dataRe['department'] = $department;
                $dataRe['designation'] = $designation;
                $dataRe['line'] = $line;
                $dataRe['floor'] = $floor;
                $dataRe['area'] = $area;
                $dataRe['uniqueGroupEmp'] = $uniqueGroupEmp;
                $dataRe['subSection'] = $subSection;
                $dataRe['section'] = $section;
                
                $filename = 'Employee report - '.$request['date'];
                $filename .= '.xlsx';
                return Excel::download(new DailyReportExport($dataRe, 'employee'), $filename);
                
            }

            if ($input['report_type'] == 'ot') {
                return view('hr.reports.daily_activity.attendance.ot_report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees', 'totalValue', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'uniqueGroupEmp'));
            } elseif ($input['report_type'] == 'in_out_missing' || $input['report_type'] == 'in_missing' || $input['report_type'] == 'out_missing') {
                return view('hr.reports.daily_activity.attendance.in_out_mis_report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'uniqueGroupEmp'));
            } elseif ($input['report_type'] == 'employee') {
                return view('hr.reports.daily_activity.attendance.employee', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'uniqueGroupEmp', 'unitWiseEmp'));
            } elseif ($input['report_type'] == 'leave') {
                return view('hr.reports.daily_activity.attendance.leave_report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'uniqueGroupEmp'));
            } elseif ($input['report_type'] == 'working_hour') {
                return view('hr.reports.daily_activity.attendance.working_hour_report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees', 'totalValue', 'totalAvgHour', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'uniqueGroupEmp'));
            } elseif ($input['report_type'] == 'late' || $input['report_type'] == 'present') {
                return view('hr.reports.daily_activity.attendance.late_report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'uniqueGroupEmp'));
            } elseif ($input['report_type'] == 'before_absent_after_present') {
                return view('hr.reports.daily_activity.attendance.before_after_report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'uniqueGroupEmp'));
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $e;
            return 'error';
        }
    }

    public function getReports($input, $request)
    {
        // dd($input);
        $unit = unit_by_id();
        $location = location_by_id();
        $line = line_by_id();
        $floor = floor_by_id();
        $department = department_by_id();
        $designation = designation_by_id();
        $section = section_by_id();
        $subSection = subSection_by_id();
        $area = area_by_id();
        $uniqueGroupEmp = [];

        $getEmployee = array();
        $data = array();
        $format = $request['report_group'];
        $uniqueGroups = ['all'];
        $totalValue = 0;

        $emp_day = $request['date'];
        if($input['report_type'] == 'employee'){
            $attData = DB::table('hr_as_basic_info as emp')
            ->select('emp.as_id', 'emp.as_unit_id', 'emp.as_gender', 'emp.as_shift_id', 'emp.as_oracle_code', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_ot', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id', 'emp.as_subsection_id', 'emp.as_doj');
            if ($emp_day == date('Y-m-d')) {
                $attData->whereIn('emp.as_status', [1,6]);
            } else {
                $attData->where('emp.as_doj', '<=', $emp_day)
                    ->where(function ($p) use ($emp_day) {
                        $p->where(function ($q) use ($emp_day) {
                            $q->whereIn('emp.as_status', [2,3,4,5,6,7,8]);
                            $q->where('emp.as_status_date', '>=', $emp_day);
                        });
                        $p->orWhere(function ($q) use ($emp_day) {
                            $q->where('emp.as_status', 1);
                            $q->where(function ($j) use ($emp_day) {
                                $j->where('emp.as_status_date', '<=', $emp_day);
                                $j->orWhereNull('emp.as_status_date');
                            });
                        });
                    });
            }
        }else if($input['report_type'] == 'present'){
            // employee basic sql binding
            $employeeData = DB::table('hr_as_basic_info');
            $employeeData_sql = $employeeData->toSql();
            $tableName = get_att_table($request['unit']).' AS a';

            $attData = DB::table($tableName)
                ->select('emp.as_id', 'emp.as_unit_id', 'emp.as_gender', 'emp.as_shift_id', 'emp.as_oracle_code', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_ot', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id', 'emp.as_subsection_id', 'emp.as_doj', 'a.in_date', 'a.in_time', 'a.out_time', 'a.remarks', 'a.hr_shift_code', 'a.line_id')
                ->where('a.in_date', $request['date']);

            $attData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function ($join) use ($employeeData) {
                $join->on('a.as_id', '=', 'emp.as_id')->addBinding($employeeData->getBindings());
            });
        }

        if ($input['report_format'] == 0 && !empty($input['employee'])) {
            $attData->where('emp.associate_id', 'LIKE', '%'.$input['employee'] .'%');
        }

        $attData->whereIn('emp.as_unit_id', auth()->user()->unit_permissions())
        ->whereIn('emp.as_location', auth()->user()->location_permissions())
        ->when(!empty($input['unit']), function ($query) use ($input) {
            if ($input['unit'] == 145) {
                return $query->whereIn('emp.as_unit_id', [1, 4, 5]);
            } else {
                return $query->where('emp.as_unit_id', $input['unit']);
            }
        })
        ->when(!empty($input['location']), function ($query) use ($input) {
            return $query->where('emp.as_location', $input['location']);
        })
        ->when(!empty($input['area']), function ($query) use ($input) {
            return $query->where('emp.as_area_id', $input['area']);
        })
        ->when(!empty($input['department']), function ($query) use ($input) {
            return $query->where('emp.as_department_id', $input['department']);
        })
        ->when($request['otnonot']!=null, function ($query) use ($input) {
            return $query->where('emp.as_ot', $input['otnonot']);
        })
        ->when(!empty($input['section']), function ($query) use ($input) {
            return $query->where('emp.as_section_id', $input['section']);
        })
        ->when(!empty($input['subSection']), function ($query) use ($input) {
            return $query->where('emp.as_subsection_id', $input['subSection']);
        })
        ->when(!empty($input['selected']) && $input['report_group'] != 'as_line_id', function ($query) use ($input) {
            if ($input['selected'] == 'null') {
                return $query->whereNull($input['report_group']);
            } else {
                if ($input['report_group'] == 'ot_hour') {
                    return $query->where($input['report_group'], 'LIKE', $input['selected']);
                } else {
                    return $query->where($input['report_group'], $input['selected']);
                }
            }
        });

        $getEmployee = $attData->get()->toArray();

        // $avail = collect($getEmployee)->pluck('associate_id');

        // modify data with current line & floor
        $lineInfo = DB::table('hr_station')
                    ->select('associate_id', 'changed_floor', 'changed_line')
                    // ->whereIn('associate_id', $avail)
                    ->whereDate('start_date', '<=', $emp_day)
                    ->where(function ($q) use ($emp_day) {
                        $q->whereDate('end_date', '>=', $emp_day);
                        $q->orWhereNull('end_date');
                    })
                    ->get()->keyBy('associate_id');

        $getGrade = designation_grade_by_id();

        $getEmployee = collect($getEmployee)->map(function ($arr) use ($lineInfo, $line, $getGrade, $designation) {
            if (count($lineInfo) > 0) {
                $as_id = $arr->associate_id;
                if (isset($lineInfo[$as_id])) {
                    $arr->as_line_id = $lineInfo[$as_id]->changed_line;
                    $arr->as_floor_id = $lineInfo[$as_id]->changed_floor;
                }
            }
            if (isset($line[$arr->as_line_id])) {
                $arr->ordby = $line[$arr->as_line_id]['hr_line_name'];
            } else {
                $arr->ordby = 'No Line';
            }
            // if($arr->as_line_id == '' || $arr->as_line_id == null){
            //     $arr->as_line_id = 0;
            //     $arr->as_floor_id = 0;
            // }
            $designationGrade = $designation[$arr->as_designation_id]['grade_id']??'';
            if($designationGrade == ''){
                $gradeSequence = 0;
                $gradeName = $designation[$arr->as_designation_id]['hr_designation_grade']??'';
            }else{
                $gradeSequence = $getGrade[$designationGrade]->grade_sequence??0;
                $gradeName = $getGrade[$designationGrade]->grade_name??'';
            }
            $arr->grade_sequence = $gradeSequence;
            $arr->grade_name = $gradeName;

            return $arr;
        });

        if(isset($input['floor_id']) && $input['floor_id'] != null){
            $getEmployee = collect($getEmployee)->where('as_floor_id', $input['floor_id']);
        }

        if(isset($input['line_id']) && $input['line_id'] != null){
            $getEmployee = collect($getEmployee)->where('as_line_id', $input['line_id']);
        }

        if(!empty($input['selected']) && $input['report_group'] == 'as_line_id'){
            $getEmployee = collect($getEmployee)->where('ordby', $input['selected']);
        }

        $totalEmployees = collect($getEmployee)->count();

        $getEmployee = collect($getEmployee)->sortByDesc(function ($d) {
            return $d->grade_sequence;
        })->toArray();

        if ($input['report_group'] == 'as_line_id') {
            $uniqueGroups = collect($getEmployee)
                            ->groupBy('ordby', true)->toArray();
            ksort($uniqueGroups);
        } else {
            $uniqueGroups = collect($getEmployee)
                            ->groupBy($request['report_group'], true);
        }
        $unitWiseEmp = [];
        if($input['report_type'] == 'employee' && $input['report_group'] != 'as_unit_id' && count($uniqueGroups) == 1 && $input['report_format'] == 0){
            $unitWiseEmp = collect($getEmployee)->groupBy('as_unit_id', true)->map(function($q){
                return $q->count();
            });
        }
        // dd($uniqueGroups);
        if($input['report_format'] == 1){
            if($input['report_type'] == 'employee'){
                $uniqueGroups = collect($uniqueGroups)->map(function($q){
                    $q = collect($q);
                    $sum  = (object)[];
                    $sum->total  = $q->count();
                    $sum->male  = $q->where('as_gender', 'Male')->count();
                    $sum->male_ot  = $q->where('as_gender', 'Male')->where('as_ot',1)->count();
                    $sum->male_nonot  = $q->where('as_gender', 'Male')->where('as_ot',0)->count();
                    $sum->female  = $q->where('as_gender', 'Female')->count();
                    $sum->female_ot  = $q->where('as_gender', 'Female')->where('as_ot',1)->count();
                    $sum->female_nonot  = $q->where('as_gender', 'Female')->where('as_ot',0)->count();
                    return $sum;
                })->all();
            }else{
                $uniqueGroups = collect($uniqueGroups)->map(function($q){
                    $q = collect($q);
                    $sum  = (object)[];
                    $sum->total  = $q->count();
                    return $sum;
                })->all();                                                    
            }
            
        }
        // dd($uniqueGroups);
        $dataReport['uniqueGroups'] = $uniqueGroups;
        $dataReport['unitWiseEmp'] = $unitWiseEmp;
        $dataReport['format'] = $format;
        $dataReport['getEmployee'] = $getEmployee;
        $dataReport['input'] = $input;
        $dataReport['totalEmployees'] = $totalEmployees;
        $dataReport['unit'] = $unit;
        $dataReport['location'] = $location;
        $dataReport['line'] = $line;
        $dataReport['floor'] = $floor;
        $dataReport['department'] = $department;
        $dataReport['designation'] = $designation;
        $dataReport['line'] = $line;
        $dataReport['floor'] = $floor;
        $dataReport['area'] = $area;
        $dataReport['uniqueGroupEmp'] = $uniqueGroupEmp;
        $dataReport['subSection'] = $subSection;
        $dataReport['section'] = $section;
        
        if(isset($input['export'])){
            $filename = 'Employee report - '.$request['date'];
            $filename .= '.xlsx';
            return Excel::download(new DailyReportExport($dataReport, $input['report_type']), $filename);
        }
        if($input['report_type'] == 'employee'){
            return view('hr.reports.daily_activity.attendance.employee', $dataReport);
        }elseif ($input['report_type'] == 'late' || $input['report_type'] == 'present') {
            return view('hr.reports.daily_activity.attendance.present', $dataReport);
        }
    }

    public function twoDayAtt($input, $request)
    {
        $date[0] = $request['date'];
        $date[1] = \Carbon\Carbon::parse($request['date'])->subDays(1)->toDateString();
        $unit = unit_by_id();
        $location = location_by_id();
        $line = line_by_id();
        $floor = floor_by_id();
        $department = department_by_id();
        $designation = designation_by_id();
        $section = section_by_id();
        $subSection = subSection_by_id();
        $area = area_by_id();
        $short_designation = shortdesignation_by_id();


        $queryData = DB::table('hr_as_basic_info AS b')
            ->select('b.as_unit_id', 'b.as_area_id', 'b.as_location', 'b.as_emp_type_id', 'b.as_id', 'b.as_gender', 'b.associate_id', 'b.as_line_id', 'b.as_designation_id', 'b.as_oracle_code', 'b.as_department_id', 'b.as_floor_id', 'b.as_pic', 'b.as_name', 'b.as_contact', 'b.as_section_id', 'b.as_subsection_id', 'b.as_oracle_sl', 'b.temp_id')
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->whereNotIn('b.associate_id', config('base.ignore_salary'))
            ->whereIn('b.as_location', auth()->user()->location_permissions())
            ->when(!empty($input['unit']), function ($query) use ($input) {
                if ($input['unit'] == 145) {
                    return $query->whereIn('b.as_unit_id', [1, 4, 5]);
                } else {
                    return $query->where('b.as_unit_id', $input['unit']);
                }
            })
            ->when(!empty($input['location']), function ($query) use ($input) {
                return $query->where('b.as_location', $input['location']);
            })
            ->when(!empty($input['area']), function ($query) use ($input) {
                return $query->where('b.as_area_id', $input['area']);
            })
            ->when(!empty($input['department']), function ($query) use ($input) {
                return $query->where('b.as_department_id', $input['department']);
            })
            ->when(!empty($input['line_id']), function ($query) use ($input) {
                return $query->where('b.as_line_id', $input['line_id']);
            })
            ->when(!empty($input['floor_id']), function ($query) use ($input) {
                return $query->where('b.as_floor_id', $input['floor_id']);
            })
            ->when($request['otnonot']!=null, function ($query) use ($input) {
                return $query->where('b.as_ot', $input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use ($input) {
                return $query->where('b.as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use ($input) {
                return $query->where('b.as_subsection_id', $input['subSection']);
            })
            ->when(!empty($input['selected']), function ($query) use ($input) {
                if ($input['report_group'] != 'as_line_id' && $input['report_group'] != 'as_floor_id') {
                    if ($input['selected'] == 'null') {
                        return $query->whereNull($input['report_group']);
                    } else {
                        return $query->where('b.'.$input['report_group'], $input['selected']);
                    }
                }
            });

        if ($input['report_type'] == 'executive_attendance') {
            // benefit
            $benefitData = DB::table('hr_benefits');
            $benefitData_sql = $benefitData->toSql();
            $queryData->leftjoin(DB::raw('(' . $benefitData_sql. ') AS ben'), function ($join) use ($benefitData) {
                $join->on('ben.ben_as_id', 'b.associate_id')->addBinding($benefitData->getBindings());
            })->whereBetween('ben.ben_current_salary', [$input['min_sal'], $input['max_sal']]);
        }
        $queryData->where('b.as_status', 1);
        if ($input['report_type'] == 'executive_attendance') {
            $queryData->orderBy('b.as_unit_id', 'ASC')->orderBy('b.as_area_id', 'ASC');
        } else {
            $queryData->orderBy('b.as_oracle_sl', 'ASC')->orderBy('b.temp_id', 'ASC');
        }
        $getEmployee = $queryData->get();



        $avail = $getEmployee->pluck('associate_id');



        // modify data with current line & floor
        $lineInfo = DB::table('hr_station')
                    ->select('associate_id', 'changed_floor', 'changed_line')
                    ->whereIn('associate_id', $avail)
                    ->whereDate('start_date', '<=', $date[0])
                    ->where(function ($q) use ($date) {
                        $q->whereDate('end_date', '>=', $date[0]);
                        $q->orWhereNull('end_date');
                    })
                    ->get()->keyBy('associate_id');


        if (count($lineInfo) > 0) {
            $getEmployee = $getEmployee->map(function ($arr) use ($lineInfo, $line) {
                $as_id = $arr->associate_id;
                if (isset($lineInfo[$as_id])) {
                    $arr->df_line_id = $arr->as_line_id;
                    $arr->df_floor_id = $arr->as_floor_id;
                    $arr->as_line_id = $lineInfo[$as_id]->changed_line;
                    $arr->as_floor_id = $lineInfo[$as_id]->changed_floor;
                }
                if (isset($line[$arr->as_line_id])) {
                    $arr->ordby = $line[$arr->as_line_id]['hr_line_name'];
                } else {
                    $arr->ordby = '';
                }
                return $arr;
            });
        }


        //dd($input['selected']);
        if (!empty($input['selected']) && ($input['report_group'] == 'as_line_id' || $input['report_group'] == 'as_floor_id')) {
            $input['sel_type'] = $input['report_group'] == 'as_line_id' ? 'ordby' : $input['report_group'];
            $getEmployee = $getEmployee->filter(function ($arr) use ($input) {
                if ($input['selected'] == 'null') {
                    return $arr->{$input['sel_type']} == '';
                } else {
                    return $arr->{$input['sel_type']} == $input['selected'];
                }
            })->values();
        }

        $avail_as = $getEmployee->pluck('as_id');


        if ($input['report_group'] == 'as_line_id') {
            $uniqueGroups = collect($getEmployee)
                            //->sortBy('ordby')
                            ->groupBy('ordby', true)->toArray();
            ksort($uniqueGroups);
        } else {
            $uniqueGroups = collect($getEmployee)
                            //->sortBy('ordby')
                            ->groupBy($request['report_group'], true);
        }

        $format = $request['report_group'];

        $gUnit = [];
        $unitWiseEId = [];
        $attPr = [];
        if ($input['report_type'] == 'executive_attendance') {
            $gUnit = array_column($getEmployee->toArray(), 'as_unit_id');
            $gUnit = array_unique($gUnit);
            // $unitWiseEId = $getEmployee->groupBy('as_unit_id', true);
            $unitWiseEId = collect($getEmployee)->groupBy('as_unit_id', true)->map(function ($row) {
                return collect($row)->pluck('as_id');
            });

            foreach ($unitWiseEId as $key => $value) {
                $table = get_att_table($key);
                $attp = DB::table($table)
                        ->whereIn('in_date', $date)
                        ->whereIn('as_id', $value)
                        ->get();
                if (count($attp) > 0) {
                    $attp = $attp->groupBy('as_id', true)
                    ->map(function ($row) {
                        return collect($row)->keyBy('in_date');
                    })->toArray();

                    foreach ($attp as $key => $value) {
                        $attPr[$key] = $value;
                    }
                }
            }

            // $pr = array_reduce($attPr, 'array_merge', array());
            $pr = $attPr;
        } else {
            $table = get_att_table($input['unit']);

            $pr = DB::table($table)
                    ->whereIn('in_date', $date)
                    ->whereIn('as_id', $avail_as)
                    ->get();
            if (count($pr) > 0) {
                $pr = $pr->groupBy('as_id', true)
                    ->map(function ($row) {
                        return collect($row)->keyBy('in_date');
                    });
            }
        }

        $ab = DB::table('hr_absent')
                ->whereIn('date', $date)
                ->whereIn('associate_id', $avail)
                ->get();
        if (count($ab) > 0) {
            $ab = $ab->groupBy('associate_id', true)
                ->map(function ($row) {
                    return collect($row)->keyBy('date');
                });
        }

        $lv = DB::table('hr_leave')
                ->selectRaw("
                    leave_ass_id,
                    leave_type,
                    (CASE 
                        WHEN leave_from <= '".$date[0]."' AND leave_to >= '".$date[0]."' AND leave_from <= '".$date[1]."' AND leave_to >= '".$date[1]."' THEN 2 
                        WHEN leave_from <= '".$date[0]."' AND leave_to >= '".$date[0]."' THEN '".$date[0]."' 
                        WHEN leave_from <= '".$date[1]."' AND leave_to >= '".$date[1]."' THEN '".$date[1]."'
                    END) AS lv
                ")
                ->whereIn('leave_ass_id', $avail)
                ->where('leave_status', 1)
                ->where(function ($q) use ($date) {
                    $q->where('leave_from', "<=", $date[0]);
                    $q->where('leave_to', ">=", $date[0]);
                })
                ->orWhere(function ($q) use ($date) {
                    $q->where('leave_from', "<=", $date[1]);
                    $q->where('leave_to', ">=", $date[1]);
                })
                ->get();

        if (count($lv) > 0) {
            $lv = $lv->groupBy('leave_ass_id', true)
                ->map(function ($row) {
                    return collect($row)->keyBy('lv');
                });
        }

        $do = DB::table('holiday_roaster')
                ->whereIn('date', $date)
                ->whereIn('as_id', $avail)
                ->where('remarks', 'Holiday')
                ->get();

        if (count($do) > 0) {
            $do = $do->groupBy('as_id', true)
                ->map(function ($row) {
                    return collect($row)->keyBy('date');
                });
        }

        ini_set('zlib.output_compression', 1);
        if ($input['report_type'] == 'executive_attendance') {
            return view('hr.reports.daily_activity.attendance.unit_wise_att', compact('uniqueGroups', 'getEmployee', 'input', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'pr', 'ab', 'lv', 'do', 'format', 'date', 'short_designation', 'avail_as'))->render();
        }
        return view('hr.reports.daily_activity.attendance.two_day_att', compact('uniqueGroups', 'getEmployee', 'input', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'pr', 'ab', 'lv', 'do', 'format', 'date', 'short_designation', 'avail_as'))->render();
    }

    public function attStatistics($input, $request)
    {
        $date = $request['date'];
        $unit = unit_by_id();
        $location = location_by_id();
        $line = line_by_id();
        $floor = floor_by_id();
        $department = department_by_id();
        $designation = designation_by_id();
        $section = section_by_id();
        $subSection = subSection_by_id();
        $area = area_by_id();

        $getEmployee = $this->employee->getEmployees($input, $date);



        $avail = $getEmployee->pluck('associate_id');



        // modify data with current line & floor
        $lineInfo = DB::table('hr_station')
                    ->select('associate_id', 'changed_floor', 'changed_line')
                    ->whereIn('associate_id', $avail)
                    ->whereDate('start_date', '<=', $date)
                    ->where(function ($q) use ($date) {
                        $q->whereDate('end_date', '>=', $date);
                        $q->orWhereNull('end_date');
                    })
                    ->get()->keyBy('associate_id');

        if (count($lineInfo) > 0) {
            $getEmployee = $getEmployee->map(function ($arr) use ($lineInfo) {
                $as_id = $arr->associate_id;
                if (isset($lineInfo[$as_id])) {
                    $arr->df_line_id = $arr->as_line_id;
                    $arr->df_floor_id = $arr->as_floor_id;
                    $arr->as_line_id = $lineInfo[$as_id]->changed_line;
                    $arr->as_floor_id = $lineInfo[$as_id]->changed_floor;
                }
                return $arr;
            });
        }

        if (!empty($input['selected']) && ($input['report_group'] == 'as_line_id' || $input['report_group'] == 'as_floor_id')) {
            $getEmployee = $getEmployee->filter(function ($arr) use ($input) {
                return $arr->{$input['report_group']} == $input['selected'];
            })->values();
        }

        $avail_as = $getEmployee->pluck('as_id');
        $avail = $getEmployee->pluck('associate_id');

        $uniqueGroups = $getEmployee->groupBy($request['report_group'], true);

        $format = $request['report_group'];

        $table = get_att_table($input['unit']);

        $pr = DB::table($table.' AS a')
                ->select('a.*', 'b.associate_id')
                ->leftjoin('hr_as_basic_info as b', 'b.as_id', 'a.as_id')
                ->where('a.in_date', $date)
                ->whereIn('a.as_id', $avail_as)
                ->orderBy('temp_id', 'ASC')
                ->get()->keyBy('associate_id');

        $pr_emps = collect($pr)->pluck('associate_id');

        $ab = DB::table('hr_absent AS a')
                ->where('date', $date)
                ->whereIn('associate_id', $avail)
                ->get()->keyBy('associate_id');

        $ab_emps = collect($ab)->pluck('associate_id');

        $lv = DB::table('hr_leave')
                ->whereIn('leave_ass_id', $avail)
                ->where('leave_from', "<=", $date)
                ->where('leave_to', ">=", $date)
                ->get()->keyBy('leave_ass_id');

        $lv_emps = collect($lv)->pluck('leave_ass_id');

        $do = DB::table('holiday_roaster')
                ->where('date', $date)
                ->whereIn('as_id', $avail)
                ->where('remarks', 'Holiday')
                ->get()->keyBy('as_id');

        $do_emps = collect($do)->pluck('as_id');

        $count = [];
        if ($input['report_format'] == 1 && $input['report_group'] != null) {
            foreach ($uniqueGroups as $key => $item) {
                $emp = collect($item)->pluck('associate_id');
                $count[$key]['total'] = count($emp);
                $count[$key]['present'] = count($emp->intersect($pr_emps));
                $count[$key]['leave'] = count($emp->intersect($lv_emps));
                $count[$key]['holiday'] = count($emp->intersect($do_emps));
                $count[$key]['absent'] = count($emp) - $count[$key]['present'] - $count[$key]['leave'] - $count[$key]['holiday'];
            }
        }

        return view('hr.reports.daily_activity.attendance.att_statistics', compact('avail', 'uniqueGroups', 'getEmployee', 'input', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'pr', 'ab', 'lv', 'do', 'format', 'date', 'avail_as', 'count'));
    }


    public function getShiftEmployee($input, $request)
    {
        $date = $request['date']??date('Y-m-d');
        $day = 'day_'.((int)date('d', strtotime($date)));
        $format = $request['report_group']??'';
        $shift_id = isset($request['shift_id']) ? $request['shift_id'] : null;

        $unit = unit_by_id();
        $location = location_by_id();
        $line = line_by_id();
        $floor = floor_by_id();
        $department = department_by_id();
        $designation = designation_by_id();
        $section = section_by_id();
        $subSection = subSection_by_id();
        $area = area_by_id();


        $employees = $this->employee->getEmployees($input, $date);
        $associates = collect($employees)->pluck('associate_id');

        $changed = DB::table('hr_shift_roaster')
            ->where('shift_roaster_month', date('n', strtotime($date)))
            ->where('shift_roaster_year', date('Y', strtotime($date)))
            ->whereIn('shift_roaster_associate_id', $associates)
            ->pluck($day, 'shift_roaster_associate_id');

        $employees = collect($employees)
            ->map(function ($q) use ($changed) {
                if (isset($changed[$q->associate_id])) {
                    $q->as_shift_id = $changed[$q->associate_id] != null ? $changed[$q->associate_id] : $q->as_shift_id;
                }
                return $q;
            });
        if ($shift_id != '') {
            $employees = collect($employees)
                ->where('as_shift_id', $shift_id)
                ->all();
        }

        $totalEmployees = count($employees);

        $employees = collect($employees)
            ->sortBy('as_shift_id')
            ->groupBy('as_shift_id')
            ->all();

        if ($input['report_format'] == 1) {
            $employees = collect($employees)
                ->map(function ($q) {
                    $p = (object)[];
                    $p->male = collect($q)->where('as_gender', 'Male')->count();
                    $p->female = collect($q)->where('as_gender', 'Female')->count();
                    return $p;
                });
        }
        ini_set('zlib.output_compression', 1);

        return view('hr.reports.daily_activity.attendance.assigned_shift', compact('employees', 'totalEmployees', 'input', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'date', 'format'))->render();
    }


    public function getSpecialOt($input, $request)
    {
        $date = $request['date']??date('Y-m-d');
        $format = $request['report_group']??'';

        $data['date']           = $date;
        $data['unit']           = unit_by_id();
        $data['location']       = location_by_id();
        $data['line']           = line_by_id();
        $data['floor']          = floor_by_id();
        $data['department']     = department_by_id();
        $data['designation']    = designation_by_id();
        $data['section']        = section_by_id();
        $data['subSection']     = subSection_by_id();
        $data['area']           = area_by_id();


        $format = $input['report_group'];
        if (!is_array($input['unit'])) {
            $input['unit'] = ($input['unit'] == 145) ? [1,4,5] : [$input['unit']];
        }

        $data['input'] = $input;
        $data['format'] = $format;

        $employeeData     = $this->employee->QueryBuild($input, $date);
        $employeeData_sql = $employeeData->toSql();

        $summary = (object) [];

        $attData = DB::table('hr_att_special AS a')
                //->select('as_id','in_date','in_time','out_time','hr_shift_code','remarks')
                ->where('a.in_date', $date)
                ->join(DB::raw('(' . $employeeData_sql. ') AS emp'), function ($join) use ($employeeData) {
                    $join->on('a.as_id', '=', 'emp.as_id')->addBinding($employeeData->getBindings());
                });
        if ($input['report_format'] == 1 && $input['report_group'] != null) {
            $uniqueGroupEmp = $attData->select(
                $format,
                DB::raw('count(*) as total'),
                DB::raw('sum(a.ot_hour) as groupOt')
            )
                ->groupBy('emp.'.$format)
                ->get()
                ->keyBy($format)
                ->sortByDesc('groupOt');

            $summary->total_employee = $uniqueGroupEmp->sum('total');
            $summary->total_ot       = $uniqueGroupEmp->sum('groupOt');
        } else {
            $select = $this->globalSelection;
            array_push($select, 'a.in_time', 'a.out_time', 'a.ot_hour');

            $employees = $attData->select($select)
                ->orderBy('emp.as_oracle_sl')
                ->orderBy('emp.temp_id')
                ->get();

            $summary->total_employee = $employees->count();
            $summary->total_ot       = $employees->sum('ot_hour');

            $uniqueGroupEmp = $employees->groupBy($format, true);
        }
        $data['uniqueGroupEmp'] = $uniqueGroupEmp;
        $data['summary']        = $summary;

        ini_set('zlib.output_compression', 1);
        $filename = '';
        if (isset($input['export'])) {
            $filename .= 'Special OT report - '.$date;
            $filename .= '.xlsx';
            return Excel::download(new DailyReportExport($data, 'special_ot'), $filename);
        }

        return view('hr.reports.daily_activity.attendance.special_ot', $data)->render();
    }




    public function getatttoken($input, $request)
    {
        $associates = $this->employee->getEmployees($input, $request['date'])
                            ->pluck('associate_id');

        //dd($associates);
        $tableName = get_att_table($request['unit']).' AS a';

        $unit = unit_by_id();
        $department = department_by_id();
        $designation = designation_by_id();
        $section = section_by_id();

        $attData = DB::table($tableName)
                    ->select('b.as_name', 'b.as_designation_id', 'b.as_department_id', 'b.as_section_id', 'a.in_time', 'a.out_time', 'a.remarks', 'b.as_oracle_code', 'b.as_unit_id', 'b.associate_id')
                    ->leftJoin('hr_as_basic_info AS b', 'b.as_id', 'a.as_id')
                    ->where('a.in_date', $request['date'])
                    ->whereIn('b.associate_id', $associates)
                    ->where(function ($q) use ($request) {
                        $q->whereNull('a.in_time');
                        if ($request['date'] != date('Y-m-d')) {
                            $q->orWhereNull('a.out_time');
                        }
                        $q->orWhere('a.remarks', 'DSI');
                    })
                    ->orderBy('b.as_unit_id', 'ASC')
                    ->get();

        /*$absData = DB::table('hr_absent AS a')
                    ->select('b.as_name','b.as_designation_id','b.as_department_id','b.as_section_id','b.as_oracle_code','b.as_unit_id','b.associate_id')
                    ->leftJoin('hr_as_basic_info AS b','b.associate_id','a.associate_id')
                    ->where('a.date', $request['date'])
                    ->whereIn('b.associate_id', $associates)
                    ->orderBy('b.as_unit_id', 'ASC')
                    ->get();*/

        return view('hr.common.in_out_token', compact('attData', 'unit', 'department', 'designation', 'section', 'request'));
    }



    public function activityProcess($input)
    {
        $data['type'] = 'success';
        try {
            $input['area']       = isset($input['area']) ? $input['area'] : '';
            $input['otnonot']    = isset($input['otnonot']) ? $input['otnonot'] : '';
            $input['department'] = isset($input['department']) ? $input['department'] : '';
            $input['line_id']    = isset($input['line_id']) ? $input['line_id'] : '';
            $input['floor_id']   = isset($input['floor_id']) ? $input['floor_id'] : '';
            $input['section']    = isset($input['section']) ? $input['section'] : '';
            $input['subSection'] = isset($input['subSection']) ? $input['subSection'] : '';

            $getEmployee = array();
            $format = $input['report_group'];
            $uniqueGroups = ['all'];
            $totalValue = 0;

            // employee basic sql binding
            $employeeData = DB::table('hr_as_basic_info');
            $employeeData_sql = $employeeData->toSql();
            // shift
            if ($input['report_type'] == 'working_hour') {
                $shiftData = DB::table('hr_shift');
                $shiftDataSql = $shiftData->toSql();
            }
            $tableName = get_att_table($input['unit']).' AS a';
            if ($input['report_type'] == 'ot' || $input['report_type'] == 'working_hour' || $input['report_type'] == 'late' || $input['report_type'] == 'present') {
                $attData = DB::table($tableName)
                ->where('a.in_date', $input['date']);
                if ($input['report_type'] == 'late') {
                    $attData->where('a.late_status', 1);
                }
            } elseif ($input['report_type'] == 'absent') {
                $attData = DB::table('hr_absent AS a')
                ->where('a.date', $input['date']);
            } elseif ($input['report_type'] == 'leave') {
                $attData = DB::table('hr_leave AS l')
                ->whereRaw('? between leave_from and leave_to', [$input['date']])
                ->where('l.leave_status', 1);
            } elseif ($input['report_type'] == 'before_absent_after_present') {
                $absentData = $this->getAbsentEmployeeFromDate($input);
                $attData = DB::table($tableName)
                ->where('a.in_date', $input['present_date'])
                ->whereIn('a.as_id', $absentData);
            }
            // employee check
            if ($input['report_format'] == 0 && !empty($input['employee'])) {
                $attData->where('emp.associate_id', 'LIKE', '%'.$input['employee'] .'%');
            }
            $attData->whereIn('emp.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('emp.as_location', auth()->user()->location_permissions())
            ->when(!empty($input['unit']), function ($query) use ($input) {
                if ($input['unit'] == 145) {
                    return $query->whereIn('emp.as_unit_id', [1, 4, 5]);
                } else {
                    return $query->where('emp.as_unit_id', $input['unit']);
                }
            })
            ->when(!empty($input['location']), function ($query) use ($input) {
                return $query->where('emp.as_location', $input['location']);
            })
            ->when(!empty($input['area']), function ($query) use ($input) {
                return $query->where('emp.as_area_id', $input['area']);
            })
            ->when(!empty($input['department']), function ($query) use ($input) {
                return $query->where('emp.as_department_id', $input['department']);
            })
            ->when(!empty($input['line_id']), function ($query) use ($input) {
                return $query->where('emp.as_line_id', $input['line_id']);
            })
            ->when(!empty($input['floor_id']), function ($query) use ($input) {
                return $query->where('emp.as_floor_id', $input['floor_id']);
            })
            ->when($input['otnonot']!=null, function ($query) use ($input) {
                return $query->where('emp.as_ot', $input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use ($input) {
                return $query->where('emp.as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use ($input) {
                return $query->where('emp.as_subsection_id', $input['subSection']);
            })
            ->when(!empty($input['selected']), function ($query) use ($input) {
                if ($input['selected'] == 'null') {
                    return $query->whereNull($input['report_group']);
                } else {
                    if ($input['report_group'] == 'ot_hour') {
                        return $query->where($input['report_group'], 'LIKE', $input['selected']);
                    } else {
                        return $query->where($input['report_group'], $input['selected']);
                    }
                }
            });

            if ($input['report_type'] == 'ot' || $input['report_type'] == 'working_hour' || $input['report_type'] == 'present' || $input['report_type'] == 'late' || $input['report_type'] == 'before_absent_after_present') {
                $attData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function ($join) use ($employeeData) {
                    $join->on('a.as_id', '=', 'emp.as_id')->addBinding($employeeData->getBindings());
                });
            } elseif ($input['report_type'] == 'absent') {
                $attData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function ($join) use ($employeeData) {
                    $join->on('a.associate_id', '=', 'emp.associate_id')->addBinding($employeeData->getBindings());
                });
            } elseif ($input['report_type'] == 'leave') {
                $attData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function ($join) use ($employeeData) {
                    $join->on('l.leave_ass_id', '=', 'emp.associate_id')->addBinding($employeeData->getBindings());
                });
            }

            if ($input['report_type'] == 'ot') {
                $attData->where('a.ot_hour', '>', 0);
                if ($input['report_format'] == 1 && $input['report_group'] != null) {
                    if ($input['report_group'] == 'ot_hour') {
                        $groupBy = 'a.'.$input['report_group'];
                    } else {
                        $groupBy = 'emp.'.$input['report_group'];
                    }
                    $attData->select($groupBy, DB::raw('count(*) as total'), DB::raw('sum(ot_hour) as groupOt'))->groupBy($groupBy)->orderBy('a.ot_hour', 'desc');
                    $totalOtHour =  array_sum(array_column($attData->get()->toArray(), 'groupOt'));
                } else {
                    $attData->select('emp.as_id', 'emp.as_gender', 'emp.as_oracle_code', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id', 'emp.as_subsection_id', 'a.in_time', 'a.out_time', 'a.ot_hour')->orderBy('a.ot_hour', 'desc');
                    $totalOtHour = $attData->sum("a.ot_hour");
                }

                $totalValue = numberToTimeClockFormat($totalOtHour);
            } elseif ($input['report_type'] == 'working_hour') {
                $attData->leftjoin(DB::raw('(' . $shiftDataSql. ') AS s'), function ($join) use ($shiftData) {
                    $join->on('a.hr_shift_code', '=', 's.hr_shift_code')->addBinding($shiftData->getBindings());
                });
                // $attData->whereNotNull('a.in_time');
                // $attData->whereNotNull('a.out_time');
                if ($input['report_format'] == 1 && $input['report_group'] != null) {
                    $groupBy = 'emp.'.$input['report_group'];

                    $attData->select($groupBy, DB::raw('count(*) as total'), DB::raw('sum((TIMESTAMPDIFF(minute, in_time, out_time) - s.hr_shift_break_time)) as groupHourDuration'))->groupBy($groupBy)->orderBy('groupHourDuration', 'desc')->orderBy('emp.as_section_id', 'desc');
                    $totalWorkingMinute =  array_sum(array_column($attData->get()->toArray(), 'groupHourDuration'));
                } else {
                    $attData->select('emp.as_id', 'emp.as_gender', 'emp.as_oracle_code', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id', 'emp.as_subsection_id', 'a.in_time', 'a.out_time', 's.hr_shift_break_time', 'a.ot_hour')->orderBy('emp.as_subsection_id', 'asc');
                    $attData->addSelect(DB::raw('(TIMESTAMPDIFF(minute, in_time, out_time) - s.hr_shift_break_time) as hourDuration'));
                    $totalWorkingMinute =  array_sum(array_column($attData->get()->toArray(), 'hourDuration'));
                }

                $hours = $totalWorkingMinute == 0 ? 0 : floor($totalWorkingMinute / 60);
                $minutes = $totalWorkingMinute == 0 ? 0 : ($totalWorkingMinute % 60);
                $totalValue = sprintf('%02d Hours, %02d Minutes', $hours, $minutes);
            } else {
                if ($input['report_format'] == 1 && $input['report_group'] != null) {
                    $attData->select('emp.'.$input['report_group'], DB::raw('count(*) as total'))->groupBy('emp.'.$input['report_group']);
                } else {
                    $attData->select('emp.as_id', 'emp.as_gender', 'emp.as_oracle_code', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_subsection_id', 'emp.as_section_id');
                    if ($input['report_type'] == 'leave') {
                        $attData->addSelect('l.leave_type');
                    }
                    if ($input['report_type'] == 'in_out_missing' || $input['report_type'] == 'present' || $input['report_type'] == 'late') {
                        $attData->addSelect('a.in_time', 'a.out_time', 'a.remarks');
                    }
                }
            }

            $getEmployee = $attData->get();
            $data['value'] = $getEmployee;
            return $data;
        } catch (\Exception $e) {
            $data['type'] = 'error';
            $data['msg'] = $e->getMessage();
            return $data;
        }
    }

    public function activityExcle(Request $request)
    {
        $input = $request->all();
        $unit = unit_by_id();
        $line = line_by_id();
        $floor = floor_by_id();
        $department = department_by_id();
        $designation = designation_by_id();
        $section = section_by_id();
        $subSection = subSection_by_id();
        $result = $this->activityProcess($input);
        if ($result['type'] == 'success') {
            $excel = [];

            foreach ($result['value'] as $key => $value) {
                $dataValue = array(
                    'Name' => $value->as_name,
                    'Associate ID' => $value->associate_id,
                    'Oracle ID' => $value->as_oracle_code,
                    'Designation' => $designation[$value->as_designation_id]['hr_designation_name']??'',
                    'Department' => $department[$value->as_department_id]['hr_department_name']??'',
                    'Section' => $section[$value->as_section_id]['hr_section_name']??'',
                    'Sub Section' => $subSection[$value->as_subsection_id]['hr_subsec_name']??'',
                    'Floor' => $floor[$value->as_floor_id]['hr_floor_name']??'',
                    'Line' => $line[$value->as_line_id]['hr_line_name']??''

                );
                if ($input['report_type'] == 'ot' || $input['report_type'] == 'working_hour' || $input['report_type'] == 'late' || $input['report_type'] == 'present') {
                    $dataValue['In Time'] = ($value->in_time != null ? date('H:i:s', strtotime($value->in_time)) : '');
                    $dataValue['Out Time'] = ($value->out_time != null ? date('H:i:s', strtotime($value->out_time)) : '');
                }
                if ($input['report_type'] == 'working_hour') {
                    $dataValue['Break Time'] = $value->hr_shift_break_time;
                    $dataValue['Working Hour'] = round($value->hourDuration/60, 2);
                }
                if ($input['report_type'] == 'ot') {
                    $dataValue['OT Hour'] = $value->ot_hour;
                }

                $excel[$value->associate_id] = $dataValue;
            }
            $fileName = ($input['unit'] == 145 ? 'MBM + MBF+MBM2' : $unit[$input['unit']]['hr_unit_short_name']).' - '.$input['report_type'].' - '.$input['date'].'.xlsx';
            $header_style = (new StyleBuilder())->setFontBold()->build();

            return (new FastExcel(collect($excel)))->headerStyle($header_style)->download($fileName);
        } else {
            return 'Something Wrong, Please try again';
        }
    }

    public function presentAbsentReport(Request $request)
    {
        $input = $request->all();
        // dd($input);
        // return $input;
        try {
            $input['area']       = isset($request['area']) ? $request['area'] : '';
            $input['otnonot']    = isset($request['otnonot']) ? $request['otnonot'] : '';
            $input['department'] = isset($request['department']) ? $request['department'] : '';
            $input['line_id']    = isset($request['line_id']) ? $request['line_id'] : '';
            $input['floor_id']   = isset($request['floor_id']) ? $request['floor_id'] : '';
            $input['section']    = isset($request['section']) ? $request['section'] : '';
            $input['subSection'] = isset($request['subSection']) ? $request['subSection'] : '';

            $getEmployee = array();
            $format = $request['report_group'];
            $uniqueGroups = ['all'];
            $totalValue = 0;

            // employee basic sql binding
            $employeeData = DB::table('hr_as_basic_info AS emp')
            ->where('emp.as_status', 1);
            if ($input['report_format'] == 0 && !empty($input['employee'])) {
                $employeeData->where('emp.associate_id', 'LIKE', '%'.$input['employee'] .'%');
            }
            $employeeData->where('emp.as_unit_id', $request['unit'])
            ->when(!empty($input['area']), function ($query) use ($input) {
                return $query->where('emp.as_area_id', $input['area']);
            })
            ->when(!empty($input['department']), function ($query) use ($input) {
                return $query->where('emp.as_department_id', $input['department']);
            })
            ->when(!empty($input['line_id']), function ($query) use ($input) {
                return $query->where('emp.as_line_id', $input['line_id']);
            })
            ->when(!empty($input['floor_id']), function ($query) use ($input) {
                return $query->where('emp.as_floor_id', $input['floor_id']);
            })
            ->when($request['otnonot']!=null, function ($query) use ($input) {
                return $query->where('emp.as_ot', $input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use ($input) {
                return $query->where('emp.as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use ($input) {
                return $query->where('emp.as_subsection_id', $input['subSection']);
            });
            if ($input['report_format'] == 1 && $input['report_group'] != null) {
                $employeeData->select('emp.'.$input['report_group'], DB::raw('count(*) as total'))->groupBy('emp.'.$input['report_group']);
            } else {
                $employeeData->select('emp.as_id', 'emp.as_gender', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id');
            }

            $getEmployee = $employeeData->get();
            $employeeAsIdData = $employeeData->pluck('emp.as_id')->toArray();
            $employeeAssIdData = $employeeData->pluck('emp.associate_id')->toArray();

            if ($input['report_format'] == 1 && $input['report_group'] != null) {
                $totalEmployees = array_sum(array_column($getEmployee->toArray(), 'total'));
            } else {
                $totalEmployees = count($getEmployee);
            }
            return $totalEmployees;

            // prsent
            $tableName = get_att_table($request['unit']).' AS a';
            $presentData = DB::table($tableName)
            ->where('a.in_date', $request['date']);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
            return 'error';
        }
    }
    public function getAbsentEmployeeFromDate($input)
    {
        // employee basic sql binding
        $employeeData = DB::table('hr_as_basic_info');
        $employeeData_sql = $employeeData->toSql();

        $queryData = Absent::select('emp.as_id');
        if ($input['unit'] == 145) {
            $queryData->whereIn('hr_unit', [1,4,5]);
        } else {
            $queryData->where('hr_unit', $input['unit']);
        }
        $queryData->where('date', $input['absent_date'])
        ->when(!empty($input['area']), function ($query) use ($input) {
            return $query->where('emp.as_area_id', $input['area']);
        })
        ->when(!empty($input['department']), function ($query) use ($input) {
            return $query->where('emp.as_department_id', $input['department']);
        })
        ->when(!empty($input['line_id']), function ($query) use ($input) {
            return $query->where('emp.as_line_id', $input['line_id']);
        })
        ->when(!empty($input['floor_id']), function ($query) use ($input) {
            return $query->where('emp.as_floor_id', $input['floor_id']);
        })
        ->when($input['otnonot']!=null, function ($query) use ($input) {
            return $query->where('emp.as_ot', $input['otnonot']);
        })
        ->when(!empty($input['section']), function ($query) use ($input) {
            return $query->where('emp.as_section_id', $input['section']);
        })
        ->when(!empty($input['subSection']), function ($query) use ($input) {
            return $query->where('emp.as_subsection_id', $input['subSection']);
        });
        $queryData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function ($join) use ($employeeData) {
            $join->on('emp.associate_id', 'hr_absent.associate_id')->addBinding($employeeData->getBindings());
        });

        $absentData = $queryData->pluck('emp.as_id')->toArray();
        return $absentData;
    }

    public function attendanceAudit(Request $request)
    {
        $input = $request->all();
        if ($input['date'] != null && $input['unit'] != null) {
            $unitList  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_name', 'hr_unit_id');
            $areaList  = DB::table('hr_area')->where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');

            return view('hr/reports/daily_activity/attendance/audit', compact('unitList', 'areaList', 'input'));
        } else {
            toastr()->error('Something Wrong!');
            return back();
        }
    }


    public function management(Request $request)
    {
        $beforeday= Carbon::parse($request->date)->subDay()->toDateString();
        // dd($beforeday);
        $input=$request->date;
        $unit= Unit::where('hr_unit_status', '1')
                ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
                ->first()->hr_unit_id;

        $unit_query = DB::table('hr_unit')
                ->whereIn('hr_unit_id', auth()->user()->unit_permissions());
        if ($request->unit != '' && $request->unit != null) {
            $unit_query->where('hr_unit_id', $request->unit);
        }

        $unit= implode(',', $unit_query->pluck('hr_unit_id')->toArray());
        if ($request->unit ==145) {
            $unit =implode(',', str_split($request->unit));
        }



        $data =  DB::select('call hr_management_attendance_prc
                (
                "'.$unit.'"
                ,"'.$request->date.'"
                )
                ');

        // dd($data);
        // $unit=(array)$request->unit;
        // $unit = [1,2,3,4,5,8];
        $date = $request->date;
        $attTable = collect(attendance_table())
                    ->unique();


        // union all atttendance table and queary atttendance  a date
        $query = [];
        foreach ($attTable as $key => $table) {
            $qr = DB::table($table)
                    ->select('as_id', 'in_unit', 'out_unit', 'in_date')
                    ->where('in_date', $date)
                    ->get()
                    ->keyBy('as_id', true);
            $query=collect($query)->union($qr);
        }

        // queary atttendance table a before date
        $query2 = [];
        foreach ($attTable as $key => $table) {
            $qr2 = DB::table($table)
                    ->select('as_id', 'in_unit', 'out_unit', 'in_date')
                    ->where('in_date', $beforeday)
                    ->get()
                    ->keyBy('as_id', true);
            $query2=collect($query2)->union($qr2);
        }

        //join query1 and query2 in data query
        $data=collect($data)->map(function ($q) use ($query, $query2) {
            $q->in_unit = null;
            $q->out_unit = null;
            if (isset($query[$q->as_id])) {
                $q->in_unit = $query[$q->as_id]->in_unit;
            }
            if (isset($query2[$q->as_id])) {
                $q->out_unit = $query2[$q->as_id]->out_unit;
            }
            return $q;
        })
                    ->groupBy('hr_area_name')
                    ->sortBy('hr_area_name');


        $managementdate=$data;

        // dd($managementdate);


        return view(
            'hr.reports.daily_activity.attendance.unit_wise_management_att',
            compact('managementdate', 'input')
        )->render();
    }


    public function habitualabsent(Request $request)
    {
        try {
            $input=$request->all();

            $unit_name= DB::table('hr_unit')
                ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
                ->where('hr_unit_id', $request->unit)
                ->pluck('hr_unit_name')
                ->first();
            if ($request->unit==145) {
                $unit_name='MBM+MFW+MBM2';
            }

            $department_by_id= DB::table('hr_department')
            ->where('hr_department_id', $request->department)
            ->pluck('hr_department_name')
            ->first();

            $area_by_id= DB::table('hr_area')
            ->where('hr_area_id', $request->area)
            ->pluck('hr_area_name')
            ->first();

            $section_by_id= DB::table('hr_section')
            ->where('hr_section_id', $request->section)
            ->pluck('hr_section_name')
            ->first();

            $hr_line= DB::table('hr_line')
            ->where('hr_line_id', $request->line_id)
            ->pluck('hr_line_name')
            ->first();

            $hr_floor= DB::table('hr_floor')
            ->where('hr_floor_id', $request->floor_id)
            ->pluck('hr_floor_name')
            ->first();

            $subsection_by_id= DB::table('hr_subsection')
            ->where('hr_subsec_id', $request->subSection)
            ->pluck('hr_subsec_name')
            ->first();



            $location_name = location_by_id()
            ->where('hr_location_id', $request->location)
            ->pluck('hr_location_name', 'hr_location_id')
            ->first();


            $unit_id=$request->unit;
            if ($request->unit==145) {
                $unit_id='1,4,5';
            }

            $absentreason=DB::Select('call hr_habitual_absent (
                "'.$request->from_date.'"
                ,"'.$request->to_date.'"
                ,"'.$unit_id.'"
                ,"'.$request->location.'"
                ,"'.$request->area.'"
                ,"'.$request->department.'"
                ,"'.$request->section.'"
                ,"'.$request->subSection.'"
                ,"'.$request->floor_id.'"
                ,"'.$request->line_id.'"
                ,"'.$request->otnonot.'"
                )');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
            return 'error';
        }


        return view(
            'hr.reports.summary.habitual_absent',
            compact('absentreason', 'input', 'unit_name', 'department_by_id', 'area_by_id', 'section_by_id', 'subsection_by_id', 'location_name', 'hr_line', 'hr_floor')
        )->render();
    }

    public function habitualabsentexcel(Request $request)
    {
        try {
            $input=$request->all();

            $unit_name= DB::table('hr_unit')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->where('hr_unit_id', $request->unit)
            ->pluck('hr_unit_name')
            ->first();
            if ($request->unit==145) {
                $unit_name='MBM+MFW+MBM2';
            }

            $department_by_id= DB::table('hr_department')
           ->where('hr_department_id', $request->department)
           ->pluck('hr_department_name')
           ->first();

            $area_by_id= DB::table('hr_area')
           ->where('hr_area_id', $request->area)
           ->pluck('hr_area_name')
           ->first();

            $section_by_id= DB::table('hr_section')
           ->where('hr_section_id', $request->section)
           ->pluck('hr_section_name')
           ->first();

            $hr_line= DB::table('hr_line')
           ->where('hr_line_id', $request->line_id)
           ->pluck('hr_line_name')
           ->first();

            $hr_floor= DB::table('hr_floor')
           ->where('hr_floor_id', $request->floor_id)
           ->pluck('hr_floor_name')
           ->first();

            $subsection_by_id= DB::table('hr_subsection')
           ->where('hr_subsec_id', $request->subSection)
           ->pluck('hr_subsec_name')
           ->first();



            $location_name = location_by_id()
           ->where('hr_location_id', $request->location)
           ->pluck('hr_location_name', 'hr_location_id')
           ->first();


            $unit_id=$request->unit;
            if ($request->unit==145) {
                $unit_id='1,4,5';
            }

            $absentreason=DB::Select('call hr_habitual_absent (
                    "'.$request->from_date.'"
                    ,"'.$request->to_date.'"
                    ,"'.$unit_id.'"
                    ,"'.$request->location.'"
                    ,"'.$request->area.'"
                    ,"'.$request->department.'"
                    ,"'.$request->section.'"
                    ,"'.$request->subSection.'"
                    ,"'.$request->floor_id.'"
                    ,"'.$request->line_id.'"
                    ,"'.$request->otnonot.'"
                )');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
            return 'error';
        }
        return (new FastExcel($absentreason))->download('Absent Reason List.csv');
    }



    public function latewarning(Request $request)
    {
        try {
            $input=$request->all();

            $unit_name= DB::table('hr_unit')
        ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
        ->where('hr_unit_id', $request->unit)
        ->pluck('hr_unit_name')
        ->first();
            if ($request->unit==145) {
                $unit_name='MBM+MFW+MBM2';
            }

            $department_by_id= DB::table('hr_department')
        ->where('hr_department_id', $request->department)
        ->pluck('hr_department_name')
        ->first();

            $area_by_id= DB::table('hr_area')
        ->where('hr_area_id', $request->area)
        ->pluck('hr_area_name')
        ->first();

            $section_by_id= DB::table('hr_section')
        ->where('hr_section_id', $request->section)
        ->pluck('hr_section_name')
        ->first();

            $hr_line= DB::table('hr_line')
        ->where('hr_line_id', $request->line_id)
        ->pluck('hr_line_name')
        ->first();

            $hr_floor= DB::table('hr_floor')
        ->where('hr_floor_id', $request->floor_id)
        ->pluck('hr_floor_name')
        ->first();

            $subsection_by_id= DB::table('hr_subsection')
        ->where('hr_subsec_id', $request->subSection)
        ->pluck('hr_subsec_name')
        ->first();



            $location_name = location_by_id()
        ->where('hr_location_id', $request->location)
        ->pluck('hr_location_name', 'hr_location_id')
        ->first();



            $unit_id=$request->unit;
            if ($request->unit==145) {
                $unit_id=[1,4,5];
            } else {
                $unit_id=[$request->unit];
            }


            if ($request->unit ==1 or $request->unit ==4 or $request->unit ==5 or $request->unit ==145) {
                $table='hr_attendance_mbm as b';
            }
            if ($request->unit==2) {
                $table='hr_attendance_ceil as b';
            }
            if ($request->unit==3) {
                $table='hr_attendance_aql as b';
            }
            if ($request->unit==8) {
                $table='hr_attendance_cew as b';
            }


            // $table='hr_attendance_mbm as b';

            $data = DB::table('hr_basic_info_view as a')
        ->leftJoin($table, 'b.as_id', '=', 'a.as_id')
        ->select(
            'a.as_id',
            'as_name',
            'as_doj',
            'hr_designation_name',
            'hr_department_name',
            'hr_unit_name',
            'hr_section_name',
            'associate_id',
            DB::raw('count(*) as count')
        )
        ->where('b.in_date', '>=', $request->from_date)
        ->where('b.in_date', '<=', $request->to_date)
        ->whereIn('a.hr_unit_id', $unit_id)
        ->when($request->otnonot != null, function ($q) use ($request) {
            $q->where('a.as_ot', $request->otnonot);
        })
        ->when(!empty($request['area']), function ($query) use ($request) {
            return $query->where('a.as_area_id', $request['area']);
        })
        ->when(!empty($request['location']), function ($query) use ($request) {
            return $query->where('a.as_location_id', $request['location']);
        })
        ->when(!empty($request['department']), function ($query) use ($request) {
            return $query->where('a.as_department_id', $request['department']);
        })
        ->when(!empty($request['line_id']), function ($query) use ($request) {
            return $query->where('a.as_line_id', $request['line_id']);
        })
        ->when(!empty($request['floor_id']), function ($query) use ($request) {
            return $query->where('a.as_floor_id', $request['floor_id']);
        })
        ->when(!empty($request['section']), function ($query) use ($request) {
            return $query->where('a.as_section_id', $request['section']);
        })
        ->when(!empty($request['subSection']), function ($query) use ($request) {
            return $query->where('a.as_subsection_id', $request['subSection']);
        })
        ->where('b.late_status', 1)
        ->where('a.as_status', [1,6])
        ->groupBy('a.as_id')
        ->having('count', '>=', $request->latecount)
        ->orderby('count', 'desc')
        ->get();

            if ($request->export=='V') {
                // dd($data);
                $data = $data->map(function ($q) {
                    return collect($q)->toArray();
                });

                return (new FastExcel($data))->download('Employee Late List.csv');
            } else {
                $data=$data->toArray();
                return view(
                    'hr.reports.summary.latewarningletter',
                    compact('data', 'input', 'unit_name', 'department_by_id', 'area_by_id', 'section_by_id', 'subsection_by_id', 'location_name', 'hr_line', 'hr_floor')
                )->render();
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            dd($bug);
            return 'error';
        }
    }


    public function viewletter($id, Request $request)
    {

        // dd(auth()->user()->name,auth()->user()->id);

        $data = DB::table('hr_basic_info_view')
                    ->where('associate_id', $id)
                    ->first();


        $signatory_name= DB::table('hr_signatory_name')
                        ->where('as_id', $request->manager)
                        ->first();
        // dd($signatory_name);

        if ($data->hr_unit_id ==1 or $data->hr_unit_id ==4 or $data->hr_unit_id ==5 or $data->hr_unit_id ==145) {
            $table='hr_attendance_mbm';
        }
        if ($data->hr_unit_id==2) {
            $table='hr_attendance_ceil';
        }
        if ($data->hr_unit_id==3) {
            $table='hr_attendance_aql';
        }
        if ($data->hr_unit_id==8) {
            $table='hr_attendance_cew';
        }

        $x= date('Y-m-01', strtotime(Carbon::now()->subDay(180)->toDateString()));
        $y= date('Y-m-d');

        $data1 = DB::table($table)
                    ->select(
                        DB::raw("DATE_FORMAT(in_date,'%M-%Y') as months"),
                        DB::raw('count(*) as days')
                    )
                    ->where('as_id', $data->as_id)
                    ->where('in_date', '>=', $x)
                    ->where('in_date', '<=', $y)
                    ->groupBy(DB::raw("DATE_FORMAT(in_date,'%M-%Y')"))
                    ->orderBy(DB::raw("DATE_FORMAT(in_date,'%m-%Y')"))
                    ->get();

        return view('hr.reports.summary.latewarningletterprint', compact('data', 'data1', 'signatory_name'))->render();
    }



    public function linechangedaily(Request $request)
    {
        try {

       // dd($request->all());
            // dd('ddddddd');

            $input=$request->all();

            $unit_name= DB::table('hr_unit')
        ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
        ->where('hr_unit_id', $request->unit)
        ->pluck('hr_unit_name')
        ->first();





            // dd($signatory_name);
            if ($request->unit==145) {
                $unit_name='MBM+MFW+MBM2';
            }

            $department_by_id= DB::table('hr_department')
        ->where('hr_department_id', $request->department)
        ->pluck('hr_department_name')
        ->first();

            $area_by_id= DB::table('hr_area')
        ->where('hr_area_id', $request->area)
        ->pluck('hr_area_name')
        ->first();

            $section_by_id= DB::table('hr_section')
        ->where('hr_section_id', $request->section)
        ->pluck('hr_section_name')
        ->first();

            $hr_line= DB::table('hr_line')
        ->where('hr_line_id', $request->line_id)
        ->pluck('hr_line_name')
        ->first();

            $hr_floor= DB::table('hr_floor')
        ->where('hr_floor_id', $request->floor_id)
        ->pluck('hr_floor_name')
        ->first();

            $subsection_by_id= DB::table('hr_subsection')
        ->where('hr_subsec_id', $request->subSection)
        ->pluck('hr_subsec_name')
        ->first();



            $location_name = location_by_id()
        ->where('hr_location_id', $request->location)
        ->pluck('hr_location_name', 'hr_location_id')
        ->first();


            $unit_id=$request->unit;
            if ($request->unit==145) {
                $unit_id=[1,4,5];
            } else {
                $unit_id=[$request->unit];
            }


            if ($request->unit ==1 or $request->unit ==4 or $request->unit ==5 or $request->unit ==145) {
                $table='hr_attendance_mbm as b';
            }
            if ($request->unit==2) {
                $table='hr_attendance_ceil as b';
            }
            if ($request->unit==3) {
                $table='hr_attendance_aql as b';
            }
            if ($request->unit==8) {
                $table='hr_attendance_cew as b';
            }

            $data1 = DB::table('hr_basic_info_view as a')
        ->leftJoin($table, 'b.as_id', '=', 'a.as_id')
        ->leftJoin('hr_line as c', 'c.hr_line_id', '=', 'b.line_id')
        ->select(
            'a.associate_id',
            'a.as_name',
            'a.as_status_NAME',
            'a.as_ot_name',
            'a.as_gender',
            'a.hr_unit_name',
            'a.hr_designation_name',
            'a.hr_department_name',
            'a.hr_section_name',
            'a.hr_subsec_name',
            'a.hr_line_name as Default_line',
            DB::raw("group_concat(ifnull(c.hr_line_name,'N/A') separator ', ') as Current_line")
        )
        ->where('b.in_date', '>=', $request->from_date)
        ->where('b.in_date', '<=', $request->to_date)
        ->whereIn('a.hr_unit_id', $unit_id)
        ->when($request->otnonot != null, function ($q) use ($request) {
            $q->where('a.as_ot', $request->otnonot);
        })
        ->when(!empty($request['area']), function ($query) use ($request) {
            return $query->where('a.as_area_id', $request['area']);
        })
            ->when(!empty($request['location']), function ($query) use ($request) {
                return $query->where('a.as_location_id', $request['location']);
            })
            ->when(!empty($request['department']), function ($query) use ($request) {
                return $query->where('a.as_department_id', $request['department']);
            })
            ->when(!empty($request['line_id']), function ($query) use ($request) {
                return $query->where('a.as_line_id', $request['line_id']);
            })
            ->when(!empty($request['floor_id']), function ($query) use ($request) {
                return $query->where('a.as_floor_id', $request['floor_id']);
            })
            ->when(!empty($request['section']), function ($query) use ($request) {
                return $query->where('a.as_section_id', $request['section']);
            })
            ->when(!empty($request['subSection']), function ($query) use ($request) {
                return $query->where('a.as_subsection_id', $request['subSection']);
            })
        ->groupBy(
            'associate_id',
            'a.as_name',
            'a.as_status_NAME',
            'a.as_ot_name',
            'a.as_gender',
            'a.hr_unit_name',
            'a.hr_designation_name',
            'a.hr_department_name',
            'a.hr_section_name',
            'a.hr_subsec_name',
            'Default_line'
        )
        ->orderby('a.associate_id', 'asc', 'in_date', 'asc')
        ->get();


            // for excel download
            if ($request->export=='X') {
                $data1 = DB::table('hr_basic_info_view as a')
            ->leftJoin($table, 'b.as_id', '=', 'a.as_id')
            ->leftJoin('hr_line as c', 'c.hr_line_id', '=', 'b.line_id')
            ->select(
                'a.associate_id',
                'a.as_name',
                'a.as_doj',
                'a.as_status_NAME',
                'a.as_ot_name',
                'a.as_gender',
                'a.hr_unit_name',
                'a.hr_designation_name',
                'a.hr_department_name',
                'a.hr_section_name',
                'a.hr_subsec_name',
                'b.in_date',
                'b.in_time',
                'b.out_time',
                'b.hr_shift_code',
                'b.ot_hour',
                'b.late_status',
                'a.floor_name as Default_floor',
                'a.hr_line_name as Default_line',
                'b.line_id as Current_line',
                DB::raw("ifnull((c.hr_line_name),'N/A') as Current_line"),
                DB::raw("ifnull((c.hr_line_name_bn),'N/A') as Current_line_name_bn")
            )
            ->where('b.in_date', '>=', $request->from_date)
            ->where('b.in_date', '<=', $request->to_date)
            ->whereIn('a.hr_unit_id', $unit_id)
            ->when($request->otnonot != null, function ($q) use ($request) {
                $q->where('a.as_ot', $request->otnonot);
            })
            ->when(!empty($request['area']), function ($query) use ($request) {
                return $query->where('a.as_area_id', $request['area']);
            })
            ->when(!empty($request['location']), function ($query) use ($request) {
                return $query->where('a.as_location_id', $request['location']);
            })
            ->when(!empty($request['department']), function ($query) use ($request) {
                return $query->where('a.as_department_id', $request['department']);
            })
            ->when(!empty($request['line_id']), function ($query) use ($request) {
                return $query->where('a.as_line_id', $request['line_id']);
            })
            ->when(!empty($request['floor_id']), function ($query) use ($request) {
                return $query->where('a.as_floor_id', $request['floor_id']);
            })
            ->when(!empty($request['section']), function ($query) use ($request) {
                return $query->where('a.as_section_id', $request['section']);
            })
            ->when(!empty($request['subSection']), function ($query) use ($request) {
                return $query->where('a.as_subsection_id', $request['subSection']);
            })
            ->orderby('a.associate_id', 'asc', 'in_date', 'asc')
            ->get();

                $data = $data1->map(function ($q) {
                    return collect($q)->toArray();
                });

                return (new FastExcel($data))->download('Line change list.csv');
            } else {
                $data=$data1->toArray();
                // dd($data);
                return view(
                    'hr.reports.summary.linechangecurrent',
                    compact('data', 'input', 'unit_name', 'department_by_id', 'area_by_id', 'section_by_id', 'subsection_by_id', 'location_name', 'hr_line', 'hr_floor')
                )->render();
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            dd($bug);
            return 'error';
        }
    }


    public function managementinoutindex(Request $request)
    {
        try {
            $unitList  = Unit::where('hr_unit_status', '1')
        ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
        ->orderBy('hr_unit_name', 'desc')
        ->pluck('hr_unit_name', 'hr_unit_id');

            $department  = DB::table('hr_department')
        ->where('hr_department_name', '<>', 'ALL')
        ->orderBy('hr_department_name', 'asc')
        ->pluck('hr_department_name', 'hr_department_id');



            $locationList  = Location::where('hr_location_status', '1')
        ->whereIn('hr_location_id', auth()->user()->location_permissions())
        ->orderBy('hr_location_name', 'desc')
        ->pluck('hr_location_name', 'hr_location_id');
            $areaList  = DB::table('hr_area')->where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');

            $reportType = [
            'ot'=>'OT',
            'working_hour' => 'Working Hour',
            'leave' => 'Leave',
        ];
            if (auth()->user()->can('OT Report')) {
                $reportType['ot_levis'] = 'OT (Levis format)';
            }
            $reportType['left_resign'] = 'Left & Resign';


            $reportType['recruitment'] = 'Recruitment';
            $reportType['absentreason'] = 'Habitual Absent List';
            $reportType['latewarning'] = 'late warning Letter';
            $reportType['linechangedaily'] = 'Current Line Wise Manpower';

            $signatory_name= DB::table('hr_signatory_name')
        ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
        ->orderBy('as_name', 'asc')
        ->pluck('as_name', 'as_id');


            return view('hr/reports/summary/management_in_out_index', compact('unitList', 'areaList', 'locationList', 'reportType', 'signatory_name', 'department'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            dd($bug);
            return 'error';
        }
    }

    public function getSectionListByDepartmentID(Request $request)
    {
        $list = "<option value=\"\">Select Section Name </option>";
        if (!empty($request->department_id) && !empty($request->department_id)) {
            $lineList  = Section::where('hr_section_department_id', $request->department_id)
                ->where('hr_section_status', '1')
                ->pluck('hr_section_name', 'hr_section_id');

            foreach ($lineList as $key => $value) {
                $list .= "<option value=\"$key\">$value</option>";
            }
        }
        return $list;
    }



    public function managementinoutgetdata(Request $request)
    {
        try {
            $input=$request->all();

            $unit_name= DB::table('hr_unit')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->where('hr_unit_id', $request->unit)
            ->pluck('hr_unit_name')
            ->first();

            $department_by_id= DB::table('hr_department')
            ->where('hr_department_id', $request->department)
            ->pluck('hr_department_name')
            ->first();

            $area_by_id= DB::table('hr_area')
            ->where('hr_area_id', $request->area)
            ->pluck('hr_area_name')
            ->first();

            $section_by_id= DB::table('hr_section')
            ->where('hr_section_id', $request->section)
            ->pluck('hr_section_name')
            ->first();

            $hr_line= DB::table('hr_line')
            ->where('hr_line_id', $request->line_id)
            ->pluck('hr_line_name')
            ->first();

            $hr_floor= DB::table('hr_floor')
            ->where('hr_floor_id', $request->floor_id)
            ->pluck('hr_floor_name')
            ->first();

            $subsection_by_id= DB::table('hr_subsection')
            ->where('hr_subsec_id', $request->subSection)
            ->pluck('hr_subsec_name')
            ->first();



            $location_name = location_by_id()
            ->where('hr_location_id', $request->location)
            ->pluck('hr_location_name', 'hr_location_id')
            ->first();


            $unitList  = DB::table('hr_unit')
            ->when(!empty($request['unit']), function ($unitList) use ($request) {
                return $unitList->where('hr_unit_id', $request['unit']);
            })
            ->pluck('hr_unit_name', 'hr_unit_id');



            $data=[];
            foreach ($unitList as $key => $value) {
                $nData = DB::select('call hr_management_in_out
                    (
                    "'.$key.'"
                    ,"'.$request->department.'"
                    ,"'.$request->location.'"
                    ,"'.$request->from_date.'"
                    ,"'.$request->to_date.'"
                    ,"'.$request->section.'"
                    )
                    ');
                $data=array_merge($data, $nData);
            }


            if ($request->export=='E') {
                $data = collect($data)->map(function ($q) {
                    return collect($q)->toArray();
                });

                return (new FastExcel($data))->download('Management_in_out_data.csv');
            } else {
                $data=collect($data)
                ->groupBy('hr_department_name')->map(function ($q) {
                    return $q->groupBy('associate_id')->map(function ($p) {
                        return $p->sortBy('month');
                    });
                });


                return view(
                    'hr.reports.summary.management_in_out_loaddata',
                    compact('data', 'input', 'unit_name', 'department_by_id', 'area_by_id', 'section_by_id', 'subsection_by_id', 'location_name', 'hr_line', 'hr_floor')
                )->render();
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            dd($bug);
            return 'error';
        }
    }

    public function hourly_ot(Request $request)
    {
        try {
            $input=$request->all();

            $unit_name= DB::table('hr_unit')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->where('hr_unit_id', $request->unit)
            ->pluck('hr_unit_name')
            ->first();
            if ($request->unit=='145') {
                $unit_name='MBM+MBW+MBM2';
            }

            $department_by_id= DB::table('hr_department')
            ->where('hr_department_id', $request->department)
            ->pluck('hr_department_name')
            ->first();

            $area_by_id= DB::table('hr_area')
            ->where('hr_area_id', $request->area)
            ->pluck('hr_area_name')
            ->first();

            $section_by_id= DB::table('hr_section')
            ->where('hr_section_id', $request->section)
            ->pluck('hr_section_name')
            ->first();

            $hr_line= DB::table('hr_line')
            ->where('hr_line_id', $request->line_id)
            ->pluck('hr_line_name')
            ->first();

            $hr_floor= DB::table('hr_floor')
            ->where('hr_floor_id', $request->floor_id)
            ->pluck('hr_floor_name')
            ->first();

            $subsection_by_id= DB::table('hr_subsection')
            ->where('hr_subsec_id', $request->subSection)
            ->pluck('hr_subsec_name')
            ->first();


            $location_name = location_by_id()
            ->where('hr_location_id', $request->location)
            ->pluck('hr_location_name', 'hr_location_id')
            ->first();


            $u=$request->unit;
            if ($u==145) {
                $u='1,4,5';
            }

            if ($request->unit ==1 or $request->unit ==4 or $request->unit ==5 or $request->unit ==145) {
                $table='hr_daily_hour_wise_ot_view_mbm as a';
            }
            if ($request->unit==2) {
                $table='hr_daily_hour_wise_ot_view_ceil as a';
            }
            if ($request->unit==3) {
                $table='hr_daily_hour_wise_ot_view_aql as a';
            }
            if ($request->unit==8) {
                $table='hr_daily_hour_wise_ot_view_cew as a';
            }

            $year=date('Y', strtotime($request->from_date));
            $month=date('m', strtotime($request->from_date));

            $sec = DB::table('hr_as_basic_info as a')
                ->select(DB::raw('count(a.as_id) as count'), 'a.as_section_id')
                ->leftjoin('hr_monthly_salary as b', 'b.as_id', 'a.associate_id')
                ->where('b.year', $year)
                ->where('b.month', $month)
                ->where('a.as_ot', 1)
                ->where('a.as_unit_id', $u)
                ->where('b.emp_status', 1)
                ->groupBy('a.as_section_id')
                ->pluck('count', 'a.as_section_id');


            $data=DB::table($table)
            ->select(
                'a.hr_section_name',
                'a.as_section_id',
                DB::raw("count(a.as_id) as Present_Employee"),
                DB::raw("count(a.ot_0_hour) as ot_0_hour"),
                DB::raw("count(a.ot_1_hour) as ot_1_hour"),
                DB::raw("count(a.ot_2_hour) as ot_2_hour"),
                DB::raw("count(a.ot_3_hour) as ot_3_hour"),
                DB::raw("count(a.ot_4_hour) as ot_4_hour"),
                DB::raw("count(a.ot_5_hour) as ot_5_hour"),
                DB::raw("count(a.ot_6_hour) as ot_6_hour"),
                DB::raw("count(a.ot_7_hour) as ot_7_hour"),
                DB::raw("count(a.ot_8_hour) as ot_8_hour"),
                DB::raw("count(a.ot_9_hour) as ot_9_hour"),
                DB::raw("count(a.ot_10_hour) as ot_10_hour"),
                DB::raw("count(a.ot_11_hour) as ot_11_hour"),
                DB::raw("round(sum(a.ot_hour),2) as total_ot_hour"),
                DB::raw("round(avg(a.ot_hour),2) as avg_ot_hour")
            )
            ->where('a.in_date', $request->from_date)
            ->where('a.hr_unit_id', $u)
            ->groupBy('a.as_section_id')
            ->orderBy('a.hr_section_name', 'asc')
            ->get();

            $data=$data->map(function ($q) use ($sec) {
                $p = collect($q);
                $arr =(object)[];
                $arr->hr_section_name = $p['hr_section_name'];
                $arr->Present_Employee = $p['Present_Employee'];
                $arr->ot_0_hour = $p['ot_0_hour'];
                $arr->ot_1_hour = $p['ot_1_hour'];
                $arr->ot_2_hour = $p['ot_2_hour'];
                $arr->ot_3_hour = $p['ot_3_hour'];
                $arr->ot_4_hour = $p['ot_4_hour'];
                $arr->ot_5_hour = $p['ot_5_hour'];
                $arr->ot_6_hour = $p['ot_6_hour'];
                $arr->ot_7_hour = $p['ot_7_hour'];
                $arr->ot_8_hour = $p['ot_8_hour'];
                $arr->ot_9_hour = $p['ot_9_hour'];
                $arr->ot_10_hour = $p['ot_10_hour'];
                $arr->ot_11_hour = $p['ot_11_hour'];
                $arr->total_ot_hour = $p['total_ot_hour'];
                $arr->avg_ot_hour = $p['avg_ot_hour'];
                $arr->active_employee = $sec[$p['as_section_id']]??0;
                return $arr;
            });

            if ($request->export=='E') {
                $data = $data->map(function ($q) {
                    return collect($q)->toArray();
                });
                return (new FastExcel($data))->download('Employee Late List.csv');
            } else {
                return view(
                    'hr.reports.summary.hourly_ot',
                    compact('data', 'input', 'unit_name', 'department_by_id', 'area_by_id', 'section_by_id', 'subsection_by_id', 'location_name', 'hr_line', 'hr_floor')
                )->render();
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            dd($bug);
            return 'error';
        }
    }

    public function hourly_ot_lnf(Request $request)
    {
        try {
            $input=$request->all();

            $unit_name= DB::table('hr_unit')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->where('hr_unit_id', $request->unit)
            ->pluck('hr_unit_name')
            ->first();
            if ($request->unit=='145') {
                $unit_name='MBM+MBW+MBM2';
            }

            $department_by_id= DB::table('hr_department')
            ->where('hr_department_id', $request->department)
            ->pluck('hr_department_name')
            ->first();

            $area_by_id= DB::table('hr_area')
            ->where('hr_area_id', $request->area)
            ->pluck('hr_area_name')
            ->first();

            $section_by_id= DB::table('hr_section')
            ->where('hr_section_id', $request->section)
            ->pluck('hr_section_name')
            ->first();

            $hr_line= DB::table('hr_line')
            ->where('hr_line_id', $request->line_id)
            ->pluck('hr_line_name')
            ->first();

            $hr_floor= DB::table('hr_floor')
            ->where('hr_floor_id', $request->floor_id)
            ->pluck('hr_floor_name')
            ->first();

            $subsection_by_id= DB::table('hr_subsection')
            ->where('hr_subsec_id', $request->subSection)
            ->pluck('hr_subsec_name')
            ->first();


            $location_name = location_by_id()
            ->where('hr_location_id', $request->location)
            ->pluck('hr_location_name', 'hr_location_id')
            ->first();


            $u=$request->unit;
            if ($u==145) {
                $u='1,4,5';
            }

            if ($request->unit ==1 or $request->unit ==4 or $request->unit ==5 or $request->unit ==145) {
                $table='hr_attendance_mbm as a';
            }
            if ($request->unit==2) {
                $table='hr_attendance_ceil as a';
            }
            if ($request->unit==3) {
                $table='hr_attendance_aql as a';
            }
            if ($request->unit==8) {
                $table='hr_attendance_cew as a';
            }

            $data=DB::table($table)
            ->select(
                'b.hr_section_name',
                'b.as_section_id',
                'b.as_id',
                DB::raw("CASE WHEN sum(a.ot_hour)<=0 then  0  END AS wrk_hrs_upto_0"),
                DB::raw("CASE WHEN sum(a.ot_hour)>0 and sum(a.ot_hour)<=48 then  48  END AS wrk_hrs_upto_48"),
                DB::raw("CASE WHEN sum(a.ot_hour)>48 and sum(a.ot_hour)<=60 then 60   END AS wrk_hrs_upto_49_60"),
                DB::raw("CASE WHEN sum(a.ot_hour)>60 and sum(a.ot_hour)<=72 then 72   END AS wrk_hrs_upto_61_72"),
                DB::raw("CASE WHEN sum(a.ot_hour)>72 and sum(a.ot_hour)<=84 then 72   END AS wrk_hrs_upto_73_84"),
                DB::raw("CASE WHEN sum(a.ot_hour)>84 then 85   END AS wrk_hrs_Above_84"),
                DB::raw("round(sum(a.ot_hour),2) as total_ot_hour")
            )
            ->join('hr_basic_info_view as b', 'b.as_id', 'a.as_id')
            ->where('a.in_date', '>=', $request->from_date)
            ->where('a.in_date', '<=', $request->to_date)
            ->where('b.as_ot', 1)
            ->where('b.hr_unit_id', $u)
            ->where('b.as_status', '<>', 0)
            ->groupBy('a.as_id', 'b.as_section_id')
            ->orderBy('b.hr_section_name', 'asc')
            ->get();

            $year=date('Y', strtotime($request->from_date));
            $month=date('m', strtotime($request->from_date));

            $sec = DB::table('hr_as_basic_info as a')
                    ->select(DB::raw('count(a.as_id) as count'), 'a.as_section_id')
                    ->leftjoin('hr_monthly_salary as b', 'b.as_id', 'a.associate_id')
                    ->where('b.year', $year)
                    ->where('b.month', $month)
                    ->where('a.as_ot', 1)
                    ->where('a.as_unit_id', $u)
                    ->where('b.emp_status', 1)
                    ->groupBy('a.as_section_id')
                    ->pluck('count', 'a.as_section_id');

            $data = $data->groupBy("as_section_id")->map(function ($q) use ($sec) {
                $p = collect($q);
                $arr =(object)[];
                $arr->Present_employee=$p->count('as_id');
                $arr->total_ot_hour=$p->sum('total_ot_hour');
                $arr->avg_ot_hour=$p->avg('total_ot_hour');
                $arr->max_ot_hour=$p->max('total_ot_hour');
                $arr->wrk_hrs_upto_0 = $p->filter(function ($l5) {
                    return $l5->wrk_hrs_upto_0 != null;
                })->count();
                $arr->wrk_hrs_upto_48 = $p->filter(function ($l) {
                    return $l->wrk_hrs_upto_48 != null;
                })->count();
                $arr->wrk_hrs_upto_49_60 = $p->filter(function ($l1) {
                    return $l1->wrk_hrs_upto_49_60 != null;
                })->count();
                $arr->wrk_hrs_upto_61_72 = $p->filter(function ($l2) {
                    return $l2->wrk_hrs_upto_61_72 != null;
                })->count();
                $arr->wrk_hrs_upto_73_84 = $p->filter(function ($l3) {
                    return $l3->wrk_hrs_upto_73_84 != null;
                })->count();
                $arr->wrk_hrs_Above_84 = $p->filter(function ($l4) {
                    return $l4->wrk_hrs_Above_84 != null;
                })->count();

                $arr->Total_employee = $sec[$p->first()->as_section_id]??0;

                $arr->hr_section_name = $p->first()->hr_section_name;
                ////new column add by index check
                return $arr;
            });


            if ($request->export=='E') {
                $data = $data->map(function ($q) {
                    return collect($q)->toArray();
                });
                return (new FastExcel($data))->download('Hourly Ot Report.csv');
            } else {
                return view(
                    'hr.reports.summary.hourly_ot_lnf',
                    compact('data', 'input', 'unit_name', 'department_by_id', 'area_by_id', 'section_by_id', 'subsection_by_id', 'location_name', 'hr_line', 'hr_floor')
                )->render();
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            dd($bug);
            return 'error';
        }
    }
}