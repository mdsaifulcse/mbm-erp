<table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
	<thead>
		<tr>
			<th width="5%">{{__('SL No.')}}</th>
			<th>{{__('Date')}}</th>
			<th>{{__('Reference No')}}</th>
			<th>{{__('Requisition By')}}</th>
			<th>{{__('Requisition Qty')}}</th>
			<th class="text-center">{{__('Option')}}</th>
		</tr>
	</thead>
	<tbody>
		@if(count($requistion_data)>0)
		@foreach($requistion_data as $key=> $values)
		<tr id="removeRow{{$values->id}}">
			<td>{{ ($requistion_data->currentpage()-1) * $requistion_data->perpage() + $key + 1 }}</td>
			<td>{{date('d-m-Y', strtotime($values->requisition_date))}}</td>
			<td><a href="javascript:void(0)" onclick="openModal({{$values->id}})"  class="btn btn-link">{{$values->reference_no}}</a></td>
			<td>{{ $values->relUsersList->name }}</td>
			
			<td> {{$values->items->sum('qty')}}</td>
			<td class="text-center action">
				<div class="btn-group">
					<button class="btn dropdown-toggle" data-toggle="dropdown">
						<span id="statusName{{$values->id}}">
							@if($values->status==0)
							{{ __('Pending')}}
							@elseif($values->status==1)
							{{ __('Acknowledge')}}
							@else
							{{ __('Halt')}}
							@endif
						</span>
					</button>
					<ul class="dropdown-menu">
						@if($values->is_send_to_rfp=='no')
							@if($values->status !=0)
								@can('pending')
								<li><a href="javascript:void(0)" title="Click Here To Pending" class="requisitionApprovedBtn" data-id="{{$values->id}}" data-status="0">{{ __('Pending')}}</a>
								</li>
								@endcan
							@endif
							@if($values->status !=1)
								@can('requisition-acknowledge')
								<li><a href="javascript:void(0)" title="Click Here To Acknowledge" class="requisitionApprovedBtn" data-id="{{$values->id}}" data-status="1">{{ __('Acknowledge')}}</a>
								</li>
								@endcan
							@endif
							@can('halt')
							<li><a href="javascript:void(0)" title="Click Here To Halt" class="requisitionApprovedBtn" data-id="{{$values->id}}" data-status="2">{{ __('Halt')}}</a>
							</li>
							@endcan
						@endif

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
<div class="p-3">
	@if(count($requistion_data)>0)
	<ul class="searchPagination">
		{{$requistion_data->links()}}
	</ul>
	@endif
</div>