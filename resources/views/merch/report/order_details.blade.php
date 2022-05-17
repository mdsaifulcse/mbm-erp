@extends('merch.layout')
@section('title', 'Order Details')
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
                    <a href="#">Order</a>
                </li>
                <li class="active">Order Details</li>
                <li class="top-nav-btn">
                  <div class="text-right">
                    <a class="btn view no-padding clear-filter" data-toggle="tooltip" data-placement="top" title="" data-original-title="Clear Filter">
                      <i class="las la-redo-alt" style="color: #f64b4b; border-color:#be7979"></i>
                    </a>
                    <a class="btn view no-padding filter" data-toggle="tooltip" data-placement="top" title="" data-original-title="Advanced Filter">
                      <i class="fa fa-filter"></i>
                    </a>
                    
                    
                  </div>
              </li>
            </ul>
        </div>

        <div class="page-content"> 
            
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
                          font-size: 13px !important;
                        }
                        .imageNone{
                          display:none;
                        }
                        .btn {
                          display: none;
                        }
                        h3{
                          font-size: 25px !important;
                          padding-bottom: 20px;
                        }
                        td{
                          font-size: 10px !important;
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
                        
                                      <h3 style="margin:4px 10px; font-weight: bold; text-align: center;"> Order Details Report</h3>
                                      
                                      <table class="table no-border f-16" border="0">
                                        <tr>
                                          <td class="headInfo" style="width: 33%;">
                                          
                                              
                                              Unit <b>: All</b> <br>
                                              Buyer <b>: All</b> <br>
                                          
                                          </td>
                                          <td style="width: 33%;text-align: center;">
                                            Month of <b>: {{ date('M-Y') }}</b><br>
                                              
                                          </td>
                                          <td style="width: 33%;text-align: right;">
                                            
                                            Run Time <b>: {{ date('D M Y H:i a') }}</b><br>
                                            
                                          </td>
                                        </tr>
                                        
                                      </table>
                                      
                                  </div>
                                 
                                  
                                 
                            </div>
                            <div class="content_list_section">
                             
                                <table id="example" class="table table-bordered table-hover table-head" style="width:100%;border:0 !important;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
                               
                                  <thead>

                                            <tr>
                                                <th width="1%">Sl</th>
                                                <th width="5%">Order No</th>
                                                <th width="20%">Reference 1</th>
                                                <th width="18%">Reference 2</th>
                                                <th width="18%">GDes</th>
                                                <th width="14%">Season</th>
                                                <th width="10%">FOB</th>
                                                <th width="5%">QTY</th>
                                                <th width="4%">SMV</th>
                                               
                                                <th width="5%">PCD</th>
                                                
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                          @php
                                            $i = 0; 
                                          @endphp
                                          @if(count($orederDetails) > 0)
                                          @foreach($orederDetails as $data)
                                         
                                            <tr>
                                              <td>{{++$i}}</td>
                                              <td>{{$data->order_code??''}}</td>
                                              <td>{{$data->style->stl_no??''}}</td>
                                              <td>{{$data->style->stl_product_name??''}}</td>
                                              <td>{{$data->style->stl_description??''}}</td>
                                              @if($data->style->stl_year)
                                              <td>{{ $getSeason[$data->style->mr_season_se_id]->se_name . '-' .  $data->style->stl_year??''}}</td>
                                              @else
                                              <td>{{ $getSeason[$data->style->mr_season_se_id]->se_name??''}}</td>
                                              @endif
                                              <td>{{$orderFOB[$data->order_id]??0}}</td>
                                              <td>{{$data->order_qty??''}}</td>
                                              <td>{{$data->style->stl_smv??''}}</td>
                                              <td>{{date('y/m/d', strtotime($data->pcd))??''}}</td>
                                            </tr>
                                          @endforeach
                                          {{-- <tr>
                                            <td colspan="11" class="text-center">No   Employee Found!</td>
                                          </tr>
                                          <tr>
                                            <td colspan="11" class="text-center">No   Employee Found!</td>
                                          </tr> --}}

                                          @else
                                        
                                            <tr>
                                              <td colspan="11" class="text-center">No   Employee Found!</td>
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
    
    
    
});



    
</script>
@endpush

@endsection