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
         
         <div class="body">
           
            <div class="table-responsive">
               <table class="table table-bordered table-striped table-hover dataTable js-exportable ">
                  <thead>
                     <tr>
                        <th>SL</th>
                        <th>User Type</th>
                        
                        <th width="100">Set Permission</th>
                     </tr>
                  </thead>
                  
                  <tbody>
                     @if(sizeof($usertypelist) > 0)   
                     @php
                     $serial =1;
                     @endphp
                     @foreach($usertypelist as $u) 
                     <tr>
                        <th>{{$serial++ }}</th>
                        <th>{{$u->user_type }}</th>
                        <th>
                        	<a class="btn bg-green btn-block btn-sm waves-effect" href="/menu-permission/{{$u->user_type_id}}">Set Permission</a>
                    	</th>
                     </tr>
                    
                     @endforeach
                     @else
                     <tr>
                        <th colspan="8">No record found.</th>
                     </tr>
                     @endif     
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
