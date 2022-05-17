<div class="container">
  <h3>MBM GROUP</h3>
  <h3>Management Attendance</h3>
  <p> <b>Date : {{ $input}} <b></p>            
  <table class="table table-bordered" style="font-family:Calibri;">
    <thead style="text-align: center;">
       @foreach($managementdate as $key=>$q)
      <tr >
        <th colspan="4"  > {{ $key}}</th>
        <th colspan="5" style="text-align: center !important;">In Date [{{$input}}] </th>
        <th colspan="5">Out Date [{{ \Carbon\Carbon::parse($input)->subDay()->toDateString()}}]</th>
   
      </tr>

      <tr >
        {{-- <th style="font-size:10px !important;" >Area</th> --}}
        <th style="font-size:10px !important;"  >Emp Id</th>
        <th style="font-size:10px !important;"  >Name</th>
        <th style="font-size:10px !important;"  >Dsgn</th>
        <th style="background:Tomato;font-size:10px !important;" >MBM</th>
        <th style="background:Chocolate;font-size:10px !important;">HO</th>
        <th style="background:SlateBlue;font-size:10px !important;">AQL</th>
        <th style="background:LightSeaGreen;font-size:10px !important;">CEG</th>
        <th style="background:#F4D03F;font-size:10px !important;" >CEW</th>
        {{-- <th></th> --}}

        
        <th style="background:Tomato;font-size:10px !important;" >MBM</th>
        <th style="background:Chocolate;font-size:10px !important;">HO</th>
        <th style="background:SlateBlue;font-size:10px !important;">AQL</th>
        <th style="background:LightSeaGreen;font-size:10px !important;">CEG</th>
        <th style="background:#F4D03F;font-size:10px !important;" >CEW</th>
   
      </tr>
    </thead>
    <tbody style="text-align: center;">
   
      {{-- <tr>{{ $key}} <td> --}}
    @php
        $s1=collect($q)->count('associate_id');

        @endphp
    @foreach($q as $managementdate1)
      <tr>

         {{-- MBM --}}
        @if(in_array($managementdate1->hr_location_id, [6,8,10,14] ))
        <td style="background:Tomato !important;"> <b>{{ $managementdate1->associate_id}}</b></td>
        <td style="text-align: left;background:Tomato !important;font-size:10px !important;font-family:Calibri;"> {{ $managementdate1->as_name}}</td>
        <td style="background:Tomato !important;font-size:10px !important;">{{ $managementdate1->hr_designation_name}}</td>
       
{{-- ho --}}
         @elseif ($managementdate1->hr_location_id==12)
        <td style="background:Chocolate !important;"> <b>{{ $managementdate1->associate_id}}</b></td>
        <td style="text-align: left;background:Chocolate !important;font-size:10px !important;"> {{ $managementdate1->as_name}}</td>
        <td style="background:Chocolate !important;font-size:10px !important;">{{ $managementdate1->hr_designation_name}}</td>
        {{-- @endif --}}

{{-- aql--}}
         @elseif  ($managementdate1->hr_location_id==9)
        <td style="background:SlateBlue;font-size:10px !important;"> <b>{{ $managementdate1->associate_id}}</b></td>
        <td style="text-align: left;background:SlateBlue;font-size:10px !important;"> {{ $managementdate1->as_name}}</td>
        <td style="background:SlateBlue;font-size:10px !important;">{{ $managementdate1->hr_designation_name}}</td>
        {{-- @endif --}}

{{-- ceg--}}
        @elseif ($managementdate1->hr_location_id==7 )
        <td style="background:LightSeaGreen;font-size:10px !important;"> <b>{{ $managementdate1->associate_id}}</b></td>
        <td style="text-align: left;background:LightSeaGreen;font-size:10px !important;"> {{ $managementdate1->as_name}}</td>
        <td style="background:LightSeaGreen;font-size:10px !important;">{{ $managementdate1->hr_designation_name}}</td>
        {{-- @endif --}}

        @elseif ($managementdate1->hr_location_id==11)
        <td style="background:#F4D03F;font-size:10px !important;" > <b>{{ $managementdate1->associate_id}}</b></td>
        <td style="text-align: left;background:#F4D03F;font-size:10px !important;"> {{ $managementdate1->as_name}}</td>
        <td style="background:#F4D03F;font-size:10px !important;" >{{ $managementdate1->hr_designation_name}}</td>

         @else 
        <td > <b>{{ $managementdate1->associate_id}}</b></td>
        <td style="text-align: left;font-size:10px !important;"> {{ $managementdate1->as_name}}</td>
        <td >{{ $managementdate1->hr_designation_name}}</td>

        @endif
   

