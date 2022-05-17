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
                  <a href="{{ route('pms.range-setup.store') }}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add Approval Range Setup" id="addApprovalRangeSetupBtn"> <i class="las la-plus">Add</i></a>
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
                                <th>{{__('From')}}</th>
                                <th>{{__('To')}}</th>
                                <th>{{__('User')}}</th>
                                <th class="text-center">{{__('Option')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($ranges as $key => $range)
                            <tr>
                                <th>{{ $key+1 }}</th>
                                <td>{{ $range->min_amount }}</td>
                                <td>{{ $range->max_amount }}</td>
                                <td>
                                    @foreach($range->relUsers as $rangeUser)
                                        <span class="badge">{{ $rangeUser->name }}</span>
                                    @endforeach
                                </td>
                                <<td class="text-center">
                                    <a href="javascript:void(0)" data-role="put" data-src="{{ route('pms.range-setup.show', $range->id) }}" class="btn btn-info m-1  editBtn"><i class="las la-edit">Edit</i></a>
                                    <a href="javascript:void(0)" data-role="delete" data-src="{{ route('pms.range-setup.destroy', $range->id) }}" class="btn btn-danger m-1  deleteBtn"><i class="las la-trash">Delete</i>
                                        <form action="" method="post">
                                            @csrf
                                            @method('delete')
                                        </form>
                                    </a>
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
<!-- Modal -->
<div class="modal fade" id="ApprovalRangeModal" tabindex="-1" role="dialog" aria-labelledby="ApprovalRangeModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ApprovalRangeModalLongTitle">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    @csrf
                    <p class="mb-1 font-weight-bold"><label for="minAmount">{{ __('From Amount') }}:</label> {!! $errors->has('min_amount')? '<span class="text-danger text-capitalize">'. $errors->first('min_amount').'</span>':'' !!}</p>
                    <div class="input-group input-group-lg mb-3 d-">
                        <input type="number" name="min_amount" id="minAmount" class="form-control rounded" aria-label="Large" placeholder="{{__('Start from ')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('min_amount') }}">

                    </div>

                    <p class="mb-1 font-weight-bold"><label for="maxAmount">{{ __('To Amount') }}:</label> {!! $errors->has('max_amount')? '<span class="text-danger text-capitalize">'. $errors->first('max_amount').'</span>':'' !!}</p>
                    <div class="input-group input-group-lg mb-3 d-">
                        <input type="number" name="max_amount" id="maxAmount" class="form-control rounded" aria-label="Large" placeholder="{{__('End to')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('max_amount') }}">

                    </div>

                    <p class="mb-1 font-weight-bold"><label for="role">{{ __('Roles') }}:</label> {!! $errors->has('role')? '<span class="text-danger text-capitalize">'. $errors->first('role').'</span>':'' !!}</p>
                    <div class="select-search-group input-group input-group-lg mb-3 d-">
                        <select name="role" id="role" class="form-control" required>
                                <option>{{ __('Select One') }}</option>
                            @foreach($roles as $role)
                                <option>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <p class="mb-1 font-weight-bold"><label for="users">{{ __('Users') }}:</label> {!! $errors->has('users')? '<span class="text-danger text-capitalize">'. $errors->first('users').'</span>':'' !!}</p>
                    <div class="select-search-group input-group input-group-lg mb-3 d-">
                        <select name="users[]" id="users" class="form-control" required multiple>

                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary submitBtn">Save</button>
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
            $('#addApprovalRangeSetupBtn').on('click', (e)=>{
                e.preventDefault();
                resetForm();
                $('#ApprovalRangeModal').find('#ApprovalRangeModalLongTitle').html('Add Approval Range');
                $('#ApprovalRangeModal').modal('show');
                $('#ApprovalRangeModal').find('form').attr('action',$('#addApprovalRangeSetupBtn').attr('href'));
                $('#ApprovalRangeModal').find('.submitBtn').on('click', ()=>{
                    $('#ApprovalRangeModal').find('form').submit();
                });
                $('#ApprovalRangeModal').find('#role').on('change',()=>{
                    allusers();
                })
            });



            $('.editBtn').on('click', function (e) {
                e.preventDefault();
                $.ajax({
                    type: 'get',
                    url: $(this).attr('data-src'),
                    success:function (data) {
                        let mBody = $('#ApprovalRangeModal').find('form').parent();
                        mBody[0].innerHTML = data;
                        // allusers(mBody.find('#users').attr('data-role').split(","));
                        $('#users').select2();
                        $('#ApprovalRangeModal').find('#role').on('change',()=>{
                            allusers();
                        });
                        $('#ApprovalRangeModal').find('.submitBtn').on('click', ()=>{
                            $('#ApprovalRangeModal').find('form').submit();
                        });
                        $('#ApprovalRangeModal').modal('show');
                    }
                })
            });

            const resetForm =()=>{
                let minAmount = document.getElementById('minAmount').value = null;
                let maxAmount = document.getElementById('maxAmount').value = null;
                let role = document.getElementById('role').value = null;
               $('#users').select2('val',['']);
            };

            const allusers = () => {
                $.ajax({
                    type: 'get',
                    url: '/pms/range-setup/create',
                    data:{
                        name:$('#ApprovalRangeModal').find('#role').val()
                    },
                    success:function (data) {
                        $('#ApprovalRangeModal').find('#users').empty();
                        Array.from(data).map((item, key)=>{
                            $('#ApprovalRangeModal').find('#users').append(`<option value="${item.id}">${item.name}</option>`)
                        });
                    }
                });
            }

            $('.deleteBtn').on('click', function (){
                $(this).find('form').attr('action',$(this).attr('data-src')).submit();
            })

        })(jQuery);
    </script>
@endsection
