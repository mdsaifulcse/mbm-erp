@extends('hr.layout')
@section('title', 'Holiday Roster List')
@section('main-content')
@push('css')
<style type="text/css">
    
    #dataTables th:nth-child(3) input, #dataTables th:nth-child(4) input, #dataTables th:nth-child(5) input, #dataTables th:nth-child(6) input, #dataTables th:nth-child(8) input, #dataTables th:nth-child(12) input{
      width: 80px !important;
    }

    #dataTables th:nth-child(7) input{
      width: 100px !important;
    }
    #dataTables th:nth-child(2) input, #dataTables th:nth-child(9) input, #dataTables th:nth-child(10) input{
      width: 40px !important;
    }
    #dataTables th:nth-child(11) input{
      width: 60px !important;
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
                    <a href="{{ url('hr/operation/holiday-roster/create')}}" class="btn btn-sm  btn-primary"> <i class="fa fa-plus"></i> Holiday Roster Assign</a>
                    @php
                        $url = url("hr/operation/holiday-roster?year_month=$month");
                    @endphp
                    
                </li>
            </ul><!-- /.breadcrumb -->
 
        </div>
        <div class="page-content"> 
            <div class="panel">
                <div class="panel-body text-center p-2">
                    @foreach(array_reverse($months) as $k => $i)
                        <a href="{{url('hr/operation/holiday-roster?year_month='.$k)}}" class="nav-year @if($k== $month) bg-primary text-white @endif" data-toggle="tooltip" data-placement="top" title="" data-original-title="Holiday list of {{$i}}" >
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
                                <th width="5%">SL.</th>
                                <th width="5%">Unit</th>
                                <th width="10%">Designation</th>
                                <th width="10%">Section</th>
                                <th width="10%">Sub Section</th>
                                <th width="10%">Floor</th>
                                <th width="10%">Name</th>
                                <th width="10%">ID</th>
                                <th width="5%">Date</th>
                                <th width="5%">Day</th>
                                <th width="8%">Day Type</th>
                                <th width="10%">Comment</th>
                                <th width="8%">Action</th>
                            </tr>
                        </thead>  
                    </table>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
{{-- edit modal --}}
<div class="modal fade apps-modal" id="editModal" tabindex="-1" role="dialog" aria-labelledby="appsModalLabel" aria-hidden="true" data-backdrop="false" >
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content" style="height:auto;width: 380px;top: 50px;background: #fff;box-shadow: rgb(71 70 70) 0px 0px 5px 2px;border-radius: 10px;min-height: 100px;margin-bottom: 10px; overflow: auto;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            <div class="content-area p-4">
                <h4 class="font-weight-bold text-center">Holiday Edit <em id="select-asid"></em> / <em id="select-date"></em></h4>
                <hr>
                <form id="holiday-edit-form">
                    <div class="row">
                        <div class="col-md-12 ml-auto mr-auto">
                            <p class="font-weight-bold mb-2">  </p>
                            <input type="hidden" id="row-id" value="" name="id">
                            
                            @method('PUT')
                            <div class="form-group has-float-label">
                                <input type="date" name="date" id="date" placeholder=" Date" value="" class="form-control"> 
                                <label for="date">Date </label>
                            </div>
                            <div class="form-group has-required has-float-label select-search-group">
                                <select class="form-control" id="targetType" name="remarks" required onchange="dayType(this.value)">
                                    <option value="">Select Type</option>
                                    <option value="Holiday" >Holiday</option>
                                    <option value="Substitute-Holiday" >Substitute Holiday</option>
                                    <option value="General" >General</option>
                                    <option value="OT" >OT</option>
                                </select>
                                <label  for="targetType" style="color: maroon;">Day Type </label>
                            </div>
                            <div class="form-group has-float-label">
                                <textarea name="comment" class="form-control" id="comment" rows="1" placeholder="Comment"></textarea>
                                <label for="comment">Comment</label>
                            </div>
                            <div class="form-group has-float-label">
                                <input type="date" name="reference_date" id="ref_date" placeholder="Reference Date" value="" class="form-control"> 
                                <label for="ref_date">Reference Date </label>
                            </div>
                            <div class="form-group has-float-label">
                                <input type="text" name="reference_comment" id="reference_comment" placeholder="Comment" value="" class="form-control"> 
                                <label for="reference_comment">Reference Comment </label>
                            </div>
                            
                            <div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                                <input type="checkbox" name="type" class="custom-control-input bg-primary" id="customCheck" value="1">
                                <label class="custom-control-label" for="customCheck"> Festival Holiday</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group pull-right">
                        <button type="button" id="formSubmit" class="btn btn-outline-primary"><i class="fa fa-save"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('js')
