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
		            <h2 style="margin:4px 10px; font-weight: bold; text-align: center;"> Employee  @if($input['report_format'] == 0) Details @else Summary @endif Report</h2>
		            
		            <table class="table no-border f-16" border="0">
		            	<tr>
		            		<td style="width: 33%;">
		            		@if(!isset($input['export']))
		            		@if(count($unitWiseEmp) > 0 )
                                @foreach($unitWiseEmp as $key => $v)
                                {{$unit[$key]['hr_unit_short_name']}}  
		                			<b>: {{$v}}</b> ,
                                @endforeach
                                <br>
                            @endif
                            @endif
		            		@if(isset($input['unit']))
			            		@if($input['unit'] != null)
		            				Unit <b>: {{ $input['unit'] == 145?'MBM + MBF + MBM 2':$unit[$input['unit']]['hr_unit_name'] }}</b> <br>
			        			@endif
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
		            			  Date <b>: {{ $input['date']}} </b> <br>
		            			@if($input['otnonot'] != null)
		                			<b> OT </b> 
		                			<b>: @if($input['otnonot'] == 0) No @else Yes @endif </b> <br>
		                		@endif
		                		@if($input['report_format'] == 1)
		                			Male: <b>{{collect($uniqueGroups)->sum('male')}}</b>
		                			Female: <b>{{collect($uniqueGroups)->sum('female')}}</b>
		                			@endif
		                		Total  
		                			<b>: {{ $totalEmployees }}</b><br>
		                			
		                		
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
			                	@if(!isset($input['export']))
			                    <th width="2%">SL</th>
			                    <th width="8%">Unit</th>
			                    <th width="10%">Associate ID</th>
			                    <th width="16%">Name & Phone</th>
			                    <th width="8%">Gender</th>
			                    <th width="8%">OT/Non-OT</th>
			                    <th width="10%">Designation</th>
			                    <th width="9%">Department</th>
			                    <th width="9%">Section</th>
			                    <th width="9%">Sub Section</th>
			                    <th width="9%">Line</th>
			                    <th width="5%">DOJ</th>
			                    @else
			                    <th >SL</th>
			                    <th >Unit</th>
			                    <th >Associate ID</th>
			                    <th >Name and Phone</th>
			                    <th >Gender</th>
			                    <th >OT/Non-OT</th>
			                    <th >Designation</th>
			                    <th >Department</th>
			                    <th >Section</th>
			                    <th >Sub Section</th>
			                    <th >Line</th>
			                    <th >DOJ</th>
			                    @endif
			                </tr>
			            </thead>
			            <tbody>
				            @php
				             $i = 0; $month = date('Y-m',strtotime($input['date'])); 
				            @endphp
				            @if(count($employees) > 0)
				            @foreach($employees as $employee)
				            	@php
				            	// dd($employee);
				            		$designationName = $designation[$employee->as_designation_id]['hr_designation_name']??'';
				            	@endphp
				            	@if($head == '')
				            	<tr>
				            		<td>{{ ++$i }}</td>
				            		<td>{{ $unit[$employee->as_unit_id]['hr_unit_short_name']??'' }}</td>
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
					            	
					            	<td>{{ $employee->as_gender }}</td>
					            	<td>{{ $employee->as_ot == 1?'OT':'Non-OT' }}</td>
					            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
					            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
					            	<td>{{ $section[$employee->as_section_id]['hr_section_name']??'' }}</td>
					            	<td>{{ $subSection[$employee->as_subsection_id]['hr_subsec_name']??'' }}</td>
					            	<td>{{ $employee->ordby??'' }}</td>
					            	<td style="white-space: nowrap;">{{$employee->as_doj}}</td>
					            	
				            	</tr>
				            	@else
				            	@if($group == $employee->$format || $input['report_group'] == 'as_line_id')
				            	<tr>
				            		<td>{{ ++$i }}</td>
				            		<td>{{ $unit[$employee->as_unit_id]['hr_unit_short_name']??'' }}</td>
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
					            	
					            	<td>{{ $employee->as_gender }}</td>
					            	<td>{{ $employee->as_ot == 1?'OT':'Non-OT' }}</td>
					            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
					            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
					            	<td>{{ $section[$employee->as_section_id]['hr_section_name']??'' }}</td>
					            	<td>{{ $subSection[$employee->as_subsection_id]['hr_subsec_name']??'' }}</td>
					            	<td>{{ $employee->ordby??'' }}</td>
					            	<td style="white-space: nowrap;">{{$employee->as_doj}}</td>
					            	
				            	</tr>
				            	@endif
				            	@endif
				            @endforeach
				            @else
					            <tr>
					            	<td colspan="11" class="text-center">No Employee Found!</td>
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
								<th rowspan="2">SL</th>
								@if($format == 'as_floor_id')
								<th rowspan="2">Unit</th>
								@endif
								
								@if($format == 'as_section_id' || $format == 'as_subsection_id')
								<th rowspan="2">Department Name</th>
								@endif
								@if($format == 'as_subsection_id')
								<th rowspan="2">Section Name</th>
								@endif
								<th rowspan="2"> {{ $head }} Name</th>
								<th colspan="3" style="text-align: center;">Male</th>
								<th colspan="3" style="text-align: center;">Female</th>
								<th rowspan="2" style="text-align: center;">Grand Total</th>
							</tr>
							<tr>
								<th style="text-align: center;">OT</th>
								<th style="text-align: center;">Non-OT</th>
								<th style="text-align: center;">Total</th>
								<th style="text-align: center;">OT</th>
								<th style="text-align: center;">Non-OT</th>
								<th style="text-align: center;">Total</th>
							</tr>
						</thead>
						<tbody>
							@php $i=0; @endphp
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
								    $body = '';$exPar =''; 
								    if(isset($subSection[$group])){
									$body = $subSection[$group]['hr_subsec_name']??'';
									$exPar = '&selected='.$subSection[$group]['hr_subsec_id']??'';
									}
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
								<td style="text-align:center;">{{ $employee->male_ot??0 }}</td>
								<td style="text-align:center;">{{ $employee->male_nonot??0 }}</td>
								<td style="text-align:center;">{{ $employee->male??0 }}</td>
								<td style="text-align:center;">{{ $employee->female_ot??0 }}</td>
								<td style="text-align:center;">{{ $employee->female_nonot??0 }}</td>
								<td style="text-align:center;">{{ $employee->female??0 }}</td>
								<td style="text-align:center;">
									{{ $employee->total }}
								</td>
							</tr>
							@endforeach
							<tr style="text-align: center;">
								<th colspan="@if($format == 'as_section_id' || $format == 'as_floor_id' )3 @else {{ ($format == 'as_subsection_id')?'4':'2'}} @endif">Total</th>
								<th>{{collect($uniqueGroups)->sum('male_ot')}}</th>
								<th>{{collect($uniqueGroups)->sum('male_nonot')}}</th>
								<th>{{collect($uniqueGroups)->sum('male')}}</th>
								<th>{{collect($uniqueGroups)->sum('female_ot')}}</th>
								<th>{{collect($uniqueGroups)->sum('female_nonot')}}</th>
								<th>{{collect($uniqueGroups)->sum('female')}}</th>
								<th>{{collect($uniqueGroups)->sum('total')}}</th>
							</tr>
							@else
							<tr>
				            	<td colspan="{{ ($format == 'as_subsection_id' || $format == 'as_subsection_id')?'7':'5'}}" class="text-center">No   Employee Found!</td>
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

