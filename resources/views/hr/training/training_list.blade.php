@extends('hr.layout')
@section('title', 'Training List')
@section('main-content')
@push('css')
<style type="text/css">
    a[href]:after { content: none !important; }
    thead {display: table-header-group;} 
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
                    <a href="#">Training</a>
                </li>
                <li class="active">Training List</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
                @include('inc/message')
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h6>Training List<a href="{{ url('hr/training/add_training')}}" class="pull-right btn  btn-primary">Add Training</a></h6>
                </div>
                <div class="panel-body">

                    <table id="dataTables" class="table table-striped table-bordered"  style="overflow-x: auto; width: 100%;">
                        <thead>
                            <tr>
                                <th>Sl. No</th>
                                <th>Training Name</th>
                                <th>Trainer Name</th>
                                <th>Schedule Date</th>
                                <th>Schedule Time</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>  
                    </table>
                               
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>

@push('js')
<script type="text/javascript">
$(document).ready(function(){ 

    $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        responsive: true,
        serverSide: true,
        pagingType: "full_numbers",
        dom: "lBftip", 
        buttons: [   
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                title: 'Training List',
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                title: 'Training List',
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary',
                title: 'Training List', 
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: 'Training List',
                exportOptions: {
                    columns: ':visible'
                } 
            } 
        ], 
        ajax: '{!! url("hr/training/training_data") !!}',
        columns: [ 
            { data: 'serial_no', name: 'serial_no' }, 
            { data: 'training_name',  name: 'training_name' }, 
            { data: 'tr_trainer_name', name: 'tr_trainer_name' }, 
            { data: 'schedule_date', name: 'schedule_date' }, 
            { data: 'schedule_time', name: 'schedule_time' }, 
            { data: 'tr_description', name: 'tr_description' }, 
            { data: 'action', name: 'action' }, 
        ],  
    }); 
});
</script>
@endpush
@endsection 