@extends('hr.layout')
@section('title', 'Add Role')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Payroll</a>
                </li>
                <li class="active">Edit OT</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content"> 
            <div class="page-header">
                <h1>Payroll<small> <i class="ace-icon fa fa-angle-double-right"></i> Edit OT</small></h1>
            </div>
            @include('inc/message')
            <div class="panel panel-default">
                <div class="row" style="padding-top: 20px;">
                    <!-- Display Erro/Success Message -->
                    
                        <!-- PAGE CONTENT BEGINS -->
                        <form class="form-horizontal" role="form" method="post" action="{{ url('hr/payroll/ot_update') }}" enctype="multipart/form-data">
                            {{ csrf_field() }} 
                    <div class="col-xs-offset-3 col-xs-6">
                            <input type="hidden" name="hr_ot_id" value="{{ $ot->hr_ot_id }}">

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="hr_ot_as_id"> Associate's ID <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                    {{ Form::select('hr_ot_as_id', [$ot->hr_ot_as_id=>$ot->hr_ot_as_id], $ot->hr_ot_as_id, ['placeholder'=>'Select Associate\'s ID', 'id'=>'hr_ot_as_id', 'class'=> 'associates no-select col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Associate\'s ID field is required']) }}  

                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="hr_ot_date"> Date <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="hr_ot_date" name="hr_ot_date" value="{{ $ot->hr_ot_date }}" class="col-xs-12 datepicker" data-validation="required"/>

                                </div>
                            </div>
       

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="hr_ot_hour"> OT Hour <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                    <input type="text" max="8" id="hr_ot_hour" name="hr_ot_hour" value="{{ $ot->hr_ot_hour }}" placeholder="1-8" class="col-xs-12" data-validation="number required" data-validation-allowing="range[1;8]"/>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="hr_ot_remarks"> Remarks </label>
                                <div class="col-sm-8">
                                    <textarea name="hr_ot_remarks" id="hr_ot_remarks" class="col-xs-12" data-validation="length" data-validation-length="1-128" data-validation-optional="true" placeholder="Remarks">{{ $ot->hr_ot_remarks }}</textarea>
                                </div>
                            </div>
                    </div>
                    <div class="col-xs-12">

                            <div class="space-4"></div>
                            <div class="space-4"></div>
                            <div class="space-4"></div>
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

                            <!-- /.row -->


                        <!-- PAGE CONTENT ENDS -->
                    </div>
                        </form>
                    <!-- /.col -->
                </div>
            </div>
            <br>
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
$(document).ready(function()
{   
    $('select.associates').select2({
        placeholder: 'Select Associate\'s ID',
        ajax: {
            url: '{{ url("hr/associate-search") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { 
                    keyword: params.term
                }; 
            },
            processResults: function (data) { 
                return {
                    results:  $.map(data, function (item) {
                        return {
                            text: item.associate_name,
                            id: item.associate_id
                        }
                    }) 
                };
          },
          cache: true
        }
    }); 
});
</script>


@endsection