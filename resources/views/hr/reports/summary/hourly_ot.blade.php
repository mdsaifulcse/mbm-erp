<style>
  @page {size:landscape}
</style>

@php
$urldata = http_build_query($input)."&export=E"."\n";
$data1=collect($data)->count();
@endphp


<a href='{{ url("hr/reports/hourly_ot?$urldata")}}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 23px; left: 66px;"><i class="fa fa-file-excel-o"></i></a>



<div class="container">
<div class="container p-3" style="overflow-x:auto; ">

  <h4>Hourly OT Status</h4>
  @if($unit_name != null)
  <p>Unit : {{ $unit_name }}</p> 
  @else
  <p>Unit : All</p> 
  @endif   
{{--   @if($location_name != null)
  <p>Location : {{ $location_name }}</p>  
  @else
  <p>Location : All</p> 
  @endif --}}
  @if($area_by_id != null)  
  <p>Area : {{ $area_by_id }}</p>   
  @endif
  @if($department_by_id != null) 
  <p>Department : {{ $department_by_id }}</p> 
  @endif
  @if($section_by_id != null)   
  <p>Section : {{ $section_by_id }}</p>  
  @endif
  @if($subsection_by_id != null)  
  <p>Sunsection : {{ $subsection_by_id }}</p>  
  @endif  
  @if($hr_floor != null)   
  <p>Floor : {{ $hr_floor }}</p>  
  @endif
  @if($hr_line != null)  
  <p>Line : {{ $hr_line }}</p>  
  @endif  
  <p>Date : {{ date('d-m-Y', strtotime($input['from_date'])) }}</p>

