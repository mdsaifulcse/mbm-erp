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
                <a href="javascript:void(0)" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add Unit" id="addProductUnitBtn"> <i class="las la-plus">Add</i></a>
            </li>
        </ul><!-- /.breadcrumb -->

    </div>

    <div class="page-content">
        <div class="">
            <div class="panel panel-info">
                <div class="panel-body">
                    <table  id="dataTable" class="table table-striped table-bordered table-head">
                        <thead>
                            <tr>
                                <th width="5%">{{__('SL No.')}}</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Code')}}</th>
                                <th>{{__('Status')}}</th>
                                
                                <th class="text-center">{{__('Option')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($product_units))
                            @foreach($product_units as $key => $unit)
                            <tr>
                                <th>{{ $key + 1  }}</th>
                                <td>{{ $unit->unit_name }}</td>
                                <td>{{ $unit->unit_code }}</td>
                                <td>{{ ucfirst($unit->status) }}</td>
                                
                                <td class="text-center">
                                    <a href="javascript:void(0)" data-role="put" data-src="{{ route('pms.product-management.product-unit.show', $unit->id) }}" class="btn btn-info m-1 rounded-circle editBtn"><i class="las la-edit"></i></a>
                                    <a href="javascript:void(0)" data-role="delete" data-src="{{ route('pms.product-management.product-unit.destroy', $unit->id) }}" class="btn btn-danger m-1 rounded-circle deleteBtn"><i class="las la-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            @endif
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
<div class="modal fade" id="productUnitAddModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productUnitAddModalLabel">Add New Unit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" data-src="{{ route('pms.product-management.product-unit.store') }}">
                @csrf
                @method('post')
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-6">
                            <p class="mb-1 font-weight-bold"><label for="unit_name">{{ __('Unit Name') }}:</label> {!! $errors->has('unit_name')? '<span class="text-danger text-capitalize">'. $errors->first('unit_name').'</span>':'' !!}</p>
                            <div class="input-group input-group-lg mb-3 d-">
                                <input type="text" name="unit_name" id="unit_name" class="form-control rounded" aria-label="Large" placeholder="{{__('Unit Name')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('unit_name') }}">
                            </div>
                        </div>
                        

                        <div class="col-md-6">
                            <p class="mb-1 font-weight-bold"><label for="unit_code">{{ __('Unit Code') }}:</label> {!! $errors->has('unit_code')? '<span class="text-danger text-capitalize">'. $errors->first('unit_code').'</span>':'' !!}</p>
                            <div class="input-group input-group-lg mb-3 d-">
                                <input type="text" name="unit_code" id="unit_code" class="form-control rounded" readonly aria-label="Large" placeholder="{{__('Unit Code')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('unit_code')?old('unit_code'):$unit_code }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <p class="mb-1 font-weight-bold"><label for="status">{{ __('Brand') }}:</label> 
                                {!! $errors->has('status')? '<span class="text-danger text-capitalize">'. $errors->first('status').'</span>':'' !!}</p>
                                <div class="select-search-group  input-group input-group-lg mb-3 d-">
                                   {!! Form::Select('status',$status,Request::old('status'),['id'=>'status', 'class'=>'form-control selectheighttype select2']) !!}
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

@endsection

@section('page-script')
<script>
   $('#dataTable').DataTable();
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

    $('.select2').select2();

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
        $('#productUnitAddModal').modal('show');
        let url = $('#productUnitAddModal').find('form').attr('data-src');
        $('#productUnitAddModal').find('form').attr('action', url);
    };

    $('#addProductUnitBtn').on('click', function () {
        $('#productUnitAddModal').find('form')[0].reset();
        $('#productUnitAddModal').find('#productUnitAddModalLabel').html(`Add new Unit`);
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
                $('#productUnitAddModal').find('form')[0].reset();
                $('#productUnitAddModal').find('form').attr('data-src', data.info.src);
                $('#productUnitAddModal').find('form').find('input[name="_method"]').val(data.info.req_type);

                $('#productUnitAddModal').find('form').find('input[name="unit_name"]').val(data.info.unit_name);
                $('#productUnitAddModal').find('form').find('input[name="unit_code"]').val(data.info.unit_code);

                if(data.info.status)
                {
                    $('#status').select2('val',[data.info.status]);
                }
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
