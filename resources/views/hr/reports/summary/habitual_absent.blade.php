

@php
$urldata = http_build_query($input) . "\n";
@endphp

<a href='{{ url("hr/reports/habitual-absent-excel?$urldata")}}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 19px; left: 65px;"><i class="fa fa-file-excel-o"></i></a>

<div class="container">
  <h4>Habitual Absent List</h4>
  @if($unit_name != null)
  <p>Unit : {{ $unit_name }}</p> 
   @endif   
   @if($location_name != null)
  <p>Location : {{ $location_name }}</p>  
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
<input class="form-control pull-right" id="myInput" type="text" placeholder="Search.." style="width:200px;">
  <br><br>

  <table class="table table-bordered" id="myTable">
    <thead >
      <tr>
        <th >SL#</th>
        <th >Associate Id</th>
        <th style="width: 90px; display:block; border:none;">Absent Date</th>
        <th>Absent Reason</th>
        {{-- <th>Doj</th> --}}
        <th>Name</th>
        <th>Gender</th>
        {{-- <th>OT Status</th> --}}
        {{-- <th>Floor Name</th>
        <th>Line Name</th> --}}
        <th>Department</th>
        <th>Designation</th>
        <th>Section</th>
        <th>Status</th>
        {{-- <th>Unit</th> --}}
      </tr>
    </thead>
    <tbody>
        @php
        $s=1;
        @endphp
    @foreach ($absentreason as $absentreason1)
      <tr>
        <th>{{$s++}}</th>
        <th>{{$absentreason1->associate_id}}</th>
        <th >{{$absentreason1->absent_date}}</th>
        <th>{{$absentreason1->absent_reason}}</th>
        {{-- <th>{{$absentreason1->as_doj}}</th> --}}
        <th>{{$absentreason1->as_name}}</th>
        <th>{{$absentreason1->as_gender}}</th>
        {{-- <th>{{$absentreason1->as_ot}}</th> --}}
        {{-- <th>{{$absentreason1->floor_name}}</th>
        <th>{{$absentreason1->hr_line_name}}</th> --}}
        <th>{{$absentreason1->hr_department_name}}</th>
        <th>{{$absentreason1->hr_designation_name}}</th>
        <th>{{$absentreason1->hr_section_name}}</th>
        <th>{{$absentreason1->as_status_NAME}}</th>
        {{-- <th>{{$absentreason1->hr_unit_short_name}}</th> --}}
      </tr>
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