@php
    $urldata = http_build_query($input) . "\n";
    if(isset($input['group_unit'])){
        $groupUnit[] = ($input['group_unit']??'');
    }else{
        $groupUnit = $input['unit']??[];
    }
    $unit = unit_by_id();
    $designation = designation_by_id();
    $department = department_by_id();
@endphp

<a href='{{ url("hr/reports/bonus-report?$urldata&export=excel")}}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 21px; left: 72px;"><i class="fa fa-file-excel-o"></i></a>

<div class="content_list_section"  id="report_section">
    <style type="text/css">
        .page-data{
            border: 1px solid #d1d1d1;
            margin: 7px 0;
            padding: 5px 0;
        }
        .table th, .table td {
            padding: 5px;
        }
        .amount{text-align: right;width: 100px;display: inline-block;float: right;padding-right: 10px;}
    </style>
    <div class="page-header report_section">
        <h3 style="font-weight: bold; text-align: center;"></h3>
        <h3 style=" text-align: center;">End of Job Audit Report </h3>
        <h4 style="text-align: center;">Date: {{date('F d, Y', strtotime($input['from_date']))}} - {{date('F d, Y', strtotime($input['to_date']))}}</h4>
        <hr>
        <table border="0" width="100%" class="p-3">
            <tr>
                <td style="width: 18%">Audited</td>
                <td style="width:11.3333%; padding-right:1%;">: <span class="amount" >{{$audited}}/{{count($employees)}}</span></td>
                <td style="width: 18%">Resign</td>
                <td style="width:11.3333%; padding-right:1%;">: <span class="amount" >{{$summary->resign??0}} </span></td>
                <td style="width: 18%">Service Benefits</td>
                <td style="width:11.3333%; padding-right:1%;">: <span class="amount" >৳ {{bn_money(round($summary->service_benefits))}}</span></td>
            </tr>
            <tr>
                <td style="width: 18%"></td>
                <td style="width:11.3333%; padding-right:1%;">: <b><span class="amount" > </td>
                <td style="width: 18%"> Left</td>
                <td style="width:11.3333%; padding-right:1%;">: <span class="amount" >{{$summary->left??0}} </span></td>
                <td style="width: 18%;font-weight:  normal;">EarnLeave Benefits</td>
                <td style="width:11.3333%; padding-right:1%;">: <span class="amount" >৳ {{bn_money(round($summary->earn_leave_amount))}}</span></td>
            </tr >
            <tr style=";">
                <td style="width: 18%">Total Employee</td>
                <td style="width:11.3333%; padding-right:1%;">: <b><span class="amount" >{{count($employees)}} </td>
                <td style="width: 18%">Terminate/Retirment/Death</td>
                <td style="width:11.3333%; padding-right:1%;">: <span class="amount" >{{count($employees) - ($summary->resign??0) - ($summary->left??0)}}</span></td>
                <td style="width: 18%;font-weight: bold">Total Amount</td>
                <td style="width:11.3333%; padding-right:1%;font-weight: bold">: <span class="amount">৳ {{bn_money(round($summary->total_amount))}}</span></td>
            </tr >
            

        </table>
        
    </div>
    <table class="table table-bordered table-hover table-head mt-3">
        <thead>
            <tr>
                <th>Sl</th>
                <th>Associate ID</th>
                <th>Name</th>
                <th>Unit</th>
                <th>Designation</th>
                <th>Department</th>
                <th>Type</th>
                <th>Date</th>
                <th>Last Working</th>
                <th>Payment</th>
                <th>Preview</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @if(count($employees) > 0)
                @foreach($employees as $key => $eob)
                <tr>
                    <td>{{$key+1}}</td>
                    <td><a href='{{ url("hr/recruitment/employee/show/".$eob->associate_id) }}' target="_blank">{{ $eob->associate_id }}</a></td>
                    <td>{{ $eob->employee->as_name }}
                    </td>
                    <td>{{ $unit[$eob->employee->as_unit_id]['hr_unit_short_name']??'' }}</td>
                    <td>{{ $designation[$eob->employee->as_designation_id]['hr_designation_name']??'' }}</td>
                    <td>{{ $department[$eob->employee->as_department_id]['hr_department_name']??'' }}</td>
                    <td>{{ $eob->type }}</td>
                    <td style="white-space: nowrap;">{{ $eob->status_date?? '' }}</td>
                    <td style="white-space: nowrap;">{{ $eob->salary_date?? '' }}</td>
                    <td class="text-right">{{ bn_money($eob->total_amount??0) }}</td>
                    
                    <td style="white-space: nowrap;">
                        <button class="btn btn-primary btn-xs" id="benefits"  data-id="{{$eob->id}}" data-toggle="tooltip" data-placement="top" title="" data-original-title="View End of Job Benefits"><i class="las la-gifts"></i></button> &nbsp;<button class="btn btn-warning btn-xs" id="partial-salary" data-emp="{{$eob->associate_id}}"  data-salary-date="{{$eob->salary_date}}" data-toggle="tooltip" data-placement="top" title="" data-original-title="View Partial Salary"><i class="las la-file-invoice-dollar"></i></button>
                    </td>
                    <td class="text-center action-{{$eob->id}}" >
                        @if($eob->audit)
                            @if($eob->audit->status == 1)
                                <i class="las la-check-circle text-success" style="font-size: 16px;cursor:pointer;" data-toggle="tooltip" data-placement="top" title="" data-original-title="Audited by ...... at {{$eob->audit->date}}"></i>
                            @elseif($eob->audit->status == 2)
                                <i class="las la-times-circle text-danger" style="font-size: 16px;cursor" id="crossReason" data-id="{{$eob->id}}" data-message="{{$eob->audit->comment}}" data-date="{{$eob->audit->date}}"></i>
                            @endif
                        @else
                            <button class="btn btn-primary btn-xs" id="auditAction"  data-id="{{$eob->id}}" data-name="{{ $eob->employee->as_name }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Audit Process">Audit</button>
                        @endif
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="5"></td>
                    <td>Total Employee</td>
                    <td>{{count($employees)}}</td>
                    <td colspan="2">Total Payment</td>
                    <td>{{bn_money($summary->total_amount)}}</td>
                    <td></td>
                </tr>
            @else
                <tr>
                    <td colspan="10">No record found</td>
                </tr>
            @endif
        </tbody>
        
    </table>
</div>
    

