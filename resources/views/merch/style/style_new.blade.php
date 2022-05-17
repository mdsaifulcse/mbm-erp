@extends('merch.layout')
@section('title', 'New Style')
@section('main-content')
@push('css')
  <style>
  .ui-autocomplete {
    position: absolute;
    z-index: 2150000000 !important;
    cursor: default;
    border: 2px solid #ccc;
    padding: 5px 0;
    border-radius: 2px;
   }
   .close-button {
    position: absolute;
    z-index: 100;
    right: -4px;
    border: none;
    background: transparent;
    padding: 5px 10px;
    color: #ff0000;
    font-size: 16px;
    top: -12px;
  }


   @media only screen and (max-width: 767px) {

    .modal{margin-top: 45px;}
    .checkbox label input[type=checkbox].ace+.lbl, .radio label input[type=radio].ace+.lbl{margin-left: 10px;}
    input[type=checkbox].ace+.lbl, input[type=radio].ace+.lbl{ margin-left: 10px; }

   }
   @media only screen and (max-width: 480px) {

    .modal{margin-top: 85px;}
    .checkbox label input[type=checkbox].ace+.lbl, .radio label input[type=radio].ace+.lbl{margin-left: 10px;}
    input[type=checkbox].ace+.lbl, input[type=radio].ace+.lbl{margin-left: 10px;}
    .modalDiv .col-xs-8 {width: 100% !important; padding-top: 10px;}
    .modalDiv .col-xs-4 {padding-left: 0px;}

   }
   
    .slide_upload{width: auto;height: 120px;position: relative;cursor: pointer;background: #eee;border: 1px dashed #999;}
    .slide_upload img{width: 100%;height: 100%;border: 1px dashed #999;padding: 2px;}
    .slide_upload::before{content: "+";position: absolute;top: 50%;color: #ccc;left: 50%;font-size: 52px;margin-left: -17px;margin-top: -37px;}

</style>
@endpush
  <div class="main-content">
      <div class="main-content-inner">
          <div class="col-sm-12">
              <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                  <ul class="breadcrumb">
                      <li>
                          <i class="ace-icon fa fa-home home-icon"></i>
                          <a href="#">Merchandising</a>
                      </li>
                      <li>
                          <a href="#">Style</a>
                      </li>
                      <li class="active">Style New</li>
                  </ul><!-- /.breadcrumb -->
       
              </div>
              <div class="panel">
                <div class="panel-heading">
                      <h6>New Style
                          <div class="pull-right">
                            
                            <a class="btn btn-sm btn-primary" href="{{ url('merch/style/style_list') }}"><i class="las la-list"></i> Style List</a>
                            
                        </div>
                      </h6>
                </div>
                <div class="panel-body">
                  @include('inc/message')
                  <b style="color: green">* Production Type (Development)</b>
                  <div class="style_section">

                      {{-- form start here --}}
                      {{ Form::open(["url" => "merch/style/style_store", "class"=>"form-horizontal", "files"=>true]) }}
                          <!-- Top -->
                         {{-- hidden field --}}
                         <input type="hidden" name="stl_order_type" id="inlineRadio1" value="Development" data-validation="required" readonly>
                          <div class="row" style="padding-top: 20px;">
                              <div class="col-sm-12">
                                  <div  class=" form-group row">
                                      <label class="col-sm-2 control-label no-padding-right" for="image" > Image  </label>
                                      <div class="col-sm-10">
                                        <div class="row" id="multi-image-div" style="padding:0;">
                                          <div class="col-sm-2 multi-image">

                                            <label class="slide_upload" for="file_image_0">
                                              <!--  -->
                                              <img id="imagepreview_0" src='{{asset('assets/files/style/placeholder.png')}}'>
                                            </label>

                                            <input type="file" class="multi-image-input" id="file_image_0" name="style_img_n" onchange="readURL(this,this.id)" style="display:none">

                                           <input type="hidden" class="setfile" name="style_img" value="/assets/files/style/placeholder.png">
                                          </div>
                                        </div>

                                      </div>


                                  </div>
                              </div>
                          </div>

                          <div class="row">
                              <!-- 1st Row -->
                              <div class="col-sm-6" id="buyerSection">

                                  <div class="form-group row">
                                      <label class="col-sm-4 control-label no-padding-right" for="b_id" >Buyer <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                      <div class="col-sm-7 col-xs-11">
                                          <?php
                                              // selected new add buyer if id exist
                                              if (Request::input('bNewId')) {
                                                 $bNewId = Request::input('bNewId');
                                              }
                                          ?>
                                          {{ Form::select('b_id', $buyerList, isset($bNewId)?$bNewId:null, ['placeholder'=>'Select Buyer', 'class'=> 'col-xs-12 form-control', 'id'=>"b_id", 'data-validation' => 'required']) }}
                                      </div>

                                      @hasanyrole("Super Admin|merchandiser")
                                          <div class="col-sm-1 col-xs-1" style="padding-left: 0px;">

                                            <a href="{{ url('merch/setup/buyer_info?pre=').url()->current() }}" class="addart btn btn-sm btn-info" style=" padding-bottom: 5px; padding-right: 0px; padding-left: 1px;" ><i class="fa fa-plus"></i></a>
                                          </div>
                                      @endhasanyrole
                                  </div>
                                  <div class="form-group row">
                                      <label class="col-sm-4 control-label no-padding-right" for="mr_brand_br_id" >Brand<span style="color: red">&#42;</span></label>
                                      <div class="col-sm-7 col-xs-11">
                                          {{ Form::select('mr_brand_br_id', $brand, null, ['id'=> 'mr_brand_br_id', 'placeholder' => 'Select Brand', 'style'=> 'width: 100%', 'data-validation' => 'required']) }}
                                      </div>
                                  </div>

                                  <div class="form-group row">
                                      <label class="col-sm-4 control-label no-padding-right" for="prd_type_id" > Product Type <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                      <div class="col-sm-7 col-xs-11">
                                          {{ Form::select('prd_type_id', $productTypeList, null, ['placeholder'=>'Select Product Type', 'class'=> 'col-xs-12  form-control', 'id'=>'prd_type_id', 'data-validation' => 'required']) }}
                                      </div>
                                      
                                  </div>

                                  <div class="form-group row">
                                      <label class="col-sm-4 control-label no-padding-right" for="gmt_id" > Garments Type <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                      <div class="col-sm-7 col-xs-11">
                                          
                                          {{ Form::select('gmt_id', $garmentsTypeList, null, ['placeholder'=>'Please Select Garments Type', 'id'=>'gmt_id', 'class'=> 'form-control col-xs-12 ', 'data-validation' => 'required']) }}
                                      </div>
                                      @hasanyrole("Super Admin|merchandiser")
                                          <div class="col-sm-1 col-xs-1" style="padding-left: 0px;">
                                              <button class="addart btn btn-sm btn-info" style=" padding-bottom: 5px; padding-right: 0px; padding-left: 1px; display: none;" data-toggle="modal" data-target="#new_garments_type" id="new_garments_type_btn_id" type="button"><i class="fa fa-plus"></i></button>
                                          </div>
                                      @endhasanyrole
                                  </div>

                                  <div class="form-group row">
                                      <label class="col-sm-4 control-label no-padding-right" for="se_id"> Season <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                      <div class="col-sm-7 col-xs-11">
                                        <div class="row">
                                          <div class="col-sm-9">
                                            {{ Form::select('se_id', [], null, ['placeholder'=>'Please Select season', 'id'=>'se_id', 'class'=> 'form-control col-xs-12 ', 'data-validation' => 'required']) }}
                                          </div>
                                        
                                          <div class="col-sm-3 pl-0">
                                            <input type="year" class=" form-control" id="year" name="stl_year" placeholder="Y" required="required" value="{{ date('Y') }}" autocomplete="off" onClick="this.select()">
                                          </div>
                                        </div> 
                                      </div>
                                      
                                  </div>

                                  <div class="form-group row">
                                      <label class="col-sm-4 control-label no-padding-right" for="gender"> Gender <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                      <div class="col-sm-7 col-xs-11">
                                        <?php $gender = ['Male'=>'Male','Female'=>'Female']; ?>
                                          {{ Form::select('gender', $gender, null, ['placeholder'=>'Please Select Gender', 'id'=>'gender', 'class'=> 'form-control col-xs-12 ', 'data-validation' => 'required']) }}
                                      </div>
                                  </div>

                              </div>

                              <!-- 2nd Row -->
                              <div class="col-sm-6">

                                <div class="form-group row" id="style-form">
                                    <label class="col-sm-4 control-label no-padding-right" for="stl_no" > Style Reference 1 <span style="color: red; vertical-align: top;">&#42;</span></label>
                                    <div class="col-sm-8 col-xs-11">
                                        <input type="text" id="stl_no" name="stl_no" placeholder="Enter Style No" class="col-xs-12 form-control" autocomplete="off" />
                                        <span id="error_stl_no"></span>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 control-label no-padding-right" for="stl_product_name" > Style Reference 2</label>
                                    <div class="col-sm-8 col-xs-11">
                                        <input type="text" id="stl_product_name" name="stl_product_name" placeholder="Enter Text" autocomplete="off" class="col-xs-12 form-control"  />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 control-label no-padding-right" for="stl_smv" > Sewing SMV <span style="color: red; vertical-align: top;">&#42;</span></label>
                                    <div class="col-sm-8 col-xs-11">
                                      <input type="text" id="stl_smv" name="stl_smv" placeholder="Enter Value" class="col-xs-12 form-control" autocomplete="off" data-validation="number" data-validation-allowing="float"/>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label class="col-sm-4 control-label no-padding-right" for="stl_description" > Remarks</label>
                                    <div class="col-sm-8 col-xs-11">
                                        <textarea name="stl_description" id="stl_description" placeholder="Enter Remarks"  class="form-control" {{-- data-validation="required length custom" data-validation-length="1-128" data-validation-regexp="^([.,-;:'&a-z A-Z]+)$" --}}></textarea>
                                    </div>
                                </div>

                                  <div class="form-group row">
                                      <label class="col-sm-4 control-label no-padding-right" for="mr_sample_style" > Sample Type </label>
                                      <div class="col-sm-8 col-xs-11" style='padding-left:20px;'>
                                          <div class="control-group" >
                                              <div class="checkbox" id="sample-checkbox">
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>

                              <!-- 3rd Row -->
                              <div class="col-sm-12 col-xs-12 modalDiv" style="padding-top: 16px; border: 1px solid lightgray; overflow-x: auto;">

                                  <!-- Operation -->

                                      <div class="col-md-12 col-xs-12">


                                          <div class="form-group row" style="padding-left: 0px;">

                                              <div class="col-sm-3 col-xs-4">
                                                  <button type="button" class="btn btn-primary btn-sm" id="operationModalId" data-toggle="modal" data-target="#operationModal" style="width:145px;border-radius: 5px;">Select Operation</button>
                                              </div>
                                              <div class="col-sm-6 col-xs-8" id="show_selected_operations" style="margin: 0px; padding-left: 0px; padding-right: 0px;">

                                              </div>
                                          </div>

                                          <div class="form-group row wash" style="padding-left: 0px;display:none;">

                                              <div class="col-sm-3 col-xs-4">
                                                  <div class="col-xs-12" style="margin-left:0px; padding-left: 0px;">
                                                      <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" id="washTypeModalId" data-target="#washTypeSelectModal" style="border-radius: 5px;">Select Wash Type</button>
                                                      @hasanyrole("Super Admin|merchandiser")
                                                         <a href="{{ url('merch/setup/wash_type') }}" class="btn btn-sm btn-info" target="_blank"><i class="fa fa-plus"></i></a>
                                                      @endhasanyrole
                                                  </div>
                                              </div>
                                              <div class="col-sm-6 col-xs-8" id="show_selected_wash_type" style="margin: 0px; padding-left: 0px; padding-right: 0px;"></div>
                                          </div>

                                          <div class="form-group row" style="padding-left: 0px;">

                                              <div class="col-sm-3 col-xs-4">
                                                  <button type="button" class="btn btn-primary btn-sm" id="specialMachineModalId" data-toggle="modal" data-target="#specialMachineModal" style="width:145px;border-radius: 5px;">Special Machine</button>
                                              </div>
                                              <div class="col-sm-6 col-xs-8" id="show_selected_machines" style="margin: 0px; padding-left: 0px; padding-right: 0px;"></div>
                                          </div>

                                          <div class="form-group row" style="padding-left: 0px;">

                                              <div class="col-sm-3 col-xs-4">
                                                  <div class="col-xs-12" style="margin-left: 0px; padding-left: 0px; padding-right: 0px;">
                                                      <button type="button" class="btn btn-primary btn-sm"  data-toggle="modal" id="sizeGroupModalId" data-target="#sizeGroupModal" style="border-radius: 5px;">Select Size Group</button>
                                                      @hasanyrole("Super Admin|merchandiser")
                                                      <a id="size-group-buyer" href="{{ url('/merch/setup/productsize?buyer=&&p_type=')}}" target="_blank" class="addart btn btn-sm btn-info" ><i class="fa fa-plus"></i></a>
                                                     @endhasanyrole
                                                  </div>
                                              </div>
                                              <div class="col-sm-6 col-xs-8" id="show_selected_size_group" style="margin: 0px; padding-left: 0px; padding-right: 0px;"></div>
                                          </div>
                                      </div>

                              </div>


                          </div>
                              <!-- Submit Button -->
                          <div class="col-sm-6 offset-sm-3">
                              <br>
                            @include('merch.common.save-btn-section')
                          </div>
                      {{ Form::close() }}
                  </div>
                 </div>
              </div>
          </div>
      </div>
  </div>
  
      


<!-- Select Size Group  -->
<div class="modal fade" id="sizeGroupModal" tabindex="-1" role="dialog" aria-labelledby="sizeLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Size Group</h4>
      </div>
      <div class="modal-body" style="padding:0 15px" id="sizeGroupModalBody">
        <div class="row" style="padding: 20px;" id="addListToModal">
            <span>No Size group, Please Select Buyer</span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
        <button type="button" id="sizeGroupModalDone" class="btn btn-primary btn-sm">Done</button>
      </div>
    </div>
  </div>
</div>

<!--Buyer Modal -->
<div class="modal fade" id="new_buyer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{ Form::open(["url"=>"", "class"=>"form-horizontal", 'id'=>'newBuyerFrm']) }}
            <div class="modal-header bg-primary">
                <h2 class="modal-title text-center" id="myModalLabel">Add New Buyer</h2>
            </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group col-sm-9">
                                <label class="col-sm-4 control-label no-padding-right" for="march_buyer_name" > Buyer Name<span style="color: red">&#42;</span> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="march_buyer_name" name="march_buyer_name" placeholder="Buyer name" class="col-xs-12 march_buyer_name" data-validation="required length custom" data-validation-length="1-50" />
                                </div>
                            </div>
                            <div class="form-group col-sm-9">
                                <label class="col-sm-4 control-label no-padding-right" for="march_buyer_short_name" > Buyer Short Name<span style="color: red">&#42;</span> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="march_buyer_short_name" name="march_buyer_short_name" placeholder="Buyer short name" class="col-xs-12 march_buyer_short_name" data-validation="required length custom" data-validation-length="1-50"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-9">
                              <label class="col-sm-4 control-label no-padding-right" for="action_type" > Country<span style="color: red">&#42;</span> </label>
                                <div class="col-sm-8">
                                  {{ Form::select('country', $country, null, ['placeholder'=>'Select Country','id'=>'country','class'=> 'col-xs-10 country', 'style'=>'width:100%', 'data-validation' => 'required']) }}
                                </div>
                            </div>

                            <div class="form-group col-sm-9">
                                <label class="col-sm-4 control-label no-padding-right" for="march_buyer_address" >  Address <span style="color: red">&#42;</span></label>
                                <div class="col-sm-8">

                                  <textarea name="march_buyer_address" class="col-xs-12 march_buyer_address" id="march_buyer_address"  data-validation="required length" data-validation-length="0-128"></textarea>
                                </div>
                            </div>
                        </div>

                        <div id="contactPersonData" class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="march_buyer_contact" > Contact Person <span style="color: red">&#42;</span></label>
                                <div class="col-sm-8">
                                    <textarea name="march_buyer_contact[]" class="col-sm-8 march_buyer_contact"  data-validation="required length" data-validation-length="0-128" cl></textarea>
                                    <!--  <a href=""><h5>+ Add More</h5></a>-->
                                    <div class="form-group col-xs-3 col-sm-3">
                                        <button type="button" class="btn btn-sm btn-success AddBtn_bu">+</button>
                                        <button type="button" class="btn btn-sm btn-danger RemoveBtn_bu">-</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer clearfix">
                    <div class="col-md-8">
                        <button type="button btn-sm" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-info btn-sm size-add-modal" type="submit" id="buyer-add-modal" >
                         DONE
                       </button>
                     </div>

                </div>

                {{ Form::close() }}
        </div>
    </div>
</div>
<!-- Product Type -->
<div class="modal fade" id="new_product" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            {{ Form::open(["url"=>"", "class"=>"form-horizontal", 'id'=>'newProdTypeFrm']) }}
                <div class="modal-header bg-primary">
                    <h2 class="modal-title text-center" id="myModalLabel">Add New Product</h2>
                </div>

                <div class="modal-body">
                    <div class="message"></div>
                    <div class="row">
                        <div class="form-group col-sm-9">
                            <label class="col-sm-4 control-label no-padding-right" for="prd_type_name" > Product Type<span style="color: red">&#42;</span> </label>
                            <div class="col-sm-8">
                                <input type="text" name="prd_type_name" id="prd_type_name" placeholder="Product Type" class="form-control col-xs-12" data-validation="required length custom" data-validation-length="1-50"/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer clearfix " >
                    <div class="col-md-8">
                        <button type="button btn-sm" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-info btn-sm product_add" type="submit" id="product_add" >DONE</button>
                    </div>
                </div>

            {{Form::close()}}

        </div>
    </div>
</div>

<!-- Garment Type -->
<div class="modal fade" id="new_garments_type" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            {{ Form::open(["url"=>"", "class"=>"form-horizontal", 'id'=>'newGmTypeFrm']) }}
                <div class="modal-header bg-primary">
                    <h2 class="modal-title text-center" id="myModalLabel">Add New Garment</h2>
                </div>

                <div class="modal-body">
                    <div class="message"></div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="prd_type_id"> Product Type<span style="color: red">&#42;</span>  </label>
                        <div class="col-sm-7">
                            {{ Form::select('modal_prd_type_id', $productTypeList, null, ['placeholder'=>'Select Product Type', 'id'=>'modal_prd_type_id', 'class'=> 'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Product Type field is required','disabled']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="gmt_name" > Garment Type<span style="color: red">&#42;</span> </label>
                        <div class="col-sm-7">
                            <input type="text" name="gmt_name" id="gmt_name" placeholder="Garment Type" class="col-xs-12" data-validation="required length custom" data-validation-length="1-50"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="gmt_remarks"> Remarks</label>
                        <div class="col-sm-7">
                            <textarea multiple="multiple" name="gmt_remarks" id="gmt_remarks" class="form-control" placeholder="Remarks"  data-validation="length custom" data-validation-length="1-128" data-validation-optional="true"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer clearfix " >
                    <div class="col-md-8">
                        <button type="button btn-sm" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-info btn-sm garments_add" type="submit" id="garments_add" >DONE</button>
                    </div>
                </div>

            {{Form::close()}}

        </div>
    </div>
</div>

<!-- Season Modal-->
<div class="modal fade" id="new_season" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h2 class="modal-title text-center" id="myModalLabel">Add New Season
                </h2>
            </div>

                <div class="modal-body">
                     <div class="message"></div>
                 {{ Form::open(["url"=>"", "class"=>"form-horizontal", 'id'=>'newSeasonFrm']) }}
                    <div class="form-horizontal">

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="se_name" > Season Name<span style="color: red">&#42;</span> </label>
                            <div class="col-sm-9">

                                <input type="text" name="se_name" id="se_name" placeholder="Season Name"  class="col-xs-8 autocomplete_pla" data-type ="season" data-validation="required length custom" data-validation-length="1-128" autocomplete="off"/>
                                <div id="suggesstion-box"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="se_mm_start" > Start Month-Year<span style="color: red">&#42;</span> </label>
                            <div class="col-sm-4">

                                <input type="text" name="se_mm_start" id="se_mm_start" placeholder="Month-y" class="form-control monthYearpicker" data-validation="required"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="se_mm_end" > End Month-Year<span style="color: red">&#42;</span> </label>
                            <div class="col-sm-4">
                              <input type="text" name="se_mm_end" id="se_mm_end" placeholder="Month-y" class="form-control monthYearpicker" data-validation="required"/>
                            </div>

                        </div>

                        <!-- /.row -->
                    </div>
                <div class="modal-footer" style="margin-top: 20px;">
                    <div class="col-md-8">

                        <button type="button btn-sm" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-info btn-sm season-add" type="submit" id="season-add" >
                         DONE
                       </button>
                    </div>
                  {{Form::close()}}
                  </div>
                </div>
        </div>
    </div>
</div>
<!-- Wash Type Modal-->
<div class="modal fade" id="newWashModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{ Form::open(["url"=>"", "class"=>"form-horizontal", 'id'=>'newWashFrm']) }}
            <div class="modal-header bg-primary">
                <h2 class="modal-title text-center" id="myModalLabel">Add New Wash
                </h2>
            </div>
            <div class="modal-body">
                <div class="message"></div>
                <div class="form-group row">
                    <label class="col-sm-3 control-label no-padding-right" for="wash_name" >Wash Name<span style="color: red">&#42;</span> </label>

                    <div class="col-sm-9">
                        <input type="text" name="wash_name" id="wash_name" placeholder="Wash Name"  class="col-xs-12" value="{{ old('wash_name') }}" data-validation="required length custom" data-validation-length="1-45"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="wash_rate" >Rate<span style="color: red">&#42;</span> </label>
                    <div class="col-sm-9">
                        <input type="text" name="wash_rate" id="wash_rate" placeholder="Wash Rate"  class="col-xs-12" value="{{ old('wash_rate') }}" data-validation="required length custom" data-validation-length="1-45"/>
                    </div>
                </div>

                <!-- /.row -->
            </div>
            <div class="modal-footer" style="margin-top: 20px;">
                <div class="col-md-8">
                   <!--<button class="btn btn-info btn-sm" type="submit">
                        <i class="ace-icon fa fa-check bigger-110"></i> ADD
                    </button>
                    <button class="btn btn-sm" type="reset">
                        <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                    </button> -->
                    <button type="button btn-sm" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-info btn-sm wash-add-modal" type="submit" id="wash-add-modal" >
                      DONE
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

<!-- Select Operation  -->
<div class="modal fade" id="operationModal" tabindex="-1" role="dialog" aria-labelledby="sizeLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Operations</h4>
      </div>
      <div class="modal-body" style="padding:0 15px">
        <div class="row" style="padding: 20px;" id="operationModalBody"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
        <button type="button" id="operationModalDone" class="btn btn-primary btn-sm">Done</button>
      </div>
    </div>
  </div>
</div>

<!-- Select Operation  -->
<div class="modal fade" id="specialMachineModal" tabindex="-1" role="dialog" aria-labelledby="sizeLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Special Machine</h4>
      </div>
      <div class="modal-body" style="padding:0 15px">
        <div class="row" style="padding: 20px;" id="specialMachineModalBody">
      </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
        <button type="button" id="specialMachineModalDone" class="btn btn-primary btn-sm">Done</button>
      </div>
    </div>
  </div>
</div>
<!-- Select Wash Data  -->
<div class="modal fade" id="washTypeSelectModal" tabindex="-1" role="dialog" aria-labelledby="sizeLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Wash Type</h4>
      </div>
      <div class="modal-body" style="padding:0 15px">
        <div class="row" id="washTypeModalBody" style="padding: 20px;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
        <button type="button" id="washTypeSelectModalDone" class="btn btn-primary btn-sm">Done</button>
      </div>
    </div>
  </div>
</div>

<!--New Size Group Modal-->
<div class="modal fade" id="new_size_group" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h2 class="modal-title text-center" id="myModalLabel">Add New Size Group
                </h2>
            </div>
            <div class="modal-body">
                {{ Form::open(["url"=>"", "class"=>"form-horizontal", 'id'=>'newSizeFrm']) }}
                    {{ csrf_field() }}

                        <div class="form-group">
                          <label class="col-sm-3 control-label no-padding-right" for="product_size_group" >Brand<span style="color: red">&#42;</span> </label>
                          <div class="col-sm-9">
                              <div class="form-group col-xs-12 col-sm-10" >
                                 {{ Form::select('brand', $brand, null, ['placeholder'=>'Select Brand', 'id'=> 'brand','class'=> 'col-xs-12','data-validation' => 'required']) }}
                               </div>
                          </div>
                        </div>
                        {{-- <div class="form-group">
                          <label class="col-sm-3 control-label no-padding-right" for="product_type" >Product Type <span style="color: red">&#42;</span> </label>
                          <div class="col-sm-7">
                            <select name="product_type" id="product_type" class="col-xs-12" data-validation = "required">
                                <option>Select</option>
                                 <option value="Bottom">Bottom</option>
                                 <option value="Top">Top</option>
                                 <option value="Top/Bottom">Top/Bottom</option>
                                 <option value="Tesco">Tesco</option>
                              </select>
                          </div>
                        </div> --}}
                        <div class="form-group">
                          <label class="col-sm-3 control-label no-padding-right" for="genre" >Genre <span style="color: red">&#42;</span> </label>
                          <div class="col-sm-7">
                            <select name="gender" id="genre" class="col-xs-12" data-validation = "required">
                              <option>Select Genre</option>
                               <option value="Mens">Men's</option>
                               <option value="Ladies">Ladies</option>
                               <option value="Boys/Girls">Boys/Girls</option>
                               <option value="Girls">Girls</option>
                               <option value="Womens">Women's</option>
                               <option value="Mens & Ladies">Men's & Ladies</option>
                               <option value="Baby Boys/Girls">Baby Boys/Girls</option>
                              </select>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-3 control-label no-padding-right" for="sg_name" >Size Group Name <span style="color: red">&#42;</span> </label>
                          <div class="col-sm-7">
                              <input type="text" id="sg_name" name="sg_name" placeholder="Enter Size Group Name" class="col-xs-12" data-validation="required length custom" data-validation-length="1-45"/>
                          </div>
                        </div>
                        <div class="addRemove">
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="psize" >Size <span style="color: red">&#42;</span></label>
                                <div class="col-sm-9">
                                    <input type="text" id="psize" name="psize[]" placeholder="Size" class="col-xs-9 psize" data-validation="required length custom" data-validation-length="1-11"/>
                                <!--  <a href=""><h5>+ Add More</h5></a>-->
                                    <div class="form-group col-xs-3 col-sm-3">
                                         <button type="button" class="btn btn-sm btn-success AddBtn_size">+</button>
                                         <button type="button" class="btn btn-sm btn-danger RemoveBtn_size">-</button>
                                    </div>
                                 </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="sino" >SI No <span style="color: red">&#42;</span></label>
                                <div class="col-sm-9">
                                    <input type="text" id="sino" name="sino[]" placeholder="Size No" class="col-xs-9 sino" data-validation="required length custom" data-validation-length="1-45"/>
                                </div>
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                <div class="modal-footer" style="margin-top: 20px;">
                    <div class="col-md-8">
                        <button type="button btn-sm" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-info btn-sm size-add-modal" type="submit" id="size-add-modal" >
                         DONE
                       </button>
                    </div>
                   {{ Form::close() }}
                </div>
        </div>
    </div>
</div>
@push('js')
<script type="text/javascript">
var url = "{{ url('/') }}";
//autocomplete placement script

$(document).on('focus','.autocomplete_pla',function(){
    var type = $(this).data('type');
    if(type == 'season')autoTypeNo=0;

    $(this).autocomplete({
        source: function( request, response ) {
            $.ajax({
                url : "{{ url('merch/setup/season_input') }}",
                method: 'GET',
                data: {
                  name_startsWith: request.term,
                  type: type,
                  b_id: $("#b_id").val()
                },
                success: function( data ) {
                    response( $.map( data, function( item ) {
                        var code = item.split("|");
                        return {
                            label: code[autoTypeNo],
                            value: code[autoTypeNo],
                            data : item
                        }
                    }));
                }
            });
        },
        autoFocus: true,
        minLength: 0,
        select: function( event, ui ) {
            var names = ui.item.data.split("|");
            $(this).val(names[0]);
        }
    });

});


// var selected_sizes = new Array();
$(document).ready(function()
{

    $('#washTypeModalId').on('click', function() {
        var checkedWashList = [];
        $('input.washType').each(function(i,v) {
            if($(this).val()) {
                checkedWashList[i] = $(this).val();
            }
        });
        $.ajax({
            url : "{{ url('merch/style/fetchwashgroup') }}",
            type: 'post',
            data: {
               checkedWash: checkedWashList
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(data)
            {
               $('#washTypeModalBody').html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });
    });

    // ajax load size group data on size group modal click
    var loaded = false;
    $('#sizeGroupModalId').on('click', function() {
        var buyer = $("#b_id").val();
        setTimeout(function() {
        $('input[name="prdsz_id[]"]').each(function(){
           var selectedgrp = $(this).val();
           $('input[name="sizeGroups[]"]').each(function(){
             if (selectedgrp == $(this).val())
             {
                  $(this).prop("checked",true) ;
             }
           });
        });
      }, 1000);

        if(buyer=="") {
            $('#sizeGroupModalBody').html('<div class="row"><p style="padding: 15px;">Please Select <strong>Buyer</strong> First.</p></div>');
        } else {
           // if(loaded) return;
           // if(loaded == false){
           var prd_type_id = $("#prd_type_id").val();
           if(prd_type_id == ""){
            $('#sizeGroupModalBody').html('<div class="row"><p style="padding: 15px;">Please select <strong>Product Type</strong>.</p></div>');
           } else {
             $.ajax({
                 url : "{{ url('merch/style/fetchsizegroup') }}"+"/"+buyer+"/"+prd_type_id,
                 type: 'get',
                 dataType: 'json',
                 success: function(data)
                 {
                     console.log(data);
                     $('#sizeGroupModalBody').html(data);
                 },
                 error: function()
                 {
                     alert('failed...');
                 }
             });
           }


            // loaded = true;
          // }
        }
    });

    // ajax load operation data on operation modal click
    var loadedop = false;
    $('#operationModalId').on('click', function() {
      setTimeout(function() {
      $('input[name="opr_id[]"]').each(function(){
         var selectedopr = $(this).val();
         $('input[name="operations[]"]').each(function(){
           if (selectedopr == $(this).val())
           {
                $(this).prop("checked",true) ;
           }
         });
      });
    }, 1000);
        //if(loadedop) return;
        $.ajax({
            url : "{{ url('merch/style/fetchoperations') }}",
            type: 'get',
            dataType: 'json',
            success: function(data)
            {
                $('#operationModalBody').html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });
         loadedop = true;
    });

    $('#specialMachineModalId').on('click', function() {
      setTimeout(function() {
      $('input[name="machine_id[]"]').each(function(){
         var selectedmachine = $(this).val();
         $('input[name="sp_machine_id[]"]').each(function(){
           if (selectedmachine == $(this).val())
           {
                $(this).prop("checked",true) ;
           }
         });
      });
    }, 1000);
        //if(loadedop) return;
        $.ajax({
            url : "{{ url('merch/style/fetchspecialmechines') }}",
            type: 'get',
            dataType: 'json',
            success: function(data)
            {
                $('#specialMachineModalBody').html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });
         loadedop = true;
    });
    // Size Group Add through ajax
    $('#new_size_group').on('show.bs.modal', function (e) {
        var modal = $(this);
        var button = $(e.relatedTarget);
        var buyer = $("#b_id").val();

        if(buyer==""){ alert('Please Select The buyer first!!');}

        $("#newSizeFrm").on("submit", function(e) {

            e.preventDefault();
             var buyer = $("#b_id").val();
             var brand = $("#brand").val();
             var product_type = $("#product_type").val();
             var gender = $("#gender").val();
             var sg_name = $("#sg_name").val();

             var that = $(this);

             var psize_array = new Array();
                $('input[name="psize[]"]').each(function(){
                   psize_array.push($(this).val());
                });
             var sino_array = new Array();
                $('input[name="sino[]"]').each(function(){
                   sino_array.push($(this).val());
                });

            // Size Group insert url
            $.ajax({
                url : "{{ url('merch/setup/productsizestore') }}",
                type: 'post',
                data: {
                    buyer  : buyer,
                    brand  : brand,
                    product_type: product_type,
                    gender : gender,
                    sg_name: sg_name,
                    psize  : psize_array,
                    sino   : sino_array

                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data)
                {
                    modal.find(".message").html("<div class='alert alert-success'>Size Group Successfully saved</div>");

                    // Ajax call for Sizegroup list if Successfulily saved
                    $.ajax({
                        url : "{{ url('merch/style/sizegroup') }}",
                        type: 'get',
                        data: { buyer: buyer },
                        success: function(data)
                        {
                           button.parent().prev().find(".prdsz_id").html(data);
                           modal.modal('hide');
                           that.unbind('submit');
                        },
                        error: function()
                        {
                            alert('failed...');
                        }
                    });
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });
    });

    // Load Size Group Modal Data on Buyer Select


     var b_id = $('#b_id :selected').val();

     if(b_id != ''){
         // Action Element list
         $.ajax({
             url : "{{ url('merch/style/get_sz_grp_modal_data') }}",
             type: 'get',
             data: {b_id: b_id},
             success: function(data)
             {
                 // $("#addListToModal").html(data.moData);
                 $("#show_selected_operations").html(data.opData);
             },
             error: function()
             {
                // $("#addListToModal").html("<span>No Size group, Please Select Buyer</span>");
             }
         });
         $("#new_session_btn_id").show();
     } else {
         // $("#addListToModal").html("<span>No Size group, Please Select Buyer</span>");
         $("#new_session_btn_id").hide();
         $('#se_id')
             .empty()
             .append('<option value="" selected="selected">Please Select Season</option>');
         $("#show_selected_size_group").html("");
     }
    $("#b_id").on("change",function(){

        $("#addListToModal").html("<span>No Size group, Please Select Buyer</span>");
        $("#show_selected_size_group").html("");
        
        var b_id = $(this).val();
        if(b_id != ''){
            // Action Element list
            $.ajax({
                url : "{{ url('merch/style/get_sz_grp_modal_data') }}",
                type: 'get',
                data: {b_id: b_id},
                success: function(data)
                {
                  
                    $("#show_selected_operations").html(data.opData);
                },
                error: function()
                {
                   // $("#addListToModal").html("<span>No Size group, Please Select Buyer</span>");
                }
            });
            $("#new_session_btn_id").show();
        } else {
            // $("#addListToModal").html("<span>No Size group, Please Select Buyer</span>");
            $("#new_session_btn_id").hide();
            $('#se_id')
                .empty()
                .append('<option value="" selected="selected">Please Select Buyer</option>');
            $("#show_selected_size_group").html("");
        }
    });

    $("#b_id").on("change",function(){

        var b_id = $(this).val();
        if(b_id != ''){
            // Action Element list
            $.ajax({
                url : "{{ url('merch/style/get_brands_data') }}",
                type: 'get',
                data: {b_id: b_id},
                success: function(data)
                {
                    $("#mr_brand_br_id").empty();
                    $("#mr_brand_br_id").append('<option value="">Select Brand</option>');
                    $.each(JSON.parse(data),function(key, value)
                    {
                        $("#mr_brand_br_id").append($('<option></option>').attr("value", key).text(value));
                    });
               },
                error: function()
                {
                }
            });
        } else {

        }
    });
    //buyer id and product id send
    $("#b_id").on("change",function(){

      var b_id=$(this).val();
      $("#size-group-buyer").attr('href', url+'/merch/setup/productsize?buyer='+b_id+'&&p_type=');

      $("#prd_type_id").on("change",function(){

          var p_id=$(this).val();

      $("#size-group-buyer").attr('href', url+'/merch/setup/productsize?buyer='+b_id+'&&p_type='+p_id);
      });

    });

    //only product id send

    $("#prd_type_id").on("change",function(){

          var p_id=$(this).val();

      $("#size-group-buyer").attr('href', url+'/merch/setup/productsize?buyer=&&p_type='+p_id);
    });

    //Show Selected Wash Type from Modal
    var sgmodal = $("#sizeGroupModal");
    $("body").on("click", "#sizeGroupModalDone", function(e) {
        var selectedData="";
        var selected_sizes = new Array();
        var selected_size_names = new Array();
        //-------- modal actions ------------------
        sgmodal.find('.modal-body input[type=checkbox]').each(function(i,v) {
            if ($(this).prop("checked") == true)
            {

                selected_sizes.push($(this).val());
                selected_size_names.push($(this).next().text());
            }
            console.log(selected_sizes);
        });
        if(selected_sizes.length>0){
        $.ajax({
                url : "{{ url('merch/style/get_sz_grp_details') }}",
                type: 'get',
                data: {selected_sizes: selected_sizes, names: selected_size_names},
                success: function(data)
                {
                  //console.log('ok7');
                    $("#show_selected_size_group").empty();
                    $("#show_selected_size_group").html(data);
                }
            });
          }else{
            $("#show_selected_size_group").empty();
          }
        sgmodal.modal('hide');
    });

    //Show Selected Wash Type from Modal
    var wmodal = $("#washTypeSelectModal");
    $("body").on("click", "#washTypeSelectModalDone", function(e) {
        var data="";
        var tr_end = 0;
        //-------- modal actions ------------------
        data += '<table class="table table-bordered" style="margin-bottom:0px;">';
        // data += '<thead>';
        // data += '<tr>';
        // data += '<td colspan="3" class="text-center">Wash</td>';
        // data += '</tr>';
        // data += '</thead>';
        data += '<tbody>';
        wmodal.find('.modal-body input[type=checkbox]').each(function(i,v) {
            if ($(this).prop("checked") == true) {
                if((i/10) % 1 === 0) {
                    data += '<tr>';
                    tr_end = i+9;
                }
                data += '<td style="border-bottom: 1px solid lightgray;">'+$(this).next().text()+'</td>';
                data+= '<input type="hidden" name="wash[]" class="washType" value="'+$(this).val()+'"></input>';
                if(tr_end == 10) {
                    data += '</tr>';
                }
            }
        });
        data += '</tbody>';
        data += '</table>';
        wmodal.modal('hide');
        $("#show_selected_wash_type").html(data);
    });

    //Show Selected Operations from Modal
    var modal = $("#operationModal");
    $("body").on("click", "#operationModalDone", function(e) {
        var data="";
        var tr_end = 0;
        //-------- modal actions ------------------
        data += '<table class="table table-bordered" style="margin-bottom:0px;">';
        // data += '<thead>';
        // data += '<tr>';
        // data += '<td colspan="3" class="text-center">Operations</td>';
        // data += '</tr>';
        // data += '</thead>';
        data += '<tbody>';
        modal.find('.modal-body input[type=checkbox]').each(function(i,v) {

            if ($(this).prop("checked") == true) {
                if((i/10) % 1 === 0) {
                    data += '<tr>';
                    tr_end = i+9;
                }
                //console.log($(this).next().text().toLowerCase());
                if($(this).next().text().toLowerCase() == 'wash'){
                    $(".wash").show();
                    //console.log('newop');
                }
                data += '<td style="border-bottom: 1px solid lightgray;">'+$(this).next().text()+'</td>';
                data+= '<input type="hidden" name="opr_id[]" value="'+$(this).val()+'"></input>';
                data+= '<input type="hidden" name="opr_type[]" value="'+$(this).data('content-type')+'"></input>';
                if(tr_end == 10) {
                    data += '</tr>';
                }

                // data+= '<button type="button" class="btn btn-sm" style="margin:2px; padding:2px;">'+$(this).next().text()+'</button>';
            }
        });
        data += '</tbody>';
        data += '</table>';
        modal.modal('hide');
        $("#show_selected_operations").html(data);
    });

    //Show Selected Machine from Modal
    var smodal = $("#specialMachineModal");
    $("body").on("click", "#specialMachineModalDone", function(e) {
        var data="";
        var tr_end = 0;
        //-------- modal actions ------------------
        data += '<table class="table table-bordered" style="margin-bottom:0px;">';
        // data += '<thead>';
        // data += '<tr>';
        // data += '<td colspan="3" class="text-center">Special Machines</td>';
        // data += '</tr>';
        // data += '</thead>';
        data += '<tbody>';
        smodal.find('.modal-body input[type=checkbox]').each(function(i,v) {

            if ($(this).prop("checked") == true) {
                if((i/10) % 1 === 0) {
                    data += '<tr>';
                    tr_end = i+9;
                }
                //console.log();
                data += '<td style="border-bottom: 1px solid lightgray;">'+$(this).next().text()+'</td>';
                data+= '<input type="hidden" name="machine_id[]" value="'+$(this).val()+'"></input>';
                data+= '<input type="hidden" name="opr_type[]" value="'+$(this).data('content-type')+'"></input>';
                if(tr_end == 10) {
                    data += '</tr>';
                }
                // data+= '<button type="button" class="btn btn-sm" style="margin:2px; padding:2px;">'+$(this).next().text()+'</button>';
            }
        });
        data += '</tbody>';
        data += '</table>';
        smodal.modal('hide');
        $("#show_selected_machines").html(data);
    });
    //Add More  buyer modal
    var data_b = $("#contactPersonData").html();
    $('body').on('click', '.AddBtn_bu', function(){
        $("#contactPersonData").append(data_b);
    });

    $('body').on('click', '.RemoveBtn_bu', function(){
        $(this).parent().parent().parent().remove();
     });


    // Sample type Based On Buyer

    var basedon = $("#b_id");
     var action_element=$("#sample-checkbox");
     var action_season=$("#se_id");
     var action_size=$("#prdsz_id");
     if($("#b_id").val()){
     $.ajax({
         url : "{{ url('merch/style/sample_season') }}",
         type: 'get',
         dateType: 'JSON',
         data: {b_id : $("#b_id").val()},
         success: function(response)
         {
             action_element.html(response.samplelist);
             action_season.html(response.selist);
             action_size.html(response.sizelist);
         },
         error: function()
         {
             alert('failed...');
         }
     })
   }
      basedon.on("change", function(){

        // Sample  list
        $.ajax({
            url : "{{ url('merch/style/sample_season') }}",
            type: 'get',
            dateType: 'JSON',
            data: {b_id : $(this).val()},
            success: function(data)
            {
                action_element.html(data.samplelist);
                action_season.html(data.selist);
                action_size.html(data.sizelist);
            },
            error: function()
            {
                alert('failed...');
            }
        });

    });

    //Add More size group in form
    var data_s = $("#size-add").html();
        $('body').on('click', '.AddBtn_size_s', function(){
            $("#size-add").append(data_s);
        });

       $('body').on('click', '.RemoveBtn_size_s', function(){
        $(this).parent().parent().parent().remove();
    });

        //Add More size group in modal
        var data = $('.AddBtn_size').parent().parent().parent().parent().html();
        $('body').on('click', '.AddBtn_size', function(){
            $('.addRemove').append("<div>"+data+"</div>");
        });

        $('body').on('click', '.RemoveBtn_size', function(){
            $(this).parent().parent().parent().parent().remove();
        });
        //Add More wash in form
        var data_w = $("#wash-add").html();
        $('body').on('click', '.AddBtn_wash', function(){
            $("#wash-add").append(data_w);
        });

       $('body').on('click', '.RemoveBtn_wash', function(){
        $(this).parent().parent().parent().remove();
        });

        // Product Type  Add through ajax

    $('#new_product').on('show.bs.modal', function (e) {
        var modal = $(this);
        var button = $(e.relatedTarget);

        $("#newProdTypeFrm").on("submit", function(e) {
            e.preventDefault();
              var prod_name = $("#prd_type_name").val();
             var that = $(this);

            // Product insert url
            $.ajax({
                url : "{{ url('merch/setup/product_type_store') }}",
                type: 'post',
                data: {
                   prd_type_name: prod_name,
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data)
                {
                    modal.find(".message").html("<div class='alert alert-success'>Wash Successfully saved</div>");

                    // Ajax call for product list if Successfulily saved
                    $.ajax({
                        url : "{{ url('merch/style/product') }}",
                        type: 'get',
                        data: {},
                        success: function(data)
                        {
                           button.parent().prev().find("#prd_type_id").html(data);
                           modal.modal('hide');
                           that.unbind('submit');
                        },
                        error: function()
                        {
                            alert('failed...');
                        }
                    });
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });
    });

    //new garments type
    $('#new_garments_type').on('show.bs.modal', function (e) {
        var modal = $(this);
        var button = $(e.relatedTarget);
        var prd_id = $('#prd_type_id').val();
        $('#modal_prd_type_id').val(prd_id).trigger('change');

        $("#newGmTypeFrm").on("submit", function(e) {
            e.preventDefault();
              var prod_name = $("#prd_type_name").val();
             var that = $(this);

            // Product insert url
            $.ajax({
                url : "{{ url('merch/setup/garments_type_store') }}",
                type: 'post',
                data: {
                   prd_type_id: $('#modal_prd_type_id').val(),
                   gmt_name: $('#gmt_name').val(),
                   gmt_remarks: $('#gmt_remarks').val(),
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data)
                {

                    toastr.success(' ','Saved Successfully.');
                    $('#gmt_name').val('');
                    modal.modal('hide');

                    // Ajax call for product list if Successfulily saved
                    $.ajax({
                        url : "{{ url('merch/style/sample_garments') }}",
                        type: 'get',
                        data: {prd_id:$('#prd_type_id').val()},
                        success: function(data)
                        {
                          // console.log(data);
                           $('#gmt_id').html(data.gmlist);
                           $('#gmt_id').val(data.lastGm).trigger('change');
                           that.unbind('submit');
                        },
                        error: function()
                        {
                            alert('failed...');
                        }
                    });
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });
    });

    // Season Add through ajax
    $('#new_season').on('show.bs.modal', function (e) {
        var modal = $(this);
        var button = $(e.relatedTarget);

        $("#newSeasonFrm").on("submit", function(e) {
            e.preventDefault();
            var action_place = $("#se_id");
            var buyr = $("#b_id").val();
            var se_name = $("#se_name").val();
            var se_mm_start  = $("#se_mm_start").val();
            var se_mm_end    = $("#se_mm_end").val();

             var that = $(this);

            // Product insert url
            $.ajax({
                url : "{{ url('merch/setup/season_store') }}",
                type: 'post',
                data: {
                    se_name    : se_name,
                    b_id       : buyr,
                    se_mm_start: se_mm_start,
                    se_mm_end  : se_mm_end
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data)
                {
                    // console.log(data);
                    modal.find(".message").html("<div class='alert alert-success'>Session Successfully saved</div>");

                    // Ajax call for product list if Successfulily saved
                    $.ajax({
                        url : "{{ url('merch/style/season') }}",
                        type: 'get',
                        data: {b_id : buyr},
                        success: function(data)
                        {
                           console.log(data);
                           button.parent().prev().find("#se_id").html(data);
                           modal.modal('hide');
                           that.unbind('submit');
                        },
                        error: function()
                        {
                            alert('failed...');
                        }
                    });
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });
    });

    // Wash Type  Add through ajax

    $('#newWashModal').on('show.bs.modal', function (e) {
        var modal = $(this);
        var button = $(e.relatedTarget);
        $("#newWashFrm").on("submit", function(e) {
            e.preventDefault();
             var wash_name = $("#wash_name").val();
             var wash_rate = $("#wash_rate").val();
             var that = $(this);

            // Wash insert url
            $.ajax({
                url : "{{ url('merch/setup/wash_type') }}",
                type: 'post',
                data: {
                    wash_name: wash_name,
                    wash_rate: wash_rate
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data)
                {
                    modal.find(".message").html("<div class='alert alert-success'>Wash Successfully saved</div>");

                    // Ajax call for wash list if Successfulily saved
                    $.ajax({
                        url : "{{ url('merch/style/wash') }}",
                        type: 'get',
                        data: {},
                        success: function(data)
                        {
                           // button.parent().prev().find("#washStoreDiv").html(data);
                           $("#washStoreDiv").html(data);
                           modal.modal('hide');
                           that.unbind('submit');
                        },
                        error: function()
                        {
                            alert('failed...');
                        }
                    });
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });
    });



    // Buyer Add through ajax
    $('#new_buyer').on('show.bs.modal', function (e) {
        var modal = $(this);
        var button = $(e.relatedTarget);
        $("#newBuyerFrm").on("submit", function(e) {

            e.preventDefault();
             var march_buyer_name = $("#march_buyer_name").val();
             var march_buyer_short_name = $("#march_buyer_short_name").val();
             var country = $("#country").val();
             var march_buyer_address = $("#march_buyer_address").val();

             var that = $(this);

             var march_buyer_contact = new Array();
                $('textarea[name="march_buyer_contact[]"]').each(function(){
                   march_buyer_contact.push($(this).val());
                });

            // Buyer insert url
            $.ajax({
                url : "{{ url('merch/setup/buyerinfostore') }}",
                type: 'post',
                data: {
                    march_buyer_name    : march_buyer_name,
                    march_buyer_short_name  : march_buyer_short_name,
                    country: country,
                    march_buyer_address : march_buyer_address,
                    march_buyer_contact : march_buyer_contact

                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data)
                {
                    console.log(data);
                    modal.find(".message").html("<div class='alert alert-success'>Buyer Successfully saved</div>");

                    // Ajax call for Buyer list if Successfulily saved
                    $.ajax({
                        url : "{{ url('merch/style/buyerlist') }}",
                        type: 'get',
                        data: {},
                        success: function(data)
                        {
                           button.parent().prev().find("#b_id").html(data);
                           modal.modal('hide');
                           that.unbind('submit');
                        },
                        error: function()
                        {
                            alert('failed...');
                        }
                    });
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });
    });

    //product type on change garment type change

    $('#prd_type_id').on("change", function(){

        // Sample  list
        $.ajax({
            url : "{{ url('merch/style/sample_garments') }}",
            type: 'get',
            dateType: 'JSON',
            data: {prd_id : $(this).val()},
            success: function(data)
            {

                $('#gmt_id').html(data.gmlist);

            },
            error: function()
            {
                alert('failed...');
            }
        });

        if($('#prd_type_id').val()!='')
        {
          $('#new_garments_type_btn_id').show();
        }
        else{
          $('#new_garments_type_btn_id').hide();
        }

    });
});

// Image preview
  var loadFile = function(event) {
    var output = document.getElementById('imagepreview');
    output.src = URL.createObjectURL(event.target.files[0]);
  };

$(document).ready(function(){
    $('#stl_no').blur(function(){
        //
        var stl = $('#stl_no').val();
        var _token = $('input[name="_token"]').val();

        if(stl != '' && stl != null){
            $.ajax({
                url:"{{URL::to('/merch/style/check-style-no')}}",
                method:"POST",
                data:{stl_no:stl, _token:_token},
                success:function(result)
                {
                    if(result === 'no'){
                       $('#error_stl_no').html('<label class="control-label status-label" for="inputSuccess">Style number available</label>');
                        $('#style-form').removeClass('has-error');
                        $('#style-form').addClass('has-success');
                    }else{
                        $('#error_stl_no').html('<label class="control-label status-label" for="inputError">Style number not available</label>');
                        $('#style-form').removeClass('has-success');
                        $('#style-form').addClass('has-error');
                    }

                }
            });
        }
    });
});
</script>

<script type="text/javascript">
    function appendImageInput(image_load_id) {
      var appendNum = $("#multi-image-div > .multi-image").length;
      var appednDiv = '<div class="col-sm-2 multi-image">\
                          <button title="Remove this image!" type="button" class="fa fa-close close-button" onclick="$(this).parent().remove();"></button>\
                          <label class="slide_upload" for="file_image_'+appendNum+'">\
                            <img id="imagepreview_'+appendNum+'" src="{{asset('assets/files/style/placeholder.png')}}">\
                          </label>\
                          <input type="file" class="multi-image-input" id="file_image_'+appendNum+'" name="style_img_multi[]" onchange="readURL(this,this.id)" style="display:none">\
                      </div>';
      var nextDiv = $('#'+image_load_id).parent().next().attr('class');
      //console.log(nextDiv);
      if(typeof nextDiv === 'undefined'){
        $("#multi-image-div").append(appednDiv);
      }
    }
    function readURL(input,image_load_id) {
      var target_image='#'+$('#'+image_load_id).prev().children().attr('id');
        var filePath = input.files[0].name;
        var fileExtension = ['jpeg', 'jpg', 'png'];
        if ($.inArray(filePath.split('.').pop().toLowerCase(), fileExtension) == -1) {
            alert("Only '.jpeg','.jpg', '.png' formats are allowed.");
        }else{
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $(target_image).attr('src', e.target.result);
                }
                //console.log(image_load_id);
                reader.readAsDataURL(input.files[0]);
                appendImageInput(image_load_id); //append new image div
            }
        }

    }
</script>

@endpush
@endsection
