@extends('hr.layout')
@section('title', 'Shift List')
@push('css')
    <style type="text/css">
        .label-head{
            background: #ffefef;
            padding: 3px 5px;
        }
    </style>
@endpush

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
                <li class="active"> Shift </li>
                <li class="top-nav-btn">
                    <a class="btn btn-primary  btn-sm" href="{{ url('hr/operation/shift') }}"><i class="fa fa-plus"></i></a>
                    <a class="btn btn-primary  btn-sm" href="{{ url('hr/operation/shift_assign') }}"><i class="fa fa-users"></i> Shift Assign</a>
                </li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="panel">
                <div class="panel-body">

                    <div class="table-responsive">
                        <table id="global-datatable" class="table table-striped table-bordered" style="display: block;width: 100%;">
                            <thead>
                                <tr>
                                    <th width="10%">Sl.</th>
                                    <th width="20%">Unit Name</th>
                                    <th width="20%">Shift Name</th>
                                    <th width="20%">Shift Time</th>
                                    <th width="10%">Break (Min)</th>
                                    <th width="20%" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=0; @endphp
                                @foreach($shifts as $shift)
                                <?php 
                                    $code= $shift->hr_shift_code;
                                    $letters = preg_replace('/[^a-zA-Z]/', '', $code);
                                    ?>
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $shift->unit->hr_unit_short_name }}</td>
                                    <td>{{ $shift->hr_shift_name }}</td>
                                    <td>{{ $shift->current_shift_time->hr_shift_start_time }} - {{ $shift->current_shift_time->hr_shift_out_time }}</td>
                                    <td class="text-center">{{ $shift->current_shift_time->hr_shift_break_time }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            {{-- <button type="button" class="btn btn-xs btn-success shift_times_btn" value="{{ $letters }}" data-toggle="modal" data-target="#myModal"><i class="fa fa-eye"></i></button> --}}
                                            {{--  --}}
                                            {{-- <a href="{{ url('hr/setup/shift/'.$shift->hr_shift_id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a> --}}
                                            <a type="button" href="{{ url('hr/operation/shift/'.$shift->hr_shift_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a> &nbsp;
                                            <button type="button" class="btn btn-xs btn-success shift_history_btn" data-id="{{ $shift->hr_shift_id }}" ><i class="fa fa-history"></i></button>
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


{{-- shift history block --}}
    <div id="myModal" class="modal fade right" role="dialog">
        <div class="modal-dialog" style="width: 1000px;">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" style="background-color:  lightblue;">
                    <h4 class="modal-title">Shift History</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div id="shift-details" class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm no-padding" style="border-radius: 2px;" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

{{-- get shift details --}}

@endsection
@push('js')
<script type="text/javascript">
    $(document).on('click', '.shift_history_btn', function(){
        $('#myModal').modal('show');
        $('#shift-details').html(loaderContent);
        let id = $(this).data('id');

        $.ajax({
            url : "{{ url('hr/operation/getshift') }}/"+id,
            method: 'get',
            success: function(data)
            {
                $('#shift-details').html(data);
            },
            error: function()
            {
                $.notify('failed...');
            }
        });
    });
    
</script>
@endpush
