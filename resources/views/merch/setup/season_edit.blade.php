@extends('merch.index')
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Merchandising </a>
                </li>
                <li> 
                    <a href="#">Setup</a>   
                </li> 
                <li class="active"> Season Edit</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content"> 

            <div class="row">
                <!-- Display Erro/Success Message -->
                <div class="col-sm-6 col-sm-offset-3">
                    @include('inc/message')
                    <div class="panel panel-success">
                        <div class="panel-heading">
                          <h6>Season Edit <a class="pull-right healine-panel" href="{{ url('merch/setup/season') }}" rel="tooltip" data-tooltip="Season List/Create" data-tooltip-location="top"><i class="fa fa-list"></i></a></h6>
                        </div>
                        <div class="panel-body">
                            <!-- PAGE CONTENT BEGINS --> 
                            {{ Form::open(["url"=>"merch/setup/season_update", "class"=>"form-horizontal"]) }}

                                <input type="hidden" name="se_id" value="{{ $season->se_id }}">


                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="b_id"> Buyer<span style="color: red">&#42;</span>  </label>
                                    <div class="col-sm-8"> 
                                        {{ Form::select('b_id', $buyerList, $season->b_id, ['placeholder'=>'Select Buyer', 'id'=>'b_id', 'class'=> 'form-control', 'style'=>'width:100%', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Buyer field is required']) }}  
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="se_name" > Season Name<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-8">

                                        <input type="text" name="se_name" id="se_name" placeholder="Season Name" data-type="season" class="col-xs-12 autocomplete_pla form-control" data-validation="required length custom" data-validation-length="1-128" value="{{ $season->se_name }}" autocomplete="off"/>

                                        <div id="suggesstion-box"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="se_mm_start" > Start Month-Year<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-8">
                                     
                                     <input type="text" name="se_mm_start" id="se_mm_start"  class="form-control monthYearpicker col-xs-12" data-validation="required" value=" {{date('F-Y',strtotime($season->se_start))}}"/> 
                                    </div>
                                    
                                </div>  
         
                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="se_mm_end" > End Month-Year<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-8">
                                   
                                     <input type="text" name="se_mm_end" id="se_mm_end" placeholder="Month-y" class="form-control monthYearpicker col-xs-12" data-validation="required" value=" {{date('F-Y',strtotime($season->se_end))}}"/>
                                    </div>
                                   
                                </div> 

                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="se_mm_end" > Status<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-8">

                                        @if($season->season_status==1)
                                            <div class="form-group">
                                                <div class="col-sm-9"> 
                                                    <input name="season_state" class="ace ace-switch ace-switch-6" type="checkbox" checked>
                                                    <span class="lbl" style="margin:6px 0 0 0"></span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="form-group">
                                                <div class="col-sm-9"> 
                                                    <input name="season_state" class="ace ace-switch ace-switch-6" type="checkbox">
                                                    <span class="lbl" style="margin:6px 0 0 0"></span>
                                                </div>
                                            </div>
                                        @endif  
                                    </div>
                                    
                                   
                                </div>  
         
                                <div class="space-4"></div>
                                <div class="space-4"></div>
                                <div class="space-4"></div>
                                <div class="space-4"></div>
                                <div class="space-4">
                                </div>
                                @include('merch.common.update-btn-section')
                                <!-- /.row --> 
                            </form> 
                            <!-- PAGE CONTENT ENDS -->
                        </div>
                    </div>
                </div>
 
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
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

function selectCountry(val) {
$("#se_name").val(val);
$("#suggesstion-box").hide();
}
</script>
@endsection