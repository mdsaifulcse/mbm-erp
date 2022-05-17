

@php
$urldata = http_build_query($input)."&export=X"."\n";
// dd($urldata);
@endphp


<a href='{{ url("hr/reports/linechangedaily?$urldata")}}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 23px; left: 66px;"><i class="fa fa-file-excel-o"></i></a>

  

<div class="container">
  <h4>Line Change List</h4>
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
 {{--  @php
dd($input['from_date']);
  @endphp --}}
  <p>Period : {{ date('d-m-Y', strtotime($input['from_date'])) }} to {{ date('d-m-Y', strtotime($input['to_date'])) }}</p>
{{--   <input class="form-control pull-right" id="myInput" type="text" placeholder="Search.." style="width:200px;"> --}}
  <br><br>

  <table class="table table-bordered" id="myTable">
    <thead >
      <tr>
        <th style="text-align:center;" >SL#</th>
        <th style="text-align:center;">Associate Id</th>
        <th style="width: 90px;">Name</th>
        <th style="text-align:center;">Designation</th>
        <th style="text-align:center;">Department</th>
        <th style="text-align:center;">Section</th>
        <th style="text-align:center;">Default Line</th>
        <th style="width: 300px; text-align:center" >Current Line</th>
      </tr>
    </thead>
    <tbody style="font-size:20px;">
      @php
      $s=1;
      @endphp
      @foreach ($data as $absentreason1)
      <tr>
        <th style="text-align:center;font-weight:normal;" >{{$s++}}</th>
        <th style="text-align:center;font-weight:normal;"  >{{$absentreason1->associate_id}}</th>
        <th style="text-align:center;font-weight:normal;" >{{$absentreason1->as_name}}</th>
        <th style="text-align:center;font-weight:normal;" >{{$absentreason1->hr_designation_name}}</th>
        <th style="text-align:center;font-weight:normal;" >{{$absentreason1->hr_department_name}}</th>
        <th style="text-align:center;font-weight:normal;" >{{$absentreason1->hr_section_name}}</th>
        <th style="text-align:center;font-weight:normal;" >{{$absentreason1->Default_line}}</th>
        <th style="text-align:center;font-weight:normal;" >{{$absentreason1->Current_line}}</th>
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