@if($data1==0)
<h3 style="text-align:center;">No record found for this date..</h3> 
@else

  <table class="table table-bordered" id="myTable">
    <thead >
      <tr  style="text-align: center;font: 3.6em Calibri;background-color:#E4EAEB; ">
        <th >SL#</th>
        <th >Section</th>
        <th style="width: 90px;">Employee</th>
        <th>Present Employee</th>
        <th>OTH 0 hr</th>
        <th>OTH 1 hr (0>n<=1)</th>
        <th>OTH 2 hr (1>n<=2)</th>
        <th>OTH 3 hr (2>n<=3)</th>
        <th>OTH 4 hr (3>n<=4)</th>
        <th>OTH 5 hr (4>n<=5)</th>
        <th>OTH 6 hr (5>n<=6)</th>
        <th>OTH 7 hr (6>n<=7)</th>
        <th>OTH 8 hr (7>n<=8)</th>
        <th>OTH 9 hr (8>n<=9)</th>
        <th>OTH 10 hr (9>n<=10)</th>
        <th>OTH 11 hr (11>)</th>
        <th>Total Perfomed</th>
        <th>Total OT</th>
        <th>Average OT</th>
       
      </tr>
    </thead>
    <tbody style="font-size:20px;">
      @php
      $s=1;
      $active_employee=0;
      $Present_Employee=0;
      $ot_0_hour=0;
      $ot_1_hour=0;
      $ot_2_hour=0;
      $ot_3_hour=0;
      $ot_4_hour=0;
      $ot_5_hour=0;
      $ot_6_hour=0;
      $ot_7_hour=0;
      $ot_8_hour=0;
      $ot_9_hour=0;
      $ot_10_hour=0;
      $ot_11_hour=0;
      $Perfomed=0;
      $total_ot_hour=0;
      @endphp

    @foreach ($data as $data1)
     @php
      $active_employee+=$data1->active_employee;
      $Present_Employee+=$data1->Present_Employee;
      $ot_0_hour+=$data1->ot_0_hour;
      $ot_1_hour+=$data1->ot_1_hour;
      $ot_2_hour+=$data1->ot_2_hour;
      $ot_3_hour+=$data1->ot_3_hour;
      $ot_4_hour+=$data1->ot_4_hour;
      $ot_5_hour+=$data1->ot_5_hour;
      $ot_6_hour+=$data1->ot_6_hour;
      $ot_7_hour+=$data1->ot_7_hour;
      $ot_8_hour+=$data1->ot_8_hour;
      $ot_9_hour+=$data1->ot_9_hour;
      $ot_10_hour+=$data1->ot_10_hour;
      $ot_11_hour+=$data1->ot_11_hour;
      $Perfomed+=$data1->Present_Employee - $data1->ot_0_hour;
      $total_ot_hour+=$data1->total_ot_hour;
      @endphp
      <tr style="font-family:Calibri;font-size:11px;">
        <th style="text-align: center;">{{$s++}}</th>
        <th>{{$data1->hr_section_name}}</th>
        <th style="text-align: center;">{{$data1->active_employee}}</th>
        <th style="text-align: center;">{{$data1->Present_Employee}}</th>
        <th style="text-align: center;">{{$data1->ot_0_hour}}</th>
        <th style="text-align: center;">{{$data1->ot_1_hour}}</th>
        <th style="text-align: center;">{{$data1->ot_2_hour}}</th>
        <th style="text-align: center;">{{$data1->ot_3_hour}}</th>
        <th style="text-align: center;">{{$data1->ot_4_hour}}</th>
        <th style="text-align: center;">{{$data1->ot_5_hour}}</th>
        <th style="text-align: center;">{{$data1->ot_6_hour}}</th>
        <th style="text-align: center;">{{$data1->ot_7_hour}}</th>
        <th style="text-align: center;">{{$data1->ot_8_hour}}</th>
        <th style="text-align: center;">{{$data1->ot_9_hour}}</th>
        <th style="text-align: center;">{{$data1->ot_10_hour}}</th>
        <th style="text-align: center;">{{$data1->ot_11_hour}}</th>
        <th style="text-align: center;">{{$data1->Present_Employee - $data1->ot_0_hour}}</th>
        <th style="text-align: center;">{{$data1->total_ot_hour}}</th>
        <th style="text-align: center;">{{$data1->avg_ot_hour}}</th>
      </tr>
    @endforeach

    </tbody>
    <tr style="font-family:Calibri;font-size:11px;background-color:#E4EAEB;">
        <th colspan="2" style="text-align: right;">Total :</th>
        <th style="text-align: center;">{{$active_employee}}</th>
        <th style="text-align: center;">{{$Present_Employee}}</th>
        <th style="text-align: center;">{{$ot_0_hour}}</th>
        <th style="text-align: center;">{{$ot_1_hour}}</th>
        <th style="text-align: center;">{{$ot_2_hour}}</th>
        <th style="text-align: center;">{{$ot_3_hour}}</th>
        <th style="text-align: center;">{{$ot_4_hour}}</th>
        <th style="text-align: center;">{{$ot_5_hour}}</th>
        <th style="text-align: center;">{{$ot_6_hour}}</th>
        <th style="text-align: center;">{{$ot_7_hour}}</th>
        <th style="text-align: center;">{{$ot_8_hour}}</th>
        <th style="text-align: center;">{{$ot_9_hour}}</th>
        <th style="text-align: center;">{{$ot_10_hour}}</th>
        <th style="text-align: center;">{{$ot_11_hour}}</th>
        <th style="text-align: center;">{{$Perfomed}}</th>
        <th style="text-align: center;">{{$total_ot_hour}}</th>
        <th style="text-align: center;"></th>
    
    </tr>
    <tr style="font-family:Calibri;font-size:11px;background-color:#E4EAEB;">
        <th colspan="3" style="text-align: center;"></th>
        <th style="text-align: center;">{{round(($ot_0_hour/$Present_Employee)*100,2)}}%</th>
        <th style="text-align: center;">{{round(($ot_1_hour/$Present_Employee)*100,2)}}%</th>
        <th style="text-align: center;">{{round(($ot_2_hour/$Present_Employee)*100,2)}}%</th>
        <th style="text-align: center;">{{round(($ot_3_hour/$Present_Employee)*100,2)}}%</th>
        <th style="text-align: center;">{{round(($ot_4_hour/$Present_Employee)*100,2)}}%</th>
        <th style="text-align: center;">{{round(($ot_5_hour/$Present_Employee)*100,2)}}%</th>
        <th style="text-align: center;">{{round(($ot_6_hour/$Present_Employee)*100,2)}}%</th>
        <th style="text-align: center;">{{round(($ot_7_hour/$Present_Employee)*100,2)}}%</th>
        <th style="text-align: center;">{{round(($ot_8_hour/$Present_Employee)*100,2)}}%</th>
        <th style="text-align: center;">{{round(($ot_9_hour/$Present_Employee)*100,2)}}%</th>
        <th style="text-align: center;">{{round(($ot_10_hour/$Present_Employee)*100,2)}}%</th>
        <th style="text-align: center;">{{round(($ot_11_hour/$Present_Employee)*100,2)}}%</th>
        <th colspan="4" style="text-align: center;"></th>
    
    </tr>
  </table>
@endif
</div>
</div>


<br>
<br>

<script>

  $(document).ready(function(){
    $("#myInput").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#myTable tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
  });



 


</script>