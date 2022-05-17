@if(count($shift->histories) > 0)
    <ul class="iq-timeline">
        @foreach($shift->histories->take(5) as $history)
        <li>
           <div class="timeline-dots border-success"></div>
           <h6 class=""><strong>{{$history->hr_shift_start_time}}</strong> - <strong>{{$history->hr_shift_out_time}}</strong></h6>
           <p>Break: {{$history->hr_shift_break_time}} Minute(s)</p>
           <span class="text-muted">
                {{$history->start_date??'_______'}}
                to
                {{$history->end_date??'continue'}}
            </span>

        </li>
       @endforeach
  </ul>
@else
<p>No history found!</p>
@endif