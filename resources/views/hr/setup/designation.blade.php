@extends('hr.layout')
@section('title', 'Designation Library')
@section('main-content')
@push('css')
    <style>
       
    </style>

@endpush
<div class="main-content">
    <div class="main-content-inner">
        
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">

            <ul class="breadcrumb">
                <div class="container p-0" style="height: 35px;">
                    <div class="row p-0">
                        <div class="col-sm-8" style="border-color: red;">
                            <ul class="breadcrumb">
                                <li>
                                    <i class="ace-icon fa fa-home home-icon"></i>
                                    <a href="#"> Human Resource </a>
                                </li> 
                                <li>
                                    <a href="#"> Setup </a>
                                </li>
                                <li class="active"> Designation </li>
                            </ul>
                        </div>
                        <div class="col-sm-2">

                        </div>
                        <div class="col-sm-2"  style="padding-right: 10px;padding-top: 5px;">

                            <li class="top-nav-btn" style="text-align: Right">
                               <a id="" href="{{ url('hr/setup/designation_hierarchy')}}" class="btn-sm btn btn-primary" >
                                View Hierarchy
                            </a>
                        </li>
                    </div>

                </div>
            </div>
        </ul>


    </div>
     

        @include('inc/message')

        <div class="row">
            <div class="col-sm-4">
                <div class="panel panel-info">
                    <div class="panel-heading"><h6>Designation</h6></div> 
                    <div class="panel-body">
                        <div class="hidden output"></div>
                        <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/designation')  }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group has-required has-float-label select-search-group">
                                {{ Form::select('hr_designation_emp_type', $emp_type, null, ['placeholder'=>'Select Associate Type', 'id'=>'hr_designation_emp_type', 'class'=> 'form-control', 'required'=>'required']) }}  
                                <label  for="hr_designation_emp_type"> Associate Type  </label> 
                            </div>

                            <div class="form-group has-required has-float-label">
                                <input type="text" name="hr_designation_name" placeholder="Designation Name" class="form-control" required="required" />
                                <label  for="hr_designation_name" > Designation Name  </label>
                            </div>  

                            <div class="form-group has-required has-float-label">
                                <input type="text" id="hr_designation_name_bn" name="hr_designation_name_bn" placeholder="পদের নাম" class="form-control"  />
                                <label  for="hr_designation_name_bn" > পদবী (বাংলা) </label>
                            </div>  


                            <div class="form-group has-required has-float-label">
                                <input type="text" name="designation_short_name" placeholder="Grade" class="form-control" required="required" />
                                <label  for="designation_short_name" >Sort Name </label>
                            </div> 

                            
                            <div class="form-group has-required has-float-label select-search-group">
                                {{ Form::select('hr_designation_grade',$hr_grade, null, ['placeholder'=>'Select Grade', 'id'=>'hr_designation_grade', 'class'=> 'form-control', 'required'=>'required']) }}  
                                <label  for="hr_designation_grade">Grade list</label>
                            </div>


                             <div class="form-group has-float-label select-search-group">
                                            <select name="parent_id" class="form-control capitalize select-search" id="parent_id" required="required">
                                                <option selected="" value="">Choose...</option>
                                            </select>
                                            <label for="parent_id">Parent Id</label>
                             </div>

                             @if(auth()->user()->hasRole('Super Admin'))
                            <div class="form-group">
                                <button class="btn btn-success" type="submit">
                                    <i class=" fa fa-check bigger-110"></i> Submit
                                </button>

                            </div>
                            @endif
                        </form>
                    </div>
                </div>


              

            </div>
            <div class="col-sm-8">
                <div class="panel panel-info">
                    <div class="panel-body">


                        <table id="global-datatable1"class="table table-striped table-bordered" style="display: block;width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Associate Type</th>
                                    <th style="width: 30%;">Designation Name</th>
                                    <th style="width: 30%;">পদবী (বাংলা)</th>
                                    <th style="width: 20%;">Grade</th>
                                    <th style="width: 20%;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="sortable">
                                @foreach($designations as $designation)
                                <tr class="ui-state-default" style="cursor:move">
                                    <td>{{ $designation->hr_emp_type_name }}</td>
                                    <td>{{ $designation->hr_designation_name }}</td>
                                    <td>{{ $designation->hr_designation_name_bn }}</td>
                                    <td>{{ $designation->hr_designation_grade }}</td>
                                    <td>
                                      {{--   <input type='hidden' class="position" name='designation[{{ $designation->hr_designation_id }}]' value='{{ $designation->hr_designation_position }}'> --}}
                                        <div class="btn-group">
                                            <a type="button" href="{{ url('hr/setup/designation_update/'.$designation->hr_designation_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                            <a href="{{ url('hr/setup/designation/'.$designation->hr_designation_id) }}" type="button" onclick="return confirm('Are you sure?')" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div> 
@push('js')



<script type="text/javascript">
$('#global-datatable1').DataTable();




</script>
@endpush
@endsection