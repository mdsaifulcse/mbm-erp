@extends('hr.layout')
@section('title', 'Add Role')
@section('main-content')
@push('css')
    <style>
        .alert-warning{
            background-color: #fdf0d4;
            border-color: #fdf0d4;
            color: #c88a0a;
            padding: 10px;
        }
        .alert-warning .alert-icon{
            background-color: #e19b0b;
        }

        .alert-success {
            background-color: #d6e9c6;
            border-color: #fdf0d4;
            color: #000;
            padding: 10px;
        }
        .alert-success .alert-icon {
            background-color: #9ddc9f;
        }
        .alert-icon {
            width: 40px;
            height: 40px;
            display: inline-block;
            border-radius: 100%;
        }
        .alert-icon i {
            width: 40px;
            height: 40px;
            display: block;
            text-align: center;
            line-height: 40px;
            font-size: 20px;
            color: #FFF;
        }
        .notification-info {
            margin-left: 56px;
            margin-top: -40px;
            min-height: 40px;
        }
        .notification-meta {
            margin-bottom: 3px;
            padding-left: 0px;
            list-style: none outside none;
        }
        .notification-sender {
            color: #414147;
        }
        .process_section{margin-top: 22px;}
        .file-processing-log .panel-title {margin-top: 3px; margin-bottom: 3px;}
        .file-processing-log .panel-title a{font-size: 15px; display: block;}
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
                    <a href="#">Operations</a>
                </li> 
                <li class="active">Salary Sheet</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            

            <div class="row">
                <div class="col-xs-12"> 
                    <div class="panel panel-info">
                      <div class="panel-body">
                        <div class="panel panel-success">
                            <div class="panel-heading"><h6>Salary generate Process</h6></div>
                            <div class="panel-body" style="background: #fbfbfb;">
                                <div class="msg" id="top-msg">
                                    <div class="alert alert-warning ">
                                        <span class="alert-icon"><i class="fa fa-refresh fa-spin"></i></span>
                                        <div class="notification-info">
                                            <h5 class=""><b id="unitName"></b> : Salary generate is being processed! Please don't close this page</h5>
                                                                           
                                        </div>
                                    </div>
                                </div>
                                <div class="att_status_section" id="progressbar-box">
                                    <div class="col-sm-1">
                                        <img class="img-responsive" src="{{ asset('assets/img/loader-box.gif')}}" style="max-width: 64px;" alt="Synchronizing..." />
                                    </div>
                                    <div class="col-sm-11">
                                        <div class="process_section" >
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-info progress-bar-striped active" id="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                                  0%
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div id="accordion" class="accordion-style panel-group">
                            <div class="panel panel-info">
                                <div class="panel-heading file-processing-log">
                                    <h2 class="panel-title">
                                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#individual" aria-expanded="false">
                                            <i class="bigger-110 ace-icon fa fa-angle-right" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                            &nbsp;Log
                                        </a>
                                    </h2>
                                </div>

                                <div class="panel-collapse collapse" id="individual" aria-expanded="false" style="height: 0px;">
                                    <div class="panel-body">
                                        <div id="log-section"></div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        
                      </div>
                    </div>
                    
                </div>
                <!-- /.col -->
            </div>
        </div><!-- /.page-content -->
    </div>
</div>

@push('js')
<script>
var url = "{{ url('/') }}";
var cur=0;
var _token = $('input[name="_token"]').val();

$(document).ready(function() {
    var datas = @json($chunkValues);
    var dataLength = datas.length - 1;
    var input = @json($data);
    var unitName = '';
    if(input.unit == 1){
        unitName = 'MBM GARMENTS LTD.';
    }else if(input.unit == 2){
        unitName = 'CUTTING EDGE INDUSTRIES LTD.';
    }else if(input.unit == 3){
        unitName = 'ABSOLUTE QUALITYWEAR LTD.';
    }else if(input.unit == 8){
        unitName = 'CUTTING EDGE INDUSTRIES LTD. (WASHING PLANT)';
    }else{
        unitName = 'MBM GARMENTS LTD.';
    }
    $("#unitName").html(unitName);
    var per = (100 / datas.length);
    var i = 0;
    $.each(datas, function(len, val) {
        i++;
        setTimeout(function(){
            $.ajax({
                url: url+'/hr/reports/salary-sheet-generate-process',
                type: "post",
                data: { 
                  _token : _token,
                  data : input,
                  getdata: val
                },
                success: function(response){
                    console.log(response);
                    if(response == 'success'){
                        cur = parseFloat(parseFloat(cur) + parseFloat(per)).toFixed(2);
                        if(cur<100){
                            $('#progress-bar').html(cur+'%');
                            $('#progress-bar').css({width: cur+'%'});
                            $('#progress-bar').attr('aria-valuenow', cur+'%');
                        }
                    }
                    if(len === dataLength){
                            // console.log('complete');
                        $("#progressbar-box").hide();
                        $("#top-msg").html('<div class="alert alert-success "><span class="alert-icon"><i class="fa fa-check"></i></span><div class="notification-info"><h5 class="">'+unitName+' Salary Generate Processed.</h5><a href="'+url+'/hr/reports/salary-sheet-generate" class="btn btn-warning btn-xs"> <i class="fa fa-reply-all"></i> Back</a></div></div>');
                    }
                    
                }, 
                error: function(response) {
                    console.log(response);
                }
            });
        }, 1000 * i);
    });
});

</script>
@endpush
@endsection