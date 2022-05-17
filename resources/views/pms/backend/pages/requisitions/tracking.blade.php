
<div class="card card-timeline px-2 border-none">
	@if(count($requisition->requisitionTracking)>0)
	@php 
	$numItems = count($requisition->requisitionTracking);
	$note=''; 
	$status=''; 
	$i = 0;
	
	$tracking=['la la-folder-open'=>'Draft','la la-clock-o'=>'Pending','las la-check'=>'Approved','las la-spinner'=>'Processing','las la-truck'=>'Delivered'];
	$tracking_array=[];
	@endphp
	<ul class="bs4-order-tracking">
		@foreach($requisition->requisitionTracking()->groupBy('status')->orderBy('id','asc')->get() as $key=> $values)
		@php 
		if($values=='halt'){
			$note=$values->note;
		}
		if(++$i === $numItems) {
			$status=$values->status;
		}
		array_push($tracking_array, ucfirst($values->status))

		@endphp
		@endforeach

		@foreach($tracking as $key=> $tr)
		<li class="step {{(in_array($tr,$tracking_array))?'active':''}}">
			<div><i class="{{$key}}"></i></div> {{$tr}}
		</li>
		@endforeach
	</ul>
	<h5 class="text-center"><b style="color:#0db5c8">{{ucwords($status)}}</b>. The requistion has been {{$status}}!</h5>
	@if(!empty($note))
	<h5 class="text-center"><b>Note: </b>{{$note}}</h5>
	@endif
	
	@endif
</div>