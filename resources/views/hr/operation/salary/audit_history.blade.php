<div class="panel">
	<div class="panel-body">
		<button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('report_section')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report" style="position: absolute; top: 16px; left: 65px;"><i class="las la-print"></i> </button>
		<div id="report_section" class="report_section">
			<style type="text/css" media="print">
				h4, h2, p{margin: 0;}
			</style>
			<style type="text/css">
              .table{
                width: 100%;
              }
              a{text-decoration: none;}
              .table-bordered {
                  border-collapse: collapse;
              }
              .table-bordered th,
              .table-bordered td {
                border: 1px solid #777 !important;
                padding:5px;
              }
              .no-border td, .no-border th{
                border:0 !important;
                vertical-align: top;
              }
              .f-14 th, .f-14 td, .f-14 td b{
                font-size: 14px !important;
              }
              .table thead th {
			    vertical-align: inherit;
			}
			.associate-right{
				cursor: pointer;
				position: relative;
			}
			.associate-right a{
				cursor: pointer;
				color:#089bab;
			}
			.view i {
			    font-size: 25px;
			    border: 1px solid #000;
			    border-radius: 3px;
			    padding: 0px 3px;
			}
			</style>
			@php
				$department = department_by_id();
				$designation = designation_by_id();
			@endphp
			<div class="top_summery_section">
				<div class="page-header">
		            <h2 style="margin:4px 10px; font-weight: bold; text-align: center;">Audit History </h2>
		            <h4  style="text-align: center;">Month : {{ date('M Y', strtotime($getHistory->year.'-'.$getHistory->month)) }} </h4>
		            @if($input['status'] == 1)
		            <h4  style="text-align: center;">Total Audited Employee's : {{ count($getIndividualAudit) }} </h4>
		            @endif
		            
		        </div>
			</div>

			<div class="content_list_section">
				
				<table class="table table-bordered table-hover table-head" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
					<thead>
						
              <tr>
                  <th>SL</th>
                  <th>Associate ID</th>
                  <th>Name</th>
                  <th>Designation</th>
                  <th>Department</th>
                  @if($input['status'] == 1)
                  <th>Audited by</th>
                  @else
                  <th>Comment</th>
                  @endif
              </tr>
          </thead>
          <tbody>
          @php $i = 0; @endphp
          @if($input['status'] == 1)
          	@if($getHistory != null && count($getIndividualAudit) > 0)
          		@foreach($getIndividualAudit as $indEmp)
	          		@php
	            		$employee = $getEmployee[$indEmp->as_id]??'';
	            		$month = $getHistory->year.'-'.$getHistory->month;
	            	@endphp
	            	@if($employee != '')
	            	<tr>
	            		<td>{{ ++$i }}</td>
		            	<td>
		            		<a class="" href='{{ url("hr/operation/job_card?associate=$employee->associate_id&month_year=$month") }}' target="_blank">{{ $employee->associate_id??'' }}</a>
		            	</td>
		            	<td>{{ $employee->as_name??'' }}</td>
		            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
		            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
		            	<td>{{ $indEmp->user->name??'' }}</td>
	            	</tr>
	            	@endif
          		@endforeach
          	@else
          	<tr>
	           	<td colspan="6" class="text-center"> No Record Found! </td>
	           </tr>
          	@endif
          @else
	          @if($getHistory != null && $getHistory->special_comment != null && json_decode($getHistory->special_comment) > 0)
	            @foreach(json_decode($getHistory->special_comment) as $history)
	            	@php
	            		$employee = $getEmployee[$history->as_id]??'';
	            		$month = $getHistory->year.'-'.$getHistory->month;
	            	@endphp
	            	@if($employee != '')
	            	<tr>
	            		<td>{{ ++$i }}</td>
		            	<td>
		            		<a class="" href='{{ url("hr/operation/job_card?associate=$employee->associate_id&month_year=$month") }}' target="_blank">{{ $employee->associate_id??'' }}</a>
		            	</td>
		            	<td>{{ $employee->as_name??'' }}</td>
		            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
		            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
		            	<td>{{ $history->comment }}</td>
	            	</tr>
	            	@endif
	            @endforeach
	          @else
	           <tr>
	           	<td colspan="6" class="text-center"> No Record Found! </td>
	           </tr> 
	          @endif
	        @endif
          </tbody>
		            
				</table>
					
			</div>
		</div>

		 
	</div>
</div>
