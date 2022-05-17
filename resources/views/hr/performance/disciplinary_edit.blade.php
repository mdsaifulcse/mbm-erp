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
                    <a href="#">Performance </a>
                </li>
                <li >
                	<a href="#"> Operation </a>
                </li>
                <li class="active"> Update Disciplinary Record</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <div class="page-header">
                <h1>Performance <small> <i class="ace-icon fa fa-angle-double-right"></i> Operation <i class="ace-icon fa fa-angle-double-right"></i> Update Disciplinary Record </small></h1>
            </div>
            <div class="panel panel-default">

                <div class="row">
                    @include('inc/message')
                    <div class="col-xs-12">
                        {{ Form::open(['url'=>'hr/performance/operation/disciplinary_edit', 'class'=>'form-horizontal']) }} 
                    <div class="col-xs-offset-3 col-xs-6">
                        <!-- PAGE CONTENT BEGINS -->
                        </br>

                            <input type="hidden" name="dis_re_id" value="{{ $record->dis_re_id }}">
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="dis_re_offender_id"> Offender ID </label>
                                <div class="col-sm-8">
                                    {{ Form::select('dis_re_offender_id', [$record->dis_re_offender_id => $record->offender], $record->dis_re_offender_id, ['placeholder'=>'Select Offender\'s Name or ID', 'id'=>'dis_re_offender_id', 'class'=> 'associates no-select col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Offender\'s ID field is required']) }} 
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="dis_re_griever_id"> Griever ID (Optional) </label>
                                <div class="col-sm-8">
                                    {{ Form::select('dis_re_griever_id', [$record->dis_re_griever_id => $record->griever], $record->dis_re_griever_id, ['placeholder'=>'Select Associate\'s ID', 'id'=>'dis_re_griever_id', 'class'=> 'associates no-select col-xs-12', 'data-validation'=>'required', 'data-validation-optional' => 'true']) }}  

                                </div>
                            </div> 

    						<div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="dis_re_discussed_date"> Discussed Date </label>
                                <div class="col-sm-8">
                                    <input type="text" name="dis_re_discussed_date" id="dis_re_discussed_date" placeholder="Discussed Date" class="datepicker col-xs-12" data-validation="required" value="{{ $record->dis_re_discussed_date }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="dis_re_issue_id">Reason</label>
                                <div class="col-sm-8">
                                    {{ Form::select('dis_re_issue_id', $issueList, $record->dis_re_issue_id, ['placeholder'=>'Select Reason', 'id'=>'dis_re_issue_id', 'class'=> 'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Reason field is required']) }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="dis_re_req_remedy"> Requested Remedy </label>
                                <div class="col-sm-8">
                                    <textarea name="dis_re_req_remedy" id="dis_re_req_remedy" class="col-xs-12" placeholder="Requested Remedy"  data-validation="required length" data-validation-length="2-255" data-validation-allowing=" -" data-validation-error-msg="The Requested Remedy has to be an alphanumeric value between 2-255 characters">{{ $record->dis_re_req_remedy }}</textarea>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="dis_re_ac_step_id">Action Steps</label>
                                <div class="col-sm-8">
                                    {{ Form::select('dis_re_ac_step_id', $stepList, $record->dis_re_ac_step_id, ['placeholder'=>'Select Action Step', 'id'=>'dis_re_ac_step_id', 'class'=> 'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Action Step field is required']) }}
                                </div>
                            </div>

                            <div class="form-group">
    							<label class="col-sm-4 control-label no-padding-right">Date of Execution</label>
    							<div class="col-sm-8">
    								<div class="col-sm-6 no-padding-left input-icon">
    									<input type="text" name="dis_re_doe_from"  id="dis_re_doe_from" class="datepicker col-sm-12" placeholder="Execution From" data-validation="required" value="{{ $record->dis_re_doe_from }}">
    								</div>

    								<div class="col-sm-5 no-padding-right no-padding-left input-icon input-icon-right">
    									<input type="text" name="dis_re_doe_to" id="dis_re_doe_to" class="datepicker col-sm-12" placeholder="Executed To" data-validation="required" value="{{ $record->dis_re_doe_to }}">
    								</div>
    							</div>
    						</div>            

                            
                        <!-- PAGE CONTENT ENDS -->
                    </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-12">
                            
                            <div class="clearfix form-actions">
                                <div class="col-md-offset-4 col-md-4 text-center" style="padding-left: 38px;">
                                    <button class="btn btn-sm btn-success" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i> Update
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
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
$(document).ready(function()
{   
    $('select.associates').select2({
        
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


     //date validation------------------
    $('#dis_re_doe_from').on('dp.change',function(){
        $('#dis_re_doe_to').val($('#dis_re_doe_from').val());    
    });

    $('#dis_re_doe_to').on('dp.change',function(){
        var end     = new Date($(this).val());
        var start   = new Date($('#dis_re_doe_from').val());
        if(start == '' || start == null){
            alert("Please enter From-Date first");
            $('#dis_re_doe_to').val('');
        }
        else{
             if(end < start){
                alert("Invalid!!\n From-Date is latest than To-Date");
                $('#dis_re_doe_to').val('');
            }
        }
    });
    //date validation end---------------
});
</script>
@endsection