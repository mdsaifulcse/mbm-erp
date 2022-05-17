<div class="panel">
	<div class="panel-body pt-0">
		
		@php
			$urldata = http_build_query($input) . "\n";
			
			$modeUrl = url("hr/reports/employee-cross-analysis-filter-report?$urldata&export=excel");
			
		@endphp

		<a href='{{ $modeUrl }}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 23px; left: 65px;"><i class="fa fa-file-excel-o"></i></a>

		
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
			
			<div class="top_summery_section">
				<div class="page-header">
					<div>
						<h4 style="margin:4px 10px; font-weight: bold; text-align: center;">Cross Analysis @if($input['report_format'] == 0) Details @else Summary @endif Report </h4>
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
		          			<p style="text-align: center; font-size: 14px;">Month : {{ date('F Y', strtotime($input['month_from'])) }} to {{ date('F Y', strtotime($input['month_to'])) }} </p>
				            
				            
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
          	</div>
          </div>
        </div>
	    </div>
	    <hr>
			<div class="content_list_section">
				@if(($input['report_format'] == 1 && $format != null))
					@php
						if($format == 'as_unit_id'){
							$head = 'Unit';
						}elseif($format == 'as_location'){
							$head = 'Location';
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
					@foreach($getSalaryGroup as $group => $groupSalary)
						@if($group != '')
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
						<div class="row">
							<div class="col-sm-6">
								<table class="table table-bordered table-hover table-head" style="width:100%;border:0 !important;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
									<thead>
			                <tr>
			                    <th colspan="2">
			                    	<span>{{ $head }}</span>
			                    </th>
			                    <th colspan="11">{{ $body }}</th>
			                </tr>
			                <tr>
			                    <th width="2%">SL</th>
			                    <th>Month, Year</th>
			                    <th>Join Employee</th>
			                    <th>Left Employee</th>
			                    <th>Active Employee</th>
			                    <th class="text-right">Active Salary</th>
			                </tr>
			            </thead>
			            <tbody>
			            	@php $i = 0; @endphp
			            	@foreach($groupSalary as $salary)
				            	<tr>
				            		<td>{{ ++$i }}</td>
				            		<td>{{ date('F, Y', strtotime($salary->yearmonth)) }}</td>
				            		<td>
				            			<a onClick="selectedGroup(this.id, '{{ $body }}', '{{ $salary->yearmonth }}', 'join')" data-body="{{ $body }}" id="{{$exPar}}" class="select-group">{{ $salary->joinemp }}</a>
				            		</td>
				            		<td>
				            			{{ $salary->leftresignemp }}
				            		</td>
				            		<td>
				            			{{ $salary->empcount }}
				            		</td>
				            		<td class="text-right">
				            			{{ $salary->totalSalary }}
				            		</td>
				            	</tr>
				            @endforeach
			            </tbody>
			            <tfoot>
			            	<tr>
			            		<td colspan="6"></td>
			            	</tr>
			            </tfoot>
								</table>
							</div>
							<div class="col-sm-6">
								<div id="chart-{{$group}}"></div>

							</div>
						</div>
						<br>
						@endif
					@endforeach
				@endif
			</div>
		</div>
	</div>
</div>
{{--  --}}


<script type="text/javascript">
    var mainurl = '/hr/reports/salary-report?';

    function selectedGroup(e, body, month, type){
    	var part = e+'selec_type='+type+'selec_month='+month;
    	var input = $("#filterForm").serialize() + '&' + $("#formReport").serialize();
    	var pareUrl = input+part;
    	console.log(pareUrl);
    	// $("#modal-title-right-group").html(' '+body+' Cross Analysis Details');
    	// $('#right_modal_lg-group').modal('show');
    	// $("#content-result-group").html(loaderContent);
    	// $.ajax({
     //      url: mainurl+pareUrl,
     //      data: {
     //          body: body
     //      },
     //      type: "GET",
     //      headers: {
     //      'X-CSRF-TOKEN': '{{ csrf_token() }}',
     //    },
     //      success: function(response){
     //      	// console.log(response);
     //          if(response !== 'error'){
     //          	setTimeout(function(){
     //          		$("#content-result-group").html(response);
     //          	}, 1000);
     //          }else{
     //          	console.log(response);
     //          }
     //      }
     //  });

    }

    @if($input['report_format'] == 1)
    var j = 0;
    @foreach($getSalaryGroup as $group1 => $groupSalary)
	    @if($group1 != '')
		    @php 
		    	$month = array_column($groupSalary->toArray(),'yearmonth');
		    	$salary = array_column($groupSalary->toArray(),'totalSalary');
		    	$joinSalary = array_column($groupSalary->toArray(),'totalJoinSalary');
		    	$leftSalary = array_column($groupSalary->toArray(),'totalLeftResignSalary');
		    @endphp
		    setTimeout(function(){
			    if (jQuery("#chart-{{ $group1 }}").length) {
				    var options = {
				        chart: {
				            height: 300,
				            type: 'line',
				            stacked: false,
				        },
				        stroke: {
				            width: [0, 2, 5],
				            curve: 'smooth'
				        },
				        plotOptions: {
				            bar: {
				                columnWidth: '50%'
				            }
				        },
				        colors: ['#089bab','#e64141' ,'#FC9F5B'],
				        series: [{
				            name: 'Salary',
				            type: 'column',
				            data: @php echo json_encode($salary); @endphp
				        }, {
				            name: 'Left Salary',
				            type: 'area',
				            data: @php echo json_encode($leftSalary); @endphp
				        }, {
				            name: 'Join Salary',
				            type: 'line',
				            data: @php echo json_encode($joinSalary); @endphp
				        }],
				        fill: {
				            opacity: [0.85, 0.25, 1],
				            gradient: {
				                inverseColors: false,
				                shade: 'light',
				                type: "vertical",
				                opacityFrom: 0.85,
				                opacityTo: 0.55,
				                stops: [0, 100, 100, 100]
				            }
				        },
				        labels: @php echo json_encode($month); @endphp,
				        markers: {
				            size: 0
				        },
				        xaxis: {
				            type: 'datetime'
				        },
				        yaxis: {
				            min: 0
				        },
				        tooltip: {
				            shared: true,
				            intersect: false,
				            y: {
				                formatter: function(y) {
				                    if (typeof y !== "undefined") {
				                        return y.toFixed(0) + " Tk.";
				                    }
				                    return y;

				                }
				            }
				        },
				        legend: {
				            labels: {
				                useSeriesColors: true
				            },
				            markers: {
				                customHTML: [
				                    function() {
				                        return ''
				                    },
				                    function() {
				                        return ''
				                    },
				                    function() {
				                        return ''
				                    }
				                ]
				            }
				        }
				    }

				    var chart = new ApexCharts(
				        document.querySelector("#chart-{{ $group1 }}"),
				        options
				    );

				    chart.render();
					}
					
					++j;
					
				}, 1000);
	    @endif
    @endforeach
    @endif
</script>
