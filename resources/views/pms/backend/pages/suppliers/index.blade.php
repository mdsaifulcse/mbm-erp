@extends('pms.backend.layouts.master-layout')

@section('title', config('app.name', 'laravel'). ' | '.$title)

@section('page-css')

@endsection

@section('main-content')
<!-- WRAPPER CONTENT ----------------------------------------------------------------------------->
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
          <ul class="breadcrumb">
              <li>
                  <i class="ace-icon fa fa-home home-icon"></i>
                  <a href="{{  route('pms.dashboard') }}">{{ __('Home') }}</a>
              </li>
              <li>
                  <a href="#">PMS</a>
              </li>
              <li class="active">{{__($title)}} List</li>
              <li class="top-nav-btn">
                <a href="{{route('pms.supplier.create')}}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add Supplier"> <i class="las la-plus"></i>Add</a>

                  <a href="javascript:void(0)" class="btn btn-sm btn-info text-white" data-toggle="tooltip" title="Upload Supplier by xlsx file" id="uploadFile"> <i class="las la-cloud-upload-alt"></i>Upload Supplier</a>
            </li>
        </ul><!-- /.breadcrumb -->

    </div>

    <div class="page-content">
        <div class="">
            <div class="panel panel-info">
                <div class="panel-body">
                    <table  id="dataTable" class="table table-striped table-bordered table-head" border="1">
                        <thead>
                            <tr>
                                <th width="5%">{{__('SL No.')}}</th>
                                <th>{{__('message.Name')}}</th>
                                <th>{{__('Email')}}</th>
                                <th>{{__('Phone')}}</th>
                                <th>{{__('Mobile No')}}</th>
                                <th>{{__('Address')}}</th>
                                <th class="text-center">{{__('Option')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suppliers as $key => $supplier)
                            <tr>
                                <th width="5%">{{ $key+1 }}</th>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->email }}</td>
                                <td>{{ $supplier->phone }}</td>
                                <td>{{ $supplier->mobile_no }}</td>
                                <td>{{ $supplier->address }}</td>
                                <td class="text-center">
                                    <a href="{{ route('pms.supplier.edit', $supplier->id) }}" class="btn btn-info m-1 rounded-circle"><i class="las la-edit"></i></a>
                                    <a href="{{ route('pms.supplier.profile', $supplier->id) }}" class="btn btn-info m-1 rounded-circle"><i class="las la-user"></i></a>

                                    <a href="javascript:void(0)" data-role="delete" data-src="{{ route('pms.supplier.destroy', $supplier->id) }}" class="btn btn-danger m-1 rounded-circle deleteBtn"><i class="las la-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- END WRAPPER CONTENT ------------------------------------------------------------------------->
<!-- Modal ------------------------------------------------------------------------->
<div class="modal fade" id="supplierAddModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supplierAddModalLabel">Add New Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" data-src="{{ route('pms.supplier.store') }}">
                @csrf
                @method('post')
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-danger rounded" id="categoryFormSubmit">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END Modal ------------------------------------------------------------------------->

    <!-- Supplier Upload Modal Start-->
<div class="modal fade bd-example-modal-lg" id="supplierUploadModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="brandAddModalLabel">{{ __('Upload Suppliers') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>
            <form id="brandForm"  enctype="multipart/form-data" action="{{route('pms.suppliers.import-excel')}}" method="POST">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-row">
                        <div class="col-md-12 pb-4">
                            <a href="{{URL::to('upload/excel/supplier-sample.xlsx')}}" download class="btn btn-link"><i class="las la-download"></i>{{__('Click Here To Download Format File')}}</a>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 font-weight-bold"><label for="code">{{ __('Select File for Upload') }}:</label> <code>{{ __('Expected file size is .xls , .xslx') }}</code> <span class="text-danger"></span></p>
                            <div class="input-group input-group-lg mb-3">
                                <input type="file" name="supplier_file" class="form-control" required id="excelFile" placeholder="Browse Excel file"
                                       accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                            </div>
                        </div>
                        <div class="col-3">

                            <button type="submit" class="btn btn-sm btn-success text-white" style="margin-top:32px"><i class="las la-cloud-upload-alt"></i>Upload Xls File</i></button>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded pull-left" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
    <!-- Supplier Upload Modal End -->
@endsection

