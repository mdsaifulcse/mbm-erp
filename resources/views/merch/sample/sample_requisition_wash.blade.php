<table class="table table-bordered" style="margin-bottom:0px;">
  
  <tr>
    @foreach($data->wash_names as $key => $washName)
    <td  style="border-bottom: 1px solid lightgray;">
      {{$washName}}
      <input class="washType" type="hidden" name="wash[]" value="{{$key}}">
    </td>
    @endforeach
  </tr>
  
</table>

