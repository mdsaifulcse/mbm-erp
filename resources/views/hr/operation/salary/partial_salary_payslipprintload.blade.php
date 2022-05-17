 
 <div>
 <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('salary-print')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
 </div>
&nbsp;&nbsp;



<div class="container-fluid" id="salary-print">

    

 @php
$pageKey = 0;  
$total_salary=0;
$total_employee=0;
$total_ot_hour=0;
$total_ot_amount=0;
$total_payble=0;
$total_stump=0;

$sl=0;

                        
@endphp

 @foreach($printload_date as $printload_date1)
@php 
$pageKey += 1;   
$total_salary_per_page =collect($printload_date1)->sum('salary_payable');
$total_employee_per_page=collect($printload_date1)->count('salary_payable');
$total_ot_amount_per_page=collect($printload_date1)->sum('ot_amount');


@endphp

                                
                  @foreach($printload_date1 as $printload_date2)
                                @php
                               $total_salary+=$printload_date2->salary_payable;
                               $total_employee +=1;
                               $total_ot_hour +=$printload_date2->ot_hour;
                               $total_ot_amount +=$printload_date2->ot_amount;
                               $total_payble +=$printload_date2->total_payable;
                               $total_stump +=$printload_date2->stamp;
                                $sl+=1;
                                @endphp

{{-- <div class="container-fluid"> --}}

                                <table style="width:100%;margin-bottom: 40px; ">
                    <tr>
                        <td colspan="2" style="">
                            <br>
                            <p style="">নামঃ <strong style="color:hotpink;">{{ $printload_date2->hr_bn_associate_name }}</strong></p>
                            <p style="">পদবীঃ <span style="color:hotpink;">{{ $printload_date2->hr_designation_name_bn}}</span></p>
                            
                            <p >যোগদানের তারিখঃ <span style="color:hotpink;">{{ eng_to_bn($printload_date2->as_doj) }}</span></p>
                        </td>
                         <td colspan="3" style="padding:5px;color:hotpink;text-align:center">
                            <h2 style="margin:0;text-align:center;font-weight:600;font-size:16px;">

                             @if ($input['printtype']=='T')
                                       #####
                                     @endif
                                     {{$unit_name}}
                                     @if ($input['printtype']=='T')
                                       Test Print #######
                                     @endif
                                </h2>
                            <h3 style="font-size: 14px;margin: 0">পে-স্লিপঃ <strong>{{ $month_year }}</strong> </h3>
                           <p style="color:black;">
                                <font style="font-size: 12px;">আইডি  <strong>{{ $printload_date2->associate_id }}</strong>  
                                </font> (পূর্বের আইডি: {{ $printload_date2->as_oracle_code }} )
                           </p>

                            
                        </td>
                        <td colspan="2" style="padding:5px;">

                            <p style="text-align: right;">
                                
                                <span style="border-radius:50%;width: 40px;height: 40px;border:1px solid #999;color:#999;line-height: 40px;text-align:center;display: inline-block;">{{eng_to_bn($sl)}}</span>
                            </p>
                  
                                    <p>গ্রেডঃ <span style="color:hotpink;">{{ eng_to_bn($printload_date2->hr_designation_grade)}}</span></p>
                
                        </td>
                    </tr>
                    <tr>
                        <!-- first -->
                        <td  style="width: 13%;">
                            <p><br></p>
                            <p>উপস্থিত দিবস</p>
                            <p>ছুটি দিবস</p>
                            <p style="margin:0;padding:0k">অনুপস্থিত দিবস</p>
                            <p>ছুটি মঞ্জুর</p>
                            <p style="border-top:1px solid #999">মোট দেয়</p>
                            <p style="">বিলম্ব উপস্থিতি</p>
                            @if($printload_date2->as_ot>0)
                            <p>মোট অতিরিক্ত দেয় </p>
                            @endif
                        </td>
                        
                        <td style="width: 10%; text-align: right;color:hotpink;">
                            <p><br></p>
                            <p>&nbsp;&nbsp;&nbsp;{{ eng_to_bn($printload_date2->present) }} দিন</p>
                            <p>&nbsp;&nbsp;&nbsp;{{ eng_to_bn($printload_date2->holiday) }} দিন</p>
               
                            <p style="margin:0;padding:0k">&nbsp;&nbsp;&nbsp;
                                {{ eng_to_bn($printload_date2->absent) }} দিন
                            </p>
                       
                            <p>&nbsp;&nbsp;&nbsp;{{ eng_to_bn($printload_date2->leave) }} দিন</p>
                            <p style="border-top:1px solid #999">&nbsp;&nbsp;&nbsp;{{ eng_to_bn($printload_date2->present + $printload_date2->holiday + $printload_date2->leave)}} দিন</p>
                            <p style="">&nbsp;&nbsp;&nbsp;{{ eng_to_bn($printload_date2->late_count) }} দিন</p>
               
                            <p>{{ eng_to_bn($printload_date2->ot_hour) }} ঘন্টা </p>
                           
                        </td>
                       
                        <td style="width: 20%;padding-left: 15px; ">
                            <p></p>
                            <p>মূল বেতন</p>
                            <p>বাড়ী বাড়া (৫০%)</p>
                            <p>চিকিৎসা ভাতা</p>
                            <p>যাতায়াত</p>
                            <p>খাদ্য</p>
                            <p style="border-top:1px solid #999">মোট মজুরি</p>
            
                            <p>অতিরিক্ত কাজের মজুরী হার </p>
                          
                        </td>



                        <td style="width: 9%;text-align: right;color:hotpink;">
                            <p></p>
                            <p>
                               {{ eng_to_bn($printload_date2->basic) }}
                           </p>
                            <p>
                                {{ eng_to_bn($printload_date2->house) }}
                            </p>
                            <p>
                            {{ eng_to_bn($printload_date2->medical) }}
                            </p>
                            <p> {{ eng_to_bn($printload_date2->transport) }}
                            </p>
                            <p> {{ eng_to_bn($printload_date2->food) }}
                            </p>
                            <p style="border-top:1px solid #999"> {{ eng_to_bn($printload_date2->basic+$printload_date2->house+$printload_date2->medical+$printload_date2->transport+$printload_date2->food) }}
                            </p>
             
                            <p > {{ eng_to_bn($printload_date2->ot_rate) }} </p>
                  
                        </td>
                        <td style="width: 32%;padding-left: 15px; ">
                            <p>অগ্রিম গ্রহণ/ভোগ্যপণ্য ক্রয়/অন্যান্য কর্তন</p>
                            <p>প্রদেয় মজুরি&nbsp;&nbsp;&nbsp;</p>
                            <p>অতিরিক্ত কাজের মজুরি &nbsp;&nbsp;&nbsp;</p>
                            <p>হাজিরা বোনাস&nbsp;&nbsp;&nbsp;</p>
                            <p>প্রোডাকশন বোনাস&nbsp;&nbsp;&nbsp;</p>
                            <p>মজুরি সমন্বয়&nbsp;&nbsp;&nbsp;
                               
                                    ( {!! eng_to_bn(0) !!})
                               
                            </p>
                           
                            <p style="border-top:1px solid #999">মোট প্রদেয়&nbsp;&nbsp;&nbsp;</p>
                        </td>
                      
                          <td  style="width: 12%;text-align: right; color:hotpink;" >

               
                                <p>&nbsp;&nbsp;&nbsp;{{ eng_to_bn(0) }} =/টঃ</p>
                            <p>&nbsp;&nbsp;&nbsp;{{ eng_to_bn($printload_date2->salary_payable) }} =/টঃ</p>
                            {{-- <p>&nbsp;&nbsp;&nbsp;{{ eng_to_bn(5555) }} =/টঃ</p> --}}
                            <p>&nbsp;&nbsp;&nbsp;{{ eng_to_bn($printload_date2->ot_amount) }} =/টঃ</p>
                            <p>&nbsp;&nbsp;&nbsp;{{ eng_to_bn(0) }} =/টঃ</p>
                            <p>&nbsp;&nbsp;&nbsp;{{ eng_to_bn(0) }} =/টঃ</p>
                            <p>&nbsp;&nbsp;&nbsp;{{ eng_to_bn(0) }} =/টঃ</p>
                      
                                

                            <p style="border-top:1px solid #999">&nbsp;&nbsp;&nbsp; {{ eng_to_bn(bn_money($printload_date2->total_payable)) }}=/টঃ</p>
                        </td> 
                    </tr>

                     </table>
                      
                                @endforeach
              
                       
 
                                    <div style="page-break-before: always !important;"></div>
                           
                                 @endforeach

