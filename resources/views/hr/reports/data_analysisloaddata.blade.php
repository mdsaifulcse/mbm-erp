

 <div class="main-content-inner">
  <div class="breadcrumbs ace-save-state" id="breadcrumbs">
    <ul class="breadcrumb">
      <table class="table table-bordered SERIAL_NUMBER" style="text-align:center;">

        <thead style="text-align:center;" >
          <tr>
            <th colspan="9" style="text-align:center;"> 
            <p id="sectionsubsection" ></p> 
          </th>
            <th colspan="8" style="text-align:center;"> All Employee</th>
          </tr> 
          <tr>
            <th scope="col" style="width:50px">SL.</th>
            <th style="width:400px">Working Day </th>
            <th>Total Employee</th>
            <th>Present</th>
            <th>leave</th>
            <th>Absent</th>
            <th>Absent %</th>
            <th>New Recruit</th>
            <th>Left </th>
            <th>New Recruit All</th>
            <th>Total Employee All</th>
            <th>Present All</th>
            <th>leave All</th>
            <th>Absent All</th>
            <th>Absent % All</th>
            <th>Left ALL</th>
            <th>Left % All</th>

          </tr>
        </thead>
        <tbody>
            @php 
            $sl=0;
            $total_leave_op=0;
            $total_absent_op=0;
            $total_absent_per_op=0;
            $total_new_rec_op=0;
            $total_left_op=0;
            $total_new_rec_all=0;
            $total_leave_all=0;
            $total_absent_all=0;
            $total_absent_per_all=0;
            $total_left_all=0;
            $total_left_all_per=0;
            @endphp
          @foreach($hr_basic_info_details as $hr_basic_info_details)
  @php
  $total_ot_emp=$hr_basic_info_details->present + $hr_basic_info_details->leaves + $hr_basic_info_details->absent;
     $absent_per= 0;
     $absent_per1= 0;
     $left_per= 0;

  if ($total_ot_emp>0)
  {
  $absent_per= round((($hr_basic_info_details->absent*100)/$total_ot_emp),2);
  }


  $total_ot_emp_all=$hr_basic_info_details->present1 + $hr_basic_info_details->leaves1 + $hr_basic_info_details->absent1;
  $absent_per1= 0;

  if ($total_ot_emp_all>0)
  {
  $absent_per1= round((($hr_basic_info_details->absent1*100)/$total_ot_emp_all),2);
  $left_per= round((($hr_basic_info_details->lefty1*100)/$total_ot_emp_all),2);
  }

            $total_leave_op+=$hr_basic_info_details->leaves;
            $total_absent_op+=$hr_basic_info_details->absent;
            $total_absent_per_op+=$absent_per;
            $total_new_rec_op+=$hr_basic_info_details->new_recruit;
            $total_left_op+=$hr_basic_info_details->lefty;
            $total_new_rec_all+=$hr_basic_info_details->new_recruit1;
            $total_leave_all+=$hr_basic_info_details->leaves1;
            $total_absent_all+=$hr_basic_info_details->absent1;
            $total_absent_per_all+=$absent_per1;
            $total_left_all+=$hr_basic_info_details->lefty1;
            $total_left_all_per+=$left_per;

           
  @endphp  

         <tr>
                <td>{{++$sl}}</td>  
                  <td>{{ $hr_basic_info_details->cur_working_date}}</td>  
                    <td>{{ $total_ot_emp}}</td>  
                      <td>{{  $hr_basic_info_details->present}}</td>
                      <td>{{ $hr_basic_info_details->leaves}}</td> 
                      <td>{{ $hr_basic_info_details->absent}}</td> 
                      <td>{{ $absent_per}}%</td> 
                      <td>{{ $hr_basic_info_details->new_recruit}}</td> 
                      <td>{{ $hr_basic_info_details->lefty}}</td> 
                      <td>{{ $hr_basic_info_details->new_recruit1}}</td> 
                      <td>{{ $total_ot_emp_all}}</td> 
                      <td>{{ $hr_basic_info_details->present1}}</td> 
                      <td>{{ $hr_basic_info_details->leaves1}}</td> 
                      <td>{{ $hr_basic_info_details->absent1}}</td> 
                      <td>{{ $absent_per1}}%</td> 
                      <td>{{ $hr_basic_info_details->lefty1}}</td> 
                      <td>{{ $left_per}}%</td>         

                           
        </tr>
          @endforeach

          @php
           $DD=round($total_absent_per_op/$sl,2);
           $DD1=round($total_absent_per_all/$sl,2);
            $DD2=round($total_left_all_per/$sl,2);
          @endphp
               <tr>
                  <td colspan="4" style="text-align:right;"> <b>Total : </b>
                  </td>
                  <td style="text-align:center;"> <b>{{$total_leave_op}}</b>
                  </td>
                  <td style="text-align:center;"> <b>{{$total_absent_op}}</b>
                  </td>
                  {{-- <td style="text-align:center;"> <b>{{$total_absent_per_op}}%</b> --}}
                  <td style="text-align:center;"> <b>{{$DD}}%</b>
                  </td>
                  <td style="text-align:center;"> <b>{{$total_new_rec_op}}</b>
                  </td>
                    <td style="text-align:center;"> <b>{{$total_left_op}}</b>
                  </td>
                    </td>
                    <td style="text-align:center;"> <b>{{$total_new_rec_all}}</b>
                  </td>
                  <td style="text-align:right;"> <b> </b>
                  </td>
                  <td style="text-align:right;"> <b> </b>
                  </td>
                     <td  style="text-align:center;"> <b>{{$total_leave_all}} </b>
                  </td>
                     <td  style="text-align:center;"> <b>{{$total_absent_all}} </b>
                  </td>
                     <td  style="text-align:center;"> <b>{{$DD1}}% </b>
                  </td>
                  </td>
                     <td  style="text-align:center;"> <b>{{$total_left_all}} </b>
                  </td>
                  </td>
                     <td  style="text-align:center;"> <b>{{$DD2}}% </b>
                  </td>
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
 












