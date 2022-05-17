<style type="text/css">
    .time-format span {
        font-family: monospace;
        font-size: 17px;
        margin-right: 0;
        border-radius: 3px;
        color: #fff;
        background: #454545;
        text-align: center;
        padding: 0;
        line-height: 1.3;
        margin: 0;
        display: inline-block;
        width: 85px;
    }
    b.shift-label {
        width: 90px;
        display: inline-block;
    }
    .time-format i{
        font-size: 20px;
        display: inline-block;
        text-align: center;
    }
    .s-second-label{
        font-size: 13px;
        display: inline-block;
        width: 100px;
    }
</style>
<div class=" mb-3">
    <div class="time-format">
        <span>{{$shift->current_shift_time->hr_shift_start_time ?? ''}} </span>
        <i class="las la-arrow-right"></i>
        <span>{{$shift->current_shift_time->hr_shift_out_time ?? ''}}</span>
    </div>
    @if($date) <p class="text-danger">Shift time for {{$date}} </p> @endif
</div>
<p><b class="shift-label">Unit </b>: {{$shift->unit->hr_unit_name ?? ''}}</p>

<p>
    <b class="shift-label">Shift Start</b>
    <span class="s-second-label">: {{$shift->current_shift_time->hr_shift_start_time ?? ''}} </span>
    @if($shift->current_shift_time->has_default_value)
    <span class="text-small text-danger">Default : {{$shift->hr_shift_start_time??''}}</span>
    @endif
</p>

<p><b class="shift-label">Shift End</b>
    <span class="s-second-label">: {{$shift->current_shift_time->hr_shift_end_time ?? ''}}</span>
    @if($shift->current_shift_time->has_default_value)
    <span class="text-small text-danger">Default : {{$shift->hr_shift_end_time??''}}</span>
    @endif
</p>
<p>
    <b class="shift-label">Break</b>
    <span class="s-second-label">: {{$shift->current_shift_time->hr_shift_break_time ?? ''}} Minute(s)</span>
    @if($shift->current_shift_time->has_default_value)
    <span class="text-small text-danger">Default : {{$shift->hr_shift_break_time??''}} Minute(s)</span>
    @endif
</p>

<p>
    <b class="shift-label">Break Start</b>
    <span class="s-second-label">: {{$shift->current_shift_time->hr_break_start_time ?? ''}}</span>
    @if($shift->current_shift_time->has_default_value)
    <span class="text-small text-danger">Default : {{$shift->hr_default_break_start??''}}</span>
    @endif
</p>
<p><b class="shift-label">Shift Outtime</b>
    <span class="s-second-label">: {{$shift->current_shift_time->hr_shift_out_time ?? ''}}</span>
    @if($shift->current_shift_time->has_default_value)
    <span class="text-small text-danger">Default : {{$shift->hr_shift_out_time??''}}</span>
    @endif
</p>
@if($shift->current_shift_time->end_date)
<p class="text-success">
    This shift time will run till {{$shift->current_shift_time->end_date}}
</p>
@endif


        
        
    