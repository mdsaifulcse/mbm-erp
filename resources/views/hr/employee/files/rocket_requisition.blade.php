<div class="row justify-content-center">
	<div class="col-sm-12 mt-2">
                            
        <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('print-area')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>

    </div>
    <?php
        date_default_timezone_set('Asia/Dhaka');
        $en = array('0','1','2','3','4','5','6','7','8','9');
        $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯');
        $date = str_replace($en, $bn, date('Y-m-d H:i:s'));
    ?>
	<div id="print-area" class="col-sm-9">
		<style type="text/css">
				.mb-2 span {
				    width: 160px;
				    font-size: 12px !important;
				    display: inline-block;
				}

                .page-break{
                    page-break-after: always;
                }
                .page-break p{
                    
                    line-height: 25px;
                }
                .page-break b{
                    
                    line-height: 25px;
                }
                p, b{font-size: 16px;line-height: 25px;}
                td{
                    line-height: 25px;
                    font-size: 16px;
                }
                .list-group-flush > .list-group-item {
                    border-width: 1px 1px 1px;
                }
                .card {
                    z-index: 0;
                    border: none;
                    position: relative;
                    text-align: center;
                }
                .list-group-horizontal {
                    flex-direction: row;
                    justify-content: center;
                }
                .card > .list-group:last-child {
                    border-bottom-width: 0;
                    border-bottom-right-radius: calc(0.25rem - 1px);
                    border-bottom-left-radius: calc(0.25rem - 1px);
                }
                .card > .list-group:first-child {
                    border-top-width: 0;
                    border-top-left-radius: calc(0.25rem - 1px);
                    border-top-right-radius: calc(0.25rem - 1px);
                }
                .list-group-item {
                    position: relative;
                    display: block;
                    padding: 8px 1.25rem;
                    background-color: #fff;
                    border: 1px solid rgba(0, 0, 0, 0.125);
                    color: #000;
                }
                .list-group {
                    display: flex;
                }
			
		</style>
		<style type="text/css" media="print">
			.bn-form-output{padding:54pt 36pt }
		</style>
		@foreach($employees as $key => $emp)
		<div id="jc-{{$emp->associate_id}}" class="bn-form-output page-break" >
			@php
                $des['bn'] = '';
            	$des['en'] = '';
            	$un['name'] = '';
            	$un['address'] = '';
            	if(isset($designation[$emp->as_designation_id])){
            		$des['bn'] = $designation[$emp->as_designation_id]['hr_designation_name_bn'];
                    $des['en'] = $designation[$emp->as_designation_id]['hr_designation_name'];
            		
            	}
            	if(isset($unit[$emp->as_unit_id])){
            		$un['name'] = $unit[$emp->as_unit_id]['hr_unit_name_bn'];
            		$un['address'] = $unit[$emp->as_unit_id]['hr_unit_address_bn'];
            	}

            @endphp
                                        
                                      
            <center><b style="font-size: 14px;"> &nbsp; </b></center>
            <center><u > &nbsp; </u> </center>
            <br><br>
            <div style="display:flex;justify-content: space-between;">
                <div style="width: 70%;">
                    <p> তারিখ:</p>
                    <br>
                    <p> বরাবর,</p>
                    <p> ব্যবস্থাপনা পরিচালক</p>
                    <p> {{ $un['name'] }}</p>
                    <p> {{ $un['address'] }}</p>
                </div>
                <div style="width: 30%;">
                	{{-- photo block --}}
                    {{-- <div style="width: 100px;height:110px;border:1px solid;margin-left: auto; "></div> --}}
                </div>
            </div>
            
            <br>
            <p> <u> <b>বিষয়ঃ রকেট এ্যাকাউন্টের মাধ্যমে বেতন গ্রহনের আবেদন।</b></u></p>
            
            <br>
            <p>
                @if($emp->as_gender == 'Female')
                জনাবা,
                @else
                জনাব,
                @endif
            </p> 
            <br>
            <p>সবিনয় নিবেদন এই যে, আমি {{ (!empty($emp->hr_bn_associate_name )?$emp->hr_bn_associate_name:null) }} পদবী: {{$des['bn']}} আই.ডি. নং: {{ $emp->associate_id }},  গত {{ eng_to_bn($emp->as_doj) }} ইং তাারিখ থেকে অত্র প্রতিষ্ঠানে কর্মরত আছি । আমি আমার মাসিক বেতন এ অন্যান্য পাওনাদী যে এ্যাকাউন্টে গ্রহন করতে আগ্রহী তা নিম্নরুপ:</p>   
            <br>
            <br>
            <div class="card" >
              <ul class="list-group list-group-horizontal">
                  <li class="list-group-item"> &nbsp; </li>
                  <li class="list-group-item"> &nbsp; </li>
                  <li class="list-group-item"> &nbsp; </li>
                  <li class="list-group-item"> &nbsp; </li>
                  <li class="list-group-item"> &nbsp; </li>
                  <li class="list-group-item"> &nbsp; </li>
                  <li class="list-group-item"> &nbsp; </li>
                  <li class="list-group-item"> &nbsp; </li>
                  <li class="list-group-item"> &nbsp; </li>
                  <li class="list-group-item"> &nbsp; </li>
                  <li class="list-group-item"> &nbsp; </li>
                  <li class="list-group-item" style="font-size: 25px; font-weight: bold;"> - </li>
                  <li class="list-group-item"> &nbsp; </li>
                </ul>
            </div>
            <br>
            <br>
            <p>মতাবস্থায় জনাবের নিকট আবেদন আমার বেতন ও অন্যান্য পাওনাদী উল্লেখীত এ্যাকাউন্ট এর মাধ্যমে প্রদানের ব্যবস্থা করে বাধিত করবেন।</p>
            <br>
            <br>
            <br>
            <p>নিবেদক/নিবেদিকা</p>
            <table style="border: none; font-size: 12px;" width="45%" cellpadding="3">
                <tr>
                    <td  style="border: none;">নাম </td>
                    <td colspan="2" style="border: none; ">: {{ (!empty($emp->hr_bn_associate_name )?$emp->hr_bn_associate_name:null) }}</td>
                </tr>
                <tr>
                    <td  style="border: none;">আই.ডি. নং </td>
                    <td colspan="2" style="border: none; ">: {{ $emp->associate_id }}</td>
                </tr>
                <tr>
                    <td  style="border: none;">
                        <br>
                        <br>
                        স্বাক্ষর ও টিপসহি 
                    </td>
                </tr>
                
            </table>
		</div>
		
		@endforeach
	</div>
</div>   