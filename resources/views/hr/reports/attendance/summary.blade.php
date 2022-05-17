@php
	$unit = unit_by_id();
	$location = location_by_id();
	$floor = floor_by_id();
	$line = line_by_id();
	$libArea = area_by_id();
	$libDepartment = department_by_id();
	$libSection = section_by_id();
	$libSubSection = subSection_by_id();

@endphp


{{-- looping through ot and non ot --}}
@php
	$urldata = http_build_query($request) . "\n";
@endphp
<a href='{{ url("hr/reports/get_att_summary_report?$urldata")}}&export=excel' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute;top: 10px;left: 60px;font-size: 12px;"><i class="fa fa-file-excel-o"></i></a>

<div id="attendance-content"> 
	<style type="text/css">
		.center{text-align: center!important;}
		.dept-title{
			text-align: center;
    		background: #ceebee;
    		font-weight: bold;
		}
		.bg-shadow{background: #fcece2;}
		.table th, .table td {padding: 2px 5px;}
		.text-right{text-align:right;}
		.text-center{text-align:center;}
		.subhead-left-text {
		    text-align: left;
		    font-size: 13px;
		    width: 28%;
		    display: inline-block;
		    vertical-align: inherit;
		}
		.subhead-right-text {
		    font-size: 13px;
		    display: inline-block;
		    width: 70%;
		    padding-right: 8px;
		    vertical-align: top;
		}
		.tag-title{
		    background: #f1e9e6;
		    text-align: center;
		    margin: 10px 0;
		}
		.flex{display: flex;}
		h3{
			font-weight: bold;
			font-size: 20px !important;
		}
		.info-box{
		    width: 100px;
		    border: 1px solid #d1d1d1;
		    border-radius: 5px;
		    padding: 10px;
		    text-align: center;
		    margin: 5px;
		}
		.info-box span{display: block;}
		.info-box span.number{
		    display: block;
		    font-size: 18px;
		    font-weight: 500;
		    color: #089eaf;
		}
		@media print {
			b{font-size: 8pt !important;}
			span{font-size: 8pt !important;}
			table{border: none!important;}
			.popover-block-container{display: none !important;}
		}
	</style>
	<div style="text-align: center;margin-bottom: 10px;">
		<h2 style="margin-bottom:10px;">Daily Attendance Summary Report</h2>
		<h4 style="margin-bottom: 15px;"> 	Unit : 
			@php
				if($request['unit'] != null){
					foreach($request['unit'] as $un){
						echo $unit[$un]['hr_unit_short_name']??'';
						echo ', ';
					}
				}else{
					echo 'ALL';
				}
			@endphp
		</h4>
    	
	</div>
<div style="display: flex;">
	<input type="hidden" id="query_params" value="{{http_build_query($request)}}">
	<div style="width:40%">
		<p class="flex"> <b class="subhead-left-text">	Date : </b>
    			<b class="subhead-right-text"> {{date('d F, Y', strtotime($date))}}</b>
    		</p>
    		
    		<p class="flex"> <b class="subhead-left-text">	Location : </b> <span class="subhead-right-text">
    				@php
    					if($request['location'] != null){
    						foreach($request['location'] as $lo){
    							echo $location[$lo]['hr_location_short_name']??'';
    							echo ', ';
    						}
    					}else{
    						echo 'ALL';
    					}
    				@endphp
    			</span>
    		</p>

    	
    		@if($request['otnonot'] != null)
    		<p class="flex"> 
    			<b class="subhead-left-text"> OT/Non-OT : </b>
    			<span class="subhead-right-text">
    				@if($request['otnonot'] == 1) OT @elseif($request['otnonot'] == 0) Non-OT @endif
    			</span>
    		</p>
    		@endif
    		@if($request['floor_id'] != null)
    		<p class="flex"> 
    			<b class="subhead-left-text">	Floor : </b>
    			<span class="subhead-right-text">
	    			{{($floor[$request['floor_id']]['hr_floor_name']??'')}}
	    		</span>
    		</p>
    		@endif
    		@if($request['line_id'] != null)
    		<p class="flex"> <b class="subhead-left-text">	Line : </b><span class="subhead-right-text"> 
    			{{($line[$request['line_id']]['hr_line_name']??'')}}
    			</span>
    		</p>
    		@endif
    		@if($request['area'] != null)
    		<p class="flex"> <b class="subhead-left-text">	Area : </b><span class="subhead-right-text"> 
    			{{($libArea[$request['area']]['hr_area_name']??'')}}
    			</span>
    		</p>
    		@endif

    		{{-- checking departments --}}
    		@if(isset($request['excludes']['as_department_id']))
    			<p class="flex">
    				<b class="subhead-left-text">Departments :</b> 
    				<span class="subhead-right-text"> <span style="color:red;"> Excluding </span> 
    					@foreach($request['excludes']['as_department_id'] as $k => $d)
    						{{$libDepartment[$d]['hr_department_name']??''}},
    					@endforeach
    				</span>
    			</p>
    		@elseif($request['department'] != null)
    		<p class="flex"> <b class="subhead-left-text"> Department : </b><span class="subhead-right-text">
    			{{($libDepartment[$request['department']]['hr_department_name']??'')}}
    			</span>
    		</p>
    		@endif

    		{{-- checking sections --}}

    		@if(isset($request['excludes']['as_section_id']))
    			<p class="flex">
    				<b class="subhead-left-text">Sections :</b> 
    				<span class="subhead-right-text"> <span style="color:red;"> Excluding </span> 
    					@foreach($request['excludes']['as_section_id'] as $k => $d)
    						{{$libSection[$d]['hr_section_name']??''}},
    					@endforeach
    				</span>
    			</p>
    		@elseif($request['section'] != null)
    		<p class="flex"> <b class="subhead-left-text"> Section : </b><span class="subhead-right-text">
    			{{($libSection[$request['section']]['hr_section_name']??'')}}
    			</span>
    		</p>
    		@endif

    		{{-- checking subsections --}}
    		@if(isset($request['excludes']['as_subsection_id']))
    			<p class="flex">
    				<b class="subhead-left-text">Sub-Sections :</b> 
    				<span class="subhead-right-text"> <span style="color:red;"> Excluding </span> 
    					@foreach($request['excludes']['as_subsection_id'] as $k => $d)
    						{{$libSubSection[$d]['hr_subsec_name']??''}},
    					@endforeach
    				</span>
    			</p>
    		@elseif($request['subSection'] != null)
    		<p class="flex"> <b class="subhead-left-text"> Sub-section : </b><span class="subhead-right-text"> 
    			{{($libSubSection[$request['subSection']]['hr_subsec_name']??'')}}
    			</span>
    		</p>
    		@endif

    		@if($date == date('Y-m-d'))
    		<p class="flex"><b class="subhead-left-text">Performing Shift : </b> <span class="subhead-right-text">{{ count($runningShift)}}/{{count($allShift) }} </span> </p>
    		{{-- <p class="flex"><b class="subhead-left-text">Enrolled Shift : </b> <span class="subhead-right-text">{{ implode(", ",$runningShift) }} </span> </p> --}}
    		@endif


    		

    		
		
	</div>
	<div style="width:60%">
		<table class="table table-bordered" style="border:none;">
			<thead>
				<tr>
					<th>OT/NonOT</th>
					<th class="center">Total Enrolled</th>
					<th class="center">Current Enrolled</th>
					<th class="center">Present</th>
					<th class="center">Absent</th>
					<th class="center">Holiday</th>
					<th class="center">Leave</th>
					<th class="center">Absent (%)</th>
				</tr>
				@foreach($attSummary as $key => $ot)
					<tr>
						
						@php 
							$ctot = $ot->t - $ot->u;
							$perot = round(($ot->a/($ctot == 0?1:$ctot))*100,2);
						@endphp
						<td>
							@if($key == 1) OT @else NonOT @endif
						</td>
						<td class="center">{{$ot->t}}</td>
						<td class="center">{{$ctot}}</td>
						<td class="center cursor-pointer text-primary" data-toggle="tooltip" data-placement="top" title="Preview  present employees" onclick="fetchEmployeeByStatus('P',{{$key}})">{{$ot->p}}</td>
						<td class="center cursor-pointer text-primary" data-toggle="tooltip" data-placement="top" title="Preview  absent  employees" onclick="fetchEmployeeByStatus('A',{{$key}})">{{$ot->a}}</td>
						<td class="center cursor-pointer text-primary" data-toggle="tooltip" data-placement="top" title="Preview  holiday employees" onclick="fetchEmployeeByStatus('H',{{$key}})">{{$ot->h}}</td>
						<td class="center cursor-pointer text-primary" data-toggle="tooltip" data-placement="top" title="Preview  leave  employees" onclick="fetchEmployeeByStatus('L',{{$key}})">{{$ot->l}}</td>

						<td class="center @if($perot > 80)bg-danger @elseif($perot > 50)bg-warning @elseif($perot > 0)bg-shadow @endif ">
							{{$perot}}
						</td>
					</tr>
				@endforeach
				<tr>
					@php
						$tp = $attSummary->sum('p');
						$tt = $attSummary->sum('t');
						$tu = $attSummary->sum('u');
						$ab = $attSummary->sum('a');
						$otu = $tt - $tu;
						$tperot = round(($ab/($otu == 0?1:$otu))*100,2);
						$mmr = round($tp/($presentOp == 0?1:$presentOp),2);

					@endphp
					<th>Total</th>
					<th class="center">{{$tt}}</th>
					<th class="center">{{$otu}}</th>
					<th class="center">{{$attSummary->sum('p')}}</th>
					<th class="center">{{$ab}}</th>
					<th class="center">{{$attSummary->sum('h')}}</th>
					<th class="center">{{$attSummary->sum('l')}}</th>
					<th class="center @if($tperot > 80)bg-danger @elseif($tperot > 50)bg-warning @elseif($tperot > 0)bg-shadow @endif ">
							{{$tperot}}
					</th>
				</tr>
			</thead>
		</table>

		{{-- get mmr report, absent % --}}
		<div style="display:flex;justify-content: flex-end;">
			<div class="info-box">
				<span>Absent</span>
				<span class="number">
					{{$tperot}} %
				</span>
			</div>
			<div class="info-box">
				<span>MMR 
					<div class="popover-block-container" style="display: inline-block;">
					  <button tabindex="0" type="button" class="popover-icon" data-popover-content="#unique-id" data-toggle="popover"  data-placement="right" title="click to see MMR calculation rule">
					    <i class="fa fa-info-circle text-primary"></i>
					  </button>
					  <div id="unique-id" style="display:none;">
					    <div class="popover-body">
					      <div class="flex">
					      	<div>
					      		<br>
					      		MMR = &nbsp; &nbsp;
					      	</div>
					      	<div class="text-center">
						      	<span class="mb-1">Present NON OT + Present OT </span>
						      	<hr class="m-0" >
						      	Sewing Operator + Finishing Operator
					      	</div>
					      </div>
					    </div>
					  </div>
					</div>
				</span>
				<span class="number">
					{{$mmr}}
				</span>
			</div>

		</div>
		
	
	</div>
</div>
<p><small style="color: #000;margin-top:5px;">*Attendance report generated till {{date('d F, Y h:i A')}} </small></p>

	
@foreach($attDetails as $key => $ot)
	<h3 class="tag-title"><strong>@if($key == 1) OT @else NonOT @endif Holder </strong></h3>
	<div class="ot-content">
		<table class="table table-bordered" style="border:none;">
				
			@foreach($ot as $key1 => $area)
				
				<tr style="border:none;">
					<td style="border:none" colspan="9"><i class="fa fa-building"></i> <strong> {{$key1}}</strong></td>
				</tr>					<tr>
					<th>Section</th>
					<th>Subsection</th>
					<th class="center">Total Enrolled</th>
					<th class="center">Current Enrolled</th>
					<th class="center">Present</th>
					<th class="center">Absent</th>
					<th class="center">Holiday</th>
					<th class="center">Leave</th>
					<th class="center">Absent (%)</th>
				</tr>
				@foreach($area as $key2 => $department)
					<tr >
						<td colspan="9" class="dept-title cursor-pointer text-primary" data-toggle="tooltip" data-placement="top" title="Preview {{$libDepartment[$key2]['hr_department_name']??''}} department's employees" onclick="fetchEmployeeList('department',{{$key2}},{{$key}})"> {{$libDepartment[$key2]['hr_department_name']??''}}</td>
					</tr>
					@foreach($department as $key3 => $section)
						<tr >
							<td rowspan="@if(count($section) > 1){{count($section) + 2}}@else {{count($section) + 1}} @endif " class="cursor-pointer text-primary" data-toggle="tooltip" data-placement="top" title="Preview {{$libSection[$key3]['hr_section_name']??''}} section's employees" onclick="fetchEmployeeList('section',{{$key3}},{{$key}})"> 
								{{$libSection[$key3]['hr_section_name']??''}}
							</td>
						</tr>

						@foreach($section as $key4 => $subsection)
						<tr class="subsection-row">
							@php 
								$ct = $subsection->t - $subsection->u;
								$per = round(($subsection->a/($ct == 0?1:$ct))*100,2);
							@endphp
							<td class="cursor-pointer text-primary" data-toggle="tooltip" data-placement="top" title="Preview {{$libSubSection[$key4]['hr_subsec_name']??''}} sub-section's employees" onclick="fetchEmployeeList('subSection',{{$key4}},{{$key}})">{{$libSubSection[$key4]['hr_subsec_name']??''}}</td>
							<td class="center cursor-pointer text-primary" data-toggle="tooltip" data-placement="top" title="Preview {{$libSubSection[$key4]['hr_subsec_name']??''}} sub-section's employees" onclick="fetchEmployeeList('subSection',{{$key4}},{{$key}})">{{$subsection->t}}</td>
							<td class="center">{{$ct}}</td>
							<td class="center cursor-pointer text-primary" data-toggle="tooltip" data-placement="top" title="Preview {{$libSubSection[$key4]['hr_subsec_name']??''}} sub-section's present employees" onclick="fetchEmployeeList('subSection',{{$key4}},{{$key}}, 'P')">{{$subsection->p}}</td>
							<td class="center cursor-pointer text-primary" data-toggle="tooltip" data-placement="top" title="Preview {{$libSubSection[$key4]['hr_subsec_name']??''}} sub-section's absent employees" onclick="fetchEmployeeList('subSection',{{$key4}},{{$key}}, 'A')">{{$subsection->a}}</td>
							<td class="center cursor-pointer text-primary" data-toggle="tooltip" data-placement="top" title="Preview {{$libSubSection[$key4]['hr_subsec_name']??''}} sub-section's holiday employees" onclick="fetchEmployeeList('subSection',{{$key4}},{{$key}},'H')">{{$subsection->h}}</td>
							<td class="center cursor-pointer text-primary" data-toggle="tooltip" data-placement="top" title="Preview {{$libSubSection[$key4]['hr_subsec_name']??''}} sub-section's on leave employees" onclick="fetchEmployeeList('subSection',{{$key4}},{{$key}},'L')">{{$subsection->l}}</td>

							<td class="center @if($per > 80)bg-danger @elseif($per > 50)bg-warning @elseif($per > 0)bg-shadow @endif ">
								{{$per}}
							</td>
						</tr>
						@endforeach
						@if(count($section) > 1)
							<tr>
								@php
									$st = $section->sum('t');
									$su = $section->sum('u');
									$sab = $section->sum('a');
									$sotu = $st - $su;
									$stperot = round(($sab/($sotu == 0?1:$sotu))*100,2);

								@endphp
								<th >Total</th>
								<th class="center cursor-pointer text-primary" data-toggle="tooltip" data-placement="top" title="Preview {{$libSection[$key3]['hr_section_name']??''}} section's employees" onclick="fetchEmployeeList('section',{{$key3}},{{$key}})">{{$st}}</th>
								<th class="center">{{$sotu}}</th>
								<th class="center cursor-pointer text-primary" data-toggle="tooltip" data-placement="top" title="Preview {{$libSection[$key3]['hr_section_name']??''}} section's present employees" onclick="fetchEmployeeList('section',{{$key3}},{{$key}}, 'P')">{{$section->sum('p')}}</th>
								<th class="center cursor-pointer text-primary" data-toggle="tooltip" data-placement="top" title="Preview {{$libSection[$key3]['hr_section_name']??''}} section's absent employees" onclick="fetchEmployeeList('section',{{$key3}},{{$key}}, 'A')">{{$sab}}</th>
								<th class="center cursor-pointer text-primary" data-toggle="tooltip" data-placement="top" title="Preview {{$libSection[$key3]['hr_section_name']??''}} section's holiday employees" onclick="fetchEmployeeList('section',{{$key3}},{{$key}},'H')">{{$section->sum('h')}}</th>
								<th class="center cursor-pointer text-primary" data-toggle="tooltip" data-placement="top" title="Preview {{$libSection[$key3]['hr_section_name']??''}} section's on leave employees" onclick="fetchEmployeeList('section',{{$key3}},{{$key}},'L')">{{$section->sum('l')}}</th>
								<th class="center @if($stperot > 80)bg-danger @elseif($stperot > 50)bg-warning @elseif($stperot > 0)bg-shadow @endif ">
										{{$stperot}}
								</th>
							</tr>
						@endif
					@endforeach
				@endforeach
			@endforeach
		</table>
	</div>
	<div style="page-break-after: always;"></div>
@endforeach
</div>
<script type="text/javascript">
	$("[data-toggle=popover]").popover({
	        html : true,
	        trigger: 'focus',
	        content: function() {
	            var content = $(this).attr("data-popover-content");
	            return $(content).children(".popover-body").html();
	        }
	    });
</script>