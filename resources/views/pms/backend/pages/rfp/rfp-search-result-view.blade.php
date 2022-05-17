<table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
	<thead>
		<tr>
			<th width="5%">{{__('SL No.')}}</th>
			<th>{{__('Date')}}</th>
			<th>{{__('Reference No')}}</th>
			<th>{{__('Request By')}}</th>
			<th>{{__('Qty')}}</th>
			<th class="text-center">{{__('Option')}}</th>
		</tr>
	</thead>
	<tbody>
		@if(count($requistion_data)>0)
		@foreach($requistion_data as $key=> $values)
		<tr id="row{{$values->id}}">
			<td>{{$key+1}}</td>
			<td>{{date('d-m-Y', strtotime($values->requisition_date))}}</td>
			<td><a href="javascript:void(0)" onclick="openModal({{$values->id}})"  class="btn btn-link">{{$values->reference_no}}</a></td>
			<td>{{$values->relUsersList->name}}</td>
			
			<td> {{$values->items->sum('qty')}}</td>
			<td class="text-center action">
				<div class="btn-group">
					<button class="btn dropdown-toggle" data-toggle="dropdown">
						<span id="statusName{{$values->id}}">
							Status
						</span>
					</button>
					<ul class="dropdown-menu">

						<li><a class="convertToRfp" data-src="{{route('pms.rfp.convert.to.rfp')}}" data-id="{{$values->id}}" title="Prepare to RFP" >{{ __('Prepare To RFP')}}</a>
						</li>

						<li>
							<a target="__blank" href="{{route('pms.rfp.send.to.purchase',$values->id)}}" title="Direct Purchase">{{ __('Direct Purchase')}}
							</a>
						</li>


					</ul>
				</div>

			</td>
		</tr>
		@endforeach
		@endif
	</tbody>

</table>
<div class="py-2 col-md-12">
	@if(count($requistion_data)>0)
	<ul class="searchPagination">
		{{$requistion_data->links()}}
	</ul>

	@endif
</div>