@php
	$libArea = area_by_id();
	$libDepartment = department_by_id();
	$libSection = section_by_id();
	$libSubSection = subSection_by_id();

@endphp
@foreach($attDetails as $key => $ot)
	<h3 class="tag-title"><strong>@if($key == 1) OT @else NonOT @endif Holder </strong></h3>
	<div class="ot-content">
		<table>
			<tr>
				<th>Department</th>
				<th>Section</th>
				<th>Subsection</th>
				<th>Total Enrolled</th>
				<th>Current Enrolled</th>
				<th>Present</th>
				<th>Absent</th>
				<th>Holiday</th>
				<th>Leave</th>
				<th>Absent rate</th>
			</tr>
			@foreach($ot as $key1 => $area)			
				@foreach($area as $key2 => $department)
					@foreach($department as $key3 => $section)
						@foreach($section as $key4 => $subsection)
						<tr>
							<td>{{$libDepartment[$key2]['hr_department_name']??''}}</td>
							<td>{{$libSection[$key3]['hr_section_name']??''}}</td>
							@php 
								$ct = $subsection->t - $subsection->u;
								$per = round(($subsection->a/($ct == 0?1:$ct))*100,2);
							@endphp
							<td>{{$libSubSection[$key4]['hr_subsec_name']??''}}</td>
							<td>{{$subsection->t}}</td>
							<td>{{$ct}}</td>
							<td>{{$subsection->p}}</td>
							<td>{{$subsection->a}}</td>
							<td>{{$subsection->h}}</td>
							<td>{{$subsection->l}}</td>
							<td> {{$per}}</td>
						</tr>
						@endforeach
					@endforeach
				@endforeach
			@endforeach
		</table>
	</div>
@endforeach