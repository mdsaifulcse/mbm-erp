<div class="top_summery_section">
	@if($input['report_format'] == 0 || ($input['report_format'] == 1 && $format != null))
	<div class="page-header">
        <h2 style="margin:4px 10px; font-weight: bold; text-align: center;"> {{$reportTitle}} </h2>
        <h4>
        	Unit : </b> 
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
        </h4>
        <table class="table no-border f-16">
        	<tr>
        		<td>
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
        		<td>

            	</div>
        			Date <b>: {{ $input['date']}} </b> <br>
        			@if($input['otnonot'] != null)
            			<b> OT </b> 
            			<b>: @if($input['otnonot'] == 0) No @else Yes @endif </b> <br>
            		@endif
        			<b>Total Employee</b>
        			<b>: {{ $totalEmployees }}</b><br>
        			<b>Total OT Hour</b>
        			<b>: {{ $totalValue }}</b>
            		
        		</td>
        		<td>
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
		
		<h2>OT Summary Report </h2>
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
		<h4>OT Date: {{ $input['date']}}</h4>
		<h4>Total OT Employee: <b>{{ $totalEmployees }}</b></h4>
		<h4>Total OT Hour: <b>{{ $totalValue }}</b></h4>
        		
    </div>
    @endif
</div>