@if ($managementdate1->mbm_in==0  && $managementdate1->aql_in==0 && $managementdate1->ceg_in==0 && $managementdate1->cew_in==0)
     @if ($managementdate1->in_leave==0)
         
          @if ($managementdate1->in_absent==0)
          <td colspan="5" style="color:green;" ><b>Holiday</b></td>
          @else
          <td colspan="5" style="color:#E74C3C;" ><b>Absent</b></td>
          @endif

       @else
       <td colspan="5" style="color:green;" ><b>On Leave</b></td>
     @endif
@else
        @php
        $inpunchunit=$managementdate1->in_unit;
        // dd($inpunchunit);
       
        @endphp

        @if ($inpunchunit==1 or $inpunchunit==4 or $inpunchunit==5 )
        <td ><b>{{$managementdate1->mbm_in}}</b></td>
        @else
        <td ><b>0</b></td>
        @endif

        @if ($inpunchunit==1001)
              @if (in_array($managementdate1->as_unit_id, [1,4,5]))
              <td ><b>{{$managementdate1->mbm_in}}</b></td>
              @endif
              @if ($managementdate1->as_unit_id==2)
              <td ><b>{{$managementdate1->ceg_in}}</b></td>
              @endif
              @if ($managementdate1->as_unit_id==8)
              <td ><b>{{$managementdate1->cew_in}}</b></td>
              @endif
              @if ($managementdate1->as_unit_id==3)
              <td ><b>{{$managementdate1->aql_in}}</b></td>
              @endif
        @else
         <td ><b>0</b></td>
        @endif
         
         @if ($inpunchunit==3)
         <td ><b>{{$managementdate1->aql_in}}</b></td>
         @else
         <td ><b>0</b></td>
         @endif


         @if ($inpunchunit==2)
         <td ><b>{{$managementdate1->ceg_in}}</b></td>
         @else
         <td ><b>0</b></td>
         @endif
         

         @if ($inpunchunit==8)
         <td ><b>{{$managementdate1->cew_in}}</b></td>
         @else
         <td ><b>0</b></td>
         @endif
        
        
        
       
@endif
  

@if ($managementdate1->mbm_out==0  && $managementdate1->aql_out==0 && $managementdate1->ceg_out==0 && $managementdate1->cew_out==0)
       @if ($managementdate1->out_leave==0)
         
            @if ($managementdate1->out_absent==0)
               <td colspan="5" style="color:green;border-left: 2px solid tomato;" ><b>Holiday</b></td>
              @else
              <td colspan="5" style="color:#E74C3C;border-left: 2px solid tomato;" ><b>Absent</b></td>
            @endif

      
       @else
              <td colspan="5" style="color:green;border-left: 2px solid tomato;" ><b>On Leave</b></td>
       @endif
    @else
            

            @php
            $outpunchunit=$managementdate1->out_unit;
         
            @endphp

             @if ($outpunchunit==1 or $outpunchunit==4 or $outpunchunit==5 )
              <td style="border-left: 2px solid tomato;"><b>{{$managementdate1->mbm_out}}</b></td>
              @else
             <td style="border-left: 2px solid tomato;"><b>0</b></td>
              @endif

              @if ($outpunchunit==1001)
               @if (in_array($managementdate1->as_unit_id, [1,4,5]))
              <td ><b>{{$managementdate1->mbm_out}}</b></td>
              @endif
              @if ($managementdate1->as_unit_id==2)
              <td ><b>{{$managementdate1->ceg_out}}</b></td>
              @endif
              @if ($managementdate1->as_unit_id==8)
              <td ><b>{{$managementdate1->cew_out}}</b></td>
              @endif
              @if ($managementdate1->as_unit_id==3)
              <td ><b>{{$managementdate1->aql_out}}</b></td>
              @endif
              @else
               <td ><b>0</b></td>
              @endif
               
               @if ($outpunchunit==3)
               <td ><b>{{$managementdate1->aql_out}}</b></td>
               @else
               <td ><b>0</b></td>
               @endif


               @if ($outpunchunit==2)
               <td ><b>{{$managementdate1->ceg_out}}</b></td>
               @else
               <td ><b>0</b></td>
               @endif
               

               @if ($outpunchunit==8)
               <td ><b>{{$managementdate1->cew_out}}</b></td>
               @else
               <td ><b>0</b></td>
               @endif


@endif
      </tr> 
    @endforeach
    <tr>
      <td ><b> Emp Count : <b></td>
      <td ><b>  {{$s1}}<b></td>
    </tr>
    @endforeach

    </tbody>
  </table>

  <br><br>
</div>