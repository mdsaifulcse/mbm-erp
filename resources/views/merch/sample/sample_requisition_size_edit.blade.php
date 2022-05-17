

<table class="table table-bordered" style="margin-bottom:0px;">
  @foreach($allsz->chunk(8) as $productChunk)
  <tr>
    @foreach($productChunk as $key => $size)
    {{-- @foreach($sz as  $vsz) --}}
    <td  style="border-bottom: 1px solid lightgray;">
      {{$size}}
      <input class="size" size="2" type="text" value=" {{$sz[$key]??''}}" name="size_val[{{$key}}]"  >
    </td>
    {{-- @endforeach --}}
    @endforeach
  </tr>
  @endforeach
</table>
