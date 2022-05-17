@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="block-header">
            <div class="row">
                <div class="col-lg-12">
                    <h2>
                        {{ $pageTitle}}
                        <small>
                         <a href="{{ URL('/dashboard') }}"> Dashboard </a> / {{$pageTitle}}
                        </small>
                    </h2>
                </div>

            </div>

        </div>

        @if(Session::has('success'))
            <div class="alert alert-success">
            {{ Session::get('success') }}
            </div>
        @endif
        @if(Session::has('error'))
            <div class="alert alert-danger">
            {{ Session::get('error') }}
            </div>
        @endif

        <div class="row clearfix">
      <!-- #END# Exportable Table -->
      <div class="card">
      		<div class="header">
      			<h2>Set Menu Permission for {{$usertype->user_type}}</h2>
      		</div>
         
         <div class="body">
           
            <div class="table-responsive">
               <table class="table table-bordered table-striped table-hover dataTable js-exportable ">
                  <thead>
                     <tr>
                        <th>Menu Name</th>
                        <th>Menu URL</th>
                        <th>Permission</th>
                        <!-- <th>View</th>
                        <th>Add</th>
                        <th>Update</th>
                        <th>Delete</th> -->
                     </tr>
                  </thead>
                  
                  <tbody>
                  	<form action="{{ URL('/menu-permission-save/'.$usertype->user_type_id)}}" method="POST">
                  		{{ csrf_field() }}
                  	@foreach($parentmenus as $p)
                  	@php
                  	$childmenus = DB::table('tbl_menu')->where('parentid', $p->menuid)->get();

                  	@endphp
                	<tr>
                		@if(sizeof($childmenus) > 0)
                  		<td><b>&#187; {{$p->menu_name}}</b><small> (Hints: {{$p->menu_hints}})</small></td>
                        <td>{{$p->menu_url}}</td>
                  		<td> 
          							<input type="checkbox" id="permission[{{$p->menuid}}]" class="radio-col-red" name="menuids[]" value="{{$p->menuid}}" @if(in_array($p->menuid, $existingmenu)) checked @endif>
          							<label for="permission[{{$p->menuid}}]"> &nbsp; </label>
          						</td>
                  		@else
                  		<td><b>&#187; {{$p->menu_name}} ({{$p->menu_hints}})</b></td>
                       <td>{{$p->menu_url}}</td>
                  		<td> 
          							<input type="checkbox" id="permission[{{$p->menuid}}]" class="radio-col-red" name="menuids[]" value="{{$p->menuid}}"  @if(in_array($p->menuid, $existingmenu)) checked @endif>
          							<label for="permission[{{$p->menuid}}]"> &nbsp; </label>
          						</td>
						<!-- <td> 
							<input name="" type="checkbox" id="view[{{$p->menuid}}]" class="radio-col-red" value="YES">
							<label for="view[{{$p->menuid}}]"> &nbsp; </label>
						</td>
                  		<td> 
							<input name="" type="checkbox" id="add[{{$p->menuid}}]" class="radio-col-red" value="YES">
							<label for="add[{{$p->menuid}}]"> &nbsp; </label>
						</td>
						<td> 
							<input name="" type="checkbox" id="update[{{$p->menuid}}]" class="radio-col-red" value="YES">
							<label for="update[{{$p->menuid}}]"> &nbsp; </label>
						</td>
						<td> 
							<input name="" type="checkbox" id="delete[{{$p->menuid}}]" class="radio-col-red" value="YES">
							<label for="delete[{{$p->menuid}}]"> &nbsp; </label>
						</td> -->
                  		@endif
                  	</tr>
                  	@if(sizeof($childmenus) > 0)
                  	@foreach($childmenus as $c)
                  	<tr>
                  		<td>&nbsp; &nbsp; &nbsp; &nbsp; {{$c->menu_name}} ({{$c->menu_hints}})</td>
                        <td>{{$c->menu_url}}</td>
                  		<td> 
							<input type="checkbox" id="permission[{{$c->menuid}}]" class="radio-col-red"  name="menuids[]" value="{{$c->menuid}}"  @if(in_array($c->menuid, $existingmenu)) checked @endif>
							<label for="permission[{{$c->menuid}}]"> &nbsp; </label>
						</td>
                  		<!-- <td> 
							<input name="" type="checkbox" id="view[{{$c->menuid}}]" class="radio-col-red" value="YES">
							<label for="view[{{$c->menuid}}]"> &nbsp; </label>
						</td>
                  		<td> 
							<input name="" type="checkbox" id="add[{{$c->menuid}}]" class="radio-col-red" value="YES">
							<label for="add[{{$c->menuid}}]"> &nbsp; </label>
						</td>
						<td> 
							<input name="" type="checkbox" id="update[{{$c->menuid}}]" class="radio-col-red" value="YES">
							<label for="update[{{$c->menuid}}]"> &nbsp; </label>
						</td>
						<td> 
							<input name="" type="checkbox" id="delete[{{$c->menuid}}]" class="radio-col-red" value="YES">
							<label for="delete[{{$c->menuid}}]"> &nbsp; </label>
						</td> -->	
                  	</tr>

                  	@endforeach
                  	@endif	
                    @endforeach 
                    <tr>
                    	<td colspan="2" style="text-align: center;">
                    		<button type="submit" class="btn btn-success"> Save Permission</button>
                    	</td>
                    </tr>
                    </form>    
                  </tbody>
               </table>
            </div>
         </div>
      </div>
      <!-- #END# Exportable Table -->
   </div>


            
    </div>
    <div id="editMenuModalDiv"></div>

</section>
@endsection
