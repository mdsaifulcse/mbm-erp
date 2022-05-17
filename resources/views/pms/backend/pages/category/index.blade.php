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
                <a href="javascript:void(0)" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add Warehouse" id="addCategoryBtn"> <i class="las la-plus"></i>Add</i></a>

                  <a href="javascript:void(0)" class="btn btn-sm btn-info text-white" data-toggle="tooltip" title="Upload Category by xlsx file" id="uploadFile"> <i class="las la-cloud-upload-alt"></i>Upload Category</a>
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
                                <th>{{__('Sub-category')}}</th>
                                <th>{{__('Departments')}}</th>
                                <th>{{__('Requisition Type')}}</th>
                                <th class="text-center">{{__('Option')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $key => $category)
                            @if(!$category->parent_id)
                            <tr id="set{{ $category->id }}">
                                <td width="5%">{{__($key+1)}}</td>
                                <td>{{ $category->code }}</td>
                                <td>{{ $category->name }}</td>

                                <td>
                                    @foreach($category->subCategory as $subCategory)
                                    <a href="javascript:void(0)" data-role="put" data-src="{{ route('pms.product-management.category.show', $subCategory->id) }}" class="editBtn"><span class="m-1 badge badge-primary">{{ $subCategory->name }}</span></a>

                                    @endforeach
                                </td>

                                <td>
                                    @foreach($category->departmentsList as $values)
                                    <a href="javascript:void(0)"><span class="m-1 badge badge-primary">{{ $values->department->hr_department_name }}</span></a>

                                    @endforeach
                                </td>

                                <td>{{ $category->requisitionType?$category->requisitionType->name:''}}</td>

                                <td class="text-center">
                                    <a href="javascript:void(0)" data-role="put" data-src="{{ route('pms.product-management.category.show', $category->id) }}" class="btn btn-info m-1  editBtn"><i class="las la-edit">Edit</i></a>
                                    <a href="javascript:void(0)" data-role="delete" data-src="{{ route('pms.product-management.category.destroy', $category->id) }}" class="btn btn-danger m-1  deleteBtn"><i class="las la-trash">Delete</i></a>
                                </td>
                            </tr>
                            @endif
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
<div class="modal fade" id="categoryAddModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryAddModalLabel">Add New Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" data-src="{{ route('pms.product-management.category.store') }}">
                @csrf
                @method('post')
                <div class="modal-body">
                    <p class="mb-1 font-weight-bold"><label for="code">{{ __('Code') }}:</label> {!! $errors->has('code')? '<span class="text-danger text-capitalize">'. $errors->first('code').'</span>':'' !!}</p>
                    <div class="input-group input-group-lg mb-3 d-">
                        <input type="text" name="code" id="code" class="form-control rounded" aria-label="Large" placeholder="{{__('Category Code')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('code') }}">

                    </div>

                    <p class="mb-1 font-weight-bold"><label for="name">{{ __('Name') }}:</label> {!! $errors->has('name')? '<span class="text-danger text-capitalize">'. $errors->first('name').'</span>':'' !!}</p>
                    <div class="input-group input-group-lg mb-3 d-">
                        <input type="text" name="name" id="name" class="form-control rounded" aria-label="Large" placeholder="{{__('Category Name')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('name') }}">

                    </div>

                    @if($categories->count() > 0)
                    <p class="mb-1 font-weight-bold"><label for="parent">{{ __('Parent') }}:</label> {!! $errors->has('parent')? '<span class="text-danger text-capitalize">'. $errors->first('parent').'</span>':'' !!}</p>
                    <div class="select-search-group input-group input-group-lg mb-3 d-">
                        <select name="parent_id" id="parent" class="form-control">
                            <option value="{{ null }}">{{ __('Select One') }}</option>
                            @foreach($categories as $key => $category)
                            @if(!$category->parent_id)
                            <option value="{{ $category->id }}">{{ $category->name.' ('.$category->code.' )' }}</option>
                            @endif
                            @endforeach
                        </select>

                    </div>
                    @endif
                    @if($requisitions->count() > 0)
                    <p class="mb-1 font-weight-bold"><label for="parent">{{ __('Requisition Type') }}:</label> {!! $errors->has('requisition_type_id')? '<span class="text-danger text-capitalize">'. $errors->first('requisition_type_id').'</span>':'' !!}</p>
                    <div class="select-search-group input-group input-group-lg mb-3 d-">
                        <select name="requisition_type_id" id="requisition_type_id" class="form-control" required>
                            <option value="{{ null }}">{{ __('Select One') }}</option>
                            @foreach($requisitions as $key => $requisition)
                            @if(!$requisition->parent_id)
                            <option value="{{ $requisition->id }}">{{ $requisition->name}}</option>
                            @endif
                            @endforeach
                        </select>

                    </div>
                    @endif


                     @if($departments->count() > 0)
                    <p class="mb-1 font-weight-bold"><label for="parent">{{ __('Departments') }}:</label> {!! $errors->has('hr_department_id')? '<span class="text-danger text-capitalize">'. $errors->first('hr_department_id').'</span>':'' !!}</p>
                    <div class="select-search-group input-group input-group-lg mb-3 d-">
                        <select name="hr_department_id[]" id="hr_department_id" class="form-control select2" multiple>
                            <option value="{{ null }}">{{ __('Select One') }}</option>
                            @foreach($departments as $key => $department)
                            
                            <option value="{{ $department->hr_department_id }}">{{ $department->hr_department_name}}</option>
                            
                            @endforeach
                        </select>

                    </div>
                    @endif
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

    <!--Upload Category Modal Start-->
<div class="modal fade bd-example-modal-lg" id="categoryUploadModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="brandAddModalLabel">{{ __('Upload Category') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>
            <form id="brandForm"  enctype="multipart/form-data" action="{{route('pms.product-management.category.import')}}" method="POST">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-row">
                        <div class="col-md-12 pb-4">
                            <a href="{{URL::to('upload/excel/categories-sample.xlsx')}}" download class="btn btn-link"><i class="las la-download"></i>{{__('Click Here To Download Format File')}}</a>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 font-weight-bold"><label for="code">{{ __('Select File for Upload') }}:</label> <code>{{ __('Expected file size is .xls , .xslx') }}</code> <span class="text-danger"></span></p>
                            <div class="input-group input-group-lg mb-3">
                                <input type="file" name="category_file" class="form-control" required id="excelFile" placeholder="Browse Excel file"
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
    <!--Upload Category Modal End-->
@endsection

@section('page-script')
<script>
    (function ($) {
        "use script";

        $('#uploadFile').on('click', function () {
            $('#categoryUploadModal').modal('show');
        });

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

        const modalShow = () => {
            $('#categoryAddModal').modal('show');
            let url = $('#categoryAddModal').find('form').attr('data-src');
            $('#categoryAddModal').find('form').attr('action', url);
        };

        $('#addCategoryBtn').on('click', function () {
            $('#categoryAddModal').find('form')[0].reset();
            $('#categoryAddModal').find('#categoryAddModalLabel').html(`Add new category`);
            modalShow()
        });

        $('.editBtn').on('click', function () {
            $.ajax({
                type: 'get',
                url: $(this).attr('data-src'),
                success:function (data) {
                    if(!data.status){
                        showAlert('error', data.info);
                        return;
                    }
                    $('#categoryAddModal').find('form')[0].reset();
                    $('#categoryAddModal').find('form').attr('data-src', data.info.src);
                        // $('#categoryAddModal').find('form').attr('method', data.info.req_type);
                        $('#categoryAddModal').find('form').find('input[name="_method"]').val(data.info.req_type);
                        $('#categoryAddModal').find('form').find('input[name="code"]').val(data.info.code);
                        $('#categoryAddModal').find('form').find('input[name="name"]').val(data.info.name);

                        if(data.info.requisition_type_id)
                        {
                            $('#requisition_type_id').select2('val',data.info.requisition_type_id);
                        }

                        if(data.departments)
                        {
                            $('#hr_department_id').select2('val',[data.departments]);
                        }

                        $('#categoryAddModal').find('#categoryAddModalLabel').html(`Edit category (${data.info.name})`);
                        if(data.info.parent_id){
                            $('#categoryAddModal').find('form').find('select[name="parent_id"]').val(data.info.parent_id.id)

                        }
                        modalShow()
                    }
                })
        })

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
