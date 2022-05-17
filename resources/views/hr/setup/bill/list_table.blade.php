<table id="global-datatable" class="table table-striped table-bordered table-head" style="display: block;overflow-x: auto;width: 100%;" border="1">
  <thead>
      <tr>
          <th width="5%">SL.</th>
          <th width="30%">Unit</th>
          <th width="15%">Bill Type</th>
          <th width="15%">Eligible Parameter</th>
          <th width="8%">Amount</th>
          <th width="15%">OT Status</th>   
          <th width="25%">Start Date</th> 
          <th width="25%">End Date</th> 
      </tr>
  </thead>
  <tbody>
    @php $i=0; @endphp
    @if(count($billList) > 0)
      @foreach($billList as $bill)
        <tr>
          @php
            $billName = $billType[$bill->bill_type_id]['name']??'';
          @endphp
          <td>{{ ++$i }}</td>
          <td>{{ $unit[$bill->unit_id]['hr_unit_name'] }}</td>
          <td><a style="color:#0aa6b7; font-weight: 600" data-id="{{ $bill->id }}" data-head="{{ $billName }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Details" class="bill-type">{{ $billName }}</a></td>
          
          <td>
            @if($bill->pay_type == 1)
              All Present
            @elseif($bill->pay_type == 2)
              Working Hour
            @elseif($bill->pay_type == 3)
              OT Hour
            @elseif($bill->pay_type == 4)
              Out Punch
            @endif
            {{ ($bill->pay_type == 1)?'':'- '. $bill->duration }}
          </td>
          <td>{{ $bill->amount }}</td>
          <td>
            @if($bill->as_ot == 0)
            Non-OT
            @elseif($bill->as_ot == 1)
            OT
            @else
            Both
            @endif
          </td>
          <td>{{ $bill->start_date }}</td>
          <td>{{ $bill->end_date ==''?'Continue':$bill->end_date }}</td>
        </tr>
      @endforeach
    @else
      <tr>
        <td colspan="7" class="text-center"> No record found! </td>
      </tr>
    @endif
  </tbody>
</table>