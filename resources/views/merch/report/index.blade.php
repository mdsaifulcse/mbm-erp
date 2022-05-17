@extends('merch.layout')
@section('title', 'Style Details')
@section('main-content')
@push('css')
<style type="text/css">
    .single-employee-search {
      margin-top: 82px !important;
    }
    .view:hover, .view:hover{
      color: #ccc !important;
      
    }
    .grid_view{

    }
    .view i{
      font-size: 25px;
      border: 1px solid #000;
      border-radius: 3px;
      padding: 0px 3px;
    }
    .view.active i{
      background: linear-gradient(to right,#0db5c8 0,#089bab 100%);
      color: #fff;
      border-color: #089bab;
    }
    .iq-card .iq-card-header {
      margin-bottom: 10px;
      padding: 15px 15px;
      padding-bottom: 8px;
    }
    #right_modal_lg_drawer .table-responsive{
        display: initial !important;
    }
    #right_modal_lg_drawer .table-title{
        display: none;
    }
    .generate-drawer{
        color:#089bab !important;
        font-weight: bold;
    }

</style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Merchandising</a>
                </li>
                <li>
                    <a href="#">Style</a>
                </li>
                <li class="active">Style Details</li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="row">
                <div class="col-12">
                    <form class="" role="form" id="activityReport" method="get" action="#"> 
                        <div class="panel">
                            <div class="panel-body pb-0">
                              
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="buyer" class="form-control capitalize select-search" id="buyer" >
                                                <option selected="" value="">Choose...</option>
                                               
                                                @foreach($buyerList as $key => $value)
                                                <option value="{{ $key}}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                          <label for="buyer">Buyer</label>
                                        </div>
                                        
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="season" class="form-control capitalize select-search" id="season">
                                                <option selected="" value="">Choose...</option>
                                                @foreach($seasonList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <label for="season">Season</label>
                                        </div>
                                        
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="productType" class="form-control capitalize select-search" id="productType" >
                                                <option selected="" value="">Choose...</option>
                                               
                                                @foreach($productTypeList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                          <label for="productType">Product Type</label>
                                        </div>
                                        
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="gender" class="form-control capitalize select-search" id="gender">
                                                <option selected="" value="">Choose...</option>
                                                
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                                
                                            </select>
                                            <label for="gender">Gender</label>
                                        </div>
                                        
                                        
                                    </div>
                                    
                                    
                                    
                                    
                                </div>
                                {{-- <div class="row">
                                    <div class="offset-8 col-4">
                                        
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
            <div class="row">
              <div class="col">
                <div class="iq-card" id="result-section" /*style="display: none" */>
                  
                  <div class="iq-card-body no-padding" id="print-area">
                    <style type="text/css" media="print">
                        .table{
                          width: 100%;
                        }
                        a{text-decoration: none;}
                        .table-bordered {
                            border-collapse: collapse;
                        }
                        .table-bordered th,
                        .table-bordered td {
                          border: 1px solid #777 !important;
                          padding:5px;
                        }
                        .no-border td, .no-border th{
                          border:0 !important;
                          vertical-align: top;
                        }
                        .f-16 th, .f-16 td, .f-16 td b{
                          font-size: 16px !important;
                        }
                        .imageNone{
                          display:none;
                        }
                        .btn {
                          display: none;
                        }
                        
                    </style>
                    <div class="result-data" id="result-data">

                      <div class="panel">
                        <div class="panel-body">
                          <div class="report_section" id="report_section">
                            @php
                              // 'Test'Head = explode('_','Test');
                              // $urldata = http_build_query($input) . "\n";
                              // $jsonUrl = json_encode($urldata);
                            @endphp
                            
                            <div class="top_summery_section">
                              
                              <div class="page-header">
                                
                                  <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('print-area')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
                                  <button class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download">
                                  <i class="fa fa-file-excel-o"></i>
                                </button>
                        
                                      <h3 style="margin:4px 10px; font-weight: bold; text-align: center;"> Style Details Report</h3>
                                      
                                      <table class="table no-border f-16" border="0">
                                        <tr>
                                          <td style="width: 33%;">
                                          
                                              Buyer <b>: All</b> <br>
                                          
                                          </td>
                                          <td style="width: 33%;text-align: center;">
                                               <br>
                                            
                                              
                                          </td>
                                          <td style="width: 33%;text-align: right;">
                                            
                                             Date <b>: {{ date('Y-m-d') }}</b><br>
                                            
                                          </td>
                                        </tr>
                                        
                                      </table>
                                      
                                  </div>
                                 
                                  
                                 
                            </div>
                            <div class="content_list_section">
                             
                                <table id="example" class="table table-bordered table-hover table-head table-responsive" style="width:100%;border:0 !important;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
                               
                                  <thead>

                                            <tr>
                                                <th width="2%">Sl</th>
                                                <th width="5%">Buyer</th>
                                                <th width="16%">Style Reference 1</th>
                                                <th width="8%">Style Description</th>
                                                <th width="15%">Style Reference 2</th>
                                                <th width="9%">Season</th>
                                                <th width="9%">Brand</th>
                                                <th width="9%">Product Type</th>
                                                <th width="5%">Garment Type</th>
                                                <th width="5%">Gender</th>
                                                <th width="5%">Sample Type</th>
                                                <th width="5%">Operation</th>
                                                <th width="5%">Special Machcine</th>
                                                <th width="5%">Swing SMV</th>
                                                <th class="imageNone" width="5%">Image</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                          @php
                                            $i = 0; 
                                          @endphp
                                          @if(count($styleDetails) > 0)
                                          @foreach($styleDetails as $data)
                                         
                                            <tr>
                                              <td>{{ ++$i }}</td>
                                              <td>{{ $getBuyer[$data->mr_buyer_b_id]->b_name??''}}</td>
                                              <td>{{ $data->stl_no??'' }}</td>  
                                              <td width="5%">{{ $data->stl_description??'' }}</td>  
                                              <td>{{ $data->stl_product_name??'' }}</td>
                                              <td>{{ $getSeason[$data->mr_season_se_id]->se_name??'' . '-'. $data->stl_year??''}}</td>  
                                              <td>{{ $getBrand[$data->mr_brand_br_id]->br_name??''}}</td>  
                                              <td>{{ $getProductType[$data->prd_type_id]->prd_type_name??''}}</td>  
                                              <td>{{ $getGermentType[$data->gmt_id]->gmt_name??''}}</td>  
                                              <td>{{ $data->gender??''}}</td>  
                                              <td>{{ $data->sample??''}}</td>  
                                              <td>{{ $data->operation??''}}</td>  
                                              <td>{{ $data->spmachine_name??''}}</td>  
                                              <td>{{ $data->stl_smv??''}}</td>  
                                              <td class="imageNone"><img width="30" height="40" src='{{ asset($data->stl_img_link)}}' style=""></td>  
                                              
                                            
                                              
                                                
                                              
                                            </tr>
                                        
                                          @endforeach

                                          @else
                                        
                                            <tr>
                                              <td colspan="15" class="text-center">No   Employee Found!</td>
                                            </tr>
                                          @endif
                                          
                                        </tbody>
                              
                                </table>
                              
                            </div>
                          </div>
                      
                          {{-- modal --}}
                        </div>
                      </div>
                      
                      
                      
                    </div>
                  </div>
               </div>
                
              </div>
          </div>
        </div><!-- /.page-content -->
    </div>
</div>


@push('js')
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript">

$(document).ready(function(){  
    
    var loader = '<div class="panel"><div class="panel-body"><p style="text-align:center;margin:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>';
    var loaderContent = '<div class="animationLoading"><div id="container-loader"><div id="one"></div><div id="two"></div><div id="three"></div></div><div id="four"></div><div id="five"></div><div id="six"></div></div>';
    
    $('#excel').click(function(){
      var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#report_section').html())
      location.href=url;
      return false;
    });
    
    var dt = $('#example').on("change", function(){
      alert('Hello');
      $.ajax({
               url: '{!! url("merch/reports") !!}',
               type: "get",
			   data: function (d) {
	                d.gender  = $('#gender').val(),
					d.buyer  = $('#buyer').val(),
					d.season  = $('#season').val(),
					d.productType  = $('#productType').val()
	                
	            },
               headers: {
                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
               } 
              });
       });
    
    //Load Department List By Area ID
    $('#area').on("change", function(){
        $.ajax({
           url : "{{ url('hr/setup/getDepartmentListByAreaID') }}",
           type: 'get',
           data: {area_id : $(this).val()},
           success: function(data)
           {
                $('#department').removeAttr('disabled');
                
                $("#department").html(data);
           },
           error: function(reject)
           {
             console.log(reject);
           }
        });
    });

    
    


    

    $(document).on("change",'#gender,#buyer,#season,#productType', function(e){
      
		e.preventDefault();
		dt.draw();
	}); 
    
});



    
</script>
@endpush

@endsection