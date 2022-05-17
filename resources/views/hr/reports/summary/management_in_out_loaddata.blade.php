

@php
$urldata = http_build_query($input)."&export=E"."\n";
// dd($urldata);
@endphp


<a href='{{ url("hr/reports/management-in-out-getdata?$urldata")}}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 20px; left: 66px;"><i class="fa fa-file-excel-o"></i></a>




<div class="container" style="overflow-x:auto;">
  <h4>Management Office In Out Monthly</h4>
  @if($unit_name != null)
  <p>Unit : {{ $unit_name }}</p> 
  @else
  <p>Unit : All</p> 
  @endif   
  @if($location_name != null)
  <p>Location : {{ $location_name }}</p>  
  @else
  <p>Location : All</p> 
  @endif
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
 {{--  @php
dd($input['from_date']);
  @endphp --}}
  <p>Period : {{ date('d-m-Y', strtotime($input['from_date'])) }} to {{ date('d-m-Y', strtotime($input['to_date'])) }}</p>

  <table class="table table-bordered" id="myTable">
    <thead >
      <tr  style="text-align: center;">
        <th >SL#</th>
        <th >Associate Id</th>
        <th style="width: 90px;">Name</th>
        <th  style="width: 100px;">Designation</th>
        <th>Department</th>
        <th style="width: 90px;" >Months</th>
        <th>In Before <br> 09:30 am</th>
        <th>In 09:30am <br>to 10:00am</th>
        <th>In 10:00am <br>to 12:00pm</th>
        <th>Out 2:00pm <br>to 04:00pm</th>
        <th>Out 04:00pm <br>to 06:00pm</th>
        <th>Out 06:00pm <br>to 08:00pm</th>
        <th>Out 08:00pm <br>to 11:00pm</th>
      </tr>
    </thead>
    <tbody style="font-size:20px;">
      @php
      $s=1;
      @endphp
      @foreach ($data as $department  )
          @foreach ($department as $employee)
              @foreach ($employee as $absentreason1)
      <tr>
        <th style="text-align: center;">{{$s++}}</th>
        <th style="text-align:center;cursor: pointer;color:#3CC8E7;" data-id="{{ $absentreason1->associate_id}}"   id="viewletter" data-toggle="modal3" data-target="#right_modal_lg_drawer3">{{$absentreason1->associate_id}}</th>
        <th >{{$absentreason1->as_name}}</th>
        <th>{{$absentreason1->hr_designation_name}}</th>
        <th>{{$absentreason1->hr_department_name}}</th>
        <th style="text-align: center;" >{{$absentreason1->months}}</th>
        <th style="text-align: center;">{{$absentreason1->in_before_eq_0930_am}}</th>
        <th style="text-align: center;">{{$absentreason1->in_after_0930am_to_10am}}</th>
        <th style="text-align: center;">{{$absentreason1->in_after_10am_to_12_pm}}</th>
        <th style="text-align: center;border-left: 2px solid pink;">{{$absentreason1->out_between_2pm_to_4_pm}}</th>
        <th style="text-align: center;">{{$absentreason1->out_between_4pm_to_6_pm}}</th>
        <th style="text-align: center;">{{$absentreason1->out_between_6pm_to_8_pm}}</th>
        <th style="text-align: center;">{{$absentreason1->out_after_08pm_to_11pm}}</th>
      </tr>
            @endforeach
        @endforeach
    @endforeach
    </tbody>
  </table>
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