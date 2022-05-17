
@extends('hr.layout')
@section('title', 'Department')
@section('main-content')
    @push('css')
    @endpush
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="#"> Human Resource </a>
            </li> 
            <li>
                <a href="#"> Library </a>
            </li>
            <li class="active"> Department </li>
        </ul><!-- /.breadcrumb --> 
    </div>
    <div class="row">
       <div class="col-lg-2 pr-0">
           <!-- include library menu here  -->
           @include('hr.settings.library_menu')
       </div>
       <div class="col-lg-10 mail-box-detail">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h6>
                        Department : {{ $department->hr_department_name }}
                        <a class="btn btn-primary pull-right" href="#list">Department List</a>
                    </h6>
                </div> 
                <div class="panel-body">
                    
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/department_update')  }}" enctype="multipart/form-data">
                        {{ csrf_field() }} 
                        <input type="hidden" name="hr_department_id"  value="{{ $department->hr_department_id }}" >
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('hr_department_area_id', $areaList, $department->hr_department_area_id, ['placeholder' => 'Select Area Name', 'class' => 'form-control', 'id'=>'hr_department_area_id', 'required'=>'required']) }}
                                    <label  for="hr_department_area_id" > Area Name </label>
                                </div>

                                <div class="form-group has-required has-float-label">
                                    <input type="text" name="hr_department_name" id="hr_department_name" placeholder="Department Name" class="form-control" required="required" value="{{ $department->hr_department_name }}"/>
                                    <label  for="hr_department_name" > Department Name </label>
                                </div>

                                
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group has-required has-float-label">
                                    <input type="text" name="hr_department_name_bn" id="hr_department_name_bn" placeholder="ডিপার্টমেন্টের নাম " class="form-control" required="required" value="{{ $department->hr_department_name_bn }}"/>
                                    <label  for="hr_department_name_bn" >ডিপার্টমেন্ট (বাংলা) </label>
                                </div>

                                <div class="form-group has-required has-float-label">
                                    <input type="text" name="hr_department_code" placeholder="Department Code" class="form-control" required="required" value="{{ $department->hr_department_code }}"/>
                                    <label  for="hr_department_code"> Department Code </label>
                                </div>

                                
                                
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label  for="hr_department_min_range"> Department ID Range </label>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" id="hr_department_min_range" name="hr_department_min_range" required="required" placeholder="Example: 000001 " class="form-control" value="{{ $department->hr_department_min_range }}" />
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" id="hr_department_max_range" name="hr_department_max_range" required="required" placeholder="Example: 001000" class="form-control" value="{{ $department->hr_department_max_range }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group"> 
                                    <button class="btn pull-right btn-primary" type="submit">Update</button>
                                </div>
                                
                            </div>
                                 

                            

                                
                        </div>    
                            
                    </form> 
                </div>
            </div>
            <div id="list" class="panel panel-info">
                <div class="panel-body">
                    <ul class="nav nav-tabs" id="myTab-1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active" role="tab" aria-controls="active" aria-selected="false">Active</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="trash-tab" data-toggle="tab" href="#trash" role="tab" aria-controls="trash" aria-selected="false">Trash</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="active" role="tabpanel" aria-labelledby="active-tab">
                         
                            <div class="table-responsive">
                                <table id="global-datatable" class="table table-striped table-bordered" style="display: block;width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 30%;">Department Name</th>
                                            <th style="width: 20%;">ডিপার্টমেন্ট (বাংলা)</th>
                                            <th style="width: 20%;">Department Code</th>
                                            <th style="width: 30%;">Department ID Range</th>
                                            <th style="width: 30%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($departments as $department)
                                        <tr>
                                            <td>{{ $department->hr_department_name }}</td>
                                            <td>{{ $department->hr_department_name_bn }}</td>
                                            <td>{{ $department->hr_department_code }}</td>
                                            <td>{{ $department->hr_department_min_range }}-{{ $department->hr_department_max_range }}</td>
                                            <td>
                                            <div class="btn-group">
                                                <a type="button" href="{{ url('hr/setup/department_update/'.$department->hr_department_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                                <a href="{{ url('hr/setup/department/'.$department->hr_department_id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
                                            </div>
                                        </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="trash" role="tabpanel" aria-labelledby="trash-tab">
                            <div class="table-responsive">
                                <table id="global-trash" class="table table-striped table-bordered" style="display: block;width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 30%;">Department Name</th>
                                            <th style="width: 20%;">ডিপার্টমেন্ট (বাংলা)</th>
                                            <th style="width: 20%;">Department Code</th>
                                            <th style="width: 30%;">Department ID Range</th>
                                            <th style="width: 30%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($trashed as $department)
                                        <tr>
                                            <td>{{ $department->hr_department_name }}</td>
                                            <td>{{ $department->hr_department_name_bn }}</td>
                                            <td>{{ $department->hr_department_code }}</td>
                                            <td>{{ $department->hr_department_min_range }}-{{ $department->hr_department_max_range }}</td>
                                            <td>
                                            <div class="btn-group">
                                                <a type="button" href="{{ url('hr/setup/department_update/'.$department->hr_department_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                                <a href="{{ url('hr/setup/department/'.$department->hr_department_id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
                                            </div>
                                        </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

       </div>
    </div>
@push('js')
<script type="text/javascript">
$(document).ready(function(){ 

    //date validation------------------
    $('#hr_department_min_range').on('change',function(){
        $('#hr_department_max_range').val('');    
    });

    $('#hr_department_max_range').on('change',function(){
        var end     = $(this).val();
        var start   = $('#hr_department_min_range').val();
        if(start == '' || start == null){
            alert("Please enter Min-Value first");
            $('#hr_department_max_range').val('');
        }
        else{
             if(parseInt(end) < parseInt(start)){
                alert("Invalid!!\n Min_Value is bigger than Max-Value");
                $('#hr_department_max_range').val('');
            }
        }
    });
    //date validation end---------------
});
</script>
@endpush
@endsection
