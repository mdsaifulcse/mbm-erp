

<div class="row justify-content-center">
   <div class="col-sm-12 mt-2">

        <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('print-area')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>

    </div>
    <?php
        date_default_timezone_set('Asia/Dhaka');
        $en = array('0','1','2','3','4','5','6','7','8','9');
        $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯');

    ?>
   <div id="print-area" class="col-sm-9">
      <style type="text/css">
            .mb-2 span {
                width: 160px;
                display: inline-block;
            }
            p,b{font-size: 12px !important;}
                .page-break{
                    page-break-after: always;
                }
                .page-break p{

                    line-height: 16px;
                }
                .page-break b{

                    line-height: 16px;
                }
                @page
                {
                    size: auto;   /* auto is the initial value */

                    /* this affects the margin in the printer settings */
                    margin: 25mm 25mm 25mm 25mm;
                }
                .table-data-width td{
                    width:250px !important;

                }

                @media print {
                    .pagebreak { page-break-before: always; } /* page-break-after works, as well */
                }
                .app-title-letter{width:140px;display:inline-block}

      </style>
      <style type="text/css" media="print">
         .bn-form-output{padding:4pt 4pt }
      </style>

      @foreach($employees as $key => $emp)
        @php $date = $emp->as_doj; @endphp
      <div id="jc-{{$emp->associate_id}}" class="bn-form-output page-break" {{-- style="page-break-after: always;" --}} >


         @php
               $des['bn'] = '';
               $des['en'] = '';
               $des['grade'] = '';
               $un['name'] = '';
               $un['address'] = '';
                $un['signature'] = '';
                $dept = '';
               if(isset($designation[$emp->as_designation_id])){
                  $des['bn'] = $designation[$emp->as_designation_id]['hr_designation_name_bn'];
                  $des['en'] = $designation[$emp->as_designation_id]['hr_designation_name'];
                  $des['grade'] = $designation[$emp->as_designation_id]['hr_designation_grade'];
               }
               if(isset($unit[$emp->as_unit_id])){
                  $un['name'] = $unit[$emp->as_unit_id]['hr_unit_name'];
                  $un['address'] = $unit[$emp->as_unit_id]['hr_unit_address'];
                  $un['signature'] = $unit[$emp->as_unit_id]['hr_unit_authorized_signature'];

               }

               if(isset($department[$emp->as_department_id])){
                  $dept = $department[$emp->as_department_id]['hr_department_name'];
               }

            @endphp
            <br>
            <br>
            <br>
            <br>
            <p><b class="app-title-letter">Date</b>: {{date('d F, Y', strtotime($emp->as_doj))}}</p>
            <p><b  class="app-title-letter">Name&nbsp;</b>:
               @if($emp->as_gender == 'Female') Ms. @else Mr. @endif
               {{ (!empty($emp->as_name)?$emp->as_name:null) }}</p>
            <p style="margin-top:0 !important">
                <b  class="app-title-letter">Address (Present)</b>:
            </p>
            <p style="margin-top:0 !important"><b  class="app-title-letter">Mobile Number</b>: {{$emp->as_contact??''}}</p>
            <p style="margin-top:0 !important"><b  class="app-title-letter">Mailing Address</b>:</p>

            
            <br>
            <p style="margin:10px 0;"><span style="text-decoration: underline;"><strong>Subject:  Appointment Letter – {{ $des['en']}} </strong></span></p>
            <p>Dear @if($emp->as_gender == 'Female') Ms. @else Mr. @endif,</p>
            <p style="text-align: justify;margin: 5px 0;">With reference to your application and subsequent interview with us for employment, we are pleased to offer you an appointment in {{$un['name']}} as <b>{{ $des['en'] }} in {{$dept}} </b> , with effect from {{date('d F, Y', strtotime($emp->as_doj))}} on the following terms and conditions: </p>
            <p style="margin-bottom:5px;"><b>1. Salary and Benefits:</b> You shall be entitled to a monthly gross salary of Taka {{$emp->ben_joining_salary}} comprising of basic salary of Taka {{$emp->ben_basic}}, housing allowance of Taka {{$emp->ben_house_rent}} and others allowance (Medical, TA, Food) of Taka 1850 as per salary matrix. In addition, you shall be entitled to other admissible benefits on such terms as may be considered appropriate by the management from time-to-time. </p>
            <!-- custom for cew -->
            <p style="margin-bottom:5px;"><b>2. Payment of Income Tax:</b> You shall pay income tax which will be due on your remuneration (through payroll deduction) as per the Income Tax Regulations of the Government of Bangladesh.</p>

            <p style="margin-bottom:5px;"><b>3. Duration of Appointment:</b> Indefinite. However, continuity will depend on your satisfactory performance and also, needs of your service for the operation.</p>
            <p style="margin-bottom:5px;"><b>4. Probationary Period:</b>  You shall be required to serve a probationary period of 6 (six) months from the date of your appointment. During the probationary period, if you fail to perform your assigned duties as expected by your immediate Supervisor and Department Head, you will be separated from the job. However, an opportunity may be given to you for improvement on the areas of improvement as identified, the time for which shall be determined by the Department Head as per established pertinent policy. </p>

            <p style="margin-bottom:5px;"><b>5. Confirmation of Appointment:</b>  On successful completion of probationary period, you shall be confirmed in appointment for the said position.</p>

            <p style="margin-bottom:5px;"><b>6. Termination of Appointment:</b>  (a) During the probationary period, the appointment may be terminated by either party by giving 14 (Fourteen) days’ notice; (b) After confirmation, the appointment may be terminated by either party (without assigning any reasons, whatsoever), by giving 60 (Sixty) days’ notice or payment in lieu of notice thereof; (c) Without prejudice to any other remedies the Company may have against you, the Company shall have the right at any time to dismiss/terminate this employment forthwith by following due process of law, in case of having violated the Compliance norms provided under the “ZERO TOLERANCE POLICY” of the Company.</p>
            @php
                $shiftStart = date('H:i', strtotime($emp->hr_shift_start_time))??'';
                $shiftEnd = date('H:i', strtotime($emp->hr_shift_end_time))??'';
                $shiftBreak = $emp->hr_shift_break_time??0;
                $breakStart = '13:00';
                $breakEnd = \Carbon\Carbon::parse(date('Y-m-d').' 13:00:00')->addMinutes($shiftBreak)->format('H:i');
            @endphp

            <p style="margin-bottom:5px;"><b>7. Work Schedule:</b> Your work week shall be from Saturday through Thursday from {{ $shiftStart }} to {{ $shiftEnd }} with {{ $shiftBreak }} minute lunch break from {{ $breakStart }} to {{ $breakEnd }}. However, work schedule may vary according to the designated departmental requirements.</p>

            <p style="margin-bottom:5px;"><b>8. Place of Posting:</b> Your place of posting shall be at {{$un['name']}}, {{$un['address']}}, Bangladesh.</p>

            <p style="margin-bottom:5px;"><b>9. Job Description:</b>  Your duties shall be as per your job description, the contents of which shall be subject to review Head of Departments. </p>


            <p style="margin-bottom:5px;"><b>10. Report to:</b> __________________________</p>

            <p style="margin-bottom:5px;"><b>11. Increment/Promotion:</b> Increment & promotion are not automatically granted and/or awarded but shall be in accordance with the established pertinent policy which is subject to the change and/or modification any time at the discretion of the management.</p>  

            <p style="margin-bottom:5px;"><b>12. Leave and Holidays: </b> You shall be entitled to leave and holidays as per policy of the company. (Casual & Sick)</p>

            <p style="margin-bottom:5px;"><b>13. Attendance:</b>  Regular and timely attendance is an essential condition of your appointment. Any absence from duty without authorization or satisfactory explanations shall be treated as Absence-Without-Leave (AWOL) and subject you to disciplinary actions. </p>

            <div class="pagebreak"> </div>


            <p style="margin-bottom:5px;"><b>14. Misconduct:</b>  Any act of gross ‘misconduct’ (including any act detrimental to the interest of the company), shall result in your instant summary dismissal from the service without notice and loss of all benefits and privileges.</p>

            <p style="margin-bottom:5px;"><b>15. Declaration (with regard to Letter of Appointment and other Company Policies including Employee Code of Conduct):</b> You shall be bound by the terms and conditions, policies, rules and regulations of the company that are currently in force and any new terms and conditions, policies, rules and regulations that may be effective in future; provided however, you will not be entitled to any benefit or profit notwithstanding anything mentioned elsewhere other than those which have been clearly stipulated in this Letter of Appointment.</p>

            <p style="margin-bottom:5px;"><b>16. Declaration (with regard to confidential information):</b> You shall not disclose any information of the company to any person as to the practice, dealings and affairs of the company or any of its customers or as to any other matters which may come within your knowledge by reason of your employment in this company without written permission of the appropriate authority.</p>

            <p style="margin-bottom:5px;">17. The acceptance of your joining is subject to your fulfilling the terms and conditions as stated in the job offer and/or explained during discussion.</p>

            <p style="margin-bottom:5px;">If the above terms and conditions are acceptable to you, please sign and date all copies of this Letter of Appointment in the space provided below, and retain the original for your records.</p>

            <p style="margin:15px 0;">Sincerely,</p>
            <br>
            <br>
            <br>
            <br>
            <p>___________________________</p>
            <p>General Manager</p>
            <p>HR, Admin & Compliance</p> 
            <br><br>

            <p style="margin-bottom:5px;"><b>Declaration of understanding and acceptance of Letter Appointment:</b></p>

            <p>I, <b>{{$emp->as_name}}</b>, have fully understood the contents of the Letter of Appointment (which has been read and translated in Bengali) and willingly agree to abide by the terms and conditions as stipulated herein above.</p>
            <br>
            <br>

            <p>_____________________________  </p>                                                                                    
            <p>Signature of Employee</p>

            <p>Date: ____________</p>

            <p style="margin: 10px 0;">Distribution:</p>

            <p>1. F&A Department</p>
            <p>2. PCF</p>
            <br><br>
            <p>Attachment:</p>
            <p style="margin-left:15px;font-size: 10px!important">a.  Annexure-1 - Zero Tolerance Policy</p>
            <p style="margin-left:15px;font-size: 10px!important">b.  Annexure-2 - Non-Compete Clause</p>
        </div>



      @endforeach
   </div>
</div>
