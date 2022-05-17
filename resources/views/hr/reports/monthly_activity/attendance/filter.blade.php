@extends('hr.layout')
@section('title', 'Attendance')

@section('main-content')
@push('css')
  <style>
    .single-employee-search {
      margin-top: 82px !important;
    }
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/editor.dataTables.min.css') }}" />
  <style>
    #dataTables th:nth-child(2) input{
      width: 80px !important;
    }
    #dataTables th:nth-child(3) input{
      width: 100px !important;
    }
    #dataTables th:nth-child(4) input{
      width: 120px !important;
    }
    #dataTables th:nth-child(5) input{
      width: 120px !important;
    }
    #dataTables th:nth-child(6) input{
      width: 120px !important;
    }
    #dataTables th:nth-child(7) input{
      width: 52px !important;
    }
    #dataTables th:nth-child(8) input, #dataTables th:nth-child(9) input, #dataTables th:nth-child(10) input{
      width: 52px !important;
    }
    #dataTables th:nth-child(11) input,#dataTables th:nth-child(12) input{
      width: 62px !important;
    }
    table.dataTable {
      border-spacing: 1px;
    }
    .badge {
      font-size: 100%;
    }
    div.dataTables_wrapper div.dataTables_processing {
      top: 10% !important;
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
                <li class="active"> Monthly Attendance Report : <b id="yearDetails">{{ $yearMonth }}</b></li>
                <li class="top-nav-btn">
                    <div class="text-right">
                      <a class="btn view no-padding clear-filter" data-toggle="tooltip" data-placement="top" title="" data-original-title="Clear Filter">
                        <i class="las la-redo-alt" style="color: #f64b4b; border-color:#be7979"></i>
                      </a>
                      <a class="btn view no-padding filter" data-toggle="tooltip" data-placement="top" title="" data-original-title="Advanced Filter">
                        <i class="fa fa-filter"></i>
                      </a>
                      
                      
                    </div>
                </li>
            </ul>
        </div>
        <div class="page-content"> 
            
            <div class="row">
                <div class="col">
                  <form role="form" method="get" action="{{ url("hr/reports/monthly-attendance-activity-data") }}" id="formReport">
                    @csrf
                    
                  </form>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="table d-table1 hide">
                      <div class="iq-card">
                        <div class="iq-card-body">
                          <table id="dataTables" class="table table-striped table-bordered table-head w-100 table-responsive" style="display: block;overflow-x: auto;width: 100%;">
                             <thead>
                                <tr>
                                   <th>Sl.</th>
                                   <th>ID</th>
                                   <th>Name & phone</th>
                                   <th>Designation</th>
                                   <th>Department</th>
                                   <th>Section</th>
                                   <th>Line</th>
                                   <th>OT/Non-OT</th>
                                   <th>Present</th>
                                   <th>Absent</th>
                                   <th>Leave</th>
                                   <th>Holiday</th>
                                   <th>Late</th>
                                   <th>OT Hour</th>
                                   <th>Total Day</th>
                                </tr>
                             </thead>
                          </table>
                       </div>
                     </div>
                   </div>
                   
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
{{-- modal employee salary --}}

@section('right-nav')
  <hr class="mt-2">
  <div class="form-group has-float-label has-required ">
    <input type="month" class="report_date form-control month-report" id="year-month" name="year_month" placeholder=" Month-Year" required="required" value="{{ $yearMonth }}" max="{{ date('Y-m') }}" autocomplete="off">
    <label for="year-month">Month</label>
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
@include('common.right-navbar-data-table')

@push('js')
<script type="text/javascript">
$(document).ready(function () {
  $(".filter").click();
  let searchable = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11];
  let selectable = [];
  let dropdownList = {};
  let exportColName = ['Sl.', 'ID', 'Name', 'Designation', 'Department', 'Section', 'Line', 'OT/Non-OT', 'Present', 'Absent', 'Leave', 'Holiday', 'Late', 'OT Hour', 'Total Day'];
  let exportCol = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13,14];
  var dTable = $('#dataTables').DataTable({

      order: [], //reset auto order
      lengthMenu: [
          [25, 50, 100, -1],
          [25, 50, 100, "All"]
      ],
      processing: true,
      responsive: true,
      serverSide: true,
      cache: false,
      fixedHeader: true,
      language: {
          processing: '<i class="fa fa-spinner fa-spin f-60"></i><span class="sr-only">Loading...</span> '
      },
      scroller: {
          loadingIndicator: false
      },
      pagingType: "full_numbers",
      ajax: {
          url: '{!! url("hr/reports/monthly-attendance-activity-data") !!}',
          data: function(d) {
              d.unit = unitSelect,
                  // d.associate_id  = $('#associate_id').val(),
                  d.otnonot = $('#otnonot').val(),
                  d.floor_id = $("#floor_id").val(),
                  d.line_id = $("#line_id").val(),
                  d.area = $("#area").val(),
                  d.department = $("#department").val(),
                  d.section = $("#section").val(),
                  d.subSection = $("#subSection").val(),
                  d.year_month = $("#year-month").val(),
                  d.min_sal = $("#min_sal").val(),
                  d.max_sal = $("#max_sal").val(),
                  d.emp_status = empStatusSelect,
                  d.shift_roaster_status = $("#shift_roaster_status").val(),
                  d.location = locationSelect

          },
          type: "get",
          headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
          }
      },

      dom: 'lBfrtip',
      buttons: [{
              extend: 'csv',
              className: 'btn btn-sm btn-success',
              title: function() {
                  var type = 'Attendance Report';

                  return type;
              },
              header: true,
              footer: false,
              exportOptions: {
                  columns: exportCol,
                  format: {
                      header: function(data, columnIdx) {
                          return exportColName[columnIdx];
                      }
                  }
              },
              "action": allExport,
              messageTop: ''
          },
          {
              extend: 'excel',
              className: 'btn btn-sm btn-warning',
              title: function() {
                  var type = 'Attendance Report';

                  return type;
              },
              header: true,
              footer: false,
              exportOptions: {
                  columns: exportCol,
                  format: {
                      header: function(data, columnIdx) {
                          return exportColName[columnIdx];
                      }
                  }
              },
              "action": allExport,
              messageTop: ''
          },
          {
              extend: 'print',
              className: 'btn btn-sm btn-default',
              title: '',
              header: true,
              footer: false,
              orientation: 'landscape',
              exportOptions: {
                  columns: exportCol,
                  format: {
                      header: function(data, columnIdx) {
                          return exportColName[columnIdx];
                      }
                  }
              },
              "action": allExport,
              messageTop: ''
              // messageTop: function() {
              //     var data = {
              //         unit: '',
              //         location: '',
              //         otnonot: '',
              //         floor_id: '',
              //         line_id: '',
              //         area: '',
              //         department: ''
              //     }
              //     if ($('#unit').val() != null) {
              //         data.unit = $('#unit').select2('data')[0].text;
              //     }
              //     if ($('#floor_id').val() != null) {
              //         data.floor_id = $('#floor_id').select2('data')[0].text;
              //     }
              //     if ($('#line_id').val() != null) {
              //         data.line_id = $('#line_id').select2('data')[0].text;
              //     }
              //     if ($('#area').val() != null) {
              //         data.area = $('#area').select2('data')[0].text;
              //     }
              //     if ($('#department').val() != null) {
              //         data.department = $('#department').select2('data')[0].text;
              //     }
              //     if ($('#section').val() != null) {
              //         data.section = $('#section').select2('data')[0].text;
              //     }
              //     if ($('#subSection').val() != null) {
              //         data.subSection = $('#subSection').select2('data')[0].text;
              //     }

              //     return operationReportHeader(data.type, data);
              // }
          }
      ],

      columns: [{
              data: 'DT_RowIndex',
              name: 'DT_RowIndex'
          },
          {
              data: 'associate_id',
              name: 'associate_id'
          },
          {
              data: 'as_name',
              name: 'as_name'
          },
          {
              data: 'hr_designation_name',
              name: 'hr_designation_name'
          },
          {
              data: 'hr_department_name',
              name: 'hr_department_name'
          },
          {
              data: 'hr_section_name',
              name: 'hr_section_name'
          },
          // { data: 'hr_subsection_name', name: 'hr_subsection_name' },
          {
              data: 'hr_line_name',
              name: 'hr_line_name'
          },
          {
              data: 'ot_status',
              name: 'ot_status'
          },
          {
              data: 'present',
              name: 'present'
          },
          {
              data: 'absent',
              name: 'absent'
          },
          {
              data: 'leave',
              name: 'leave'
          },
          {
              data: 'holiday',
              name: 'holiday'
          },
          {
              data: 'late_count',
              name: 'late_count'
          },
          {
              data: 'ot_hour',
              name: 'ot_hour'
          },
          {
              data: 'total_day',
              name: 'total_day'
          }

      ],

      initComplete: function() {
          var api = this.api();

          // Apply the search
          api.columns(searchable).every(function() {
              var column = this;
              var input = document.createElement("input");
              input.setAttribute('placeholder', $(column.header()).text());

              $(input).appendTo($(column.header()).empty())
                  .on('keyup', function(e) {
                      if (e.keyCode == 13) {
                          column.search($(this).val(), false, false, true).draw();
                      }
                  });

              $('input', this.column(column).header()).on('click', function(e) {
                  e.stopPropagation();
              });
          });

          // each column select list
          api.columns(selectable).every(function(i, x) {
              var column = this;

              var select = $('<select><option value="">' + $(column.header()).text() + '</option></select>')
                  .appendTo($(column.header()).empty())
                  .on('change', function(e) {
                      var val = $.fn.dataTable.util.escapeRegex(
                          $(this).val()
                      );
                      column.search(val ? val : '', true, false).draw();
                      e.stopPropagation();
                  });

              // column.data().unique().sort().each( function ( d, j ) {
              // if(d) select.append('<option value="'+d+'">'+d+'</option>' )
              // });
              $.each(dropdownList[i], function(j, v) {
                  select.append('<option value="' + v + '">' + v + '</option>')
              });
          });
      }

  });
  $(document).on('click', '.filterBtnSubmit', function(e) {
    e.preventDefault();
    $(".d-table1").removeClass('hide');
    $(".prev_btn").click();
    unitSelect = [];
    $('input:checkbox.unit').each(function () {
      var sThisVal = (this.checked ? $(this).val() : "");
      if(sThisVal !== ""){
        unitSelect.push(sThisVal);
      }
    });
    locationSelect = [];
    
    $('input:checkbox.location').each(function () {
      var lThisVal = (this.checked ? $(this).val() : "");
      if(lThisVal !== ""){
        locationSelect.push(lThisVal);
      }
    });
    empStatusSelect = [];
    $('input:checkbox.sta').each(function () {
      var sThisVal = (this.checked ? $(this).val() : "");
      if(sThisVal !== ""){
        empStatusSelect.push(sThisVal);
      }
    });
    dTable.draw();
    // advFilter();
  });
});
$(document).on('change', '#year-month', function(event) {
  $("#yearDetails").html($(this).val())
});
</script>
@endpush

@endsection