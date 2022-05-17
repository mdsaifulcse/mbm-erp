<!DOCTYPE html>
<html lang="en">
@include('pms.backend.layouts.head')
<body>
    <!------------------------------------------------------------------------------------------------>
    @include('pms.backend.layouts.pre-loader')
    <!-- WRAPPER ------------------------------------------------------------------------------------->
    <div id="app">
        <!-- Wrapper Start -->
        <div class="wrapper">
            <!------------------------------------------------------------------------------------------------>
            @include('pms.backend.menus.left-menu')
            <!------------------------------------------------------------------------------------------------>
            <!-- Page Content  -->
            <div id="content-page" class="content-page">
                @include('pms.backend.menus.header-menu')
                <!------------------------------------------------------------------------------------------------>
                <main class="">
                  <div id="main-body" class="container-fluid">
                    @yield('main-content')
                  </div>
                </main>
                <!------------------------------------------------------------------------------------------------>
                @include('pms.backend.layouts.footer')
            </div>
            <div class="app-loader">
                <i class="fa fa-spinner fa-spin"></i>
            </div>
        </div>
        <!-- END WRAPPER --------------------------------------------------------------------------------->
    </div>
    <!------------------------------------------------------------------------------------------------>
    @include('pms.backend.layouts.script')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @yield('page-script')
    @include('pms.backend.layouts.toster-script')
</body>

</html>
