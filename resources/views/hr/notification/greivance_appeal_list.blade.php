@extends('hr.layout')
@section('title', 'Greivance Appeal List')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Notification</a>
                </li>
                <li class="active">Greivance Appeal List</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <div class="page-header">
                <h1>Notification <small><i class="ace-icon fa fa-angle-double-right"></i> Greivance Appeal List </small></h1>
            </div>

            <div class="row">
                  <!-- Display Erro/Success Message -->
                @include('inc/message')
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS --> 
                        <table id="dataTables" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Sl. No</th>
                                    <th>Offender ID</th>
                                    <th>Griever ID</th>
                                    <th>Issue</th>
                                    <th>Step</th>
                                    <th>Discussed Date</th>
                                    <th>Request Remedy</th>
                                    <th>Action</th>
                                </tr>
                            </thead> 
                        </table>
                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->

                    <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col -->
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
        dom: "lBftrip", 
        buttons: [  
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary', 
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                exportOptions: {
                    columns: ':visible'
                } 
            } 
        ], 
        ajax: '{!! url("hr/notification/greivance/greivance_appeal_data") !!}',
        columns: [  
            { data: 'serial_no', name: 'serial_no' }, 
            { data: 'offender', name: 'offender' }, 
            { data: 'griever',  name: 'griever' }, 
            { data: 'issue', name: 'issue' }, 
            { data: 'hr_griv_appl_step', name: 'hr_griv_appl_step' }, 
            { data: 'hr_griv_appl_discussed_date', name: 'hr_griv_appl_discussed_date' }, 
            { data: 'hr_griv_appl_req_remedy', name: 'hr_griv_appl_req_remedy' }, 
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],  
    }); 
});
</script>
@endpush
@endsection