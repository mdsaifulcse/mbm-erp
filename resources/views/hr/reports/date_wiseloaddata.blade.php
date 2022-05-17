

@if($Type==1)

<div class="main-content-inner">
  <div class="breadcrumbs ace-save-state" id="breadcrumbs">
    <ul class="breadcrumb">



      <table class="table table-bordered SERIAL_NUMBER">

        <thead style="text-align:center;" >
          <tr>
          </tr>
          <tr>
            <th scope="col" style="width:50px">SL.</th>
            <th style="width:400px">Company Name </th>
            <th>Recruit</th>
            <th>Recruit Salary</th>
            <th>Lefty</th>
            <th>Lefty Salary</th>
          </tr>
        </thead>
        <tbody>
          @php
          $sl=0;
          $total_new_recruit=0;
          $total_new_recruit_salary=0;
          $total_lefty=0;
          $total_lefty_salary=0;
          @endphp
          @foreach($hr_basic_info_status_Group as $hr_basic_info_status_Group)
          @php
          $total_new_recruit+=$hr_basic_info_status_Group->new_recruit;
          $total_new_recruit_salary+=$hr_basic_info_status_Group->new_recruit_salary;
          $total_lefty+=$hr_basic_info_status_Group->lefty;
          $total_lefty_salary+=$hr_basic_info_status_Group->lefty_salary;
          @endphp
          <tr>
            <td style="text-align:center;">{{++$sl}}</td>
            <td style="text-align:left;">{{ $hr_basic_info_status_Group->hr_unit_name}}</td>
            <td style="text-align:center;">{{ $hr_basic_info_status_Group->new_recruit}}</td>
            <td style="text-align:right;">{{bn_money ($hr_basic_info_status_Group->new_recruit_salary)}}</td>
            <td style="text-align:center;">{{ $hr_basic_info_status_Group->lefty}}</td>
            <td style="text-align:right;">{{bn_money ($hr_basic_info_status_Group->lefty_salary)}}</td>
          </tr>

          @endforeach
          <tr>
           <td>
             <td style="text-align:right;"><b>Total :</b></td>
             <td style="text-align:center;"><b>{{$total_new_recruit}}</b></td>
             <td style="text-align:right;"><b>{{bn_money ($total_new_recruit_salary)}}</b></td>
             <td style="text-align:center;"><b>{{$total_lefty}}</b></td>
             <td style="text-align:right;"><b>{{bn_money ($total_lefty_salary)}}</b></td>
           </td>
         </tr>

         <tr>
           @php
           $salary_difference =($total_new_recruit_salary -$total_lefty_salary);
           @endphp
              <td colspan="2" style="text-align:right;"><b>Salary Difference :</b></td>
               <td colspan="5" style="text-align:left;"><b>{{bn_money ($salary_difference)}}</b></td>
         </tr>

       </tbody>
     </table>
   </ul>
 </div>
 <div class="page-content">

  <div class="row">
    <div class="col">

    </div>
  </div>
</div><!-- /.page-content -->
</div>
@endif







@if($Type==2)

<div class="main-content-inner">
  <div class="breadcrumbs ace-save-state" id="breadcrumbs">
    <ul class="breadcrumb">

      {{-- <h4 style="margin:4px 10px; font-weight: bold; text-align: left;;font-size:12px;">Left Employee     </h4> --}}


      @php
      $gnd_total_new_recruit=0;
      $gnd_total_new_recruit_salary=0;
      $gnd_total_lefty=0;
      $gnd_total_lefty_salary=0;
      $gnd_total_difference=0;

      @endphp
      @foreach($hr_basic_info_date_Group as $key =>  $px)


      <table class="table table-bordered SERIAL_NUMBER">

        <thead style="text-align:center;" >
          <tr>
            <th colspan="6" style="text-align:left;"> Month : {{$key}}</th>
          </tr>
          <tr>
            <th scope="col" style="width:50px">SL.</th>
            <th style="width:400px">Company Name </th>
            <th>Recruit</th>
            <th>Recruit Salary</th>
            <th>Lefty</th>
            <th>Lefty Salary</th>
          </tr>
        </thead>
        <tbody>


         @php
         $sl=0;
         $subtotal=0;
         $subtotal_emp=0;

         $total_new_recruit=0;
         $total_new_recruit_salary=0;
         $total_lefty=0;
         $total_lefty_salary=0;

         @endphp
         @foreach($px as $p1)
         @php
                                                 // $subtotal+=$p1->new_recruit;
                                                 // $subtotal_emp+=$p1->new_recruit;
                                                 // $grand_total+=$p1->new_recruit;
      

         $total_new_recruit+=$p1->new_recruit;
         $total_new_recruit_salary+=$p1->new_recruit_salary;
         $total_lefty+=$p1->lefty;
         $total_lefty_salary+=$p1->lefty_salary;

         $gnd_total_new_recruit+=$p1->new_recruit;
         $gnd_total_new_recruit_salary+=$p1->new_recruit_salary;
         $gnd_total_lefty+=$p1->lefty;
         $gnd_total_lefty_salary+=$p1->lefty_salary;
         // $gnd_total_difference=

         @endphp

         <tr>

          <td style="text-align:center;"> {{++$sl}}</td>
          <td>{{ $p1->hr_unit_name}}</td>
          <td style="text-align:center;">{{ $p1->new_recruit}}</td>
          <td style="text-align:right;">{{bn_money ($p1->new_recruit_salary)}}</td>
          <td style="text-align:center;">{{ $p1->lefty}}</td>
          <td style="text-align:right;">{{bn_money ($p1->lefty_salary)}}</td>

        </tr>


        @endforeach
        <tr>
          <td colspan="2" style="text-align:right;"> <b>Sub Total </b>:</td>
          <td style="text-align:center;"> <b> {{$total_new_recruit}} </b></td>
          <td style="text-align:right;"> <b> {{bn_money($total_new_recruit_salary)}} </b></td>

          <td style="text-align:center;"> <b> {{$total_lefty}} </b></td>

          <td style="text-align:right;"> <b> {{bn_money($total_lefty_salary)}} </b></td>


        </tr>


        <br> <br>


        @endforeach
        <tr>

          <td colspan="2" style="text-align:right;"> <b>Grand Total </b>:</td>
          <td style="text-align:center;"> <b> {{$gnd_total_new_recruit}} </b></td>
          <td style="text-align:right;"> <b> {{bn_money($gnd_total_new_recruit_salary)}} </b></td>

          <td style="text-align:center;"> <b> {{$gnd_total_lefty}} </b></td>
          <td style="text-align:right;"> <b> {{bn_money($gnd_total_lefty_salary)}} </b></td>
          {{-- <td style="text-align:right;"> <b> {{bn_money($gnd_total_new_recruit_salary)}} </b></td> --}}
        </tr>

        <tr>
          <td colspan="2" style="text-align:right;"> <b>Grand Total Salary Difference : </b>:</td>

          @php
          $gnd_total_difference=$gnd_total_new_recruit_salary-$gnd_total_lefty_salary;
          @endphp
          <td  style="text-align:center;"> <b> {{bn_money($gnd_total_difference)}} </b></td>

        </tr>

      </tbody>
    </table>
  </ul>
