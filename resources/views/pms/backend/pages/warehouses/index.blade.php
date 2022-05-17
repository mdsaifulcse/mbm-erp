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
                  <a href="#">Home</a>
              </li>
              <li>
                  <a href="#">PMS</a>
              </li>
              <li class="active">{{__($title)}} List</li>
              <li class="top-nav-btn">
                <a href="javascript:void(0)" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add Warehouse" id="addWarehouseBtn"> <i class="las la-plus"></i>Add</i></a>
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
                                    <th>{{__('Code')}}</th>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Phone')}}</th>
                                    <th>{{__('Email')}}</th>
                                    <th>{{__('Location')}}</th>
                                    <th class="text-center">{{__('Option')}}</th>
                                </tr>
                            </thead>
                            <tbody>

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
<div class="modal fade bd-example-modal-lg" id="warehouseAddModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="warehouseAddModalLabel">Add New Location</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="warehouseForm">
                    <div class="form-row">
                        <div class="col-md-6">
                            <p class="mb-1 font-weight-bold"><label for="name">{{ __('Name') }}:</label> <span class="text-danger"></span></p>
                            <div class="input-group input-group-lg mb-3 d-">
                                <input type="text" name="name" id="name" class="form-control rounded" aria-label="Large" placeholder="{{__('Location Name')}}" aria-describedby="inputGroup-sizing-sm" required>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 font-weight-bold"><label for="email">{{ __('Email') }}:</label> <span class="text-danger"></span></p>
                            <div class="input-group input-group-lg mb-3">
                                <input type="email" name="email" id="email" class="form-control rounded" aria-label="Large" placeholder="{{__('Location Email')}}" aria-describedby="inputGroup-sizing-sm" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 font-weight-bold"><label for="phone">{{ __('Phone') }}:</label> <span class="text-danger"></span></p>
                            <div class="input-group input-group-lg mb-3">
                                <input type="tel" maxlength="14" name="phone" id="phone" class="form-control rounded" aria-label="Large" placeholder="{{__('Location Phone')}}" aria-describedby="inputGroup-sizing-sm" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 font-weight-bold"><label for="code">{{ __('Code') }}:</label> <span class="text-danger"></span></p>
                            <div class="input-group input-group-lg mb-3">
                                <input type="text" name="code" id="code" class="form-control rounded" aria-label="Large" placeholder="{{__('Location Code')}}" aria-describedby="inputGroup-sizing-sm" required>

                            </div>
                        </div>
                        <div class="col-md-12">
                            <p class="mb-1 font-weight-bold"><label for="location">{{ __('Location') }}:</label> <span class="text-danger"></span></p>
                            <div class="form-group mb-3">
                                <textarea name="location" id="location" class="form-control" rows="3" placeholder="{{ __('Location') }}" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p class="mb-1 font-weight-bold"><label for="address">{{ __('Address') }}:</label> <span class="text-danger"></span></p>
                            <div class="form-group mb-3">
                                <textarea name="address" id="address" class="form-control" rows="3" placeholder="{{ __('Location address') }}" required></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded" data-dismiss="modal">{{ __('Close') }}</button>
                <button type="button" class="btn btn-danger rounded" id="warehouseFormSubmit">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- END Modal ------------------------------------------------------------------------->
@endsection

