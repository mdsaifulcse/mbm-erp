<style>
    .text-bold{font-weight:bold;font-size:14px;}
</style>
@php 
    $unit = unit_by_id(); 
    $designation = designation_by_id(); 
    $section = section_by_id(); 
    $department = department_by_id(); 
@endphp


<div class="col-sm-5">
    <div class="user-details-block benefit-employee">
        <div class="user-profile text-center mt-0">
            <img id="avatar" class="avatar-130 img-fluid" src="{{ emp_profile_picture($employee) }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
        </div>
        <p class="mt-3 text-center">
            <b class="text-primary f-16">{{$employee->associate_id}}</b> 
        </p>
        
    </div>
</div>


<div class="col-sm-7">
    <div class="mt-3">
        <p><b class="h-text-left">Name </b>: {{$employee->as_name}} </b></p>
        @if($employee->as_oracle_code)
        <p class="mb-0" ><b class="h-text-left">Oracle ID </b>: {{$employee->as_oracle_code}} </p>
        @endif
        <p class="mb-0" >
            <b class="h-text-left">Designation </b>: {{$designation[$employee->as_designation_id]['hr_designation_name']}} 
        </p>
        <p class="mb-0" >
            <b class="h-text-left">Department </b>: 
            {{$section[$employee->as_section_id]['hr_section_name']}}, 
            <b>{{$department[$employee->as_department_id]['hr_department_name']}} </b>
        </p>
        <p class="mb-0" >
            <b class="h-text-left">Unit </b>: {{$unit[$employee->as_unit_id]['hr_unit_name']}} 
        </p>
        
        <p class="mb-0" ><b class="h-text-left">Date of Join </b>: {{$employee->as_doj->format('d F, Y')}} </p>
        <p class="text-primary" style="margin-top: 5px">
            Print leave application <i onclick="printDiv('DivIdToPrint')" class="fa fa-print text-danger cursor-pointer f-16"></i>
        </p>
    </div>
</div>
<div class="col-sm-12 mt-3">
    <div class="pl-4 pr-4">
        @include('hr.timeattendance.leave.leave-balance-table')
    </div>
</div>

@include('hr.timeattendance.leave.leave_application_form')

