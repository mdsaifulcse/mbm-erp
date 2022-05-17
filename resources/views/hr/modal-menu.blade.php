<div class="modal fade apps-modal" id="appsModal" tabindex="-1" role="dialog" aria-labelledby="appsModalLabel" aria-hidden="true" data-backdrop="false">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="quick-search">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4 ml-auto mr-auto">
                            <div class="input-wrap">
                                <input type="text" id="quick-search" class="form-control" placeholder="Search..." autofocus="autofocus" />
                                <i class="fa fa-search"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-body d-flex align-items-center">
                <div class="container">
                    <div class="apps-wrap">
                        <div class="app-item">
                            <a href="{{ url('/hr') }}"><i class="fa fa-home"></i><span>{{ __('Dashboard')}}</span></a>
                        </div>
                        
                        <div class="app-item">
                            <a href="{{ url('/hr/employee/list') }}"><i class="fa fa-users"></i><span>{{ __('Employees')}}</span></a>
                        </div>
                        <div class="app-item">
                            <a href="{{ url('/hr/reports/attendance_summary_report') }}"><i class="fa fa-file"></i><span>{{ __('Summer Report')}}</span></a>
                        </div>
                        <div class="app-item">
                            <a href="{{ url('hr/reports/daily-attendance-activity') }}"><i class="fa fa-file"></i><span>{{ __('Daily Report')}}</span></a>
                        </div>
                        <div class="app-item">
                            <a href="{{ url('hr/operation/job_card') }}"><i class="fa fa-id-card"></i><span>{{ __('Job Card')}}</span></a>
                        </div>
                        <div class="app-item">
                            <a href="{{ url('hr/reports/salary') }}"><i class="fa fa-file"></i><span>{{ __('Salary Report')}}</span></a>
                        </div>
                        <div class="app-item">
                            <a href="{{ url('hr/reports/monthly-attendance-activity') }}"><i class="fa fa-file"></i><span>{{ __('Monthly Attendance')}}</span></a>
                        </div>
                        <div class="app-item">
                            <a href="{{ url('hr/reports/attendance-consecutive') }}"><i class="fa fa-id-badge"></i><span>{{ __('Consecutive')}}</span></a>
                        </div>
                        <div class="app-item">
                            <a href="{{ url('hr/reports/bill-announcement') }}"><i class="fa fa-file"></i><span>{{ __('Bill Announce')}}</span></a>
                        </div>
                        <div class="app-item">
                            <a href="{{ url('hr/reports/bonus') }}"><i class="fa fa-file"></i><span>{{ __('Bonus Report')}}</span></a>
                        </div>
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
<script type="text/javascript">
    $(document).on('keyup',"#quick-search", function() {
        var e = $(this).val().trim().toLowerCase();
        $(".app-item").hide().filter(function() {
            return -1 != $(this).html().trim().toLowerCase().indexOf(e)
        }).show()
    })
</script>
@endpush