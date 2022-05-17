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
                        <a href="javascript:void(0)" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add Requisition Type" id="addRequisitionTypeBtn"> <i class="las la-plus"></i>Add</i></a>
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
                                    <th>{{__('Name')}}</th>
                                    <th width="20%" class="text-center">{{__('Option')}}</th>
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
    <div class="modal fade bd-example-modal-md" id="requisitionTypeModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="requisitionAddModalLabel">Add New Requisition Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="requisitionForm">
                        <div class="form-row">
                            <div class="col-md-12">
                                <p class="mb-1 font-weight-bold"><label for="name">{{ __('Name') }}:</label> <span class="text-danger"></span></p>
                                <div class="input-group input-group-lg mb-12 d-">
                                    <input type="text" name="name" id="name" class="form-control rounded" aria-label="Large" placeholder="{{__('Rfp')}}" aria-describedby="inputGroup-sizing-sm" required>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-danger rounded" id="requisitionTypeFormSubmit">{{ __('Save') }}</button>
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
            const form = document.getElementById('requisitionForm');
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
                    if(value)form.reset();
            });
            };

            $.ajax({
                type: 'get',
                url: '/pms/requisition/type/create',
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

            $('#addRequisitionTypeBtn').on('click', function () {
                $('#requisitionTypeModal').modal('show');
                form.setAttribute('data-type', 'post');
            });

            const showEmptyTable = () => {
                if(tableContainer.querySelectorAll('tr').length === 0){
                    const row = document.createElement('tr');
                    row.id = 'emptyRow';
                    let colEmpty = document.createElement('td');
                    colEmpty.innerHTML = 'No data is available';
                    colEmpty.className = 'text-center';
                    colEmpty.colSpan = 3;
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
                            form.parentElement.parentElement.querySelector('#requisitionAddModalLabel').innerHTML = `Edit Requisition Type (${data.info.name})`;
                            form.reset();
                            $('#requisitionTypeModal').modal('show');
                            form.setAttribute('data-type', 'put');
                            form.setAttribute('data-src', url);
                            form.querySelector('#name').value = data.info.name;

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
                editBtn.setAttribute('data-src', `/pms/requisition/type/${id}`);
                let editIcn = document.createElement('i');
                editIcn.className = 'las la-edit';
                editIcn.innerText = 'Edit';
                editBtn.appendChild(editIcn);
                colOption.appendChild(editBtn);
                //delete btn
                let deleteBtn = document.createElement('button');
                deleteBtn.className = 'btn btn-danger btn-circle mx-1 deleteBtn';
                deleteBtn.setAttribute('data-src', `/pms/requisition/type/${id}`);
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
                let colName = document.createElement('td');
                colName.innerHTML = item.name;

                row.appendChild(colSl);
                row.appendChild(colName);
                row.appendChild(rowOption(item.id));
                tableContainer.appendChild(row);
            });
                deleteData();
                editData();
                showEmptyTable()
            };

            const requisitionTypeStore = (inputs) => {
                $.ajax({
                    type: 'post',
                    route:  '/pms/requisition/type',
                    data: {
                        name: inputs.name
                    },
                    success:function (data) {
                        if(!data.status){
                            showAlert('error', data.info);
                            return;
                        }
                        insertDataIntoTable(data.info);
                        form.reset();
                        $('#requisitionTypeModal').modal('hide');
                    },
                    error: function(xhr, status, error) {
                        showAlert(status, error)
                    }
                })
            };

            const requisitionTypeUpdate = (inputs, src) => {
                $.ajax({
                    type: 'put',
                    url: src,
                    data: {
                        name: inputs.name,
                    },
                    success:function (data) {
                        console.log(data.info);
                        if(!data.status){
                            showAlert('error', data.info);
                            return;
                        }
                        // insertDataIntoTable(data.info);
                        form.reset();
                        $('#requisitionTypeModal').modal('hide');
                        const row = tableContainer.querySelector(`#set${data.info.id}`);
                        const rowCol = row.querySelectorAll('td');
                        rowCol[1].innerText = data.info.name;
                    },
                    error: function(xhr, status, error) {
                        showAlert(status, error)
                    }
                })
            };

            $('#requisitionTypeFormSubmit').on('click', function () {
                // let form = $(this).parent().parent().find('.modal-body form')[0];
                let inputs = form.querySelectorAll('input');

                //let textarea = form.querySelectorAll('textarea');
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

                if(Object.keys(data).length === requiredInputs && form.getAttribute('data-type') === 'post'){
                    requisitionTypeStore(data)
                }else if(Object.keys(data).length === requiredInputs && form.getAttribute('data-type') === 'put'){
                    requisitionTypeUpdate(data, form.getAttribute('data-src'));
                }
            });
        })(jQuery)
    </script>
@endsection