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
                     <a href="javascript:history.back()" class="btn btn-sm btn-warning text-white" data-toggle="tooltip" title="Back" > <i class="las la-chevron-left"></i>Back</a>
                </li>
            </ul><!-- /.breadcrumb -->

        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">

                    <form action="{{ route('pms.requisition.requisition.update',$requisition->id) }}" method="POST" id="editRequisitionForm">
                        <input type="hidden" name="_method" value="PUT">
                        @csrf
                        <div class="panel-body">
                            <div class="row">


                                <div class="col-md-3 col-sm-6">
                                    <p class="mb-1 font-weight-bold"><label for="reference">{{ __('Reference No.') }}:</label></p>
                                    <div class="input-group input-group-lg mb-3 d-">
                                        <input type="text" name="reference_no" id="reference" class="form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required readonly value="{{ old('reference_no',$requisition->reference_no) }}">


                                        @if ($errors->has('reference_no'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('reference_no') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    <p class="mb-1 font-weight-bold"><label for="date">{{ __('Date') }}:</label> </p>
                                    <div class="input-group input-group-lg mb-3 d-">
                                        <input type="text" name="requisition_date" id="date" class="form-control rounded air-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="<?php echo date('Y-m-d',strtotime($requisition->requisition_date)) ?>" >
                                    </div>
                                </div>

                                <div class="col-md-12  table-responsive style-scroll">

                                    <table class="table table-striped table-bordered miw-500 dac_table" cellspacing="0" width="100%" id="dataTable">
                                        <thead>
                                            <tr>

                                                <th>{{__('Category')}}</th>
                                                <th>{{__('Sub Category')}}</th>
                                                <th width="50%">{{__('Product')}}</th>
                                                <th width="10%">{{__('Qty')}}</th>
                                                <th class="text-center">{{__('Action')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="field_wrapper">

                                            <?php $oldProductIds=[];?>
                                            @forelse($requisition->requisitionItems as $key=>$requisitionItem)
                                            <tr>
                                                <td>
                                                    <div class="input-group input-group-lg mb-3 d-">
                                                        <select name="category_id" id="category_e{{$key}}" class="form-control category">
                                                            <option value="{{ null }}">{{ __('Select One') }}</option>
                                                            
                                                            @foreach($categories as $category)
                                                            <option value="{{ $category->id }}" {{$requisitionItem->product->category->parent_id==$category->id?'selected':''}}>
                                                                {{ $category->name.'('.$category->code.')'}}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="input-group input-group-lg mb-3 d-">
                                                        <select name="sub_category_id[]" id="subCategoryId_e{{$key}}" class="form-control subcategory">
                                                            <option value="{{ null }}">{{ __('Select Category first') }}</option>

                                                            
                                                            <option value="{{ $requisitionItem->product->category_id }}" selected>
                                                                {{ $requisitionItem->product->category->name.'('.$requisitionItem->product->category->code.')'}}
                                                            </option>

                                                        </select>
                                                    </div>

                                                </td>

                                                <td>
                                                    <div class="input-group input-group-lg mb-3 d-">
                                                        <?php
                                                            array_push($oldProductIds,$requisitionItem->product->id);
                                                        ?>
                                                        <select name="product_id[]" id="product_e{{$key}}" class="form-control product" required>
                                                            <option value="{{$requisitionItem->product->id}}">{{ __($requisitionItem->product->name) }}</option>
                                                        </select>
                                                    </div>

                                                </td>
                                                <td>
                                                    <div class="input-group input-group-lg mb-3 d-">
                                                        <input type="number" name="qty[]" min="1" max="99999999" id="qty_e{{$key}}" onKeyPress="if(this.value.length==4) return false;" class="form-control " aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{ old('qty',$requisitionItem->qty) }}">
                                                    </div>
                                                </td>

                                                <td>
                                                    <a href="javascript:void(0);" id="remove_e{{$key}}" class="remove_button btn btn-danger btn-sm" style="margin-right:17px;" title="Remove" >
                                                        <i class="las la-trash"></i>
                                                    </a>
                                                </td>
                                                {{--@endif--}}
                                            </tr>
                                            @empty
                                            @endforelse

                                        </tbody>
                                    </table>
                                    <a href="javascript:void(0);" style="margin-right:27px;" class="add_button btn btn-sm btn-primary pull-right" title="Add More Product">
                                        <i class="las la-plus"></i>
                                    </a>
                                </div>
                                
                                <div class="col-md-12">
                                    <p class="mb-1 font-weight-bold"><label for="remarks">{{ __('Remarks') }}:</label> {!! $errors->has('remarks')? '<span class="text-danger text-capitalize">'. $errors->first('remarks').'</span>':'' !!}</p>
                                    <div class="form-group form-group-lg mb-3 d-">
                                        <textarea rows="3" name="remarks" id="remarks" class="form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm">{!! old('remarks',$requisition->remarks) !!}</textarea>
                                    </div>

                                    <input type="hidden" name="status" value="{{$requisition->status}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-danger rounded pull-right">{{ __('Update Requisition') }}</button>
                                </div>
                                
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- END WRAPPER CONTENT ------------------------------------------------------------------------->

@endsection

@section('page-script')

<!-- Add / Remove Code -->
<script type="text/javascript">
    var selectedProductIds=["<?php echo implode(",",$oldProductIds) ?>"];

    function changeSelectedProductIds() {
        selectedProductIds=[];
        $('.product').each(function () {
            selectedProductIds.push($(this).val());
        })
    }

    $(document).ready(function(){
            var maxField = 500; //Input fields increment limitation
            var addButton = $('.add_button'); //Add button selector
            var x = 1; //Initial field counter is 1
            var wrapper = $('.field_wrapper'); //Input field wrapper
            //var fieldHTML = '<div><input type="text" name="field_name[]" value=""/><a href="javascript:void(0);" class="remove_button"><img src="https://www.paibaa.com/images/default/remove-icon.png"/></a></div>'; //New input field html


            //Once add button is clicked
            $(addButton).click(function(){
                //Check maximum number of input fields
                //if(x < maxField){
                x++; //Increment field counter

                var fieldHTML = '<tr>\n' +
                '                                            <td>\n' +
                '                                              <div class="input-group input-group-lg mb-3 d-">\n' +
                '                                                <select name="category_id" id="category_'+x+'" class="form-control category select2">\n' +
                '                                                    <option value="{{ null }}">{{ __("Select One") }}</option>\n' +
                '                                                    @foreach($categories as $category)\n' +
                '                                                        <option value="{{ $category->id }}">{{ $category->name."(".$category->code.")"}}</option>\n' +
                '                                                    @endforeach\n' +
                '                                                </select>\n' +
                '                                              </div>\n' +
                '                                            </td>\n' +
                        '<td>\n' +
                    '                                                    <div class="input-group input-group-lg mb-3 d-">\n' +
                    '                                                        <select name="sub_category_id[]" id="subCategoryId_'+x+'" class="form-control subcategory" placeholder="Select Category first">\n' +
                    '                                                            <option value="{{ null }}">{{ __('Select Category first') }}</option>\n' +
                    '\n' +
                    '                                                            \n' +
                    '                                                            <option value="{{ null }}" selected>\n' +
                    '                                                            </option>\n' +
                    '                                                            \n' +
                    '\n' +
                    '                                                        </select>\n' +
                    '                                                    </div>\n' +
                    '\n' +
                    '                                                </td>'+

                '                                            <td>\n' +
                '\n' +
                '                                                <div class="input-group input-group-lg mb-3 d-">\n' +
                '                                                    <select name="product_id[]" id="product_'+x+'" class="form-control select2 product" required>\n' +
                '                                                        <option value="{{ null }}">{{ __("Select Category first") }}</option>\n' +
                '                                                    </select>\n' +
                '                                                </div>\n' +
                '\n' +
                '                                            </td>\n' +
                '                                            <td>\n' +
                '                                                <div class="input-group input-group-lg mb-3 d-">\n' +
                '                                                    <input type="number" name="qty[]" min="1" max="99999999" id="qty_'+x+'" onKeyPress="if(this.value.length==4) return false;" class="form-control " aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{ old("qty") }}">\n' +
                '                                                </div>\n' +
                '                                            </td>\n' +
                '\n' +
                '                                            <td>\n' +
                '                                                <a href="javascript:void(0);" id="remove_'+x+'" class="remove_button btn btn-sm btn-danger" title="Remove" >\n' +
                '                                                    <i class="las la-trash"></i>\n' +
                '                                                </a>\n' +
                '                                            </td>\n' +
                '\n' +
                '                                        </tr>';

                $(wrapper).append(fieldHTML); //Add field html
                $('#category_'+x, wrapper).select2();
                $('#subCategoryId_'+x, wrapper).select2();
                $('#product_'+x, wrapper).select2();
                //}
            });


            //Once remove button is clicked
            $(wrapper).on('click', '.remove_button', function(e){
                e.preventDefault();

                var incrementNumber = $(this).attr('id').split("_")[1];
                var productVal=$('#product_'+incrementNumber).val()

                const index = selectedProductIds.indexOf(productVal);
                if (index > -1) {
                    selectedProductIds.splice(index, 1);
                }
                console.log(selectedProductIds)

                $(this).parent('td').parent('tr').remove(); //Remove field html
                //x--; //Decrement field counter
            });


        });
    </script>


    <!-- Load category wise products  -->
    <script>
        $(document).ready(function() {
            var wrapper = $('.field_wrapper');
        
        $(wrapper).on('change', '.category', function (e) {

            changeSelectedProductIds();

           var incrementNumber = $(this).attr('id').split("_")[1];

            $('#qty_'+incrementNumber).val('')
            //console.log($('#product_e'+incrementNumber).val())
            $('#product_'+incrementNumber).val('').trigger('change')

            var categoryId = $('#category_' + incrementNumber).val();

            if (categoryId.length === 0) {
                categoryId = 0
                $('#subCategoryId_' + incrementNumber).html('<center><img src=" {{'<i class="fa fa-spinner"></i>'}}"/></center>').load('{{URL::to(Request()->route()->getPrefix()."requisition/load-category-wise-subcategory")}}/' + categoryId);

            } else {
                $('#subCategoryId_' + incrementNumber).html('<center><img src=" {{'<i class="fa fa-spinner"></i>'}}"/></center>').load('{{URL::to(Request()->route()->getPrefix()."requisition/load-category-wise-subcategory")}}/' + categoryId);
            }

        });

            $(wrapper).on('change','.subcategory', function (e) {

                changeSelectedProductIds();

                var incrementNumber = $(this).attr('id').split("_")[1];
                var subcategory_id = $('#subCategoryId_' + incrementNumber).val();

                if (subcategory_id.length === 0) {
                    subcategory_id = 0
                    $('#qty_'+incrementNumber).val('')
                    $('#product_' + incrementNumber).html('<center><img src=" {{'<i class="fa fa-spinner"></i>'}}"/></center>').load('{{URL::to(Request()->route()->getPrefix()."requisition/load-category-wise-product")}}/' + subcategory_id+'?products_id='+selectedProductIds);
                } else {
                    $('#qty_'+incrementNumber).val('')
                    $('#product_' + incrementNumber).html('<center><img src=" {{asset('images/default/loader.gif')}}"/></center>').load('{{URL::to(Request()->route()->getPrefix()."requisition/load-category-wise-product")}}/' + subcategory_id+'?products_id='+selectedProductIds);
                }
            });

            $(wrapper).on('change','.product', function (e) {

                changeSelectedProductIds();

                var incrementNumber = $(this).attr('id').split("_")[1];
                $('#qty_'+incrementNumber).val('')
            });

        });

    </script>


    @endsection
