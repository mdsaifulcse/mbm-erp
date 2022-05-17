@php 
	$bill_type = bill_type_by_id(); 
@endphp
<style type="text/css">
	.label-head-c {
    background: var(--gray);
    padding: 3px 5px;
    font-weight: bold;
    color: #fff;
}
</style>
<div class="row">
	<div class="col-sm-5">
		{{-- include intro --}}
		@include('hr.setup.shift.shift_intro')

   



	</div>
	<div class="col-sm-4">

      <h6 class="label-head-c mb-3">Bill </h6>
      <ul class="speciality-list m-0 p-0">
      	@if(count($shift->current_bills) > 0)
      	@foreach($shift->current_bills as $bill)
         <li class="d-flex mb-4 align-items-center">
            <div class="user-img img-fluid"><a href="#" class="iq-bg-primary"><i class="las la-money-bill-wave-alt"></i></a></div>
            <div class="media-support-info ml-3">
               <h6 class="font-weight-bold">{{$bill_type[$bill]}}</h6>
               <p class="mb-0">currently running</p>
            </div>
         </li>
         @endforeach
         @else
         <li>No bill assigned</li>
         @endif
      </ul>
      
		<h6 class="label-head-c mb-3">Break Properties</h6>
		@if(count($shift->current_break_rules ) > 0)
	       <b>Custom Rules</b>
	       @foreach($shift->current_break_rules as $k => $rules)
	       <div class="break-rule p-2 mb-2" style="background: rgb(254 237 233);">
	           <b>Break:</b> {{$rules->break_time}} Minute(s)
	           @if($rules->designations)
	              <p><b> for</b> 
	               @foreach(explode(",",$rules->designations) as $k => $des)
	                   <span class="badge badge-primary">{{$designation[$des]??''}}</span>
	               @endforeach
	               <p>
	           @endif
	           @if($rules->days)
	              <p><b> on </b>
	               @foreach(explode(",",$rules->days) as $k => $day)
	                   <span class="badge badge-primary">{{$day}}</span>
	               @endforeach
	               <p>
	           @endif
	       </div>
	       @endforeach
	   @endif
		
	</div>
	<div class="col-sm-3">
		<h6 class="label-head-c mb-3">History </h6>
		
		@include('hr.setup.shift.shift-history')
		
	</div>
	
</div>