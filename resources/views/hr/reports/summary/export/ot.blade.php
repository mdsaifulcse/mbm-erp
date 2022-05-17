<div class="panel">
	<div class="panel-body">
		<div class="report_section" id="report_section">
			@php
				$formatHead = explode('_',$format);
			@endphp
			
			<div class="top_summery_section">
				
		        <div class="page-header-summery">
        			
        			<h2 style="text-align: center;">Over Time Report </h2>
        			<h3 style="text-align: center;">Date {{$input['from_date']}} to {{$input['to_date']}}</h3>
        			@if($input['unit'] == 145)
        				<h4>Unit: MBM + MBW + MBM 2</h4>
        			@else
        			<h4  style="text-align: center;">Unit: {{ $unit[$input['unit']]['hr_unit_name'] }}</h4>
        			@endif
        			<h4  style="text-align: center;">
	        			@if($input['area'] != null)
	        			@endif
	        			@if($input['department'] != null)
	        				Department: {{ $department[$input['department']]['hr_department_name'] }}
	        			@endif

	        			@if($input['section'] != null)
	        			Section: {{ $section[$input['section']]['hr_section_name'] }}
	        			@endif

	        			@if($input['subSection'] != null)
	        			Sub Section: {{ $subSection[$input['subSection']]['hr_subsec_name'] }}
	        			@endif

	        			@if($input['floor_id'] != null)
	        			Floor: {{ $floor[$input['floor_id']]['hr_floor_name'] }}
	        			@endif

	        			@if($input['line_id'] != null)
	        			Line: {{ $line[$input['line_id']]['hr_line_name'] }}
	        			@endif
        			</h4>
        			<h4>Total Employee: {{ $totalEmployees }} </h4>
        			<h4> Total OT: {{ number_format($totalValue,2, '.', '')  }} </h4> 
        			<h4>Total Amount: {{number_format($totalAmount,2, '.', '')  }} BDT</h4>
        			<h4></h4>
		            		
		        </div>
			</div>
			<div class="content_list_section" >
				@if($input['report_format'] == 0)
					@foreach($uniqueGroups as $group => $employees)
					
					<table class="table table-bordered table-hover table-head" border="1">
						<thead >
							@if(count($employees) > 0 && $format != 'as_unit_id')
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
									}elseif($format == 'ot_hour'){
										$head = 'OT Hour';
										
					                    $otHourBody = $group;
										$body = $otHourBody??'N/A';
									}else{
										$head = '';
									}
								@endphp
			                	@if($head != '' )
			                    <th colspan="2" style="font-weight: bold;">{{ $head }}</th>
			                    <th colspan="13" style="font-weight: bold;">{{ $body }}</th>
			                    @endif
			                </tr>
			                @endif
			                <tr >
			                    <th style="font-weight: bold;" >Sl</th>
			                    <th style="font-weight: bold;" >Associate ID</th>
			                    <th style="font-weight: bold;" >Name</th>
			                    <th style="font-weight: bold;" >Oracle ID</th>
			                    <th style="font-weight: bold;" >Designation</th>
			                    <th style="font-weight: bold;" >Unit</th>
			                    <th style="font-weight: bold;" >Department</th>
			                    <th style="font-weight: bold;" >Section</th>
			                    <th style="font-weight: bold;" >Sub Section</th>
			                    <th style="font-weight: bold;" >Floor</th>
			                    <th style="font-weight: bold;" >Line</th>
			                    <th style="font-weight: bold;" >Days</th>
			                    <th style="font-weight: bold;" >OT Hour</th>
			                    <th style="font-weight: bold;" >OT Amount</th>
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
			            		
			                    $otHour = $employee->ot_hour;

			            	@endphp
			            	<tr>
			            		<td>{{ ++$i }}</td>
				            	{{-- <td><img src="{{ emp_profile_picture($employee) }}" class='small-image' style="height: 40px; width: auto;"></td> --}}
				            	<td>{{ $employee->associate_id }}</td>
				            	<td>{{ $employee->as_name }}</td>
				            	<td>{{ $employee->as_oracle_code }}</td>
				            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
				            	<td>{{ $unit[$employee->as_unit_id]['hr_unit_short_name']??'' }}</td>
				            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
				            	<td>{{ $section[$employee->as_section_id]['hr_section_name']??'' }}</td>
				            	<td>{{ $subSection[$employee->as_subsection_id]['hr_subsec_name']??'' }}</td>
				            	<td>{{ $floor[$employee->as_floor_id]['hr_floor_name']??'' }}</td>
				            	<td>{{ $line[$employee->as_line_id]['hr_line_name']??'' }}</td>
				            	<td style="text-align: center;">{{$employee->days}}</td>
				            	<td>{{ number_format($otHour,2, '.', '') }}</td>
				            	<td style="text-align: right;">{{ number_format($employee->ot_amount,2, '.', '') }}</td>
				            
			            	</tr>
			            	@php 
			            		$totalOt += $otHour; 
			            		$totalPay += ceil($employee->ot_amount); 
			            	@endphp
			            @endforeach
			            	<tr>
			            		<td colspan="13" style="text-align: right;font-weight: bold;"><b>Total</b></td>
			            		<td  style="text-align: right;font-weight: bold;"><b>
			            			{{number_format($totalOt,2, '.', '')}}
			            		</b>
			            		</td>

			            		<td style="text-align: right;font-weight: bold;"><strong>{{ceil($totalPay)}}</strong></td>
			            	</tr>
			            @else
			            @endif
			            </tbody>
					</table>
					@endforeach
				@elseif(($input['report_format'] == 1 and $format != null))
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
						}elseif($format == 'ot_hour'){
							$head = 'Hour';
						}else{
							$head = '';
						}
					@endphp
					<table class="table table-bordered table-hover table-head" border="1">
						<thead>
							<tr>
								<th style="font-weight: bold;">Sl</th>
								@if($format == 'as_section_id' || $format == 'as_subsection_id')
								<th style="font-weight: bold;">Department Name</th>
								@endif
								@if($format == 'as_subsection_id')
								<th style="font-weight: bold;">Section Name</th>
								@endif
								<th style="font-weight: bold;"> {{ $head }} {{ $format != 'ot_hour'?'Name':'' }}</th>
								<th style="font-weight: bold;text-align: center;">Employee</th>
								<th style="font-weight: bold;">Total OT Hour</th>
								<th style="font-weight: bold;">Total OT Amount</th>
							</tr>
						</thead>
						<tbody>
							@php $i=0; @endphp
							
							@if(count($getEmployee) > 0)
								@foreach($getEmployee as $employee)
									@php $group = $employee->$format; @endphp
									<tr>
										<td>{{ (++$i) }}</td>
										@if($format == 'as_section_id' || $format == 'as_subsection_id')
										<td>
											@php
												if($format == 'as_subsection_id'){
													$getDepar = $subSection[$group]['hr_subsec_department_id']??'';
												}else{
													$getDepar = $section[$group]['hr_section_department_id']??'';
												}
												$departmentName =  $department[$getDepar]['hr_department_name']??'';
											@endphp
											{{$departmentName }}
										</td>
										@endif
										@if($format == 'as_subsection_id')
										<td>
											@php
												$sectionName = '';
												if(isset($subSection[$group])){
													$getSec = $subSection[$group]['hr_subsec_section_id']??'';
													if(isset($section[$getSec])){
														$sectionName =  $section[$getSec]['hr_section_name']??'';
													}

												}
											@endphp
											{{$sectionName}}
										</td>
										@endif
										<td>
											@php
												if($format == 'as_unit_id'){
													if($group == 145){
														$body = 'MBM + MBF + MBM 2';
													}else{
														$body = $unit[$group]['hr_unit_name']??'';
													}
												}elseif($format == 'as_line_id'){
													$body = $line[$group]['hr_line_name']??'';
												}elseif($format == 'as_floor_id'){
													$body = $floor[$group]['hr_floor_name']??'';
												}elseif($format == 'as_department_id'){
													$body = $department[$group]['hr_department_name']??'';
												}elseif($format == 'as_section_id'){
													$body = $section[$group]['hr_section_name']??'N/A';
												}elseif($format == 'as_subsection_id'){
													$body = $subSection[$group]['hr_subsec_name']??'';
												}elseif($format == 'as_designation_id'){
													$body = $designation[$group]['hr_designation_name']??'';
												}elseif($format == 'ot_hour'){
													
								                    $otHourBody = $group;
													$body = $otHourBody??'N/A';
												}else{
													$body = 'N/A';
												}
											@endphp
											{{ (($body == null)?'N/A':$body) }}
										</td>
										<td style="text-align: center">
											{{ $employee->total }}
										</td>
										<td style="text-align: right">
											

											{{number_format($employee->groupOt,2, '.', '')}}
										</td>
										<td style="text-align: right">{{ number_format($employee->ot_amount,2, '.', '') }}</td>
									</tr>
								@endforeach
								<tr style="font-weight: bold;">
									<td style="font-weight: bold;"
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
									<td style="text-align: center;font-weight: bold;">{{$totalEmployees}}</td>
									<td style="text-align: right;font-weight: bold;">{{number_format($totalValue,2, '.', '')}}</td>
									<td style="text-align: right;font-weight: bold;">{{number_format($totalAmount,2, '.', '')}}</td>
								</tr>
							@endif
						</tbody>
						
					</table>
				@endif
			</div>
			<div class="bottom_summery_section">
                <div class="page-footer">
                    
                    <div class="bottom_summery_section">
		                <div class="page-footer">
		                    
		                    <table class="table table-bordered table-hover table-head" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
		                        <tr>
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            @if($input['report_format'] == 0)
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            @endif
		                            <th></th>
		                            <th colspan="2" style="font-weight: bold; font-size:13px;">Total Employee</th>
		                            <td style="text-align: right; font-size:13px;font-weight: bold;">{{ $totalEmployees }}</td>
		                        </tr>
		                        <tr>
		                            <th></th>
		                            <th></th>
		                            @if($input['report_format'] == 0)
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            @endif
		                            <th></th>
		                            <th></th>
		                            <th colspan="2" style="font-weight: bold; font-size:13px;">Total OT Hour</th>
		                            <td style="text-align: right; font-size:13px;font-weight: bold;">{{ number_format($totalValue,2, '.', '')  }}</td>
		                        </tr>
		                        <tr>
		                            <th></th>
		                            @if($input['report_format'] == 0)
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            @endif
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            <th colspan="2" style="font-weight: bold; font-size:13px;">Total OT Amount</th>
		                            <td style="text-align: right; font-size:13px;font-weight: bold;">{{ number_format($totalAmount,2, '.', '')  }}</td>
		                        </tr>
		                    </table>
		                </div>
		            </div>
                </div>
            </div>
		</div>

	</div>
</div>

