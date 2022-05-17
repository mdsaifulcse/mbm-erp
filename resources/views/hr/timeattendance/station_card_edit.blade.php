@extends('hr.layout')
@section('title', '')
@section('main-content')
@push('css')
<style type="text/css">
@media only screen and (max-width: 771px) {
    
.form-group{margin-bottom: 0px !important}

}
</style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Time & Attendance</a>
                </li>
                <li class="active">Station Card Edit</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <div class="page-header">
                <h1>Time & Attendance<small> <i class="ace-icon fa fa-angle-double-right"></i>Station Card Edit</small></h1>
            </div>

            <div class="row">

                <!-- Display Erro/Success Message -->
                @include('inc/message')

                <div class="col-xs-12">
                    {{ Form::open(['url'=>'hr/timeattendance/station_card_update', 'class'=>'form-horizontal', 'method'=>'POST']) }}
                        <input type="hidden" name="unit_id" value="{{ $station->unit_id }}">
                        <input type="hidden" name="station_id" value="{{ $station->station_id }}">
                    <div class="col-sm-5 col-xs-12 responsive-hundred">
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="associate_id"> Associate's ID </label>
                            <div class="col-sm-8">
                                <input type="text" name="associate_id" id="associate_id" class="col-xs-12" value="{{ $station->associate_id }}" readonly>  

                            </div>
                        </div> 
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Unit </label>
                            <div class="col-sm-8">
                                <input type="text" id="unit" class="col-xs-12" value="{{ $station->hr_unit_name }}" readonly>
                            </div>
                        </div>   
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Floor </label>
                            <div class="col-sm-8">
                                <input type="text" id="floor" class="col-xs-12" value="{{ $station->hr_floor_name }}" readonly>
                            </div>
                        </div>   
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Line </label>
                            <div class="col-sm-8">
                                <input type="text" id="line" class="col-xs-12" value="{{ $station->hr_line_name }}" readonly>
                            </div>
                        </div>   
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right"> Shift </label>
                            <div class="col-sm-8">
                                <input type="text" id="shift" class="col-xs-12" value="{{ $station->as_shift_id }}" readonly>
                            </div>
                        </div> 
                    </div>

                    <div class="col-sm-2"></div>

                    <div class="col-sm-5 col-xs-12 responsive-hundred">      
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="floor_id">Changed Floor </label>
                            <div class="col-sm-8">
                                {{Form::select('floor_id', $floorList, $station->changed_floor, ['id'=> 'floor_id', 'placeholder' => "Select Floor", 'class'=> "no-select col-xs-12", 'data-validation'=>'required'])}}
                            </div>
                        </div>      
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="line_id">Changed Line </label>
                            <div class="col-sm-8">
                                {{Form::select('line_id', $lineList, $station->changed_line, ['id'=> 'line_id', 'placeholder' => "Select Line", 'class'=> "no-select col-xs-12", 'data-validation'=>'required'])}}
                            </div>
                        </div>     

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="shift_id">Start Date </label>
                            <div class="col-sm-8">
                                <input type="text" name="start_date" class="datetimepicker col-xs-12" value="{{ $station->start_date }}" placeholder="Start Date" data-validation="required">
                            </div>
                        </div> 

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="shift_id">End Date </label>
                            <div class="col-sm-8">
                                <input type="text" name="end_date" class="datetimepicker col-xs-12" value="{{ $station->end_date }}" placeholder="End Date" data-validation="required">
                            </div>
                        </div>    
                    </div> 
                    
                    <div class="col-sm-12 responsive-hundred">
                        
                        <div class="clearfix form-actions">
                            <div class="col-md-offset-4 col-md-4 text-center" style="padding-left: 30px;">
                                <button class="btn btn-sm       btn-success" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                </button>

                                &nbsp; &nbsp; &nbsp;
                                <button class="btn btn-sm      " type="reset">
                                    <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                </button>
                            </div>
                        </div>
                  </div>
                    {{ Form::close() }}
                <!-- /.col -->
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

    //get associate information on select associate id
    $("#associate_id").on("change", function(){

        if($(this).val() != ""){
            $.ajax({
                url: '{{ url("hr/timeattendance/station_as_info") }}',
                data: {associate_id: $(this).val()},
                success: function(data)
                { 
                    $("#unit").val(data.unit);
                    $("#floor").val(data.floor);
                    $("#line").val(data.line);
                    $("#shift").val(data.shift);
                    $("#floor_id").html(data.floorList);
                },
                error: function(xhr)
                {
                    alert('failed');
                }
            }); 
        }
    });

    //get line list of selected floor
    $("#floor_id").on("change", function(){

        if($(this).val() != ""){
            $.ajax({
                url: '{{ url("hr/timeattendance/station_line_info") }}',
                data: {floor_id: $(this).val()},
                success: function(data)
                { 
                    $("#line_id").html(data);
                },
                error: function(xhr)
                {
                    alert('failed');
                }
            }); 
        }
    });


});
</script>
@endsection