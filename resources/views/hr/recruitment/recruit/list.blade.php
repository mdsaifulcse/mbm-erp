@extends('hr.layout')
@section('title', 'Recruitment List')
@push('css')
  
  <link rel="stylesheet" href="{{ asset('assets/css/recruitment.css')}}">
  <style>
    #recruit th:nth-child(2) input{
      width: 120px !important;
    }
    #recruit th:nth-child(3) input{
      width: 60px !important;
    }
    #recruit th:nth-child(4) input{
      width: 60px !important;
    }
    #recruit th:nth-child(5) input{
      width: 120px !important;
    }
    #recruit th:nth-child(6) input{
      width: 60px !important;
    }
    #recruit th:nth-child(7) input{
      width: 70px !important;
    }
    #recruit th:nth-child(8) input{
      width:80px !important;
    }

    #recruit th:nth-child(9) input{
      width: 70px !important;
    }
    
    table.dataTable thead>tr>td.sorting, table.dataTable thead>tr>td.sorting_asc, table.dataTable thead>tr>td.sorting_desc, table.dataTable thead>tr>th.sorting, table.dataTable thead>tr>th.sorting_asc, table.dataTable thead>tr>th.sorting_desc {
    padding-right: 16px;
}
  </style>
@endpush
@section('main-content')
   <div class="row">
      <div class="col-sm-12 col-lg-12">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                   <a href="/"><i class="ace-icon fa fa-home home-icon"></i>Human Resource</a> 
                </li>
                <li>
                    <a href="#">Recruitment</a>
                </li>
                <li class="active">Recruit List</li>
            </ul><!-- /.breadcrumb --> 
        </div>
         <div class="panel">
            <div class="panel-heading">
                  <h6 >Recruitment List
                    <a href="{{url('hr/recruitment/recruit/create')}}" class="btn btn-primary pull-right">New Recruit</a>
                  </h6>
            </div>
            <div class="panel-body">
               <table id="recruit" class="table table-striped table-bordered table-head table-responsive w-100">
                  <thead>
                     <tr>
                        <th width="5%">Sl</th>
                        <th>Name</th>
                        <th>Employee Type</th>
                        <th>Oracle ID</th>
                        <th>Designation</th>
                        <th width="5%">Unit</th>
                        <th>Area</th>
                        <th>Contact</th>
                        <th>DOJ</th>
                        <th>NID/Birth</th>
                        <th>Medical</th>
                        <th>IE</th>
                        <th width="10%">Action</th>
                     </tr>
                  </thead>
               </table>
            </div>
         </div>
      </div>
   </div>
   @push('js')
   <script type="text/javascript">
   $(document).ready(function(){ 
      var searchable = [1,2,3,4,5,6,7];
      var exportColName = ['SL','Employee Type','Oracle ID','Designation','Unit', 'Area', 'Name','Contact','DOJ'];
      var exportCol = [0,1,2,3,4,5,6,7];
      var dt = $('#recruit').DataTable({
          order: [], //reset auto order
          processing: true,
          language: {
              processing: '<i class="fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;z-index:100;"></i>'
          },
          responsive: true,
          serverSide: true,
          pagingType: "full_numbers", 
          ajax: {
               url: '{!! url("hr/recruitment/recruit-data-list") !!}',
               type: "GET",
               headers: {
                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
               } 
          }, 
          dom: "lBftrip",
          buttons: [   
              {
                  extend: 'csv', 
                  className: 'btn btn-sm btn-success',
                  title: 'Employee recruitment list',
                  header: true,
                  footer: false,
                  exportOptions: {
                      columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
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
                  title: 'Employee recruitment list',
                  header: true,
                  footer: false,
                  exportOptions: {
                      columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      }
                  },
                  "action": allExport,
                  messageTop: ''
              }, 
              {
                  extend: 'pdf', 
                  className: 'btn btn-sm btn-primary', 
                  title: 'Employee recruitment list',
                  header: true,
                  footer: false,
                  exportOptions: {
                      columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
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
                  exportOptions: {
                      columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      }
                  },
                  "action": allExport,
                  messageTop: customReportHeader('Employee recruitment list', { })
              } 
          ],
          columns: [  
               {data: 'DT_RowIndex', name: 'DT_RowIndex'}, 
               {data: 'worker_name', name: 'worker_name'}, 
               {data: 'hr_emp_type_name', name: 'hr_emp_type_name'}, 
               {data: 'as_oracle_code', name: 'as_oracle_code'}, 
               {data: 'hr_designation_name', name: 'hr_designation_name'}, 
               {data: 'hr_unit_short_name', name: 'hr_unit_short_name'}, 
               {data: 'hr_area_name', name: 'hr_area_name'}, 
               {data: 'worker_contact', name: 'worker_contact'}, 
               {data: 'worker_doj', contact: 'worker_doj'},
               {data: 'worker_nid', contact: 'worker_nid'},
               {data: 'medical_info', contact: 'medical_info'},
               {data: 'ie_info', contact: 'ie_info'},
               {data: 'action', name: 'action', orderable: false, searchable: false}
            ],

            initComplete: function () {   
            var api =  this.api();

            // Apply the search 
            api.columns(searchable).every(function () {
                var column = this; 
                var input = document.createElement("input"); 
                input.setAttribute('placeholder', $(column.header()).text());
                input.setAttribute('style', 'width: 120px; height:25px; border:1px solid whitesmoke;');

                $(input).appendTo($(column.header()).empty())
                .on('keyup', function () {
                    column.search($(this).val(), false, false, true).draw();
                });

                $('input', this.column(column).header()).on('click', function(e) {
                    e.stopPropagation();
                });
            });
         } 
       }); 

   }); 
   </script>
   @endpush
@endsection