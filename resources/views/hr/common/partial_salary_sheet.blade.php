
<div id="payment_slip_data" style="font-size: 9px;">
    <div class="d-flex justify-content-between">
        <div class="action">
            <button type="button" onclick="printMe('partial_data')" class="btn btn-warning btn-sm" title="Print">
                <i class="fa fa-print"></i> 
            </button>
        </div>
        <div class="d-flex justify-content-between">
                @if($salary->disburse_date && $salary->disburse_date != '0000-00-00')
                @else
                <div class="has-float-label">
                    <input style="height: 30px;line-height: 30px;" type="date" class="form-control" name="print-date" id="print-date">
                    <label>Payment Date</label>
                </div> 
                <div class="ml-2">
                    {{-- <button class="btn btn-sm btn-primary">Pay</button> --}}
                </div>
                @endif
        </div>
    </div>

        <div class="tinyMceLetter" name="job_application" id="partial_data" style="font-size: 9px;">
            <?php
            date_default_timezone_set('Asia/Dhaka');
            $en = array('0','1','2','3','4','5','6','7','8','9');
            $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯');
            $date = str_replace($en, $bn, date('Y-m-d H:i:s'));
            ?>
            <p>
            <center><b style="font-size: 16px;">{{$employee->hr_unit_name_bn??''}}</b></center style="font-size: 12px;">
            <center>{{ (!empty($employee->hr_unit_address_bn)?$employee->hr_unit_address_bn:null) }}</center>
            <hr>
            <style type="text/css">

                table{
                    font-size: 12px;
                    width: 100%;
                }
                .table-bordered {
                    border-collapse: collapse;
                }
                .table-bordered th,
                .table-bordered td {
                  border: 1px solid #000 !important;
                  padding: 0 5px;
                }
            </style>
            <table border="0" style="width: 100%;">
                <tr>
                    <th colspan="2" style="width:70%;text-align: left;" > আংশিক মজুরীর বিবরণী - </th>
                    <th style="width:30%; text-align: right;font-weight: bold;">
                        তারিখঃ 
                        @if($salary->disburse_date && $salary->disburse_date != '0000-00-00')
                            {{str_replace($en, $bn, date('d-m-Y', strtotime($salary->disburse_date)))}}
                        @else
                            <span id="new-date"> {{str_replace($en, $bn, date('d-m-Y'))}} </span>
                        @endif
                        ইং
                    </th>
                </tr>
                <tr>
                    <td>কর্মকর্তা/কর্মচারীর নাম </td>
                    <td>{{$employee->hr_bn_associate_name??''}}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>পদবী</td>
                    <td>{{$employee->hr_designation_name_bn??''}}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>সেকশন</td>
                    <td>{{$employee->hr_section_name_bn??''}}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>আইডি নং</td>
                    <td><b>{{$employee->associate_id}}</b>  &nbsp;&nbsp;পূর্বের আইডিঃ {{$employee->as_oracle_code}}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>যোগদানের তারিখ</td>
                    <td>{{str_replace($en, $bn, $employee->as_doj->format('d-m-Y'))}} ইং</td>
                    <td></td>
                </tr>
                <tr>
                    <td>মোট মজুরী</td>
                    <td>{{eng_to_bn(bn_money($salary->gross))}}</td>
                    <td></td>
                </tr>
                
            </table>
            <br>
            <table style="text-align: center;" class="table-bordered">
                <tr>
                    <td rowspan="2">
                        বিস্তারিত
                    </td>
                    <td>মূল বেতন</td>
                    <td>বাসা ভাড়া</td>
                    <td>মেডিকেল</td>
                    <td>খাদ্য</td>
                    <td>যাতায়াত</td>
                    <td>মোট বেতন</td>
                </tr>
                <tr>
                    <td>{{eng_to_bn(bn_money($salary->basic))}}</td>
                    <td>{{eng_to_bn(bn_money($salary->house))}}</td>
                    <td>{{eng_to_bn(bn_money($salary->medical))}}</td>
                    <td>{{eng_to_bn(bn_money($salary->food))}}</td>
                    <td>{{eng_to_bn(bn_money($salary->transport))}}</td>
                    <td>{{eng_to_bn(bn_money($salary->gross))}}</td>
                </tr>
            </table>
            <br>
            {{-- calculations start--}}
            @php
                $dateCount = cal_days_in_month(CAL_GREGORIAN, $salary->month, $salary->year);
                $perDayGross = number_format($salary->gross/$dateCount,2,".","");
                $perDayBasic = number_format($salary->basic/30,2,".","");
                $salaryDays = $salary->present + $salary->absent + $salary->holiday + $salary->leave;
                $salary_pay = number_format(($salaryDays*$perDayGross),2,".","");
            
                $ot = number_format(($salary->ot_hour*$salary->ot_rate),2,".","");
                $yearMonthDay = date('Y-m-d', strtotime($salary->year.'-'.$salary->month.'-01'));
                $total  = $salary->total_payable + $salary->stamp;
                $stamp = 0;
                if((in_array($salary->unit_id, [2,3,8]) && $yearMonthDay > date('Y-m-d', strtotime('2021-08-31'))) || (in_array($salary->unit_id, [1,4,5]) && $yearMonthDay > date('Y-m-d', strtotime('2021-09-30')))){
                    $stamp = $salary->stamp;
                }

                $adjust = $total - ($salary->salary_payable + $stamp + $ot);
                $adjust = $adjust > 15?$adjust:0;
            @endphp

            <table border="0">
                <tr>
                    <td></td>
                    <td>দিন</td>
                    <td>হার</td>
                    <td style="text-align: right;">মোট</td>
                </tr>
                <tr>
                    <td>{{num_to_bn_month($salary->month)}}, {{eng_to_bn($salary->year)}} এর বেতন</td>
                    <td>{{eng_to_bn($salaryDays??0)}}</td>
                    <td>{{eng_to_bn(bn_money($perDayGross??0))}}</td>
                    <td style="text-align: right;">{{eng_to_bn(bn_money($salary_pay))}}</td>
                </tr>
                <tr>
                    <td>অনুপস্থিতির কর্তন</td>
                    <td>{{eng_to_bn($salary->absent??0)}}</td>
                    <td>
                    @if($salary->absent > 0)
                        {{eng_to_bn(bn_money($perDayBasic??0))}}
                    @else
                        ০ 
                    @endif

                    </td>
                    <td style="text-align: right;">
                        {{eng_to_bn(bn_money(number_format(($salary->absent_deduct),2,".","")))}}
                    </td>
                </tr>
                <tr>
                    <td>মজুরী সমন্বয় বাবদ</td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right;">{{eng_to_bn(bn_money(number_format(($adjust),2,".","")))}}</td>
                </tr>
                @if($salary->partial_amount > 0)
                <tr>
                    <td>মজুরী অগ্রিম </td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right;">{{eng_to_bn(bn_money(number_format(($salary->partial_amount),2,".","")))}}</td>
                </tr>
                @endif
                <tr>
                    <td></td>
                    <td>ঘন্টা</td>
                    <td>হার</td>
                    <td></td>
                </tr>

                <tr>
                    <td>{{num_to_bn_month($salary->month)}}, {{eng_to_bn($salary->year)}} এর অতিরিক্ত</td>
                    <td>{{eng_to_bn(numberToTimeClockFormat($salary->ot_hour))}}</td>
                    <td>{{eng_to_bn(number_format($salary->ot_rate??0,2,".",""))}}</td>
                    <td style="text-align: right;">{{eng_to_bn(bn_money($ot))}}</td>
                </tr>

                <tr>
                    <td> </td>
                    <td>সর্বমোট টাকা</td>
                    <td></td>
                    <td style="text-align: right;">

                        {{eng_to_bn(bn_money(number_format($total,2,".","")))}}</td>
                </tr>
                <tr>
                    <td> </td>
                    <td>চূড়ান্ত প্রাপ্য</td>
                    <td></td>
                    <td style="text-align: right;">{{eng_to_bn(bn_money(number_format(($total),2,".","")))}}</td>
                </tr>
                <tr>
                    @php
                        $bnConvert = new BnConvert();
                        $toword = $bnConvert->bnMoney(number_format(($total),2,".",""));

                    @endphp
                    <td colspan="4" ><br><strong> কথায়ঃ {{$toword}} মাত্র </strong></td>
                </tr>
            </table>
            
            <table style="margin-top: 100px; " width="100%" cellpadding="3" border="0">
                
                <tr style="width: 100%">
                    <td style="text-align: center;vertical-align: top;">
                        <hr style="margin:10px;">
                        প্রস্তুত/যাচাইকারী
                    </td>
                    <td style="text-align: center;vertical-align: top;">
                        <hr style="margin:10px;">
                        হিসাব বিভাগ 
                    </td>
                    <td style="text-align: center;vertical-align: top;">
                       <hr style="margin:10px;">
                        ব্যাবস্থাপক<br>
                        মানবসম্পদ
                    </td>
                    <td style="text-align: center;vertical-align: top;">
                        <hr style="margin:10px 30px;">
                        উপমহাব্যাবস্থাপক<br>
                        প্রশাসন ও মানবসম্পদ বিভাগ
                    </td>
                </tr>
            </table>
            <p style="margin-top: 30px;">{{$employee->hr_unit_name_bn??''}} এর কাছ থেকে আমি আমার সকল পাওনা বুঝিয়া পাইয়া নিম্নে স্বাক্ষর ও টিপসহি প্রদান করিলাম।</p>
            <div style="display:flex; justify-content: space-between;margin-top: 80px;">
                
                <table >
                    <tr>
                        <td>অনুলিপিঃ</td>
                    </tr>
                    <tr>
                        <td>১। হিসাব বিভাগ</td>
                    </tr>
                    <tr>
                        <td>২। ব্যাক্তিগত নথি</td>
                    </tr>
                    <tr>
                        <td>৩। অফিস কপি</td>
                    </tr>
                </table>
                
                <div style="margin-right:100px;width:150px; text-align: center;font-size: 12px; ">
                    <hr>
                    গ্রহীতার স্বাক্ষর
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var dar = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
        String.prototype.getDigitBanglaFromEnglish = function() {
            var retStr = this;
            for (var x in dar) {
                 retStr = retStr.replace(new RegExp(x, 'g'), dar[x]);
            }
            return retStr;
        };
        $(document).on('change', '#print-date', function(){
            const d = new Date($(this).val()),
                 ye = new Intl.DateTimeFormat('en', { year: 'numeric' }).format(d),
                 mo = new Intl.DateTimeFormat('en', { month: '2-digit' }).format(d),
                 da = new Intl.DateTimeFormat('en', { day: '2-digit' }).format(d),
                 nd = da+'-'+mo+'-'+ye;

            $('#new-date').html(nd.getDigitBanglaFromEnglish());
        });
    </script>