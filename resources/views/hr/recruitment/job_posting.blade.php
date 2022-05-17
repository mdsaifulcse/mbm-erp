@extends('hr.layout')
@section('title', '')
@section('main-content')
<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
			<ul class="breadcrumb">
                <li>
                   <a href="/"><i class="ace-icon fa fa-home home-icon"></i>Human Resource</a> 
                </li>
                <li>
                    <a href="#">Recruitment</a>
                </li>
                <li>
                    <a href="#">Job Portal</a>
                </li>  
                <li class="active">Job Posting</li>
			</ul><!-- /.breadcrumb -->
		</div>

		<div class="page-content">
			<div class="panel panel-info">
                <div class="panel-heading"><h6>Job Posting<a href="{{ url('hr/recruitment/job_portal/job_posting_list')}}" class="pull-right btn btn-xx btn-info">Job Posting list</a></h6></div> 
                  <div class="panel-body">                  
					<div class="row">
						<!-- Display Erro/Success Message -->
		                @include('inc/message')

						<div class="col-xs-12" style="padding-left: 21px;">
							<!-- PAGE CONTENT BEGINS --> 
		                    <br>
		                    <form class="form-horizontal" role="form" method="post" action="#" enctype="multipart/form-data">
		                    	{{ csrf_field() }}

								<div class="form-group">
									<label class="col-sm-2 control-label no-padding-right" for="job_po_title"> Job title <span style="color: red; vertical-align: top;">&#42;</span> </label>
									<div class="col-sm-9">
										<input type="text" name="job_po_title" placeholder="Job title" class="col-xs-10 col-sm-5" data-validation="required length custom"  data-validation-length="1-64" data-validation-error-msg="Job title required between 1-64 characters"/>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-2 control-label no-padding-right" for="job_po_vacancy"> Vacancy <span style="color: red; vertical-align: top;">&#42;</span> </label>
									<div class="col-sm-9">
										<input type="text" name="job_po_vacancy" placeholder="Vacancy" class="col-xs-10 col-sm-5" data-validation="required length number" data-validation-length="1-11" data-validation-error-msg="Vacancy is required in digits"/>
									</div>
								</div>
								<div class="form-group">
		                        <label class="col-sm-2 control-label no-padding-right" for="job_po_application_deadline"> Last Date of Application <span style="color: red; vertical-align: top;">&#42;</span> </label>
		                        <div class="col-sm-9">
		                            <input type="text" name="job_po_application_deadline" placeholder="Y-m-d" id="job_po_application_deadline" placeholder="Application deadline" class="col-xs-10 col-sm-5 datepicker" />
		                        </div>
		                    </div>
		                                    
		                        <div class="form-group">
									<label class="col-sm-2 control-label no-padding-right" for="job_po_description"> Job Description <span style="color: red; vertical-align: top;">&#42;</span> </label>
									<div class="col-sm-5">
										<textarea name="job_po_description" id="job_po_description" class="tinyMce" ></textarea>
									</div>
								</div>
		                                    

								<div class="form-group">
									<label class="col-sm-2 control-label no-padding-right" for="job_po_responsibility"> Job Responsibility <span style="color: red; vertical-align: top;">&#42;</span> </label>
									<div class="col-sm-5">
									  	<textarea name="job_po_responsibility" class="tinyMce" ></textarea>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-2 control-label no-padding-right" for="job_po_nature"> Job Nature <span style="color: red; vertical-align: top;">&#42;</span></label>
									<div class="col-sm-9">
										<div class="radio">
											<label>
												<input name="job_po_nature" type="radio" class="ace" value="1" data-validation="required" />
												<span class="lbl"> Full Time</span>
											</label>
										</div>
		                                <div class="radio">
											<label>
												<input name="job_po_nature" type="radio" class="ace" value="2"/>
												<span class="lbl"> Part Time</span>
											</label>
										</div>

										<div class="radio">
											<label>
												<input name="job_po_nature" type="radio" class="ace" value="3"/>
												<span class="lbl"> Contractual</span>
											</label>
									  	</div>
									</div>
								</div>


								<div class="form-group">
									<label class="col-sm-2 control-label no-padding-right" for="job_po_edu_req"> Educational Requirement <span style="color: red; vertical-align: top;">&#42;</span> </label>
									<div class="col-sm-5">
									  	<textarea name="job_po_edu_req" class="tinyMce" ></textarea>
									</div>
					      		</div>
		                    
		                                    
			                    <div class="form-group">
									<label class="col-sm-2 control-label no-padding-right" for=" "> Experience Requirement <span style="color: red; vertical-align: top;">&#42;</span></label>
									<div class="col-sm-5">
										<textarea name="job_po_experience" class="tinyMce" ></textarea>
									</div>
								</div>
		                                    
		                        <div class="form-group">
									<label class="col-sm-2 control-label no-padding-right" for="job_po_age_limit"> Age Limit <span style="color: red; vertical-align: top;">&#42;</span></label>
									<div class="col-sm-9">
									  	<input type="text" name="job_po_age_limit" placeholder="Age Limit 18-35" class="col-xs-10 col-sm-5" data-validation="required" data-validation-allowing="range[18;35]"/>
									</div>
								</div>


								<div class="form-group">
									<label class="col-sm-2 control-label no-padding-right" for="job_po_requirment"> Additional Requirement <span style="color: red; vertical-align: top;">&#42;</span> </label>
									<div class="col-sm-5">
										<textarea name="job_po_requirment" class="tinyMce" ></textarea>
									</div>
						     	</div>

								<div class="form-group">
									<label class="col-sm-2 control-label no-padding-right" for="job_po_location"> Job Location <span style="color: red; vertical-align: top;">&#42;</span></label>
									<div class="col-sm-9">
									  	<input type="text" name="job_po_location" placeholder="Job Location" class="col-xs-10 col-sm-5"  data-validation="required"/>
									</div>
						      	</div>
		                                    
		                        <div class="form-group">
									<label class="col-sm-2 control-label no-padding-right" for="job_po_salary"> Salary Range <span style="color: red; vertical-align: top;">&#42;</span></label>
									<div class="col-sm-9">
										<input type="text" name="job_po_salary" placeholder="Salary Range" class="col-xs-10 col-sm-5"  data-validation="required"/>
									</div>
								</div>
		                                    
								<div class="form-group">
									<label class="col-sm-2 control-label no-padding-right" for="job_po_benefits"> Other Benefits <span style="color: red; vertical-align: top;">&#42;</span> </label>
									<div class="col-sm-5">
									  	<textarea type="text" name="job_po_benefits" placeholder="Other Benefits" class="tinyMce" ></textarea>
									</div>
						      	</div>

						    	<div class="space-4"></div>
							   	<div class="space-4"></div>
							   	<div class="space-4"></div>
							   	<div class="space-4"></div>
							   	<div class="space-4"></div>

							   <div class="clearfix form-actions">
									<div class="col-md-offset-4 col-md-4 text-center">
										<button class="btn btn-sm btn-success" type="submit">
											<i class="ace-icon fa fa-check bigger-110"></i>Submit
										</button>
										&nbsp; &nbsp; &nbsp;
										<button class="btn btn-sm" type="reset">
											<i class="ace-icon fa fa-undo bigger-110"></i>Reset
										</button>
							     	</div>
						    	</div>
								<!-- /.row -->

								<hr />
										

		                    </form>

		                              <!-- PAGE CONTENT ENDS -->
						</div><!-- /.col -->
					</div><!-- /.row -->
				  </div>
			</div>

		</div><!-- /.page-content -->
	</div>
</div>


@endsection
