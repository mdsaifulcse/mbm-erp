@extends('hr.layout')
@section('title', '')
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
					<a href="#">Recruitment</a>
				</li>
				<li>
					<a href="#">Job Portal</a>
				</li>
				<li class="active">Interview Notes</li>
			</ul><!-- /.breadcrumb -->

			{{-- <div class="nav-search" id="nav-search">
				<form class="form-search">
					<span class="input-icon">
						<input type="text" placeholder="Search ..." class="nav-search-input" id="nav-search-input" autocomplete="off" />
						<i class="ace-icon fa fa-search nav-search-icon"></i>
					</span>
				</form>
			</div> --}}<!-- /.nav-search -->
		</div>

		<div class="page-content"> 
                @include('inc/message')
            
            <div class="panel panel-info">
                <div class="panel-heading"><h6>Interview Notes<a href="{{ url('hr/recruitment/job_portal/interview_notes_list')}}" class="pull-right btn btn-xx btn-info">Interview Notes list</a></h6></div> 
                  <div class="panel-body">
                    <div class="row">
                          <!-- Display Erro/Success Message -->
                        <div class="col-xs-offset-3 col-xs-6">
                            <!-- PAGE CONTENT BEGINS -->
                            <!-- <h1 align="center">Add New Employee</h1> -->
                            </br>
                            <form class="form-horizontal" role="form" method="post" action="{{ url('hr/recruitment/job_portal/interview_notes') }}" enctype="multipart/form-data">
                            {{ csrf_field() }} 

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="hr_interview_date"> Interview  Date <span style="color: red; vertical-align: top;">&#42;</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="hr_interview_date" placeholder="Y-m-d" class="datepicker col-xs-12" data-validation="required"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="hr_interview_name"> Interviewee Name <span style="color: red; vertical-align: top;">&#42;</span></label>
                                    <div class="col-sm-8">
                                        <input name="hr_interview_name" type="text" id="as_name" placeholder="Interviewee's Name" class="col-xs-12" data-validation="required length custom" data-validation-length="3-64" data-validation-error-msg="Interviewee name has to be an alphabetic value between 3-64 characters" />
                                    </div>
                                </div> 
                      

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="hr_interview_contact"> Contact Number <span style="color: red; vertical-align: top;">&#42;</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="hr_interview_contact" placeholder="Reference Contact" class="col-xs-12" data-validation="length number required" data-validation-length="1-11" data-validation-error-msg="Contact number is required" />
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="hr_interview_exp_salary"> Expected Salary <span style="color: red; vertical-align: top;">&#42;</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="hr_interview_exp_salary" placeholder="Expected Salary" class="col-xs-12" data-validation="required length number" data-validation-length="1-10" data-validation-error-msg="Expected salary is required"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="hr_interview_board_member"> Board Members <span style="color: red; vertical-align: top;">&#42;</span></label>
                                    <div class="col-sm-8">
                                        <textarea type="text" name="hr_interview_board_member" placeholder="Board Members" class="col-xs-12" data-validation="required length" data-validation-length="1-255" data-validation-error-msg="Board members name is required"></textarea> 
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="hr_interview_note"> Notes</label>
                                    <div class="col-sm-8">
                                        <textarea type="text" name="hr_interview_note" placeholder="notes" class="col-xs-12" data-validation="length" data-validation-length="0-255"></textarea> 
                                    </div>
                                </div>
                        </div>


                        <div class="col-xs-12">        
                                <div class="clearfix form-actions" >
                                    <div class="col-md-offset-4 col-md-4 text-center" style="padding-left: 30px;"> 
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
                                <hr /> 
                            </form> 
                            <!-- PAGE CONTENT ENDS -->
                        </div>
                        <!-- /.col -->
                    </div>
                </div>
            </div>
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