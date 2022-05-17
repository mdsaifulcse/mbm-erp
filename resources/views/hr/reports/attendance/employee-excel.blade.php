@php
	$unit = unit_by_id();
	$location = location_by_id();
	$floor = floor_by_id();
	$line = line_by_id();
	$area = area_by_id();
	$department = department_by_id();
	$designation = designation_by_id();
	$section = section_by_id();
	$subSection = subSection_by_id();
@endphp

<table class="table table-bordered table-hover table-head table-responsive" border="1" style="    display: table;">
	<thead>
        <tr>
            <th >Sl</th>
            <th >Associate ID</th>
            <th >Name</th>
            <th> Phone</th>
            <th >Oracle ID</th>
            <th >Unit</th>
            <th >Location</th>
            <th >OT</th>
            <th >Designation</th>
            <th >Department</th>
            <th >Section</th>
            <th >Sub Section</th>
            <th >Floor</th>
            <th >Line</th>
            <th >Shift</th>
            <th >Status</th>
        </tr>
    </thead>
    <tbody>
    	@php $i = 0; @endphp
		@foreach($att as $key => $employee)
        	<tr>
        		<td>{{ ++$i }}</td>
            	<td>{{ $employee->associate_id }}</td>
            	<td>{{ $employee->as_name }}</td>
            	<td>{{ $employee->as_contact }}</td>
            	<td>{{ $employee->as_oracle_code }}</td>
            	<td>{{ $unit[$employee->as_unit_id]['hr_unit_short_name']??'' }}</td>
            	<td>{{ $location[$employee->as_location]['hr_location_name']??'' }}</td>
            	<td>
            		@if($employee->as_ot == 1) OT @else Non-OT @endif
            	</td>
            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
            	<td>{{ $section[$employee->as_section_id]['hr_section_name']??'' }}</td>
            	<td>{{ $subSection[$employee->as_subsection_id]['hr_subsec_name']??'' }}</td>
            	<td>{{ $floor[$employee->as_floor_id]['hr_floor_name']??'' }}</td>
            	<td>{{ $line[$employee->as_line_id]['hr_line_name']??'' }}</td>
            	<td>
            		{{$employee->as_shift_id}}
            	</td>
            	<td>
            		@if($employee->status == 'P') Present
            		@elseif($employee->status == 'H') Holiday
            		@elseif($employee->status == 'L') Leave
            		@elseif($employee->status == 'A') Absent
            		@endif

            	</td>
        	</tr>
		@endforeach
    </tbody>
</table>