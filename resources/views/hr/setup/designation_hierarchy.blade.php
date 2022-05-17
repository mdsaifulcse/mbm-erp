@extends('hr.layout')
@section('title', 'Designation Library')
@section('main-content')
@push('css')
<style>
.list-group-item {border: 0px; padding: 5px 5px 5px 2px;}
.aminul {border: 0px; padding: 0px 0px 0px 20px;}
ul.list-group{border: 0px solid #ccc}
.first-child{border-left: 0px solid #ccc;}
</style>


@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li> 
                <li>
                    <a href="#"> Setup </a>
                </li>
                <li class="active">Designation Hierarchy </li>

                <li class="top-nav-btn" style="text-align: Right">
                    <a id="" href="{{ url('hr/setup/designation')}}" class="btn-sm btn btn-primary" >
                        Back
                    </a>
                </li>

            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb" style="height:50px;">
                <li >
                    <div>
                        {{ Form::select('hr_designation_emp_type144', $emp_type, null, ['placeholder'=>'Select Associate Type', 'id'=>'hr_designation_emp_type144', 'class'=> 'form-control', 'required'=>'required']) }}  
                    </div>
                </li>
            </ul><!-- /.breadcrumb --> 
        </div>


        @include('inc/message')

        <div class="row">
            <div class="col-sm-6">
                <div class="panel panel-info">
                    <div class="panel-heading"><h6>Top Management</h6>           
                    </div> 
                    <div class="panel-body">
                        <div class="hidden output"></div>
                        @foreach($designation_for_top_managenent as $taxonomy)
                        <ul class="list-group ">

                            <li class="list-group-item" > &nbsp; <i class="fa fa-angle-right" aria-hidden="true"  ></i>
                                <a style="cursor: pointer;color:#58C0D2;" data-id="{{$taxonomy->hr_designation_id}}"   id="viewletter"  data-toggle="tooltip" data-placement="top" title="Double Click For Update" >
                                    &nbsp; {{$taxonomy->hr_designation_name}}   
                                    {{$employee_count[$taxonomy->hr_designation_id]??0}}   
                                </a>
                            </li>
                            @if(count($taxonomy->subcategory))
                            @include('hr.setup.designation_tree_include',['subcategory' => $taxonomy->subcategory])
                            @endif
                        </ul>
                        @endforeach

                    </div>
                </div>


                <div class="panel panel-info">
                    <div class="panel panel-info" id="worker_data">
                        {{-- <div class="panel-heading"><h6> Worker</h6></div>  --}}
                        {{-- <div class="panel-body"> --}}
                            {{-- <div class="hidden output"></div> --}}
                            {{-- @foreach($designation_for_worker as $taxonomyw)
                            <ul class="list-group ">
                                <li class="list-group-item" > &nbsp; <i class="fa fa-angle-right" aria-hidden="true"></i>  <a style="cursor: pointer;color:#58C0D2;" data-id="{{$taxonomyw->hr_designation_id}}"   id="viewletter"  data-toggle="tooltip" data-placement="top" title="Double Click For Update" >
                                    &nbsp; {{$taxonomyw->hr_designation_name}}</li>
                                    @if(count($taxonomyw->subcategory))
                                    @include('hr.setup.designation_tree_include',['subcategory' => $taxonomyw->subcategory])
                                    @endif
                                </ul>
                                @endforeach --}}

                            {{-- </div> --}}
                        </div>
                    </div>


                </div>

                <div class="col-sm-6">
                    <div class="panel panel-info">
                        <div class="panel panel-info">
                            <div class="panel-heading"><h6>Management & Stuff </h6></div> 
                            <div class="panel-body">
                                <div class="hidden output"></div>
                                @foreach($designation_for_managenent_staff as $taxonomy)
                                <ul class="list-group ">
                                    <li class="list-group-item" > &nbsp; <i class="fa fa-angle-right" aria-hidden="true"></i> <a style="cursor: pointer;color:#58C0D2;" data-id="{{$taxonomy->hr_designation_id}}"   id="viewletter"  data-toggle="tooltip" data-placement="top" title="Double Click For Update" >
                                        &nbsp; {{$taxonomy->hr_designation_name}}      
                                    </a> </li>
                                    @if(count($taxonomy->subcategory))
                                    @include('hr.setup.designation_tree_include',['subcategory' => $taxonomy->subcategory])
                                    @endif
                                </ul>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /.page-content -->

        </div>
    </div> 





    <div class="modal fade" id="exampleModal"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title " id="exampleModalLabel">Designation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" >
                    <div id="showdata">

                    </div>
                    <form id="myfrom">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="panel panel-info">

                                    <div class="panel-body">
                                        <div class="hidden output"></div>
                                        <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/designation')  }}" enctype="multipart/form-data">
                                            @csrf

                                            <div class="form-group has-required has-float-label">
                                                <input type="text" name="hr_designation_namedisplay" placeholder="Designation Name" id="hr_designation_namedisplay" class="form-control" required="required"  disabled />
                                                <label  for="hr_designation_namedisplay" > Parent Designation  </label>
                                            </div> 


                                            <div class="form-group has-required has-float-label select-search-group">
                                                {{ Form::select('hr_designation_emp_type', $emp_type, null, ['placeholder'=>'Select Associate Type', 'id'=>'hr_designation_emp_type', 'class'=> 'form-control', 'required'=>'required']) }}  
                                                <label  for="hr_designation_emp_type"> Associate Type  </label> 
                                            </div>

                                            <input type="text" name="hr_designation_id" placeholder="Designation Name" id="hr_designation_id" class="form-control" required="required" hidden />
                                            <div class="form-group has-required has-float-label">
                                                <input type="text" name="hr_designation_name" placeholder="Designation Name" id="hr_designation_name" class="form-control" required="required" />
                                                <label  for="hr_designation_name" > Designation Name  </label>
                                            </div>  

                                            <div class="form-group has-required has-float-label">
                                                <input type="text" id="hr_designation_name_bn" name="hr_designation_name_bn" placeholder="পদের নাম" class="form-control"  />
                                                <label  for="hr_designation_name_bn" > পদবী (বাংলা) </label>
                                            </div>  


                                            <div class="form-group has-required has-float-label">
                                                <input type="text" id="designation_short_name" name="designation_short_name" placeholder="Grade" class="form-control" required="required" />
                                                <label  for="designation_short_name" >Sort Name </label>
                                            </div> 


                                            <div class="form-group has-required has-float-label select-search-group">
                                                {{ Form::select('hr_designation_grade',$hr_grade, null, ['placeholder'=>'Select Grade', 'id'=>'hr_designation_grade', 'class'=> 'form-control', 'required'=>'required']) }}  
                                                <label  for="hr_designation_grade">Grade list</label>
                                            </div>



                                            <div class="form-group has-required has-float-label select-search-group" id="parent_id1_div">
                                                @php
                                                $parent[0] = 'Main Head Top Management';
                                                $parent[-1] = 'Main Head Management';
                                                $parent[-2] = 'Main Head Worker';


                                                @endphp
                                                {{ Form::select('parent_id1', $parent, null, ['placeholder'=>'Select Associate Type', 'id'=>'parent_id1','name'=>'parent_id1', 'class'=> 'form-control', 'required'=>'required']) }}  
                                                <label  for="parent_id1"> Parent_id  </label> 
                                            </div>



                                            <div class="form-group has-float-label select-search-group" id="parent_id_div">
                                                <select name="parent_id" class="form-control capitalize select-search" id="parent_id" required="required">
                                                    <option selected="" value="">Choose...</option>
                                                </select>
                                                <label for="parent_id">New Parent Id</label>
                                            </div>


                                            <div class="form-group ">
                                                <div>

                                                    <input class="radio" type="radio" id="create_dsgn" name="radio" value="C">
                                                    <label for="html">Create New</label>
                                                    <br>
                                                    <input class="radio" type="radio" id="update_dsgn" name="radio" value="U">
                                                    <label for="css">Update</label>
                                                    <br>
                                                    <input class="radio" type="radio" id="change_parent" name="radio" value="P">
                                                    <label for="css">Change Parent</label>
                                                    <br>
                                                    <input class="radio" type="radio" id="inacive" name="radio" value="I">
                                                    <label for="css">Inactive</label>

                                                </div>

                                            </div>

                                            <div class="form-group">


                                                <input type="button" class="btn btn-success nextBtn btn-lg pull-right"   id="btn_update" value="Save" style="width:82px">



                                            </div>
                                        </form>
                                    </div>
                                </div>




                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@push('js')


<script type="text/javascript">
    var keep;
$('#global-datatable1').DataTable();


$(document).on('change','#hr_designation_emp_type144', function(){
$('#worker_data').html(loaderContent);
get_worker_data();
});

function get_worker_data()
{ 
    $.ajax({
        url: '{{url("hr/setup/designation_get_worker_data")}}', 
        type:'get', 
        data:{
             'emp_type':$('#hr_designation_emp_type144').val(),
         },
        success:function(data){
        $("#worker_data").html(data);
        }
    })
}



$(document).on('change','.radio', function(){
$("#btn_update").show();

    var selected=$("input[type='radio'][name='radio']:checked");
    if (selected.length>0) {
        selectedVal = selected.val();
    }


if (selectedVal=='C'){
    // $("#hr_designation_namedisplay").show();
    $('#hr_designation_emp_type').prop('disabled', true);
    $('#parent_id1').prop('disabled', true);
    $('#parent_id_div').hide();
    $('#parent_id1_div').show();
    $('#hr_designation_name').prop('disabled', false);
    $('#hr_designation_name_bn').prop('disabled', false);
    $('#designation_short_name').prop('disabled', false);
    $('#hr_designation_grade').prop('disabled', false);

    $('#hr_designation_name').val('');
    $('#hr_designation_name_bn').val('');
    $('#designation_short_name').val('');
    $('#hr_designation_grade').val('').trigger('change');
    // $('#hr_designation_emp_type').val('').trigger('change');
}
if (selectedVal=='U'){
    keep_value();
    $('#hr_designation_emp_type').prop('disabled', true);
    $('#parent_id1').prop('disabled', true);
    $('#parent_id_div').hide();
    $('#parent_id1_div').show();
    $('#hr_designation_name').prop('disabled', false);
    $('#hr_designation_name_bn').prop('disabled', false);
    $('#designation_short_name').prop('disabled', false);
    $('#hr_designation_grade').prop('disabled', false);
    // $("#hr_designation_namedisplay").hide();
}
if (selectedVal=='P'){
    keep_value();
    $('#hr_designation_emp_type').prop('disabled', false);
    $('#parent_id1_div').hide();
    $('#parent_id_div').show();
     $('#parent_id').prop('disabled', false);
    $('#parent_id1').prop('disabled', false);
    $('#hr_designation_name').prop('disabled', true);
    $('#hr_designation_name_bn').prop('disabled', true);
    $('#designation_short_name').prop('disabled', true);
    $('#hr_designation_grade').prop('disabled', false);
    // $("#hr_designation_namedisplay").show();
    
    
}

if (selectedVal=='I'){
    keep_value();
    $('#hr_designation_emp_type').prop('disabled', true);
    $('#parent_id1').prop('disabled', true);
    $('#parent_id1_div').show();
    $('#parent_id_div').hide();
    $('#hr_designation_name').prop('disabled', true);
    $('#hr_designation_name_bn').prop('disabled', true);
    $('#designation_short_name').prop('disabled', true);
    $('#hr_designation_grade').prop('disabled', true);
}


});


$(document).on('change','#hr_designation_emp_type', function(){
parent_get();
});
function parent_get()
{ 
    $.ajax({
        url: '{{url("hr/setup/designation_parentget")}}', 
        type:'get', 
        data:{
             'hr_designation_emp_type':$('#hr_designation_emp_type').val(),
         },
        success:function(data){
        $("#parent_id").html(data);
        // $("#parent_id").html(data);
        }
    })
}



$(document).on('dblclick','#viewletter', function(){
    $("input[name='radio'][value='"+'U'+"']").prop('checked', true);
    viewletter($(this).data('id'));
    $("#myfrom").hide();
});

function viewletter(id = null) {
    // console.log(id);
    $("#exampleModal").modal();
    $('#showdata').html(loaderContent);
    $.ajax({
    type:'get',  
    url: '{{url("hr/setup/designationgetdata/")}}/'+id,   
    data:{},
success:function(data){
    keep = data[0];
    // console.log(keep);
    // console.log(data);
    $('#showdata').html(data);
    $("#myfrom").show();
    $('#hr_designation_emp_type').val(data[0].hr_designation_emp_type).trigger('change');
    $('#hr_designation_id').val(data[0].hr_designation_id);
    $('#hr_designation_name').val(data[0].hr_designation_name);
    $('#hr_designation_namedisplay').val( data[0].hr_designation_name);
    $('#hr_designation_name_bn').val(data[0].hr_designation_name_bn);
    $('#designation_short_name').val(data[0].designation_short_name);
    $('#hr_designation_grade').val(data[0].hr_designation_grade).trigger('change');
    $('#parent_id1').val(data[0].parent_id).trigger('change');
    $('#hr_designation_emp_type').prop('disabled', true);
    $('#parent_id1').prop('disabled', true);
    $('#parent_id_div').hide();
    $('#parent_id1_div').show();
}
});


}


function keep_value(id = null) {
    $('#hr_designation_name').val(keep.hr_designation_name);
    $('#hr_designation_name_bn').val(keep.hr_designation_name_bn);
    $('#designation_short_name').val(keep.designation_short_name);
    $('#hr_designation_grade').val(keep.hr_designation_grade).trigger('change');
}

$(document).on('click','#btn_update', function(){
         if(confirm("Are you sure  want to update it now")){
            btn_update();  
            }
            else{
                 return false;
            }
    
    });

    function btn_update(id = null) {
    // $('#tabledata').html(loaderContent);
    var selected=$("input[type='radio'][name='radio']:checked");
    if (selected.length>0) {
        selectedVal = selected.val();
    }

    $.ajax({
    type:'post',  
    url: '{{url("hr/setup/designation_update")}}',   
    data:{
    '_token': '{{csrf_token()}}',
    'hr_designation_emp_type':$('#hr_designation_emp_type').val(),
    'hr_designation_name':$('#hr_designation_name').val(),
    'hr_designation_name_bn':$('#hr_designation_name_bn').val(),
    'designation_short_name':$('#designation_short_name').val(),
    'hr_designation_grade':$('#hr_designation_grade').val(),
    'parent_id':$('#parent_id').val(),
    'parent_id1':$('#parent_id1').val(),
    'hr_designation_id':$('#hr_designation_id').val(),
    'action_type':selectedVal,
    },
    success:function(data){
    if (data.type=='success') {  
    $.notify(data.msg, data.type)
    }
    else{
   $.notify(data.msg, data.type)
    }

    }
    });
    }


</script>
@endpush
@endsection