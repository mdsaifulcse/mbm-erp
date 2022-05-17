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
            <li>
                <a href="#" class="search_area" data-unit="{{ $request1['unit'] }}"> {{$unit->hr_unit_name}} </a>
            </li>
            <li>
                 <a href="#" class="search_dept" data-area="{{ $request1['area'] }}">{{ $area->hr_area_name }} </a>
            </li>
            <li class="active"> {{ $department->hr_department_name }} </li>
            <li>
                 Floor
            </li>
        </ul><!-- /.breadcrumb -->
        <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv({{json_encode($floor_list)}},"{{$showTitle}}")'>Print</a>
    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
            
            @foreach($floor_list as $k=>$floor)
                <div class="search-result-div col-xs-12 col-sm-3 widget-container-col ui-sortable">
                    <div class="widget-box widget-color-green2 light-border ui-sortable-handle" id="widget-box-6">
                        <div class="widget-header">
                            <a href="#" class="white search_section" data-floor="{{ $floor->hr_floor_id }}">
                                <h5 class="widget-title smaller">  {{ $floor->hr_floor_name }} </h5></a>
                        </div>

                        <div class="widget-body">
                            <div class="widget-main padding-6">
                                <div class="profile-info-row search_section" data-floor="{{ $floor->hr_floor_id }}">
                                    <div class="profile-info-name"> Total Section </div>

                                    <div class="profile-info-value">
                                        <span>{{ $floor->getSectionCount($request1['area'],$request1['department']) }}</span>
                                    </div>
                                </div>
                                {{-- <a href="#" class="search_emp" data-floor="{{ $floor->hr_floor_id }}">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Total Employee </div>

                                        <div class="profile-info-value">
                                            <span>{{ count($floor->getFloorWiseEmp($request1['unit'],$request1['area'],$request1['department'],$floor->hr_floor_id)) }}</span>
                                        </div>
                                    </div>
                                </a> --}}
                                <a href="#" class="search_emp" data-floor="{{ $floor->hr_floor_id }}" data-attstatus="absent">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Total Absent </div>

                                        <div class="profile-info-value after-load" id="{{ $floor->hr_floor_id }}_tAbsent">
                                            <span>0</span>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="search_emp" data-floor="{{ $floor->hr_floor_id }}" data-attstatus="present">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Total Present </div>

                                        <div class="profile-info-value after-load" id="{{ $floor->hr_floor_id }}_tPresent">
                                            <span>0</span>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="search_emp" data-floor="{{ $floor->hr_floor_id }}" data-attstatus="leave">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Total Leave </div>

                                        <div class="profile-info-value after-load" id="{{ $floor->hr_floor_id }}_tLeave">
                                            <span>0</span>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="search_emp" data-floor="{{ $floor->hr_floor_id }}" data-attstatus="late">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Total Late </div>

                                        <div class="profile-info-value after-load" id="{{ $floor->hr_floor_id }}_tLate">
                                            <span>0</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
				
        </div>
    </div>
</div>
<div id="printOutputSection" style="display: none;"></div>
@php
    $rangeFrom = date('Y-m-d');
    $rangeTo = date('Y-m-d');
    if($request1['type'] == 'date'){
        $rangeFrom = $request1['date'];
        $rangeTo = $request1['date'];
    }
@endphp
<script>
    $(document).ready(function(){
        $('.after-load span').html('<i class="ace-icon fa fa-spinner fa-spin orange bigger-30"></i>');
    });
    var response = [];
    var floor_list = <?php echo json_encode($floor_list); ?>;
    jQuery.each(floor_list, function(index, floor) {
        $.ajax({
            url: '{{ url('hr/search/hr_att_search_emp_count') }}',
            type: 'get',
            data: {
                unit: {{ $request1['unit'] }},
                area: {{ $request1['area'] }},
                department: {{ $request1['department'] }},
                floor: floor.hr_floor_id,
                rangeFrom: '{{ $rangeFrom }}',
                rangeTo: '{{ $rangeTo }}'
            },
            success: function(res) {
                response[floor.hr_floor_id] = res;
                $('#'+floor.hr_floor_id+'_tAbsent span').html(res.absent);
                $('#'+floor.hr_floor_id+'_tPresent span').html(res.present);
                $('#'+floor.hr_floor_id+'_tLeave span').html(res.leave);
                $('#'+floor.hr_floor_id+'_tLate span').html(res.late);
                // console.log(res);
            },
            error: function() {
                console.log('error occored');
            }
        })
    });


    function printDiv(result,pagetitle) {
        $.ajax({
            url: '{{url('hr/search/hr_att_search_floorPrint')}}',
            type: 'get',
            data: {
                data: result,
                unitName: '{{ $unit->hr_unit_name }}',
                areaName: '{{ $area->hr_area_name }}',
                departmentName: '{{ $department->hr_department_name }}',
                title: pagetitle,
                response: response
            },
            success: function(data) {
                $('#printOutputSection').html(data);
                var divToPrint=document.getElementById('printOutputSection');
                var newWin=window.open('','Print-Window');
                newWin.document.open();
                newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
                newWin.document.close();
                setTimeout(function(){newWin.close();},10);
            }
        });
    }
</script>