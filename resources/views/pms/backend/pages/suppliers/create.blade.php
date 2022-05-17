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
                  <a href="{{route('pms.supplier.index')}}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Supplier List"> <i class="las la-list"></i>List</a>
            </li>
        </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="">
            <div class="panel panel-info">
                <div class="panel-body">
                    <form method="post" action="{{ route('pms.supplier.store') }}">
                        {{ csrf_field() }}
                        <div class="form-row">

                            <div class="col-md-6">
                                <p class="mb-1 font-weight-bold"><label for="name">{{ __('Name') }}:</label> {!! $errors->has('name')? '<span class="text-danger text-capitalize">'. $errors->first('name').'</span>':'' !!}</p>
                                <div class="input-group input-group-lg mb-3 d-">
                                    <input type="text" name="name" id="name" class="form-control rounded" aria-label="Large" placeholder="{{__('Supplier Name')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('name') }}">
                                </div>


                                <p class="mb-1 font-weight-bold"><label for="phone">{{ __('Phone') }}:</label> {!! $errors->has('phone')? '<span class="text-danger text-capitalize">'. $errors->first('phone').'</span>':'' !!}</p>
                                <div class="input-group input-group-lg mb-3 d-">
                                    <input type="tel" name="phone" id="phone" class="form-control rounded" aria-label="Large" placeholder="{{__('Supplier Phone')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('phone') }}">

                                </div>

                                <p class="mb-1 font-weight-bold"><label for="mobile_no">{{ __('Mobile No') }}:</label> {!! $errors->has('mobile_no')? '<span class="text-danger text-capitalize">'. $errors->first('mobile_no').'</span>':'' !!}</p>
                                <div class="input-group input-group-lg mb-3 d-">
                                    <input type="tel" name="mobile_no" id="mobile_no" class="form-control rounded" aria-label="Large" placeholder="{{__('Ex: 88017********')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('mobile_no') }}">

                                </div>


                                <p class="mb-1 font-weight-bold"><label for="email">{{ __('Email') }}:</label> {!! $errors->has('email')? '<span class="text-danger text-capitalize">'. $errors->first('email').'</span>':'' !!}</p>
                                <div class="input-group input-group-lg mb-3 d-">
                                    <input type="email" name="email" id="email" class="form-control rounded" aria-label="Large" placeholder="{{__('Supplier Email')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('email') }}">

                                </div>

                                <p class="mb-1 font-weight-bold"><label for="term_condition">{{ __('Term&Condition') }}:</label> {!! $errors->has('term_condition')? '<span class="text-danger text-capitalize">'. $errors->first('term_condition').'</span>':'' !!}</p>
                                <div class="form-group form-group-lg mb-3 d-">
                                    <textarea name="term_condition" id="term_condition" class="form-control rounded" rows="3" placeholder="{{__('Term & Condition Here')}}">{!! old('term_condition') !!}</textarea>

                                </div>

                            </div> <!-- end col-6 -->


                            <div class="col-md-6">
                                <p class="mb-1 font-weight-bold"><label for="zipcode">{{ __('Zip Code') }}:</label> {!! $errors->has('zipcode')? '<span class="text-danger text-capitalize">'. $errors->first('zipcode').'</span>':'' !!}</p>
                                <div class="input-group input-group-lg mb-3 d-">
                                    <input type="text" name="zipcode" id="zipcode" class="form-control rounded" aria-label="Large" placeholder="{{__('Supplier zipcode')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('zipcode') }}">
                                </div>

                                <p class="mb-1 font-weight-bold"><label for="city">{{ __('City') }}:</label> {!! $errors->has('city')? '<span class="text-danger text-capitalize">'. $errors->first('city').'</span>':'' !!}</p>
                                <div class="input-group input-group-lg mb-3 d-">
                                    <input type="text" name="city" id="city" class="form-control rounded" aria-label="Large" placeholder="{{__('Supplier City')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('city') }}">
                                </div>

                                <p class="mb-1 font-weight-bold"><label for="state">{{ __('State') }}:</label> {!! $errors->has('state')? '<span class="text-danger text-capitalize">'. $errors->first('state').'</span>':'' !!}</p>
                                <div class="input-group input-group-lg mb-3 d-">
                                    <input type="text" name="state" id="state" class="form-control rounded" aria-label="Large" placeholder="{{__('Supplier state')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('state') }}">
                                </div>

                                <p class="mb-1 font-weight-bold"><label for="country">{{ __('Country') }}:</label> {!! $errors->has('country')? '<span class="text-danger text-capitalize">'. $errors->first('country').'</span>':'' !!}</p>
                                <div class="input-group input-group-lg mb-3 d-">
                                    <input type="text" name="country" id="country" class="form-control rounded" aria-label="Large" placeholder="{{__('Supplier country')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('country') }}">
                                </div>

                                {{--<p class="mb-1 font-weight-bold"><label for="status">{{ __('Status') }}:</label> {!! $errors->has('status')? '<span class="text-danger text-capitalize">'. $errors->first('status').'</span>':'' !!}</p>--}}

                                {{--<div class="input-group input-group-lg mb-3 d-">--}}

                                    {{--{{ Form::select('status', $status, null, array('id'=>'status','class'=>'form-control','required'=>true,'style'=>'width:100%')) }}--}}
                                {{--</div>--}}


                                <p class="mb-1 font-weight-bold"><label for="address">{{ __('Address') }}:</label> {!! $errors->has('address')? '<span class="text-danger text-capitalize">'. $errors->first('address').'</span>':'' !!}</p>
                                <div class="form-group form-group-lg mb-3 d-">
                                    <textarea name="address" id="address" class="form-control rounded" rows="3" placeholder="{{__('Supplier Address')}}">{!! old('address') !!}</textarea>

                                </div>
                            </div>


                            <hr>
                        </div>

                        <div class="form-row table-responsive">
                            <h4>Payment Term</h4>
                            <table class="table table-striped table-bordered miw-500 dac_table" cellspacing="0" width="100%" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>{{ __('Payment Term') }}</th>
                                        <th  width="15%">{{ __('Payment Percent') }}</th>
                                        <th width="15%">{{__('Day Duration')}}</th>
                                        <th  width="15%">{{__('Type')}}</th>
                                        <th>{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody class="field_wrapper">
                                <tr>
                                    <td>
                                        <div class="input-group input-group-lg mb-12 d-">

                                            <select name="payment_term_id[]" id="paymentTermId_1" class="form-control" required style="width: 100%;">
                                                <option value="{{ null }}">Select One</option>
                                                @foreach($paymentTerms as $paymentTerm)
                                                    <option value="{{ $paymentTerm->id }}">{{ $paymentTerm->term}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-lg mb-12 d-">

                                            <input type="number" name="payment_percent[]" id="paymentPercent_1" class="form-control" min="1" placeholder="%" onkeypress="if(this.value.length==2) return false;" required />
                                        </div>
                                    </td>
                                    <td>

                                        <div class="input-group input-group-lg mb-12 d-">
                                            <input type="number" name="day_duration[]" id="dayDuration_1" class="form-control" min="1" placeholder="Day" onkeypress="if(this.value.length==3) return false;" required />
                                        </div>

                                    </td>
                                    <td>
                                        <div class="input-group input-group-lg mb-12 d-">

                                            <select name="type[]" id="type_1" class="form-control"  required>
                                                <option value="{{ null }}">Select One</option>

                                                    <option value="{{\App\Models\PmsModels\SupplierPaymentTerm::ADVANCE}}">{{\App\Models\PmsModels\SupplierPaymentTerm::ADVANCE}}
                                                    </option>
                                                    <option value="{{\App\Models\PmsModels\SupplierPaymentTerm::DUE}}">{{\App\Models\PmsModels\SupplierPaymentTerm::DUE}}
                                                    </option>
                                            </select>

                                        </div>

                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" id="remove_1" class="remove_button btn btn-sm btn-danger" title="Remove" style="color:#fff;">
                                            <i class="las la-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                        </div><!-- end row -->

                        <a href="javascript:void(0);" class="add_button btn btn-sm btn-success" style="margin-right:50px;float: right;" title="Add More Term">
                            <i class="las la-plus"></i>
                        </a>

                        <hr>
                        <button type="submit" class="btn btn-primary rounded" >{{ __('Save Supplier') }}</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- END WRAPPER CONTENT ------------------------------------------------------------------------->

<!-- END Modal ------------------------------------------------------------------------->

@endsection

@section('page-script')
<script>
    (function ($) {
        "use script";


        $(document).ready(function(){
            var maxField = "{{count($paymentTerms)}}";
            var addButton = $('.add_button');
            var x = 1;
            var wrapper = $('.field_wrapper');

            $(addButton).click(function(){
                if(x < maxField) {
                    x++;
                    var fieldHTML = '<tr>\n' +
                        '                                    <td>\n' +
                        '                                        <div class="input-group input-group-lg mb-12 d-">\n' +
                        '\n' +
                        '                                            <select name="payment_term_id[]" id="paymentTermId_' + x + '" class="form-control" style="width: 100%;">\n' +
                        '                                                <option value="{{ null }}">Select One</option>\n' +
                        '                                                @foreach($paymentTerms as $paymentTerm)\n' +
                        '                                                    <option value="{{ $paymentTerm->id }}">{{ $paymentTerm->term}}</option>\n' +
                        '                                                @endforeach\n' +
                        '                                            </select>\n' +
                        '\n' +
                        '                                        </div>\n' +
                        '                                    </td>\n' +
                        '                                    <td>\n' +
                        '\n' +
                        '                                        <div class="input-group input-group-lg mb-12 d-">\n' +

                        '<input type="number" name="payment_percent[]" id="paymentPercent_'+x+'" class="form-control" min="1" placeholder="%" onkeypress="if(this.value.length==2) return false;" required /></div>\n' +
                        '                                    </td>\n' +
                        '                                    <td>\n' +
                        '                                        <div class="input-group input-group-lg mb-12 d-">\n' +
                        '<input type="number" name="day_duration[]" id="dayDuration_'+x+'" class="form-control" min="1" placeholder="Day" onkeypress="if(this.value.length==3) return false;" required /></div>\n' +
                        '                                    </td>\n' +
                        '                                    <td>\n' +
                        '                                        <div class="input-group input-group-lg mb-12 d-">\n' +
                        '\n' +
                        '                                            <select name="type[]" id="type_' + x + '" class="form-control">\n' +
                        '                                                <option value="{{ null }}">Select One</option>\n' +
                        '\n' +
                        '                                                    <option value="{{\App\Models\PmsModels\SupplierPaymentTerm::ADVANCE}}">{{\App\Models\PmsModels\SupplierPaymentTerm::ADVANCE}}\n' +
                        '                                                    </option>\n' +
                        '                                                    <option value="{{\App\Models\PmsModels\SupplierPaymentTerm::DUE}}">{{\App\Models\PmsModels\SupplierPaymentTerm::DUE}}\n' +
                        '                                                    </option>\n' +
                        '                                            </select>\n' +
                        '\n' +
                        '                                        </div>\n' +
                        '\n' +
                        '                                    </td>\n' +
                        '                                    <td>\n' +
                        '                                        <a href="javascript:void(0);" id="remove_1" class="remove_button btn btn-sm btn-danger" title="Remove" style="color:#fff;">\n' +
                        '                                            <i class="las la-trash"></i>\n' +
                        '                                        </a>\n' +
                        '                                    </td>\n' +
                        '                                </tr>';

                    $(wrapper).append(fieldHTML);
                    $('#paymentTermId_' + x, wrapper).select2();
                }

            });

            $(wrapper).on('click', '.remove_button', function(e){
                e.preventDefault();

//                var incrementNumber = $(this).attr('id').split("_")[1];
//                var productVal=$('#product_'+incrementNumber).val()
//
//                const index = selectedProductIds.indexOf(productVal);
//                if (index > -1) {
//                    selectedProductIds.splice(index, 1);
//                }

                $(this).parent('td').parent('tr').remove();

            });

        });


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

    })(jQuery)
</script>
@endsection
