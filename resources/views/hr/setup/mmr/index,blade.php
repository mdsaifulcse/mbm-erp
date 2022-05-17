@extends('hr.layout')
@section('title', ' MMR Settings')

@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li> 
                <li>
                    <a href="#"> Operation </a>
                </li>
                <li class="active"> MMR Settings </li>
                <li class="top-nav-btn">
                    
                    <a class="btn btn-primary btn-sm" href="{{ url('hr/operation/shift_assign') }}"><i class="fa fa-users"></i> MMR Report</a>
                </li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="panel">
                <div class="panel-body">

                    

                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/shift')  }}" enctype="multipart/form-data">
                        {{ csrf_field() }} 
                        <div class="row">
                            
                            <div class="col-sm-3 " style="border-left:1px solid #d1d1d1">
                                <p class="mb-3"><strong>
                                    <i class="fa fa-history text-primary" aria-hidden="true"></i>
                                    &nbsp; History
                                </strong></p>
                                <p>No history found!</p>
                                
                            </div>

                        </div>
                        
                    </form>
                    
                </div> 
            </div>
        </div><!-- /.page-content -->
    </div>
</div>

    
@push('js')

@endpush
@endsection

