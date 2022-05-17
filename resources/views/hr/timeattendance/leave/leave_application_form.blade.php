<div id="DivIdToPrint" style="padding: 0px 96px; display: none; ">
    <div style="text-align: center; margin-top: -5px">
        <span style="font-size: 1.5em; font-weight: bolder">{{$unit[$employee->as_unit_id]['hr_unit_name']}}</span>
        <hr>
        <span style="font-size: 1.17em; font-weight: bolder">Leave Application Form</span>
        <hr>
    </div>
    <div>
        <p>Application Submission Date: </p>
        <div>
            <div style="width: 50%; float: left;">
                <h4>Personal Details</h4>
                <table style="text-align: left;">
                    <tbody>
                    <tr>
                        <th>Name</th>
                        <td>: {{$employee->as_name}}</td>
                    </tr>
                    <tr>
                        <th>Designation</th>
                        <td>: {{$employee->designation->hr_designation_name}}</td>
                    </tr>
                    <tr>
                        <th>Card No</th>
                        <td>:  {{$employee->associate_id}}</td>
                    </tr>
                    <tr>
                        <th>Dept/ Section</th>
                        <td>: {{$employee->department->hr_department_name}}</td>
                    </tr>
                    <tr>
                        <th>Unit</th>
                        <td>: {{$unit[$employee->as_unit_id]['hr_unit_name']}}</td>
                    </tr>
                    <tr>
                        <th>Date of Joining</th>
                        <td>: {{$employee->as_doj->format('d-m-Y')}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div style="float: right; width: 50%;">
                <h4>Application Leave Details</h4>
                <table style="text-align: left;">
                    <tbody>
                    <tr>
                        <th>Leave From</th>
                        <td>: </td>
                    </tr>
                    <tr>
                        <th>Leave To</th>
                        <td>: </td>
                    </tr>
                    <tr>
                        <th>No Of Days</th>
                        <td>: </td>
                    </tr>
                    <tr>
                        <th>Leave Type</th>
                        <td>: Casual/Sick/Earn/Maternity/Without Pay</td>
                    </tr>
                    <tr>
                        <th>Reason of Leave</th>
                        <td>: </td>
                    </tr>
                    <tr>
                        <th>Resume Duty On</th>
                        <td>: </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div style="clear: both;"></div>
    <div style="margin-top: 20px">
        <div style="width: 75%; float: left;">
            <div class="boxbody">
                <div style="width: 95%; height: 80px; background: rgba(255, 255, 255, 0.9); padding: auto; border: 1px solid black; border-radius: 10px;">
                    <div style="padding-left: 5px"><u>Address While On leave (Include Mobile Number)</u></div>
                </div>
            </div>
        </div>

        <div style="width: 25%; float: right; margin-top: 8%;">
            <p>-------------------------------<br> Applicant's Signature</p>
        </div>
    </div>
    <div style="clear: both;"></div>
<div style="margin-top: 20px">
    <div style="margin-top: 30px">
        <hr>
        <p style="margin-top: 10px">Application can be given a leave of total _____________ From _______________________________ To _______________________________</p>
    </div>
    <div style="margin-top: 40px;">
        <div style="float: left;">
            <p style="margin-top: 5%; text-align: center">-------------------------------------- <br> Leave Recommended</p>
        </div>
        <div style="float: right;">
            <p style="margin-top: 5%; text-align: center">-------------------------------------- <br>Department Head</p>
        </div>
    </div>
</div>
    <div style="clear: both;"></div>
    <div style="margin-top: 20px">
        <hr style="color: black; font-weight: bolder">
        <h4>HRD Department Use only</h4>
        <div style="margin-top: 30px">
            <div style="float: left; width: 15%; margin-top: 20%;">
                <p style="text-align: center">----------------------------<br>HR Officer</p>
            </div>
            <div style="width: 70%; float: left;">
                <style type="text/css">
                    table.leave-table {
                        border: 1px solid black; 
                        width: 95%; 
                        border-collapse: collapse; 
                        text-align: center
                    }
                    table.leave-table tr{border: 1px solid black; padding: 20px}
                    table.leave-table td{border: 1px solid black; padding: 5px 0px;}
                </style>
                <table class="leave-table" >
                    <thead>
                        <tr>
                            <th style="text-align:center;">Leave Type</th>
                            <th>Entitled Leave</th>
                            <th>Availed</th>
                            <th>Not Availed</th>
                            <th>Approved Leave</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Casual</td>
                            <td>{{$balance->casual->total??0}}</td>
                            <td>{{ $balance->casual->enjoyed??0 }}</td>
                            <td>{{ $balance->casual->total - $balance->casual->enjoyed }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Sick</td>
                            <td>{{$balance->sick->total??0}}</td>
                            <td>{{ $balance->sick->enjoyed??0 }}</td>
                            <td>{{ $balance->sick->total - $balance->sick->enjoyed }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Earn</td>
                            <td>{{$balance->earned->total??0}}</td>
                            <td>{{$balance->earned->enjoyed??0}}</td>
                            <td>
                                @if(\Carbon\Carbon::parse($employee->as_doj)->age >= 1)
                                {{round((($balance->earned->total) - $balance->earned->enjoyed),2) }}
                                @endif
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Without Pay</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Special</td>
                            <td></td>
                            <td>{{$balance->special??0}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>

            </div>
            <div style="float: right; width: 15%; margin-top: 20%;">
                <p style="text-align: center">----------------------------<br>Approved By</p>
            </div>
        </div>

    </div>
    <div style="clear: both;"></div>


    <div style="margin-top: 30px">
        <hr>
        <div >
            <h4 style="text-align: center">{{$unit[$employee->as_unit_id]['hr_unit_name']}}</h4>
            <hr style="font-weight: bolder; color: black">
            <div style="margin-top: 30px">
                <div>
                    <span style="text-transform:capitalize; float: left; font-size: 1.17em; font-weight: bolder">Leave Pass</span>
                    <span style="float: right; border: 1px solid black; padding: 5px;">Date: @for($nb = 1; $nb < 18; $nb++) &nbsp; @endfor </span>
                </div>
                <div style="clear: both"></div>
                <p style="clear: both; line-height: 150%; margin-top: 30px">This is certify that <b>{{$employee->as_name}}</b> Designation <b>{{$employee->designation->hr_designation_name}}</b> Card No <b>{{$employee->associate_id}}</b> Section <b>{{$employee->section->hr_section_name}}</b> Department
                    <b>{{$employee->department->hr_department_name}}</b> Unit <b>{{$unit[$employee->as_unit_id]['hr_unit_name']}}</b> is given a casual/sick/earn/maternity/without
                    pay <br> Leave of total _____________ From _______________________ To _______________________</p>
            </div>
        </div>
        <div style="margin-top: 40px;">
            <div style="float: left;">
                <p style="margin-top: 12%; text-align: center;">------------------------------- <br> Applicant's Signature</p>
            </div>
            <div style="float: right;">
                <p style="margin-top: 12%; text-align: center">------------------------------- <br>Office Signature</p>
            </div>
        </div>
    </div>

</div>
