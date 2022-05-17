
@if($input['report_format'] == 0)
<table >
    <tr>
        <th>Sl</th>
        <th>Associate ID</th>
        <th>Name</th>
        <th>Oracle ID</th>
        <th>Unit</th>
        <th>Location</th>
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
	@foreach($uniqueGroupEmp as $group => $employees)

	    @php
	     $i = 0; $month = date('Y-m',strtotime($input['date'])); $totalOt=0;
	    @endphp
	    @if(count($employees) > 0)
	        @foreach($employees as $employee)
	        	@php
	        		$designationName = $designation[$employee->as_designation_id]['hr_designation_name']??'';
	        		
	                $otHour = $employee->ot_hour;

	        	@endphp
	        	<tr>
	        		<td>{{ ++$i }}</td>
	            	<td>{{ $employee->associate_id }}</td>
	            	<td>{{ $employee->as_name }}</td>
	            	<td>{{ $employee->as_oracle_code }}</td>
	            	<td>{{ $unit[$employee->as_unit_id]['hr_unit_short_name']??'' }}</td>
	            	<td>{{ $location[$employee->as_location]['hr_location_name']??'' }}</td>
	            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
	            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
	            	<td>{{ $section[$employee->as_section_id]['hr_section_name']??'' }}</td>
	            	<td>{{ $subSection[$employee->as_subsection_id]['hr_subsec_name']??'' }}</td>
	            	<td>{{ $floor[$employee->as_floor_id]['hr_floor_name']??'' }}</td>
	            	<td>{{ $line[$employee->as_line_id]['hr_line_name']??'' }}</td>
	            	<td>
	            		{{$employee->in_time }}
	            	</td>
	            	<td>
	            		{{ $employee->out_time }}
	            	</td>
	            	<td>{{ $otHour }}</td>
	        	</tr>
	        @endforeach
	    	
	    @endif
	    

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
	}elseif($format == 'ot_hour'){
		$head = 'Hour';
	}else{
		$head = '';
	}
@endphp
<table>
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
			<th>Employee</th>
			<th>OT Hour</th>
		</tr>
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
		<tr>
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
				@endphp
					{{$department[$getDepar]['hr_department_name']??''}}
			</td>
			@endif
			@if($format == 'as_subsection_id')
			<td>
				@php
					$getSec = $subSection[$group]['hr_subsec_section_id']??'';
				@endphp
					 {{$section[$getSec]['hr_section_name']??''}}
			</td>
			@endif
			<td>
				
				{{ ($body == null)?'N/A':$body }}
			</td>
			<td>
				{{ $employee->total }}
			</td>
			<td>
				{{ round($employee->groupOt,2) }}
			</td>
		</tr>
		@endforeach
		@endif
	
</table>

@endif

			