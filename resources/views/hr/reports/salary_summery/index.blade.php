@extends('hr.layout')
@section('title', 'Salary')

@section('main-content')
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
                <li class="active"> Salary Summary</li>
            </ul>
        </div>
        <div class="page-content"> 
            
            <div class="row">
                <div class="col">
                  <form role="form" method="get" action="{{ url("hr/reports/salary-summary-report") }}" id="formReport">
                    @csrf
                    <div class="iq-card" id="result-section">
                      <div class="iq-card-header d-flex mb-0">
                         <div class="iq-header-title w-100">
                            <div class="row">
                              <div style="width: 10%; float: left; margin-left: 15px; margin-top: 2px;">
                                <div id="result-section-btn">
                                  <button type="button" class="btn btn-sm btn-primary hidden-print" onclick="printDiv('report_section')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report" style="margin-top: 3px;"><i class="las la-print"></i> </button>
                                </div>
                              </div>
                              <div class="text-center" style="width: 47%; float: left">
                                {{-- <h4 class="card-title capitalize inline">
                                  @foreach(array_reverse($months) as $k => $i)
                                    <a class="nav-year @if($k== $yearMonth) bg-primary text-white @endif" data-toggle="tooltip" data-placement="top" data-year-month="{{ date('Y-m', strtotime($k)) }}" title="" data-original-title="Salary of {{$i}}" >
                                        {{$i}}
                                    </a>
                                  @endforeach
                                </h4> --}}
                              </div>

                              <input type="hidden" id="reportFormat" name="report_format" value="1">
                              <input type="hidden" id="salaryReportType" name="salary_report_type" value="summery">
                              <div style="width: 40%; float: left">
                                <div class="row">
                                  <div class="col-3 p-0">
                                      <div class="form-group has-float-label has-required ">
                                        <input type="hidden" class="report_date form-control month-report" id="yearMonth" name="year_month" placeholder=" Month-Year" required="required" value="{{ $yearMonth }}" max="{{ date('Y-m') }}" autocomplete="off">
                                        {{-- <label for="yearMonth">Month</label> --}}
                                      </div>
                                  </div>
                                  <div class="col-4 pr-0">
                                    <div class="format">
                                      
                                      <div class="form-group has-float-label select-search-group mb-0">
                                          <?php
                                            
                                            $type = ['as_unit_id'=>'Unit','as_designation_id'=>'Designation','as_location'=>'Location','as_department_id'=>'Department','as_section_id'=>'Section','as_subsection_id'=>'Sub Section'];
                                            if(auth()->user()->hasRole('Super Admin')){
                                              $selectGroup = 'as_designation_id';
                                            }else{
                                              $selectGroup = 'as_designation_id';
                                            }
                                          ?>
                                          
                                          {{ Form::select('report_group', $type, $selectGroup, ['class'=>'form-control capitalize', 'id'=>'reportGroupHead']) }}
                                          <label for="reportGroupHead">Report Format</label>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-5 pl-0">
                                    <div class="text-right">
                                      <a class="btn view no-padding clear-filter" data-toggle="tooltip" data-placement="top" title="" data-original-title="Clear Filter">
                                        <i class="las la-redo-alt" style="color: #f64b4b; border-color:#be7979"></i>
                                      </a>
                                      <a class="btn view no-padding filter" data-toggle="tooltip" data-placement="top" title="" data-original-title="Advanced Filter">
                                        <i class="fa fa-filter"></i>
                                      </a>
                                      <a class="btn view grid_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Summary Report View" id="1">
                                        <i class="las la-th-large"></i>
                                      </a>
                                      <a class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Details Report View" id="0">
                                        <i class="las la-list-ul"></i>
                                      </a>
                                      
                                    </div>
                                  </div>
                                </div>
                                
                              </div>
                            </div>
                         </div>
                      </div>
                      <div class="iq-card-body no-padding">
                        <div class="result-data" id="result-data">
                          
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
{{-- modal employee salary --}}
  
<div class="modal right fade" id="right_modal_lg-group" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg-group">
  <div class="modal-dialog modal-lg right-modal-width" role="document" > 
    <div class="modal-content">
      <div class="modal-header">
        <a class="view prev_btn" data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Back to Report">
      <i class="las la-chevron-left"></i>
    </a>
        <h5 class="modal-title right-modal-title text-center" id="modal-title-right-group"> &nbsp; </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="modal-content-result content-result" id="content-result-group">
          
        </div>
      </div>
      
    </div>
  </div>
