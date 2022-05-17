

<table class="table table-bordered" style="margin-bottom:0px;">
  @foreach($data->product_size->chunk(8) as $productChunk)
  <tr>
    @foreach($productChunk as $key => $size)
    <td  style="border-bottom: 1px solid lightgray;">
      {{$size}}
      <input class="size" size="2" type="text" name="size_val[{{$key}}][]"  >
    </td>
    @endforeach
  </tr>
  @endforeach
</table>
