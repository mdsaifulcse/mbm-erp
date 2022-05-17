@extends('merch.layout')
@section('title', 'Sample List')
@section('main-content')
@push('css')
<style type="text/css">
  tr td:nth-child(2){
      display: block;
      width: 75px !important;
  }
  tr th:nth-child(3) input{
    width: 80px !important;
  }
  tr th:nth-child(6) input{
    width: 70px !important;
  }
  tr th:nth-child(7) input{
    width: 80px !important;
  }
  tr th:nth-child(8) select{
    width: 80px !important;
  }
  tr th:nth-child(9) select{
    width: 100px !important;
  }
</style>
@endpush
<div class="main-content">
  <div class="main-content-inner">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
      <ul class="breadcrumb">
        <li>
          <i class="fa fa-home home-icon"></i>
          <a href="#">Merchandisign</a>
        </li>
        <li>
          <a href="#">Sample</a>
        </li>
        <li class="active">Sample Requisition List</li>
      </ul><!-- /.breadcrumb -->
    </div>

        @include('inc/message')
    <div class="page-content">
      {{-- <div class="panel ">
                
                <div class="panel-body pb-0">
       <!-- Display Erro/Success Message -->
          <form class="row" role="form" id="empFilter" method="get" action="#">
                        <div class="col-3">
                            <div class="form-group has-float-label has-required select-search-group">
                                {{ Form::select('unit', $allUnit, null, ['placeholder'=>'Select Unit', 'id'=>'unit',  'class'=>'form-control']) }}
                                <label  for="unit"> Unit </label>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="form-group has-float-label select-search-group">
                                <select name="otnonot" id="otnonot" class="form-control filter">
                                    <option value="">Select OT/Non-OT</option>
                                    <option value="0">Non-OT</option>
                                    <option value="1">OT</option>
                                </select>
                                <label  for="otnonot">OT/Non-OT </label>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="form-group has-float-label select-search-group">
                                {{ Form::select('emp_type', $empTypes, null, ['placeholder'=>'Select Employee Type', 'id'=>'emp_type',  'class'=>'form-control']) }}
                                <label  for="emp_type"> Employee Type </label>
                            </div>
                        </div>

                        <div class="col-2">
                            <button type="button" id="" class="btn btn-primary  empFilter">
                            <i class="fa fa-search"></i>
                            Search
                            </button>
                        </div>
                </form>
            </div>
        </div> --}}
        

      <div class="panel ">  
        <div class="panel-heading"><h6>Sample Requisition List</h6></div>   
        
        
        <div class="col-12 worker-list pb-3 pt-3">
          <table id="dataTables" class="table table-striped table-bordered" style="overflow-x: auto;width: 100%;" border="1">
            <thead>
              <tr>
                <th>Sl.</th>
                <th>Buyer</th>
                <th>Style</th>
                <th>Product</th>
                <th>Description</th>
                <th>Color</th>
                <th>Sample Type</th>
                <th>Quantity</th>
                <th>Requisition Date</th>
                <th>Sample Delevery Date</th>
                <th>Test Send Date</th>
                <th>Test Status</th>
                @can('Manage Employee')
                <th>Action</th>
                @endcan
              </tr>
            </thead>
    
          </table>
        </div>
      </div>

    </div><!-- /.page-content -->
  </div>
</div>
@push('js')
<script type="text/javascript">

$(document).ready(function()
{
  $('#dataTables').DataTable({
    // processing: true,
    // serverSide: true,
    order: [],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        // processing: true,
        responsive: false,
        serverSide: true,
          processing: true,
            language: {
              processing: '<i class="fa fa-spinner fa-spin f-60" style="font-size:60px;margin-top:50px;z-index:100;"></i>'
            },
            scroller: {
                loadingIndicator: false
            },
          pagingType: "full_numbers",
    ajax: {
      url: "{{url('merch/sample/sample_requisition_listcollect')}}",
    },
    dom:'lBfrtip',
        buttons: [   
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                title: 'Manpower Bullantin',
                "action": allExport,
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,2,3,4,5]
                }
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                title: 'Manpower Bullantin ',
                "action": allExport,
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,2,3,4,5]
                }
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary', 
                title: 'Manpower Bullantin',
                "action": allExport,
                exportOptions: {
                    // columns: ':visible'
                    columns: [0,2,3,4,5]
                }
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: 'Manpower Bullantin',
                "action": allExport,
                exportOptions: {
                    columns: [0,2,3,4,5]
                } 
            } 
        ], 
    columns: [
    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
    {data: 'buyer', name:  'buyer'},
    {data: 'style',name:  'style',orderable: false},
	{data: 'product',name:'product'},
	{data: 'garment_description',name:'garment_description'},
	{data: 'color',name:'color'},
	{data: 'sample_name',name:'sample_name'},
	{data: 'quantity',name:'quantity'},
	{data: 'requisition_date',name:'requisition_date'},
	{data: 'sample_delevary_date',name:'sample_delevary_date'},
    {data: 'test_send_date',name:'test_send_date'},
    {data: 'test_status',name:'test_status'},
    {data: 'action', name: 'action', orderable: false, searchable: false}
    
    ]
});
 
});
</script>
@endpush
@endsection