</div>
@section('right-nav')
  <hr class="mt-2">
  <div class="form-group mb-2">
    <label for="" class="m-0 fwb">Salary</label>
    <hr class="mt-2">
    <div class="row">
      <div class="col-5 pr-0">
        <div class="form-group has-float-label has-required">
          <input type="number" class="report_date min_sal form-control" id="min_sal" name="min_sal" placeholder="Min Salary" required="required" value="0" min="0" max="{{ $salaryMax }}" autocomplete="off" />
          <label for="min_sal">Min</label>
        </div>
      </div>
      <div class="col-1 p-0" style="line-height: 35px;">
        <div class="c1DHiF text-center">-</div>
      </div>
      <div class="col-6 pl-0">
        <div class="form-group has-float-label has-required">
          <input type="number" class="report_date max_sal form-control" id="max_sal" name="max_sal" placeholder="Max Salary" required="required" value="{{ $salaryMax }}" min="0" max="{{ $salaryMax }}" autocomplete="off" />
          <label for="max_sal">Max</label>
        </div>
      </div>
    </div>
  </div>
  <hr class="mt-2">
  <div class="form-group mb-2">
    <label for="" class="m-0 fwb">Service Length</label>
    <hr class="mt-2">
    <div class="row">
      <div class="col-5 pr-0">
        <div class="form-group has-float-label">
          <input type="number" class="report_date min_len form-control" id="min_len" name="min_len" placeholder="Min Len" value="0" min="0" autocomplete="off" />
          <label for="min_len">Min</label>
        </div>
      </div>
      <div class="col-1 p-0" style="line-height: 35px;">
        <div class="c1DHiF text-center">-</div>
      </div>
      <div class="col-6 pl-0">
        <div class="form-group has-float-label ">
          <input type="number" class="report_date max_len form-control" id="max_len" name="max_len" placeholder="Max Len" value="" min="0" autocomplete="off" />
          <label for="max_len">Max</label>
        </div>
      </div>
    </div>
  </div>
  <hr class="mt-2">
  <div class="form-group mb-2">
    <label for="" class="m-0 fwb">Designation Grade</label>
    <hr class="mt-2">
    <div class="row">
      <div class="col-6 pr-0">
        <div class="form-group has-float-label select-search-group">
          <select name="from_grade" class="form-control capitalize select-search" id="from_grade" >
              <option selected="" value="">Choose</option>
              @foreach(designation_grade_by_id() as $key => $grade)
              <option value="{{ $grade->grade_sequence }}">{{ $grade->grade_name }}</option>
              @endforeach
          </select>
          <label for="from_grade">Grade From</label>
        </div>
      </div>
      <div class="col-1 p-0" style="line-height: 35px;">
        <div class="c1DHiF text-center">-</div>
      </div>
      <div class="col-5 pl-0">
        <div class="form-group has-float-label select-search-group">
          <select name="to_grade" class="form-control capitalize select-search" id="to_grade" >
              <option selected="" value="">Choose</option>
              @foreach(designation_grade_by_id() as $key => $grade)
              <option value="{{ $grade->grade_sequence }}">{{ $grade->grade_name }}</option>
              @endforeach
          </select>
          <label for="to_grade">Grade To</label>
        </div>
      </div>
    </div>
  </div>
  <hr class="mt-2">
  <div class="form-group mb-2">
    <label for="" class="m-0 fwb">DOJ Length</label>
    <hr class="mt-2">
    <div class="row">
      <div class="col-12">
        <div class="form-group has-float-label">
          <input type="date" class="report_date from_doj form-control" id="from_doj" name="from_doj" placeholder="DOJ From" value="" autocomplete="off" />
          <label for="from_doj">DOJ From</label>
        </div>
      </div>
      
      <div class="col-12">
        <div class="form-group has-float-label">
          <input type="date" class="report_date to_doj form-control" id="to_doj" name="to_doj" placeholder="DOJ To" value="" autocomplete="off" />
          <label for="to_doj">DOJ To</label>
        </div>
      </div>
    </div>
  </div>
  <hr class="mt-2">
  <div class="form-group has-float-label select-search-group">
    <?php
      $payType = ['all'=>'All', 'cash'=>'Cash', 'rocket'=>'Rocket', 'bKash'=>'bKash', 'dbbl'=>'Dutch-Bangla Bank Limited.'];
    ?>
    {{ Form::select('pay_status', $payType, 'all', ['placeholder'=>'Select Payment Type', 'class'=>'form-control capitalize select-search', 'id'=>'paymentType']) }}
    <label for="paymentType">Payment Type</label>
  </div>
@endsection
  {{--  --}}
@include('common.right-modal')
@include('common.right-navbar')

@push('js')
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript">
  @if(!Request::get('unit')) 
    advFilter();
  @endif
  $(document).on('click', '.yearly-activity', function() {
      let id = $(this).data('id');
      let associateId = $(this).data('eaid');
      let name = $(this).data('ename');
      let designation = $(this).data('edesign');
      let yearMonth = $(this).data('yearmonth');
      $("#modal-title-right").html(' '+name+' Salary Details');
      $('#right_modal_jobcard').modal('show');
      $("#content-result-jobcard").html(loaderContent);
      $.ajax({
          url: '/hr/operation/unit-wise-salary-sheet',
          type: "GET",
          data: {
              as_id: [associateId],
              year_month: yearMonth,
              sheet:0,
              perpage:1
          },
          type: "GET",
          dataType: "json",
          success: function(response){
              if(response !== 'error'){
                setTimeout(function(){
                  $("#content-result-jobcard").html(response.view);
                }, 1000);
              }else{
                console.log(response.view);
              }
          }
      });
  });
  $(document).on('change', '#reportGroupHead', function(){
    // if($(this).val() === 'all'){
    //   $("#salaryReportType").val('all');
    // }else{
    //   $("#salaryReportType").val('summery');
    // }
  });
</script>
@endpush
@endsection