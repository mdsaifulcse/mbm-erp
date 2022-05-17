<ul class="list-group">
  @foreach($value as $d)
   <li class="list-group-item">
    {{ $d->in_date }} - 
    @if($d->remarks != 'DSI' && $d->in_time != null && $d->in_time != '')
    {{ date('H:i:s', strtotime($d->in_time)) }} - 
    @else
    -
    @endif
    @if($d->out_time != null && $d->out_time != '')
    {{ date('H:i:s', strtotime($d->out_time)) }}
    @else
    -
    @endif
    -
    {{ $d->amount }}
  </li>
  @endforeach
 </ul>