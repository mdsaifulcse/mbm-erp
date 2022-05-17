@extends('pms.backend.layouts.master-layout')

@section('title', config('app.name', 'laravel'). ' | '.$title)

@section('page-css')

@endsection

@section('main-content')
<?php 
use Illuminate\Support\Facades\Request;
?>
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
                <a href="javascript:void(0)" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add Warehouse" id="addProductBtn"> <i class="las la-plus">Add</i></a>

                <a href="javascript:void(0)" class="btn btn-sm btn-success text-white" data-toggle="tooltip" title="Upload Product Using Xls Sheet" id="uploadFile"> <i class="las la-cloud-upload-alt"></i>Upload Xls File</i></a>
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
                                <th>{{__('SKU')}}</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Brand')}}</th>
                                <th>{{__('Category')}}</th>
                                <th>{{__('Product Unit')}}</th>
                                <th>{{__('Unit Price')}}</th>
                                <th class="text-center">{{__('Option')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $key => $product)
                            <tr>
                                <th>{{  ($products->currentpage()-1) * $products->perpage() + $key + 1  }}</th>
                                <td>{{ $product->sku }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->brand->name }}</td>
                                <td>{{ $product->category->name }}</td>
                                <td>{{ isset($product->productUnit)?$product->productUnit->unit_name:'' }}</td>
                                <td>{{ number_format($product->unit_price,2) }}</td>
                                <td class="text-center">
                                    <a href="javascript:void(0)" data-role="put" data-src="{{ route('pms.product-management.product.show', $product->id) }}" class="btn btn-info m-1 rounded-circle editBtn"><i class="las la-edit"></i></a>
                                    <a href="javascript:void(0)" data-role="delete" data-src="{{ route('pms.product-management.product.destroy', $product->id) }}" class="btn btn-danger m-1 rounded-circle deleteBtn"><i class="las la-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        

                    </table>
                    <div class="text-center">
                        @if($products)
                        {{$products->links()}}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- END WRAPPER CONTENT ------------------------------------------------------------------------->
