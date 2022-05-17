@extends('hr.layout')
@section('title', 'Holiday Planner Calendar')
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
    .fc-header-toolbar{
        display: none !important;
    }
</style>
<link rel="stylesheet" href="{{ asset('assets/css/fullcalendar2.min.css') }}">
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
                    
                    <a href='{{ url("hr/operation/holiday-planner?year_month=$month")}}' class=" btn btn-sm  btn-success"> <i class="fa fa-list"></i> List view</a>
                </li>
            </ul><!-- /.breadcrumb -->
 
        </div>
        <div class="page-content"> 
            <div class="panel">
                <div class="panel-body text-center p-2">
                    @foreach(array_reverse($months) as $k => $i)
                        <a href="{{url('hr/operation/holiday-planner?year_month='.$k.'&view=calendar')}}" class="nav-year @if($k== $month) bg-primary text-white @endif" data-toggle="tooltip" data-placement="top" title="" data-original-title="Holiday list of {{$i}}" >
                            {{$i}}
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="panel panel-info"> 
                <div class="panel-body">
                    <ul class="color-bar">
                        <li><span class="color-label bg-warning"></span><span class="lib-label"> Weekend </span></li>
                        <li><span class="color-label bg-primary"></span><span class="lib-label"> OT</span></li>
                        <li><span class="color-label bg-success"></span><span class="lib-label"> General</span></li>
                    </ul>
                    <div class="row justify-content-sm-center">
                        @foreach($unitHoliday as $key=> $holiday)
                        <div class="col-sm-6">
                            <h4 class="text-center mt-4">{{ $unit[$key] }} - {{ date('F Y', strtotime($month)) }}</h4>
                            <div id="event-calendar-{{$key}}"></div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/fullcalendar2.min.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
    var i = 0;
    var subArrayDate = new Array();
    @foreach($unitHoliday as $k=> $data)
    var unitid = '{{ $k }}'
    $('#event-calendar-'+unitid).fullCalendar({
        defaultDate: '{{ $defaultDate }}', //'2014-11-10',
        header: {
            left: '',
            center: '',
            right: ''
        },
        viewRender: function(view, element) {
          view.title = 'Your Custom Title';
        },
        defaultView: 'month',
        events: [
            @foreach($data as $d)
                {
                    title : '{{ $d->hr_yhp_comments }}',
                    description : '{{ $d->hr_yhp_comments }}',
                    start : '{{ $d->hr_yhp_dates_of_holidays }}',
                    end   : '{{ $d->hr_yhp_dates_of_holidays }}',
                    className: '{{ $d->hr_yhp_open_status }}'
                    
                },
            @endforeach
        ],
        eventRender: function(info, element) {
            $(element).tooltip({
                title: info.description,
                container: 'body',
                delay: { "show": 50, "hide": 50 }
            });
            // element.popover({
            //   title: info.title,
            //   content: info.description,
            //   trigger: 'hover',
            //   placement: 'top',
            //   container: 'body'
            // });
            if(info.className == 0) {
                element.css('background-color', '#fc9e5b');
            }else if(info.className == 1){
                element.css('background-color', '#31c02c');
            }else if(info.className == 2){
                element.css('background-color', '#089bab');
            }
        },
        selectable: false,
        
    });
    @endforeach
});

</script>
@endpush
@endsection