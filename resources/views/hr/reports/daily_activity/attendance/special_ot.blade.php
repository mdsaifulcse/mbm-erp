<div class="panel">
	<div class="panel-body">
		@php
			$urldata = http_build_query($input) . "\n";
			$jsonUrl = json_encode($urldata);
		@endphp

		<a href='{{ url("hr/reports/daily-attendance-activity-report?$urldata")}}&export=excel' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 15px; left: 65px;"><i class="fa fa-file-excel-o"></i></a>
		
		<div class="report_section" id="report_section">
			@php
				$formatHead = explode('_',$format);
			@endphp
			<div class="top_summery_section">
				<div class="page-header">
		            <h3 style="margin:4px 10px; font-weight: bold; text-align: center;">Special OT @if($input['report_format'] == 0) Details @else Summary @endif Report </h3>
		            <h5 style="text-align: center;">
			        	<b>Unit : </b>
						@php
							
							if($input['unit'] != null){
								foreach($input['unit'] as $un){
									echo $unit[$un]['hr_unit_name']??'';
									echo ', ';
								}
							}else{
								echo 'ALL';
							}
						@endphp
			        </h5>
			        <div class="report-header">
			        	<div class="report-header-items"><b>Date</b> <span>{{ $input['date']}}</span></div>
			        	<div class="report-header-items"><b>Total OT</b> <span>{{ numberToTimeClockFormat($summary->total_ot) }} Hrs</span></div>
			        	<div class="report-header-items"><b>Total Employee</b> <span>{{ $summary->total_employee }}</span></div>
			        	@if($input['location'] != null)
			        		<div class="report-header-items"><b>Location </b> <span>{{ $location[$input['location']]['hr_location_name'] }}</span></div>
			        	@endif
			        	@if($input['area'] != null)
			        	<div class="report-header-items"><b>Area</b> <span>{{ $area[$input['area']]['hr_area_name'] }}</span></div>
			        	@endif
			        	@if($input['department'] != null)
			        	<div class="report-header-items"><b>Department</b> <span>{{ $department[$input['department']]['hr_department_name'] }}</span></div>
			        	@endif
			        	@if($input['section'] != null)
			        	<div class="report-header-items"><b>Section</b> <span>{{ $section[$input['section']]['hr_section_name'] }}</span></div>
			        	@endif
			        	@if($input['subSection'] != null)
			        	<div class="report-header-items"><b>Sub-section</b> <span>{{ $subSection[$input['subSection']]['hr_subsec_name'] }}</span></div>
			        	@endif
			        	@if($input['floor_id'] != null)
			        	<div class="report-header-items"><b>Floor</b> <span>{{ $floor[$input['floor_id']]['hr_floor_name'] }}</span></div>
			        	@endif
			        	@if($input['line_id'] != null)
			        	<div class="report-header-items"><b>Line</b> <span>{{ $line[$input['line_id']]['hr_line_name'] }}</span></div>
			        	@endif


			        </div>
		            
		        </div>
			</div>
			
			
			<div class="content_list_section">
				@if($input['report_format'] == 0)
					<table class="table table-bordered table-hover table-head" style="width:100%;border:0 !important;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
					@foreach($uniqueGroupEmp as $group => $employees)
					
						@if(count($employees) > 0)
		                <tr>
		                	@php
								if($format == 'as_line_id'){
									$head = 'Line';
									$body = $line[$group]['hr_line_name']??'N/A';
								}elseif($format == 'as_floor_id'){
									$head = 'Floor';
									$body = $floor[$group]['hr_floor_name']??'N/A';
								}elseif($format == 'as_department_id'){
									$head = 'Department';
									$body = $department[$group]['hr_department_name']??'N/A';
								}elseif($format == 'as_section_id'){
									$head = 'Section';
									$body = $section[$group]['hr_section_name']??'N/A';
								}elseif($format == 'as_subsection_id'){
									$head = 'Sub Section';
									$body = $subSection[$group]['hr_subsec_name']??'N/A';
								}elseif($format == 'as_designation_id'){
									$head = 'Designation';
									$body = $designation[$group]['hr_designation_name']??'N/A';
								}elseif($format == 'ot_hour'){
									$head = 'OT Hour';
									
				                    $otHourBody = numberToTimeClockFormat($group);
									$body = $otHourBody??'N/A';
								}else{
									$head = '';
								}
							@endphp
		                	@if($head != '')
		                    <th colspan="2">{{ $head }}</th>
		                    <th colspan="11">{{ $body }}</th>
		                    @endif
		                </tr>
		                @endif
		                <tr>
		                    <th>Sl</th>
		                    <th>Associate ID</th>
		                    <th>Name & Phone</th>
		                    <th>Oracle ID</th>
		                    <th>Designation</th>
		                    <th>Department</th>
		                    <th>Section</th>
		                    <th>Sub Section</th>
		                    <th>Floor</th>
		                    <th>Line</th>
		                    <th>In-Time</th>
		                    <th>Out-Time</th>
		                    <th>OT Hour</th>
		                </tr>
			            @php
			             $i = 0; $month = date('Y-m',strtotime($input['date'])); $totalOt=0;
			            @endphp
			            @if(count($employees) > 0)
				            @foreach($employees as $employee)
				            	@php
				            		$designationName = $designation[$employee->as_designation_id]['hr_designation_name']??'';
				            		
				                    $otHour = numberToTimeClockFormat($employee->ot_hour);

				            	@endphp
				            	<tr>
				            		<td>{{ ++$i }}</td>
					            	<td>
					            		<a class="job_card" data-name="{{ $employee->as_name }}" data-associate="{{ $employee->associate_id }}" data-month-year="{{ $month }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Job Card">{{ $employee->associate_id }}</a>
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
					            	<td>
					            		@if($employee->in_time != null)
					            		{{ date('H:i:s', strtotime($employee->in_time)) }}
					            		@endif
					            	</td>
					            	<td>
					            		@if($employee->out_time != null)
					            		{{ date('H:i:s', strtotime($employee->out_time)) }}
					            		@endif
					            	</td>
					            	<td style="text-align:right;">{{ $otHour }}</td>
				            	</tr>
				            @endforeach
			            	
			            	
			            @else
				            <tr>
				            	<td colspan="13" class="text-center">No OT Employee Found!</td>
				            </tr>
			            @endif
			            <tr style="border:0 !important;"><td colspan="13" style="border: 0 !important;height: 20px;"></td> </tr>
			            
					
					@endforeach
					</table>
				@elseif(($input['report_format'] == 1 && $format != null))
					@php
						$colspan = 4;
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
							$colspan = 5;
						}elseif($format == 'as_subsection_id'){
							$colspan = 6;
							$head = 'Sub Section';
						}elseif($format == 'ot_hour'){
							$head = 'Hour';
						}else{
							$head = '';
						}
					@endphp
					<table class="table table-bordered table-hover table-head" border="1">
						<thead>
							<tr>
								<th>Sl</th>
								@if($format == 'as_floor_id' || $format == 'as_line_id')
								<th>Unit</th>
								@endif
								@if($format == 'as_line_id')
								<th>Floor</th>
								@endif
								@if($format == 'as_section_id' || $format == 'as_subsection_id')
								<th>Department Name</th>
								@endif
								@if($format == 'as_subsection_id')
								<th>Section Name</th>
								@endif
								<th> {{ $head }} {{ $format != 'ot_hour'?'Name':'' }}</th>
								<th style="text-align:center;">No. Of Employee</th>
								<th style="text-align:center;">Total OT Hour</th>
							</tr>
						</thead>
						<tbody>
							@php $i=0; @endphp
							@if(count($uniqueGroupEmp) > 0)
								@foreach($uniqueGroupEmp as $key => $employee)
								@php $group = $key; @endphp
								@php
									if($format == 'as_unit_id'){
										if($group == 145){
											$body = 'MBM + MBF + MBM 2';
										}else{
											$body = $unit[$group]['hr_unit_name']??'';
										}
										$exPar = '&selected='.($unit[$group]['hr_unit_id']??'');
									}elseif($format == 'as_line_id'){
										$body = $line[$group]['hr_line_name']??'';
										$exPar = '&selected='.($line[$group]['hr_line_id']??'');
									}elseif($format == 'as_floor_id'){
										$body = $floor[$group]['hr_floor_name']??'';
										$exPar = '&selected='.($floor[$group]['hr_floor_id']??'');
									}elseif($format == 'as_department_id'){
										$body = $department[$group]['hr_department_name']??'';
										$exPar = '&selected='.$department[$group]['hr_department_id']??'';
									}elseif($format == 'as_section_id'){
										$body = $section[$group]['hr_section_name']??'N/A';
										$exPar = '&selected='.$section[$group]['hr_section_id']??'';
									}elseif($format == 'as_subsection_id'){
										$body = $subSection[$group]['hr_subsec_name']??'';
										$exPar = '&selected='.$subSection[$group]['hr_subsec_id']??'';
									}elseif($format == 'as_designation_id'){
										$body = $designation[$group]['hr_designation_name']??'';
										$exPar = '&selected='.$designation[$group]['hr_designation_id']??'';
									}elseif($format == 'ot_hour'){
										
					                    $otHourBody = numberToTimeClockFormat($group);
										$body = $otHourBody??'N/A';
										$exPar = '&selected='.number_format($group,3)??'';
									}else{
										$body = 'N/A';
									}
								@endphp
								<tr class="cursor-pointer" onClick="selectedGroup(this.id, '{{ $body }}', {{ $jsonUrl }})" data-body="{{ $body }}" id="{{$exPar}}" data-url="{{ $jsonUrl }}" class="select-group">
									<td>{{ ++$i }}</td>
									@if($format == 'as_floor_id' || $format == 'as_line_id')
									<td>
										@if($format == 'as_floor_id')
											@php $unitIdfl = $floor[$group]['hr_floor_unit_id']??''; @endphp
										@else
											@php $unitIdfl = $line[$group]['hr_line_unit_id']??''; @endphp
										@endif
										{{ $unitIdfl != ''?($unit[$unitIdfl]['hr_unit_name']??''):'' }}
									</td>
									@endif
									@if($format == 'as_line_id')
									<td>
										@php $lineFloorId = $line[$group]['hr_line_floor_id']??''; @endphp
										{{ $lineFloorId != ''?($floor[$lineFloorId]['hr_floor_name']??''):'' }}
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
											echo $department[$getDepar]['hr_department_name']??'';
										@endphp
									</td>
									@endif
									@if($format == 'as_subsection_id')
									<td>
										@php
											$getSec = $subSection[$group]['hr_subsec_section_id']??'';
											echo $section[$getSec]['hr_section_name']??'';
										@endphp
									</td>
									@endif
									<td >
										
										{{ ($body == null)?'N/A':$body }}
									</td>
									<td style="text-align:center;">
										{{ $employee->total }}
									</td>
									<td style="text-align:right;">
										{{ numberToTimeClockFormat(round($employee->groupOt,2)) }}
									</td>
								</tr>
								@endforeach

								<tr>
				            		<th colspan="{{ ($colspan - 2)}}" >Total</th>
				            		<th style="text-align: center;">{{ $summary->total_employee }}</th>
				            		<th style="text-align:right;">{{ numberToTimeClockFormat($summary->total_ot) }}</th>
								</tr>
							@else
							<tr>
				            	<td colspan="{{ $colspan }}" class="text-center">No OT Employee Found!</td>
				            </tr>
							@endif
						</tbody>
						
					</table>
				
				@endif
			</div>
		</div>

	</div>
</div>
