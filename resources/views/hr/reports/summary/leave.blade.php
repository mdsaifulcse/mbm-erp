<div class="panel">
	<div class="panel-body">
			@php
				$urldata = http_build_query($input) . "\n";
			@endphp
			<a href='{{ url("hr/reports/summary/excel?$urldata")}}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 20px; left: 66px;"><i class="fa fa-file-excel-o"></i></a>
		<div class="report_section" id="report_section">
			@php
				$formatHead = explode('_',$format);
			@endphp
			
			<div class="top_summery_section">
				@if($input['report_format'] == 0 || ($input['report_format'] == 1 && $format != null))
				<div class="page-header">
		            <h2 style="margin:4px 10px; font-weight: bold; text-align: center;">Leave @if($input['report_format'] == 0) Details @else Summary @endif Report </h2>
		            <table class="table no-border f-16">
		            	<tr>
		            		<td style="text-align: left;width: 30%">
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
		            		<td style="text-align: center;width: 45%">

		            			Date <b>: {{ $input['from_date']}} to {{ $input['to_date']}} </b> <br>
		            			@if($input['otnonot'] != null)
		                			<b> OT </b> 
		                			<b>: @if($input['otnonot'] == 0) No @else Yes @endif </b> <br>
		                		@endif
	                			Total Employe
	                			<b>: {{ $totalEmployees }}</b>
		                		
		            		</td>
		            		<td style="text-align: right;width: 25%">
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
		        @else
		        <div class="page-header-summery">
        			
        			<h2>Leave List </h2>
        			<h4>Unit: {{ $unit[$input['unit']]['hr_unit_name'] }}</h4>
        			@if($input['area'] != null)
        			<h4>Area: {{ $area[$input['area']]['hr_area_name'] }}</h4>
        			@endif
        			@if($input['department'] != null)
        			<h4>Department: {{ $department[$input['department']]['hr_department_name'] }}</h4>
        			@endif

        			@if($input['section'] != null)
        			<h4>Section: {{ $section[$input['section']]['hr_section_name'] }}</h4>
        			@endif

        			@if($input['subSection'] != null)
        			<h4>Sub Section: {{ $subSection[$input['subSection']]['hr_subsec_name'] }}</h4>
        			@endif

        			@if($input['floor_id'] != null)
        			<h4>Floor: {{ $floor[$input['floor_id']]['hr_floor_name'] }}</h4>
        			@endif

        			@if($input['line_id'] != null)
        			<h4>Line: {{ $line[$input['line_id']]['hr_line_name'] }}</h4>
        			@endif
        			@if($input['otnonot'] != null)
        			<h4>OT: @if($input['otnonot'] == 0) No @else Yes @endif </h4>
        			@endif
        			<h4>Employee: <b>{{ $totalEmployees }}</b></h4>
		            		
		        </div>
		        @endif
			</div>
			<div class="content_list_section" >
				@if($input['report_format'] == 0)
					<style type="text/css">
                        @media print{@page {size: landscape}}
                    </style>
					@foreach($uniqueGroups as $group => $employees)
					
					<table class="table table-bordered table-hover table-head table-responsive" border="1">
						<thead>
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
									}elseif($format == 'as_unit_id'){
										$head = 'Unit';
										$body = $unit[$group]['hr_unit_name']??'N/A';
									}else{
										$head = '';
									}
								@endphp
			                	@if($head != '')
			                    <th colspan="2" style="font-style: bold;">{{ $head }}</th>
			                    <th colspan="12" style="font-style: bold;">{{ $body }}</th>
			                    @endif
			                </tr>
			                @endif
			                <tr>
			                    <th style="width:20px;">Sl</th>
			                    <th style="width:60px;">ID</th>
			                    <th style="width:100px;">Name</th>
			                    <th style="width:100px;">Designation</th>
			                    <th style="width:100px;">Department</th>
			                    <th style="width:5%;">Section</th>
			                    <th style="width:100px;">Sub Section</th>
			                    <th style="width:5%;">Floor</th>
			                    <th style="width:5%;">Line</th>
			                    <th style="width:5%;">Casual</th>
			                    <th style="width:5%;">Sick</th>
			                    <th style="width:5%;">Earned</th>
			                    <th style="width:5%;">Special</th>
			                    <th style="width:5%;">Total</th>
			                </tr>
			            </thead>
			            <tbody>
			            @php
			             $i = 0; $month = date('Y-m',strtotime($input['from_date'])); 
			             $totalOt=0; $totalPay = 0;
			            @endphp
			            @if(count($employees) > 0)
			            @foreach($employees as $employee)
			            	@php
			            		$designationName = $designation[$employee->as_designation_id]['hr_designation_name']??'';
			            		

			            	@endphp
			            	<tr>
			            		<td style="text-align: center;">{{ ++$i }}</td>
				            	<td><a href='{{ url("hr/operation/job_card?associate=$employee->associate_id&month_year=$month") }}' target="_blank">{{ $employee->associate_id }}</a><br>
				            		{{ $employee->as_oracle_code }}
				            	</td>
				            	<td>
				            		<b>{{ $employee->as_name }}</b>
				            		<p>{{ $employee->as_contact }}</p>
				            	</td>
				            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
				            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
				            	<td>{{ $section[$employee->as_section_id]['hr_section_name']??'' }}</td>
				            	<td>{{ $subSection[$employee->as_subsection_id]['hr_subsec_name']??'' }}</td>
				            	<td>{{ $floor[$employee->as_floor_id]['hr_floor_name']??'' }}</td>
				            	<td style="text-align: center;">{{ $line[$employee->as_line_id]['hr_line_name']??'' }}</td>
				            	<td style="text-align: center;">
				            		{{(isset($employee->Casual)?$employee->Casual:0)}}
				            	</td>
				            	<td style="text-align: center;">
				            		{{(isset($employee->Sick)?$employee->Sick:0)}}
				            	</td>
				            	<td style="text-align: center;">
				            		{{(isset($employee->Earned)?$employee->Earned:0)}}
				            	</td>
				            	<td style="text-align: center;">
				            		{{(isset($employee->Special)?$employee->Special:0)}}
				            	</td>
				            	<td style="text-align: center;">
				            		{{(isset($employee->days)?$employee->days:0)}}
				            	</td>
			            	</tr>
			            @endforeach
			            @else
				            <tr>
				            	<td colspan="15" class="text-center">No Leave Found!</td>
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
					<table class="table table-bordered table-hover table-head" border="1">
						<thead>
							<tr style="text-align: center;">
								<th rowspan="2">Sl</th>
								@if($format == 'as_section_id' || $format == 'as_subsection_id')
								<th rowspan="2">Department Name</th>
								@endif
								@if($format == 'as_subsection_id')
								<th rowspan="2">Section Name</th>
								@endif
								<th rowspan="2"> {{ $head }} {{ $format != 'ot_hour'?'Name':'' }}</th>
								<th colspan="2">Casual</th>
								<th colspan="2">Sick</th>
								<th colspan="2">Earned</th>
								<th colspan="2">Special</th>
								<th colspan="2">Total</th>
							</tr>
							<tr>
								<th>Emp</th>
								<th>Days</th>
								<th>Emp</th>
								<th>Days</th>
								<th>Emp</th>
								<th>Days</th>
								<th>Emp</th>
								<th>Days</th>
								<th>Emp</th>
								<th>Days</th>
							</tr>
						</thead>
						<tbody>
							@php $i=0;  @endphp
							@if(count($uniqueGroups) > 0)
							@foreach($uniqueGroups as $group => $employee)
							<tr>
								<td>{{ ++$i }}</td>
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
								<td>
									@php
										if($format == 'as_unit_id'){
											if($group == 145){
												$body = 'MBM + MBF + MBM 2';
												$exPar = '&selected=145';
											}else{
												$body = $unit[$group]['hr_unit_name']??'';
												$exPar = '&selected='.$unit[$group]['hr_unit_id']??'';
											}
										}elseif($format == 'as_line_id'){

											if(isset($line[$group])){
												$body = $line[$group]['hr_line_name']??'';
												$exPar = '&selected='.$line[$group]['hr_line_id']??'';
											}else{
												$body = '-';
												$exPar = '&selected=null';
											}
										}elseif($format == 'as_floor_id'){
											if(isset($floor[$group])){

												$body = $floor[$group]['hr_floor_name']??'';
												if($input['unit'] == 145){
													$uid = $floor[$group]['hr_floor_unit_id'];	
													$body .= '('.$unit[$uid]['hr_unit_short_name'].')';
												}
												$exPar = '&selected='.$floor[$group]['hr_floor_id']??'';
											}else{
												$body = '-';
												$exPar = '&selected=null';
											}
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
											$body = $seName;
											$exPar = '&selected='.$section[$group]['hr_section_id']??'';
										}elseif($format == 'as_subsection_id'){
											$body = $subSection[$group]['hr_subsec_name']??'';
											$exPar = '&selected='.$subSection[$group]['hr_subsec_id']??'';
										}else{
											$body = 'N/A';
										}
									@endphp
									<a class="generate-drawer" data-url="{{($urldata.$exPar)}}" data-body="{{ ($head.' : '.$body
									) }}" id="{{$exPar}}" class="select-group"> 
										{{ ($body == null)?'N/A':$body }} 
									</a>
								</td>
								<td style="text-align: center;">
									{{$employee->Casual??''}}
								</td>
								<td style="text-align: center;">
									{{$employee->CasualDays??''}}
								</td>
								<td style="text-align: center;">
									{{$employee->Sick??''}}
								</td>
								<td style="text-align: center;">
									{{$employee->SickDays??''}}
								</td>
								<td style="text-align: center;">
									{{$employee->Earned??''}}
								</td>
								<td style="text-align: center;">
									{{$employee->EarnedDays??''}}
								</td>
								<td style="text-align: center;">
									{{$employee->Special??''}}
								</td>
								<td style="text-align: center;">
									{{$employee->SpecialDays??''}}
								</td>
								<td style="text-align: center;">
									{{count($employee->emp)}}
								</td>
								<td style="text-align: center;">
									{{ $employee->CasualDays + $employee->SickDays + $employee->EarnedDays + $employee->SpecialDays}}
								</td>
							</tr>
							@endforeach
							<tr style="font-weight: bold;">
								<td 
								@if($format == 'as_section_id')
									colspan="3" 
								@elseif($format == 'as_subsection_id')
									colspan="4" 
								@else
									colspan="2" 
								@endif
								>
									Total
								</td>
								@php $all = collect($uniqueGroups)  @endphp
								<td style="text-align: center;">{{$all->sum('Casual')}}</td>
								<td style="text-align: center;">{{$all->sum('CasualDays')}}</td>
								<td style="text-align: center;">{{$all->sum('Sick')}}</td>
								<td style="text-align: center;">{{$all->sum('SickDays')}}</td>
								
								<td style="text-align: center;">{{$all->sum('Earned')}}</td>
								<td style="text-align: center;">{{$all->sum('EarnedDays')}}</td>
								<td style="text-align: center;">{{$all->sum('Special')}}</td>
								<td style="text-align: center;">{{$all->sum('SpecialDays')}}</td>
								<td style="text-align: center;">
									{{ $all->sum('Casual') + $all->sum('Sick') + $all->sum('Earned') + $all->sum('Special')}}
								</td>
								<td style="text-align: center;">
									{{ $all->sum('CasualDays') + $all->sum('SickDays') + $all->sum('EarnedDays') + $all->sum('SpecialDays')}}
								</td>
							</tr>
							
							@else
							<tr>
				            	<td colspan="{{ ($format == 'as_subsection_id' || $format == 'as_subsection_id')?'9':'7'}}" class="text-center">No Leave Found!</td>
				            </tr>
							@endif
						</tbody>
						
					</table>
				@endif
			</div>
		</div>

	</div>
</div>
