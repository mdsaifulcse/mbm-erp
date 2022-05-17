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
                <a href="javascript:void(0)" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add Brand" id="addBrandBtn"> <i class="las la-plus"></i>Add</i></a>

                <a href="javascript:void(0)" class="btn btn-sm btn-info text-white" data-toggle="tooltip" title="Upload Brand Using Xls Sheet" id="uploadFile"> <i class="las la-cloud-upload-alt"></i>Upload Xls File</i></a>
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
                                <th>{{__('Image')}}</th>
                                <th>{{__('Code')}}</th>
                                <th>{{__('Name')}}</th>
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
<div class="modal fade bd-example-modal-lg" id="brandAddModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="brandAddModalLabel">{{ __('Add New Brand') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="brandForm"  enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <p class="mb-1 font-weight-bold"><label for="code">{{ __('Code') }}:</label> <span class="text-danger"></span></p>
                                <div class="input-group input-group-lg mb-3">
                                    <input type="text" name="code" id="code" class="form-control rounded" aria-label="Large" placeholder="{{__('Brand Code')}}" aria-describedby="inputGroup-sizing-sm" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <p class="mb-1 font-weight-bold"><label for="name">{{ __('Name') }}:</label> <span class="text-danger"></span></p>
                                <div class="input-group input-group-lg mb-3">
                                    <input type="text" name="name" id="name" class="form-control rounded" aria-label="Large" placeholder="{{__('Brand Name')}}" aria-describedby="inputGroup-sizing-sm" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 font-weight-bold"><label for="code">{{ __('Image') }}:</label> <code>{{ __('Expected file size is --px x --px') }}</code> <span class="text-danger"></span></p>
                            <div class="input-group input-group-lg mb-3">
                                <input type="file" name="image" id="image" class="form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" onChange="img_pathUrl(this);">
                            </div>
                            <div class="col-12 text-center">
                                <img src="" alt="" id="modalImageShow" class="img-fluid img-thumbnail w-50" accept="image/png, image/jpeg"/>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded" data-dismiss="modal">{{ __('Close') }}</button>

                <button type="button" class="btn btn-danger rounded" id="brandFormSubmit">{{ __('Save') }}</button> <!--  -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade bd-example-modal-lg" id="brandUploadModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="brandAddModalLabel">{{ __('Upload Brand') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="brandForm"  enctype="multipart/form-data" action="{{route('pms.product-management.brand.import')}}" method="POST">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-row">
                        <div class="col-md-12 pb-4">
                            <a href="{{URL::to('upload/excel/Brand.xlsx')}}" download class="btn btn-link"><i class="las la-download"></i>{{__('Click Here To Download Format File')}}</a>
                        </div>
                      <div class="col-md-8">
                        <p class="mb-1 font-weight-bold"><label for="code">{{ __('Select File for Upload') }}:</label> <code>{{ __('Expected file size is .xls , .xslx') }}</code> <span class="text-danger"></span></p>
                        <div class="input-group input-group-lg mb-3">
                            <input type="file" name="brand_file" id="brand_file" class="form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm">
                        </div>
                    </div>
                    <div class="col-4">

                        <button type="submit" class="btn btn-sm btn-success text-white" style="margin-top:32px"><i class="las la-cloud-upload-alt"></i>Upload Xls File</i></button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded" data-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- END Modal ------------------------------------------------------------------------->

@endsection

@section('page-script')
<script>
    function img_pathUrl(input){
        $('#modalImageShow')[0].src = (window.URL ? URL : webkitURL).createObjectURL(input.files[0]);
    }
    (function ($) {
        "use script";
        $('[data-toggle="tooltip"]').tooltip();
        const form = document.getElementById('brandForm');
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
            url: '/pms/product-management/brand/create',
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

        $('#addBrandBtn').on('click', function () {
            $('#brandAddModal').modal('show');
            form.setAttribute('data-type', 'post');
        });

        $('#uploadFile').on('click', function () {
            $('#brandUploadModal').modal('show');
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
                        form.parentElement.parentElement.querySelector('#brandAddModalLabel').innerHTML = `Edit Brand (${data.info.name})`;
                        form.reset();
                        $('#brandAddModal').modal('show');
                        form.setAttribute('data-type', 'put');
                        form.setAttribute('data-src', url);
                        form.querySelector('#code').value = data.info.code;
                        form.querySelector('#name').value = data.info.name;
                        form.querySelector('#modalImageShow').src = data.info.image;
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
                editBtn.setAttribute('data-src', `/pms/product-management/brand/${id}`);
                let editIcn = document.createElement('i');
                editIcn.className = 'las la-edit';
                editIcn.innerText = 'Edit';
                editBtn.appendChild(editIcn);
                colOption.appendChild(editBtn);
                //delete btn
                let deleteBtn = document.createElement('button');
                deleteBtn.className = 'btn btn-danger btn-circle mx-1 deleteBtn';
                deleteBtn.setAttribute('data-src', `/pms/product-management/brand/${id}`);
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
                    colSl.innerHTML = tableContainer.querySelectorAll('tr').length + 1;
                    let colImg = document.createElement('td');
                    colImg.setAttribute('width', '15%');
                    if (item.image) {

                    colImg.innerHTML = `<img class="img-fluid img-thumbnail w-50" src="${item.image}" alt="${item.name}">`;
                }else{
                    colImg.innerHTML = '';
                }

                    let colCode = document.createElement('td');
                    colCode.innerHTML = item.code;
                    let colName = document.createElement('td');
                    colName.innerHTML = item.name;


                    row.appendChild(colSl);
                    row.appendChild(colImg);
                    row.appendChild(colCode);
                    row.appendChild(colName);
                    row.appendChild(rowOption(item.id));
                    tableContainer.appendChild(row);
                });
                deleteData();
                editData();
                showEmptyTable()
            };

            const brandStore = (inputs) => {
                let fd = new FormData();
                fd.append('image',inputs.image);
                fd.append('name', inputs.name);
                fd.append('code', inputs.code);
                $.ajax({
                    type: 'post',
                    url: '/pms/product-management/brand',
                    data: fd,
                    processData: false,
                    contentType: false,
                    success:function (data) {
                        if(!data.status){
                            showAlert('error', data.info);
                            return;
                        }
                        insertDataIntoTable(data.info);
                        form.reset();
                        $('#brandAddModal').modal('hide');
                    },
                    error: function(xhr, status, error) {
                        showAlert(status, error)
                    }
                })
            };

            const brandUpdate = (inputs, src) => {
                
                if(inputs.image){
                    let fd = new FormData();
                    fd.append('image',inputs.image);
                    fd.append('name', inputs.name);
                    fd.append('code', inputs.code);
                    $.ajax({
                        type: 'post',
                        url: '/pms/product-management/brand',
                        data: fd,
                        processData: false,
                        contentType: false,
                        success:function (data) {
                            if(!data.status){
                                showAlert('error', data.info);
                                return;
                            }
                            insertDataIntoTable(data.info);
                            form.reset();
                            $('#brandAddModal').modal('hide');
                        },
                        error: function(xhr, status, error) {
                            showAlert(status, error)
                        }
                    })
                    
                }else{
                    $.ajax({
                        type: 'put',
                        url: src,
                        data: {
                            name: inputs.name,
                            code: inputs.code
                        },
                        success:function (data) {
                            if(!data.status){
                                showAlert('error', data.info);
                                return;
                            }
                            form.reset();
                            $('#brandAddModal').modal('hide');
                            const row = tableContainer.querySelector(`#set${data.info.id}`);
                            const rowCol = row.querySelectorAll('td');
                            rowCol[1].querySelector('img').src = data.info.image;
                            rowCol[2].innerText = data.info.code;
                            rowCol[3].innerText = data.info.name;
                        },
                        error: function(xhr, status, error) {
                            showAlert(status, error)
                        }
                    });
                }


            };

            $('#brandFormSubmit').on('click', function () {
                let inputs = form.querySelectorAll('input');
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

                    if(item.getAttribute('type') === 'file'){
                        data[item.getAttribute('name')] = item.files[0];
                    } else{
                        data[item.getAttribute('name')] = item.value
                    }
                    if(item.hasAttribute('required')){
                        requiredInputs++;
                    }
                });

                if((Object.keys(data).length - 1) === requiredInputs && form.getAttribute('data-type') === 'post'){
                    brandStore(data)
                }else if((Object.keys(data).length - 1) === requiredInputs && form.getAttribute('data-type') === 'put'){
                    brandUpdate(data, form.getAttribute('data-src'));
                }
            });

            // form.addEventListener('submit', function (e) {
            //     e.preventDefault();
            //     let formData = new FormData(this);
            //     $('#image').text('');
            //     console.log(formData);
            // })

        })(jQuery)
    </script>
    @endsection
