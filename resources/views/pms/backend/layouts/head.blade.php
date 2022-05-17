<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - MBM ERP</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/mbm.ico')}} " />
    

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" media='screen,print'>
    <link rel="stylesheet" href="{{ asset('assets/css/all.css') }}" media='screen,print'>
    @stack('css')
    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css?v=1.3') }}" media='screen,print'>
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}" media='screen,print'>
     <!-- jQuery Confirm -->
    <link rel="stylesheet" href="{{asset('assets/js/jquery-confirm/jquery-confirm.min.css')}}" />
     <!-- toastr alert -->
    <link rel="stylesheet" href="{{asset('notification_assets/css/toastr.min.css')}}" />
    <link rel="stylesheet" href="{{asset('plugins/air-datepicker/css/datepicker.min.css')}}" />
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">

    @yield('page-css')
</head>