<script type="text/javascript">

    {{-- holiday edit --}}
    $(document).on('click', '.holiday-edit', function(event) {
        $("#row-id").val($(this).data('id'));
        $("#select-date").html($(this).data('date'));
        $("#date").val($(this).data('date'));
        $("#select-asid").html($(this).data('as_id'));
        $("#targetType").val($(this).data('type')).change();
        $("#comment").val($(this).data('comment'));
        $("#ref_date").val($(this).data('ref-date'));
        $("#reference_comment").val($(this).data('ref-comment'));
        $("#emp_unit").val($(this).data('unit'));
        if($(this).data('holiday-type') == 2){
            $('#customCheck').prop('checked', true);
        }else{
            $('#customCheck').prop('checked', false);
        }
        $("#editModal").modal('show');
    });
    function dayType(val){
        if(val === 'Substitute-Holiday'){
            $("#comment").html('Substitute');
            $("#comment").attr('readonly', true);
        }else{
            $("#comment").html('');
            $("#comment").attr('readonly', false);
        }
    }

    $(document).ready(function(){ 
        var searchable = [1,2,3,4,5,6,7,8,9,10];
        var selectable = [];
        var dropdownList = {
            
        };
        var exportColName = ['Sl','Unit', 'Designation', 'Section', 'Sub Section', 'Floor', 'Name', 'ID','Date', 'Day','Day Type', 'Comment'];
        var exportCol = [1,2,3,4,5,6,7,8,9,10, 11,12];
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
                url: '{!! url("hr/operation/holiday-roster-list") !!}',
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
                    title: 'Holiday roster',
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
                    title: 'Holiday roster',
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
                    title: 'Holiday roster',
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
                    title: 'Holiday roster',
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
                { data: 'hr_designation_name', name: 'hr_designation_name' }, 
                { data: 'hr_section_name', name: 'hr_section_name' }, 
                { data: 'hr_subsec_name', name: 'hr_subsec_name' }, 
                { data: 'hr_floor_name', name: 'hr_floor_name' }, 
                { data: 'as_name', name: 'as_name' }, 
                { data: 'emp_id', name: 'emp_id' }, 
                { data: 'date',  name: 'date' },
                { data: 'day',  name: 'day' },
                { data: 'remarks', name: 'remarks' },  
                { data: 'comment', name: 'comment' },  
                // { data: 'reference_date', name: 'reference_date' },  
                // { data: 'reference_comment', name: 'reference_comment' },  
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
                    .on('keyup', function (e) {
                        if(e.keyCode == 13){
                            column.search($(this).val(), false, false, true).draw();
                        }
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
    $(document).on('click', '#formSubmit', function(event) {
        event.preventDefault();
        var id = $("#row-id").val();
        $(".app-loader").show();
        $.ajax({
            type: "POST",
            url: '{{ url("/hr/operation/holiday-roster") }}'+'/'+id,
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            data: $("#holiday-edit-form").serialize(),
            success: function(response)
            {
                console.log(response);
                if(response.type === 'success'){
                    $('.close').click();
                    location.reload();
                }
                
                setTimeout(function(){
                    $(".app-loader").hide();
                }, 300);
                if(response.message){
                    $.notify(response.message, response.type);
                }
            },
            error: function (reject) {
                $(".app-loader").hide();
                // console.log(reject)
            }
        });
    });
    
</script>
@endpush
@endsection