</div>
<div class="page-content">

  <div class="row">
    <div class="col">

    </div>
  </div>
</div><!-- /.page-content -->
</div>
@endif









@if($Type==3)

<div class="main-content-inner">
  <div class="breadcrumbs ace-save-state" id="breadcrumbs">
    <ul class="breadcrumb">



      <table class="table table-bordered SERIAL_NUMBER">

        <thead style="text-align:center;" >
          <tr>
          </tr>
          <tr>
            <th scope="col" style="width:50px">SL.</th>
            <th style="width:400px">Company Name </th>
            <th>Recruit</th>
            <th>Recruit Salary</th>
            <th>Lefty</th>
            <th>Lefty Salary</th>
          </tr>
        </thead>
        <tbody>
          @php
          $sl=0;
          $total_new_recruit=0;
          $total_new_recruit_salary=0;
          $total_lefty=0;
          $total_lefty_salary=0;
          @endphp
          @foreach($hr_basic_info_dept_Group as $hr_basic_info_dept_Group)
          @php
          $total_new_recruit+=$hr_basic_info_dept_Group->new_recruit;
          $total_new_recruit_salary+=$hr_basic_info_dept_Group->new_recruit_salary;
          $total_lefty+=$hr_basic_info_dept_Group->lefty;
          $total_lefty_salary+=$hr_basic_info_dept_Group->lefty_salary;
          @endphp
          <tr>
            <td style="text-align:center;">{{++$sl}}</td>
            <td style="text-align:left;">{{ $hr_basic_info_dept_Group->hr_department_name}}</td>
            <td style="text-align:center;">{{ $hr_basic_info_dept_Group->new_recruit}}</td>
            <td style="text-align:right;">{{bn_money ($hr_basic_info_dept_Group->new_recruit_salary)}}</td>
            <td style="text-align:center;">{{ $hr_basic_info_dept_Group->lefty}}</td>
            <td style="text-align:right;">{{bn_money ($hr_basic_info_dept_Group->lefty_salary)}}</td>
          </tr>

          @endforeach
          <tr>
           <td>
             <td style="text-align:right;"><b>Total :</b></td>
             <td style="text-align:center;"><b>{{$total_new_recruit}}</b></td>
             <td style="text-align:right;"><b>{{bn_money ($total_new_recruit_salary)}}</b></td>
             <td style="text-align:center;"><b>{{$total_lefty}}</b></td>
             <td style="text-align:right;"><b>{{bn_money ($total_lefty_salary)}}</b></td>
           </td>
         </tr>

          <tr>
           @php
           $salary_difference =($total_new_recruit_salary -$total_lefty_salary);
           @endphp
              <td colspan="2" style="text-align:right;"><b>Salary Difference :</b></td>
               <td colspan="5" style="text-align:left;"><b>{{bn_money ($salary_difference)}}</b></td>
         </tr>


       </tbody>
     </table>
   </ul>
 </div>
 <div class="page-content">

  <div class="row">
    <div class="col">

    </div>
  </div>
</div><!-- /.page-content -->
</div>
          @endif
