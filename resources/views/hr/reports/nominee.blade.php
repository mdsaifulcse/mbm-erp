@extends('hr.layout')
@section('title', 'Nominee')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Recruitment</a>
                </li>
                <li class="active"> Nominee</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content"> 
            <div class="row">
                <form class="col-12" role="form" method="get" action="{{ url('hr/recruitment/nominee') }}">
                    <div class="panel">
                        <div class="panel-heading">
                            <h6>Nominee</h6>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                
                                <div class="col-offset-2 col-4">
                                    
                                    <div class="form-group has-float-label has-required select-search-group">
                                        
                                        {{ Form::select('associate', [Request::get('associate_id') => Request::get('associate_id')], Request::get('associate_id'),['placeholder'=>'Select Associate\'s ID', 'data-validation'=> 'required','id'=>'associate',  'class'=> 'associates no-select col-xs-12', 'required' => 'required']) }} 
                                        <label  for="associate"> Associate's ID </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-search"></i>
                                        Search
                                    </button>
                                    @if(!empty(Request::get('associate')))
                                    <button type="button" onclick="printMe('PrintArea')" class="btn btn-warning" title="Print">
                                        <i class="fa fa-print"></i> 
                                    </button>
                                    <a href="{{request()->fullUrl()}}&pdf=true" target="_blank" class="btn btn-danger" title="PDF">
                                        <i class="fa fa-file-pdf-o"></i> 
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            @include('inc/message')
            <div class="row">
                <!-- Display Erro/Success Message -->
                @if(!empty(Request::get('associate')))
                <div class="col-12">
                    
                    <div class="panel p-30">
                        <div class="form-group">
                            <div class="col-xs-12">
                            <div class="tinyMceLetter" id="PrintArea">
                                <style type="text/css" media="all">
                                    .d-flex{
                                        display: flex;
                                    }
                                    .bordered{
                                        border-bottom: 1px dotted #777;
                                        flex-grow: 1;
                                    }
                                    p{
                                        margin: 5px;padding:0;
                                    }
                                </style>
                                <?php
                                date_default_timezone_set('Asia/Dhaka');
                                $en = array('0','1','2','3','4','5','6','7','8','9', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
                                $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯', 'জানুয়ারী', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর');
                                $date = str_replace($en, $bn, date('Y-m-d H:i:s'));
                                ?>
                                <center><h1>নমিনী মনোনয়ন</h1></center>
                                <center><h2>ফরম-৪১</h2></center>
                                <center><p>[ধারা ১৯, ১৩১(১)(ক), ১৫৫(২), ২৩৪, ২৪৬, ২৬৫ ও ২৭৩ এবং বিধি ১১৮(১), ১৩৬, ২৩২(২), ২৬২(১), ২৮৯(১) ও ৩২১(১) দ্রষ্টব্য] <br/>জমা ও বিভিন্নখাতে প্রাপ্য অর্থ পরিশোধের ঘোষণা ও মনোনয়ন ফরম </p></center> 
                                <br/>


                                <p>১ । প্রতিষ্ঠানের নামঃ&nbsp;{{ (!empty($info->hr_unit_name_bn)?$info->hr_unit_name_bn:null) }}</p>
                                <p>২ । প্রতিষ্ঠানের ঠিকানাঃ&nbsp;{{ (!empty($info->hr_unit_address_bn)?$info->hr_unit_address_bn:null) }}</p>
                                <p class="d-flex">
                                    <font style="width:250px">৩ । কর্মকর্তা/কর্মচারী/শ্রমিকের নাম ও ঠিকানাঃ &nbsp;</font>
                                    <font class="bordered">
                                        {{ (!empty($info->hr_bn_associate_name)?$info->hr_bn_associate_name:null) }} 
                                        
                                    </font>
                                </p>
                                <p class="d-flex">
                                    <font style="width:60%;border-bottom:1px dotted #999; display:inline-block">&nbsp; 
                                        ({{ (!empty($info->hr_bn_present_road)?"রোড নং-".$info->hr_bn_present_road:null) }},
                                        {{ (!empty($info->hr_bn_present_house)?"বাড়ি নং-".$info->hr_bn_present_house:null) }},
                                        {{ (!empty($info->hr_bn_present_po)?"ডাকঘর-".$info->hr_bn_present_po:null) }},
                                        {{ (!empty($info->present_upazilla_bn)?"উপজেলা-".$info->present_upazilla_bn:null) }},
                                        {{ (!empty($info->present_district_bn)?"জেলা-".$info->present_district_bn:null) }} )
                                    </font>লিঙ্গঃ<font style="width:36%;border-bottom:1px dotted #999;flex-grow: 1">&nbsp;{{ ((!empty($info->as_gender) && $info->as_gender=="Male")?"পুরুষ":"মহিলা") }}
                                    </font>
                                </p>
                                
                                <p class="d-flex">
                                    <font style="width:150px">
                                    ৪ । পিতা/মাতা/{{ ((!empty($info->as_gender) && $info->as_gender=="Male")?"স্ত্রীর":"স্বামীর") }}  নামঃ 
                                    </font>
                                    &nbsp;
                                    <font class="bordered">{{ (!empty($info->hr_bn_father_name)?$info->hr_bn_father_name:null) }}/{{ (!empty($info->hr_bn_mother_name)?$info->hr_bn_mother_name:null) }}/{{ (!empty($info->hr_bn_spouse_name)?$info->hr_bn_spouse_name:null) }}
                                    </font>
                                </p>
                                
                                <p >
                                    <font style="width:25%">
                                    ৫ । জন্ম তারিখঃ</font>&nbsp;
                                    তারিখঃ&nbsp;
                                    <font style="width:25%;border-bottom:1px dotted #999; display:inline-block; text-align: center;">
                                        {{ (!empty($info->as_dob)?str_replace($en,$bn, date("d", strtotime($info->as_dob))):null) }}&nbsp;
                                    </font>
                                    মাসঃ&nbsp;
                                    <font style="width:25%;border-bottom:1px dotted #999;text-align: center; display:inline-block">
                                        {{ (!empty($info->as_dob)?str_replace($en,$bn, date("F", strtotime($info->as_dob))):null) }}&nbsp;
                                    </font>
                                    বছরঃ&nbsp;
                                    <font style="width:25%;border-bottom:1px dotted #999;text-align: center; display:inline-block">
                                        {{ (!empty($info->as_dob)?str_replace($en,$bn, date("Y", strtotime($info->as_dob))):null) }}&nbsp;
                                    </font>
                                </p>
                                <p>
                                    <font style="width:19%;display:inline-block">৬ । সনাক্তকরণ চিন্হ (যদি থাকে)</font>
                                    <font style="width:76%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                                    </font>
                                </p>
                                
                                <p class="d-flex">
                                    <font style="width:14%;display:inline-block">৭ । স্থায়ী ঠিকানাঃ</font>
                                    গ্রামঃ<font style="width:40%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                                        {{ (!empty($info->hr_bn_permanent_village)?$info->hr_bn_permanent_village:null) }}
                                    </font>
                                    ডাকঘরঃ<font style="width:40%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                                        {{ (!empty($info->hr_bn_permanent_po)?$info->hr_bn_permanent_po:null) }}
                                    </font>
                                </p>
                                
                                <p class="d-flex">
                                    <font style="width:14%;display:inline-block">&nbsp;</font>
                                    থানাঃ<font style="width:40%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                                        {{ (!empty($info->permanent_upazilla_bn)?$info->permanent_upazilla_bn:null) }}
                                    </font>
                                    জেলাঃ<font style="width:40%;border-bottom:1px dotted #999; display:inline-block">&nbsp;
                                        {{ (!empty($info->permanent_district_bn)?$info->permanent_district_bn:null) }}
                                    </font>
                                </p> 
                                
                                <p class="d-flex">
                                    <font style="width:18%;display:inline-block">৮ । চাকরিতে নিযুক্তির তারিখঃ</font>
                                    <font class="bordered">
                                        {{ (!empty($info->as_doj)?str_replace($en,$bn, date("d F, Y", strtotime($info->as_doj))):null) }}&nbsp;
                                    </font>
                                </p>
                            
                                <p class="d-flex">
                                    <font style="width:10%;display:inline-block">৯ । পদের নামঃ</font>
                                    <font class="bordered">
                                        {{ (!empty($info->hr_designation_name_bn)?$info->hr_designation_name_bn:null) }}
                                    </font>
                                </p> 

                                <p style="text-align:justify;"><br>আমি এতদ্বারা ঘোষণা করিতেছি যে, আমার মৃত্যু হইলে বা আমার অবর্তমানে, আমার অনুকূলে জমা ও বিভিন্নখাতে প্রাপ্য টাকা গ্রহণের জন্য আমি নিন্মবর্ণিত ব্যক্তিকে/ব্যক্তিগণকে মনোনয়ন দান করিতেছি এবং নির্দেশ দিচ্ছি যে, উক্ত টাকা নিম্নবর্ণিত পদ্ধতিতে মনোনীত ব্যাক্তিদের মধ্যে বন্টন করিতে হইবেঃ</p>

                                <table style="font-size:11px; border-collapse: collapse;" align="center" width="100%" border="1" cellspacing="0" cellpadding="4">
                                    <thead>
                                        <tr>
                                            <td width="45%" align="center">
                                                মনোনীত ব্যক্তি বা ব্যক্তিদের নাম, ঠিকানা ও ছবি <br/>
                                                (নমিনীর ছবি ও স্বাক্ষর কর্মকর্তা/কর্মচারী/শ্রমিক কর্তৃক সত্যায়িত) <br/>
                                                এন আই ডি নং  
                                            </td>
                                            <td width="15%" align="center">সদস্যদের সহিত মনোনীত ব্যক্তিদের সম্পর্ক</td>
                                            <td width="10%" align="center">বয়স</td>
                                            <td width="30%" align="center" colspan="2">প্রত্যেক মনোনীত ব্যক্তিকে দেয় অংশ</td>
                                        </tr>
                                        <tr>
                                            <td align="center">(১)</td>
                                            <td align="center">(২)</td>
                                            <td align="center">(৩)</td>
                                            <td align="center" colspan="2">(৪)</td>
                                        </tr>
                                        <tr>
                                            <td align="center"  rowspan="2" style="height: 80px"></td>
                                            <td align="center"  rowspan="2"></td>
                                            <td align="center"  rowspan="2"></td>
                                            <td align="center" width="15%">জমাখাত</td>
                                            <td align="center" width="15%">অংশ</td> 
                                        </tr> 
                                        <tr>
                                            <td align="center">বকেয়া মজুরি </td>
                                            <td align="center"></td> 
                                        </tr>
                                        <tr>
                                            <td align="center" rowspan="2" style="height: 80px"></td>
                                            <td align="center" rowspan="2"></td>
                                            <td align="center" rowspan="2"></td>
                                            <td align="center">প্রভিডেন্ট ফান্ড </td>
                                            <td align="center"></td> 
                                        </tr>
                                        <tr>
                                            <td align="center">বীমা</td>
                                            <td align="center"></td> 
                                        </tr>
                                        <tr>
                                            <td align="center" rowspan="2" style="height: 80px"></td>
                                            <td align="center" rowspan="2"></td>
                                            <td align="center" rowspan="2"></td>
                                            <td align="center">দুর্ঘটনার ক্ষতিপূরণ</td>
                                            <td align="center"></td> 
                                        </tr>
                                        <tr>
                                            <td align="center">লভ্যাংশ</td>
                                            <td align="center"></td> 
                                        </tr>
                                        <tr>
                                            <td align="center" style="height: 80px;"></td>
                                            <td align="center"></td>
                                            <td align="center"></td>
                                            <td align="center">অন্যান্য</td>
                                            <td align="center"></td> 
                                        </tr> 
                                    </thead>
                                    <tbody>
                                        <tr></tr>
                                    </tbody>
                                </table>

                                <p style="margin:20px auto;text-align:justify;">প্রত্যয়ন করিতেছি যে, আমার উপস্থিতিতে জনাব/জনাবা <font style="width:160px;border-bottom:1px dotted #999; display:inline-block"></font> লিপিবদ্ধ বিবরণসমূহ পাঠ করিবার পর উক্ত ঘোষণা সাক্ষর করিয়াছেন। </p>

                                <div style="display: flex;justify-content: space-between;">
                                    <div style="width: 50%">
                                        <p style="text-align:left;">
                                            <br><br><br>
                                            <font style="width:100%;border-top:1px dotted #999; display:inline-block;padding-top:10px">তারিখ সহ মনোনীত ব্যক্তিগণের স্বাক্ষর অথবা টিপসই <br/>
                                            (কর্মকর্তা/কর্মচারী//শ্রমিক কর্তৃক সত্যায়িত ছবি)</font> <br>
                                            <div style="width:120px;height: 120px;border:1px solid;"></div> 
                                        </p> 
                                        
                                    </div>
                                    <div style="width: 50%">
                                        <p style="text-align:right;">
                                            <br/>
                                            <font style="width:50%;border-bottom:1px dotted #999; display:inline-block">&nbsp;</font><br/><br/><br>
                                            <font style="width:50%;display:inline-block;text-align:left">মনোনয়ন প্রদানকারী কর্মকর্তা/কর্মচারী/শ্রমিকের স্বাক্ষর, টিপসই ও তারিখ</font><br/>
                                        </p>
                                        <p style="text-align:right;">
                                            <br><br><br>
                                            <font style="width:50%;border-bottom:1px dotted #999; display:inline-block">&nbsp;</font><br/><br/>
                                            <font style="width:50%;display:inline-block;text-align:left">মালিকের বা প্রধিকারপ্রাপ্ত কর্মকর্তার স্বাক্ষর ও তারিখ</font><br/>
                                            <font style="width:50%;border-bottom:1px dotted #999; display:inline-block">&nbsp;</font>
                                        </p>
                                        
                                    </div>
                                </div>
                                

                                        

                                
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<script type="text/javascript"> 
    $(document).ready(function(){
        function formatState (state) {
            //console.log(state.element);
            if (!state.id) {
                return state.text;
            }
            var baseUrl = "/user/pages/images/flags";
            var $state = $(
            '<span><img /> <span></span></span>'
            );
            // Use .text() instead of HTML string concatenation to avoid script injection issues
            var targetName = state.name;
            $state.find("span").text(targetName);
            // $state.find("img").attr("src", baseUrl + "/" + state.element.value.toLowerCase() + ".png");
            return $state;
        };

    })

    function printMe(el)
    { 
        var myWindow=window.open('','','width=800,height=800');
        myWindow.document.write('<html><head></head><body style="font-size:9px;">');
        myWindow.document.write(document.getElementById(el).innerHTML);
        myWindow.document.write('</body></html>');
        // myWindow.document.close();
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }
    function attLocation(loc){
     window.location = loc;
   }
</script>
@endpush
@endsection