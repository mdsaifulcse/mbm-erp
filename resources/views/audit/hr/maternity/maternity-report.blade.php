<div class="panel">
	<div class="panel-body">
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
				<h3 style=" text-align: center;">Maternity Leave Audit Report </h3>
                <h4 style="text-align: center;">Date: {{date('F d, Y', strtotime($input['from_date']))}} - {{date('F d, Y', strtotime($input['to_date']))}}</h4>
                <hr>
	            <table border="0" width="100%" class="p-3">
            		<tr>
            			<td style="width: 18%">Audited</td>
            			<td style="width:11.3333%; padding-right:1%;">: <span class="amount" >{{$audited}}/{{count($employees)}}</span></td>
            			<td style="width: 18%">OT Employee</td>
            			<td style="width:11.3333%; padding-right:1%;">: <span class="amount" >{{$summary->ot}}</span></td>
            			<td style="width: 18%">First Payment</td>
            			<td style="width:11.3333%; padding-right:1%;">: <span class="amount" >৳ {{bn_money(round($summary->first_payment))}}</span></td>
            		</tr>
            		<tr>
            			<td style="width: 18%"> 3rd Child Employee</td>
            			<td style="width:11.3333%; padding-right:1%;">: <span class="amount" >{{$summary->third_child}} </span></td>
            			<td style="width: 18%">Non OT Employee</td>
            			<td style="width:11.3333%; padding-right:1%;">: <b><span class="amount" >{{$summary->non_ot}} </td>
            			<td style="width: 18%;font-weight:  normal;">Second Payment</td>
            			<td style="width:11.3333%; padding-right:1%;">: <span class="amount" >৳ {{bn_money(round($summary->second_payment))}}</span></td>
            		</tr >
            		<tr style="font-weight: bold;">
            			<td style="width: 18%">For 3rd Child Payment</td>
            			<td style="width:11.3333%; padding-right:1%;">: <span class="amount" >৳ {{bn_money(round($summary->third_child_payment))}}</span></td>
            			<td style="width: 18%">Total Employee</td>
            			<td style="width:11.3333%; padding-right:1%;">: <b><span class="amount" >{{count($employees)}} </td>
            			<td style="width: 18%;">Total Payment</td>
            			<td style="width:11.3333%; padding-right:1%;">: <span class="amount">৳ {{bn_money(round($summary->total_payment))}}</span></td>
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
                        <th>Leave Start</th>
                        <th>EDD</th>
                        <th>Payment</th>
                        <th>Preview</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php $payment = 0; @endphp
                    @if(count($employees) > 0)
                        @foreach($employees as $key => $leave)
                            @php 
                            	$payment += $leave->payment->total_payment;
                            	$employee_history = json_decode($leave->employee_history); 
                    
                            @endphp
                        <tr>
                            <td>{{$key+1}}</td>
                            <td><a href='{{ url("hr/recruitment/employee/show/".$leave->associate_id) }}' target="_blank">{{ $leave->associate_id }}</a></td>
                            <td>{{ $leave->employee->as_name }}
                            </td>
                            <td>{{ $unit[$leave->unit_id]['hr_unit_short_name']??'' }}</td>
                            <td>{{ $designation[$employee_history->as_designation_id]['hr_designation_name']??'' }}</td>
                            <td>{{ $department[$employee_history->as_department_id]['hr_department_name']??'' }}</td>
                            <td style="white-space:nowrap;">{{ $leave->leave_from->format('Y-m-d')?? '' }}</td>
                            <td style="white-space:nowrap;">{{ $leave->edd?? '' }}</td>
                            <td class="text-right">{{ bn_money($leave->payment->total_payment??0) }}</td>
                            
                            <td style="white-space: nowrap;">
                            	<button class="btn btn-primary btn-xs" id="benefits"  data-id="{{$leave->id}}" data-toggle="tooltip" data-placement="top" title="" data-original-title="View Maternity Benefits"><i class="las la-gifts"></i></button> &nbsp;<button class="btn btn-warning btn-xs" id="partial-salary"  data-emp="{{$leave->associate_id}}"  data-salary-date="{{$leave->leave_from->format('Y-m-d')}}" data-toggle="tooltip" data-placement="top" title="" data-original-title="View Maternity Partial Salary"><i class="las la-file-invoice-dollar"></i></button>
                            </td>
                            <td class="text-center action-{{$leave->id}}" >
                                @if($leave->audit)
                                    @if($leave->audit->status == 1)
                                        <i class="las la-check-circle text-success" style="font-size: 16px" data-toggle="tooltip" data-placement="top" title="" data-original-title="Audited by ......"></i>
                                    @elseif($leave->audit->status == 2)
                                        <i class="las la-times-circle text-danger" style="font-size: 16px" id="crossReason" data-message="{{$leave->audit->comment}}" data-date="{{$leave->audit->date}}"></i>
                                    @endif
                                @else
                                    <button class="btn btn-primary btn-xs" id="auditAction"  data-id="{{$leave->id}}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Audit Process">Audit</button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="3"></td>
                            <td colspan="2">Total Employee</td>
                            <td>{{count($employees)}}</td>
                            <td colspan="2">Total Payment</td>
                            <td style="text-align:right;">{{bn_money($payment)}}</td>
                            <td colspan="2"></td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="10">No record found</td>
                        </tr>
                    @endif
                </tbody>
                
            </table>
		</div>
	</div>
</div>

