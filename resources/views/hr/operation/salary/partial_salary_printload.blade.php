 
 <div>
 <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('salary-print')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
 </div>
&nbsp;&nbsp;

  <div>
         @php
        $urldata = http_build_query($input) . "\n";
        @endphp

        <a href='{{ url("hr/operation/partial-salary-partsalaryexcel?$urldata")}}' target="_blank" class="btn btn-sm btn-info" id="excelsss" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" ><i class="fa fa-file-excel-o"></i> </a>
 </div>

 


<div id="salary-print">

 @php
$pageKey = 0;  
$total_salary=0;
$total_employee=0;
$total_ot_hour=0;
$total_ot_amount=0;
$total_payble=0;
$total_stump=0;
 $sl=0;
// dd($input);
@endphp

 @foreach($printload_date as $printload_date1)
@php 
$pageKey += 1;   
$total_salary_per_page =collect($printload_date1)->sum('salary_payable');
$total_employee_per_page=collect($printload_date1)->count('salary_payable');
$total_ot_amount_per_page=collect($printload_date1)->sum('ot_amount');


@endphp
<div calss="container">
    <table class="table" style="width:100%;border-bottom:1px solid #ccc;margin-bottom:0;font-size:12px;color:lightseagreen;text-align:left" cellpadding="5">
        <tr>
            <td style="width:14%">
                <p style="margin:0;padding: 0"><strong>লোকেশনঃ </strong>
                    {{$location_name[$input['location']]??'ALL'}}  
                </p>

                <p style="margin:0;padding: 0">
                    @if(!empty($input['subSection']))
                    <strong>সাব-সেকশন:</strong>  {{$subsection_by_id}}
                    @elseif(!empty($input['section']))
                    <strong>সেকশন: </strong> {{$section_by_id}}
                    @elseif(!empty($input['department']))
                    <strong>ডিপার্টমেন্ট: </strong> {{$department_by_id}}
                    @elseif(!empty($input['area']))
                    <strong>এরিয়া: </strong>  {{$area_by_id}}
                   {{--  @elseif(!empty($input['floor']))
                    <strong>এরিয়া: </strong>  {{$area_by_id}}
                    @elseif(!empty($input['line']))
                    <strong>এরিয়া: </strong>  {{$area_by_id}} --}}
                    @endif
                </p>
                <p style="margin:0;padding: 0"><strong>&nbsp;পৃষ্ঠা নংঃ </strong>
                    {{ Custom::engToBnConvert($pageKey) }}
                </p>
            </td>
            <td style="width:15%;font-size:10px">
                <p style="margin:0;padding: 0"><strong>&nbsp;</strong>
                </p>
            </td>
            <td>
                <h3 style="margin:4px 10px;text-align:center;font-weight:600;font-size:14px;">
                    @if ($input['printtype']=='T')
                    #####
                    @endif
                    {{$unit_name}}
                    @if ($input['printtype']=='T')
                    Test Print #######
                    @endif
                </h3>
                <h5 style="margin:4px 10px;text-align:center;font-weight:600;font-size:11px;">
                    {{-- বেতন/মজুরী এবং অতিরিক্ত সময়ের মজুরী --}}
                     {{-- অগ্রীম  --}}
                     বেতন/মজুরী (১৫ দিন)
                    <br/>
                    মাসঃ {{$month_year}}
                    @if ($input['paymentType']=='cash')
                    &nbsp; - ক্যাশ পে
                    @endif
                    @if ($input['paymentType']=='rocket')
                    &nbsp; - ব্যাংক পে
                    @endif
                    @if ($input['paymentType']=='dbbl')
                    &nbsp; - ব্যাংক পে
                    @endif
                </h5>
            </td>
            <td width="0%"> &nbsp;</td>
            <td style="width:30%" style="text-align: right;">
                <p style="margin:0;padding: 0;text-align: right;">
                    সর্বমোট টাকার পরিমানঃ <span style="color:hotpink" >{{Custom::engToBnConvert(bn_money($total_salary_per_page))}}</span>
                </p>
                {{-- $list_total_ot = array_column(array_column($lists, 'salary'),'ot_rate'); --}}
                <p style="margin:0;padding: 0;text-align: right;">
                    মোট কর্মী/কর্মচারীঃ <span style="color:hotpink" >{{Custom::engToBnConvert($total_employee_per_page)}}</span>
                </p>
                <p style="margin:0;padding: 0;text-align: right;">
                    অতিরিক্ত কাজের মজুরীঃ <span style="color:hotpink" id="">{{Custom::engToBnConvert(bn_money($total_ot_amount_per_page))}}</span>
                </p>
            </td>
        </tr>
    </table>


