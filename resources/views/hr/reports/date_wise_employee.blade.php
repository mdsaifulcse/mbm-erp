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
                            <li class="active">Date Wise NewJoin And Left Employees</li>
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
                                  <option selected="" value="">All Units</option>
                                  @foreach($unitList as $key => $value)
                                  <option value="{{ $key }}">{{ $value }}</option>
                                  @endforeach
                                </select>
                                <label for="unit">Unit</label>
                              </div>
                            </div>
                            <div class="col-2">
                             <div class="form-group has-float-label select-search-group">
                              <select name="otnonot" class="form-control capitalize select-search" id="otnonot" >
                                <option selected="" value="">OT/NON OT ALL</option>
                                <option value="0">Non-OT</option>
                                <option value="1">OT</option>
                              </select>
                              <label for="otnonot">OT/Non-OT</label>
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


                      

                   

                        {{-- <div class="col-2">
                             <div class="form-group has-float-label select-search-group">
                              <select name="Status" class="form-control capitalize select-search" id="Status" >
                                <option selected="" value="">Choose...</option>
                                <option value="1">Active</option>
                                <option value="2">RESIGN</option>
                                <option value="3">TERMINATE</option>
                                <option value="4">SUSPEND</option>
                                <option value="5">LEFT</option>
                                <option value="6">MATERNITY</option>
                              </select>
                              <label for="Status">Status</label>
                            </div>
                          </div> --}}

                           <div class="col-2">
                             <div class="form-group has-float-label select-search-group">
                              <select name="Type" class="form-control capitalize select-search" id="Type" >
                                <option value="1">UNIT WISE</option>
                                <option value="2">MONTH WISE</option>
                                <option value="3">DEPARTMENT WISE</option>

                              </select>
                              <label for="Type">Report Type</label>
                            </div>
                          </div>

                          <div class="col-10">
                           <div class="form-group">
                            
                          </div>
                        </div>


                        <div class="col-2 center" style="posi" >
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

          


@push('js')
  <script type="text/javascript">
    
   // $(document).ready( function(){
   //    callAjax()
   // }); 

  // $(document).on('change','#unit,#otnonot,#from_date,#to_date', function(){
  //     callAjax();
  // });

  function filter(){
    callAjax();
  }

  function callAjax(id = null) {
    $('#tabledata').html(loaderContent);
    $.ajax({
      type:'get',
      url: '{{url("hr/reports/date-wiseloaddata")}}',           
      data:{
        'unit':$('#unit').val(),
        'otnonot':$('#otnonot').val(),
        'from_date':$('#from_date').val(),
        'to_date':$('#to_date').val(),
        'Status':$('#Status').val(),
        'Type':$('#Type').val(),
      },
      success:function(data){
        $('#tabledata').html(data);
              // $("#header").html('Date Wise  NewJoin And Left Employees<br> Report Run Date Between : '+$('#from_date').val()  + ' And '+$('#to_date').val() );
              // console.log('ddddd');

              $("#header").html( 'Date Wise  NewJoin And Left Employees <br> Report Type : '+$("#Type option:selected").text()+'<br>'+$("#unit option:selected").text()
                + $("#subsection option:selected").text() +'<br>' +$("#otnonot option:selected").text() + '<br> Report Run Date Between : '
                +$('#from_date').val()  + ' And '+$('#to_date').val() );
              $("#sectionsubsection").html( $("#section option:selected").text() +'::::' + $("#subsection option:selected").text());
            }
          });
  }


  </script>
@endpush
@endsection

