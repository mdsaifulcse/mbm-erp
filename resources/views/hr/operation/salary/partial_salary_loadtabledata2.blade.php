<table class="table table-bordered">
                            <thead >
                                <tr  style="text-align:center;">
                                    <th>SL</th>
                                    <th>Unit</th>
                                    <th>Total Employee</th>
                                    <th>Salary </th>
                                    <th>OT Amount</th>
                                    


                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $sl=0;
                                $total_employee=0;
                                $total_salary=0;
                                $ot_amount=0;
                                @endphp
                                @foreach($partial_salary_group_data as $partial_salary_group_data)
                                
                                @php
                                $total_employee+=$partial_salary_group_data->total_employee;
                                $total_salary+=$partial_salary_group_data->total_salary;
                                $ot_amount+=$partial_salary_group_data->ot_amount;
                                @endphp


                                <td style="text-align:center;">{{++$sl}}</td>

                                <td style="text-align:left;">{{ $partial_salary_group_data->hr_unit_name}} ({{ $partial_salary_group_data->as_ot_name}})</td>

                                <td class="unitdlt" style="text-align:center;" >
                                    {{  bn_money($partial_salary_group_data->total_employee)}}

                                </td>

                                <td style="text-align:center;">{{ bn_money( $partial_salary_group_data->total_salary)}}</td>
                                <td style="text-align:center;">{{ bn_money( $partial_salary_group_data->ot_amount)}}</td>


                            </tr>

                                
                                @endforeach
                                <tr>
                                    
                                    <td colspan="2"  class="unitdlt" style="text-align:Right;" >
                                   <b> Total :</b>
                                    </td>
                                   <td  class="unitdlt" style="text-align:center;" >
                                   <b> {{ bn_money($total_employee)}}</b>
                                    </td>

                                <td style="text-align:center;"><b> {{ bn_money($total_salary)}}</b></td>
                                <td style="text-align:center;"><b> {{ bn_money($ot_amount)}}</b></td>
                           

                                </tr>

            <tr>
           

               {{--  @if ($find_partial_paramiter->approve_status=='S' or $find_partial_paramiter->approve_status=='A')
                <td >
                  <button class="btn btn-primary nextBtn btn-sm pull-right"  disabled ><i class="fa fa-save"></i> Submit For Approval</button>
                </td>
                @else
                    
                <td >
                  <button class="btn btn-primary nextBtn btn-sm pull-right" type="submit" id="Approval" value="Submit"><i class="fa fa-save"></i> Submit For Approval</button>
                </td>
              
                @endif

                  
                    @if ($find_partial_paramiter->approve_status=='A' AND  $find_partial_paramiter->audit_status=='N')
                <td colspan="4">
                 <button class="btn btn-primary nextBtn btn-sm pull-right" ><i class="fa fa-save"></i> Submit For Audit
                    </button>
                </td>
                @else
                    
               <td colspan="4">
                 <button class="btn btn-primary nextBtn btn-sm pull-right" disabled ><i class="fa fa-save"></i> Submit For Audit
                    </button>
                </td>
              
                @endif --}}


                    

                   
                </td>
            </tr>

                              


                            </tbody>
                        </table>


   


