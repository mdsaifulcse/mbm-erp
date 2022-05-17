<button type="button" onclick="printMe('payment_slip_data')" class="btn btn-warning" title="Print">
    <i class="fa fa-print"></i> 
</button>
<div class="col-xs-12 no-padding-left" id="payment_slip_data" style="font-size: 12px;">
    <div class="tinyMceLetter" name="job_application" id="job_application" style="font-size: 12px;text-align: left;">
        <?php
        date_default_timezone_set('Asia/Dhaka');
        $en = array('0','1','2','3','4','5','6','7','8','9');
        $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯');
        $date = str_replace($en, $bn, $benefits->status_date);
        ?>
        <p>
        <center><h2>{{$employee->hr_unit_name_bn??''}}</h2></center>
        <center>{{ (!empty($employee->hr_unit_address_bn)?$employee->hr_unit_address_bn:null) }}</center>
        <center>চূড়ান্ত নিষ্পত্তিকরন</center>
        <br>
        <style type="text/css">
            table{
                font-size: 12px;
                text-align: left;
            }
            p span{font-size: 12px !important;}
            .table-bordered {
                border-collapse: collapse;
            }
            .table-bordered th,
            .table-bordered td {
              border: 1px solid #000 !important;

            }
            .d-flex{display: flex;}
            span.uline {
                border-bottom: 1px solid #000;
                flex-grow: 1;
                text-align: center;
                padding: 0 10px;
            }
            span.d-uline {
                border-bottom: 1px solid #000;
                display: block;
                text-align: center;
                margin: 0 10px;
            }
            .center{text-align: center;}
            .right{text-align: right;}
        </style>
        <p class="d-flex justify-content-between">
            <span>প্রতিষ্ঠানের চাকুরী হইতে পদত্যাগ এর পরিপ্রেক্ষিতে জনাব/জনাবা</span>
            <span>তারিখঃ {{eng_to_bn(date('d-m-Y', strtotime($benefits->status_date)))}} ইং</span>
        </p>
        <p class="d-flex justify-content-between">
            <span style="width: 45%;display: flex;">নামঃ <span class="uline">{{$employee->hr_bn_associate_name??''}}</span></span>
            <span style="width: 25%;display: flex;">আইডি নংঃ <span class="uline">{{$employee->associate_id}}</span></span> 
            <span style="width: 30%;display: flex;">পূর্বের আইডিঃ<span class="uline">{{$employee->as_oracle_code??''}}</span></span>
        </p>
        <p class="d-flex justify-content-between">
            <span style="width: 40%;display: flex;">পদবীঃ <span class="uline">{{$employee->hr_designation_name_bn??''}}</span></span>
            <span style="width: 30%;display: flex;">সেকশনঃ <span class="uline">{{$employee->hr_section_name_bn??''}}</span></span>
            <span style="width: 30%;display: flex;">বিভাগঃ <span class="uline">{{$employee->hr_department_name_bn??''}}</span></span>
        </p>
        <p>এর চূড়ান্ত নিস্পত্তিকরন নিম্নলিখিতভাবে সম্পন্ন করা হইলঃ</p>
        <table border="0" style="width: 100%;">
            @php
                $perbasic = eng_to_bn(bn_money(round($employee->ben_basic/30,2)));
                $pergross = eng_to_bn(bn_money(round($employee->ben_current_salary/30,2)));
                $total_1 = number_format(($benefits->earn_leave_amount + $benefits->service_benefits + $benefits->death_benefits),2,".","");
                $total_s1 = eng_to_bn(bn_money($total_1));
                $total_s2 = $total_1;
                $total_s = 0;
                if($benefits->benefit_on == 2 && $benefits->notice_pay_month > 0){
                    $total_s2 = number_format($total_1 - $benefits->notice_pay,2,".","");
                    $total_s = $benefits->notice_pay;
                }

                if($benefits->benefit_on == 3 && $benefits->notice_pay_month > 0){
                    $total_s = $benefits->notice_pay;
                    $total_s2 = number_format($total_1 + $benefits->notice_pay,2,".","");
                }

            @endphp
            <tr>
                <td colspan="3"></td>
                <td style="text-align: right;"></td>
            </tr>
            <tr>
                <td colspan="4">
                    
                    
                    
                    
                      
                </td>

            </tr>
            <tr><td colspan="4"></td></tr>
            <tr>
                <td>১। চাকুরীতে যোগদানের তারিখ</td>
                <td colspan="3">{{str_replace($en, $bn, $employee->as_doj->format('d-m-Y'))}} ইং</td>
            </tr>
            <tr>
                <td>২। চাকুরী ছাড়ার তারিখ</td>
                <td colspan="3">{{eng_to_bn(date('d-m-Y', strtotime($benefits->status_date)))}} ইং</td>
            </tr>
            <tr>
                <td>৩। চাকুরীকালীন সময়ে মোট কার্যকাল</td>
                <td colspan="3">@if($jobDuration->years > 0) {{eng_to_bn($jobDuration->years)}} বছর @endif @if($jobDuration->months > 0) {{eng_to_bn($jobDuration->months)}} মাস @endif</td>
            </tr>
            <tr>
                <td>৪। বর্তমান মজুরী</td>
                <td><span class="uline">{{eng_to_bn(bn_money($employee->ben_current_salary))}}</span> টাকা</td>
                <td>মূল মজুরীঃ <span class="uline">{{eng_to_bn(bn_money($employee->ben_basic))}}</span> টাকা</td>
                <td>অন্যান্যঃ <span class="uline">{{eng_to_bn(bn_money(1850))}}</span> টাকা</td>
            </tr>
            <tr>
                <td>৫। প্র্যাপ্য সুবিধা</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td></td>
                <td><span class="d-uline" style="margin-left: 0">দিন</span></td>
                <td><span class="d-uline">হার</span></td>
                <td><span class="d-uline" style="margin-right: 0">টাকা</span></td>
            </tr>
            <tr>
                <td>মঞ্জুরীকৃত বাৎসরিক/প্রাপ্ত ছুটি</td>
                <td class="center">{{eng_to_bn($benefits->earned_leave)}}</td>
                <td class="center">{{$pergross}}</td>
                <td class="right">{{eng_to_bn(bn_money(number_format($benefits->earn_leave_amount,2,".","")))}}</td>
            </tr>
            <tr>
                <td>প্রাপ্ত/মঞ্জুরীকৃত সার্ভিস বেনিফিট</td>
                <td class="center">{{eng_to_bn($benefits->service_days)}}</td>
                <td class="center">{{$perbasic}}</td>
                <td class="right">{{eng_to_bn(bn_money(number_format($benefits->service_benefits,2,".","")))}}</td>
            </tr>
            <tr>
                <td colspan="4"></td>
            </tr>
            @if($benefits->benefit_on == 7)
            <tr>
                <td>মৃত্যুজনিত বেনিফিট</td>
                <td class="center">{{eng_to_bn($benefits->death_days)}}</td>
                <td class="center">{{$perbasic}}</td>
                <td class="right">{{eng_to_bn(bn_money(number_format($benefits->death_benefits,2,".","")))}}</td>
            </tr>
            @endif
            <tr>
                <td></td>
                <td style="border-top:1px solid #000 !important;">সর্বমোট টাকা</td>
                <td colspan="2" class="right" style="border-top:1px solid #000 !important;">{{$total_s1}}</td>
            </tr>
            <tr>
                <td>৬। প্রদেয় সুবিধা</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td></td>
                <td><span class="d-uline" style="margin-left: 0">মাস</span></td>
                <td><span class="d-uline">হার</span></td>
                <td><span class="d-uline" style="margin-right: 0">টাকা</span></td>
            </tr>
            @if($benefits->benefit_on == 2 || $benefits->benefit_on == 3)
            <tr>
                <td>নোটিশ পে</td>
                <td class="center"> {{eng_to_bn($benefits->notice_pay_month)}} </td>
                <td class="center"> {{eng_to_bn(bn_money($employee->ben_basic))}} </td>
                <td class="right">{{eng_to_bn(bn_money(number_format($benefits->notice_pay,2,".","")))}}</td>
            </tr>
            @endif 
            <tr>
                <td>অন্যান্য সমন্বয় (যদি থাকে)</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="border-top:1px solid #000 !important;">সর্বমোট টাকা</td>
                <td colspan="2" style="border-top:1px solid #000 !important;text-align: right;">{{eng_to_bn(bn_money($total_s))}}</td>
            </tr>
            <tr>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="3">৭। চূড়ান্ত প্র্যাপ্য/পরিশোধিত মোট টাকা</td>
                <td ><b>{{eng_to_bn(bn_money(number_format($benefits->total_amount,2,".","")))}}  টাকা </b></td>
            </tr>
            <tr>
                <td colspan="4"><br></td>
            </tr>
            <tr>
                <td colspan="4">

                    @php
                        $bnConvert = new BnConvert();
                        $toword = $bnConvert->bnMoney($benefits->total_amount);

                    @endphp
                    <strong>কথায়ঃ {{$toword}} মাত্র </strong>
                </td>
            </tr>
        </table>
        <br>
        <br>
       
        <table style=" " width="100%" cellpadding="3" border="0">
            
            <tr style="width: 100%">
                <td style="text-align: center;">
                    <br><br><br>
                    প্রস্তুত
                </td>
                <td style="text-align: center;">
                    <br><br><br>
                    যাচাইকারী
                </td>
                <td style="text-align: center;">
                    <br><br><br>

                    হিসাববিভাগ 
                </td>
                <td style="text-align: center;">
                    <br><br><br>
                    ব্যাবস্থাপক<br>
                    মানবসম্পদ, প্রশাসন ও কমপ্লাইন্স
                </td>
                <td style="text-align: center;">
                    <br><br><br>
                    উপমহাব্যাবস্থাপক<br>
                    মানবসম্পদ, প্রশাসন ও কমপ্লাইন্স
                </td>
            </tr>
            <tr>
                <td colspan="5"></td>
            </tr>
            <tr>
                <td colspan="5">{{$employee->hr_unit_name_bn??''}} এর কাছ থেকে আমি আমার সকল পাওনা বুঝিয়া পাইয়া নিম্নে স্বাক্ষর ও টিপসহি প্রদান করিলাম</td>
            </tr>
            <tr style="width: 100%">
                <td colspan="4">
                </td>
                <td style="text-align: center;">
                    <br><br><br>
                    গ্রহীতার স্বাক্ষর
                </td>
            </tr>
        </table>
        <br>
        
        <p>অনুলিপিঃ</p>
        <p>১। হিসাববিভাগ</p>
        <p>২। ব্যাক্তিগত নথি</p>
        <p>৩। অফিস কপি</p>
    </div>
</div>