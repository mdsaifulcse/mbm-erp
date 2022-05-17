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
            @if(isset($request2['as_unit_id']))
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
            @if(isset($request1['shiftcode']))
                <li>
                    <a href="#" class="search_ot_shift"
                    @if(isset($request2['as_unit_id'])) 
                        data-unit="{{ $request2['as_unit_id'] }}"
                    @endif
                    @if(isset($request2['as_area_id'])) 
                        data-area="{{ $request2['as_area_id'] }}"
                    @endif
                    @if(isset($request2['as_department_id'])) 
                        data-department="{{ $request2['as_department_id'] }}"
                    @endif
                    @if(isset($request2['as_floor_id'])) 
                        data-floor="{{ $request2['as_floor_id'] }}"
                    @endif
                    @if(isset($request2['as_section_id'])) 
                        data-section="{{ $request2['as_section_id'] }}"
                    @endif
                    >  {{$data['shift']->hr_shift_name??''}} 
                    </a>
                </li>
                @endif
            <li class="active">OT Employee List @if(isset($request1["hour"])) ({{$request1["hour"]}} Hour) @endif</li>
        </ul><!-- /.breadcrumb -->

    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <!-- <h4 class="center">MBM Group</h4> -->
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
        	<div class="col-sm-12">
                <table id="dataTables" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Associate ID</th>
                            <th>Name</th>
                            <th>Unit</th>
                            <th>Designation</th>
                            <th>Floor</th>
                            <th>Total OT</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
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
                url: '{!! url("hr/search/hr_ot_search_employee_list") !!}',
                data: {
                    unit: '{{ isset($request2['as_unit_id'])?$request2['as_unit_id']:'' }}',
                    area: '{{ isset($request2['as_area_id'])?$request2['as_area_id']:'' }}',
                    department: '{{ isset($request2['as_department_id'])?$request2['as_department_id']:'' }}',
                    section: '{{ isset($request2['as_section_id'])?$request2['as_section_id']:'' }}',
                    floor: '{{ isset($request2['as_floor_id'])?$request2['as_floor_id']:'' }}',
                    subsection: '{{ isset($request2['as_subsection_id'])?$request2['as_subsection_id']:'' }}',
                    hour: '{{isset($request1['hour'])?$request1['hour']:''}}',
                    shiftcode: '{{isset($request1['shiftcode'])?$request1['shiftcode']:''}}'
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
                        columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-sm btn-warning',
                    "action": allExport,
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdf',
                    "action": allExport,
                    className: 'btn-sm btn-primary',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {

                    extend: 'print',
                    autoWidth: true,
                    "action": allExport,
                    className: 'btn-sm btn-default print',
                    title: '',
                    exportOptions: {
                        columns: ':visible',
                        stripHtml: false
                    },
                    title: '',
                    messageTop: function () {
                        return  '<h3 class="text-center">MBM Garments Ltd.</h3>'+
                                '<h4 class="text-center">Employee OT Report @if(isset($request1["hour"])) ({{$request1["hour"]}} Hour) @endif</h4>'+
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
                { data: 'hr_unit_name', name: 'hr_unit_name'},
                { data: 'hr_designation_name',  name: 'hr_designation_name' },
                { data: 'hr_floor_name',  name: 'hr_floor_name' },
                { data: 'ot_hour',  name: 'ot_hour' }
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
    });


    
</script>