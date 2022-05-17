
<button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('salary-print')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
<div id="salary-print">
    <style>
        @media print {
            
            .pagebreak {
                page-break-before: always !important;
            }
            .disburse-button{
                display: none;
            }
        }
    </style>
    @php
        $loc_count = 0;
        $total_emp = 0;
        $total_sal = 0;
        $salmonth = date_to_bn_month($input['year_month']);
        $month = date('m', strtotime($input['year_month'].'-01'));
        $year = date('Y', strtotime($input['year_month'].'-01'));
        $totalPayable = 0;
        $attendanceBonus = 0;
        $sl = 0;
    @endphp

    @php $pageno = 0; $tEmp = 0; $tStamp = 0; $tBonus = 0;@endphp
    @foreach($salaryList as $u => $unitList)
        <!-- lcation loop -->
        @foreach($unitList as $l => $locList)
            <!-- perage 10 employee -->
            @foreach($locList as $key => $page)
                @php ++$pageno; @endphp
                <table class="table" style="width:100%;border-bottom:1px solid #ccc;margin-bottom:0;font-size:12px;color:lightseagreen;text-align:left" cellpadding="5">
                    <tr>
                        <td style="width:14%">
                            <p style="margin:0;padding: 0"><strong>লোকেশনঃ </strong>
                                {{ $location[$l]['hr_location_name']??'' }}
                            </p>
                            <p style="margin:0;padding: 0">
                                @if(isset($input['subSection']) && !empty($input['subSection']))
                                    <strong>সাব-সেকশন:</strong> {{ $subSection[$input['subSection']]['hr_subsec_name_bn']??'' }}
                                @elseif(isset($input['section']) && !empty($input['section']))
                                    <strong>সেকশন: </strong> {{ $section[$input['section']]['hr_section_name_bn']??'' }}
                                @elseif(isset($input['department']) && !empty($input['department']))
                                    <strong>ডিপার্টমেন্ট: </strong> {{ $department[$input['department']]['hr_department_name_bn']??'' }}
                                @elseif(isset($input['area']) && !empty($input['area']))
                                    <strong>এরিয়া: </strong> {{ $area[$input['area']]['hr_area_name_bn']??'' }}
                                @endif
                            </p>
                            
                            @if(isset($input['perpage']) && $input['perpage'] > 1)
                            <p style="margin:0;padding: 0"><strong>&nbsp;পৃষ্ঠা নংঃ </strong>
                                {{eng_to_bn($pageno)}}
                            </p>
                            @endif
                        </td>
                        
                        <td>
                            <h3 style="margin:4px 10px;text-align:center;font-weight:600;font-size:14px;">
                                {{ $unit[$u]['hr_unit_name_bn']??'' }}
                            </h3>
                            <h5 style="margin:4px 10px;text-align:center;font-weight:600;font-size:11px;">
                                বেতন/মজুরী এবং অতিরিক্ত সময়ের মজুরী
                            
                                <br/>
                                মাসঃ {{ $salmonth }}
                                @if(isset($input['pay_status']) && $input['pay_status'] != null)
                                    @if($input['pay_status'] == 'cash')
                                    - ক্যাশ পে
                                    @else
                                    - ব্যাংক পে
                                    @endif
                                @endif
                            </h5>
                        </td>
                        <td width="0%"> &nbsp;</td>
                        <td style="width:30%" style="text-align: right;">
                            @if(isset($input['floor_id']) && $input['floor_id'] != null)
                            <p style="margin:0;padding: 0;">
                                <strong>ফ্লোর নংঃ
                                    {{ $floor[$input['floor_id']]['hr_floor_name_bn']??'' }}
                                </strong>
                            </p>
                            @endif
                            @if(isset($input['perpage']) && $input['perpage'] > 0)
                            <p style="margin:0;padding: 0;text-align: right;">
                                সর্বমোট টাকার পরিমানঃ <span style="color:hotpink" >{{eng_to_bn(bn_money(collect($page)->sum('total_payable')))}}</span>
                            </p>
                            
                            <p style="margin:0;padding: 0;text-align: right;">
                                মোট কর্মী/কর্মচারীঃ <span style="color:hotpink" >{{ eng_to_bn(collect($page)->count()) }}</span>
                            </p>
                            <p style="margin:0;padding: 0;text-align: right;">
                                অতিরিক্ত কাজের মজুরীঃ <span style="color:hotpink" id="">
                                    {{ eng_to_bn(bn_money(collect($page)->sum('ot_amount'))) }}
                                </span>
                            </p>
                            @endif
                        </td>
                    </tr>
                </table>
                
                <table class="table" style="width:100%;border:1px solid #ccc; font-size:12px !important; color: #2A86FF"  cellpadding="2" cellspacing="0" border="1" align="center"> 
                    <thead>
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
                            <th class="disburse-button" width="80">বিতরণ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($page as $key => $emp)
                            <tr>
                                @php
                                    $empBanglaName = $empBangla[$emp->as_id]??'';
                                    ++$sl;
                                @endphp
                                <td style="text-align: center;">{{ eng_to_bn($sl) }}</td>
                                <td>
                                    <p style="margin:0;padding:0;">{{ $empBanglaName }}</p>
                                    <p style="margin:0;padding:0;">{{ !empty($emp->as_doj)?(eng_to_bn(date('d-m-Y', strtotime($emp->as_doj)))):'' }}</p>
                                    <p style="margin:0;padding:0;">
                                        {{$designation[$emp->as_designation_id]['hr_designation_name_bn']}}
                                        @if($emp->ot_status == 0)
                                        - {{ $getSection[$emp->as_section_id]['hr_section_name_bn']??''}}
                                        @endif 
                                    </p>
                                    <p style="margin:0;padding:0;color:hotpink">মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য </p>
                                    <p style="margin:0;padding:0;">
                                        {{ eng_to_bn($emp->basic.'+'.$emp->house.'+'.$emp->medical.'+'.$emp->transport.'+'.$emp->food) }}
                                    </p>
                                    <p style="margin:0;padding:0;color:hotpink">
                                       {{ $salmonth}}
                                    </p>
                                </td>
                                <td>
                                    <p style="font-size:14px;margin:0;padding:0;color:blueviolet">
                                        {{ $emp->as_id }}
                                    </p>
                                    পূর্বের আইডিঃ 
                                    <p style="font-size:11px;margin:0;padding:0;color:blueviolet">
                                        {{ $emp->as_oracle_code }}
                                    </p>
                                    <p style="margin:0;padding:0;color:hotpink">
                                        বিলম্ব উপস্থিতিঃ {{ eng_to_bn($emp->late_count) }}
                                    </p>
                                    @if(isset($designation[$emp->as_designation_id]))
                                        @if($designation[$emp->as_designation_id]['hr_designation_grade'] > 0 || $designation[$emp->as_designation_id]['hr_designation_grade'] != null)
                                        <p style="margin:0;padding:0">গ্রেডঃ {{ eng_to_bn($designation[$emp->as_designation_id]['hr_designation_grade'])}}</p>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <p style="margin:0;padding:0;text-align: center;">
                                        {{ eng_to_bn($emp->gross) }}
                                        @if(isset($salaryIncrement[$emp->as_id]) && $salaryIncrement[$emp->as_id] != null)
                                           <br>

                                           <p style="font-size:11px;margin:0;padding:0;color:blueviolet">বর্ধিত বেতন:</p>
                                            <p style="font-size:11px;margin:0;padding:0;color:blueviolet">
                                                {{ Custom::engToBnConvert($salaryIncrement[$emp->as_id]->increment_amount??'0.00') }}
                                            </p>
                                        @endif
                                    </p>
                                </td>
                                <td>
                                    <p style="margin:0;padding:0">
                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">উপস্থিত দিবস
                                        </span>
                                        <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                        </span>
                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                            <font style="color:hotpink;" > {{ eng_to_bn($emp->present) }}</font>
                                        </span>

                                    </p>
                                    <p style="margin:0;padding:0">
                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">সরকারি ছুটি </span>
                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                            <font style="color:hotpink"> {{ eng_to_bn($emp->holiday) }}</font>
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
                                            @if(($input['year_month'] < '2021-04'))
                                              <font style="color:hotpink"> {{ eng_to_bn($emp->absent + $emp->leave) }}</font>
                                            @else
                                               <font style="color:hotpink"> {{ eng_to_bn($emp->absent) }}</font>
                                            @endif
                                        </span>
                                    </p>
                                    <p style="margin:0;padding:0">
                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ছুটি মঞ্জুর </span>
                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink"> {{ eng_to_bn($emp->leave) }}</font>
                                        </span>

                                    </p>
                                    <p style="margin:0;padding:0">

                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">মোট দেয় </span>
                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink"> {{ eng_to_bn($emp->present + $emp->holiday + $emp->leave)}}</font>
                                        </span>
                                    </p>
                                </td>
                                <td>
                                    <p style="margin:0;padding:0">
                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অনুপস্থিতির জন্য</span>
                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">{{  eng_to_bn(number_format($emp->absent_deduct, 2)) }}</font>
                                        </span>
                                    </p>
                                    @if($emp->half_day_deduct > 0)
                                    <p style="margin:0;padding:0">

                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অর্ধ দিবসের জন্য কর্তন </span>
                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                            <font style="color:hotpink">{{ eng_to_bn(number_format($emp->half_day_deduct,2)) }}</font>
                                        </span>
                                    </p>
                                    @endif
                                    <p style="margin:0;padding:0">

                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অগ্রিম গ্রহণ বাবদ </span>
                                        <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                        </span>
                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                                            @if(array_key_exists($emp->as_id, $salaryAddDeduct))
                                                {{ eng_to_bn(number_format($salaryAddDeduct[$emp->as_id]->advp_deduct,2)??'0.0') }}
                                            @else
                                                {{ eng_to_bn('0.00') }}
                                            @endif
                                            
                                        </font>
                                        </span>

                                    </p>
                                    <p style="margin:0;padding:0">

                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">স্ট্যাম্প বাবদ </span>
                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                            <font style="color:hotpink">{{ eng_to_bn(number_format($emp->stamp,2)??0) }}</font>
                                        </span>
                                    </p>
                                    <p style="margin:0;padding:0">

                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ভোগ্যপণ্য ক্রয় </span>
                                        <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                        </span>
                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                                            @if(array_key_exists($emp->as_id, $salaryAddDeduct))
                                                {{ eng_to_bn(number_format($salaryAddDeduct[$emp->as_id]->cg_deduct,2)??'0.0') }}
                                            @else
                                                {{ eng_to_bn('0.00') }}
                                            @endif
                                            
                                        </font>
                                        </span>
                                    </p>
                                    @if($emp->as_location == 7)
                                        
                                        <p style="margin:0;padding:0">
                                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">খাবার বাবদ কর্তন </span>
                                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                            </span>
                                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                                                @if(array_key_exists($emp->as_id, $salaryAddDeduct))
                                                {{ eng_to_bn(number_format($salaryAddDeduct[$emp->as_id]->food_deduct,2)??'0.0') }}
                                                @else
                                                    {{ eng_to_bn('0.00') }}
                                                @endif
                                              </font>
                                            </span>
                                        </p>
                                        
                                    @endif
                                    <p style="margin:0;padding:0">
                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অন্যান্য </span>
                                        <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                        </span>
                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                                            @if(array_key_exists($emp->as_id, $salaryAddDeduct))
                                                {{ eng_to_bn(number_format($salaryAddDeduct[$emp->as_id]->others_deduct,2)??'0.0') }}
                                            @else
                                                {{ eng_to_bn('0.00') }}
                                            @endif
                                        </font>
                                        </span>

                                    </p>
                                </td>

                                @php
                                    
                                    $otHour = numberToTimeClockFormat($emp->ot_hour);
                                    $ot = ((float)($emp->ot_rate) * $emp->ot_hour);
                                    $ot = number_format((float)$ot, 2, '.', '');
                                    $totalPayable = $totalPayable + $emp->salary_payable;
                                    $attendanceBonus = $attendanceBonus + $emp->attendance_bonus;
                                @endphp
                                <td>
                                    <p style="margin:0;padding:0">

                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">বেতন/মজুরী </span>
                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                            <font style="color:hotpink"> {{ eng_to_bn(number_format($emp->salary_payable,2)) }}</font>
                                        </span>
                                    </p>
                                    <p style="margin:0;padding:0">

                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অতিরিক্ত সময়ের কাজের মজুরী </span>
                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                            <font style="color:hotpink">{{ eng_to_bn(number_format($ot,2)) }}</font>
                                        </span>
                                    </p>
                                    <p style="margin:0;padding:0">

                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অতিরিক্ত কাজের মজুরী হার </span>
                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                            <font style="color:hotpink">{{ eng_to_bn(number_format($emp->ot_rate,2)) }} </font>
                                        </span>
                                        
                                    </p>
                                    <p style="margin:0;padding:0">
                                        @if($emp->ot_status>0)
                                        <span style="text-align: right;width: 100%; float: right;  white-space: wrap;">
                                            <font style="color:hotpink"> ({{ $emp->ot_status==1?eng_to_bn($otHour):eng_to_bn('00') }}  ঘন্টা)</font>
                                        </span>
                                        @endif
                                    </p>
                                    
                                    <p style="margin:0;padding:0">

                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">উপস্থিত বোনাস </span>
                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                            <font style="color:hotpink">{{ eng_to_bn(number_format($emp->attendance_bonus,2)) }}</font>
                                        </span>
                                    </p>
                                    <p style="margin:0;padding:0">

                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">প্রোডাকশন বোনাস </span>
                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                            <font style="color:hotpink">{{ eng_to_bn(number_format($emp->production_bonus,2)) }}</font>
                                        </span>
                                    </p>
                                    
                                    <p style="margin:0;padding:0">

                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">
                                            বেতন/সমন্বয় 
                                            @if(isset($salaryAdjust[$emp->as_id][2]))
                                                ( {!! eng_to_bn($salaryAdjust[$emp->as_id][2]->days??'') !!})
                                            @endif
                                        </span>
                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                        </span>
                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                            <font style="color:hotpink">

                                                @php 
                                                    $salaryExtraAdd = 0;
                                                    if(array_key_exists($emp->as_id, $salaryAddDeduct)){
                                                    
                                                        $salaryExtraAdd = $salaryAddDeduct[$emp->as_id]->salary_add??0;
                                                    }
                                                    if(isset($salaryAdjust[$emp->as_id][2])){
                                                        $salaryExtraAdd += $salaryAdjust[$emp->as_id][2]->sum??0;
                                                    }
                                                    
                                                @endphp
                                                {{ eng_to_bn(number_format($salaryExtraAdd,2)??'0.0') }}
                                                
                                            </font>
                                        </span>

                                    </p>
                                    <p style="margin:0;padding:0">

                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">
                                            মজুরী অগ্রিম 
                                            
                                        </span>
                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                        </span>
                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                            <font style="color:hotpink">

                                                @php 
                                                    $salaryAdjustDeduct = 0;
                                                    if(isset($emp->partial_amount) && $emp->partial_amount > 0){
                                                        $salaryAdjustDeduct += $emp->partial_amount??0;
                                                    }
                                                @endphp
                                                {{ eng_to_bn(number_format($salaryAdjustDeduct,2)??'0.0') }}
                                                
                                            </font>
                                        </span>

                                    </p>
                                    @if(isset($salaryAdjust[$emp->as_id][3]))
                                    <p style="margin:0;padding:0">

                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">বর্ধিত বেতন সমন্বয়</span>
                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                        </span>
                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                            <font style="color:hotpink">
                                                {{ eng_to_bn(number_format($salaryAdjust[$emp->as_id][3]->sum, 2) ??'0.00') }}
                                            </font>
                                        </span>

                                    </p>
                                    @endif
                                    @if(isset($salaryAdjust[$emp->as_id][4]))
                                    <p style="margin:0;padding:0">

                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">বর্ধিত বোনাস সমন্বয়</span>
                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                        </span>
                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                            <font style="color:hotpink">
                                                {{ eng_to_bn(number_format($salaryAdjust[$emp->as_id][4]->sum, 2) ??'0.00') }}
                                            </font>
                                        </span>

                                    </p>
                                    @endif

                                    <p style="margin:0;padding:0">

                                        <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ছুটি সমন্বয়</span>
                                        <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                        </span>
                                        <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                            <font style="color:hotpink">{{ eng_to_bn(number_format($emp->leave_adjust,2)) }}</font>
                                        </span>

                                    </p>
                                </td>
                                <td style="text-align: center;">
                                    
                                    {{ eng_to_bn(bn_money($emp->total_payable)) }}
                                    @if(isset($input['pay_status']) && ($input['pay_status'] == 'dbbl' || $input['pay_status'] == 'cash'))
                                        @if(isset($emp->pay_status) && $emp->pay_status == 3)
                                        <p style="margin:0;padding:0">

                                            <span style="text-align: left; width: 45%; float: left;  white-space: wrap;">ব্যাংক পে</span>
                                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                            </span>
                                            <span style="text-align: right;width: 50%; float: right;  white-space: wrap;">
                                                <font style="color:hotpink">{{ eng_to_bn(bn_money($emp->bank_payable)) }}</font>
                                            </span>

                                        </p>
                                        <p style="margin:0;padding:0">

                                            <span style="text-align: left; width: 45%; float: left;  white-space: wrap;">ক্যাশ পে</span>
                                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                                            </span>
                                            <span style="text-align: right;width: 50%; float: right;  white-space: wrap;">
                                                <font style="color:hotpink">{{ eng_to_bn(bn_money($emp->cash_payable)) }}</font>
                                            </span>

                                        </p>
                                        @endif
                                    @endif
                                </td>
                                <td></td>
                                <td class="disburse-button" id="{{ $sl }}-{{ $emp->as_id }}">
                                    @if($emp->disburse_date == null)
                                        <a data-id="{{ $sl }}-{{ $emp->as_id }}" class="btn btn-primary btn-sm disbursed_salary text-white" data-eaid="{{ $emp->as_id }}" data-date="{{ $input['year_month'] }}" data-month="{{ $month }}" data-year="{{ $year }}" data-name="{{ $empBanglaName }}" data-post="{{ $designation[$emp->as_designation_id]['hr_designation_name_bn']}}"  rel='tooltip' data-tooltip-location='top' data-tooltip='বেতন প্রদান করুন' > হয় নি </a>
                                    @else
                                        হ্যাঁ 
                                        <br>
                                        <b>{{ eng_to_bn($emp->disburse_date) }}</b>
                                    @endif
                                </td>
                                
                            </tr>
                            
                        @endforeach

                    </tbody>
                </table>
                <div class="page-break page-break-{{$pageno}}">
                <style type="text/css">
                    .page-break-{{$pageno}}{
                        page-break-after: always; !important;
                    }
                </style>    
                </div>
            @endforeach
        @endforeach
    @endforeach
    <style type="text/css">
        .page-break-{{$pageno}}{
            page-break-after: avoid; !important;
        }
    </style> 
    <div id="unit-info">
        <table class="table" style="width:100%;border-bottom:1px solid #ccc;margin-bottom:0;font-size:12px;color:lightseagreen;text-align:left" cellpadding="5">
            <tr>
                <td style="width:25%">
                    <p style="margin:0;padding: 0"><strong>মোট কর্মী/কর্মচারীঃ </strong>
                        {{ eng_to_bn(bn_money($sum->totalEmployees)) }}
                    </p>
                    <p style="margin:0;padding: 0"><strong>স্ট্যাম্প বাবদঃ </strong>
                        {{ eng_to_bn(bn_money($sum->totalStamp)) }}
                    </p>
                    <p style="margin:0;padding: 0"><strong>অতিরিক্ত কাজের সময়: </strong>
                        {{ eng_to_bn(numberToTimeClockFormat($sum->totalOtHour)) }}
                    </p>
                </td>
                <td style="width:25%;">
                    <p style="margin:0;padding: 0"><strong>সর্বমোট বেতন/মজুরী: </strong>
                        {{ eng_to_bn(bn_money($sum->tSalaryPayable)) }}
                    </p>
                    <p style="margin:0;padding: 0"><strong>অতিরিক্ত কাজের মজুরী: </strong>
                        {{ eng_to_bn(bn_money(round($sum->totalOTHourAmount))) }}
                    </p>
                    <p style="margin:0;padding: 0"><strong> উপস্থিত বোনাস: </strong>
                        {{ eng_to_bn(bn_money($sum->totalAttBonus)) }}
                    </p>
                </td>
                
                @php
                    $fraction = ($sum->totalSalary + $sum->totalAdvanceAmount) - ($sum->tSalaryPayable + round($sum->totalOTHourAmount) + $sum->totalAttBonus);
                    $fraction = $fraction<0?0:$fraction;
                    $fraction = number_format((float)$fraction, 2, '.', '');
                    
                @endphp
                @if($fraction > 0 || $sum->totalAdvanceAmount > 0)
                <td style="width:19%;">
                    <p style="margin:0;padding: 0"><strong> অগ্রিম সমন্বয়: </strong>
                        {{ eng_to_bn(bn_money($sum->totalAdvanceAmount)) }}
                    </p>
                    <p style="margin:0;padding: 0"><strong>অন্যান্য সমন্বয়: </strong>
                        {{ eng_to_bn(bn_money($fraction)) }}
                    </p>
                </td>
                @endif
                <td style="width:25%; text-align:right;">
                    <p style="margin:0;padding: 0"><strong>সর্বমোট টাকার পরিমানঃ </strong>
                        {{ eng_to_bn(bn_money($sum->totalSalary)) }}
                    </p>
                </td>
                
            </tr>
        </table>
    </div>
