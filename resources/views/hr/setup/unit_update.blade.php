@extends('hr.layout')
@section('title', '')
@section('main-content')
<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
			<ul class="breadcrumb">
				<li>
					<i class="ace-icon fa fa-home home-icon"></i>
					<a href="#"> Human Resource </a>
				</li> 
				<li>
					<a href="#"> Setup </a>
				</li>
				<li class="active"> Unit Update</li>
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content"> 
            <div class="page-header">
				<h1>Setup <small><i class="ace-icon fa fa-angle-double-right"></i> Unit Update </small></h1>
            </div>

            <div class="row">
                  <!-- Display Erro/Success Message -->
                @include('inc/message')
                
                <div class="col-sm-offset-3 col-sm-6">
                    <!-- PAGE CONTENT BEGINS -->
                    <!-- <h1 align="center">Add New Employee</h1> -->
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('hr/setup/unit_update')  }}" enctype="multipart/form-data">
                    {{ csrf_field() }} 
                        <input type="hidden" name="hr_unit_id" value="{{ $unit->hr_unit_id }}"/>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="hr_unit_name" > Unit Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-9">
                                <input type="text" id="hr_unit_name" name="hr_unit_name" placeholder="Unit name" class="col-xs-12" value="{{ $unit->hr_unit_name }}" data-validation="required length custom" data-validation-length="1-128"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="hr_unit_short_name" > Unit Short Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-9">
                                <input type="text" id="hr_unit_short_name" name="hr_unit_short_name" placeholder="Unit short name" class="col-xs-12" value="{{ $unit->hr_unit_short_name }}" data-validation="required length custom" data-validation-length="1-20"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="hr_unit_name_bn" > ইউনিট (বাংলা) </label>
                            <div class="col-sm-9">
                                <input type="text" id="hr_unit_name_bn" name="hr_unit_name_bn" placeholder="ইউনিটের নাম" class="col-xs-12" value="{{ $unit->hr_unit_name_bn }}" data-validation="length" data-validation-length="0-255" data-validation-error-msg="সঠিক নাম দিন"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="hr_unit_address" > Unit Adrress </label>
                            <div class="col-sm-9">
                                <input type="text" id="hr_unit_address" name="hr_unit_address" placeholder="Unit name" value="{{ $unit->hr_unit_address }}" class="col-xs-12" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="hr_unit_address_bn" > ইউনিট ঠিকানা (বাংলা) </label>
                            <div class="col-sm-9">
                                <input type="text" id="hr_unit_address_bn" name="hr_unit_address_bn" placeholder="ইউনটের ঠিকানা (বাংলা)" class="col-xs-12" value="{{ $unit->hr_unit_address_bn }}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="hr_unit_code"> Unit Code </label>
                            <div class="col-sm-9">
                                <input type="text" id="hr_unit_code" name="hr_unit_code" placeholder="Unit code" class="col-xs-12" value="{{ $unit->hr_unit_code }}" data-validation="length" data-validation-length="0-10"/>
                            </div>
                        </div>



                        <div class="form-group" style="padding-top: 10px;">
                            <label class="col-sm-3 col-xs-3 control-label no-padding-right no-padding-top" for="hr_unit_logo">Logo <br><span>(jpg|jpeg|png) <br> Max Size: 200KB<br> Dimention: (148x248)px</span></label>

                            <div class="col-sm-2">
                                <img id="avatar" style="width: 100%;" class="img-responsive" alt="profile picture" src="{{ url($unit->hr_unit_logo?$unit->hr_unit_logo:'null') }}" />
                                <input type="hidden" name="old_pic" value="{{ $unit->hr_unit_logo }}">
                            </div>
                            <div class="col-sm-2">
                                <input name="hr_unit_logo" type="file" 
                                class="dropZone"
                                data-validation="mime size dimension" data-validation-dimension="min248x148"
                                data-validation-allowing="jpeg,png,jpg"
                                data-validation-max-size="200kb"
                                data-validation-error-msg-size="You can not upload images larger than 200kb"
                                data-validation-error-msg-mime="You can only upload jpeg, jpg or png images">
                            </div>
                        </div>

                          <div class="form-group">
                            <label class="col-sm-3 col-xs-3 control-label no-padding-right" for="hr_unit_authorized_signature">Signature <br><span> (jpg|jpeg|png) Max Size: 80kB<br> Dimension: (120x80)px</span></label>
                             <div class="col-sm-2">
                                <img id="avatar" style="width: 100%;" class="img-responsive" alt="signature" src="{{ url($unit->hr_unit_authorized_signature?$unit->hr_unit_authorized_signature:'null') }}" />
                                <input type="hidden" name="old_signature" value="{{ $unit->hr_unit_authorized_signature }}">
                            </div>    
                            <div class="col-sm-2">
                                <input name="hr_unit_authorized_signature" type="file" 
                                class="dropZone"
                                data-validation="mime size dimension" data-validation-dimension="min120x80"
                                data-validation-allowing="jpeg,png,jpg"
                                data-validation-max-size="80kb"
                                data-validation-error-msg-size="You can not upload images larger than 80kB"
                                data-validation-error-msg-mime="You can only upload jpeg, jpg or png images">
                            </div>
                        </div>


                        
                    <!-- PAGE CONTENT ENDS -->
                </div>
                <div class="col-sm-12 col-xs-12">
                        <div class="space-4"></div>
                        <div class="space-4"></div>
                        <div class="clearfix form-actions">
                            <div class="col-md-offset-4 col-md-4 text-center"> 
                                <button class="btn btn-sm btn-success" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                </button>
                                &nbsp; &nbsp; &nbsp;
                                <button class="btn btn-sm" type="reset">
                                    <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                </button>
                            </div>
                        </div>
                </div>
                    </form> 
            </div>
		</div><!-- /.page-content -->
	</div>
</div>

@endsection