    

    <div class="col-sm-12 mt-2">
                            
        <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('printss')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>

    </div>
<div id="printss" >


<div  style="overflow: hidden; width:100%" >


    <div style="width:8%; float: left;">
        &nbsp;
    </div>
    <div style="width:82%; float: left;">
        @php
// dd($increment_history);
        @endphp
        <h2 style="text-align:center;">Increment Promotion History</h2>
        <table border="0"  style="width:50%;">
            <tr>
                <th style="width:20px;line-height: 30px;text-align:left;" >Name  </th>
                <th style="width:200px;line-height: 30px;text-align:left;">: &nbsp; {{$emp->as_name}}</th>
            </tr>
            <tr>
                <th style="width:20px;line-height: 30px;text-align:left;" >Unit</th>
                <th style="width:250px;line-height: 30px;text-align:left;" >: &nbsp; {{$emp->hr_unit_name}}</th>
            </tr>
            <tr>
                <th style="width:20px;line-height: 30px;text-align:left;" >Date Of Join</th>
                <th style="width:200px;line-height: 30px;text-align:left;" >: &nbsp; 
                   {{date('d-m-Y', strtotime($emp->as_doj))}}</th>
            </tr>
            <tr>
                <th style="width:20px;line-height: 30px;text-align:left;" >Department</th>
                <th style="width:200px;line-height: 30px;text-align:left;" >: &nbsp; {{$emp->hr_department_name}}</th>
            </tr>
            <tr>
                <th style="width:20px;line-height: 30px;text-align:left;" >Section</th>
                <th style="width:200px;line-height: 30px;text-align:left;" >: &nbsp; {{$emp->hr_section_name}}</th>
            </tr>
            <tr>
                <th style="width:20px;line-height: 30px;text-align:left;" >Subsection</th>
                <th style="width:200px;line-height: 30px;text-align:left;" >: &nbsp; {{$emp->hr_subsec_name}}</th>
            </tr>
            <tr>
                <th style="width:20px;line-height: 30px;text-align:left;" >Designation</th>
                <th style="width:200px;line-height: 30px;text-align:left;" >: &nbsp; {{$emp->hr_designation_name}}</th>
            </tr>
            <tr>
                <th style="width:20px;line-height: 30px;text-align:left;" >Current&nbsp;Salary</th>
                <th style="width:200px;line-height: 30px;text-align:left;" >: &nbsp; {{$emp->salary}}</th>
            </tr>
            <tr>
                <th style="width:20px;line-height: 30px;text-align:left;" >Employee&nbsp;Status</th>
                <th style="width:200px;line-height: 30px;text-align:left;" >: &nbsp; {{$emp->as_status_NAME}}</th>
            </tr>
        </table>
        <br> <br>  
    </div>
    <div  style="width:8%; float: left;">
        &nbsp;
    </div>
</div>

<br><br>



<div  style="overflow: hidden; width:100%">
    <div style="width:6%; float: left;">
        &nbsp;
    </div>

    <div style="width:42%; float: left;">
        <table border="1"  style="width:100%;border-collapse: collapse;" >
            <tr>
                 <th colspan="5" style="text-align:center;background-color: lightgrey;">Promotion History </th> 
            </tr>
            <tr>
                <th style="text-align:center;">Current Designation</th>
                <th style="text-align:center;">Previous Designation</th> 
                <th style="text-align:center;">Eligible Date</th>
                <th style="text-align:center;">Effective Date</th>
            </tr>
             @php
              $count_promotion_history=count($promotion_history);
              $count_INCREMENT_history=count($increment_history);
              // dd($count_promotion_history);
             @endphp
            @foreach($promotion_history as $promotion_history1)
            <tr>
                <td style="padding-left:4px;">{{$promotion_history1->current_designation_name}}</td>
                <td style="padding-left:4px;">{{$promotion_history1->previous_designation_name}}</td>
                <td style="text-align:center;padding-left:4px;width:75px"> {{date('d-m-Y', strtotime($promotion_history1->eligible_date))}}</td>
                <td style="text-align:center;width:75px" > {{date('d-m-Y', strtotime($promotion_history1->effective_date))}}</td>
            </tr>
            @endforeach
            @if ($count_promotion_history==0)
              <td colspan="5" style="padding-left:4px;">NO RECORD FOUND..</td>
            @endif
        </table>
        <br> <br>    
    </div>


    <div  style="width:3%; float: left;">
        &nbsp;
    </div>
    <div style="width:42%; float: left;">
        <table border="1"  style="width:100%;border-collapse: collapse;">

            <tr>
                <th colspan="5" style="text-align:center;background-color: lightgrey;">Increment History</th> 
            </tr>

            <tr>
                <th style="text-align:center;">Increment Amount</th> 
                <th style="text-align:center;">Previous Salary</th> 
                <th style="text-align:center;">Current Salary</th>
                <th style="text-align:center;">Eligible Date</th>
                <th style="text-align:center;"> Effective Date</th>
            </tr>
            @foreach($increment_history as $increment_history1)
            <tr>
                <td style="text-align:center;" >{{$increment_history1->increment_amount}}
                </td>
                <td style="text-align:center;" >{{$increment_history1->current_salary}}</td>
                <td style="text-align:center;" >{{$increment_history1->increment_amount +$increment_history1->current_salary}}</td>
                <td style="text-align:center;"> {{date('d-m-Y', strtotime($increment_history1->eligible_date))}}</td>
                <td style="text-align:center;" > {{date('d-m-Y', strtotime($increment_history1->effective_date))}}</td>
            </tr>
            @endforeach
            @if ($count_INCREMENT_history==0)
              <td colspan="5" style="padding-left:4px;">NO RECORD FOUND..</td>
            @endif
        </table>
        <br> <br>    
    </div>
    <div  style="width:6%; float: left;">
        &nbsp;
    </div>
</div>
</div>