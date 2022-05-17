
@for ($j=0; $j < $totalDay; $j++)
    @php $selectDay = $weekends[$j]; @endphp
    @if(in_array($selectDay, array_keys($dayDates)))
        @php $week = $dayDates[$selectDay]; @endphp
        <div class="col-sm-2 pl-0">
            <div class="card">
                <div class="card-header text-center min-h-62">
                    {{ $selectDay }} of <br>{{ date('F Y', strtotime($yearMonth)) }}
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($week as $value)
                        <li class="list-group-item text-center">
                            {{ $value }}
                            <br>
                            <div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                              <input type="checkbox" name="holiday_type[]" class="custom-control-input bg-primary" id="customCheck-{{ $value }}" value="{{ $value }}">
                              <label class="custom-control-label" for="customCheck-{{ $value }}"> Festival</label>
                            </div>
                            <input type="hidden" name="hr_yhp_dates_of_holidays[]" value="{{ $value }}" />
                            <input type="hidden" name="hr_yhp_comments[]" value="Weekend"/>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
@endfor