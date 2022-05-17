@extends('hr.layout')
@section('title', 'HR Dashboard')
@section('main-content')
@push('css')
    <style>
    .future-services { margin-bottom: 45px; }
    .iq-fancy-box { box-shadow: 0 0px 90px 0 rgba(0, 0, 0, .04); position: relative; top: 0; -webkit-transition: all 0.5s ease-out 0s; -moz-transition: all 0.5s ease-out 0s; -ms-transition: all 0.5s ease-out 0s; -o-transition: all 0.5s ease-out 0s; transition: all 0.5s ease-out 0s; padding: 50px 30px; overflow: hidden; position: relative; margin-bottom: 30px; -webkit-border-radius: 0; -moz-border-radius: 0; border-radius: 0; }
    .iq-fancy-box .iq-icon { font-size: 36px; border-radius: 90px; display: inline-block; height: 86px; width: 86px; margin-bottom: 15px; line-height: 86px; text-align: center; color: #ffffff; background: #089bab; -webkit-transition: all .5s ease-out 0s; -moz-transition: all .5s ease-out 0s; -ms-transition: all .5s ease-out 0s; -o-transition: all .5s ease-out 0s; transition: all .5s ease-out 0s; }
    .iq-fancy-box:hover { box-shadow: 0 44px 98px 0 rgba(0, 0, 0, .12); top: -8px; }
    .iq-fancy-box .fancy-content h4 { z-index: 9; position: relative; padding-bottom: 5px }
    .iq-fancy-box .fancy-content p { margin-bottom: 0 }
    .iq-fancy-box .future-img i { font-size: 45px; color: #089bab; }
    .feature-effect-box { box-shadow: 0px 7px 22px 0px rgba(0, 0, 0, 0.06); padding: 10px 15px; margin-bottom: 15px; position: relative; top: 0; -webkit-transition: all 0.3s ease-in-out; -o-transition: all 0.3s ease-in-out; -ms-transition: all 0.3s ease-in-out; -webkit-transition: all 0.3s ease-in-out; }
    .feature-effect-box:hover { top: -10px }
    .feature-effect-box .feature-i { margin-right: 10px; width: 45px; padding: 10px 10px; padding-bottom: 10px; border-radius: 50%; display: inline-block;}
    .feature-effect-box .feature-i i{ font-size: 25px;}
    .feature-effect-box .feature-icon { display: inline-block; }
    .title-box { margin-bottom: 30px;}
    .iq-timeline li {
        margin-left: 15px;
        position: relative;
        padding: 20px 15px 0 5px;
        list-style-type: none;
    }
    .position-bottom{
        bottom: 0px !important;
    }
    .apexcharts-toolbar{
        display: none !important;
    }
    .link-summery-head{
        margin: 10px 0px;  
    }
    .top-summery-box{
        padding: 10px 6px;
        border: 1px solid #ccc;
        border-radius: 5px;
        text-align: center;
        box-shadow: 1px 1px 2px 0px #ccc;
    }
    .top-summery-head{
        margin: 5px 0px;
    }
    .col-panel-scroll, .col-panel-scroll1, .col-panel-scroll2{
        height: calc(100vh - 120px);
    }
    
    </style>
@endpush
    @php $user = auth()->user(); @endphp
   
    <div class="row">
      <div class="col-sm-10">
          <div class="row">
              <div class="col-sm-6">
                  <div class="hr_relative_section col-panel-scroll">
                    @if($user->can('Today Attendance') || $user->hasRole('Super Admin'))
                    <div class="card mb-3">
                        <div class="card-body">
                            <h4 class="card-title">Today's Attendance statistics</h4>
                            {{-- <b>Unit Wise Absenteeism</b>
                            <div class="row">
                                <div class="col-sm-3">
                                    <a href="#">
                                        <div class="top-summery-head top-summery-box">
                                            <h5>MBM </h5>
                                            <h5><b>0</b>%</h5>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-sm-3">
                                    <a href="#">
                                        <div class="top-summery-head top-summery-box">
                                            <h5>CEIL </h5>
                                            <h5><b>0</b>%</h5>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-sm-3">
                                    <a href="#">
                                        <div class="top-summery-head top-summery-box">
                                            <h5>AQL </h5>
                                            <h5><b>0</b>%</h5>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-sm-3">
                                    <a href="#">
                                        <div class="top-summery-head top-summery-box">
                                            <h5>CEW </h5>
                                            <h5><b>0</b>%</h5>
                                        </div>
                                    </a>
                                </div>
                            </div> --}}
                            @if($today_att_chart['present'] > 0 )
                               <div id="today-att" style="width: 100%; height: 400px;"></div>
                            @else
                                <p class=" text-danger p-5">Today's Attendance has not been uploaded! Please make sure this step's should done before attendance file upload.</p>

                                <ul class="iq-timeline" style="width:200px;margin:0 auto;">
                                  <li>
                                     <div class="timeline-dots"></div>
                                     <h6 class="mb-1"><a target="_blank" href="{{ url('hr/operation/shift')}}" >Add Shift</a></h6>
                                  </li>
                                  <li>
                                     <div class="timeline-dots border-success"></div>
                                     <h6 class="mb-1"><a target="_blank" href="{{ url('hr/operation/shift_assign')}}" >Shift Assign</a></h6>
                                  </li>
                                  <li>
                                     <div class="timeline-dots border-primary"></div>
                                     <h6 class="mb-1"><a target="_blank" href="{{ url('hr/operation/holiday-roster')}}"  style="width:200px;">Holiday Roster</a></h6>
                                  </li>
                                  <li>
                                     <div class="timeline-dots border-warning"></div>
                                     <h6 class="mb-1"><a target="_blank" href="{{ url('hr/timeattendance/attendance-upload')}}" >Upload Attendance</a></h6>
                                  </li>
                                </ul>
                            @endif
                        </div>
                    </div>
                    @endif
                    @if($user->can('Monthly OT') || $user->hasRole('Super Admin')) 
                    <div class="card mb-3">
                        <div class="card-body">
                           <h4 class="card-title">Monthly Overtime Statistics</h4>
                           <div class="row">
                               <div class="col-sm-9">
                                   <div id="ot-comparison"></div>
                               </div>
                               <div class="col-sm-3 pl-0" style="padding-right:20px;">
                                    <a href="#">
                                        <div class="link-summery-head border-0 text-center" style="box-shadow:none;">
                                            <h5>This Month statistics </h5>
                                        </div>
                                    </a>
                                    <a href="#">
                                        <div class="link-summery-head top-summery-box">
                                            <h5>Max OT Hour</h5>
                                            <h5><b>0</b></h5>
                                            <p class="text-left">Employee's: 0</p>
                                        </div>
                                    </a>
                                    <a href="#">
                                        <div class="link-summery-head top-summery-box">
                                            <h5 data-toggle="tooltip" data-placement="top" title="Average OT hour">Avg. OT Hour</h5>
                                            <h5><b>0</b></h5>
                                            <p class="text-left">Employee's: 0</p>
                                        </div>
                                    </a>
                                    <a href="#">
                                        <div class="link-summery-head top-summery-box">
                                            <h5 data-toggle="tooltip" data-placement="top" title="More than 6 hour">
                                                OT Performer 
                                            </h5>
                                            
                                            <h5><b>0</b></h5>
                                            <p class="text-left">Employee's: 0</p>
                                        </div>
                                    </a>
                                    
                               </div>
                           </div>
                           
                        </div>
                    </div>
                    @endif
                    
                    {{-- <div class="card mb-3">
                        <div class="card-body">
                           <h4 class="card-title">Monthly Leave (Month of -)</h4>
                           <p class="card-text">Show Graph</p>
                           
                        </div>
                    </div> --}}
                    <div class="card mb-3">
                        <div class="card-body">
                           <h4 class="card-title">Shift Wise Employee</h4>
                           <div id="high-columnndbar-chart"></div>
                           
                        </div>
                    </div>
                  </div>
              </div>
              <div class="col-sm-6">
                  <div class="account_relative_section col-panel-scroll1">
                    <div class="card mb-3">
                        <div class="card-body">
                           <h4 class="card-title">Today's MMR</h4>
                           <div id="apex-bar"></div>
                        </div>
                    </div>
                    
                    @if($user->can('Monthly Salary Comparison') || $user->hasRole('Super Admin'))
                    <div class="card mb-3">
                        <div class="card-body">
                           <h4 class="card-title">Monthly Salary <span class="pull-right" style="font-size:14px; margin-top:10px;">This Month statistics</span></h4>
                           <div class="row">
                               <div class="col-sm-9">
                                   <div id="monthly-salary-chart"></div>
                               </div>
                               <div class="col-sm-3 pl-0" style="padding-right:20px;">
                                    
                                    <a href="#">
                                        <div class="link-summery-head top-summery-box">
                                            <h6>Approximate Salary </h6>
                                            <h5><b>0</b></h5>
                                        </div>
                                    </a>
                                    <a href="#">
                                        <div class="link-summery-head top-summery-box">
                                            <h6>Current OT Hour </h6>
                                            <h5><b>0</b></h5>
                                        </div>
                                    </a>
                                    <a href="#">
                                        <div class="link-summery-head top-summery-box">
                                            <h6>Approximate OT Hour </h6>
                                            <h5><b>0</b></h5>
                                        </div>
                                    </a>
                                    {{-- <a href="#">
                                        <div class="link-summery-head top-summery-box">
                                            <h5>Current OT Amount </h5>
                                            <h5><b>0</b></h5>
                                        </div>
                                    </a> --}}
                                    <a href="#">
                                        <div class="link-summery-head top-summery-box">
                                            <h6>Approximate OT Amount </h6>
                                            <h5><b>0</b></h5>
                                        </div>
                                    </a>
                               </div>
                           </div>
                           
                        </div>
                    </div>
                    @endif
                    <div class="card mb-3">
                        <div class="card-body">
                           <h4 class="card-title">Turn Over Statistic <span class="pull-right" style="font-size:14px; margin-top:10px;">This Month statistics</span></h4>
                           
                           <div class="row">
                               <div class="col-sm-9">
                                   <div id="apex-column"></div>
                               </div>
                               <div class="col-sm-3 pl-0" style="padding-right:20px;">
                                    <a href="#">
                                        <div class="link-summery-head text-center border-0" style="box-shadow:none;">
                                            <h5>Management </h5>
                                        </div>
                                    </a>
                                    <a href="#">
                                        <div class="link-summery-head top-summery-box">
                                            
                                            <p class="text-left text-black">Join: <b>0</b></p>
                                            <p class="text-left text-black">Left & Reign: <b>0</b></p>
                                            <h5 class="text-center">Salary Ratio</h5>
                                            <h5 class="text-center"><b>0</b></h5>
                                        </div>
                                    </a>
                                    <a href="#">
                                        <div class="link-summery-head text-center border-0" style="box-shadow:none;">
                                            <h5>Staff </h5>
                                        </div>
                                    </a>
                                    <a href="#">
                                        <div class="link-summery-head top-summery-box">
                                            
                                            <p class="text-left text-black">Join: <b>0</b></p>
                                            <p class="text-left text-black">Left & Reign: <b>0</b></p>
                                            <h5 class="text-center">Salary Ratio</h5>
                                            <h5 class="text-center"><b>0</b></h5>
                                        </div>
                                    </a>
                               </div>
                           </div>
                        </div>
                    </div>
                    
                    {{-- <div class="card mb-3">
                        <div class="card-body">
                           <h4 class="card-title">Maternity Statistic</h4>
                           <div id="high-area-chart"></div>
                           
                        </div>
                    </div> --}}
                    {{-- <div class="card mb-3">
                        <div class="card-body">
                           <h4 class="card-title">Monthly Bill Announce</h4>
                           <p class="card-text">Show Graph</p>
                           
                        </div>
                    </div> --}}

                  </div>
              </div>
          </div>
      </div>
      <div class="col-sm-2 pl-0">
            <aside class="bg-white col-panel-scroll2">
                
                <a href="{{ url('hr/reports/daily-attendance-activity') }}">
                  <div class="feature-effect-box wow fadeInUp" data-wow-duration="0.4s">
                      <div class="feature-i iq-bg-success">
                        <i class="lab la-battle-net"></i>
                      </div>
                      <div class="feature-icon">
                          <h5>Today's MMR </h5>
                          <h5><b>0</b></h5>
                      </div>
                  </div>
                </a>
                <a href="{{ url('hr/reports/daily-attendance-activity') }}">
                  <div class="feature-effect-box wow fadeInUp" data-wow-duration="0.4s">
                      <div class="feature-i iq-bg-success">
                        <i class="las la-users"></i>
                      </div>
                      <div class="feature-icon">
                          <h5>Active </h5>
                          <h5><b>0</b></h5>
                      </div>
                  </div>
                </a>
                <a href="{{ url('hr/reports/daily-attendance-activity') }}">
                  <div class="feature-effect-box wow fadeInUp" data-wow-duration="0.4s">
                      <div class="feature-i iq-bg-success">
                        <i class="las la-male"></i>
                      </div>
                      <div class="feature-icon">
                          <h5>Male </h5>
                          <h5><b>0</b></h5>
                      </div>
                  </div>
                </a>
                <a href="{{ url('hr/reports/daily-attendance-activity') }}">
                  <div class="feature-effect-box wow fadeInUp" data-wow-duration="0.4s">
                      <div class="feature-i iq-bg-success">
                        <i class="las la-female"></i>
                      </div>
                      <div class="feature-icon">
                          <h5>Female </h5>
                          <h5><b>0</b></h5>
                      </div>
                  </div>
                </a>
                <a href="{{ url('hr/reports/daily-attendance-activity') }}">
                  <div class="feature-effect-box wow fadeInUp" data-wow-duration="0.4s">
                      <div class="feature-i iq-bg-success">
                        <i class="las la-user-clock"></i>
                      </div>
                      <div class="feature-icon">
                          <h5>OT  </h5>
                          <h5><b>0</b></h5>
                      </div>
                  </div>
                </a>
                <a href="{{ url('hr/reports/daily-attendance-activity') }}">
                  <div class="feature-effect-box wow fadeInUp" data-wow-duration="0.4s">
                      <div class="feature-i iq-bg-success">
                        <i class="las la-user-times"></i>
                      </div>
                      <div class="feature-icon">
                          <h5>Non-OT  </h5>
                          <h5><b>0</b></h5>
                      </div>
                  </div>
                </a>
                <a href="{{ url('hr/reports/daily-attendance-activity') }}">
                  <div class="feature-effect-box wow fadeInUp" data-wow-duration="0.4s">
                      <div class="feature-i iq-bg-success">
                        <i class="las la-calendar-day"></i>
                      </div>
                      <div class="feature-icon">
                          <h5>Today Join </h5>
                          <h5><b>0</b></h5>
                      </div>
                  </div>
                </a>
            </aside>
      </div>
   </div>
   @push('js')
      <script src="{{ asset('assets/js/apexcharts.js') }}"></script>
      <script src="{{ asset('assets/js/lottie.js') }}"></script>
      <!-- am core JavaScript -->
      <script src="{{ asset('assets/js/core.js') }}"></script>
      <!-- am charts JavaScript -->
      <script src="{{ asset('assets/js/charts.js') }}"></script>
      
      <script src="{{ asset('assets/js/animated.js') }}"></script>
      <script src="{{ asset('assets/js/highcharts.js')}}"></script>
      <!-- Chart Custom JavaScript -->
      

      <script type="text/javascript">

        @if($user->can('Monthly Salary Comparison') || $user->hasRole('Super Admin')) 
        jQuery("#monthly-salary-chart").length && am4core.ready(function() {
            var options = {
                  series: [{
                  name: 'Salary (Lakh)',
                  data: @php echo json_encode(array_values($salary_chart['salary'])); @endphp
                }, {
                  name: 'OT Payment (Lakh)',
                  data: @php echo json_encode(array_values($salary_chart['ot'])); @endphp
                }],
                colors: ['#089bab','#FC9F5B'],
                  chart: {
                  type: 'bar',
                  height: 350,
                  stacked: true,
                  toolbar: {
                    show: true
                  },
                  zoom: {
                    enabled: true
                  }
                },
                responsive: [{
                  breakpoint: 480,
                  options: {
                    legend: {
                      position: 'bottom',
                      offsetX: -10,
                      offsetY: 0
                    }
                  }
                }],
                plotOptions: {
                  bar: {
                    horizontal: false,
                  },
                },
                xaxis: {
                  categories: @php echo json_encode(array_values($salary_chart['category'])); @endphp,
                },
                /*yaxis:{
                    labels: {
                        formatter: function() {
                            return this.value / 1000 + 'K'; // clean, unformatted number for year
                        }
                    },
                },*/
                legend: {
                  position: 'bottom', 
                  offsetY: 40
                },
                fill: {
                  opacity: 1
                }
                };

                var chart = new ApexCharts(document.querySelector("#monthly-salary-chart"), options);
                chart.render();
        })
        @endif

        @if($user->can('Monthly OT') || $user->hasRole('Super Admin')) 
        if (jQuery('#ot-comparison').length) {
            Highcharts.chart('ot-comparison', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: ''
                },
                xAxis: [{
                    categories: @php echo json_encode(array_keys($ot_chart)); @endphp,
                    crosshair: true
                }],
                yAxis: [{ // Primary yAxis
                    labels: {
                        format: '{value} H',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },

                    title: {
                        text: 'Overtime',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    }
                }],
                tooltip: {
                    shared: true
                },
                legend: {
                    layout: 'vertical',
                    align: 'left',
                    x: 120,
                    verticalAlign: 'top',
                    y: 100,
                    floating: true,
                    backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || // theme
                        'rgba(255,255,255,0.25)'
                },
                series: [{
                    name: 'Overtime',
                    type: 'area',
                    data: @php echo json_encode(array_values($ot_chart)); @endphp,
                    color: '#FC9F5B',
                    tooltip: {
                        valueSuffix: ' Hour'
                    }
                }]
            });
        }
        @endif
    
        
        @if($user->can('Today Attendance') || $user->hasRole('Super Admin')) 
          if (jQuery('#today-att').length) {
             am4core.ready(function() {

                 // Themes begin
                 am4core.useTheme(am4themes_animated);
                 // Themes end

                 var chart = am4core.create("today-att", am4charts.PieChart3D);
                 chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

                 chart.legend = new am4charts.Legend();

                 chart.data = [ {
                     title: "Present",
                     employee: {{($today_att_chart['present']??0)}}
                 }, {
                     title: "Leave",
                     employee: {{$today_att_chart['leave']??0}}
                 }, {
                     title: "Absent",
                     employee: {{$today_att_chart['absent']??0}}
                 },{
                     title: "Holiday",
                     employee: {{$today_att_chart['holiday']??0}}
                 }];

                 var series = chart.series.push(new am4charts.PieSeries3D());
                 series.colors.list = [am4core.color("#089bab"), am4core.color("#208207"), am4core.color("#f45b5b"),
                     am4core.color("#FC9F5B")
                 ];
                 series.dataFields.value = "employee";
                 series.dataFields.category = "title";

             }); // end am4core.ready()
             am4core.getInteraction().body.events.on("hit", function(ev) {
                  // $(".apexcharts-bar-area").attr('data-name', 'mmr');
                  console.log(ev)
             });
          }
        @endif
        if (jQuery('#apex-bar').length) {
            var options = {
                
                chart: {
                    height: 180,
                    type: 'bar',
                    events: {
                      click: function(event, chartContext, config) {
                        
                      }
                    }
                },

                colors: ['#089bab'],
                plotOptions: {
                    bar: {
                        horizontal: true,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                series: [{
                    data: [2.5, 2.8, 3, 5.2]
                }],
                xaxis: {
                    categories: ['MBM', 'CEIL', 'AQL', 'CEW'],
                }
                

            }

            var chart = new ApexCharts(
                document.querySelector("#apex-bar"),
                options
            );
            
            chart.render();
        }
        if (jQuery('#apex-column').length) {
            var options = {
                chart: {
                    height: 350,
                    type: 'bar',
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                colors: ['#089bab', '#FC9F5B', '#e64141'],
                series: [{
                    name: 'Join',
                    data: [44, 55, 57, 56]
                }, {
                    name: 'Ratio',
                    data: [76, 85, 101, 98]
                }, {
                    name: 'Left',
                    data: [35, 41, 36, 26]
                }],
                xaxis: {
                    categories: ['Mar', 'Apr', 'May', 'Jun'],
                },
                yaxis: {
                    title: {
                        text: '$ (thousands)'
                    }
                },
                fill: {
                    opacity: 1

                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "$ " + val + " thousands"
                        }
                    }
                }
            }

            var chart = new ApexCharts(
                document.querySelector("#apex-column"),
                options
            );

            chart.render();
        }
        if (jQuery('#high-columnndbar-chart').length) {
            Highcharts.chart('high-columnndbar-chart', {
                chart: {
                    type: 'bar'
                },
                title: {
                    text: ''
                },
                subtitle: {
                    text: '</a>'
                },
                xAxis: {
                    categories: ['Day Early', 'Day', 'Day Mid', 'Night', 'Night 2'],
                    title: {
                        text: null
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: '',
                        align: 'high'
                    },
                    labels: {
                        overflow: 'justify'
                    }
                },
                tooltip: {
                    valueSuffix: ' millions'
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'top',
                    x: -40,
                    y: 80,
                    floating: true,
                    borderWidth: 1,
                    backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
                    shadow: true
                },
                credits: {
                    enabled: false
                },
                series: [{
                    name: 'MBM',
                    data: [133, 156, 947, 408, 6],
                    color: '#089bab'
                }, {
                    name: 'CEIL',
                    data: [814, 841, 3714, 727, 31],
                    color: '#e64141'
                }, {
                    name: 'AQL',
                    data: [1216, 1001, 4436, 738, 40],
                    color: '#FC9F5B'
                }, {
                    name: 'CEW',
                    data: [332, 322, 123, 222, 60],
                    color: '#28a745'
                }]
            });
        }

        $(".apexcharts-bar-area").dblclick(function() {
            console.log('dbclick')
        })

      </script>
   @endpush 
@endsection

