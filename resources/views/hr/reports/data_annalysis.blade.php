@extends('hr.layout')
@section('title', 'Salary')
@push('css')
  <style>
    .single-employee-search {
      margin-top: 82px !important;
    }
    .view:hover, .view:hover{
      color: #ccc !important;
      
    }
    .grid_view{

    }
    .view i{
      font-size: 25px;
      border: 1px solid #000;
      border-radius: 3px;
      padding: 0px 3px;
    }
    .view.active i{
      background: linear-gradient(to right,#0db5c8 0,#089bab 100%);
      color: #fff;
      border-color: #089bab;
    }
    .iq-card .iq-card-header {
      margin-bottom: 10px;
      padding: 15px 15px;
      padding-bottom: 0px;
    }
    .modal-h3{
      line-height: 15px !important;
    }
    .select2-container .select2-selection--single, .month-report { height: 30px !important;}
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 30px !important;}



    
  </style>
@endpush
@section('main-content')


       
            <div class="main-content">
                <div class="main-content-inner">
                    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                        <ul class="breadcrumb">
                            <li>
                                <i class="ace-icon fa fa-home home-icon"></i>
                                <a href="#">Human Resource</a>
                            </li>
                            <li>
                                <a href="#">Reports</a>
                            </li>
                            <li class="active">Data Annalysis</li>
                        </ul>
                    </div>
                    <div class="page-content"> 
                        
                        <div class="row">
                            <div class="col">
                              
                            </div>
                        </div>
                    </div><!-- /.page-content -->
                </div>
            </div>


            <div class="main-content">
                  <div class="main-content-inner">
                    <div class="row">
                      <div class="col-12">
                       <div class="breadcrumbs ace-save-state" id="breadcrumbs">

                        <ul class="breadcrumb">
                          <li>

                            <h4 class="card-title capitalize inline">
                              <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('print')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
                              <button class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download">
                                <i class="fa fa-file-excel-o"></i>
                              </button>
                            </h4>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <div class="page-content"> 
                    <div class="row">
                      <div class="col-3">
                      </div>
                    </div>
                  </div><!-- /.page-content -->
                </div>
            </div>



                  <div class="main-content">
                    <div class="main-content-inner">
                      <div class="row">
                        <div class="col-12">
                         <div class="breadcrumbs ace-save-state" id="breadcrumbs">

                          <ul class="breadcrumb" style="display:block;">
                           <div class="row">
                             <div class="col-4">
                              <div class="form-group has-float-label has-required select-search-group">
                                <select name="unit" class="form-control capitalize select-search" id="unit"required >
                                  {{-- <option selected="" value="">Choose...</option> --}}
                                  @foreach($unitList as $key => $value)
                                  <option value="{{ $key }}">{{ $value }}</option>
                                  @endforeach
                                </select>
                                <label for="unit">Unit</label>
                              </div>
                            </div>
                            <div class="col-2">
                             <div class="form-group has-float-label select-search-group">
                              <select name="section" class="form-control capitalize select-search" id="section" >
                                 <option selected="" value="">All Section</option>
                                  @foreach($section as $key => $value)
                                 <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                                  
                              </select>
                              <label for="section">Section</label>
                            </div>
                          </div>


                          <div class="col-2">
                             <div class="form-group has-float-label select-search-group">
                              <select name="subsection" class="form-control capitalize select-search" id="subsection" >
                                <option selected="" value="">All Sub Section</option>
                              </select>
                              <label for="Status">Sub section</label>
                            </div>
                          </div>



                          <div class="col-2">
                           <div class="form-group has-float-label has-required">
                            <input type="date" class="report_date datepicker form-control" id="from_date" name="from_date" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" />
                            <label for="from_date">Date From</label>
                          </div>
                        </div>

                        <div class="col-2">
                         <div class="form-group has-float-label has-required">
                          <input type="date" class="report_date datepicker form-control" id="to_date" name="to_date" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" />
                          <label for="to_date">Date To</label>
                        </div>
                      </div>


                      

                   

{{-- 

                           <div class="col-2">
                             <div class="form-group has-float-label select-search-group">
                              <select name="Type" class="form-control capitalize select-search" id="Type" >
                                <option value="1">UNIT WISE</option>
                                <option value="2">MONTH WISE</option>
                                <option value="3">DEPARTMENT WISE</option>
                              </select>
                              <label for="Type">Report Type</label>
                            </div>
                          </div> --}}

 <div class="col-12">
                     <div class="form-group">
                      <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" onClick="filter()"><i class="fa fa-save"></i> Generate</button>
                    </div>
                  </div>


                </div>
                
              </ul>

            </div>
          </div>
        </div>
        <div class="page-content"> 
          <div class="row">
            <div class="col-3">
            </div>
          </div>
        </div><!-- /.page-content -->
      </div>
      </div>



                      