</div>
<table class="table table-head" style="width:100%;border:1px solid #ccc;font-size:9px;color:lightseagreen" cellpadding="2" cellspacing="0" border="1" align="center">
                            <thead >
                   
                                     <tr style="color:hotpink">
                                    <th style="color:lightseagreen">ক্রমিক নং</th>
                                    <th width="180" style="width: 225px;">কর্মী/কর্মচারীদের নাম
                                        <br/> ও যোগদানের তারিখ</th>
                                    <th>আই ডি নং</th>
                                    <th>মাসিক বেতন/মজুরী</th>
                                    <th width="140">হাজিরা দিবস</th>
                                    <th width="180">বেতন হইতে কর্তন </th>
                                    <th width="250">মোট দেয় টাকার পরিমান</th>
                                    <th>সর্বমোট টাকার পরিমান</th>
                                    <th width="80">দস্তখত</th>
                                    {{-- <th class="disburse-button" width="80">বিতরণ</th> --}}
                                </tr>
                              
                            </thead>
                            <tbody>
                                @php
                                // $sl=0;

                                @endphp
                               
                                
                                @foreach($printload_date1 as $printload_date2)
                                @php
                               $total_salary+=$printload_date2->salary_payable;
                               $total_employee +=1;
                               $total_ot_hour +=$printload_date2->ot_hour;
                               $total_ot_amount +=$printload_date2->ot_amount;
                               $total_payble +=$printload_date2->total_payable;
                               $total_stump +=$printload_date2->stamp;
                                @endphp
                               <tr>

                                <tr>
                                <td style="text-align: center;">{{ Custom::engToBnConvert(++$sl) }}</td>
                        
                                <td>
                                    <p style="margin:0;padding:0;">{{ $printload_date2->hr_bn_associate_name }}</p>
                                    <p style="margin:0;padding:0;">{{ Custom::engToBnConvert(date('Y-m-d', strtotime($printload_date2->as_doj))) }}</p>
                                    <p style="margin:0;padding:0;">
                                        {{ $printload_date2->hr_designation_name_bn}}
                                 
                                        - {{ $printload_date2->hr_section_name_bn}}
                                    </p>
                                    <p style="margin:0;padding:0;color:hotpink">মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য </p>
                                    <p style="margin:0;padding:0;">
                                        {{ Custom::engToBnConvert($printload_date2->basic.'+'.$printload_date2->house.'+'.$printload_date2->medical.'+'.$printload_date2->transport.'+'.$printload_date2->food) }}
                                    </p>
                                    <p style="margin:0;padding:0;color:hotpink">
                                        {{ $printload_date2->salary}}
                                    </p>
                                </td>



                                <td>
                                    <p style="font-size:14px;margin:0;padding:0;color:blueviolet">
                                        {{ $printload_date2->associate_id }}
                                    </p>
                                    পূর্বের আইডিঃ 
                                    <p style="font-size:11px;margin:0;padding:0;color:blueviolet">
                                        {{ $printload_date2->as_oracle_code }}
                                    </p>
                                    <p style="margin:0;padding:0;color:hotpink">
                                        বিলম্ব উপস্থিতিঃ {{ Custom::engToBnConvert($printload_date2->late_count) }}
                                    </p>

                                    <p style="margin:0;padding:0">গ্রেডঃ {{ eng_to_bn($printload_date2->hr_designation_grade)}}</p>
                                   
                                </td>

                                <td>
                                <p style="margin:0;padding:0;text-align: center;">
                                                    {{ Custom::engToBnConvert($printload_date2->gross) }}
                                </p>
                                </td>




                    <td>
                    <p style="margin:0;padding:0">
                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">উপস্থিত দিবস
                    </span>
                    <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                    </span>
                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                    <font style="color:hotpink;" > {{ Custom::engToBnConvert($printload_date2->present) }}</font>
                    </span>

                    </p>
                    <p style="margin:0;padding:0">
                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">সরকারি ছুটি </span>
                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                    <font style="color:hotpink"> {{ Custom::engToBnConvert($printload_date2->holiday) }}</font>
                    </span>
                    </p>
                    <p style="margin:0;padding:0k">
                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অনুপস্থিত দিবস </span>
                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                    <!--
                    HR Requirements Applicable from April
                    2021/05/05 
                    -->
                   
                    <font style="color:hotpink"> {{ Custom::engToBnConvert($printload_date2->absent) }}</font>
                   
                    </span>
                    </p>
                    <p style="margin:0;padding:0">
                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ছুটি মঞ্জুর </span>
                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink"> {{ Custom::engToBnConvert($printload_date2->leave) }}</font>
                    </span>

                    </p>
                    <p style="margin:0;padding:0">

                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">মোট দেয় </span>
                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink"> {{ Custom::engToBnConvert($printload_date2->present + $printload_date2->holiday + $printload_date2->leave)}}</font>
                    </span>
                    </p>
                    </td>



                    <td>

                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অনুপস্থিতির জন্য</span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">{{  Custom::engToBnConvert(number_format($printload_date2->absent_deduct, 2)) }}</font>
                                                    </span>
                                                </p>
                                          
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অগ্রিম গ্রহণ বাবদ </span>
                                                    <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                                    </span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                                                      
                                                            {{ Custom::engToBnConvert('0.00') }}
                                                      
                                                        
                                                    </font>
                                                    </span>

                                                </p>
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">স্ট্যাম্প বাবদ </span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                        <font style="color:hotpink">{{ Custom::engToBnConvert(number_format($printload_date2->stamp,2)??0) }}</font>
                                                    </span>
                                                </p>
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ভোগ্যপণ্য ক্রয় </span>
                                                    <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                                    </span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                                                        
                                                            {{ Custom::engToBnConvert('0.00') }}
                                                      
                                                        
                                                    </font>
                                                    </span>
                                            
                                            
                                                <p style="margin:0;padding:0">
                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অন্যান্য </span>
                                                    <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                                    </span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                                                 
                                                            {{ Custom::engToBnConvert('0.00') }}
                                                   
                                                    </font>
                                                    </span>

                                                </p>
                                            </td>





                                             <td>
                                                <p style="margin:0;padding:0">

                                                      <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">বেতন/মজুরী </span>
                                                      <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                      <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                            <font style="color:hotpink"> {{ Custom::engToBnConvert(number_format($printload_date2->salary_payable,2)) }}</font>
                                                     </span>
                                                </p>
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অতিরিক্ত সময়ের কাজের মজুরী </span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                        <font style="color:hotpink">{{ Custom::engToBnConvert(number_format($printload_date2->ot_amount,2)) }}</font>
                                                    </span>
                                                </p>
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অতিরিক্ত কাজের মজুরী হার </span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                    <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                        <font style="color:hotpink">{{ Custom::engToBnConvert(number_format($printload_date2->ot_rate,2)) }} </font>
                                                    </span>
                                                    
                                                </p>
                                                <p style="margin:0;padding:0">
                                                    @if($printload_date2->as_ot>0)
                                                    <span style="text-align: right;width: 100%; float: right;  white-space: wrap;">
                                                        <font style="color:hotpink"> ({{ $printload_date2->as_ot==1?Custom::engToBnConvert($printload_date2->ot_hour):Custom::engToBnConvert('00') }}  ঘন্টা)</font>
                                                    </span>
                                                    @endif
                                                </p>
                                                
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">উপস্থিত বোনাস </span>
                                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                            <font style="color:hotpink">{{ Custom::engToBnConvert(number_format('00')) }}</font>
                                                        </span>
                                                </p>
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">প্রোডাকশন বোনাস </span>
                                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                                            <font style="color:hotpink">{{ Custom::engToBnConvert(number_format('00')) }}</font>
                                                        </span>
                                                </p>
                                                
                                                <p style="margin:0;padding:0">

                                                    <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">
                                                        বেতন/মজুরী অগ্রিম/সমন্বয় 
                                                        <font style="color:hotpink">{{ Custom::engToBnConvert(number_format('00')) }}</font>
                                                    
                                                    </span>
                                                    <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                                    </span>
                                                </p>
                                            </td>


                                <td>
                                <p style="padding:0; text-align:center;">
                                    <font style="color:hotpink"> {{ Custom::engToBnConvert(bn_money($printload_date2->total_payable))}}</font>
                                </p>
                                </td>

                                <td>
                                   
                                </td>

                              </tr>
                 
                    
                                @endforeach
                              

                         </tbody>
                        </table>
                                    {{-- <div style="page-break-before: always !important;"></div> --}}
                                     {{-- aminul --}}

                                    <div class="page-break page-break-{{$pageKey}}">
                                        <style type="text/css">
                                            .page-break-{{$pageKey}}{
                                                page-break-after: always; !important;
                                            }
                                        </style>    
                                    </div>


                                 @endforeach
   <style type="text/css">
        .page-break-{{$pageKey}}{
            page-break-after: avoid; !important;
        }
    </style> 


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