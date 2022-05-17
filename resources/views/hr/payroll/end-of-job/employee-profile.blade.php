<div class="user-details-block" style="border-right: 1px solid #d1d1d1;padding-top: 1.5rem;">
    <div class="user-profile text-center mt-0">
        <img id="avatar" class="avatar-130 img-fluid" src="{{ emp_profile_picture($employee) }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
    </div>
    <div class="text-center mt-3">
        <h4><b id="name">{{$employee->as_name}}</b></h4>
        <p><span id="designation">{{$employee->hr_designation_name}}</span>, <b id="department">{{$employee->hr_department_name}}</b></p>
    </div>
    <div class="text-center">
        <p class="mb-0"  id="unit">{{$employee->hr_unit_name}}</p>
    </div>
    <div class="text-center">
        <p class="mb-0">DOJ: <b id="doj">{{$employee->as_doj->format('Y-m-d')}}</b></p>
    </div>
</div>
<br>
<ul class="speciality-list m-0 p-0">
    <li class="d-flex mb-4 align-items-center">
       <div class="user-img img-fluid"><a href="#" class="iq-bg-warning"><i class="las f-18 la-dollar-sign"></i></a></div>
       <div class="media-support-info ml-3">
          <h6>Salary</h6>
          <p class="mb-0">Gross:  <span class="text-danger" id="gross_salary">{{$employee->ben_current_salary}}</span> Basic: <span class="text-success" id="basic_salary">{{$employee->ben_basic}}</span></p>
       </div>
    </li>
    @if($earnedLeave > 0)
    <li class="d-flex mb-4 align-items-center">
       <div class="user-img img-fluid"><a href="#" class="iq-bg-info"><i class="las f-18 la-database"></i></a></div>
       <div class="media-support-info ml-3">
          <h6>Earned Leave</h6>
          <b>{{$earnedLeave??0}}</b> Day(s)
       </div>
    </li>
    @endif
</ul>