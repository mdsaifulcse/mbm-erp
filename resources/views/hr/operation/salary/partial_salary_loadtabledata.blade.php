<table class="table table-bordered">
                            <thead >
                                <tr  style="text-align:center;">
                                    <th>SL</th>
                                    <th>Unit</th>
                                    <th>Status</th>
                                    <th>Salary<br>Date Range</th>
                                    {{-- <th>Salary <br> To Date</th> --}}
                                    <th>OT Pay <br> Status</th>
                                    <th>OT <br>Date Range</th>
                                    {{-- <th>OT<br> To Date</th> --}}
                                    <th>Salary <br>Below</th>
                                    <th>Location</th>
                                    <th>Area</th>
                                    <th>Process</th>
                                    <th>Action</th>



                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $sl=0;
                            
                                // and date('Y-m')==$process_paramiter->salary_to_date)
                                @endphp
                                @foreach($process_paramiter as $process_paramiter)
                                @php
                                 // dd( date('Y-m', strtotime($process_paramiter->salary_to_date)));
                                 $x=date('Y-m', strtotime($process_paramiter->salary_to_date));
                                @endphp
                                <tr  class="delete-{{ $process_paramiter->id}}">
                                    <td style="text-align:center;">{{++$sl}}
                                    <br>
                             @if ($process_paramiter->approve_status=='N' and $x==date('Y-m'))
                                    <i class="fa fa-edit" style="text-align:center;cursor: pointer;color:blue;" data-id="{{ $process_paramiter->id}}" id="updatedata"></i>
                            @endif

                                    </td>
                    
                    <td class="unitdlt" data-id="{{ $process_paramiter->id}}" style="text-align:center;cursor: pointer;color:green;" >
                        <u>{{ $process_paramiter->hr_unit_name}}</u>
                        
                        <br>
                         E( {{ $process_paramiter->Active_emp}})
                       
                       <br>
                      P( {{ $process_paramiter->Process_emp}})
                       <br>
                         @if ($process_paramiter->approve_status=='S')
                            <p>(Submit for Approval)</p> 
                         @endif
                          @if ($process_paramiter->approve_status=='Y')
                            <p>(Approve)</p> 
                         @endif
                        @if ($process_paramiter->approve_status=='Y' and $process_paramiter->audit_status=='S')
                            <p>(Audit Submit)</p> 
                         @endif
                           @if ($process_paramiter->approve_status=='Y' and $process_paramiter->audit_status=='A')
                            <p>(Audit Pass)</p> 
                         @endif

                    </td>


                                    <td style="text-align:left;">{{ $process_paramiter->Employee_status}}</td>

                                    <td style="text-align:center;">{{ $process_paramiter->salary_from_date}}
                                        <br>To 
                                        <br>{{ $process_paramiter->salary_to_date}}
                                    </td>
                           
                                    <td style="text-align:center;">{{ $process_paramiter->ot_give_status}}</td>

                              
                                    <td style="text-align:center;">{{ $process_paramiter->ot_from_date}}
                                        <br>To 
                                        <br>{{ $process_paramiter->ot_to_date}}
                                    </td>


                                    <td style="text-align:Right;">{{ $process_paramiter->salary_below}}</td>
                                    <td style="text-align:left; width:100px;">{{ $process_paramiter->location_name}} 
                                        @if($process_paramiter->coment!='')<br>
                                       <P style="color:red">({{ $process_paramiter->coment}})</P> @endif
                                    </td>

                                    <td style="text-align:left;width:80px;">{{ $process_paramiter->area_name}}</td>
                                   <td style="text-align:center;" > 
                                    
                            {{-- process Button start--}}

                                   <div>
                                  
                                    @if ($process_paramiter->audit_status=='N')
                                      <button class="btn btn-primary nextBtn btn-sm pull-center Process"  data-toggle='tooltip' data-placement='top' title='' data-original-title='Process Salary' type="submit" id="Process" data-id="{{ $process_paramiter->id}}"  value="Process"><i class="fa fa-rocket"></i></button>
                                    @endif

                                    @if ($process_paramiter->audit_status=='S')
                                      <button class="btn btn-primary nextBtn btn-sm pull-center " disabled  data-toggle='tooltip' data-placement='top' title='' data-original-title='Salary Lock' type="submit" id=""   value=""><i class="fa fa-rocket"></i></button>
                                    @endif


                                   </div>
                           {{-- process Button end--}}
                                    <div>
                                {{-- delete Button start--}}
                                     @if ($process_paramiter->approve_status=='N')
                                        
                                        <button class="btn btn-danger nextBtn btn-sm pull-center btn_delete" data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete Process' type="submit"  id="btn_delete" data-id="{{ $process_paramiter->id}}"  value="delete"><i class="fa fa-trash"></i></button> 
                                    @endif
                                {{-- delete Button end--}}
                                   </div>
                        
                                    </td>
                                    <td style="text-align:center;"> 
                                   {{-- submit for approval  Button start--}}
                                   
                                    @if ($process_paramiter->approve_status=='N' and $process_paramiter->Process_emp >0)
                                     <button class="btn btn-primary nextBtn btn-sm pull-center Approval" type="submit" data-toggle='tooltip' data-placement='top' title='' data-original-title='Submit For Approval' id="Approval" data-id="{{ $process_paramiter->id}}"  value="Approval"><i class="fa fa-check-square-o"></i></button>

                                    @else
                                     <button class="btn btn-primary nextBtn btn-sm pull-center Approval" type="" data-toggle='tooltip' data-placement='top' title='' data-original-title='Pending For Approval' id="Approval"  value="Approval" disabled ><i class="fa fa-check-square-o"></i></button>
                                    @endif
                                  {{-- submit for approval  Button end--}}

                                     <br>

                                   {{-- SALARY LOCK  Button start--}}
                                   
                                    @if ($process_paramiter->audit_status=='N' and $process_paramiter->Process_emp >0 and $process_paramiter->approve_status=='Y')
                                     <button class="btn btn-warning nextBtn btn-sm pull-center locksalary" type="submit" data-toggle='tooltip' data-placement='top' title='' data-original-title='Submit Salary Lock for Audit' id="locksalary" data-id="{{ $process_paramiter->id}}"  value="locksalary"><i class="fa fa-check-square-o"></i></button>

                                    @else
                                     <button class="btn btn-warning nextBtn btn-sm pull-center locksalary" type="" data-toggle='tooltip' data-placement='top' title='' data-original-title='Salary Lock' id="locksalary"  value="locksalary" disabled ><i class="fa fa-check-square-o"></i></button>
                                    @endif
                                     {{-- SALARY LOCK  Button END--}}
                                     
                                    </td>

                                </tr>

                                
                                @endforeach
                                
                            </tbody>
                        </table>





