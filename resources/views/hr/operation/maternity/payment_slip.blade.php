<button type="button" onclick="printDiv('payment_slip_data')" class="btn btn-warning" title="Print">
    <i class="fa fa-print"></i> 
</button>
<div class="col-xs-12 no-padding-left" id="payment_slip_data" style="font-size: 12px;">
    <div class="tinyMceLetter" name="job_application" id="job_application" style="font-size: 12px;">
        <?php
        date_default_timezone_set('Asia/Dhaka');
        $en = array('0','1','2','3','4','5','6','7','8','9');
        $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯');
        $date = eng_to_bn(date('d-m-Y H:i:s'));
        ?>
        <p>
        <center><h2>{{$employee->hr_unit_name_bn??''}}</h2></center>
        <center>{{ (!empty($employee->hr_unit_address_bn)?$employee->hr_unit_address_bn:null) }}</center>
        <hr>
        <table border="0" style="width: 100%;">
            <tr>
                <th colspan="2" style="text-align: left;">মাতৃত্ব কল্যাণ সুবিধার হিসাব - </th>
                <th style="text-align: right;">তারিখঃ {{str_replace($en, $bn, date('d-m-Y'))}} ইং</th>
            </tr>
            <tr>
                <td style="line-height: 1.5">কর্মকর্তা/করমচারীর নাম </td>
                <td>{{$employee->hr_bn_associate_name??''}}</td>
                <td></td>
            </tr>
            <tr>
                <td style="line-height: 1.5">পদবী</td>
                <td>{{$employee->hr_designation_name_bn??''}}</td>
                <td></td>
            </tr>
            <tr>
                <td style="line-height: 1.5">সেকশন</td>
                <td>{{$employee->hr_section_name_bn??''}}</td>
                <td></td>
            </tr>
            <tr>
                <td style="line-height: 1.5">আইডি নং</td>
                <td>{{str_replace($en, $bn, $employee->associate_id)}}</td>
                <td></td>
            </tr>
            </tr>
            <tr>
                <td style="line-height: 1.5">যোগদানের তারিখ</td>
                <td>{{str_replace($en, $bn, $employee->as_doj->format('d-m-Y'))}} ইং</td>
                <td></td>
            </tr>
            <tr>
                <td style="line-height: 1.5">মোট মজুরী</td>
                <td>{{eng_to_bn(bn_money($benefits->ben_current_salary))}} টাকা</td>
                <td></td>
            </tr>
            <tr>
                <td style="line-height: 1.5">সন্তান প্রসবের সম্ভাব্য তারিখ</td>
                <td colspan="2">{{str_replace($en, $bn, date('d-m-Y', strtotime($leave->edd)))}} ইং </td>
            </tr>
            <tr>
                <td style="line-height: 1.5">ছুটির তারিখ</td>
                <td colspan="2">১১২ দিন &nbsp; &nbsp; {{str_replace($en, $bn, $leave->leave_from->format('d-m-Y'))}} তারিখ থেকে {{str_replace($en, $bn, $leave->leave_to->format('d-m-Y'))}} পর্যন্ত</td>
            </tr>
            
        </table>
        <br>

        <!---if not employee has less than 3 child -->
        @if($payment->maternity_for_3 != 1)
            <strong><u>বিগত ০৩ (তিন) মাসের প্রাপ্ত মজুরীর বিবরনঃ</u></strong>
            <table class="table-bordered" style=" text-align: center;margin-top: 50px;" width="100%" cellpadding="3" >
                <tr>
                    <th rowspan="2">মাসের নাম</th>
                    <th colspan="3">হাজিরা</th>
                    <th colspan="6">মজুরী</th>
                </tr>
                <tr>
                    <th>উপস্থিত</th>
                    <th>অনুপস্থিত</th>
                    <th>ছুটি</th>
                    <th>মোট প্রদেয় মজুরী</th>
                    <th>হাজিরা বোনাস</th>
                    <th>অভারটাইম ভাতা</th>
                    <th>অন্যান্য ভাতা</th>
                    <th>ঈদ বোনাস</th>
                    <th>প্রাপ্ত মোট মজুরী</th>
                </tr>
                @foreach($salary_history->salary as $key => $sal)
                    @if($sal)
                    <tr>
                        <td>{{num_to_bn_month(date('n', strtotime($key)))}}</td>
                        <td>{{str_replace($en, $bn, $sal->present)}}</td>
                        <td>{{str_replace($en, $bn, $sal->absent)}}</td>
                        <td>{{str_replace($en, $bn, $sal->leave)}}</td>
                        <td style="text-align: right;">{{eng_to_bn(bn_money($sal->salary_payable))}}</td>
                        <td style="text-align: right;">{{eng_to_bn(bn_money($sal->attendance_bonus))}}</td>
                        <td style="text-align: right;">{{eng_to_bn(bn_money($sal->ot_payment))}}</td>
                        <td style="text-align: right;">{{eng_to_bn(bn_money($sal->others))}}</td>
                        <td style="text-align: right;">{{eng_to_bn(bn_money($sal->eid_bonus))}}</td>
                        <td style="text-align: right;">{{eng_to_bn(bn_money($sal->total_payable))}}</td>
                    </tr>
                    @else
                        <tr>
                            <td>{{num_to_bn_month(date('n', strtotime($key)))}}</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <th>মোট</th>
                    <th>{{eng_to_bn($salary_history->total->present)}}</th>
                    <th>{{eng_to_bn($salary_history->total->absent)}}</th>
                    <th>{{eng_to_bn($salary_history->total->leave)}}</th>
                    <th style="text-align: right;">{{eng_to_bn(bn_money($salary_history->total->salary_payable))}}</th>
                    <th style="text-align: right;">{{eng_to_bn(bn_money($salary_history->total->attendance_bonus))}}</th>
                    <th style="text-align: right;">{{eng_to_bn(bn_money($salary_history->total->ot_payment))}}</th>
                    <th style="text-align: right;">{{eng_to_bn(bn_money($salary_history->total->others))}}</th>
                    <th style="text-align: right;">{{eng_to_bn(bn_money($salary_history->total->eid_bonus))}}</th>
                    <th style="text-align: right;">{{eng_to_bn(bn_money($salary_history->total->total_amount))}}</th>
                </tr>
            </table>

            <br><br><br>
            <table style="border: none; " width="100%" cellpadding="3" width="100%">
                <tr>
                    <th style="width: 300px;line-height: 1.5">০১ (এক) দিনের গড় মজুরী</th>
                    <th style="width: 5%;">=</th>
                    <td colspan="2"> তিন মাসের মোট প্রাপ্ত টাকা / (ভাগ) তিন মাসের মোট উপস্থিতি</td>
                </tr>
                <tr>
                    <td style="width: 300px;line-height: 1.5">০১ (এক) দিনের গড় মজুরী</td>
                    <td>=</td>
                    <td style="text-align: right;"> {{eng_to_bn(bn_money($payment->per_day_wages))}} টাকা</td>
                    <td style="width: 30%"></td>
                </tr>
                <tr>
                    <td style="width: 300px;line-height: 1.5">৫৬ দিনের মোট মজুরী</td>
                    <td>=</td>
                    <td style="text-align: right;"> {{eng_to_bn(bn_money($payment->first_payment))}} টাকা</td>
                    <td></td>
                </tr>
                <tr>
                    <td style="width: 300px;line-height: 1.5">সর্বমোট ১১২ দিনের প্র্যাপ্য </td>
                    <td>=</td>
                    <td style="text-align: right;"> {{eng_to_bn(bn_money($payment->first_payment + $payment->second_payment))}} টাকা</td>
                    <td></td>
                </tr>
                
            </table >
            <table class="table-bordered" style="text-align: center;width: 700px !important; margin:30px 0 ;">
                <tr>
                    <th>১ম কিস্তির টাকা</th>
                    <th>টাকার পরিমান</th>
                    <th>বিল গ্রহিতার স্বাক্ষর</th>
                <tr>
                    <td style="padding:20px 10px;">৫৬ দিনের মোট মজুরী </td>
                    <td style="padding:20px 10px;"><b>{{eng_to_bn(bn_money($payment->first_payment))}}</b></td>
                    <td style="padding:20px 10px;"></td>
                </tr>
            </table>
            <table class="table-bordered" style="text-align: center;width: 700px !important; ">
                <tr>
                    <th>২য় কিস্তির টাকা</th>
                    <th>টাকার পরিমান</th>
                    <th>বিল গ্রহিতার স্বাক্ষর</th>
                <tr>
                    <td style="padding:20px 10px;"> ৫৬ দিনের মোট মজুরী  </td>
                    <td style="padding:20px 10px;"><b>{{eng_to_bn(bn_money($payment->second_payment))}}</b></td>
                    <td style="padding:20px 10px;"></td>
                </tr>
            </table>
        @endif

        <!-- this block is for third child -->
        @if($payment->maternity_for_3 == 1)

            @php
                // make some common calculation here
                $totalLeave = $payment->earned_leave + $payment->sick_leave;
                $sickLeavePay = round($payment->sick_leave * $payment->per_day_wages);
                $earnLeavePay = round($payment->earned_leave * $payment->per_day_wages);
                $leavePay = $sickLeavePay + $earnLeavePay;


            @endphp
            <strong><u>প্রাপ্য ছুটির বিবরনঃ</u></strong>
            <table class="table-bordered" style="text-align: center; margin-bottom: 20px;" width="100%" cellpadding="3">
                <tr>
                    <th>জমাকৃত অর্জিত ছুটির দিন</th>
                    <th>জমাকৃত অর্জিত ছুটির টাকা</th>
                    <th>অভোগকৃত অসুস্থতা ছুটির দিন</th>
                    <th>অভোগকৃত অসুস্থতা ছুটির টাকা</th>
                <tr>
                    <th style="padding:20px 10px;">{{eng_to_bn($payment->earned_leave)}}</th>
                    <th style="padding:20px 10px;">{{eng_to_bn(bn_money($earnLeavePay))}}</th>
                    <th style="padding:20px 10px;">{{eng_to_bn($payment->sick_leave)}}</th>
                    <th style="padding:20px 10px;">{{eng_to_bn(bn_money($sickLeavePay))}}</th>
                </tr>
            </table>
            <strong style="text-align: center;"> 
                (@if($payment->earned_leave > 0)
                    {{eng_to_bn($payment->earned_leave)}} দিন অর্জিত ছুটি, 
                @endif
                @if($payment->sick_leave > 0)
                    {{eng_to_bn($payment->sick_leave)}} দিন অসুস্থতা ছুটি  
                @endif

                এবং ({{eng_to_bn($payment->earned_leave)}} + {{eng_to_bn($payment->earned_leave)}}) = {{eng_to_bn($totalLeave)}}, (১১২-{{eng_to_bn($totalLeave)}}) = {{eng_to_bn($payment->benefits_day)}} দিনের বিনা বেতনে ছুটি)  
            </strong>
            <table style="border: none;" width="100%" cellpadding="3" width="100%">
                <tr>
                    <td style="width: 300px;line-height: 1.5"> {{eng_to_bn($totalLeave)}} দিনের মোট মজুরী</td>
                    <td>= <span style="display: inline-block;width: 80px;text-align: right;">{{eng_to_bn(bn_money($leavePay))}}</span> টাকা ({{eng_to_bn($payment->earned_leave)}} দিন অর্জিত ছুটি + {{eng_to_bn($payment->earned_leave)}} দিন অসুস্থতা ছুটি)</td>
                </tr>
                <tr>
                    <td style="width: 300px;line-height: 1.5">বিনা বেতনে ছুটি</td>
                    <td>= <span style="display: inline-block;width: 80px;text-align: right;">{{eng_to_bn(bn_money($payment->benefits_day))}}</span> দিন</td>
                </tr>
                <tr>
                    <td style="width: 300px;line-height: 1.5">মোট মজুরী</td>
                    <td>= <span style="display: inline-block;width: 80px;text-align: right;">{{eng_to_bn(bn_money($benefits->ben_current_salary))}}</span> টাকা</td>
                </tr>
                <tr>
                    <td style="width: 300px;line-height: 1.5">বাড়ি ভাড়া</td>
                    <td>= <span style="display: inline-block;width: 80px;text-align: right;">{{eng_to_bn(bn_money($benefits->ben_house_rent))}}</span> টাকা</td>
                </tr>
                <tr>
                    <td style="width: 300px;line-height: 1.5">যাতায়াত, চিকিৎসা ও খাদ্য ভাতা</td>
                    <td>= <span style="display: inline-block;width: 80px;text-align: right;">{{eng_to_bn(bn_money($benefits->food + $benefits->transport + $benefits->medical))}}</span> টাকা</td>
                </tr>
                <tr>
                    <td style="width: 300px;line-height: 1.5">বাড়ি ভাড়া + যাতায়াত, চিকিৎসা ও খাদ্য ভাতা</td>
                    <td>= <span style="display: inline-block;width: 80px;text-align: right;">{{eng_to_bn(bn_money($benefits->ben_house_rent + $benefits->food + $benefits->transport + $benefits->medical))}}</span> টাকা</td>
                </tr>
                <tr>
                    <td style="width: 300px;line-height: 1.5">০১ দিনের বাড়ি ভাড়া + যাতায়াত, চিকিৎসা ও খাদ্য ভাতা</td>
                    <td>=  <span style="display: inline-block;width: 80px;text-align: right;">{{eng_to_bn(bn_money($payment->per_day_benefit))}}</span> টাকা</td>
                </tr>
                <tr>
                    <td style="width: 300px;line-height: 1.5">{{eng_to_bn(bn_money($payment->benefits_day))}} দিনের বাড়ি ভাড়া + যাতায়াত, চিকিৎসা ও খাদ্য ভাতা</td>
                    <td>= <span style="display: inline-block;width: 80px;text-align: right;">{{eng_to_bn(bn_money(ceil($payment->benefits_day * $payment->per_day_benefit)))}}</span> টাকা</td>
                </tr>
                <tr>
                    <td style="width: 300px;line-height: 1.5">{{eng_to_bn($totalLeave)}} দিনের ছুটির টাকা</td>
                    <td>= <span style="display: inline-block;width: 80px;text-align: right;">{{eng_to_bn(bn_money($leavePay))}}</span> টাকা</td>
                </tr>
                <tr>
                    <td style="width: 300px;line-height: 1.5">সর্বমোট ১১২ দিনের প্র্যাপ্য </td>
                    <td>= <span style="display: inline-block;width: 80px;text-align: right;">{{eng_to_bn(bn_money($payment->first_payment + $payment->second_payment))}} </span> টাকা</td>
                </tr>
                
            </table >
            <table class="table-bordered" style="text-align: center;width: 700px; margin: 30px 0 !important;">
                <tr>
                    <th>১ম কিস্তির টাকা</th>
                    <th>টাকার পরিমান</th>
                    <th>বিল গ্রহিতার স্বাক্ষর</th>
                <tr>
                    <td style="padding:20px 10px;"><p style="width:300px;line-height: 1.5;">{{eng_to_bn($totalLeave)}} দিনের ছুটির টাকা ও (৫৬ - {{eng_to_bn($totalLeave)}}) = {{eng_to_bn(56 - ($totalLeave))}} দিনের <br> বিনা বেতনে ছুটির টাকা</p>   </td>
                    <td style="padding:20px 10px;"><p style="width: 100px;"><b>{{eng_to_bn(bn_money($payment->first_payment))}}</b></p></td>
                    <td style="padding:20px 10px;"></td>
                </tr>
            </table>
            <table class="table-bordered" style="text-align: center;width: 700px; ">
                <tr>
                    <th>২য় কিস্তির টাকা</th>
                    <th>টাকার পরিমান</th>
                    <th>বিল গ্রহিতার স্বাক্ষর</th>
                <tr>
                    <td style="padding:20px 10px;"><p style="width:300px;">৫৬  দিনের বিনা বেতনে ছুটির টাকা</p>   </td>
                    <td style="padding:20px 10px;"><p style="width: 100px;"><b>{{eng_to_bn(bn_money($payment->second_payment))}}</b></p></td>
                    <td style="padding:20px 10px;"></td>
                </tr>
            </table>
        
            
        @endif
       
        <table style="margin-top: 150px;" width="100%" cellpadding="3" border="0">
            
            <tr style="width: 100%">
                <td style="text-align: center;">
                    <br><br><br>
                    প্রস্তুত/যাচাইকারী
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
        </table>
    </div>
</div>