</div>
{{-- modal --}}
<div class="item_details_section">
    <div class="overlay-modal overlay-modal-details" style="margin-left: 0px; display: none;">
      <div class="item_details_dialog show_item_details_modal" style="min-height: 115px;">
        <div class="fade-box-details fade-box">
          <div class="inner_gray clearfix">
            <div class="inner_gray_text text-center" id="heading">
             <h3 class="no_margin text-white">বেতন বিতরণ</h3>   
            </div>
            <div class="inner_gray_close_button">
              <a class="cancel_details item_modal_close" role="button" rel='tooltip' data-tooltip-location='left' data-tooltip="Close Modal">Close</a>
            </div>
          </div>

          <div class="inner_body" id="modal-details-content" style="display: none">
            <div class="inner_body_content">
               <input type="hidden" name="id" value="" id="modal-id">
               <input type="hidden" name="associateId" value="" id="modal-associateId">
               <input type="hidden" name="month" value="" id="modal-month">
               <input type="hidden" name="year" value="" id="modal-year">
               <h3 class="text-center" ><strong class="f22" id="disbursed_name"></strong></h3>
               <h4 class="text-center" id="disbursed_post"></h4>
               <h4 class="text-center" id="disbursed_id"></h4>
               <h4 class="text-center" id="disbursed_body"></h4>
            </div>
            <div class="inner_buttons">
              <button class="okay_modal_button confirm-disbursed" id="confirm-disbursed" type="submit" tabindex="0">
                Confirm
              </button>
              <a class="cancel_modal_button cancel_details" role="button"> Cancel </a>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

