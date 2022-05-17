@extends('hr.layout')
@section('title', 'Holiday Edit')
@section('main-content')
@push('css')
    <link href="{{ asset('assets/css/jquery-ui.min.css') }}" rel="stylesheet">
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li> 
                <li>
                    <a href="#"> Operation </a>
                </li>
                <li class="#">Holiday Planner</li>
                <li class="active">Edit</li>
                <li class="top-nav-btn">
                    <a href="{{ url('hr/operation/holiday-planner')}}" class="pull-right btn btn-sm  btn-primary"> <i class="fa fa-list"></i> Holiday List</a>
                </li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <form id="formData" class="form-horizontal">
                
                <div class="panel panel-info">
                    <div class="panel-heading"><h6>Holiday Planner</h6></div> 
                    <div class="panel-body">
                        <div class="row justify-content-center">
                            <div class="col-sm-8">
                                <div class="form-section">
                                    <div class="row">
                                        <div class="col-sm-4 pr-0">
                                            <div class="form-group has-required has-float-label">
                                                <input type="date" name="hr_yhp_dates_of_holidays" id="holiday-date" placeholder="Cut of Date" value="{{ $day->hr_yhp_dates_of_holidays }}"  class="form-control" required>
                                                <label for="holiday-date">Holiday Date </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 pr-0">
                                            <div class="form-group has-required has-float-label">
                                                <input type="text" name="hr_yhp_comments" id="hr_yhp_comments" placeholder="Comment" value="{{ $day->hr_yhp_comments }}"  class="form-control" required @if($day->flag==0) readonly @endif>
                                                <label for="hr_yhp_comments">Remarks </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 pr-0">
                                            <div class="form-group has-float-label has-required select-search-group">
                                                {{ Form::select('hr_yhp_open_status', ['0'=>'Holiday', '1'=>'General', '2'=>'OT'],$day->hr_yhp_open_status, ['id'=>'day_type', 'class'=> 'daytype form-control select-search no-select','style', 'data-validation'=>'required']) }}
                                                <label for="day_type">Day Type</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 pr-0">
                                            <div class="form-group has-float-label">
                                                <input type="date" name="reference_date" id="ref_date" placeholder="Reference Date" value="{{ $day->reference_date }}"  class="form-control" >
                                                <label for="ref_date">Reference Date </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 pr-0">
                                            <div class="form-group has-float-label">
                                                <input type="text" name="reference_comment" id="reference_comment" placeholder="Comment" value="{{ $day->reference_comment }}"  class="form-control">
                                                <label for="reference_comment">Reference Comment </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 pr-0">
                                            <div class="form-group has-float-label has-required select-search-group">
                                                {{ Form::select('holiday_type', ['1'=>'Holiday', '2'=>'Festival'],$day->holiday_type, ['id'=>'holiday_type', 'class'=> ' form-control select-search no-select','style', 'data-validation'=>'required']) }}
                                                <label for="holiday_type">Holiday Type</label>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    {{-- <div class="row">
                                      <div class="col-sm-12"><hr class="mt-0"></div>
                                      <div class="col-sm-5">
                                        <div class="custom-control custom-switch">
                                          <input name="special" type="checkbox" class="custom-control-input" id="specialCheck">
                                          <label class="custom-control-label" for="specialCheck">Advanced</label>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="rule-section">
                                        <div class="iq-card-spacial pb-0">
                                            <div class="iq-sp-head">
                                                <p class="card-title">Special </p>
                                            </div>
                                            <div class="iq-sp-body pb-0">
                                               <div class="row">
                                                    <div class="offset-sm-3 col-sm-9">
                                                        <div class="specialsection" id="special-section">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="form-group has-required has-float-label select-search-group">
                                                                        <select name="" id="special-type-for" class="form-control">
                                                                            <option value=""> - Select - </option>
                                                                            <option value="as_department_id"> Department</option>
                                                                            <option value="as_designation_id"> Designation</option>
                                                                            <option value="as_section_id"> Section</option>
                                                                            <option value="as_subsection_id"> Sub Section</option>
                                                                            <option value="as_id"> Employee</option>
                                                                        </select>
                                                                        <label for="special-type-for">Type </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2" >
                                                                    <div class="form-group">
                                                                        <button class="btn btn-outline-primary sync-type" data-category="special" type="button" id="special-sync-type">
                                                                            <i class="las la-sync"></i>
                                                                        </button> 
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="special-targettype"></div>
                                                <div id="special-appendType" class="appendType"></div>
                                            </div>
                                        </div><div class="iq-card-spacial pb-0">
                                            <div class="iq-sp-head">
                                                <p class="card-title">Partial </p>
                                            </div>
                                            <div class="iq-sp-body pb-0">
                                               <div class="row">
                                                    <div class="offset-sm-3 col-sm-9">
                                                        <div class="partialsection" id="partial-section">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="form-group has-required has-float-label select-search-group">
                                                                        <select name="" id="partial-type-for" class="form-control">
                                                                            <option value=""> - Select - </option>
                                                                            <option value="as_department_id"> Department</option>
                                                                            <option value="as_designation_id"> Designation</option>
                                                                            <option value="as_section_id"> Section</option>
                                                                            <option value="as_subsection_id"> Sub Section</option>
                                                                            <option value="as_id"> Employee</option>
                                                                        </select>
                                                                        <label for="partial-type-for">Type </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2" >
                                                                    <div class="form-group">
                                                                        <button class="btn btn-outline-primary sync-type" data-category="partial" type="button" id="partial-sync-type">
                                                                            <i class="las la-sync"></i>
                                                                        </button> 
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="partial-targettype"></div>
                                                <div id="partial-appendType" class="appendType"></div>
                                            </div>
                                        </div>
                                        <div class="iq-card-spacial pb-0">
                                            <div class="iq-sp-head">
                                                <p class="card-title">Excluding </p>
                                            </div>
                                            <div class="iq-sp-body pb-0">
                                               <div class="row">
                                                    <div class="offset-sm-3 col-sm-9">
                                                        <div class="excludingsection" id="excluding-section">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="form-group has-required has-float-label select-search-group">
                                                                        <select name="" id="excluding-type-for" class="form-control">
                                                                            <option value=""> - Select - </option>
                                                                            <option value="as_department_id"> Department</option>
                                                                            <option value="as_designation_id"> Designation</option>
                                                                            <option value="as_section_id"> Section</option>
                                                                            <option value="as_subsection_id"> Sub Section</option>
                                                                            <option value="as_id"> Employee</option>
                                                                        </select>
                                                                        <label for="excluding-type-for">Type </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2" >
                                                                    <div class="form-group">
                                                                        <button class="btn btn-outline-primary sync-type" data-category="excluding" type="button" id="excluding-sync-type">
                                                                            <i class="las la-sync"></i>
                                                                        </button> 
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="excluding-targettype"></div>
                                                <div id="excluding-appendType" class="appendType"></div>
                                            </div>
                                        </div>
                                        <div class="rule-overlay" id="rule-overlay"></div>
                                    </div> --}}
                                </div>
                                <div class="process-btn">
                                    <div class="form-group pull-right">
                                        <button type="button" class="btn btn-md btn-outline-primary pull-right" onclick="updateHoliday()"> <i class="fa fa-save"></i> Save </button>
                                    </div>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </form>
            
        </div> 
    </div> 
</div> 
@include('hr.common.right-modal')
@push('js')
<script src="{{ asset('assets/js/jquery-ui.js')}}"></script>
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script src="{{ asset('assets/js/holiday-planner.js')}}"></script>
<script type="text/javascript">
    function updateHoliday(){
        $(".app-loader").show();
        var data = $("#formData").serialize();
        let url = '{{ url("hr/operation/holiday-planner/$day->hr_yhp_id") }}'
        $.ajax({
          type: 'PUT',
          url: url,
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
          },
          data: data, // serializes the form's elements.
          success: function(response)
          {
            // console.log(response)
            if(response.type === 'success'){
                setTimeout(function(){
                    window.location.href = response.url;
                }, 500);
            }
            $.notify(response.message, response.type);
            $(".app-loader").hide();
            
          },
          error: function (reject) {
            $.notify('Something wrong, please try again!', 'error');
            $(".app-loader").hide();
          }
        });
    }
</script>
@endpush
@endsection