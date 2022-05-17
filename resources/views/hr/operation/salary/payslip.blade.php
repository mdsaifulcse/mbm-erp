
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
                
                @foreach($page as $key => $emp)
                @php
                    $empBanglaName = $empBangla[$emp->as_id]??'';
                    ++$sl;
                    
                    $listdeduct = 0;
                    if(($emp->salary_add_deduct_id != null) && isset($salaryAddDeduct[$emp->as_id])){
                        $adv = $salaryAddDeduct[$emp->as_id]->advp_deduct??0;
                        $cg = $salaryAddDeduct[$emp->as_id]->cg_product??0;
                        $fd = $salaryAddDeduct[$emp->as_id]->food_deduct??0;
                        $other = $salaryAddDeduct[$emp->as_id]->others_deduct??0;
                        
                        $listdeduct = $adv+$cg+$fd+$other;
                    }
                    $deduct = $emp->absent_deduct+$emp->half_day_deduct+$listdeduct;
                    $otHour = numberToTimeClockFormat($emp->ot_hour);
                    $ot = ((float)($emp->ot_rate) * $emp->ot_hour);
                    $ot = number_format((float)$ot, 2, '.', '');
                @endphp
                <table style="width:100%;margin-bottom: 40px; ">
                    <tr>
                        <td colspan="2" style="">
                            <br>
                            <p style="">নামঃ <strong style="color:hotpink;">{{ $empBanglaName }}</strong></p>
                            <p style="">পদবীঃ <span style="color:hotpink;">{{ $designation[$emp->as_designation_id]['hr_designation_name_bn']}}</span></p>
                            
                            <p >যোগদানের তারিখঃ <span style="color:hotpink;">{{ eng_to_bn($emp->as_doj) }}</span></p>
                        </td>
                        <td colspan="3" style="padding:5px;color:hotpink;text-align:center">
                            <h2 style="margin:0;text-align:center;font-weight:600;font-size:16px;">{{ $unit[$emp->as_unit_id]['hr_unit_name_bn']}}</h2>
                            <h3 style="font-size: 14px;margin: 0">পে-স্লিপঃ <strong>{{ $salmonth }}</strong> </h3>
                           <p style="color:black;">
                                <font style="font-size: 12px;">আইডি  <strong>{{ $emp->as_id }}</strong>  
                                </font> (পূর্বের আইডি: {{ $emp->as_oracle_code }} )
                           </p>

                            
                        </td>
                        <td colspan="2" style="padding:5px;">

                            <p style="text-align: right;">
                                
                                <span style="border-radius:50%;width: 40px;height: 40px;border:1px solid #999;color:#999;line-height: 40px;text-align:center;display: inline-block;">{{eng_to_bn($sl)}}</span>
                            </p>
                                @if(isset($designation[$emp->as_designation_id]))
                                    @if($designation[$emp->as_designation_id]['hr_designation_grade'] > 0 || $designation[$emp->as_designation_id]['hr_designation_grade'] != null)
                                    <p>গ্রেডঃ <span style="color:hotpink;">{{ eng_to_bn($designation[$emp->as_designation_id]['hr_designation_grade'])}}</span></p>
                                    @endif
                                @endif
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
                            @if($emp->ot_status>0)
                            <p>মোট অতিরিক্ত দেয় </p>
                            @endif
                        </td>
                        <td style="width: 10%; text-align: right;color:hotpink;">
                            <p><br></p>
                            <p>&nbsp;&nbsp;&nbsp;{{ eng_to_bn($emp->present) }} দিন</p>
                            <p>&nbsp;&nbsp;&nbsp;{{ eng_to_bn($emp->holiday) }} দিন</p>
                            @if(($input['year_month'] < '2021-04'))
                            <p style="margin:0;padding:0k">&nbsp;&nbsp;&nbsp;
                                {{ eng_to_bn($emp->absent + $emp->leave) }} দিন
                            </p>
                            @else
                            <p style="margin:0;padding:0k">&nbsp;&nbsp;&nbsp;
                                {{ eng_to_bn($emp->absent) }} দিন
                            </p>
                            @endif
                            <p>&nbsp;&nbsp;&nbsp;{{ eng_to_bn($emp->leave) }} দিন</p>
                            <p style="border-top:1px solid #999">&nbsp;&nbsp;&nbsp;{{ eng_to_bn($emp->present + $emp->holiday + $emp->leave)}} দিন</p>
                            <p style="">&nbsp;&nbsp;&nbsp;{{ eng_to_bn($emp->late_count) }} দিন</p>
                            @if($emp->ot_status>0)
                            <p>{{ $emp->ot_status==1?eng_to_bn($otHour):eng_to_bn('00') }} ঘন্টা </p>
                            @endif

                        </td>
                        <!-- second -->
                        <td style="width: 20%;padding-left: 15px; ">
                            <p></p>
                            <p>মূল বেতন</p>
                            <p>বাড়ী বাড়া (৫০%)</p>
                            <p>চিকিৎসা ভাতা</p>
                            <p>যাতায়াত</p>
                            <p>খাদ্য</p>
                            <p style="border-top:1px solid #999">মোট মজুরি</p>
                            @if($emp->ot_status>0)
                            <p>অতিরিক্ত কাজের মজুরী হার </p>
                            @endif
                        </td>
                        <td style="width: 9%;text-align: right;color:hotpink;">
                            <p></p>
                            <p>
                               {{ eng_to_bn($emp->basic) }}
                           </p>
                            <p>
                                {{ eng_to_bn($emp->house) }}
                            </p>
                            <p>
                            {{ eng_to_bn($emp->medical) }}
                            </p>
                            <p> {{ eng_to_bn($emp->transport) }}
                            </p>
                            <p> {{ eng_to_bn($emp->food) }}
                            </p>
                            <p style="border-top:1px solid #999"> {{ eng_to_bn($emp->basic+$emp->house+$emp->medical+$emp->transport+$emp->food) }}
                            </p>
                            @if($emp->ot_status>0)
                            <p > {{ eng_to_bn($emp->ot_rate) }} </p>
                            @endif
                        </td>
                        <td style="width: 32%;padding-left: 15px; ">
                            <p>ভোগ্যপণ্য ক্রয়/অন্যান্য কর্তন</p>
                            <p>অগ্রিম গ্রহণ</p>
                            <p>প্রদেয় মজুরি&nbsp;&nbsp;&nbsp;</p>
                            <p>অতিরিক্ত কাজের মজুরি &nbsp;&nbsp;&nbsp;</p>
                            <p>হাজিরা বোনাস&nbsp;&nbsp;&nbsp;</p>
                            <p>প্রোডাকশন বোনাস&nbsp;&nbsp;&nbsp;</p>
                            <p>মজুরি সমন্বয়&nbsp;&nbsp;&nbsp;
                                @if(isset($salaryAdjust[$emp->as_id][2]))
                                    ( {!! eng_to_bn($salaryAdjust[$emp->as_id][2]->days??'') !!})
                                @endif
                            </p>
                            @if(isset($salaryAdjust[$emp->as_id][3]))
                            <p>বর্ধিত বেতন সমন্বয়&nbsp;&nbsp;&nbsp;</p>
                            @endif
                            @if(isset($salaryAdjust[$emp->as_id][4]))
                            <p>বর্ধিত বোনাস সমন্বয়&nbsp;&nbsp;&nbsp;</p>
                            @endif
                            <p>ছুটি সমন্বয়&nbsp;&nbsp;&nbsp;</p>
                            <p style="border-top:1px solid #999">মোট প্রদেয়&nbsp;&nbsp;&nbsp;</p>
                        </td>
                        <!-- third -->
                        
                        <td  style="width: 12%;text-align: right; color:hotpink;" >

                            <p>
                                &nbsp;&nbsp;&nbsp; {{eng_to_bn($deduct)}} =/টঃ
                            </p>
                            <p>
                                @php
                                    $advanceDeductAmount = 0;
                                    if(isset($emp->partial_amount) && $emp->partial_amount > 0){
                                        $advanceDeductAmount += $emp->partial_amount??0;
                                    }
                                @endphp
                                &nbsp;&nbsp;&nbsp; {{eng_to_bn($advanceDeductAmount)}} =/টঃ
                            </p>
                            <p>&nbsp;&nbsp;&nbsp;{{ eng_to_bn($emp->salary_payable) }} =/টঃ</p>
                            <p>&nbsp;&nbsp;&nbsp;{{ eng_to_bn($ot) }} =/টঃ</p>
                            <p>&nbsp;&nbsp;&nbsp;{{ eng_to_bn($emp->attendance_bonus) }} =/টঃ</p>
                            <p>&nbsp;&nbsp;&nbsp;{{ eng_to_bn($emp->production_bonus) }} =/টঃ</p>
                            <p>&nbsp;&nbsp;&nbsp;
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

                             =/টঃ</p>
                            @if(isset($salaryAdjust[$emp->as_id][3]))
                            <p>&nbsp;&nbsp;&nbsp;
                                {{ eng_to_bn(number_format($salaryAdjust[$emp->as_id][3]->sum, 2) ??'0.00') }}
                            =/টঃ</p>
                            @endif
                            @if(isset($salaryAdjust[$emp->as_id][4]))
                            <p>&nbsp;&nbsp;&nbsp;
                                {{ eng_to_bn(number_format($salaryAdjust[$emp->as_id][4]->sum, 2) ??'0.00') }}
                            =/টঃ</p>
                            @endif
                            <p>&nbsp;&nbsp;&nbsp;{{ eng_to_bn($emp->leave_adjust) }} =/টঃ</p>
                            <p style="border-top:1px solid #999">&nbsp;&nbsp;&nbsp; {{ eng_to_bn(bn_money($emp->total_payable)) }}=/টঃ</p>
                        </td>
                    </tr>
                </table>
                @endforeach
                
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

