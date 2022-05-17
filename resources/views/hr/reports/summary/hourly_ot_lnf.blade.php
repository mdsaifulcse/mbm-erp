<style>
  @page {size:landscape}
</style>

@php
$urldata = http_build_query($input)."&export=E"."\n";
$data1=collect($data)->count();
@endphp


<a href='{{ url("hr/reports/hourly_ot_lnf?$urldata")}}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 23px; left: 66px;"><i class="fa fa-file-excel-o"></i></a>



<div class="container">
<div class="container p-3" style="overflow-x:auto; ">

  <h4>WORKING HOUR STATEMENT</h4>
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
  <p>Period : {{ date('d-m-Y', strtotime($input['from_date'])) }} To {{ date('d-m-Y', strtotime($input['to_date'])) }}</p>

@if($data1==0)
<h3 style="text-align:center;">No record found for this date..</h3> 
@else

  <table class="table table-bordered" id="myTable">
    <thead >
      <tr  style="text-align: center;font: 3.6em Calibri;background-color:#E4EAEB; ">
        <th >SL#</th>
        <th style="width: 150px;" >Section</th>
        <th style="width: 90px;">Total Employee</th>
        <th>No Of Present Employee</th>
        <th>Work Hour Upto 48</th>
        <th>Work Hour 49 60</th>
        <th>Work Hour 61 72</th>
        <th>Work Hour 73 84</th>
        <th>Work Above 84</th>
        <th>Average Hour</th>
        <th>Height Ot Hour</th>
        <th>Total Perfomed</th>
      </tr>
    </thead>
    <tbody style="font-size:20px;">
      @php
      $s=1;
      $active_employee=0;
      $Present_Employee=0;
      $wrk_hrs_upto_48=0;
      $wrk_hrs_upto_49_60=0;
      $wrk_hrs_upto_61_72=0;
      $wrk_hrs_upto_73_84=0;
      $wrk_hrs_Above_84=0;
      $avg_ot_hour=0;
      $max_ot_hour=0;
      $Perfomed=0;
      @endphp

    {{-- @foreach ($data as $data2) --}}
    @foreach ($data as $data1)
     @php
     
      $active_employee+=$data1->Total_employee;
      $Present_Employee+=$data1->Present_employee;
      $wrk_hrs_upto_48+=$data1->wrk_hrs_upto_48;
      $wrk_hrs_upto_49_60+=$data1->wrk_hrs_upto_49_60;
      $wrk_hrs_upto_61_72+=$data1->wrk_hrs_upto_61_72;
      $wrk_hrs_upto_73_84+=$data1->wrk_hrs_upto_73_84;
      $wrk_hrs_Above_84+=$data1->wrk_hrs_Above_84;
      $avg_ot_hour+=round($data1->avg_ot_hour,2);
      $max_ot_hour+=$data1->max_ot_hour;
      $Perfomed+=$data1->Present_employee - $data1->wrk_hrs_upto_0;
     
      @endphp
      <tr style="font-family:Calibri;font-size:11px;">
        <th style="text-align: center;">{{$s++}}</th>
        <th>{{$data1->hr_section_name}}</th>
        <th style="text-align: center;">{{$data1->Total_employee}}</th>
        <th style="text-align: center;">{{$data1->Present_employee}}</th>
        <th style="text-align: center;">{{$data1->wrk_hrs_upto_48}}</th>
        <th style="text-align: center;">{{$data1->wrk_hrs_upto_49_60}}</th>
        <th style="text-align: center;">{{$data1->wrk_hrs_upto_61_72}}</th>
        <th style="text-align: center;">{{$data1->wrk_hrs_upto_73_84}}</th>
        <th style="text-align: center;">{{$data1->wrk_hrs_Above_84}}</th>
        <th style="text-align: center;">{{round($data1->avg_ot_hour,2)}}</th>
        <th style="text-align: center;">{{$data1->max_ot_hour}}</th>
        <th style="text-align: center;">{{$data1->Present_employee - $data1->wrk_hrs_upto_0}}</th>
      </tr>
    @endforeach
   {{-- @endforeach --}}

    </tbody>
    <tr style="font-family:Calibri;font-size:11px;background-color:#E4EAEB;">
        <th colspan="2" style="text-align: right;">Total :</th>
        <th style="text-align: center;">{{$active_employee}}</th>
        <th style="text-align: center;">{{$Present_Employee}}</th>
        <th style="text-align: center;">{{$wrk_hrs_upto_48}}</th>
        <th style="text-align: center;">{{$wrk_hrs_upto_49_60}}</th>
        <th style="text-align: center;">{{$wrk_hrs_upto_61_72}}</th>
        <th style="text-align: center;">{{$wrk_hrs_upto_73_84}}</th>
        <th style="text-align: center;">{{$wrk_hrs_Above_84}}</th>
        <th style="text-align: center;">{{$avg_ot_hour}}</th>
        <th style="text-align: center;">{{$max_ot_hour}}</th>
        <th style="text-align: center;">{{$Perfomed}}</th>
    
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