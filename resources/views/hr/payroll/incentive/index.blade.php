@extends('hr.layout')
@section('title', 'Incentive')

@section('main-content')
@push('css')
<link href="{{ asset('assets/css/jquery-ui.min.css') }}" rel="stylesheet">
<style>
    .flex-chunk{
        min-width: 40px;margin-right: 2px;border-right: 1px solid;padding-right: 2px;
    }
    .flex-chunk:last-child{
        margin-right: 0px;border-right: 0px solid;padding-right: 0px;
    }
    .modal-footer {
        position: fixed;
        width: 100%;
        bottom: 0;
        overflow: hidden;
        background: #fff;
    }
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
                    <a href="#">Payroll</a>
                </li>
                <li class="active"> Incentive Bonus</li>
                <li class="top-nav-btn">
                    <a href="{{url('hr/reports/incentive-bonus?report_format=0')}}" class="btn btn-sm btn-primary" target="_blank"> <i class="fa fa-list"></i> Incentive List</a>
                    <a href="{{url('hr/operation/incentive-bonus')}}" class="btn btn-sm btn-success" target="_blank"> <i class="las la-file-invoice-dollar"></i> Incentive Pay</a>
                </li>
            </ul>
        </div>

        <div class="page-content">
            <div class="iq-accordion career-style mat-style  ">
                {{-- <div class="iq-card iq-accordion-block">
                 <div class="active-mat clearfix">
                    <div class="container-fluid">
                       <div class="row">
                          <div class="col-sm-12"><a class="accordion-title"><span class="header-title"> Upload File </span> </a></div>
                       </div>
                    </div>
                 </div>
                 <div class="accordion-details">
                    <div class="row1">
                        <div class="col-12">
                            <form class="" role="form" id="employeeWiseSalary">
                                <div class="panel">
                                      <div class="panel-body">
                                          <div class="row">
                                              <div class="col-sm-4">
                                                  <div class="form-group select-search-group has-float-label has-required select-search-group">
                                                      <input type="file" class="form-control" id="as_id" style="line-height:16px;">
                                                      <label for="as_id">Choose XLSX File</label>
                                                  </div>
                                                  
                                              </div>
                                              
                                              <div class="col-sm-2">
                                                  <div class="form-group has-float-label has-required">
                                                    <input type="date" class="form-control" id="disburse_date" required>
                                                    <label for="disburse_date">Disburse Date</label>
                                                  </div>
                                              </div>
                                              <div class="col-sm-2">
                                                  <button type="button" class="btn btn-outline-primary btn-sm"><i class="fa fa-save"></i> Upload</button>
                                                  
                                              </div>
                                          </div>
                                      </div>
                                </div>
                            </form>
                        </div>
                    </div>
                 </div>
                </div> --}}
                <form id="employeeIncentive">
                    <div class="iq-card iq-accordion-block accordion-active">
                        <div class="active-mat clearfix">
                            <div class="container-fluid">
                               <div class="row">
                                    <div class="col-sm-12">
                                        <a class="accordion-title">
                                            <span class="header-title" style="width:25%"> Incentive </span> 
                                            <div class="form-group has-float-label has-required" style="display: inline-block; margin-top: 10px; margin-bottom: 0px;">
                                                <input type="date" name="date" class="form-control" id="incentive-date" required value="{{ date('Y-m-d') }}">
                                                <label for="incentive-date">Incentive Date</label>
                                            </div>
                                        </a>
                                    </div>
                               </div>
                            </div>
                        </div>
                        <div class="accordion-details">
                            <div class="panel">
                                <div class="panel-body">
                                    <div class='table-wrapper-scroll-y table-custom-scrollbar'>
                                        <table class="table table-bordered table-hover table-fixed table-head table-responsive" id="itemList">
                                            <thead>
                                                <tr class="text-center active">
                                                    <th width="2%">
                                                        <button class="btn btn-sm btn-outline-success addmore" type="button" ><i class="fa fa-plus"></i></button>
                                                    </th>
                                                    <th width="2%">SL.</th>
                                                    <th width="11%">Associate ID</th>
                                                    <th width="9%">Oracle ID</th>
                                                    <th width="7%">Amount</th>
                                                    <th width="15%">Name</th>
                                                    <th width="15%">Designation</th>
                                                    <th width="12%">Department</th>
                                                    <th width="8%">Floor</th>
                                                    <th width="8%">Line</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table-body">
                                              
                                              <tr >
                                                <td>
                                                    <button class="btn btn-sm btn-outline-danger delete" type="button" id="deleteItem1" onClick="deleteItem(this.id)">
                                                        <i class="las la-trash"></i>
                                                    </button>
                                                </td>
                                                <td class="index">1</td>
                                                <td>
                                                  <input type="text" data-type="associateid" name="associate[]" id="associate_1" class="form-control autocomplete_txt" autofocus="autofocus" autocomplete="off" >
                                                  <div id="extrainput_1"></div>
                                                </td>
                                                <td>
                                                  <input type="text" data-type="oracleid" name="oracle[]" id="oracle_1" class="form-control autocomplete_txt" autofocus="autofocus" autocomplete="off" >
                                                  <div id="oracleinput_1"></div>
                                                </td>
                                                <td>
                                                  <input type="text" name="amount[]" step="any" id="amount_1" class="form-control amount" autocomplete="off" value="0" onClick="this.select()" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" >
                                                </td>
                                                <td>
                                                  <input type="text" class="form-control" id="name_1" readonly>
                                                </td>
                                                <td>
                                                  <input type="text" value="" class="form-control" id="designation_1" readonly>
                                                </td>
                                                <td>
                                                  <input type="text" value="" class="form-control" id="department_1" readonly>
                                                </td>
                                                <td>
                                                  <input type="text" value="" class="form-control" id="floor_1" readonly>
                                                </td>
                                                <td>
                                                  <input type="text" value="" class="form-control" id="line_1" readonly>
                                                </td>
                                              </tr>
                                            </tbody>

                                            <tfoot>
                                              <tr>
                                                <td colspan="4" class="text-right">Total</td> 
                                                <td class="text-right" id="total">0</td>
                                                <td colspan="5"></td> 
                                              </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div id="employee-select"></div>
                                    <div class="bottom-section pb-3" style="overflow: hidden;">
                                      {{-- <button type="button" class="btn btn-md btn-outline-success pull-left" onclick="saveIncentive()"> <i class="fa fa-save"></i> Save </button> --}}
                                      <button type="button" class="btn btn-md btn-outline-primary pull-right" onclick="previewIncentive()"> <i class="las la-angle-double-right"></i> Continue </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
