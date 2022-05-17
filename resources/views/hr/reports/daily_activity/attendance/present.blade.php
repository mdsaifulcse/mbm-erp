<div class="panel">
	<div class="panel-body">
		<div class="report_section" id="report_section">
			@php
				$formatHead = explode('_',$format);
				if(!isset($input['export'])){
					$urldata = http_build_query($input) . "\n";
					$jsonUrl = json_encode($urldata);
				}
			@endphp

			@if(!isset($input['export']))
			<a href='{{ url("hr/reports/daily-attendance-activity-report?$urldata")}}&export=excel' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 15px; left: 65px;"><i class="fa fa-file-excel-o"></i></a>
			@endif
			<div class="top_summery_section">
				
				<div class="page-header">
		            <h2 style="margin:4px 10px; font-weight: bold; text-align: center; text-transform: capitalize;">
		            	{{ $input['report_type'] }} @if($input['report_format'] == 0) Details @else Summary @endif Report 
		            </h2>
		            <table class="table no-border f-16" border="0">
		            	<tr>
		            		<td style="width: 33%;">
		            			@if($input['unit'] != null)
		            				Unit <b>: {{ $input['unit'] == 145?'MBM + MBF + MBM 2':$unit[$input['unit']]['hr_unit_name'] }}</b> <br>
			        			@endif
			        			@if($input['location'] != null)
			        			Location <b>: {{ $location[$input['location']]['hr_location_name'] }}</b> <br>
			        			@endif
		            			@if($input['area'] != null)
		            			Area 
		                			<b>: {{ $area[$input['area']]['hr_area_name'] }}</b> <br>
		                		@endif
		                		@if($input['department'] != null)
		                			Department 
		                			<b>: {{ $department[$input['department']]['hr_department_name'] }}</b> <br>
		                		@endif
		                		@if($input['section'] != null)
		                		Section 
		                			<b>: {{ $section[$input['section']]['hr_section_name'] }}</b>
		                		@endif
		            		</td>
		            		<td style="width: 33%;text-align: center;">

		            			{{ $input['report_type'] }} Date <b>: {{ $input['date']}} </b> <br>
		            			@if($input['otnonot'] != null)
		                			<b> OT </b> 
		                			<b>: @if($input['otnonot'] == 0) No @else Yes @endif </b> <br>
		                		@endif
		                		{{ $input['report_type'] }} Employee
		                			<b>: {{ $totalEmployees }}</b>
		                		
		            		</td>
		            		<td style="width: 33%;text-align: right;">
		            			@if($input['subSection'] != null)
		            			Sub-section <b>: {{ $subSection[$input['subSection']]['hr_subsec_name'] }}</b><br>
		            			@endif
		            			@if($input['floor_id'] != null)
		                			Floor 
		                			<b>: {{ $floor[$input['floor_id']]['hr_floor_name'] }}</b><br>
		                		@endif
		                		@if($input['line_id'] != null)
		                		Line 
		                			<b>: {{ $line[$input['line_id']]['hr_line_name'] }}</b> <br>
		                		@endif
		                		Format 
		                			<b class="capitalize">: {{ isset($formatHead[1])?$formatHead[1]:'N/A' }}</b>
		            		</td>
		            	</tr>
		            </table>
		        </div>
			</div>
			<div class="content_list_section">
				@if($input['report_format'] == 0)
					<table class="table table-bordered table-hover table-head" style="width:100%;border:0 !important;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
					@foreach($uniqueGroups as $group => $employees)
						<thead>
							@if(count($employees) > 0)
			                <tr>
			                	@php
									if($format == 'as_line_id'){
										$head = 'Line';
										$body = $group;
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
										$body = $subSection[$group]['hr_subsec_name']??'N/A';
									}else{
										$head = '';
									}
								@endphp
			                	@if($head != '')
			                    <th>{{ $head }}</th>
			                    <th colspan="11">{{ $body }}</th>
			                    @endif
			                </tr>
			                @endif
			                <tr>
			                    <th>SL</th>
			                    <th>Associate ID</th>
			                    <th>Name and Phone</th>
			                    <th>Oracle ID</th>
			                    <th>Designation</th>
			                    <th>Department</th>
			                    <th>Section</th>
			                    <th>Sub Section</th>
			                    <th>Floor</th>
			                    <th>Line</th>
			                    <th>In Time</th>
			                    <th>Out Time</th>
			                </tr>
			            </thead>
			            <tbody>
				            @php
				             $i = 0; $month = date('Y-m',strtotime($input['date'])); 
				            @endphp
				            @if(count($employees) > 0)
				            @foreach($employees as $employee)
				            	@php
				            		$designationName = $designation[$employee->as_designation_id]['hr_designation_name']??'';
				            	@endphp
				            	@if($head == '')
				            	<tr>
				            		<td>{{ ++$i }}</td>
					            	
					            	<td>
					            		@if(!isset($input['export']))
						            		<a class="job_card" data-name="{{ $employee->as_name }}" data-associate="{{ $employee->associate_id }}" data-month-year="{{ $month }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Job Card">{{ $employee->associate_id }}</a>
						            	@else
						            		{{ $employee->associate_id }}
					            		@endif
					            	</td>
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
					            	<td>{{ ($employee->in_time != null && $employee->remarks != 'DSI') ? date('H:i:s', strtotime($employee->in_time)):'' }}</td>
						            <td>{{ $employee->out_time != null?date('H:i:s', strtotime($employee->out_time)):'' }}</td>
					            	
				            	</tr>
				            	@else
				            	@if($group == $employee->$format || $input['report_group'] == 'as_line_id')
				            	<tr>
				            		<td>{{ ++$i }}</td>
					            	
					            	<td>
					            		@if(!isset($input['export']))
					            			<a class="job_card" data-name="{{ $employee->as_name }}" data-associate="{{ $employee->associate_id }}" data-month-year="{{ $month }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Job Card">{{ $employee->associate_id }}</a>
					            		@else
					            		{{ $employee->associate_id }}
					            		@endif
					            	</td>
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
					            	<td>{{ ($employee->in_time != null && $employee->remarks != 'DSI') ? date('H:i:s', strtotime($employee->in_time)):'' }}</td>
						            <td>{{ $employee->out_time != null?date('H:i:s', strtotime($employee->out_time)):'' }}</td>
					            	
				            	</tr>
				            	@endif
				            	@endif
				            @endforeach
				            @else
					            <tr>
					            	<td colspan="12" class="text-center">No Employee Found!</td>
					            </tr>
				            @endif
				            @if(!isset($input['export']))
				            <tr style="border:0 !important;"><td colspan="14" style="border: 0 !important;height: 20px;"></td> </tr>
				            @endif
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
					<table class="table table-bordered table-hover table-head">
						<thead>
							<tr>
								<th>SL</th>
								@if($format == 'as_floor_id')
								<th>Unit</th>
								@endif
								
								@if($format == 'as_section_id' || $format == 'as_subsection_id')
								<th>Department Name</th>
								@endif
								@if($format == 'as_subsection_id')
								<th>Section Name</th>
								@endif
								<th> {{ $head }} Name</th>
								<th>Employee</th>
							</tr>
						</thead>
						<tbody>
							@php $i=0; $totalEmployee = 0; @endphp
							@if(count($uniqueGroups) > 0)
							@foreach($uniqueGroups as $group => $employee)
							@php		
								if($format == 'as_unit_id'){
									if($group == 145){
										$body = 'MBM + MBF + MBM 2';
									}else{
										$body = $unit[$group]['hr_unit_name']??'';
									}
									$exPar = '&selected='.($unit[$group]['hr_unit_id']??'');
								}elseif($format == 'as_line_id'){
									$body = $group;
									$exPar = '&selected='.$group;
								}elseif($format == 'as_floor_id'){
									$body = $floor[$group]['hr_floor_name']??'';
									$exPar = '&selected='.($floor[$group]['hr_floor_id']??'');
								}elseif($format == 'as_department_id'){
									$body = $department[$group]['hr_department_name']??'';
									$exPar = '&selected='.$department[$group]['hr_department_id']??'';
								}elseif($format == 'as_designation_id'){
									$body = $designation[$group]['hr_designation_name']??'';
									$exPar = '&selected='.$designation[$group]['hr_designation_id']??'';
								}elseif($format == 'as_section_id'){
									$body = $section[$group]['hr_section_name']??'';
									$exPar = '&selected='.$section[$group]['hr_section_id']??'';
								}elseif($format == 'as_subsection_id'){
									$body = $subSection[$group]['hr_subsec_name']??'';
									$exPar = '&selected='.$subSection[$group]['hr_subsec_id']??'';
								}else{
									$body = 'N/A';
								}
							@endphp
							@if(!isset($input['export']))
							<tr class="cursor-pointer" onClick="selectedGroup(this.id, '{{ $body }}', {{ $jsonUrl }})" data-body="{{ $body }}" id="{{$exPar}}" data-url="{{ $jsonUrl }}" class="select-group">
							@else
							<tr>
							@endif
								<td>{{ ++$i }}</td>
								@if($format == 'as_floor_id')
								<td>
									@if($format == 'as_floor_id')
										@php $unitIdfl = $floor[$group]['hr_floor_unit_id']??''; @endphp
									@else
										@php $unitIdfl = $line[$group]['hr_line_unit_id']??''; @endphp
									@endif
									{{ $unitIdfl != ''?($unit[$unitIdfl]['hr_unit_name']??''):'' }}
								</td>
								@endif
								
								@if($format == 'as_section_id' || $format == 'as_subsection_id')
								<td>
									@php
										if($format == 'as_subsection_id'){
											$getDepar = $subSection[$group]['hr_subsec_department_id']??'';
										}else{
											$getDepar = $section[$group]['hr_section_department_id']??'';
										}
									@endphp
									{{ $department[$getDepar]['hr_department_name']??'' }}
								</td>
								@endif
								@if($format == 'as_subsection_id')
								<td>
									@php
										$getSec = $subSection[$group]['hr_subsec_section_id']??'';
									@endphp
									{{ $section[$getSec]['hr_section_name']??'' }}
								</td>
								@endif
								<td>
									
									{{ ($body == null)?'N/A':$body }}
								</td>
								<td>
									{{ $employee->total }}
									
								</td>
							</tr>
							@endforeach
							@else
							<tr>
				            	<td colspan="3" class="text-center">No Employee Found!</td>
				            </tr>
							@endif
						</tbody>
						
					</table>
				@endif
			</div>
		</div>

	</div>
</div>
