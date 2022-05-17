<div class="panel">
    <div class="panel-body">
        <div id="report_section" class="report_section">
            
            @php
                $formatHead = explode('_',$format);
            @endphp
            <div class="top_summery_section">
                
                <div class="page-header">
                    <h2 style="margin:4px 10px; font-weight: bold; text-align: center;">Salary @if($input['report_format'] == 0) Details Summary @else Summary @endif Report </h2>
                    <h4  style="text-align: center;">Month : {{ date('F', strtotime($input['year_month'])) }} </h4>
                </div>
                
                
            </div>
            <div class="content_list_section">
                @if($input['report_format'] == 0)
                    <table class="table table-bordered table-hover table-head table-responsive">
                    @if($format == 'as_unit_id')
                        @php $i = 0; @endphp
                        <thead>
              <tr>
                  <th>Sl</th>
                  <th>Associate ID</th>
                  <th>Name</th>
                  <th>Unit</th>
                  <th>Designation</th>
                  <th>Department</th>
                  <th>Section</th>
                  <th>DOJ</th>
                  <th>YOS</th>
                  <th>Grade</th>
                  <th>Salary</th>
                  
              </tr>
            </thead>
                    @endif
                    @foreach($uniqueGroup as $group => $employees)
                    
                        @if($format != 'as_unit_id')
                        <thead>
                            @if(count($employees) > 0)
                <tr>
                    @php
                        if($format == 'as_unit_id'){
                            $head = 'Unit';
                            $body = $unit[$group]['hr_unit_name']??'';
                        }elseif($format == 'as_location'){
                            $head = 'Location';
                            $body = $location[$group]['hr_location_name']??'';
                        }elseif($format == 'as_line_id'){
                            $head = 'Line';
                            $body = $line[$group]['hr_line_name']??'';
                        }elseif($format == 'as_floor_id'){
                            $head = 'Floor';
                            $body = $floor[$group]['hr_floor_name']??'';
                        }elseif($format == 'as_department_id'){
                            $head = 'Department';
                            $body = $department[$group]['hr_department_name']??'';
                        }elseif($format == 'as_designation_id'){
                            $head = 'Designation';
                            $body = $designation[$group]['hr_designation_name']??'';
                        }elseif($format == 'as_section_id'){
                            $head = 'Section';
                            $body = $section[$group]['hr_section_name']??'';
                        }elseif($format == 'as_subsection_id'){
                            $head = 'Sub Section';
                            $body = $subSection[$group]['hr_subsec_name']??'';
                        }else{
                            $head = '';
                        }
                    @endphp
                    @if($head != '')
                    <th colspan="2">{{ $head }}</th>
                    <th colspan="14">{{ $body }}</th>
                    @endif
                </tr>
              @endif
              <tr>
                  <th>Sl</th>
                  <th>Associate ID</th>
                  <th>Name</th>
                  <th>Unit</th>
                  <th>Designation</th>
                  <th>Department</th>
                  <th>Section</th>
                  <th>DOJ</th>
                  <th>YOS</th>
                  <th>Grade</th>
                  <th>Salary</th>
                  
              </tr>
              
            </thead>
            @php $i = 0; @endphp
            @endif
            <tbody>
            @php $month = $input['year_month']; @endphp
            @if(count($employees) > 0)
                @foreach($employees as $employee)
                    @php
                        $designationName = $designation[$employee->as_designation_id]['hr_designation_name']??'';
                    @endphp
                    
                        <tr>
                            <td>{{ ++$i }}</td>
                            
                            <td>
                                {{ $employee->as_id }}
                            </td>
                            <td>
                                <b>{{ $employee->as_name }}</b>
                            </td>
                            <td>{{ $unit[$employee->as_unit_id]['hr_unit_short_name']??'' }}</td>
                            <td>{{ $designationName }}</td>

                            <td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
                            <td>{{ $section[$employee->as_section_id]['hr_section_name']??'' }}</td>
                            <td>{{ date('d/m/Y', strtotime($employee->as_doj)) }}</td>
                            <td>{{ Carbon\Carbon::createFromFormat('Y-m-d', $employee->as_doj)->diff(Carbon\Carbon::now())->format('%y.%m') }}</td>
                            <td>{{ $designation[$employee->as_designation_id]['hr_designation_grade']??'' }}</td>
                            <td>
                                {{ bn_money($employee->gross) }}
                            </td>
                            
                        </tr>
                    
                @endforeach
            @else
                <tr>
                    <td colspan="7" class="text-center">No Employee Found!</td>
                </tr>
            @endif
                <tr style="border:0 !important;"><td colspan="7" style="border: 0 !important;height: 20px;"></td> </tr>
            </tbody>
                        
                    @endforeach
                </table>
                @elseif(($input['report_format'] == 1 && $format != null))
                    @php
                        if($format == 'as_unit_id'){
                            $head = 'Unit';
                        }elseif($format == 'as_line_id'){
                            $head = 'Line';
                        }elseif($format == 'as_floor_id'){
                            $head = 'Floor';
                        }elseif($format == 'as_department_id'){
                            $head = 'Department';
                        }elseif($format == 'as_designation_id'){
                            $head = 'Designation';
                        }elseif($format == 'as_section_id'){
                            $head = 'Section';
                        }elseif($format == 'as_subsection_id'){
                            $head = 'Sub Section';
                        }else{
                            $head = '';
                        }
                    @endphp
                    <table class="table table-bordered table-hover table-head" border="1" cellpadding="5">
                        <!-- custom design for all-->
                        
                        <thead>
                            <tr class="text-center">
                                <th rowspan="2">SL.</th>
                                <th rowspan="2"> {{ $head }} Name</th>
                                <th colspan="3">No. of Employee</th>
                                
                                <th rowspan="2">Salary (BDT)</th>
                            </tr>
                            <tr class="text-center">
                                <th>Non OT</th>
                                <th>OT</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=0; @endphp
                            @if(count($uniqueGroup) > 0)
                            @foreach($uniqueGroup as $group => $groupSal)
                            
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>
                                    @php
                                        
                                        if($format == 'as_unit_id'){
                                            $body = $unit[$group]['hr_unit_name']??'';
                                            $exPar = '&selected='.$unit[$group]['hr_unit_id']??'';
                                        }elseif($format == 'as_location'){
                                            $body = $location[$group]['hr_location_name']??'';
                                            $exPar = '&selected='.$location[$group]['hr_location_name']??'';
                                        }elseif($format == 'as_line_id'){
                                            $body = $line[$group]['hr_line_name']??'';
                                            $exPar = '&selected='.$body;
                                        }elseif($format == 'as_floor_id'){
                                            $body = $floor[$group]['hr_floor_name']??'';
                                            $exPar = '&selected='.$body;
                                        }elseif($format == 'as_department_id'){
                                            $body = $department[$group]['hr_department_name']??'';
                                            $exPar = '&selected='.$department[$group]['hr_department_id']??'';
                                        }elseif($format == 'as_designation_id'){
                                            $body = $designation[$group]['hr_designation_name']??'';
                                            $exPar = '&selected='.$designation[$group]['hr_designation_id']??'';
                                        }elseif($format == 'as_section_id'){
                                            $depId = $section[$group]['hr_section_department_id']??'';
                                            $seDeName = $department[$depId]['hr_department_name']??'';
                                            $seName = $section[$group]['hr_section_name']??'';
                                            $body = $seDeName.' - '.$seName;
                                            $exPar = '&selected='.$section[$group]['hr_section_id']??'';
                                        }elseif($format == 'as_subsection_id'){
                                            $body = $subSection[$group]['hr_subsec_name']??'';
                                            $exPar = '&selected='.$subSection[$group]['hr_subsec_id']??'';
                                        }else{
                                            $body = 'N/A';
                                            $exPar = '';
                                        }
                                        $secUrl = $exPar;
                                    @endphp
                                    {{ $body }}
                                </td>
                                <td style="text-align: center;">
                                    {{ $groupSal->nonot }}
                                </td>

                                <td style="text-align: center;">
                                    {{ $groupSal->ot }}
                                </td>
                                <td style="text-align: center;">
                                    {{ $groupSal->nonot + $groupSal->ot }}
                                    
                                </td>
                                
                                <td class="text-right" style="font-weight: bold">
                                    {{ bn_money(round($groupSal->grossPayable)) }}
                                </td>
                                
                            </tr>
                            @endforeach
                             <tr>
                                <td></td>
                                <td class="text-center fwb"> Total </td>
                                <td class="text-center fwb">{{ $summary->totalNonot }}</td>
                                <td class="text-center fwb">{{ $summary->totalOt }}</td>
                                <td class="text-center fwb">{{ $summary->totalEmployees }}</td>
                                
                                <td class="text-right fwb">{{ bn_money(round($summary->totalGrossPay)) }}</td>
                                
                            </tr>
                             
                            @else
                            <tr>
                    <td colspan="6" class="text-center">No Data Found!</td>
                </tr>
                            @endif
                        </tbody>
                    </table>
                @endif
            </div>
            
        </div>

        {{-- modal --}}
        
    </div>
</div>
