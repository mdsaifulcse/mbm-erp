<table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" >
	<thead>
		<tr>
			<th width="5%">{{__('SL No.')}}</th>
			<th>{{__('Date')}}</th>
			<th>{{__('Department')}}</th>
			<th>{{__('Reference No')}}</th>
			
			<th>{{__('Requisition By')}}</th>
			<th>{{__('Qty')}}</th>
			<th class="text-center">{{__('Option')}}</th>
		</tr>
	</thead>
	<tbody id="viewResult">
		@if(count($requistion_data)>0)
		@foreach($requistion_data as $key=> $values)
		<tr id="row{{$values->id}}">
			<td>{{$key+1}}</td>
			<td>{{date('d-m-Y', strtotime($values->requisition_date))}}</td>
			<td>{{isset($values->relUsersList->employee)?$values->relUsersList->employee->department->hr_department_name:''}}</td>
			<td><a href="javascript:void(0)" onclick="openModal({{$values->id}})"  class="btn btn-link">{{$values->reference_no}}</a></td>
			
			<td> {{$values->relUsersList->name}}</td>
			<td> {{$values->items->sum('qty')}}</td>
			<td class="text-center action">
				<div class="btn-group">
					<button class="btn dropdown-toggle" data-toggle="dropdown">
						<span id="statusName{{$values->id}}">
							Action
						</span>
					</button>
					<ul class="dropdown-menu">
						@can('confirm-delivery')
						<li><a href="{{route('pms.store-manage.store-requistion.delivery',$values->id)}}" title="Click Here To Confirm Delivery" >{{ __('Confirm Delivery')}}</a>
						</li>
						@endcan
						@can('send-to-rfp')
						<li>
							<a class="sendToPurchaseDepartment" data-src="{{route('pms.store-manage.send.to.purchase.department')}}" data-id="{{$values->id}}"  title="Send To RFP">{{ __('Send To RFP')}}
							</a>
						</li>
						@endcan
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
	<ul  class="searchPagination">
		{{$requistion_data->links()}}
	</ul>

	@endif
</div>