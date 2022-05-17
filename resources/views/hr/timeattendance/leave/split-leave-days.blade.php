@php $sel = 0; @endphp

<div class="p-3 mb-3" style="border:1px solid #d1d1d1;border-radius: 5px;">
	<label for="" class="m-0 fwb"> <input type='checkbox' id="day-group" class=" group-checkbox bg-primary" checked /> Check All Days</label>

	@foreach($leaveDate as $k => $dt)
		<div class="form-group custom-control custom-checkbox custom-checkbox-color-check mb-1">
	       <input type="checkbox" name="leave_days[]" class="custom-control-input bg-primary leave-date-item" id="dt-{{$k}}"  value="{{$dt}}" @if(collect($holidays)->contains($dt) || collect($leave)->contains($dt)) disabled="disabled" @elseif(collect($present)->contains($dt)) @else checked @endif >
	       <label class="custom-control-label" for="dt-{{$k}}">
	       		<span style="width: 100px;display: inline-block;"> 
	       			{{$dt}}  {{date('D', strtotime($dt))}} 
	       		</span>

	       		@if(collect($present)->contains($dt)) 
	       			- <span class="text-success text-italic"> Present</span>
	       		@elseif(collect($holidays)->contains($dt))
	       			- <span class="text-danger text-italic"> Holiday</span>
	       		@elseif(collect($leave)->contains($dt))
	       			- <span class="text-warning text-italic"> Leave</span>
	       		@else
	       			@php $sel++; @endphp
	       		@endif
	       	</label>
	    </div>

	@endforeach

	<p class="text-primary">You have selected <b class="text-danger"  id="selLeaveDays">{{$sel}}</b class="text-primary"> Day(s) for Leave.</p>

</div>