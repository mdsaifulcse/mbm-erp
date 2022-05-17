<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - MBM ERP</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/mbm.ico')}} " />
    

    <!-- Styles -->
    <link href="{{ asset('css/app.css?v=2.0') }}" rel="stylesheet" media='screen,print'>
    <link rel="stylesheet" href="{{ asset('assets/css/all.css?v=2.0') }}" media='screen,print'>
    @stack('css')
    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css?v=2.1') }}" media='screen,print'>
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}" media='screen,print'>
    
</head>
<body class="@if(request()->segment(3) =='monthly-attendance-activity' || request()->segment(3) =='salary' || request()->segment(3) =='increment-approval') sidebar-main-menu @endif">
    <!-- loader Start -->
    <div id="loading">
        <div id="loading-center">
        </div>
    </div>
    <!-- loader END -->
    <div id="app">
        <!-- Wrapper Start -->
        <div class="wrapper">
            <!-- Sidebar  -->
            <div class="iq-sidebar">
              <input type="hidden" value="{{ url('/') }}" id="base_url">
                <div class="iq-sidebar-logo d-flex justify-content-between">
                   <a href="{{ url('/') }}">
                   <img src="{{ asset('images/mbm-logo-w.png') }}" class="img-fluid" alt="MBM">
                   {{-- <span>MBM</span> --}}
                   </a>
                   <div class="iq-menu-bt-sidebar">
                      <div class="iq-menu-bt align-self-center">
                         <div class="wrapper-menu">
                            <div class="main-circle"><i class="las la-ellipsis-h"></i></div>
                            <div class="hover-circle"><i class="las la-ellipsis-v"></i></div>
                         </div>
                      </div>
                   </div>
                </div>
                <div id="sidebar-scrollbar">
                    @include('hr.menu')
                    <div class="p-3"></div>
                </div>
            </div>
            <!-- Page Content  -->
            <div id="content-page" class="content-page">
                <!-- TOP Nav Bar -->
                <div class="iq-top-navbar">
                   <div class="iq-navbar-custom">
                      <div class="iq-sidebar-logo">
                         <div class="top-logo">
                            <a href="#" class="logo">
                            <img src="{{ asset('assets/images/logo.png') }}" class="img-fluid" alt="">
                            <span>MBM Group</span>
                            </a>
                         </div>
                      </div>
                      <nav class="navbar navbar-expand-lg navbar-light p-0">
                         @include('common.top_search')
                         <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                         <i class="las la-ellipsis-h"></i>
                         </button>
                         <div class="iq-menu-bt align-self-center">
                            <div class="wrapper-menu">
                               <div class="main-circle"><i class="las la-ellipsis-h"></i></div>
                               <div class="hover-circle"><i class="las la-ellipsis-v"></i></div>
                            </div>
                         </div>
                         <div class="nav-item iq-full-screen">
                            <a href="#" class="iq-waves-effect" id="btnFullscreen"><i class="ri-fullscreen-line"></i></a>
                         </div>
                         <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ml-auto navbar-list">

                              <li class="nav-item">
                                 {{-- <a class="iq-waves-effect" title="Shortcut Menu" data-original-title="Shortcut Menu" id="apps_modal_btn" data-toggle="modal" data-target="#appsModal"><i class="fa fa-th-large"></i></a> --}}
                              </li>
                            </ul>
                         </div>
                         <ul class="navbar-list">
                           
                            <li>
                               <a href="#" class="search-toggle iq-waves-effect d-flex align-items-center">
                                  @if(auth()->user()->employee)
                                  <img src='{{ emp_profile_picture(auth()->user()->employee)}}' class="img-fluid rounded mr-3" alt="{{ auth()->user()->name }}" >
                                  @else
                                    <img class="img-fluid rounded mr-3" src="{{ asset('assets/images/user/09.jpg') }} ">
                                  @endif
                                  <div class="caption">
                                     <h6 class="mb-0 line-height">{{ auth()->user()->name }}</h6>
                                     <span class="font-size-12">Available</span>
                                  </div>
                               </a>

                               <div class="iq-sub-dropdown iq-user-dropdown">
                                  <div class="iq-card shadow-none m-0">
                                     <div class="iq-card-body p-0 ">
                                        <div class="bg-primary p-3">
                                           <h5 class="mb-0 text-white line-height">Hello {{ auth()->user()->name }}</h5>
                                           <span class="text-white font-size-12">Available</span>
                                        </div>
                                        <a href="{{url('profile')}}" class="iq-sub-card iq-bg-primary-hover">
                                           <div class="media align-items-center">
                                              <div class="rounded iq-card-icon iq-bg-primary">
                                                 <i class="f-18 las la-user-tie"></i>
                                              </div>
                                              <div class="media-body ml-3">
                                                 <h6 class="mb-0 ">My Profile</h6>
                                                 <p class="mb-0 font-size-12">View personal profile details.</p>
                                              </div>
                                           </div>
                                        </a>
                                        
                                        <a href="{{url('user/change-password')}}" class="iq-sub-card iq-bg-primary-hover">
                                           <div class="media align-items-center">
                                              <div class="rounded iq-card-icon iq-bg-primary">
                                                 <i class="f-18 las la-key"></i>
                                              </div>
                                              <div class="media-body ml-3">
                                                 <h6 class="mb-0 ">Account settings</h6>
                                                 <p class="mb-0 font-size-12">Manage your password.</p>
                                              </div>
                                           </div>
                                        </a>
                                        <div class="d-inline-block w-100 text-center p-3">
                                           
                                           <a class="bg-primary iq-sign-btn" role="button" href="{{ route('logout') }}"
                                              onclick="event.preventDefault();
                                              document.getElementById('logout-form').submit();">
                                              {{ __('Sign out') }} <i class="ri-login-box-line ml-2"></i>
                                           </a>

                                           <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                               @csrf
                                           </form>
                                        </div>
                                     </div>
                                  </div>
                               </div>
                            </li>
                         </ul>
                      </nav>
                   </div>
                </div>
                {{-- main content --}}
                <main class="">
                  <div id="main-body" class="container-fluid">
                    @yield('main-content')
                  </div>
                </main>
                <!-- Footer -->
                <footer class="bg-white iq-footer mr-0">
                   <div class="container-fluid">
                      <div class="row">
                         <div class="col-lg-6">
                            <ul class="list-inline mb-0">
                               <li class="list-inline-item"><a href="#">Privacy Policy</a></li>
                               <li class="list-inline-item"><a href="#">Terms of Use</a></li>
                            </ul>
                         </div>
                         <div class="col-lg-6 text-right">
                            Copyright 2018 - {{date('Y')}} <a>MBM Group</a> All Rights Reserved.
                         </div>
                      </div>
                   </div>
                </footer>
                <!-- Footer END -->
            </div>
            <div class="app-loader">
                <i class="fa fa-spinner fa-spin"></i>
            </div>
            

        </div>
        @include('hr.modal-menu')
    </div>
    <script src="{{ asset('js/app.js?v=2.0') }}"></script>
    <script src="{{asset('assets/js/all.js?v=2.0')}}"></script>
    <script>
        var count = 0;
        var refreshIntervalId =setInterval(function(){ 
            count++;
            jQuery(document).ready(function() {
                clearInterval(refreshIntervalId);
                jQuery("#load").fadeOut();
                jQuery("#loading").fadeOut("");
                
            });
            if( count == 5){
                clearInterval(refreshIntervalId);
                jQuery("#load").fadeOut();
                jQuery("#loading").fadeOut("");
            }
        }, 300);
    </script>
    <script>
      var loaderContent = '<div class="animationLoading"><div id="container-loader"><div id="one"></div><div id="two"></div><div id="three"></div></div><div id="four"></div><div id="five"></div><div id="six"></div></div>';
      let afterLoader = '<div class="loading-select left"><img src="{{ asset('images/loader.gif')}}" /></div>';
    </script>
    <!-- Custom JavaScript -->
    @stack('js')
    @toastr_render
    <script>
      // 
      
      $( document ).ajaxComplete(function() {
        // Required for Bootstrap tooltips in DataTables
        $('[data-toggle="tooltip"]').tooltip({
            "html": true,
            //"delay": {"show": 1000, "hide": 0},
        });
        $('[data-toggle="popover"]').popover({
            html: true,
         });

      });
      $('[data-toggle="popover"]').click(function () {
         $(this).popover('show');
      });
      $(document).on('keyup', 'input, select', function(e) {
          if (e.which == 39) { // right arrow
            $(this).closest('td').next().find('input, select').focus().select();
          } else if (e.which == 37) { // left arrow
            $(this).closest('td').prev().find('input, select').focus().select();
          } else if (e.which == 40) { // down arrow
            $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus().select();
          } else if (e.which == 38) { // up arrow
            $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus().select();
          }
      });
      $(document).on('click', '#apps_modal_btn', function(event) {
         setTimeout(function(){
            $("#quick-search").focus();
         },500);
      });
      $(document).ajaxError(function(event, jqxhr, settings, exception) {
        if (exception == 'Unauthorized') {
          $.notify("Your session has expired!", 'error');
          setTimeout(function(){
            window.location = '{{ url()->full() }}';
          }, 1000)

        }
      });
      let panelOptions = [];
      let Scrollbar = window.Scrollbar;
      if (jQuery('.col-panel-scroll').length) {
         Scrollbar.init(document.querySelector('.col-panel-scroll'), panelOptions);
      }
      let Scrollbar1 = window.Scrollbar;
      if (jQuery('.col-panel-scroll1').length) {
         Scrollbar1.init(document.querySelector('.col-panel-scroll1'), panelOptions);
      }
      let Scrollbar2 = window.Scrollbar;
      if (jQuery('.col-panel-scroll2').length) {
         Scrollbar2.init(document.querySelector('.col-panel-scroll2'), panelOptions);
      }
      
      // on first focus (bubbles up to document), open the menu
      $(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
        $(this).closest(".select2-container").siblings('select:enabled').select2('open');
      });
      // steal focus during close - only capture once and stop propogation
      $('select.select2').on('select2:closing', function (e) {
        $(e.target).data("select2").$selection.one('focus focusin', function (e) {
          e.stopPropagation();
        });
      });
    </script>
    <script src="{{ asset('assets/js/custom.js?v=2.0') }}?v=2.0"></script>
    <script src="{{ asset('js/custom.js?v=2.0') }}?v=2.0"></script>
    
</body>
</html>
