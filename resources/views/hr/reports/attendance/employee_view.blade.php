@php
	$unit = unit_by_id();
	$location = location_by_id();
	$floor = floor_by_id();
	$line = line_by_id();
	$area = area_by_id();
	$department = department_by_id();
	$designation = designation_by_id();
	$section = section_by_id();
	$subSection = subSection_by_id();

@endphp
<div class="panel">
	<div class="panel-body">
		@php
			$urldata = http_build_query($request) . "\n";
		@endphp
		<a href='{{ url("hr/reports/get_att_summary_report?$urldata")}}&export=excel' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute;top: 15px;left: 60px;"><i class="fa fa-file-excel-o"></i></a>
		<div class="report_section" id="report_section">
			
			<div class="top_summery_section">
				<div class="page-header">
		            <h4 style="margin:0 10px; font-weight: bold; text-align: center;">Employee Attendance Status </h4>
		            <p style="text-align: center;">
			            @if(isset($request['department']))
			            	@php $dept = $request['department']  @endphp
			            	<b>Department:</b> {{$department[$dept]['hr_department_name']}}
			            @endif
			            @if(isset($request['section']))
			            	@php $sec = $request['section']  @endphp
			            	<b>section:</b> {{$section[$sec]['hr_section_name']}}
			            @endif
			            @if(isset($request['subSection']))
			            	@php $subsec = $request['subSection']  @endphp
			            	<b>Sub-Section:</b> {{$subSection[$subsec]['hr_subsec_name']}}
			            @endif
		            </p>
		            <p class="mb-3 text-center">Date: {{$date}}</p>
		            <p style="text-align: center">
		            	@if(isset($request['otnonot']))
		            		<b>OT Status:</b> @if($request['otnonot'] == 1) OT @else NonOT @endif
		            	@endif
		            	@if(isset($request['status']))
		            		<b>Attendance Status:</b> {{$request['status']}}
		            	@endif
		            </p>
		            <ul class="color-bar">
                        <li><span class="color-label bg-default"></span><span class="lib-label"> Present </span></li>
                        <li><span class="color-label bg-danger"></span><span class="lib-label"> Absent </span></li>
                        <li><span class="color-label bg-warning"></span><span class="lib-label"> Holiday </span></li>
                        <li><span class="color-label bg-primary"></span><span class="lib-label"> Leave</span></li>
                    </ul>
					<table class="table table-bordered table-hover table-head table-responsive" border="1" style="    display: table;">
						<thead>
			                <tr>
			                    <th >Sl</th>
			                    <th >Associate ID</th>
			                    <th >Name & Phone</th>
			                    <th >Oracle ID</th>
			                    <th >Designation</th>
			                    <th >Department</th>
			                    <th >Section</th>
			                    <th >Sub Section</th>
			                    <th >Floor</th>
			                    <th >Line</th>
			                    <th >Shift</th>
			                    <th >Status</th>
			                </tr>
			            </thead>
			            <tbody>
			            	@php $i = 0; $month = date('Y-m', strtotime($date)); @endphp
							@foreach($att as $key => $employee)
				            	<tr>
				            		<td>{{ ++$i }}</td>
					            	<td><a class="job_card" data-name="{{ $employee->as_name }}" data-associate="{{ $employee->associate_id }}" data-month-year="{{ $month }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Job Card">{{ $employee->associate_id }}</a></td>
					            	<td>
					            		<b>{{ $employee->as_name }}</b>
					            		<p>{{ $employee->as_contact }}</p>
					            	</td>
					            	<td>{{ $employee->as_oracle_code }}</td>
					            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
					            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
					            	<td>{{ $section[$employee->as_section_id]['hr_section_name']??'' }}</td>
					            	<td>{{ $subSection[$employee->as_subsection_id]['hr_subsec_name']??'' }}</td>
					            	<td>{{ $floor[$employee->as_floor_id]['hr_floor_name']??'' }}</td>
					            	<td>{{ $line[$employee->as_line_id]['hr_line_name']??'' }}</td>
					            	<td>
					            		{{$employee->as_shift_id}}
					            	</td>
					            	<td>
					            		@if($employee->status == 'P') Present
					            		@elseif($employee->status == 'H') <span class="text-center d-block bg-warning">Holiday</span> 
					            		@elseif($employee->status == 'L') <span class="text-center d-block bg-primary">Leave</span>
					            		@elseif($employee->status == 'A') <span class="text-center d-block bg-danger">Absent</span>
					            		@endif

					            	</td>
				            	</tr>
							@endforeach
			            	<tr>
			            		<td colspan="9" style="text-align: right;"><b>Total</b></td>
			            		<td colspan="2"><b>{{count($att)}}</b></td>
			            	</tr>
			            </tbody>
					</table>
				</div>
				
			</div>
		</div>

	</div>
</div>
