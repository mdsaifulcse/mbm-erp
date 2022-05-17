@extends('merch.layout')
@section('title', 'Style Profile')
@section('main-content')
    @push('css')
        <style type="text/css">
            /*#largePreview{position: absolute;display:none;top: 0;height: auto;z-index: 100;box-shadow: 0 0 10px 5px #428BCA;left: 300px;max-width: 800px;}*/

            .slider-container {
                width: 90%;
                height: 152px !important;
                
            }

            .light-box .slider-container {
                width: 90% !important;
                height: 100% !important;
            }

            .multi-image{
                width:
            }

            .steps-div {
                padding-bottom: 20px;
                border-left: 2px solid #d1d1d1;
            }

            .steps {
                list-style: none;
                display: table;
                width: 100%;
                padding: 0;
                margin: 0;
                position: relative;
            }


            .steps > li {
                display: table-cell;
                text-align: center;
                width: 1%;
            }

            .steps > li.active .step, .steps > li.active:before, .steps > li.complete .step, .steps > li.complete:before {
                border-color: #039e08;
            }

            .steps > li.active .step, .steps > li.active:before, .steps > li.complete .step, .steps > li.complete:before {
                border-color: #5293c4;
            }

            .steps > li:first-child:before {
                max-width: 51%;
                left: 50%;
            }

            .steps > li:before {
                display: block;
                content: "";
                width: 100%;
                height: 1px;
                font-size: 0;
                overflow: hidden;
                border-top: 4px solid #ced1d6;
                position: relative;
                top: 21px;
                z-index: 1;
            }

            .steps > li.active .step, .steps > li.active:before, .steps > li.complete .step, .steps > li.complete:before {
                border-color: #039e08;
            }

            .steps > li.active .step, .steps > li.active:before, .steps > li.complete .step, .steps > li.complete:before {
                border-color: #5293c4;
            }

            .steps > li .step {
                border: 5px solid #ced1d6;
                color: #546474;
                font-size: 15px;
                border-radius: 100%;
                position: relative;
                z-index: 2;
                display: inline-block;
                width: 40px;
                height: 40px;
            }


            .steps > li .step, .steps > li.complete .step:before {
                line-height: 30px;
                background-color: #fff;
                text-align: center;
            }

            .steps > li.active .title, .steps > li.complete .title {
                color: #2b3d53;
            }

            .steps > li .title {
                display: block;
                margin-top: 4px;
                max-width: 100%;
                color: #949ea7;
                font-size: 14px;
                z-index: 104;
                text-align: center;
                table-layout: fixed;
                word-wrap: break-word;
            }


            .accordion-style2.panel-group .panel-heading .accordion-toggle {
                background-color: #edf3f7;
                border: 3px solid #089eaf;
                border-width: 0 0 0 3px;
            }

            .accordion-style1.panel-group .panel-heading .accordion-toggle {
                color: #089eaf;
                background-color: #eef4f9;
                position: relative;
                font-weight: 700;
                font-size: 13px;
                line-height: 1;
                padding: 10px;
                display: block;
            }

            .accordion-style1.panel-group .panel-heading .accordion-toggle > .ace-icon:first-child {
                width: 16px;
            }

            .bigger-110 {
                font-size: 110% !important;
            }

            .ace-icon {
                text-align: center;
            }

            .glyphicon {
                position: relative;
                top: 1px;
                display: inline-block;
                font-family: 'Glyphicons Halflings';
                font-weight: 400;
                line-height: 1;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }

            .glyphicon, address {
                font-style: normal;
            }

            .profile-picture {
                border: 1px solid #ccc;
                background-color: #fff;
                padding: 4px;
                display: inline-block;
                max-width: 100%;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
                box-shadow: 1px 1px 1px rgb(0 0 0 / 15%);
            }

            .label-xlg.arrowed-in-right, .label-xlg.arrowed-right {
                margin-right: 7px;
            }

            .label.arrowed-in-right, .label.arrowed-right {
                position: relative;
                z-index: 1;
            }

            .label.arrowed, .label.arrowed-in {
                position: relative;
                z-index: 1;
            }

            .badge-info, .badge.badge-info, .label-info, .label.label-info {
                background-color: #3a87ad;
            }

            .label.arrowed, .label.arrowed-in {
                margin-left: 5px;
            }

            .label.arrowed-in-right, .label.arrowed-right {
                margin-right: 5px;
            }

            .label {
                margin: 1px;
            }

            .label-xlg {
                padding: .3em .7em .4em;
                font-size: 14px;
                line-height: 1.3;
                height: 28px;
            }

            .label {
                color: #fff;
                display: inline-block;
            }

            .badge.no-radius, .btn.btn-app.no-radius > .badge.no-radius, .btn.btn-app.radius-4 > .badge.no-radius, .label {
                border-radius: 0;
            }

            .badge, .label {
                font-size: 12px;
            }

            .badge, .label {
                font-weight: 400;
                background-color: #abbac3;
                text-shadow: none;
            }

            .width-80 {
                width: 80% !important;
            }

            .label-info {
                background-color: #5bc0de;
            }

            .label {
                display: inline;
                padding: .2em .6em .3em;
                font-size: 75%;
                color: #fff;
                border-radius: .25em;
            }

            .badge, .close, .label {
                line-height: 1;
            }

            .badge, .label {
                font-weight: 700;
                white-space: nowrap;
                text-align: center;
            }

            .label, sub, sup {
                vertical-align: baseline;
            }

            html {
                font-size: 10px;
                -webkit-tap-highlight-color: transparent;
            }

            html {
                font-family: sans-serif;
                -ms-text-size-adjust: 100%;
                -webkit-text-size-adjust: 100%;
            }


            @media screen and (-webkit-min-device-pixel-ratio: 1.2) and (-webkit-max-device-pixel-ratio: 1.3), not all
                .label-xlg.arrowed-in-right:after, .label-xlg.arrowed-in:before {
                    border-width: 14.5px 7px;
                }

                .label-xlg.arrowed-in:before {
                    left: -7px;
                    border-width: 14px 7px;
                }

                .label.arrowed-in:before {
                    left: -5px;
                    border-width: 10px 5px;
                }

                .label-info.arrowed-in:before {
                    border-color: #3a87ad #3a87ad #3a87ad transparent;
                }

                .label.arrowed-in:before {
                    border-color: #abbac3 #abbac3 #abbac3 transparent;
                }

                .label.arrowed-in:before, .label.arrowed:before {
                    display: inline-block;
                    content: "";
                    position: absolute;
                    top: 0;
                    z-index: -1;
                    border: 1px solid transparent;
                    border-right-color: #abbac3;
                }

                .label-xlg {
                    padding: .3em .7em .4em;
                    font-size: 14px;
                    line-height: 1.3;
                    height: 28px;
                }

                .label {
                    line-height: 1.15;
                    height: 20px;
                }

                .white {
                    color: #fff !important;
                }

                select, input[type=email], span, input[type=url], input[type=search], input[type=tel], input[type=color], input[type=text], input[type=password], input[type=datetime], input[type=datetime-local], input[type=date], input[type=month], input[type=time], input[type=week], input[type=number], textarea {
                    font-size: 11px;
                }

                @media screen and (-webkit-min-device-pixel-ratio: 1.2) and (-webkit-max-device-pixel-ratio: 1.3), not all
                    .label-xlg.arrowed-in-right:after, .label-xlg.arrowed-in:before {
                        border-width: 14.5px 7px;
                    }

                    .label-xlg.arrowed-in-right:after {
                        right: -7px;
                        border-width: 14px 7px;
                    }

                    .label.arrowed-in-right:after {
                        right: -5px;
                        border-width: 10px 5px;
                    }

                    .label-info.arrowed-in-right:after {
                        border-color: #3a87ad transparent #3a87ad #3a87ad;
                    }

                    .label.arrowed-in-right:after {
                        border-color: #abbac3 transparent #abbac3 #abbac3;
                    }

                    .label.arrowed-in-right:after, .label.arrowed-right:after {
                        display: inline-block;
                        content: "";
                        position: absolute;
                        top: 0;
                        z-index: -1;
                        border: 1px solid transparent;
                        border-left-color: #abbac3;
                    }

            
            .slide-image{
                max-width: 100%;
                height: 126px;
                width: 200px;
                object-fit: cover;
                border-radius:0 !important;
            }

            .infoHeader{
                
                padding: 4px;
                font-weight: 700;
                color: #089eaf;
                background: #eef4f9;
                border-left: 3px solid #089eaf;
                padding-left:10px;
                font-size: 13px;
                
            }

            @media print {
                
                
                /*div1 */
                h4{font-size: 10pt;}
                div,p,td,span,strong,th,b{line-height: 120%;padding: 0;margin: 0;font-size: 6pt;}
                p{padding: 0;margin: 0;}
                @import url(https://fonts.googleapis.com/css?family=Poppins:200,200i,300,400,500,600,700,800,900&amp;display=swap);
                body {font-family: Poppins,sans-serif;}
                .table{width: 100%;}a{text-decoration: none !important;}
                .table-bordered {border-collapse: collapse;}
                .table-bordered th,.table-bordered td {border: 1px solid #777 !important;padding:5px;}
                .no-border td, .no-border th{border:0 !important;vertical-align: top;}
                .f-16 th,.f-16 td, .f-16 td b{font-size: 16px !important;}
                footer{
                    display: none;
                }
            }

            

           
           
  

        </style>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
              rel="stylesheet">
        
    @endpush
    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <a href="/"><i class="ace-icon fa fa-home home-icon"></i> Merchandising</a>
                    </li>
                    <li>
                        <a href="#">Style</a>
                    </li>
                    <li>
                        <a href="#">Style Bom</a>
                    </li>
                    <li class="active">Single View</li>
                    <li class="top-nav-btn">
                        <button class="btn btn-sm btn-primary pull-right hidden-print"
                                onclick="printDivStyleProfile()"
                                style="margin-left: 5px; height: 25px;"><i class="las la-print"></i>
                        </button>
                        <a class="btn btn-sm btn-info hidden-print" href="/merch/style/bom-single-view/{{ $stylebom_id }}?export=excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download">
                                  <i class="fa fa-file-excel-o"></i>
                                </a>
                    </li>
                </ul><!-- /.breadcrumb -->
                
            </div><!-- /* breadcrumbs */ -->

            
  

            <div class="page-content" id="printMe">



                

                
                <div class="panel">
                    <div class="panel-body">
                        
                        <div id="printExcell" class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                    <div class="panel panel-info">
                                        <div class="panel-heading">
                                                        <h4 class="text-center">
                                                            
                                                                Style Bom Information
                                                            
                                                        </h4>
                                                    </div>
                                    </div>
                                        
                                    </div>
                                </div>
<br>

                    <div class="row">
                                        
                                                <div class="col-md-10" >
                                                   <!-- <div class="row">
                                                        <div class="col-md-12"> -->
                                                            <div class="widget-body" style="border-radius:0;">
                                                                
                                                                <table
                                                                    class="table custom-font-table"
                                                                    width="50%" cellpadding="0"
                                                                    cellspacing="0" border="0">
                                                                    <tr>
                                                                        <th>Production Type</th>
                                                                        <td>{{-- (!empty($style->stl_type)?($style->stl_type=='Development'?'Development':'Bulk'):null) --}}</td>
                                                                        <th>Style Reference 1</th>
                                                                        <td>{{ (!empty($style->stl_no)?$style->stl_no:null) }}</td>
                                                                        <th>Operation</th>
                                                                        <td>{{ (!empty($operations->name)?$operations->name:null) }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Buyer</th>
                                                                        <td>{{ (!empty($style->b_name)?$style->b_name:null) }}</td>
                                                                        <th>SMV/PC</th>
                                                                        <td>{{ (!empty($style->stl_smv)?$style->stl_smv:null) }}</td>
                                                                        <th>Speacial Machine</th>
                                                                        <td>{{ (!empty($machines->name)?$machines->name:null) }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Style Reference 2</th>
                                                                        <td>{{ (!empty($style->stl_product_name)?$style->stl_product_name:null) }}</td>
                                                                        <th>Sample Type</th>
                                                                        <td>{{ (!empty($samples->name)?$samples->name:null) }}</td>
                                                                        <th>Remarks</th>
                                                                        <td>{{ (!empty($style->stl_description)?$style->stl_description:null) }}</td>
                                                                    </tr>
                                                                </table>
                                                                                            
                                                            </div>
                                                    <!--    </div>
                                                    </div> -->
                                                    
                                                    
                                                </div> <!-- end col-md-9 -->
                                                <div class="col-md-2 col-sm-12"  >
                                           <!-- <div class="row">
                                                <div class="col-md-12"> -->
                                                            @if(count($styleImages) > 0)
                                                            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel" data-interval="2500">

                                                                <div class="carousel-inner" role="listbox">
                                                                    @foreach( $styleImages as $styleImage )
                                                                        <div class=" carousel-item {{ $loop->first ? 'active' : '' }}">   
                                                                            <div class="d-flex justify-content-center w-100 h-100 img-thumbnail">
                                                                                    <img class="img-fluid align-middle slide-image  widget-body" src="{{ asset(!empty($styleImage->image)?$styleImage->image:'assets/images/avatars/profile-pic.jpg') }}" alt="No Image">
                                                                                </div>    
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                
                                                                </a>
                                                                <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                
                                                                </a>
                                                            </div>
                                                                @else
                                                                <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                                                                    <div class="carousel-inner" role="listbox">
                                                                        <div class="carousel-item active">
                                                                            <div class="d-flex justify-content-center w-100 h-100">
                                                                                <img  class="d-block img-fluid slide-image  widget-body" src="{{ asset(!empty($style->stl_img_link)?$style->stl_img_link:'assets/images/avatars/profile-pic.jpg') }}" alt="No Image">
                                                                            </div>  
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                    @endif
                                               <!-- </div>
                                            </div> -->
                                            
                            
                                        </div>
                                


                                
                            </div>
                                
                                    
                                
                               
                                
                            
                        </div> <!-- end row -->
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                
                                                <!-- Basic Information -->
                                                <div class="panel panel-default printArea bomClass">
                                                    
                                                    <div class="panel-collapse "
                                                         id="basicInfo"  style="">
                                                        <div class=" table-responsive">
                                                           
                                                                
                                                                <br>
                                                                
                                                                <div class="widget-body" id="" style="border-radius:0; width: 100%;">
                                                                    <table 
                                                                           class="custom-font-table table table-bordered">
                                                                        <thead>
                                                                        <tr>
                                                                            <th>Main Category</th>
                                                                            <th>Item</th>
                                                                          
                                                                            <th>Item Description</th>
                                                                            <th >Supplier</th>
                                                                            <th width="100">Article No / Item Code</th>
                                                                            <th >Color/Shade</th>
                                                                            <th>Size/Width</th>
                                                                            
                                                                            
                                                                            <th >Thread Brand</th>
                                                                           
                                                                            <th >UoM</th>
                                                                            <th>Consumption</th>
                                                                            <th>Extra (%)</th>
                                                                            <th>Extra Qty</th>
                                                                            <th>Total</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        <?php if(count($styleCatMcats) == 0){ ?>
                                                                        <tr>
                                                                            <td colspan="15"><h4
                                                                                    class="text-center">No BOM
                                                                                    found for this style</h4>
                                                                            </td>
                                                                        </tr>
                                                                        <?php }else{ ?>
                                                                        
                                                                        @foreach ($styleCatMcats as $catwise)
                                                                            @foreach ($catwise as $styleCatMcat)

                                                                            <tr>
                                                                                <td>{{ $styleCatMcat->mcat_name}}</td>
                                                                                <td>{{ $styleCatMcat->item_name}}</td>
                                                                            
                                                                                <td>{{ $styleCatMcat->item_description}}</td>
                                                                                <td width="80">{{ $styleCatMcat->sup_name}}</td>
                                                                                <td width="80">{{ $styleCatMcat->art_name}}</td>
                                                                                <td width="80">{{ $styleCatMcat->clr_name}}</td>
                                                                                <td>{{ $styleCatMcat->size}}</td>
                                                                                
                                                                                
                                                                                <td>{{ $styleCatMcat->thread_brand}}</td>
                                                                                
                                                                                <td width="80">{{ $styleCatMcat->uom}}</td>
                                                                                <td>{{ $styleCatMcat->consumption}}</td>
                                                                                <td>{{ $styleCatMcat->extra_percent}}</td>
                                                                                <td><?= ($styleCatMcat->consumption * $styleCatMcat->extra_percent) / 100 ?></td>
                                                                            
                                                                                <td><?= $styleCatMcat->extra_percent != 0 ? (($styleCatMcat->consumption * $styleCatMcat->extra_percent) / 100) + $styleCatMcat->consumption: 0  ?></td> 
                                                                            </tr>
                                                                            @endforeach
                                                                        @endforeach
                                                                        <?php } ?>
                                                                        </tbody>
                                                                    </table>
                                                                 
                                                                    
                                                                </div><!-- /.col -->
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Advance Information -->
                                                
                                                
                                            
                            </div> <!-- END col md 12 -->
                            
                        
                        </div><!-- end row -->
                        
                    </div>  <!-- panel body  -->
                </div><!-- panel  -->
            </div> <!--/* page-content */ -->
        </div> <!-- /* main-content-inner */  -->
    </div> <!-- /* main-content */ -->
    @push('js')
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>

        <script type="text/javascript">

$(document).ready(function(){  
    
    
    
    $('#excel').click(function(){
      var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#printExcell').html())
      location.href=url;
      return false;
    });

});



    
</script>
          


    @endpush
@endsection
