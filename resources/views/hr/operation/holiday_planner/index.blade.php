@extends('hr.layout')
@section('title', 'Holiday Planner List')
@section('main-content')
@push('css')
<style type="text/css">
    
    #dataTables th:nth-child(2) select{
      width: 250px !important;
    }

    #dataTables th:nth-child(5) input{
      width: 80px !important;
    }
    
    .nav-year {
        font-size: 13px;
        font-weight: bold;
        color: #9c9c9c;
        padding: 1px 10px;
        border-radius: 10px;
        margin: 0 2px;
        background: #eff7f8;
        display: inline-block;
    }
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
                    <a href="#"> Operation </a>
                </li>
                <li class="active"> Holiday : {{\Carbon\Carbon::parse($month)->format('F, Y')}}</li>
                <li class="top-nav-btn">
                    <a href="{{ url('hr/operation/holiday-planner/create')}}" class="btn btn-sm  btn-primary"> <i class="fa fa-plus"></i> Holiday Entry</a>
                    @php
                        $url = url("hr/operation/holiday-planner?year_month=$month");
                    @endphp
                    <a href="{{ $url.'&view=calendar' }}" class=" btn btn-sm  btn-success"> <i class="fa fa-calendar"></i> Calendar view</a>
                </li>
            </ul><!-- /.breadcrumb -->
 
        </div>
        <div class="page-content"> 
            <div class="panel">
                <div class="panel-body text-center p-2">
                    @foreach(array_reverse($months) as $k => $i)
                        <a href="{{url('hr/operation/holiday-planner?year_month='.$k)}}" class="nav-year @if($k== $month) bg-primary text-white @endif" data-toggle="tooltip" data-placement="top" title="" data-original-title="Holiday list of {{$i}}" >
                            {{$i}}
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="panel panel-info"> 
                <div class="panel-body">

                    <table id="dataTables" class="table table-striped table-bordered" style="width: 100%; overflow-x: auto; display: block; ">
                        <thead>
                            <tr>
                                <th width="8%">SL.</th>
                                <th width="25%">Unit</th>
                                <th width="12%">Date</th>
                                <th width="10%">Day Type</th>
                                <th width="10%">Open Status</th>
                                <th width="12%">Ref. Date</th>
                                <th width="20%">Comment</th>
                                <th width="15%">Action</th>
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
    var searchable = [2,3];
    var selectable = [1,4];
    var dropdownList = {
        '1':[@foreach($unit as $u) <?php echo "'$u'," ?> @endforeach],
        '4':['Holiday','General','OT']
    };
    var exportColName = ['Sl','Unit','Date','Day Type','Day Name', 'Reference Date', 'Comment'];
    var exportCol = [1,2,3,4,5,6,7];
    var dt = $('#dataTables').DataTable({
        order: [],  
        processing: true,
        responsive: false,
        serverSide: true,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '

        },
        pagingType: "full_numbers", 
        dom:'lBfrtip', 
        ajax: {
            url: '{!! url("hr/operation/holiday-planner-list") !!}',
            type: "GET",
            data:{
                year_month: '{{request()->get('year_month')??date('Y-m') }}'
            },
            // headers: {
            //       'X-CSRF-TOKEN': '{{ csrf_token() }}'
            // } 
        }, 
        buttons: [  
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                title: 'Holiday Planner',
                header: false,
                footer: true,
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
                title: 'Holiday Planner',
                header: false,
                footer: true,
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
                title: 'Holiday Planner',
                header: false,
                footer: true,
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
                className: 'btn-sm btn-default',
                title: 'Holiday Planner',
                header: true,
                footer: false,
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
            { data: 'hr_unit_name', name: 'hr_unit_name' }, 
            { data: 'hr_yhp_dates_of_holidays',  name: 'hr_yhp_dates_of_holidays' },
            { data: 'hr_yhp_comments', name: 'hr_yhp_comments' },  
            { data: 'open_status', name: 'open_status' },  
            { data: 'reference_date', name: 'reference_date' },  
            { data: 'reference_comment', name: 'reference_comment' },  
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ], 
        initComplete: function () {   
            var api =  this.api();

            // Apply the search 
            api.columns(searchable).every(function () {
                var column = this; 
                var input = document.createElement("input"); 
                input.setAttribute('placeholder', $(column.header()).text());
                input.setAttribute('style', 'width: 140px; height:25px; border:1px solid whitesmoke;');

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

                var select = $('<select  style="width: 140px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
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
@endpush
@endsection