@extends('hr.layout')
@section('title', 'Partial Salary')

@section('main-content')
@push('js')
<style>
table tr p span{
    font-size: 10px !important;
}
table td, table th{
    vertical-align: top !important;
}
.table td {
    padding: 5px;
}
.panel-body {
    padding: 10px 8px;
}
h3 {
    font-size: 1rem;
}
h2, h2 b {
    font-size: 1.5rem;
}
#top-tab-list li a {
    border: 1px solid;
}
.iq-accordion.career-style .iq-accordion-block {
    margin-bottom: 15px;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    margin-top: 10px;
}
p span, p span font{
    font-size: 10px;
}
.iq-accordion-block{
    padding: 10px 0;
}

.select2-container--default .select2-selection--multiple {
    border-color: #d7dbda;
    height: 70px;
}



</style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Operation</a>
                </li>
                <li class="active"> Partial Salary Approval</li>
            </ul>
        </div>
        <div class="page-content"> 
            <div class="iq-accordion career-style mat-style  ">
                <div class="iq-card iq-accordion-block">
                    <div class="active-mat clearfix">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-sm-12"> <h2 style="text-align:center">Submitted Partial Salary for Approval (Pending List)</h2>
                                    <br>
                                    @php
                                    $count=count($process_paramiter);
                                    @endphp
                                    @if ($count==0)
                                    <td colspan="11">No Record Found..</td>
                                    @else
                                    <div calss="container">
                                        <table class="table table-bordered ">
                                            <thead class="table-active" style="text-align:center;">
                                                <tr>
                                                    <th>SL</th>
                                                    <th>Unit</th>
                                                    <th>Status</th>
                                                    <th>Salary<br>Date Range</th>
                                                    <th>OT Pay <br> Status</th>
                                                    <th>OT <br>Date Range</th>
                                                    <th>Salary <br>Below</th>
                                                    <th>Location</th>
                                                    <th>Area</th>
                                                    <th>Coments</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                $sl=0;

                                                @endphp
                                                @foreach($process_paramiter as $process_paramiter1)

                                                <tr style="text-align:center;">
                                                    <td style="text-align:center;">{{++$sl}}</td>
                                                    <td class="unitdtl"  data-toggle='tooltip' data-placement='top' data-original-title='Click To View Salary' data-id="{{ $process_paramiter1->id}}"  style="text-align:center;cursor: pointer;color:green;">{{$process_paramiter1->hr_unit_name}}</td>
                                                    <td>{{$process_paramiter1->Employee_status}}</td>
                                                    <td>{{$process_paramiter1->salary_from_date}}
                                                        <br>
                                                        {{$process_paramiter1->salary_to_date}}
                                                    </td>
                                                    <td>{{$process_paramiter1->ot_give_status}}</td>
                                                    <td>{{$process_paramiter1->ot_from_date}} <br>
                                                        {{$process_paramiter1->ot_to_date}}
                                                    </td>
                                                    <td>{{$process_paramiter1->salary_below}}</td>
                                                    <td style="text-align:left;width:100px;">{{$process_paramiter1->location_name}}</td>
                                                    <td style="text-align:left;width:80px;">{{$process_paramiter1->area_name}}</td>
                                                    <td style="text-align:left;width:60px;">  <div class="form-group has-float-label has-required">
                                                        <input type="text" class="report_date datepicker form-control" id="coment-{{ $process_paramiter1->id}}" name="coment"  required="required" value="" autocomplete="off" />

                                                    </div>
                                                </td>
                                                <td style="width:30px;">
                                                    <button class="btn btn-primary nextBtn btn-sm pull-center approve" type="submit" data-toggle='tooltip' data-placement='top' title='' data-original-title='Approve' id="approve-{{ $process_paramiter1->id}}" data-id="{{ $process_paramiter1->id}}"  value="approve"><i class="fa fa-check-square-o"></i></button>

                                                    <button class="btn btn-success nextBtn Approvesuccess btn-sm pull-center " type="" data-toggle='tooltip' data-placement='top' title='' data-original-title='Approved' id="Approvesuccess-{{ $process_paramiter1->id}}"   value=""><i class="fa fa-check-circle"></i></button>

                                                    <br><br>
                                                    <button class="btn btn-warning nextBtn btn-sm pull-center redo" type="submit" data-toggle='tooltip' data-placement='top' title='' data-original-title='Regenerate With Coment' id="redo-{{ $process_paramiter1->id}}" data-id="{{ $process_paramiter1->id}}"  value="redo"><i class="fa fa-repeat"></i></button>

                                                    <button class="btn btn-danger nextBtn btn-sm pull-center redodelete" type="submit" data-toggle='tooltip' data-placement='top' title='' data-original-title='Regenerate With Coment' id="redodelete-{{ $process_paramiter1->id}}" value=""><i class="fa fa-minus-square"></i></button>

                                                </td>
                                            </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-12" id="resultdata"> 
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.page-content -->
</div>
{{-- </div> --}}










@push('js')


<script type="text/javascript">

    $(".Approvesuccess").hide();
    $(".redodelete").hide();

    $(document).on('click','.approve', function(){
        if(confirm("Are you sure want to approve it now?")){
            approve_summit($(this).data('id'));  
        }else{
            return false;
        }

    });

    function approve_summit(id = null) {
// $('#tabledata').html(loaderContent);
$.ajax({
    type:'get',  
    url: '{{url("hr/operation/partial-salary-approve-flag/")}}/'+id,   

    data:{
// 'month':$('#month').val(),
'redo_flag':'N',
},
success:function(data){
    $.notify(data.msg, data.type)
    if (data.type=='success') {
// callAjax();   
$("#Approvesuccess-"+id).show();
$("#approve-"+id).hide();
$("#redo-"+id).hide();
}
}
});
}


$(document).on('click','.redo', function(){
    if(confirm("Are you sure want to Send back for correction?")){
        redo_summit($(this).data('id'));  
    }else{
        return false;
    }

});

function redo_summit(id = null) {
// $('#tabledata').html(loaderContent);
var coment=$("#coment-"+id).val();
if (coment==[]){
    $.notify('Please type coment', 'error')
}else{
    $.ajax({
        type:'get',  
        url: '{{url("hr/operation/partial-salary-approve-flag/")}}/'+id,   

        data:{
            'coment':$("#coment-"+id).val(),
            'redo_flag':'Y',
        },
        success:function(data){
            $.notify(data.msg, data.type)
            if (data.type=='success') {
// callAjax();   
$("#Approvesuccess-"+id).hide();
$("#approve-"+id).hide();
$("#redo-"+id).hide();
$("#redodelete-"+id).show();
}
}
});
}
}


 $(document).on('click','.unitdtl', function(){
            approve_data($(this).data('id'));  
    });

function approve_data(id = null) {
$('#resultdata').html(loaderContent);
$.ajax({
    type:'get',  
    // url: '{{url("hr/operation/partial-salary-approvedataview")}},    
    url: '{{url("hr/operation/partial-salary-unitdtl")}}/'+id,   

    data:{
// 'month':$('#month').val(),
},
success:function(data){ 
$('#resultdata').html(data);
}
});
}
</script>
@endpush
@endsection