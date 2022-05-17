<div class="panel">
	<div class="panel-body pt-0">
		
		@php
			$urldata = http_build_query($input) . "\n";
			if(auth()->user()->hasRole('Buyer Mode')){
				$modeUrl = url("hrm/reports/monthly-salary-excel?$urldata");
			}else{
				$modeUrl = url("hr/reports/salary-report?$urldata&export=excel");
			}
		@endphp

		<a href='{{ $modeUrl }}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 21px; left: 65px;"><i class="fa fa-file-excel-o"></i></a>

		
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
						<h4 style="margin:4px 10px; font-weight: bold; text-align: center;">Salary @if($input['report_format'] == 0) Details @else Summary @endif Report </h4>
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
				            @if($summary->totalAdvanceAmount == 0)
					            <p style="text-align: center; font-size: 14px;">Total Payable : {{ bn_money(round($summary->totalSalary,2)) }} </p>
					          @else
					            <p style="text-align: center; font-size: 14px;">Total Payable : {{ bn_money(round(($summary->totalSalary + $summary->totalAdvanceAmount),2)) }} </p>
					            <p style="text-align: center; font-size: 14px;">Advance Paid : {{ bn_money(round($summary->totalAdvanceAmount,2)) }} </p>
	                		<p style="text-align: center; font-size: 14px;">Remaining Payable : {{ bn_money(round(($summary->totalSalary),2)) }} </p>
					          @endif
				            
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
					<table class="table table-bordered table-hover table-head table-responsive" style="width:100%;border:0 !important;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
					@foreach($uniqueGroup as $group => $employees)
					
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
                    <th>Designation</th>
                    <th>Department</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>OT Hour</th>
                    <th>Payment Method</th>
                    <th>Payable Salary</th>
                    @if($input['pay_status'] == 'all' || ($input['pay_status'] != 'cash' && $input['pay_status'] != null))
                    <th>Bank Amount</th>
                    <th>Tax Amount</th>
                    @endif
                    @if($input['pay_status'] == 'all' || $input['pay_status'] == 'cash')
                    <th>Cash Amount</th>
                    @endif
                    <th>Stamp Amount</th>
                    <th>Net Pay</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            @php $i = 0; $otHourSum=0; $salarySum=0; $month = $input['year_month']; @endphp
            @if(count($employees) > 0)
	            @foreach($employees as $employee)
	            	@php
	            		$designationName = $designation[$employee->as_designation_id]['hr_designation_name']??'';
                        $otHour = numberToTimeClockFormat($employee->ot_hour);
	            	@endphp
	            	@if($head == '')
		            	<tr>
		            		<td>{{ ++$i }}</td>
			            	
			            	<td>
			            		<a class="job_card" data-name="{{ $employee->as_name }}" data-associate="{{ $employee->as_id }}" data-month-year="{{ $month }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Job Card">{{ $employee->as_id }}</a>
			            	</td>
			            	<td>
			            		<b>{{ $employee->as_name }}</b>
			            	</td>
			            	<td>{{ $designationName }}</td>

			            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
			            	<td>{{ $employee->present }}</td>
			            	<td>{{ $employee->absent }}</td>
			            	<td><b>{{ $otHour }}</b></td>
			            	<td>
			            		@if($employee->pay_status == 1)
			            			Cash
			            		@elseif($employee->pay_status == 2)
			            		<b>{{ $employee->pay_type }}</b>
			            		<b>{{ ($employee->bank_payable > 0)?$employee->bank_no:'' }}</b>
			            		@else
			            		Bank & Cash
			            		<b>{{ ($employee->bank_payable > 0)?$employee->bank_no:'' }}</b>
			            		@endif
			            	</td>
			            	<td>
			            		@php $totalPay = $employee->total_payable + $employee->stamp; @endphp
			            		{{ bn_money($totalPay) }}
			            	</td>	
			            	@if($input['pay_status'] == 'all' || ($input['pay_status'] != 'cash' && $input['pay_status'] != null))
			            	<td>{{ bn_money($employee->bank_payable) }}</td>
			            	<td>{{ bn_money($employee->tds) }}</td>
			            	@endif
			            	@if($input['pay_status'] == 'all' || $input['pay_status'] == 'cash')
			            	<td>{{ bn_money($employee->cash_payable + $employee->stamp) }}</td>
			            	@endif
			            	<td>{{ bn_money($employee->stamp) }}</td>
			            	
			            	<td>
			            		@php
			            			if($input['pay_status'] == 'cash'){
			            				$totalNet = $employee->cash_payable;
			            			}else{
			            				$totalNet = $employee->total_payable - $employee->tds;
			            			}
			            		@endphp
			            		{{ bn_money($totalNet) }}
			            	</td>
			            	<td>
			            		<button type="button" class="btn btn-primary btn-sm yearly-activity" data-id="{{ $employee->as_id}}" data-eaid="{{ $employee->as_id }}" data-ename="{{ $employee->as_name }}" data-edesign="{{ $designationName }}" data-yearmonth="{{ $input['year_month'] }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='Employee Salary Sheet' ><i class="fa fa-eye"></i></button>
			            	</td>
		            	</tr>
	            	@else
	            	
		            	<tr>
		            		<td>{{ ++$i }}</td>
			            	
			            	<td>
			            		<a @if(auth()->user()->hasRole('Buyer Mode'))@else class="job_card" @endif data-name="{{ $employee->as_name }}" data-associate="{{ $employee->as_id }}" data-month-year="{{ $month }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Job Card">{{ $employee->as_id }}</a>
			            	</td>
			            	<td>
			            		<b>{{ $employee->as_name }}</b>
			            	</td>
			            	<td>{{ $designationName }}</td>
			            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
			            	<td>{{ $employee->present }}</td>
			            	<td>{{ $employee->absent }}</td>
			            	<td><b>{{ $otHour }}</b></td>
			            	<td>
			            		@if($employee->pay_status == 1)
			            			Cash
			            		@elseif($employee->pay_status == 2)
			            		<b class="uppercase">{{ $employee->pay_type }}</b>
			            		<br>
			            		<b>{{ ($employee->bank_payable > 0)?$employee->bank_no:'' }}</b>
			            		@else
			            		<b class="uppercase">{{ $employee->pay_type }}</b> & Cash
			            		<br>
			            		<b>{{ ($employee->bank_payable > 0)?$employee->bank_no:'' }}</b>
			            		@endif
			            	</td>
			            	<td>
			            		@php $totalPay = $employee->total_payable + $employee->stamp; @endphp
			            		{{ bn_money($totalPay) }}
			            	</td>	
			            	@if($input['pay_status'] == 'all' || ($input['pay_status'] != 'cash' && $input['pay_status'] != null))
			            	<td>{{ bn_money($employee->bank_payable) }}</td>
			            	<td>{{ bn_money($employee->tds) }}</td>
			            	@endif
			            	@if($input['pay_status'] == 'all' || $input['pay_status'] == 'cash')
			            	<td>{{ bn_money($employee->cash_payable) }}</td>
			            	@endif
			            	<td>{{ bn_money($employee->stamp) }}</td>
			            	<td>
			            		@php
			            			if($input['pay_status'] == 'cash'){
			            				$totalNet = $employee->cash_payable;
			            			}else{
			            				$totalNet = $employee->total_payable - $employee->tds;
			            			}
			            		@endphp
			            		{{ bn_money($totalNet) }}
			            	</td>
			            	<td>
			            		<button type="button" class="btn btn-primary btn-sm yearly-activity" data-id="{{ $employee->as_id}}" data-eaid="{{ $employee->as_id }}" data-ename="{{ $employee->as_name }}" data-edesign="{{ $designationName }}" data-yearmonth="{{ $input['year_month'] }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='Employee Salary Sheet' ><i class="fa fa-eye"></i></button>
			            	</td>
		            	</tr>
	            	
	            	@endif
	            @endforeach
            @else
	            <tr>
	            	@if($input['pay_status'] == 'cash')
	            	<td colspan="15" class="text-center">No Employee Found!</td>
	            	@elseif($input['pay_status'] != 'cash' && $input['pay_status'] != 'all')
	            	<td colspan="15" class="text-center">No Employee Found!</td>
	            	@else
	            	<td colspan="15" class="text-center">No Employee Found!</td>
	            	@endif
	            </tr>
            @endif
            	<tr style="border:0 !important;"><td colspan="16" style="border: 0 !important;height: 20px;"></td> </tr>
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
								<th rowspan="2">OT Hour</th>
								@if($summary->totalAdvanceAmount > 0)
									<th rowspan="2">Total Salary</th>
									<th rowspan="2">Advance Salary</th>
									<th rowspan="2">Remaining Payable Salary</th>
								@else
									<th rowspan="2">Payable Salary</th>
								@endif
								<th colspan="5">Salary segmentation (BDT)</th>
								<th rowspan="2">Net Pay Amount</th>
								<th colspan="3">Cash & Bank (BDT)</th>
							</tr>
							<tr class="text-center">
								<th>Non OT</th>
								<th>OT</th>
								<th>Total</th>
								<th>Salary</th>
								<th>Wages</th>
								<th>OT Amount</th>
								<th>Food Deduct</th>
								<th>stamp</th>
								<th>Cash</th>
								<th>Bank</th>
								<th>Tax</th>
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
								<td class="text-right">
									{{ numberToTimeClockFormat($groupSal->otHour) }}
								</td>
								@if($summary->totalAdvanceAmount > 0)
									<td class="text-right">
										{{ bn_money(round($groupSal->totalPayable+$groupSal->stamp+$groupSal->foodAmount + $groupSal->advanceAmount)) }}
									</td>
									<td class="text-right">
										{{ bn_money(round($groupSal->advanceAmount)) }}
									</td>
								@endif
								<td class="text-right">
									{{ bn_money(round($groupSal->totalPayable+$groupSal->stamp+$groupSal->foodAmount)) }}
								</td>
								<td class="text-right">
									{{ bn_money(round($groupSal->nonotAmount)) }}
								</td>
								<td class="text-right">
									{{ bn_money(round($groupSal->otAmount)) }}
								</td>
								<td class="text-right">
									{{ bn_money(round($groupSal->otHourAmount)) }}
								</td>
								
								<td class="text-right">{{ bn_money(round($groupSal->foodAmount))}}</td>
								
								<td class="text-right">
									{{ bn_money(round($groupSal->stamp)) }}
								</td>
								<td class="text-right" style="font-weight: bold">
									{{ bn_money(round($groupSal->totalPayable)) }}
								</td>

								<td class="text-right">
									{{ bn_money(round($groupSal->cashPayable)) }}
								</td>
								
								<td class="text-right">
									{{ bn_money(round($groupSal->bankPayable)) }}
								</td>
								<td class="text-right">
									{{ bn_money(round($groupSal->tds)) }}
								</td>
								
							</tr>
							@endforeach
							 <tr>
								<td></td>
								<td class="text-center fwb"> Total </td>
								<td class="text-center fwb">{{ $summary->totalNonot }}</td>
								<td class="text-center fwb">{{ $summary->totalOt }}</td>
								<td class="text-center fwb">{{ $summary->totalEmployees }}</td>
								<td class="text-right fwb">{{ numberToTimeClockFormat(round($summary->totalOtHour,2)) }}</td>
								@if($summary->totalAdvanceAmount > 0)
									<td class="text-right fwb">{{ bn_money(round($summary->totalSalary + $summary->totalStamp + $summary->totalFood + $summary->totalAdvanceAmount)) }}</td>
									<td class="text-right fwb">{{ bn_money(round($summary->totalAdvanceAmount)) }}</td>
								@endif
								<td class="text-right fwb">{{ bn_money(round($summary->totalSalary + $summary->totalStamp + $summary->totalFood)) }}</td>
								<td class="text-right fwb">{{ bn_money(round($summary->totalNonotAmount)) }}</td>
								<td class="text-right fwb">{{ bn_money(round($summary->totalOtAmount)) }}</td>
								<td class="text-right fwb">{{ bn_money(round($summary->totalOTHourAmount)) }}</td>
								
								<td class="text-right fwb">{{ bn_money(round($summary->totalFood)) }}</td>
								
								<td class="text-right fwb">{{ bn_money(round($summary->totalStamp)) }}</td>
								
								<td class="text-right fwb" style="font-weight: bold">{{ bn_money(round($summary->totalSalary)) }}</td>
								<td class="text-right fwb">{{ bn_money(round($summary->totalCashSalary)) }}</td>
								<td class="text-right fwb">{{ bn_money(round($summary->totalBankSalary)) }}</td>
								<td class="text-right fwb">{{ bn_money(round($summary->totalTax)) }}</td>
								
							</tr>
							 
							@else
							<tr>
				            	<td colspan="16" class="text-center">No Data Found!</td>
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
    	var mainurl = '/hr/reports/salary-report?';
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