@section('page-script')
<script>
    (function ($) {
        "use script";
        $('[data-toggle="tooltip"]').tooltip();
        const form = document.getElementById('warehouseForm');
        const tableContainer = document.getElementById('dataTable').querySelector('tbody');

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

        $.ajax({
            type: 'get',
            url: '/pms/warehouse/create',
            success:function (data) {
                if(!data.status){
                    showAlert('error', data.info);
                    return;
                }
                insertDataIntoTable(data.info);
            },
            error: function(xhr, status, error) {
                showAlert(status, error)
            }
        });

        $('#addWarehouseBtn').on('click', function () {
            $('#warehouseAddModal').modal('show');
            form.setAttribute('data-type', 'post');
        });

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

        const deleteData = () => {
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
        };

        const editData = () => {
            $('.editBtn').on('click', function () {
                const url = $(this).attr('data-src');
                $.ajax({
                    type: 'get',
                    url: url,
                    success:function (data) {
                        if(!data.status){
                            showAlert('error', data.info);
                            return;
                        }
                        form.parentElement.parentElement.querySelector('#warehouseAddModalLabel').innerHTML = `Edit Location (${data.info.name})`;
                        form.reset();
                        $('#warehouseAddModal').modal('show');
                        form.setAttribute('data-type', 'put');
                        form.setAttribute('data-src', url);
                        form.querySelector('#code').value = data.info.code;
                        form.querySelector('#name').value = data.info.name;
                        form.querySelector('#phone').value = data.info.phone;
                        form.querySelector('#email').value = data.info.email;
                        form.querySelector('#location').value = data.info.location;
                        form.querySelector('#address').value = data.info.address;
                    }
                })
            })
        };

        const rowOption = (id) => {
                //edit btn
                let colOption = document.createElement('td');
                colOption.className = 'text-center';
                let editBtn = document.createElement('button');
                editBtn.className = 'btn btn-info btn-circle mx-1 editBtn';
                editBtn.setAttribute('data-src', `/pms/warehouse/${id}`);
                let editIcn = document.createElement('i');
                editIcn.className = 'las la-edit';
                editIcn.innerText = 'Edit';
                editBtn.appendChild(editIcn);
                colOption.appendChild(editBtn);
                //delete btn
                let deleteBtn = document.createElement('button');
                deleteBtn.className = 'btn btn-danger btn-circle mx-1 deleteBtn';
                deleteBtn.setAttribute('data-src', `/pms/warehouse/${id}`);
                let deleteIcn = document.createElement('i');
                deleteIcn.className = 'las la-trash';
                deleteIcn.innerText = 'Delete';
                deleteBtn.appendChild(deleteIcn);
                colOption.appendChild(deleteBtn);

                return colOption;
            };

            const insertDataIntoTable = (data) => {
                $.map(data, (item, key) => {
                    const row = document.createElement('tr');
                    row.id = `set${item.id}`;
                    let colSl = document.createElement('td');
                    colSl.innerHTML = tableContainer.querySelectorAll('tr').length +1;
                    let colCode = document.createElement('td');
                    colCode.innerHTML = item.code;
                    let colName = document.createElement('td');
                    colName.innerHTML = item.name;
                    let colPhone = document.createElement('td');
                    colPhone.innerHTML = item.phone;
                    let colEmail = document.createElement('td');
                    colEmail.innerHTML = item.email;
                    let colLocation = document.createElement('td');
                    colLocation.innerHTML = item.location;


                    row.appendChild(colSl);
                    row.appendChild(colCode);
                    row.appendChild(colName);
                    row.appendChild(colPhone);
                    row.appendChild(colEmail);
                    row.appendChild(colLocation);
                    row.appendChild(rowOption(item.id));
                    tableContainer.appendChild(row);
                });
                deleteData();
                editData();
                showEmptyTable()
            };

            const warehouseStore = (inputs) => {
                $.ajax({
                    type: 'post',
                    url: '/pms/warehouse',
                    data: {
                        name: inputs.name,
                        email: inputs.email,
                        phone: inputs.phone,
                        code: inputs.code,
                        location: inputs.location,
                        address: inputs.address,
                    },
                    success:function (data) {
                        if(!data.status){
                            showAlert('error', data.info);
                            return;
                        }
                        insertDataIntoTable(data.info);
                        form.reset();
                        $('#warehouseAddModal').modal('hide');
                    },
                    error: function(xhr, status, error) {
                        showAlert(status, error)
                    }
                })
            };

            const warehouseUpdate = (inputs, src) => {
                $.ajax({
                    type: 'put',
                    url: src,
                    data: {
                        name: inputs.name,
                        email: inputs.email,
                        phone: inputs.phone,
                        code: inputs.code,
                        location: inputs.location,
                        address: inputs.address,
                    },
                    success:function (data) {
                        console.log(data.info);
                        if(!data.status){
                            showAlert('error', data.info);
                            return;
                        }
                        // insertDataIntoTable(data.info);
                        form.reset();
                        $('#warehouseAddModal').modal('hide');
                        const row = tableContainer.querySelector(`#set${data.info.id}`);
                        const rowCol = row.querySelectorAll('td');
                        rowCol[1].innerText = data.info.code;
                        rowCol[2].innerText = data.info.name;
                        rowCol[3].innerText = data.info.phone;
                        rowCol[4].innerText = data.info.email;
                        rowCol[5].innerText = data.info.location;
                    },
                    error: function(xhr, status, error) {
                        showAlert(status, error)
                    }
                })
            };

            $('#warehouseFormSubmit').on('click', function () {
                // let form = $(this).parent().parent().find('.modal-body form')[0];
                let inputs = form.querySelectorAll('input');
                let textarea = form.querySelectorAll('textarea');
                let data = [];
                let requiredInputs = 0;
                $.map(inputs, function (item, key) {
                    if(item.hasAttribute('required') && !item.value){
                        item.parentElement.parentElement.querySelector('.text-danger').classList.add('text-capitalize');
                        item.parentElement.parentElement.querySelector('.text-danger').innerHTML = item.getAttribute('name')+' field is required';
                    } else{
                        item.parentElement.parentElement.querySelector('.text-danger').innerHTML = '';
                        data[item.getAttribute('name')] = item.value
                    }

                    if(item.hasAttribute('required')){
                        requiredInputs++;
                    }
                });
                $.map(textarea, function (item, key) {
                    if(item.hasAttribute('required') && !item.value){
                        item.parentElement.parentElement.querySelector('.text-danger').classList.add('text-capitalize');
                        item.parentElement.parentElement.querySelector('.text-danger').innerHTML = item.getAttribute('name')+' field is required';
                    } else{
                        item.parentElement.parentElement.querySelector('.text-danger').innerHTML = '';
                        data[item.getAttribute('name')] = item.value
                    }

                    if(item.hasAttribute('required')){
                        requiredInputs++;
                    }
                });

                if(Object.keys(data).length === requiredInputs && form.getAttribute('data-type') === 'post'){
                    warehouseStore(data)
                }else if(Object.keys(data).length === requiredInputs && form.getAttribute('data-type') === 'put'){
                    warehouseUpdate(data, form.getAttribute('data-src'));
                }
            });
        })(jQuery)
    </script>
    @endsection
