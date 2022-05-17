<div class="panel">
    <div class="panel-body">
        <div id="report_section" class="report_section">
            
            @php
                $formatHead = explode('_',$format);
            @endphp
            <div class="top_summery_section">
                
                <div class="page-header">
                    <h2 style="margin:4px 10px; font-weight: bold; text-align: center;">Incentive Bonus  @if($input['report_format'] == 0) Details @else Summary @endif Report Report </h2>
                    @if($input['date_type'] == 'month')
                    <h4  style="text-align: center;">Month : {{ date('F Y', strtotime($input['month_year'])) }} </h4>
                    @else
                    <h4  style="text-align: center;">Date : {{ date('Y-m-d', strtotime($input['from_date'])) }} To {{ date('Y-m-d', strtotime($input['to_date'])) }} </h4>
                    @endif
                </div>
                
                
            </div>
            <div class="content_list_section">
                @if($input['report_format'] == 0)
                    @foreach($uniqueGroup as $group => $employees)
                    
                    <table class="table table-bordered table-hover table-head" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
                        <thead style="font-weight: bold; font-size:14px; text-align: center;">
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
                                <th style=" font-weight: bold; font-size:13px;" colspan="2">{{ $head }}</th>
                                <th style=" font-weight: bold; font-size:13px;" colspan="13">{{ $body }}</th>
                                @endif
                            </tr>
                            @endif
                            <tr>
                                <th style=" font-weight: bold; font-size:13px;">Sl</th>
                                <th style=" font-weight: bold; font-size:13px;">ID</th>
                                <th style=" font-weight: bold; font-size:13px;">Name</th>
                                <th style=" font-weight: bold; font-size:13px;">Designation</th>
                                <th style=" font-weight: bold; font-size:13px;">Department</th>
                                <th style=" font-weight: bold; font-size:13px;">Section</th>
                                <th style=" font-weight: bold; font-size:13px;">Sub Section</th>
                                <th style=" font-weight: bold; font-size:13px;">Floor</th>
                                <th style=" font-weight: bold; font-size:13px;">Line</th>
                                <th style=" font-weight: bold; font-size:13px;">Payment Method</th>
                                <th style=" font-weight: bold; font-size:13px;">Account Number</th>
                                <th style=" font-weight: bold; font-size:13px;">Amount</th>
                                <th style=" font-weight: bold; font-size:13px;">Days</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php $i = 0; @endphp
                        @if(count($employees) > 0)
                            @foreach($employees as $employee)
                                @php
                                    $designationName = $designation[$employee->as_designation_id]['hr_designation_name']??'';
                                @endphp
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $employee->associate_id }}</td>
                                    <td>{{ $employee->as_name }}</td>
                                    <td>{{ $designationName }}</td>
                                    <td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
                                    <td>{{ $section[$employee->as_section_id]['hr_section_name']??'' }}</td>
                                    <td>{{ $subSection[$employee->as_subsection_id]['hr_subsec_name']??'' }}</td>
                                    <td>{{ $floor[$employee->as_floor_id]['hr_floor_name']??'' }}</td>
                                    <td>{{ $line[$employee->as_line_id]['hr_line_name']??'' }}</td>
                                    
                                    <td>
                                        @if($employee->bank_payable == 0 && $employee->cash_payable > 0)
                                            Cash
                                        @elseif($employee->bank_payable > 0)
                                        <b>{{ $employee->bank_name }}</b>
                                        @endif
                                    </td>
                                    <td>
                                        @if($employee->bank_payable > 0)
                                        <b>{{ ($employee->bank_payable > 0)?$employee->bank_no:'' }}</b>
                                        @endif
                                    </td>
                                    <td>
                                        {{ ($employee->amount) }}
                                    </td>
                                    <td>{{ $employee->count }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="13" class="text-center">No Employee Found!</td>
                            </tr>
                        @endif
                        </tbody>
                        
                    </table>
                    @endforeach
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
                    <table class="table table-bordered table-hover table-head" border="1" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left" cellpadding="5">
                        <!-- custom design for all-->
                        
                            <thead>
                                <tr class="text-center">
                                    <th>SL.</th>
                                    <th> {{ $head }} Name</th>
                                    <th>Non OT</th>
                                    <th>OT</th>
                                    <th>Total Employee</th>
                                    <th>Non OT Holder Amount</th>
                                    <th>OT Holder Amount</th>
                                    <th>Total Amount</th>
                                    <th>Cash Amount</th>
                                    <th>Bank Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Due Amount</th>
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
                                            }elseif($format == 'as_line_id'){
                                                $body = $line[$group]['hr_line_name']??'';
                                                
                                            }elseif($format == 'as_floor_id'){
                                                $body = $floor[$group]['hr_floor_name']??'';
                                                
                                            }elseif($format == 'as_department_id'){
                                                $body = $department[$group]['hr_department_name']??'';
                                                
                                            }elseif($format == 'as_designation_id'){
                                                $body = $designation[$group]['hr_designation_name']??'';
                                                
                                            }elseif($format == 'as_section_id'){
                                                $depId = $section[$group]['hr_section_department_id']??'';
                                                $seDeName = $department[$depId]['hr_department_name']??'';
                                                $seName = $section[$group]['hr_section_name']??'';
                                                $body = $seDeName.' - '.$seName;
                                                
                                            }elseif($format == 'as_subsection_id'){
                                                $body = $subSection[$group]['hr_subsec_name']??'';
                                                
                                            }else{
                                                $body = 'N/A';
                                            }
                                        @endphp
                                        {{ ($body == null)?'N/A':$body }}
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
                                    <td class="text-right">
                                        {{ round($groupSal->nonotAmount) }}
                                    </td>

                                    <td class="text-right">
                                        {{ round($groupSal->otAmount) }}
                                    </td>
                                    <td class="text-right">
                                        {{ round($groupSal->incentiveAmount) }}
                                    </td>
                                    
                                    <td class="text-right">
                                        {{ round($groupSal->cashPayable) }}
                                    </td>
                                    <td class="text-right">
                                        {{ round($groupSal->bankPayable) }}
                                    </td>
                                    <td class="text-right">
                                        {{ round($groupSal->paidAmount) }}
                                    </td>
                                    <td class="text-right">
                                        {{ round($groupSal->incentiveAmount - $groupSal->paidAmount) }}
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td></td>
                                    <td class="text-center fwb" style="font-weight: bold; font-size:13px;"> Total </td>
                                    <td class="text-center fwb" style="font-weight: bold; font-size:13px;">{{ $summary->totalNonot }}</td>
                                    <td class="text-center fwb" style="font-weight: bold; font-size:13px;">{{ $summary->totalOt }}</td>
                                    <td class="text-center fwb" style="font-weight: bold; font-size:13px;">{{ $summary->totalEmployees }}</td>
                                    <td class="text-right fwb" style="font-weight: bold; font-size:13px;">{{ round($summary->totalNonotAmount) }}</td>
                                    <td class="text-right fwb" style="font-weight: bold; font-size:13px;">{{ round($summary->totalOtAmount) }}</td>
                                    <td class="text-right fwb" style="font-weight: bold; font-size:13px;">{{ round($summary->totalIncentive) }}</td>
                                    
                                    <td class="text-right fwb" style="font-weight: bold; font-size:13px;">{{ round($summary->totalCash) }}</td>
                                    <td class="text-right fwb" style="font-weight: bold; font-size:13px;">{{ round($summary->totalBank) }}</td>
                                    <td class="text-right fwb" style="font-weight: bold; font-size:13px;">{{ round($summary->totalPaidAmount) }}</td>
                                    <td class="text-right fwb" style="font-weight: bold; font-size:13px;">{{ round($summary->totalIncentive - $summary->totalPaidAmount) }}</td>
                                </tr>
                                
                                @else
                                <tr>
                                    <td colspan="14" class="text-center">No Data Found!</td>
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
