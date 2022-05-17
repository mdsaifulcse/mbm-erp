@extends('user.layout')
@section('title', 'User Dashboard')
@section('main-content')
@push('css')
  
  <style>
    .avatar-130 {
      border-radius: 10% !important;
      object-fit: contain !important;
    }
    .future-services { margin-bottom: 45px; }
    .iq-fancy-box { box-shadow: 0 0px 90px 0 rgba(0, 0, 0, .04); position: relative; top: 0; -webkit-transition: all 0.5s ease-out 0s; -moz-transition: all 0.5s ease-out 0s; -ms-transition: all 0.5s ease-out 0s; -o-transition: all 0.5s ease-out 0s; transition: all 0.5s ease-out 0s; padding: 50px 30px; overflow: hidden; position: relative; margin-bottom: 30px; -webkit-border-radius: 0; -moz-border-radius: 0; border-radius: 0; }
    .iq-fancy-box .iq-icon { font-size: 36px; border-radius: 90px; display: inline-block; height: 86px; width: 86px; margin-bottom: 15px; line-height: 86px; text-align: center; color: #ffffff; background: #089bab; -webkit-transition: all .5s ease-out 0s; -moz-transition: all .5s ease-out 0s; -ms-transition: all .5s ease-out 0s; -o-transition: all .5s ease-out 0s; transition: all .5s ease-out 0s; }
    .iq-fancy-box:hover { box-shadow: 0 44px 98px 0 rgba(0, 0, 0, .12); top: -8px; }
    .iq-fancy-box .fancy-content h4 { z-index: 9; position: relative; padding-bottom: 5px }
    .iq-fancy-box .fancy-content p { margin-bottom: 0 }
    .iq-fancy-box .future-img i { font-size: 45px; color: #089bab; }
    .feature-effect-box { box-shadow: 0px 7px 22px 0px rgba(0, 0, 0, 0.06); padding: 10px 15px; margin-bottom: 30px; position: relative; top: 0; -webkit-transition: all 0.3s ease-in-out; -o-transition: all 0.3s ease-in-out; -ms-transition: all 0.3s ease-in-out; -webkit-transition: all 0.3s ease-in-out; }
    .feature-effect-box:hover { top: -10px }
    .feature-effect-box .feature-i { margin-right: 10px; width: 60px; padding: 18px 18px; padding-bottom: 12px;border-radius: 50%;  display: inline-block;}
    .feature-effect-box .feature-i i{ font-size: 25px;}
    .feature-effect-box .feature-icon { display: inline-block; }
    .title-box { margin-bottom: 30px;}
  </style>
@endpush
   @php $user = auth()->user(); @endphp
   <div class="row">
      <div class="col-lg-4 row m-0 p-0">
         <div class="col-sm-12 mb-3">
            <div class="iq-card iq-card-block iq-card-stretch iq-card-height iq-user-profile-block" style="height: 75%;">
               <div class="iq-card-body">
                  <div class="user-details-block">
                     <div class="user-profile text-center">
                        @if($user->employee)
                        <img src='{{ $user->employee['as_pic'] != null?asset($user->employee['as_pic'] ):($user->employee['as_gender'] == 'Female'?asset('assets/images/user/1.jpg'):asset('assets/images/user/09.jpg')) }}' class="avatar-130 img-fluid" alt="{{ $user->name }}" onError='this.onerror=null;this.src="{{ ($user->employee['as_gender'] == 'Female'?asset('assets/images/user/1.jpg'):asset('assets/images/user/09.jpg')) }}";'>
                        @else
                           <img class="avatar-130 img-fluid" src="{{ asset('assets/images/user/09.jpg') }} ">
                         @endif
                     </div>
                     <div class="text-center mt-3">
                        <h4><b>{{ $user->name }}</b></h4>
                        @if($user->employee)
                        <p class="mb-0">
                           {{ $user->employee->designation['hr_designation_name']??''}}</p>
                        <p class="mb-0">Joined {{ $user->employee['as_doj']}}</p>
                        @else
                           <p class="mb-0">Joined in ERP {{ $user->created_at}}</p>
                        @endif

                        {{-- @if($user->employee)
                        <p class="mb-0">
                           {{ $user->employee->designation['hr_designation_name']??''}}</p>
                        <p class="mb-0">Joined {{ $user->employee['as_doj']->diffForHumans() }}</p>
                        @else
                           <p class="mb-0">Joined in ERP {{ $user->created_at->diffForHumans() }}</p>
                        @endif --}}
                     </div>
                     @php $last_login = $user->lastlogin(); @endphp
                     {{-- @if($last_login)
                     <ul class="doctoe-sedual d-flex align-items-center justify-content-between p-0 mt-4 mb-0">
                        <li class="text-center">
                           <h6 class="text-primary">Last Logged In </h6>
                           <span>{{$last_login->login_at->diffForHumans() }}</span>
                        </li>
                     </ul>
                     @endif --}}
                  </div>
               </div>
            </div>
         </div>
         <div class="col-sm-12">
            <div class="panel iq-card-block iq-card-stretch iq-card-height" style="height: calc(100% - 10px);">
               <div class="panel-heading d-flex justify-content-between">
                     <h6 >My Logs</h6>
               </div>
               <div class="panel-body">
                  <ul class="iq-timeline">
                     @if(count($user->logs) > 0)
                        @foreach($user->logs as $log)
                        <li>
                           <div class="timeline-dots"></div>
                           <h6 class="float-left mb-1">{{$log->log_message??''}}</h6>
                           <small class="float-right mt-1">{{date('d F, Y',strtotime($log->created_at))}}</small>
                           @if($log->log_row_no != 0)
                           <div class="d-inline-block w-100">
                              <p>at row no.  {{$log->log_row_no}} </p>
                           </div>
                           @endif
                        </li>
                        @endforeach
                    @else
                    <center>No logs found </center>
                    @endif
                  </ul>
               </div>
            </div>
         </div>
         @if($user->hasRole('Super Admin') || $user->can(['Job Card', 'Attendance Report', 'Attendance Consecutive Report', 'Attendance Summary Report', 'Salary Report', 'Employee List']))
         <div class="col-sm-12">
            <div class="panel iq-card-block iq-card-stretch iq-card-height" style="height: calc(100% - 10px);">
               <div class="panel-heading d-flex justify-content-between">
                     <h6 >My Leave - {{ date('Y') }}</h6>
               </div>
               <div class="panel-body">
                 <ul class="speciality-list m-0 p-0">
                    @if(count($leaves) >0 )
                       @foreach($leaves as $key => $lv)
                      <li class="d-flex mb-4 align-items-center">
                         <div class="user-img img-fluid">
                          @if($lv->leave_status==1)
                               <a href="#" class="iq-bg-success">
                                <i class="las f-18 la-check-circle"></i>
                             </a>
                           @else
                               <a href="#" class="iq-bg-danger">
                                <i class="las f-18 la-times-circle"></i>
                             </a>
                           @endif
                          
                          </div>
                         <div class="media-support-info ml-3">
                            <h6>{{$lv->leave_type}} Leave</h6>
                            <p class="mb-0">
                            @if($lv->leave_from != $lv->leave_to)
                                {{$lv->leave_from->format('d M, Y')}} - {{$lv->leave_to->format('d M, Y')}}
                             @else
                                {{$lv->leave_from->format('d M, Y')}}
                             @endif
                             </p>
                         </div>
                      </li>
                      @endforeach
                    @else
                       <li class="d-flex mb-4 align-items-center">
                         <div class="user-img img-fluid">
                             <a href="#" class="iq-bg-danger">
                                <i class="las f-18 la-times-circle"></i>
                             </a>
                          </div>
                         <div class="media-support-info ml-3">
                            <h6>No leave record!</h6>
                            <p class="mb-0">
                             ------------
                             </p>
                         </div>
                      </li>
                    @endif
                 </ul>
              </div>
            </div>
         </div>
         @endif
      </div>
      <div class="col-lg-8 pl-0">
         <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
            <div class="iq-card-body pb-0">
               <div class="row"> 
                    <div class="col-sm-12">
                        <div class="iq-card">
                            <div class="iq-card-body bg-primary rounded pt-2 pb-2 pr-2">
                               <div class="d-flex align-items-center justify-content-between">
                                  <p class="mb-0">Welcome to MBM ERP, Stay connected!</p>
                                  <div class="rounded iq-card-icon bg-white">
                                     <img src="{{ asset('assets/images/page-img/37.png') }}" class="img-fluid" alt="icon">
                                  </div>
                               </div>
                            </div>
                        </div>
                    </div> 
                    @if($user->hasRole('Super Admin') || $user->can(['Job Card', 'Attendance Report', 'Attendance Consecutive Report', 'Attendance Summary Report', 'Salary Report', 'Employee List']))  
                    <div class="col-lg-12">
                     <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                           <div class="iq-header-title">
                              <h4 class="card-title text-primary border-left-heading">HR Shortcut Link </h4>
                           </div>
                        </div>
                        <div class="iq-card-body p-0">
                          <div class="hr-section">
                            <section id="features">
                              <div class="container-fluid p-0">
                                  <div class="row">
                                    @if($user->can('Attendance Summary Report'))
                                    <div class="col-md-4 pr-0">
                                        <a href="{{ url('hr/reports/attendance_summary_report') }}">
                                          <div class="feature-effect-box wow fadeInUp" data-wow-duration="0.8s">
                                              <div class="feature-i iq-bg-info">
                                                <i class="las la-fingerprint"></i>
                                              </div>
                                              <div class="feature-icon">
                                                  <h5>Attendance Report</h5>
                                              </div>
                                          </div>
                                        </a>
                                      </div>
                                      @endif
                                      @if($user->can('Attendance Report'))
                                      <div class="col-md-4 pr-0">
                                        <a href="{{ url('hr/reports/daily-attendance-activity') }}">
                                          <div class="feature-effect-box wow fadeInUp" data-wow-duration="0.4s">
                                              <div class="feature-i iq-bg-success">
                                                <i class="lar la-chart-bar"></i>
                                              </div>
                                              <div class="feature-icon">
                                                  <h5>Daily Report</h5>
                                              </div>
                                          </div>
                                        </a>
                                      </div>
                                      @endif
                                      @if($user->can('Attendance Summary Report'))
                                      <div class="col-md-4 pr-0">
                                        <a href="{{ url('hr/reports/summary') }}">
                                          <div class="feature-effect-box wow fadeInUp" data-wow-duration="0.6s">
                                              <div class="feature-i iq-bg-warning">
                                                <i class="lab la-buffer"></i>
                                              </div>
                                              <div class="feature-icon">
                                                  <h5>Summary Report</h5>
                                              </div>
                                          </div>
                                        </a>
                                      </div>
                                      @endif
                                      @if($user->can('Attendance Consecutive Report'))
                                      <div class="col-md-4">
                                        <a href="{{ url('hr/reports/attendance-consecutive') }}">
                                          <div class="feature-effect-box wow fadeInUp" data-wow-duration="1s">
                                              <div class="feature-i iq-bg-danger">
                                                <i class="las la-project-diagram"></i>
                                              </div>
                                              <div class="feature-icon">
                                                  <h5>Consecutive</h5>
                                              </div>
                                          </div>
                                        </a>
                                      </div>
                                      @endif
                                      @if($user->can('Salary Report'))
                                      <div class="col-md-4 pr-0">
                                        <a href="{{ url('hr/reports/salary') }}">
                                          <div class="feature-effect-box wow fadeInUp" data-wow-duration="0.4s">
                                            <div class="feature-i iq-bg-primary">
                                              <i class="las la-file-invoice-dollar"></i>
                                            </div>
                                              <div class="feature-icon">
                                                  <h5>Salary</h5>
                                              </div>
                                          </div>
                                        </a>
                                      </div>
                                      @endif
                                      @if($user->can('Job Card'))
                                      <div class="col-md-4 pr-0">
                                        <a href="{{ url('hr/operation/job_card') }}">
                                          <div class="feature-effect-box wow fadeInUp" data-wow-duration="0.4s">
                                            <div class="feature-i iq-bg-info">
                                              <i class="las la-id-card"></i>
                                            </div>
                                              <div class="feature-icon">
                                                  <h5>Job Card</h5>
                                              </div>
                                          </div>
                                        </a>
                                      </div>
                                      @endif
                                      @if($user->can('Employee List'))
                                      <div class="col-md-4">
                                        <a href="{{ url('hr/employee/list') }}">
                                          <div class="feature-effect-box wow fadeInUp" data-wow-duration="0.4s">
                                            <div class="feature-i iq-bg-success">
                                              <i class="las la-users"></i>
                                            </div>
                                              <div class="feature-icon">
                                                  <h5>Employee</h5>
                                              </div>
                                          </div>
                                        </a>
                                      </div>
                                      @endif
                                      @if($user->can('Attendance Upload'))
                                      <div class="col-md-4">
                                        <a href="{{ url('hr/timeattendance/attendance-upload') }}">
                                          <div class="feature-effect-box wow fadeInUp" data-wow-duration="0.4s">
                                            <div class="feature-i iq-bg-primary">
                                              <i class="las la-cloud-upload-alt"></i>
                                            </div>
                                              <div class="feature-icon">
                                                  <h5>Attendance Upload</h5>
                                              </div>
                                          </div>
                                        </a>
                                      </div>
                                      @endif
                                      @canany(['Increment Approval', 'Increment Process 2', 'Increment Process 1'])
                                      <div class="col-md-4">
                                        <a href="{{ url('hr/payroll/increment-approval') }}">
                                          <div class="feature-effect-box wow fadeInUp" data-wow-duration="0.4s">
                                            <div class="feature-i iq-bg-success">
                                              <i class="las la-dollar-sign"></i>
                                            </div>
                                              <div class="feature-icon">
                                                  <h5>Increment</h5>
                                              </div>
                                          </div>
                                        </a>
                                      </div>
                                      @endcan
                                  </div>
                                </div>
                            </section>
                          </div>
                        </div>
                     </div>
                    </div>
                    @else
                    <div class="col-lg-7">
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                               <div class="iq-header-title">
                                  <h4 class="card-title text-primary border-left-heading">Attendance History</h4>
                               </div>
                            </div>
                            <div class="iq-card-body p-0">
                               <div id="patient-chart-2"></div>
                            </div>
                         </div>
                      </div>
                      <div class="col-lg-5">

                         <div class="iq-card mb-0">
                            <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                               <div class="iq-header-title">
                                  <h4 class="card-title text-primary border-left-heading">My Leave - {{ date('Y') }}</h4>
                               </div>
                            </div>
                            <div class="iq-card-body p-0">
                               <ul class="speciality-list m-0 p-0">
                                  @if(count($leaves) >0 )
                                     @foreach($leaves as $key => $lv)
                                    <li class="d-flex mb-4 align-items-center">
                                       <div class="user-img img-fluid">
                                        @if($lv->leave_status==1)
                                             <a href="#" class="iq-bg-success">
                                              <i class="las f-18 la-check-circle"></i>
                                           </a>
                                         @else
                                             <a href="#" class="iq-bg-danger">
                                              <i class="las f-18 la-times-circle"></i>
                                           </a>
                                         @endif
                                        
                                        </div>
                                       <div class="media-support-info ml-3">
                                          <h6>{{$lv->leave_type}} Leave</h6>
                                          <p class="mb-0">
                                          @if($lv->leave_from != $lv->leave_to)
                                              {{$lv->leave_from->format('d M, Y')}} - {{$lv->leave_to->format('d M, Y')}}
                                           @else
                                              {{$lv->leave_from->format('d M, Y')}}
                                           @endif
                                           </p>
                                       </div>
                                    </li>
                                    @endforeach
                                  @else
                                     <li class="d-flex mb-4 align-items-center">
                                       <div class="user-img img-fluid">
                                           <a href="#" class="iq-bg-danger">
                                              <i class="las f-18 la-times-circle"></i>
                                           </a>
                                        </div>
                                       <div class="media-support-info ml-3">
                                          <h6>No leave record!</h6>
                                          <p class="mb-0">
                                           ------------
                                           </p>
                                       </div>
                                    </li>
                                  @endif
                               </ul>
                            </div>
                         </div>
                      </div>
                    @endif
               </div>
            </div>
         </div>
      </div>
   </div>
   
@endsection