<div class="modal right fade" id="right_modal_lg-group" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg-group">
  <div class="modal-dialog modal-lg right-modal-width" role="document" > 
    <div class="modal-content">
      <div class="modal-header">
        <a class="view prev_btn" data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Back to Report">
            <i class="las la-chevron-left"></i>
        </a>
        <h5 class="modal-title right-modal-title text-center" id="modal-title-right-group"> Incentive bonus </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="modal-content-result content-result" id="body_result_section">
            
        </div>
      </div>
      <div class="modal-footer">
        <div class="inner_buttons">
          <a class=" prev_btn btn btn-outline-danger btn-sm" data-toggle="tooltip" data-dismiss="modal"><i class="las la-times"></i> Cancel </a>

          <button class=" btn btn-sm btn-outline-success confirm-disbursed" id="confirm-disbursed" onClick="saveIncentive()" type="button" tabindex="0">
           <i class="las la-check"></i> Confirm & Save
          </button>
         
        </div>
      </div>
    </div>
  </div>
</div>
@push('js')
<script src="{{ asset('assets/js/jquery-ui.js')}}"></script>
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script src="{{ asset('assets/js/incentive.js')}}"></script>
<script src="{{asset('assets/js/sweetalert2.min.js')}}"></script>
   <script>
        $(document).on('keypress', function(e) {
            var that = document.activeElement;
            if( e.which == 13 ) {
                if($(document.activeElement).attr('type') == 'submit'){
                    return true;
                }else{
                    e.preventDefault();
                }
            }            
        });
        function saveIncentive(){
            
            $(".app-loader").show();
            var data = $("#employeeIncentive").serialize();
            let url = '{{ url('hr/payroll/incentive-bonus') }}'
            $.ajax({
              type: 'POST',
              url: url,
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
              },
              data: data, // serializes the form's elements.
              success: function(response)
              {
                if(response.type === 'success'){
                    setTimeout(function(){
                        window.location.href = response.url;
                    }, 500);
                }
                $.notify(response.message, response.type);
                $(".app-loader").hide();
                
              },
              error: function (reject) {
                $.notify('Something wrong, please try again!', 'error');
                $(".app-loader").hide();
              }
            });
            
            
        }

        function previewIncentive(){
            swal({
                title: "Incentive Bonus",
                text: "Confirm date "+$('#incentive-date').val()+" . Do you want to continue?",
                icon: "warning",
                buttons: ['Cancel','Continue'],
                dangerMode: true,
                closeModal: false
            })
            .then((willDelete) => {
                // var r = confirm("Confirm incentive date "+$('#incentive-date').val()+" . Do you want to continue?");
                if (willDelete) {
                    $('#right_modal_lg-group').modal('show');
                    $("#body_result_section").html(loaderContent);
                    var data = $("#employeeIncentive").serialize();
                    let url = '{{ url('hr/payroll/incentive-bonus-preview') }}'
                    $.ajax({
                      type: 'POST',
                      url: url,
                      headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                      },
                      data: data, // serializes the form's elements.
                      success: function(response)
                      {
                        // console.log(response)
                        if(response !== 'error'){
                            setTimeout(function(){
                                $("#body_result_section").html(response);
                            }, 1000);
                        }
                      },
                      error: function (reject) {
                        $.notify('Something wrong, please try again!', 'error');
                      }
                    });
                }
            })
            .then(results => {
              
            })
            .catch(err => {
                if (err) {
                    swal("Oh noes!", "The AJAX request failed!", "error");
                } else {
                    swal.stopLoading();
                    swal.close();
                }
            });
        }
    </script>
@endpush
@endsection