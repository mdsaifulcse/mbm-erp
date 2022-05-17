<?php
date_default_timezone_set('Asia/Dhaka');
$en = array('0','1','2','3','4','5','6','7','8','9');
$bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯');

?>
@php 
$date = str_replace($en, $bn, date('d-m-Y')); 
@endphp

<div class="container">

    <div class="w-100" style="overflow: hidden;">
        <div  style="width:8%; float: left;">
            &nbsp;
        </div>
        <div style="width:84%; float: left;">
             <br><br>
            <center><b style="font-size: 18px; line-height: 20px;"> হাতেহাতে</b></center>
            <br>
            <br>
            <p style="font-size: 16px; line-height: 20px;" >তারিখঃ&nbsp; {{$date}}&nbsp;ইং</p>
            <br> <br> <br>
            <p style="font-size: 14px; line-height: 20px;">নামঃ&nbsp; {{$data->hr_bn_associate_name}}</p>
            <p style="font-size: 14px; line-height: 20px;">পদবীঃ&nbsp; {{$data->hr_designation_name_bn}}</p>
            <p style="font-size: 14px; line-height: 20px;">সেকশনঃ&nbsp; {{$data->hr_department_name_bn}}</p>
            <p style="font-size: 14px; line-height: 20px;">আই ডি নং-&nbsp;{{$data->associate_id}}</p>
            <br>
        
            <p style="font-size: 16px; line-height: 27px;"><b style="font-size: 16px; line-height: 27px;">বিষয়ঃ অভিযোগ পত্র &nbsp;</b></p>
            <br> <br>

            <p>
                @if($data->as_gender == 'Female')
                জনাবা,
                @else
                জনাব,
                @endif
            </p>
            <br>
            <p style="font-size: 14px; line-height: 20px;text-align: justify;">
                আপনার বিরুদ্ধে গুরুত্বর অসদাচরনের অভিযোগ পাওয়া গিয়াছে যে, আপনি বেশীরভাগ কর্মদিবস ভাবে অফিসে বিলম্বে উপস্থিত হন, বিলম্বে অফিসে আসাটা আপনার একটি নিত্য নৈমিত্তিক অভ্যাসে পরিনত হয়েছে। এ ব্যপারে অনেকবার আপনাকে মৌখিকভাবে সাবধান করা হয়েছে কিন্তু তবুও আপনি আপনার বিলম্বে উপস্থিত হওয়ার বিষয়ে সচেতন হননি। আপনি গত ছয় মাসে যে কয়দিন বিলম্বে অফিসে এসেছেন তার বিবরণ নিম্নে দেওয়া হইলঃ 
            </p>
                <br><br>


            <div class="container">

                <table class="table table-bordered" style="width:70%;margin-left:auto; margin-right:auto;"  id="monthdata">
                    <thead >
                        <tr >
                            <th style=" text-align:center; width:200px;">মাস</th>
                            <th style="text-align:center;">বিলম্বে উপস্থিতের মোট দিন </th>
                 
                        </tr>
                    </thead>
                    <tbody >
                        @foreach($data1 as $data2)
                        <tr>
                            
                            <td style=" text-align:Left;">{{$data2->months}}</td>
                            <td style=" text-align:center;">{{$data2->days}}</td>
                           
                           
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>



            <br><br>
            <p style="font-size: 14px; line-height: 20px;text-align: justify;">
                কর্মক্ষেত্রে আপনার অভ্যাসগত বিলম্বে উপস্থিত হওয়া বাংলাদেশ শ্রম আইন ২০০৬ (২৩ ধারা এর ঙ )  এর মতে অসদাচরনের আওতায় পড়ে। 
                সুতরাং, আপনার এরূপ অসদাচরনের জন্য কেন আপনার বিরুদ্ধে শাস্তিমূলক ব্যবস্থা গ্রহণ করা হবে না তাহা অত্র পত্র প্রাপ্তির ৭ (সাত) কর্ম দিবসের মধ্যে নিম্ন স্বাক্ষরকারীর নিকট তার কারণ দর্শানোর জবাব দেয়ার নির্দেশ দেওয়া হচ্ছে। আপনার লিখিত জবাব উক্ত সময়ের মধ্যে নিম্ন স্বাক্ষরকারীর কাছে পৌছাতে হবে। অন্যথায় কর্তৃপক্ষ আপনার বিরুদ্ধে একতরফা ভাবে শাস্তিমুলক ব্যবস্থা গ্রহন করিতে বাধ্য হবেন।   

            </p>
        </div>
        <div  style="width:8%; float: left;">
            &nbsp;
        </div>
    </div>

    <div class="w-100"  style="overflow: hidden;">

        <div style="width:8%; float: left;">
            &nbsp;
        </div>

        <div  style="width:52%; float: left;">
            <br><br><br><br><br><br><br><br>
            <p style="font-size: 14px; line-height: 20px;"> মাননীয় এম ডি স্যার</p>
            <p style="font-size: 14px; line-height: 20px;"> (সদয় অবগতির জন্য)</p>
            <p ><u style="font-size: 14px; line-height: 20px;">অনুলিপি</u> </p>
            <br><br>
            <p style="font-size: 14px; line-height: 20px;"> ১। সহকারী ব্যবস্থাপনা পরিচালক (অবগতির জন্য)</p>
            <p style="font-size: 14px; line-height: 20px;"> ২। প্রধান নির্বাহী কর্মকর্তাগণ</p>
            <p style="font-size: 14px; line-height: 20px;"> ৩। মহা ব্যবস্থাপক (প্রডাকশন)</p>
            <p style="font-size: 14px; line-height: 20px;"> ৪। উপ মহা ব্যবস্থাপক (এইচ আর, এ্যাডমিন এন্ড কমপ্লায়েন্স)</p>
            <p style="font-size: 14px; line-height: 20px;"> ৫। ব্যক্তিগত নথি</p>
        </div>
        <div  style="width:32%; float: left;text-align:center;">
            <br><br><br><br><br><br><br><br>
            <p style="font-size: 14px; line-height: 20px;"> {{$signatory_name->hr_bn_associate_name}}</p>
            <p style="font-size: 14px; line-height: 20px;">{{$signatory_name->hr_designation_name_bn}} </p>
            <p style="font-size: 14px; line-height: 20px;">{{$signatory_name->hr_department_name_bn}}, এডমিন অ্যান্ড কমপ্লাইন্স</p>
            <p style="font-size: 14px; line-height: 20px;">{{$signatory_name->hr_unit_name_bn}}</p>



        </div>
        <div style="width:8%; float: left; ">
            &nbsp;
        </div>

    </div>

</div>