<!-- Modal ------------------------------------------------------------------------->
<div class="modal fade" id="productAddModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productAddModalLabel">Add New Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" data-src="{{ route('pms.product-management.product.store') }}">
                @csrf
                @method('post')
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-6">
                            <p class="mb-1 font-weight-bold"><label for="sku">{{ __('SKU') }}:</label> {!! $errors->has('sku')? '<span class="text-danger text-capitalize">'. $errors->first('sku').'</span>':'' !!}</p>
                            <div class="input-group input-group-lg mb-3 d-">
                                <input type="text" name="sku" id="sku" class="form-control rounded" aria-label="Large" placeholder="{{__('Product SKU')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('sku')?old('sku'):$sku }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <p class="mb-1 font-weight-bold"><label for="name">{{ __('Name') }}:</label> {!! $errors->has('name')? '<span class="text-danger text-capitalize">'. $errors->first('name').'</span>':'' !!}</p>
                            <div class="input-group input-group-lg mb-3 d-">
                                <input type="text" name="name" id="name" class="form-control rounded" aria-label="Large" placeholder="{{__('Product Name')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('name') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 font-weight-bold"><label for="category_id">{{ __('Category') }}:</label> {!! $errors->has('category_id')? '<span class="text-danger text-capitalize">'. $errors->first('category_id').'</span>':'' !!}</p>
                            <div class="select-search-group input-group input-group-lg mb-3 d-">
                                <select name="category_id" id="category_id" class="form-control rounded select2" required>
                                    <option selected disabled value="{{ null }}">{{ __('Select One') }}</option>
                                    @foreach($categories as $key => $category)
                                    @if(!$category->parent_id)
                                    
                                    <optgroup label="{{$category->name}}">
                                        @if(isset($category->subCategory[0]))
                                        @foreach ($category->subCategory as $key => $data)
                                        <option value="{{$data->id}}">{{$data->name}}</option>
                                        @endforeach
                                        @endif
                                    </optgroup>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <p class="mb-1 font-weight-bold"><label for="brand_id">{{ __('Brand') }}:</label> 
                                {!! $errors->has('brand_id')? '<span class="text-danger text-capitalize">'. $errors->first('brand_id').'</span>':'' !!}</p>
                                <div class="select-search-group  input-group input-group-lg mb-3 d-">
                                   {!! Form::Select('brand_id',$brands,Request::old('brand_id'),['id'=>'brand_id', 'class'=>'form-control selectheighttype select2']) !!}
                               </div>
                           </div>



                           <div class="col-md-6">
                            <p class="mb-1 font-weight-bold"><label for="supplier">{{ __('Supplier') }}:</label> {!! $errors->has('supplier')? '<span class="text-danger text-capitalize">'. $errors->first('supplier').'</span>':'' !!}</p>
                            <div class="select-search-group input-group input-group-lg mb-3 d-">
                                {!! Form::Select('supplier[]',$suppliers,Request::old('supplier'),['id'=>'supplier', 'class'=>'form-control rounded select2','multiple'=>'multiple','style'=>'width:100%']) !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <p class="mb-1 font-weight-bold"><label for="product_unit_id">{{ __('Product Unit') }}:</label> 
                                {!! $errors->has('product_unit_id')? '<span class="text-danger text-capitalize">'. $errors->first('product_unit_id').'</span>':'' !!}</p>
                                <div class="select-search-group  input-group input-group-lg mb-3 d-">
                                   {!! Form::Select('product_unit_id',$unit,Request::old('product_unit_id'),['id'=>'product_unit_id', 'class'=>'form-control selectheighttype select2']) !!}
                               </div>
                           </div>
                           <div class="col-md-6">
                            <p class="mb-1 font-weight-bold"><label for="unit_price">{{ __('Unit Price') }}:</label> {!! $errors->has('unit_price')? '<span class="text-danger text-capitalize">'. $errors->first('unit_price').'</span>':'' !!}</p>
                            <div class="input-group input-group-lg mb-3 d-">
                                <input type="number" name="unit_price" id="unit_price" class="form-control rounded" aria-label="Large" placeholder="{{__('Product unit price')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('unit_price') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 font-weight-bold"><label for="tax">{{ __('Tax') }}:</label> {!! $errors->has('tax')? '<span class="text-danger text-capitalize">'. $errors->first('tax').'</span>':'' !!}</p>
                            <div class="input-group input-group-lg mb-3 d-">
                                <input type="number" name="tax" id="tax" class="form-control rounded" aria-label="Large" placeholder="{{__('Product tax')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('tax') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-danger rounded" id="productFormSubmit">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END Modal ------------------------------------------------------------------------->

<div class="modal fade bd-example-modal-lg" id="brandUploadModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="brandAddModalLabel">{{ __('Upload Product') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="brandForm"  enctype="multipart/form-data" action="{{route('pms.product-management.product.import')}}" method="POST">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-row">
                        <div class="col-md-12 pb-4">
                            <a href="{{URL::to('upload/excel/Product.xlsx')}}" download class="btn btn-link"><i class="las la-download"></i>{{__('Click Here To Download Format File')}}</a>
                        </div>
                        <div class="col-md-8">
                            <p class="mb-1 font-weight-bold"><label for="code">{{ __('Select File for Upload') }}:</label> <code>{{ __('Expected file size is .xls , .xslx') }}</code> <span class="text-danger"></span></p>
                            <div class="input-group input-group-lg mb-3">
                                <input type="file" name="product_file" id="product_file" class="form-control rounded" required aria-label="Large" aria-describedby="inputGroup-sizing-sm">
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
                colEmpty.colSpan = 8;
                row.appendChild(colEmpty);
                tableContainer.appendChild(row);
            } else {
                if(tableContainer.querySelector('#emptyRow')){
                    tableContainer.querySelector('#emptyRow').remove();
                }
            }
        };
        showEmptyTable();

        $('#supplier').select2();

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
                    // if(value) form.reset();
                });
        };

        const modalShow = () => {
            $('#productAddModal').modal('show');
            let url = $('#productAddModal').find('form').attr('data-src');
            $('#productAddModal').find('form').attr('action', url);
        };

        $('#addProductBtn').on('click', function () {
            $('#productAddModal').find('form')[0].reset();
            $('#productAddModal').find('#productAddModalLabel').html(`Add new product`);
            modalShow()
        });

        $('#uploadFile').on('click', function () {
            $('#brandUploadModal').find('form')[0].reset();
            $('#brandUploadModal').modal('show');
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
                    $('#productAddModal').find('form')[0].reset();
                    $('#productAddModal').find('form').attr('data-src', data.info.src);
                    $('#productAddModal').find('form').find('input[name="_method"]').val(data.info.req_type);
                    
                    $('#productAddModal').find('form').find('input[name="sku"]').val(data.info.sku);
                    $('#productAddModal').find('form').find('input[name="name"]').val(data.info.name);

                    if(data.info.supplier)
                    {
                        $('#supplier').select2('val',[data.info.supplier]);
                    }

                    if(data.info.category_id)
                    {
                        $('#category_id').select2('val',[data.info.category_id]);
                    }

                    if(data.info.brand_id)
                    {
                        $('#brand_id').select2('val',[data.info.brand_id]);
                    }

                    if(data.info.product_unit_id)
                    {
                        $('#product_unit_id').select2('val',[data.info.product_unit_id]);
                    }

                    $('#productAddModal').find('form').find('input[name="tax"]').val(data.info.tax);
                    $('#productAddModal').find('form').find('input[name="unit_price"]').val(data.info.unit_price);
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