<script type="text/javascript">

    $(".overlay-modal, .item_details_dialog").css("opacity", 0);
    /*Remove inline styles*/
    $(".overlay-modal, .item_details_dialog").removeAttr("style");
    /*Set min height to 90px after  has been set*/
    detailsheight = $(".item_details_dialog").css("min-height", "115px");
    $(document).on('click','.disbursed_salary',function(){
        let id = $(this).data('id');
        let associateId = $(this).data('eaid');
        let date = $(this).data('date');
        let name = $(this).data('name');
        let post = $(this).data('post');
        $("#modal-id").val(id);
        $("#modal-associateId").val(associateId);
        $("#modal-month").val($(this).data('month'));
        $("#modal-year").val($(this).data('year'));
        $('#disbursed_name').html(name);
        $('#disbursed_post').html(post);
        $('#disbursed_id').html('আইডি: '+associateId);
        $('#disbursed_body').html(date+' মাসের বেতন প্রদান করা হচ্ছে । ');
        /*Show the dialog overlay-modal*/
        $(".overlay-modal-details").show();
        $(".inner_body").show();
        /*Animate Dialog*/
        $(".show_item_details_modal").css("width", "225").animate({
          "opacity" : 1,
          height : detailsheight,
          width : "40%"
        }, 600, function() {
          /*When animation is done show inside content*/
          $(".fade-box").show();
        });
        // 
        
    });
    $("#confirm-disbursed").click(function() {
        let associate_id = $("#modal-associateId").val();
        let month = $("#modal-month").val();
        let year = $("#modal-year").val();
        let select_id = $("#modal-id").val();
        //alert(id);
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: '/hr/reports/employee-salary-disbursed',
            type: "post",
            data: { _token : _token,
                as_id: associate_id,
                year: year,
                month: month
            },
            success: function(response){
                console.log(response);
                if(response.status === 'success'){
                    $("#"+select_id).html( $.trim(response.value) ).effect('highlight',{},2500);
                }

            }
        });

        $(".overlay-modal-details, .show_item_details_modal").fadeOut("slow", function() {
          $(".overlay-modal, .item_details_dialog").removeAttr("style");
          $('body').css('overflow', 'unset');
        });
    });

    $(".cancel_details").click(function() {
        $(".overlay-modal-details, .show_item_details_modal").fadeOut("slow", function() {
          /*Remove inline styles*/

          $(".overlay-modal, .item_details_dialog").removeAttr("style");
          $('body').css('overflow', 'unset');
        });
    });
</script>