{{-- </div>     --}}

        @if($pageKey>0) 
        <div id="unit-info">
            <table class="table" style="width:100%;border-bottom:1px solid #ccc;margin-bottom:0;font-size:12px;color:lightseagreen;text-align:left" cellpadding="5">
                <tr>
                    <td style="width:25%">
                        <p style="margin:0;padding: 0"><strong>মোট কর্মী/কর্মচারীঃ </strong>
                            {{ Custom::engToBnConvert($total_employee) }}
                        </p>
                        <p style="margin:0;padding: 0"><strong>স্ট্যাম্প বাবদঃ </strong>
                            {{ Custom::engToBnConvert(bn_money($total_stump)) }}
                        </p>
                        <p style="margin:0;padding: 0"><strong>অতিরিক্ত কাজের সময়: </strong>
                            {{ Custom::engToBnConvert(numberToTimeClockFormat($total_ot_hour)) }}
                        </p>
                    </td>
                    <td style="width:25%;">
                        <p style="margin:0;padding: 0"><strong>সর্বমোট বেতন/মজুরী: </strong>
                            {{ Custom::engToBnConvert(bn_money($total_salary)) }}
                        </p>
                        <p style="margin:0;padding: 0"><strong>অতিরিক্ত কাজের মজুরী: </strong>
                            {{ Custom::engToBnConvert(bn_money($total_ot_amount)) }}
                        </p>
                      
                    </td>
                    
                    <td style="width:25%; text-align:right;">
                        <p style="margin:0;padding: 0"><strong>সর্বমোট টাকার পরিমানঃ </strong>
                            {{ Custom::engToBnConvert(bn_money($total_payble)) }}
                        </p>
                    </td>
                    
                </tr>
            </table>
        </div>
    @endif
   @if($pageKey==0) 
   <br>
   <br>No Record Found..
   @endif

                        
</div>

