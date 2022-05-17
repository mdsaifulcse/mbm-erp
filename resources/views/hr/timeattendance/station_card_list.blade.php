@extends('hr.layout')
@section('title', 'Line Change List')
@section('main-content')
@push('css')
<style type="text/css">
    {{-- removing the links in print and adding each page header --}}
    a[href]:after { content: none !important; }
    thead {display: table-header-group;}

    /*making place holder custom*/
    input::-webkit-input-placeholder {
        color: black;
        font-weight: bold;
        font-size: 12px;
    }
    input:-moz-placeholder {
        color: black;
        font-weight: bold;
        font-size: 12px;
    }
    input:-ms-input-placeholder {
        color: black;
        font-weight: bold;
        font-size: 12px;
    }
    th{
        font-size: 12px;
        font-weight: bold;
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
                    <a href="#">Time & Attendance</a>
                </li>
                <li class="active">Station Card List</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            

            <div class="row">
               <div class="page-header">
                   <div class=" text-right">
                    <a type="button" class="btn btn-primary btn-xs" href="{{ url('hr/timeattendance/new_card') }}">Add Station</a>
                </div><!-- /.Widget Header-->
               </div>
                
                <!-- Display Erro/Success Message -->
                @include('inc/message')
                <div class="col-xs-12 worker-list">
                    <!-- PAGE CONTENT BEGINS --> 
                    <table id="dataTables" class="table table-striped table-bordered" style="width: 100%; overflow-x: auto; display: block; ">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Unit</th>
                                <th>Associate ID</th>
                                <th>Name</th>
                                <th>Floor</th>
                                <th>Line</th>
                                <th>Changed Floor</th>
                                <th>Changed Line</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Changed By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                         
                    </table>

                    <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col -->
            </div>
            <br>
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
$(document).ready(function()
{   
    var searchable = [2,3,8,9,10];
    var selectable = [1,4,5,6,7]; //use 4,5,6,7,8,9,10,11,....and * for all
 
    var dropdownList = {
        '1' :[@foreach($unitList as $e) <?php echo "'$e'," ?> @endforeach],
        '4' :[@foreach($floorList as $e) <?php echo "'$e'," ?> @endforeach],
        '5' :[@foreach($lineList as $e) <?php echo "'$e'," ?> @endforeach],
        '6' :[@foreach($floorList as $e) <?php echo "'$e'," ?> @endforeach],
        '7' :[@foreach($lineList as $e) <?php echo "'$e'," ?> @endforeach],
    }; 

    $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        responsive: false,
        serverSide: true,
        pagingType: "full_numbers",
        ajax: {
            url: '{!! url("hr/timeattendance/station_card_data") !!}',
            type: "get",
            headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
            } 
        },
        dom: "lBftrip", 
        buttons: [  
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                title: 'Station Card List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                title: 'Station Card List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary',
                title: 'Station Card List',
                header: false,
                footer: true,
                pageSize: 'A4',
                orientation: 'landscape', 
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: 'Station Card List',
                header: true,
                footer: false,
                pageSize: 'A4',
                orientation: 'landscape',
                exportOptions: {
                    columns: ':visible',
                    stripHtml: false
                } 
            } 
        ], 
        columns: [ 
            { data: 'DT_RowIndex', name: 'DT_RowIndex' }, 
            { data: 'hr_unit_name',  name: 'hr_unit_name' }, 
            { data: 'associate_id',  name: 'associate_id' }, 
            { data: 'as_name', name: 'as_name' }, 
            { data: 'hr_floor_name', name: 'hr_floor_name' }, 
            { data: 'hr_line_name', name: 'hr_line_name' }, 
            { data: 'changed_floor', name: 'changed_floor' }, 
            { data: 'changed_line', name: 'changed_line' }, 
            { data: 'start_date', name: 'start_date' }, 
            { data: 'end_date', name: 'end_date' }, 
            { data: 'updated_by', name: 'updated_by' }, 
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ], 
        initComplete: function () {   
            var api =  this.api();

            // Apply the search 
            api.columns(searchable).every(function () {
                var column = this; 
                var input = document.createElement("input"); 
                input.setAttribute('placeholder', $(column.header()).text());
                input.setAttribute('style', 'width: 110px; height:25px; border:1px solid whitesmoke;');

                $(input).appendTo($(column.header()).empty())
                .on('keyup', function () {
                    column.search($(this).val(), false, false, true).draw();
                });

                $('input', this.column(column).header()).on('click', function(e) {
                    e.stopPropagation();
                });
            });
 
            // each column select list
            api.columns(selectable).every( function (i, x) {
                var column = this; 

                var select = $('<select style="width: 110px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
                    .appendTo($(column.header()).empty())
                    .on('change', function(e){
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                        column.search(val ? val : '', true, false ).draw();
                        e.stopPropagation();
                    });

                // column.data().unique().sort().each( function ( d, j ) {
                // if(d) select.append('<option value="'+d+'">'+d+'</option>' )
                // });
                $.each(dropdownList[i], function(j, v) {
                    select.append('<option value="'+v+'">'+v+'</option>')
                }); 
            });
        }   
    }); 

});
</script>


@endsection