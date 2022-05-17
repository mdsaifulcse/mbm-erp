
<div id="incentive-review-print">
    <form  class="report-preview">  
        <div class="panel panel-info">
            
            <div class="panel-body">

                <table class="table" style="width:100%;margin-bottom:0;text-align:left" cellpadding="5">
                    <tr>
                        <td>
                            <h5 style="margin:4px 10px;text-align:center;font-weight:600;">
                                Incentive Bonus
                                <br/>
                                Date: {{ date('d-F-Y', strtotime($input['date'])) }}
                            </h5>
                        </td>
                    </tr>
                </table>

                <table class="table table-head" style="width:100%;border:1px solid #ccc;" cellpadding="2" cellspacing="0" border="1" align="center">
                    <thead>
                        <tr >
                            <th >SL.</th>
                            <th >ID</th>
                            <th >Name</th>
                            <th >Designation</th>
                            <th >Department</th>
                            <th >Floor</th>
                            <th >Line</th>
                            <th >Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $totalEmp = count($input['as_id']); $j=0; ?>
                        @for($i=0; $i < $totalEmp; $i++)
                        @if($input['as_id'][$i] != '')
                            @php
                                $asid = $input['as_id'][$i];
                                $amount = $input['amount'][$i];
                                $emp = $employee[$asid];
                                $totalAmount += $amount;
                            @endphp
                            <tr>
                                <td style="text-align: center;">{{ ++$j }}</td>
                                <td> {{ $emp->associate_id }}</td>
                                <td> {{ $emp->as_name }}</td>
                                <td> {{ $designation[$emp->as_designation_id]['hr_designation_name']??'' }}</td>
                                <td> {{ $department[$emp->as_department_id]['hr_department_name']??'' }}</td>
                                <td> {{ $floor[$emp->as_floor_id]['hr_floor_name']??'' }}</td>
                                <td> {{ $line[$emp->as_line_id]['hr_line_name']??'' }}</td>
                                <td> {{ $amount }}</td>
                            </tr>
                        @endif       
                        @endfor
                        
                    </tbody>
                </table>
                

            </div>
        </div>
                
        @if(isset($input['as_id']))
            <div id="unit-info">
                <table class="table" style="width:100%;border-bottom:1px solid #ccc;margin-bottom:0;font-size:12px;text-align:left" cellpadding="5">
                    <tr>
                        <td style="width:25%; text-align:right;">
                            
                        </td>
                        <td style="width:25%">
                            <p style="margin:0;padding: 0"><strong>মোট কর্মী/কর্মচারীঃ </strong>
                                {{ count($input['as_id']) }}
                            </p>
                            
                        </td>
                        <td style="width:30%;">
                            <p style="margin:0;padding: 0"><strong>সর্বমোট টাকার পরিমান: </strong>
                                {{ bn_money($totalAmount) }}
                            </p>
                            
                        </td>
                        
                        <td style="width:20%; text-align:right;">
                            
                        </td>
                        
                    </tr>
                </table>
            </div>
        @endif
        
    </form>
    
</div>