<div id="print">    

                        <div  class="main-content" style="overflow-x:auto;">

                          <div class="main-content-inner">
                            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                              <ul class="breadcrumb">

                               <h4  style="margin:4px 10px; font-weight: bold; text-left: center;">
                               <p id="header" style="width:auto;text-left:center;">
                                 
                               </p> 
                                {{-- herder data set from ajax call --}}
                                
                             </h4>


                             </ul>
                           </div>
                           <div class="page-content"> 

                            <div class="row">
                              <div class="col">

                              </div>
                            </div>
                          </div><!-- /.page-content -->
                        </div>

                      </div>



                    <div id="tabledata" class="main-content" style="overflow-x:auto;">

                       <!-- call view page date_wiseloaddata.balade.php -->
                                        <!-- hr.reports.date_wise_employee -->
                                   
                    </div>



                 <!--  <div id="tabledata" class="main-content" style="overflow-x:auto;">
                         call view page date_wiseloaddata.balade.php
                           
                  </div> -->

</div>

            {{-- 
             <!-- <div class="main-content" style="overflow-x:auto;">
                    <div class="main-content-inner">
                        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                            <ul class="breadcrumb">
                                      
                   <h3 style="margin:4px 10px; font-weight: bold; text-align: center;"> Date Wise Employee Details</h3>
                                      
                                  <table class="table table-bordered SERIAL_NUMBER">
                                    <thead style="text-align:center;" >
                                      <tr>
                                        <th scope="col">SL.</th>
                                        <th>Employee Id </th>
                                        <th>Name</th>
                                        <th  style="width:100px;text-align:center;" >Doj</th>
                                        <th>Gender</th>
                                        <th>Designation</th>
                                        <th>Department</th>
                                        <th>Section</th>
                                        <th>Unit</th>
                                        <th>Floor</th>
                                        <th>Line</th>
                                      </tr>
                                    </thead>
                                    <tbody>

                                     
                                    @foreach($hr_basic_info_view as $hr_basic_info_view)
                                      <tr>
                                          <td style="text-align:center;"> </td>
                                          <td>{{ $hr_basic_info_view->associate_id}}</td>
                                          <td>{{ $hr_basic_info_view->as_name}}</td>
                                          <td>{{ $hr_basic_info_view->as_doj}}</td>
                                          <td>{{ $hr_basic_info_view->as_gender}}</td>
                                          <td>{{ $hr_basic_info_view->hr_designation_name}}</td>
                                          <td>{{ $hr_basic_info_view->hr_department_name}}</td>
                                          <td>{{ $hr_basic_info_view->hr_section_name}}</td>
                                          <td>{{ $hr_basic_info_view->hr_unit_name}}</td>
                                          <td>{{ $hr_basic_info_view->floor_name}}</td>
                                          <td>{{ $hr_basic_info_view->hr_line_name}}</td>
                                          
                                        
                                      </tr>
                                      @endforeach
                                      
                                    </tbody>
                                  </table>
                            </ul>
                        </div>
                        <div class="page-content"> 
                            
                            <div class="row">
                                <div class="col">
                                  
                                </div>
                            </div>
                        </div>
                    </div>
            </div>    -->
            --}}


@push('js')
<script type="text/javascript">

// $(document).ready( function(){
//    callAjax()
// }); 

// $(document).on('change','#unit,#otnonot,#from_date,#to_date', function(){
//     callAjax();
// });

$(document).on('change','#section', function(){
  $("#subsection").empty().html('<option value="">All Sub Section</option>');
$.ajax({
type:'get',
url: '{{url("hr/reports/sub_section_callll")}}',           
data:{
'section':$('#section').val(),
},
success:function(data){
  $('#tabledata').html(data);
  console.log(data)
  $("#subsection").select2({
     data: data
 })
}
});
});



function filter(){
callAjax();
}

function callAjax(id = null) {
$('#tabledata').html(loaderContent);
$.ajax({
type:'get',
url: '{{url("hr/reports/data-annalysisloaddata")}}',           
data:{
'unit':$('#unit').val(),
'section':$('#section').val(),
'subsection':$('#subsection').val(),
'from_date':$('#from_date').val(),
'to_date':$('#to_date').val(),
'Status':$('#Status').val(),
'Type':$('#Type').val(),
},
success:function(data){
$('#tabledata').html(data);
$("#header").html( 'Data Annalysis Report <br>'+$("#unit option:selected").text()
 +'<br> Section : '+ $("#section option:selected").text() +'::::' + $("#subsection option:selected").text() + '<br> Report Run Date Between : '
 +$('#from_date').val()  + ' And '+$('#to_date').val() );
$("#sectionsubsection").html( $("#section option:selected").text() +'::::' + $("#subsection option:selected").text());


}
});
}
  </script>
@endpush
@endsection

