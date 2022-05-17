<table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
	<thead>
		<tr>
			<th width="5%">{{__('SL No.')}}</th>
			<th>{{__('Date')}}</th>
			<th>{{__('Department')}}</th>
			<th>{{__('Reference No')}}</th>
			<th>{{__('Requisition By')}}</th>
			<th>{{__('Stock Qty')}}</th>
			<th>{{__('Req Qty')}}</th>
			<th class="text-center">{{__('Option')}}</th>
		</tr>
	</thead>
	<tbody>
		@if(count($requisition)>0)
		@foreach($requisition as $key=> $values)
		<?php 
		$stockQty=0;
		foreach($values->items as $item){
			$stockQty +=isset($item->product->relInventorySummary->qty)?$item->product->relInventorySummary->qty:0;
		}
	?>
	@if($stockQty >= $values->items->sum('qty'))
	<tr id="row{{$values->id}}">
		<td>{{($requisition->currentpage()-1) * $requisition->perpage() + $key + 1 }}</td>
		<td>{{date('d-m-Y', strtotime($values->requisition_date))}}</td>
		<td>{{isset($values->relUsersList->employee)?$values->relUsersList->employee->department->hr_department_name:''}}</td>
		<td><a href="javascript:void(0)" onclick="openModal({{$values->id}})"  class="btn btn-link">{{$values->reference_no}}</a></td>
		
		<td> {{$values->relUsersList->name}}</td>
		<td> {{$stockQty}}</td>
		<td> {{$values->items->sum('qty')}}</td>
		<td class="text-center action">
			<a href="{{route('pms.store-manage.requisition.items.list',$values->id)}}">{{ __('Show Items')}}</a>
		</td>
	</tr>
	@endif
	@endforeach
	@endif
</tbody>

</table>
<div class="py-2 col-md-12">
	@if(count($requisition)>0)
	<ul class="searchPagination">
		{{$requisition->links()}}
	</ul>

	@endif
</div>
