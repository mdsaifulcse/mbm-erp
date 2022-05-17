@extends('hr.layout')
@section('title', 'Benefit List')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="col-sm-12">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="#">Human Resource</a>
                    </li>
                    <li>
                        <a href="#">Employee</a>
                    </li>
                    <li class="active">Benefit List</li>
                </ul><!-- /.breadcrumb -->
     
            </div>

            @include('inc/message')
            <div class="panel panel-success">
                <div class="panel-body">
                    <div class="worker-list">
                        <table id="dataTables" class="table table-striped table-bordered" style="display:table;overflow-x: auto;width: 100%;">
                            <thead>
                                <tr>
                                    <!-- <th>Sl. No</th> -->
                                    <th>Associate ID</th>
                                    <th>Name</th>
                                    <th>Oracle ID</th>
                                    <th>Unit</th>
                                    <th>Department</th>
                                    <th>Line</th>
                                    <th>Month</th>
                                    <th>Bonus Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
<script type="text/javascript">
$(document).ready(function(){ 
    var searchable = [0,1,2];
    var selectable = [3]; 

    var dropdownList = {
        '3' :[@foreach($unitList as $e) <?php echo "'$e'," ?> @endforeach]
    };

    var exportColName = ['Associate ID','Name','Oracle ID','Unit','Department', 'Line','Month' ,'Bonus Amount'];
    var exportCol = [0,1,2,3,4,5,6,7];
    
    var dt = $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        responsive: true,
        serverSide: true,
        lengthMenu: [[10,25, 100, -1], [10,25, 100, "All"]],
        pagingType: "full_numbers",
        language: {
            processing: '<i class="fa fa-spinner fa-spin f-60"></i><span class="sr-only">Loading...</span> '

        },
        ajax: {
            url: '{!! url("hr/operation/production-bonus-list-data") !!}',
            type: "POST",
            headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
            } 
        },
        dom: "lBfrtip", 
        buttons: [   
            {
                extend: 'csv', 
                className: 'btn btn-sm btn-success',
                title: 'Employee benefit report',
                header: true,
                footer: false,
                exportOptions: {
                    columns: exportCol,
                    format: {
                        header: function ( data, columnIdx ) {
                            return exportColName[columnIdx];
                        }
                    }
                },
                "action": allExport,
                messageTop: ''
            }, 
            {
                extend: 'excel', 
                className: 'btn btn-sm btn-warning',
                title: 'Employee benefit report',
                header: true,
                footer: false,
                exportOptions: {
                    columns: exportCol,
                    format: {
                        header: function ( data, columnIdx ) {
                            return exportColName[columnIdx];
                        }
                    }
                },
                "action": allExport,
                messageTop: '',
            }, 
            {
                extend: 'pdf', 
                className: 'btn btn-sm btn-primary', 
                title: 'Employee benefit report',
                header: true,
                footer: false,
                exportOptions: {
                    columns: exportCol,
                    format: {
                        header: function ( data, columnIdx ) {
                            return exportColName[columnIdx];
                        }
                    }
                },
                "action": allExport,
                messageTop: ''
            }, 
            {
                extend: 'print', 
                className: 'btn btn-sm btn-default',
                title: '',
                header: true,
                footer: false,
                exportOptions: {
                    columns: exportCol,
                    format: {
                        header: function ( data, columnIdx ) {
                            return exportColName[columnIdx];
                        }
                    }
                },
                "action": allExport,
                messageTop: customReportHeader('Employee benefit report', { "unit": "MBM Garments Ltd." })
            } 
        ], 
        columns: [ 
            { data: 'associate_id', name: 'associate_id' }, 
            { data: 'as_name',  name: 'as_name' }, 
            { data: 'as_oracle_code', name: 'as_oracle_code' }, 
            { data: 'hr_unit_short_name',  name: 'hr_unit_short_name' }, 
            { data: 'hr_department_name', name: 'hr_department_name' }, 
            { data: 'hr_line_name', name: 'hr_line_name' }, 
            { data: 'month', name: 'month' }, 
            { data: 'bonus_add', name: 'bonus_add' }, 
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ], 
        initComplete: function () {   
            var api =  this.api();

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

                $.each(dropdownList[i], function(j, v) {
                    select.append('<option value="'+v+'">'+v+'</option>')
                }); 
            });
        }   
    }); 

});
</script>
@endpush
@endsection