@section('page-script')
<script>
    (function ($) {
        "use script";
        const tableContainer = document.getElementById('dataTable').querySelector('tbody');
        const showEmptyTable = () => {
            if(tableContainer.querySelectorAll('tr').length === 0){
                const row = document.createElement('tr');
                row.id = 'emptyRow';
                let colEmpty = document.createElement('td');
                colEmpty.innerHTML = 'No data is available';
                colEmpty.className = 'text-center';
                colEmpty.colSpan = 7;
                row.appendChild(colEmpty);
                tableContainer.appendChild(row);
            } else {
                if(tableContainer.querySelector('#emptyRow')){
                    tableContainer.querySelector('#emptyRow').remove();
                }
            }
        };
        showEmptyTable();

        const showAlert = (status, error) => {
            swal({
                icon: status,
                text: error,
                dangerMode: true,
                buttons: {
                    cancel: false,
                    confirm: {
                        text: "OK",
                        value: true,
                        visible: true,
                        closeModal: true
                    },
                },
            }).then((value) => {
                if(value) form.reset();
            });
        };

        $('#uploadFile').on('click', function () {
            $('#supplierUploadModal').modal('show');
        });

        const modalShow = () => {
            $('#supplierAddModal').modal('show');
            let url = $('#supplierAddModal').find('form').attr('data-src');
            $('#supplierAddModal').find('form').attr('action', url);
        };

        $('#addSupplierBtn').on('click', function () {
            $('#supplierAddModal').find('form')[0].reset();
            $('#supplierAddModal').find('#supplierAddModalLabel').html(`Add new supplier`);
            modalShow()
        });

        $('.editBtn').on('click', function () {
            $.ajax({
                type: 'get',
                url: $(this).attr('data-src'),
                success:function (data) {
                    console.log(data);
                    if(!data.status){
                        showAlert('error', data.info);
                        return;
                    }
                    $('#supplierAddModal').find('form')[0].reset();
                    $('#supplierAddModal').find('form').attr('data-src', data.info.src);
                        // $('#categoryAddModal').find('form').attr('method', data.info.req_type);
                        $('#supplierAddModal').find('form').find('input[name="_method"]').val(data.info.req_type);
                        $('#supplierAddModal').find('form').find('input[name="phone"]').val(data.info.phone);
                        $('#supplierAddModal').find('form').find('input[name="mobile_no"]').val(data.info.mobile_no);
                        $('#supplierAddModal').find('form').find('input[name="name"]').val(data.info.name);
                        $('#supplierAddModal').find('form').find('input[name="email"]').val(data.info.email);
                        $('#supplierAddModal').find('form').find('textarea[name="address"]').val(data.info.address);
                        $('#supplierAddModal').find('form').find('input[name="city"]').val(data.info.city);
                        $('#supplierAddModal').find('form').find('input[name="state"]').val(data.info.state);
                        $('#supplierAddModal').find('form').find('input[name="country"]').val(data.info.country);
                        $('#supplierAddModal').find('form').find('input[name="zipcode"]').val(data.info.zipcode);

                        if(data.info.status)
                        {
//                            console.log(data.info.status)
                            $('#status').select2().val(data.info.status).trigger("change");
                            //$('#supplierAddModal').find('form').find('input[name="status"]').select2('val',data.info.status);
                        }

                        $('#supplierAddModal').find('#supplierAddModalLabel').html(`Edit supplier (${data.info.name})`);
                        modalShow()
                    }
                })
        });

        $('.deleteBtn').on('click', function () {
            swal({
                title: "{{__('Are you sure?')}}",
                text: "{{__('Once you delete, You can not recover this data and related files.')}}",
                icon: "warning",
                dangerMode: true,
                buttons: {
                    cancel: true,
                    confirm: {
                        text: "Delete",
                        value: true,
                        visible: true,
                        closeModal: true
                    },
                },
            }).then((value) => {
                if(value){
                    $.ajax({
                        type: 'DELETE',
                        url: $(this).attr('data-src'),
                        success:function (data) {
                            if(data){
                                showAlert('error', data);
                                return;
                            }
                            swal({
                                icon: 'success',
                                text: 'Data deleted successfully',
                                button: false
                            });
                            setTimeout(()=>{
                                swal.close();
                            }, 1500);
                        },
                    });
                    $(this).parent().parent().remove();
                    showEmptyTable();
                }
            });
        })
    })(jQuery)
</script>
@endsection
