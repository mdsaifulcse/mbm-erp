<label for="" class="m-0 fwb">Shift <input type='checkbox' id="shift" class="shift-group group-checkbox bg-primary" checked onclick="checkAllGroup(this)" /></label>
  <hr class="mt-2">
  <div id="shift-checkbox-area" class="row">
    @php $incr = 0; @endphp
    @foreach($shifts as $shift)
        <div class="col-sm-6 pr-0 ">
          @php $incr++ @endphp
          <div class="custom-control custom-checkbox custom-checkbox-color-check" title="{{ $shift }}">
            <input type="checkbox" name="shift[]" class="custom-control-input bg-primary shift" value="{{$shift}}" id="shift-checkbox-{{$incr}}" checked>
            <label class="custom-control-label" for="shift-checkbox-{{$incr}}"> {{ $shift }}</label>
          </div>
        </div>
    @endforeach
  </div>