<div class="panel">
	<div class="panel-body pt-0">
		
		@php
			$urldata = http_build_query($input) . "\n";
			
			$modeUrl = url("hr/reports/incentive-bonus-report?$urldata&export=excel");
		@endphp

		<a href='{{ $modeUrl }}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 21px; left: 65px;"><i class="fa fa-file-excel-o"></i></a>
		@if(isset($input['selected'])) 
		<br>
		<br>
		@endif
		<div id="report_section" class="report_section">
			<style type="text/css" media="print">

				h4, h2, p{margin: 0;}
				.text-right{text-align:right;}
				.text-center{text-align:center;}
				.subhead-left-text{text-align: left; font-size: 13px;}
				.subhead-right-text{text-align: right; font-size: 13px;}
			</style>
			<style>
				@media print {
	                      
          @page {
              size: landscape;
          }

          body{
          	font-size: 10pt !important;
            line-height: 1.1 !important;
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
							<h4 style="margin:4px 10px; font-weight: bold; text-align: center;">Incentive Bonus @if($input['report_format'] == 0) Details @else Summary @endif Report </h4>
						</div>
            <div class="row w-100" style="">

            	<div style="width:26%; padding-left:15px; padding-right:15px; float:left;">
            		<p class="subhead-left-text"> <b>	Unit : </b>
            				@php
            					if($input['unit'] != null){
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
            					if($input['location'] != null){
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
	        					if($input['area'] != null){  
	        							echo $area[$input['area']]['hr_area_name']??'';
	        					}else{
	        						echo 'ALL';
	        					}
	        				@endphp
            		</p>
            		<p class="subhead-left-text"> <b>	Floor : </b>
            			@php
	        					if($input['floor_id'] != null){  
	        							echo $floor[$input['floor_id']]['hr_floor_name']??'';
	        					}else{
	        						echo 'ALL';
	        					}
	        				@endphp
            		</p>
            		<p class="subhead-left-text"> <b>	Line : </b> 
            			@php
	        					if($input['line_id'] != null){  
	        							echo $line[$input['line_id']]['hr_line_name']??'';
	        					}else{
	        						echo 'ALL';
	        					}
	        				@endphp
            		</p>
            	</div>
            	<div style="width:26%; float:left;">
            		
            
		            <table class="table no-border f-14" border="0" style="width:100%;margin-bottom:0;font-size:13px;text-align:left"  cellpadding="5">
		            	<tr>
		            		<td>
		            			@if($input['date_type'] == 'month')
		            			<p style="text-align: center; font-size: 13px;">Month : {{ date('F Y', strtotime($input['month_year'])) }} </p>
		            			@else
		            			<p style="text-align: center; font-size: 13px;">Date : {{ date('Y-m-d', strtotime($input['from_date'])) }} To {{ date('Y-m-d', strtotime($input['to_date'])) }}</p>
		            			@endif
					            <p style="text-align: center; font-size: 13px;">Total Employee : {{ $summary->totalEmployees }} </p>
					            
					            <p style="text-align: center; font-size: 13px;">Total Incentive : {{ bn_money(round($summary->totalIncentive,2)) }} </p>
		            		</td>
		            	</tr>
		            </table>
            	</div>
            	
            	<div style="width:21%; float:left;">
            		<p class="subhead-left-text"> <b> Department : </b>
            			@php
	        					if($input['department'] != null){  
	        							echo $department[$input['department']]['hr_department_name']??'';
	        					}else{
	        						echo 'ALL';
	        					}
	        				@endphp
            		</p>
            		<p class="subhead-left-text"> <b> Section : </b>
            			@php
	        					if($input['section'] != null){  
	        							echo $section[$input['section']]['hr_section_name']??'';
	        					}else{
	        						echo 'ALL';
	        					}
	        				@endphp
            		</p>
            		<p class="subhead-left-text"> <b> Sub-section : </b> 
            			@php
	        					if($input['subSection'] != null){  
	        							echo $subSection[$input['subSection']]['hr_subsec_name']??'';
	        					}else{
	        						echo 'ALL';
	        					}
	        				@endphp
            		</p>
            		{{-- <p class="subhead-left-text"> <b> Designation : </b> 
            			@php
	        					if(isset($input['designation']) && $input['designation'] != null){  
	        							echo $designation[$input['designation']]['hr_designation_name']??'';
	        					}else{
	        						echo 'ALL';
	        					}
	        				@endphp
            		</p> --}}
            	</div>
            	<div style="width:12%;  float:left;">
            		<p class="subhead-left-text"> <b> OT/Non-OT : </b> <span class="subhead-right-text">
            			@php
	        					if($input['otnonot'] != null){  
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
            		<p class="subhead-left-text"> <b> Pay Status : </b> <span class="subhead-right-text">
            				@php
	        					if($input['pay_type'] != null){  
	        						if($input['pay_type'] == 1){
	        							echo 'Paid';
	        						}else{
	        							echo 'Due';
	        						}
	        					}else{
	        						echo 'ALL';
	        					}
	        					@endphp
            			</span>
            		</p>
            		<p class="subhead-left-text"> <b> Payment Type : </b> <span class="subhead-right-text"> 
            				@php
	        					if($input['pay_status'] != null && $input['pay_status'] != 'all'){  
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
						@if(count($uniqueGroup) > 0)
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
		                  <th>SL.</th>
		                  <th>ID</th>
		                  <th>Name</th>
		                  <th>Designation</th>
		                  <th>Department</th>
		                  <th>Section</th>
		                  <th>Sub Section</th>
		                  <th>Floor</th>
		                  <th>Line</th>
		                  <th>Payment Method</th>
		                  <th>Incentive Amount</th>
		                  <th>Days</th>
		              </tr>
		          </thead>
		          <tbody>
		          @php 
		          	$i = 0; $month = date('Y-m', strtotime($input['to_date'])); 
		          @endphp
		          @if(count($employees) > 0)
		            @foreach($employees as $employee)
		            	@php
		            		$designationName = $designation[$employee->as_designation_id]['hr_designation_name']??'';
		            	@endphp
		            	
		            	<tr>
		            		<td>{{ ++$i }}</td>
			            	
			            	<td>
			            		<a class="job_card" data-name="{{ $employee->as_name }}" data-associate="{{ $employee->associate_id }}" data-month-year="{{ $month }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Job Card">{{ $employee->associate_id }}</a>
			            	</td>
			            	<td>
			            		<b>{{ $employee->as_name }}</b>
			            	</td>
			            	<td>{{ $designationName }}</td>
			            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
			            	<td>{{ $section[$employee->as_section_id]['hr_section_name']??'' }}</td>
			            	<td>{{ $subSection[$employee->as_subsection_id]['hr_subsec_name']??'' }}</td>
			            	<td>{{ $floor[$employee->as_floor_id]['hr_floor_name']??'' }}</td>
			            	<td>{{ $line[$employee->as_line_id]['hr_line_name']??'' }}</td>
			            	
			            	<td>
			            		@if($employee->bank_payable == 0 && $employee->cash_payable > 0)
			            			Cash
			            		@elseif($employee->bank_payable > 0)
			            		<b>{{ $employee->bank_name }}</b>
			            		<b>{{ ($employee->bank_payable > 0)?$employee->bank_no:'' }}</b>
			            		@endif
			            	</td>
			            	<td>
			            		{{ bn_money($employee->amount) }}
			            	</td>
			            	<td>
			            		<a class="popover-data w-100">{{ $employee->count }}</a>
			            	</td>
		            	</tr>
		            	
		            @endforeach
		          @else
		            <tr>
		            	<td colspan="12" class="text-center">No Employee Found!</td>
		            </tr>
		          @endif
		          	<tr style="border:0 !important;"><td colspan="12" style="border: 0 !important;height: 20px;"></td> </tr>
		          </tbody>
				            
						@endforeach
						@else
						<thead>
              <tr>
                <th>SL.</th>
                <th>ID</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Department</th>
                <th>Section</th>
                <th>Sub Section</th>
                <th>Floor</th>
                <th>Line</th>
                <th>Payment Method</th>
                <th>Incentive Amount</th>
                <th>Days</th>
              </tr>
          	</thead>
          	<tbody>
          		<tr>
          			<td colspan="12" class="text-center">No Employee Found!</td>
          		</tr>
          	</tbody>
          	@endif
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
								<th colspan="3">Employee Amount (BDT)</th>
								<!--<th colspan="2">Cash & Bank (BDT)</th>-->
								<th rowspan="2">Paid Amount (BDT)</th>
								<th rowspan="2">Due Amount (BDT)</th>
							</tr>
							<tr class="text-center">
								<th>Non OT</th>
								<th>OT</th>
								<th>Total</th>
								<th>Non OT Holder</th>
								<th>OT Holder</th>
								<th>Total</th>
								<!--<th>Cash</th>-->
								<!--<th>Bank</th>-->
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
											$exPar = '&selected='.$location[$group]['hr_location_id']??'';
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
									{{ bn_money(round($groupSal->nonotAmount)) }}
								</td>
								<td class="text-right">
									{{ bn_money(round($groupSal->otAmount)) }}
								</td>
								<td class="text-right">
									{{ bn_money(round($groupSal->incentiveAmount)) }}
								</td>
								
								<!--<td class="text-right">-->
								<!--	{{ bn_money(round($groupSal->cashPayable)) }}-->
								<!--</td>-->
								
								<!--<td class="text-right">-->
								<!--	{{ bn_money(round($groupSal->bankPayable)) }}-->
								<!--</td>-->
								<td class="text-right">
									{{ bn_money(round($groupSal->paidAmount)) }}
								</td>

								<td class="text-right">
									{{ bn_money(round($groupSal->incentiveAmount - $groupSal->paidAmount)) }}
								</td>
							</tr>
							@endforeach
							 <tr>
								<td></td>
								<td class="text-center fwb"> Total </td>
								<td class="text-center fwb">{{ $summary->totalNonot }}</td>
								<td class="text-center fwb">{{ $summary->totalOt }}</td>
								<td class="text-center fwb">{{ $summary->totalEmployees }}</td>
								<td class="text-right fwb">{{ bn_money(round($summary->totalNonotAmount)) }}</td>
								<td class="text-right fwb">{{ bn_money(round($summary->totalOtAmount)) }}</td>
								<td class="text-right fwb" style="font-weight: bold">{{ bn_money(round($summary->totalIncentive)) }}</td>
								<!--<td class="text-right fwb">{{ bn_money(round($summary->totalCash)) }}</td>-->
								<!--<td class="text-right fwb">{{ bn_money(round($summary->totalBank)) }}</td>-->
								<td class="text-right fwb" style="font-weight: bold">{{ bn_money(round($summary->totalPaidAmount)) }}</td>
								<td class="text-right fwb" style="font-weight: bold">{{ bn_money(round($summary->totalIncentive - $summary->totalPaidAmount)) }}</td>
							</tr>
							 
							@else
							<tr>
				          <td colspan="9" class="text-center">No Data Found!</td>
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

<script>
	
  var mainurl = '/hr/reports/incentive-bonus-report?';

  function selectedGroup(e, body){
  	var part = e;
  	var input = $("#filterForm").serialize() + '&' + $("#formReport").serialize();
  	{{-- var input = @json($urldata); --}}
  	var pareUrl = input+part;
  	// console.log(mainurl+pareUrl)
  	$("#modal-title-right-group").html(' '+body+' Incentive Details');
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

   //  $('[data-toggle="popover"]').popover({
	  //   html: true,
	  //   trigger: 'manual',
	  //   content: function() {
	  //     return $.ajax({
		 //      url : "/hr/reports/incentive-single-report",
		 //      data:{
		 //      	as_id: $(this).data('id'),
		 //      	from_date: $(this).data('fromdate'),
		 //      	to_date: $(this).data('todate'),
		 //      	pay_status: $(this).data('paytype'),
		 //      	unit: $(this).data('unit')
		 //      },
		 //      dataType: 'html',
		 //      type: 'get',
		 //      async: false,
		 //      success: function(response)
		 //      {
		 //      	return response;
		 //      },
		 //      error: function(reject)
		 //      {
		 //        return '';
		 //      }
		 //    }).responseText;

	  //   }
	  // }).click(function(e) {
	  // 	// $(".popover-data").popover('hide');
	  //   $(this).popover('toggle');
	  // });
</script>