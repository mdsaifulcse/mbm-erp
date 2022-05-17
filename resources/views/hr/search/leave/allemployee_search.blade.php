
<div class="panel panel-info col-sm-12">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                <a href="#" class="search_all" data-category="{{ $request1['category'] }}" data-type="{{ $request1['type'] }}"> MBM Group </a>
            </li>
            <li>
                <a href="#" class="search_unit"> All Unit </a>
            </li>
            <li class="active"> Employee </li>
        </ul><!-- /.breadcrumb -->
    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
        	<div class="col-sm-12">
                <div class="table-responsive">
                    <table id="dataTables" class="table table-striped table-bordered" >
                        <thead>
                            <tr>
                                <th>Sl. No</th>
                                <th>Associate ID</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Shift</th>
                                <th>Attendance Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $rangeFrom = date('Y-m-d');
    $rangeTo   = date('Y-m-d');
    if($request1['type'] == 'date'){
        $rangeFrom = $request1['date'];
        $rangeTo = $request1['date'];
    }
@endphp
<script type="text/javascript">
var attstatus = '{{ isset($request1['attstatus'])?$request1['attstatus']:'' }}';
$(document).ready(function(){ 

    var searchable = [1,2];
    var selectable = []; //use 4,5,6,7,8,9,10,11,....and * for all
    var dropdownList = {};
    var exportColName = ['Sl.','Associate ID','Name','Designation','Shift','Status'];
        var exportCol = [0,1,2,3,4,5];

    var dt =  $('#dataTables').DataTable({
       order: [], //reset auto order
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        processing: true,
        responsive: false,
        serverSide: true,
        language: {
          processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
            },
        scroller: {
            loadingIndicator: false
        },
        pagingType: "full_numbers",
        ajax: {
            url: '{!! url("hr/search/hr_att_search_allemp_data") !!}',
            data: function (d) {
                d.rangeFrom     = '{{ $rangeFrom }}',
                d.rangeTo       = '{{ $rangeTo }}'
            },
            type: "get",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        },
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'csv',
                className: 'btn-sm btn-success',
                "action": allExport,
                exportOptions: {
                    columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      }
                }
            },
            {
                extend: 'excel',
                className: 'btn-sm btn-warning',
                "action": allExport,
                exportOptions: {
                    columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      }
                }
            },
            {
                extend: 'pdf',
                className: 'btn-sm btn-primary',
                "action": allExport,
                exportOptions: {
                    columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      }
                }
            },
            {

                extend: 'print',
                autoWidth: true,
                className: 'btn-sm btn-default print',
                "action": allExport,
                exportOptions: {
                    columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      },
                    stripHtml: false
                }

            }
        ],

        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'associate_id',  name: 'associate_id' },
            { data: 'as_name', name: 'as_name' },
            { data: 'hr_designation_name', name: 'hr_designation_name' },
            { data: 'hr_shift_name', name: 'hr_shift_name' },
            { data: 'att_status', name: 'att_status' }
        ],
        initComplete: function () {
            var api =  this.api();

            // Apply the search
            api.columns(searchable).every(function () {
                var column = this;
                var input = document.createElement("input");
                input.setAttribute('placeholder', $(column.header()).text());

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

                var select = $('<select><option value="">'+$(column.header()).text()+'</option></select>')
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
    if(attstatus) {
        dt.columns(4).search(attstatus).draw();
    }
});
</script>