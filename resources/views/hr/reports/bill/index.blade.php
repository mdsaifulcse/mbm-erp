@extends('hr.layout')
@section('title', 'Bill')

@section('main-content')
@push('css')

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
                <li class="active"> Bill Info</li>
            </ul>
        </div>
        
        <div class="page-content"> 
            <div class="row">
                <div class="col">
                  <form role="form" method="get" action="{{ url("hr/reports/bill-announcement-report") }}" id="formReport">
                    @csrf
                    <div class="iq-card" id="result-section">
                      <div class="iq-card-header d-flex mb-0">
                         <div class="iq-header-title w-100">
                            <div class="row">
                              <div style="width: 10%; float: left; margin-left: 15px; margin-top: 2px;">
                                <div id="result-section-btn">
                                  <button type="button" class="btn btn-sm btn-primary hidden-print" onclick="printDiv('report_section')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
                                </div>
                              </div>
                              <div class="text-center" style="width: 47%; float: left">
                                
                              </div>

                              <input type="hidden" id="reportFormat" name="report_format" value="1">
                              <div style="width: 40%; float: left">
                                <div class="row">
                                  <div class="col-3 p-0">
                                      {{-- <div class="form-group has-float-label has-required ">
                                        <input type="month" class="report_date form-control month-report" id="yearMonth" name="year_month" placeholder=" Month-Year" required="required" value="" max="{{ date('Y-m') }}" autocomplete="off">
                                        <label for="yearMonth">Month</label>
                                      </div> --}}
                                  </div>
                                  <div class="col-4 pr-0">
                                    <div class="format">
                                      
                                      <div class="form-group has-float-label select-search-group mb-0">
                                          <?php
                                            
                                            $type = ['as_unit_id'=>'Unit','as_location'=>'Location','as_department_id'=>'Department','as_designation_id'=>'Designation','as_section_id'=>'Section','as_subsection_id'=>'Sub Section'];
                                            if(auth()->user()->hasRole('Super Admin')){
                                              $selectGroup = 'as_unit_id';
                                            }else{
                                              $selectGroup = 'as_department_id';
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
 
<div class="modal right fade" id="right_modal_lg-group"  role="dialog" aria-labelledby="right_modal_lg-group">
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
  <div class="form-group has-float-label select-search-group">
    {{ Form::select('bill_type', $billType, '', ['placeholder'=>'Select Bill Type', 'class'=>'form-control capitalize select-search', 'id'=>'billType']) }}
    <label for="billType">Bill Type</label>
  </div>
  <hr class="mt-2">
  <div class="form-inline mb-3 mt-10">
    {{-- <input type="hidden" id="durationType" name="duration_type" value="1">                         --}}
    <div class="custom-control custom-radio custom-control-inline">
       <input type="radio" id="form-range" name="date_type" class="date_type custom-control-input" value="range" checked="" onclick="durationTypeChange('range')">
       <label class="custom-control-label cursor-pointer" for="form-range"> Range </label>
    </div>
    
    <div class="custom-control custom-radio custom-control-inline">
       <input type="radio" id="form-month" name="date_type" class="date_type custom-control-input" value="month" onclick="durationTypeChange('month')">
       <label class="custom-control-label cursor-pointer" for="form-month"> Month </label>
    </div>
  </div>
  <div id="month-form" style="display: none;">
      <div class="form-group has-float-label has-required">
        <input type="month" name="month_year" class="report_date form-control" id="month-year" placeholder=" Month-Year" value="{{ $yearMonth }}" max="{{ date('Y-m')}}" autocomplete="off" />
        <label for="month-year">Month</label>
      </div>
  </div>
  <div class="row" id="range-form">
    <div class="col">
        <div class="form-group has-float-label has-required">
            <input type="date" name="from_date" class="report_date datepicker form-control" id="from_date" placeholder="Y-m-d" required="required" value="{{ $form_date }}" max="{{ date("Y-m-d") }}" autocomplete="off" />
            <label for="from_date">From Date</label>
        </div>
    </div>
    <div class="col">
        <div class="form-group has-float-label has-required">
            <input type="date" name="to_date" class="report_date datepicker form-control" id="to_date" placeholder="Y-m-d" required="required" value="{{ $to_date }}" max="{{ date("Y-m-d") }}" autocomplete="off" />
            <label for="to_date">To Date</label>
        </div>
    </div>
  </div>
  <hr class="mt-2">
  <div class="form-group has-float-label select-search-group">
    {{ Form::select('pay_type', [0=>'Due', 1=> 'Paid'], '', ['placeholder'=>'Select Pay Type', 'class'=>'form-control capitalize select-search', 'id'=>'payType']) }}
    <label for="payType">Pay Status</label>
  </div>
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
  function durationTypeChange(value){
    if(value == 'month'){
      $("#month-form").show();
      $("#range-form").hide();
      // $("#durationType").val(1);
    }else if(value == 'range'){
      $("#month-form").hide();
      $("#range-form").show();
      // $("#durationType").val(0);
    }
  }
  $(document).on('click', '.bill-details', function() {
    let name = $(this).data('ename');
    $("#modal-title-right").html(' '+name+' Bill Details');
    $('#right_modal_jobcard').modal('show');
    $("#content-result").html(loaderContent);
    $.ajax({
        url: '{{ url("/hr/reports/bill-single-report") }}',
        type: "GET",
        data: {
          as_id: $(this).data('id'),
          from_date: $(this).data('fromdate'),
          to_date: $(this).data('todate'),
          pay_status: $(this).data('paytype'),
          unit: $(this).data('unit')
        },
        type: "GET",
        success: function(response){
          // console.log(response);
            if(response !== 'error'){
              setTimeout(function(){
                $("#content-result").html(response);
              }, 1000);
            }else{
              console.log(response);
            }
        }
      });
  });
</script>
@endpush
@endsection