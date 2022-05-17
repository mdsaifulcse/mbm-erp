
@php
    if(isset($request1['salstatus']) && $request1['salstatus'] == 'ot'){
        $listType = 'Over Time Payable';
    }
    elseif(isset($request1['salstatus']) && $request1['salstatus'] == 'salary'){
        $listType = 'Salary Payable';
    }
    else{
        $listType = 'Total Salary Payable';
    }
@endphp
<div class="panel panel-info col-sm-12 col-xs-12">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                <a href="#" class="search_all" data-category="{{ $request1['category'] }}" data-type="{{ $request1['type'] }}"> MBM Group </a>
            </li>
            
            @if(isset($request2['as_unit_id']))
                <li>
                    <a href="#" class="search_unit"> All Unit </a>
                </li>
                <li>
                    <a href="#" class="search_area" data-unit="{{ $request2['as_unit_id'] }}">
                        {{ $data['unit']->hr_unit_name }}
                    </a>
                </li>
            @endif
            @if(isset($request2['as_area_id']))
                <li>
                     <a href="#" class="search_dept" data-area="{{ $request2['as_area_id'] }}">
                        {{ $data['area']->hr_area_name }}
                    </a>
                </li>
            @endif
            @if(isset($request2['as_department_id']))
                <li>
                    <a href="#" class="search_floor" data-department="{{ $request2['as_department_id'] }}">
                        {{ $data['department']->hr_department_name }}
                    </a>
                </li>
            @endif
            @if(isset($request2['as_floor_id']))
                <li>
                    <a href="#" class="search_section" data-floor="{{ $request2['as_floor_id'] }}">
                        {{ $data['floor']->hr_floor_name }}
                    </a>
                </li>
            @endif
            @if(isset($request2['as_section_id']))
                <li>
                    <a href="#" class="search_subsection" data-section="{{ $request2['as_section_id'] }}">
                        {{ $data['section']->hr_section_name }}
                    </a>
                </li>
            @endif
            @if(isset($data['subsection']))
                <li>
                    {{ $data['subsection']->hr_subsec_name }}
                </li>
            @endif
            <li class="active"> Employee List ({{$listType}})</li>
        </ul><!-- /.breadcrumb -->

    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <!-- <h4 class="center">MBM Group</h4> -->
        
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
                <div class="col-sm-12">
                    <table id="dataTables" class="table table-striped table-bordered" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Associate ID</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Floor</th>
                                <th class="center">Month</th>
                                <th  class="align-right"  >OT Payable</th>
                                <th  class="align-right">Salary Payable</th>
                                <th  class="align-right">Total Payable</th>
                            </tr>
                        </thead>
                    </table>
                </div>

        </div>
    </div>
</div>


<script type="text/javascript">
$(document).ready(function(){
    $(document).on('click','.employee_info', function() {
        var data = {
            emp: $(this).data('emp')
        };
        var url = getCategoryWiseUrl(categorySelect,'employee_info');
        attAjaxCall(url,data);
    });
        var searchable = [1,2,3,4];
        var selectable = []; //use 4,5,6,7,8,9,10,11,....and * for all
        var dropdownList = {
        };

        var exportColName = ['Sl.','Associate ID','Name','Designation','Floor','Month','OT Payable','Salary Payable', 'Total Payable'];
        var exportCol = [0,1,2,3,5,6,7,8];

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
                url: '{!! url("hr/search/hr_salary_search_employee_list") !!}',
                data: {
                    unit: '{{ isset($request2['as_unit_id'])?$request2['as_unit_id']:'' }}',
                    area: '{{ isset($request2['as_area_id'])?$request2['as_area_id']:'' }}',
                    department: '{{ isset($request2['as_department_id'])?$request2['as_department_id']:'' }}',
                    section: '{{ isset($request2['as_section_id'])?$request2['as_section_id']:'' }}',
                    floor: '{{ isset($request2['as_floor_id'])?$request2['as_floor_id']:'' }}',
                    subsection: '{{ isset($request2['as_subsection_id'])?$request2['as_subsection_id']:'' }}'
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
                    title: '',
                    "action": allExport,
                    exportOptions: {
                        columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      },
                        stripHtml: true
                    },
                    title: '',
                    messageTop: function () {
                        return  '<h3 class="text-center">MBM Garments Ltd.</h3>'+
                                '<h4 class="text-center">{{$listType}} Report</h4>'+
                                '@if(isset($request2["as_unit_id"]))<h6 class="text-center">Unit: {{ $data["unit"]->hr_unit_name }} </h6>@endif'+
                                '@if(isset($request2["as_area_id"]))<h6 class="text-center">Area: {{$data["area"]->hr_area_name}}'+
                                    '@if(isset($request2["as_department_id"]))| Department: {{$data["department"]->hr_department_name}} @endif'+
                                    ' @if(isset($request2["as_floor_id"]))| Floor: {{$data["floor"]->hr_floor_name}}  @endif '+
                                    ' @if(isset($request2["as_section_id"]))| Section: {{$data["section"]->hr_section_name}} @endif '+
                                '</h6>@endif'+
                                '<h5 class="text-center">{{$showTitle}}</h5>';
                    }

                }
            ],

            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'associate_id',  name: 'associate_id' },
                { data: 'as_name', name: 'as_name'},
                { data: 'hr_designation_name',  name: 'hr_designation_name' },
                { data: 'hr_floor_name',  name: 'hr_floor_name' },
                { data: 'month',  name: 'month' },
                { data: 'ot_payable',  name: 'ot_payable' },
                { data: 'salary',  name: 'salary' },
                { data: 'total_payable',  name: 'total_payable' }
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
        var status = '{{ Request::get('salstatus') }}';
        if(status=== 'ot'){
            dt.column(7).visible(false);
            dt.column(8).visible(false);       
        }
        if(status=== 'salary'){
            dt.column(6).visible(false);
            dt.column(8).visible(false);       
        }
        console.log('fbgyh');
    });


    
</script>