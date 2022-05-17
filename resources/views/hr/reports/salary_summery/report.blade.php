<div class="panel">
	<div class="panel-body pt-0">
		
		@php
			$urldata = http_build_query($input) . "\n";
			if(auth()->user()->hasRole('Buyer Mode')){
				$modeUrl = url("hrm/reports/monthly-salary-excel?$urldata");
			}else{
				$modeUrl = url("hr/reports/salary-summary-report?$urldata&export=excel");
			}
		@endphp

		<a href='{{ $modeUrl }}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 24px; left: 65px;"><i class="fa fa-file-excel-o"></i></a>

		
		<div id="report_section" class="report_section">
			<style type="text/css" media="print">

				h4, h2, p{margin: 0;}
				.text-right{text-align:right;}
				.text-center{text-align:center;}
			</style>
			<style>
						@media print {
				                      
				        @page {
				              size: landscape;
				          }
				          
				          
				      }
              .table{
                width: 100%;
              }
              a{text-decoration: none;}
              .table-bordered {
                  border-collapse: collapse;
              }
              .table-bordered th{
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
				.content-result .panel .panel-body .loader-p{
					margin-top: 20% !important;
				} 
				.modal-h3{
					line-height: 1;
				}
			</style>
			@php
				$formatHead = explode('_',$format);
			@endphp
			
			<div class="top_summery_section">
				<div class="page-header">
					<div>
						<h4 style="margin:4px 10px; font-weight: bold; text-align: center;">Salary Summary Report </h4>
					</div>
		            
          <div class="row w-100" style="">
          	<div style="width:26%; padding-left:15px; padding-right:15px; float:left;">
          		<p class="subhead-left-text"> <b>	Unit : </b>
          				@php
          					if(isset($input['unit']) && $input['unit'] != null){
          						foreach($input['unit'] as $un){
          							echo $unit[$un]['hr_unit_short_name']??'';
          							echo ', ';
          						}
          					}else{
          						echo 'ALL';
          					}
          				@endphp
          		</p>
          		<p class="subhead-left-text"> <b>	Location : </b>
          				@php
          					if(isset($input['location']) && $input['location'] != null){
          						foreach($input['location'] as $lo){
          							echo $location[$lo]['hr_location_short_name']??'';
          							echo ', ';
          						}
          					}else{
          						echo 'ALL';
          					}
          				@endphp
          		</p>
          		<p class="subhead-left-text"> <b> Status : </b> 
          				@php
          					if($input['emp_status'] != null){
          						foreach($input['emp_status'] as $st){
          							if($st == 1){
          								echo 'Active, ';
          							}elseif($st == 5){
          								echo 'Left, ';
          							}elseif($st == 2){
          								echo 'Resign, ';
          							}elseif($st == 3){
          								echo 'Terminate, ';
          							}elseif($st == 6){
          								echo 'Maternity';
          							}
          						}
          					}else{
          						echo 'ALL';
          					}
          				@endphp
          		</p>
          	</div>
          	<div style="width:10%; float:left;">
          		<p class="subhead-left-text"> <b>	Area : </b> 
        				@php
        					if(isset($input['area']) && $input['area'] != null){  
        							echo $area[$input['area']]['hr_area_name']??'';
        					}else{
        						echo 'ALL';
        					}
        				@endphp
          		</p>
          		<p class="subhead-left-text"> <b>	Floor : </b>
          			@php
        					if(isset($input['floor_id']) && $input['floor_id'] != null){  
        							echo $floor[$input['floor_id']]['hr_floor_name']??'';
        					}else{
        						echo 'ALL';
        					}
        				@endphp
          		</p>
          		<p class="subhead-left-text"> <b>	Line : </b> 
          			@php
        					if(isset($input['line_id']) && $input['line_id'] != null){  
        							echo $line[$input['line_id']]['hr_line_name']??'';
        					}else{
        						echo 'ALL';
        					}
        				@endphp
          		</p>
          	</div>
          	<div style="width:26%; float:left;">
	            <table class="table no-border f-14" border="0" style="width:100%;margin-bottom:0;font-size:14px;text-align:left"  cellpadding="5">
		          	<tr>
		          		<td>
		          			<p style="text-align: center; font-size: 14px;">Month : {{ date('F Y', strtotime($input['year_month'])) }} </p>
				            <p style="text-align: center; font-size: 14px;">Total Employee : {{ $summary->totalEmployees }} </p>
				            <p style="text-align: center; font-size: 14px;">Total Payable : {{ bn_money(round($summary->totalGrossPay,2)) }} </p>
				            
		          		</td>
		          	</tr>
		          </table>
          	</div>
          	
          	<div style="width:21%; float:left;">
          		<p class="subhead-left-text"> <b> Department : </b>
          			@php
        					if(isset($input['department']) && $input['department'] != null){  
        							echo $department[$input['department']]['hr_department_name']??'';
        					}else{
        						echo 'ALL';
        					}
        				@endphp
          		</p>
          		<p class="subhead-left-text"> <b> Section : </b>
          			@php
        					if(isset($input['section']) && $input['section'] != null){  
        							echo $section[$input['section']]['hr_section_name']??'';
        					}else{
        						echo 'ALL';
        					}
        				@endphp
          		</p>
          		<p class="subhead-left-text"> <b> Sub-section : </b> 
          			@php
        					if(isset($input['subSection']) && $input['subSection'] != null){  
        							echo $subSection[$input['subSection']]['hr_subsec_name']??'';
        					}else{
        						echo 'ALL';
        					}
        				@endphp
          		</p>
          		
          	</div>
          	<div style="width:12%;  float:left;">
          		
          		<p class="subhead-left-text"> <b> OT/Non-OT : </b> <span class="subhead-right-text">
          			@php
        					if(isset($input['otnonot']) && $input['otnonot'] != null){  
        						if($input['otnonot'] == 1){
        							echo 'OT';
        						}else{
        							echo 'Non-OT';
        						}
        					}else{
        						echo 'ALL';
        					}
        				@endphp
          			</span>
          		</p>
          		
          		<p class="subhead-left-text"> <b> Payment Type : </b> <span class="subhead-right-text"> 
          				@php
        					if(isset($input['pay_status']) && $input['pay_status'] != null && $input['pay_status'] != 'all'){  
        						echo $input['pay_status'];
        					}else{
        						echo 'ALL';
        					}
        					@endphp
          			</span>
          		</p>
          	</div>
          </div>
        </div>
	    </div>

			<div class="content_list_section">
				@if($input['report_format'] == 0)
					<table class="table table-bordered table-hover table-head" style="width:100%;border:0 !important;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
					@if($format == 'as_unit_id')
						@php $i = 0; @endphp
						<thead>
              <tr>
                  <th>Sl</th>
                  <th>Associate ID</th>
                  <th>Name</th>
                  <th>Unit</th>
                  <th>Designation</th>
                  <th>Department</th>
                  <th>Section</th>
                  <th>DOJ</th>
                  <th>YOS</th>
                  <th>Grade</th>
                  <th>Salary</th>
                  
              </tr>
            </thead>
					@endif
					@foreach($uniqueGroup as $group => $employees)
					
						@if($format != 'as_unit_id')
						<thead>
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
                    <th colspan="2">{{ $head }}</th>
                    <th colspan="14">{{ $body }}</th>
                    @endif
                </tr>
              @endif
              <tr>
                  <th>Sl</th>
                  <th>Associate ID</th>
                  <th>Name</th>
                  <th>Unit</th>
                  <th>Designation</th>
                  <th>Department</th>
                  <th>Section</th>
                  <th>DOJ</th>
                  <th>YOS</th>
                  <th>Grade</th>
                  <th>Salary</th>
                  
              </tr>
              
            </thead>
            @php $i = 0; @endphp
            @endif
            <tbody>
            @php $month = $input['year_month']; @endphp
            @if(count($employees) > 0)
	            @foreach($employees as $employee)
	            	@php
	            		$designationName = $designation[$employee->as_designation_id]['hr_designation_name']??'';
                        $otHour = numberToTimeClockFormat($employee->ot_hour);
	            	@endphp
	            	
		            	<tr>
		            		<td>{{ ++$i }}</td>
			            	
			            	<td>
			            		<a class="job_card" data-name="{{ $employee->as_name }}" data-associate="{{ $employee->as_id }}" data-month-year="{{ $month }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Job Card">{{ $employee->as_id }}</a>
			            	</td>
			            	<td>
			            		<b>{{ $employee->as_name }}</b>
			            	</td>
			            	<td>{{ $unit[$employee->as_unit_id]['hr_unit_short_name']??'' }}</td>
			            	<td>{{ $designationName }}</td>

			            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
			            	<td>{{ $section[$employee->as_section_id]['hr_section_name']??'' }}</td>
			            	<td>{{ date('d/m/Y', strtotime($employee->as_doj)) }}</td>
			            	<td>{{ Carbon\Carbon::createFromFormat('Y-m-d', $employee->as_doj)->diff(Carbon\Carbon::now())->format('%y.%m') }}</td>
			            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_grade']??'' }}</td>
			            	<td>
			            		{{ bn_money($employee->gross) }}
			            	</td>
			            	
		            	</tr>
	            	
	            @endforeach
            @else
	            <tr>
	            	<td colspan="7" class="text-center">No Employee Found!</td>
	            </tr>
            @endif
            	<tr style="border:0 !important;"><td colspan="7" style="border: 0 !important;height: 20px;"></td> </tr>
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
					<table class="table table-bordered table-hover table-head" border="1" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left" cellpadding="5">
						<!-- custom design for all-->
						
						<thead>
							<tr class="text-center">
								<th rowspan="2">SL.</th>
								<th rowspan="2"> {{ $head }} Name</th>
								<th colspan="3">No. of Employee</th>
								
								<th rowspan="2">Salary (BDT)</th>
							</tr>
							<tr class="text-center">
								<th>Non OT</th>
								<th>OT</th>
								<th>Total</th>
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
											$exPar = '&selected='.$unit[$group]['hr_unit_id']??'';
										}elseif($format == 'as_location'){
											$body = $location[$group]['hr_location_name']??'';
											$exPar = '&selected='.$location[$group]['hr_location_name']??'';
										}elseif($format == 'as_line_id'){
											$body = $line[$group]['hr_line_name']??'';
											$exPar = '&selected='.$body;
										}elseif($format == 'as_floor_id'){
											$body = $floor[$group]['hr_floor_name']??'';
											$exPar = '&selected='.$body;
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
											$body = $seDeName.' - '.$seName;
											$exPar = '&selected='.$section[$group]['hr_section_id']??'';
										}elseif($format == 'as_subsection_id'){
											$body = $subSection[$group]['hr_subsec_name']??'';
											$exPar = '&selected='.$subSection[$group]['hr_subsec_id']??'';
										}else{
											$body = 'N/A';
											$exPar = '';
										}
										$secUrl = $urldata.$exPar;
									@endphp
									<a onClick="selectedGroup(this.id, '{{ $body }}')" data-body="{{ $body }}" id="{{$exPar}}" class="select-group">{{ ($body == null)?'N/A':$body }}</a>
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
								
								<td class="text-right" style="font-weight: bold">
									{{ bn_money(round($groupSal->grossPayable)) }}
								</td>
								
							</tr>
							@endforeach
							 <tr>
								<td></td>
								<td class="text-center fwb"> Total </td>
								<td class="text-center fwb">{{ $summary->totalNonot }}</td>
								<td class="text-center fwb">{{ $summary->totalOt }}</td>
								<td class="text-center fwb">{{ $summary->totalEmployees }}</td>
								
								<td class="text-right fwb">{{ bn_money(round($summary->totalGrossPay)) }}</td>
								
							</tr>
							 
							@else
							<tr>
	            	<td colspan="6" class="text-center">No Data Found!</td>
	            </tr>
							@endif
						</tbody>
					</table>
				@endif
			</div>
		</div>
	</div>
</div>

{{--  --}}

<script type="text/javascript">
    
    @if(auth()->user()->hasRole('Buyer Mode'))
    	var mainurl = '/hrm/reports/salary-report?';
    @else
    	var mainurl = '/hr/reports/salary-summary-report?';
    @endif
    function selectedGroup(e, body){
    	var part = e;
    	var input = $("#filterForm").serialize() + '&' + $("#formReport").serialize();
    	{{-- var input = @json($urldata); --}}
    	var pareUrl = input+part;
    	console.log(mainurl+pareUrl)
    	$("#modal-title-right-group").html(' '+body+' Salary Details');
    	$('#right_modal_lg-group').modal('show');
    	$("#content-result-group").html(loaderContent);
    	$.ajax({
            url: mainurl+pareUrl,
            data: {
                body: body
            },
            type: "GET",
            headers: {
	          'X-CSRF-TOKEN': '{{ csrf_token() }}',
	        },
            success: function(response){
            	// console.log(response);
                if(response !== 'error'){
                	setTimeout(function(){
                		$("#content-result-group").html(response);
                	}, 1000);
                }else{
                	console.log(response);
                }
            }
        });

    }

    
</script>