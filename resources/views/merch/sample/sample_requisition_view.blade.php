@extends('merch.layout')
@section('title', 'Rfp')
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
                height: 200px;
                width: 200px;
                object-fit: cover;
            }

            .infoHeader{
                
                padding: 4px;
                font-weight: 700;
                color: #2196f3;
                background: #eef4f9;
                border-left: 3px solid #2196f3;
                padding-left:10px;
                font-size: 13px;
                
            }

            .hrad-space{
                display: inline-block;
                width: 122px;
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
            <a href="#">Sample</a>
        </li>
        <li>
            <a href="#">Sample List</a>
        </li>
        <li class="active">Sample Requisition VIEW</li>
        <li class="top-nav-btn">

            <a class="btn btn-sm btn-info hidden-print" href="/merch/sample/sample_requisition_view/{{ $id }}?export=excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download">
                                  <i class="fa fa-file-excel-o"></i>
                                </a>
            <button class="btn btn-xs btn-primary pull-right hidden-print"
                    onclick="printDivStyleProfile()"
                    style="border-radius: 5px;margin-left: 5px;
    height: 25px;"><span class="glyphicon glyphicon-print"
                                                      aria-hidden="true"></span> 
                                                      <i class="las la-print"></i>
            </button>
           
        </li>
    </ul><!-- /.breadcrumb -->
    
</div><!-- /* breadcrumbs */ -->

        <div class="page-content" id="printMe">
                <div class="panel">
                  <div class="panel-body">
                    <div class="container">
                    <div class="row">
                       <div class="col-md-12">
                         {{-- <div class="panel panel-info">
                            <div class="panel-heading">
                            <h4 class="panel-title infoHeader">

                            Sample RequRfpails

                            </h4>
                            </div>
                           </div> --}}
                           <p style="text-align:center;font-weight: bold; "> <strong style="font-size: 25px;">{{$unit->hr_unit_name}}</strong></p>
                        </div>
                    </div>
                    {{-- @php
                    dd($unit);
                    @endphp --}}
                    <div class="row">
                        <div class="col-md-12">
                                <div class="row ">
                                    <div class="col-md-12">
                                        {{-- class="widget-body" --}}
                                        <div  style="border-radius:0;">
                                            <div style="text-align: center;border: 2px solid #1b1b1c;font-weight:bold;font-size: 16px;"> SAMPLE REQUISITION FORM </div>
                                            <div style="padding-left:11px"  >
                                            <tr>
                                              <th style="width:50%;"> 
                                                <p style="margin:0;padding:4px 20px"><strong class="hrad-space">Atten </strong>: {{ $spl_man->as_name }}</p>
                                                <p style="margin:0;padding:4px 20px"><strong class="hrad-space">Merchandiser </strong >: {{ $req_merchandiser->as_name }} </p>
                                              </th>
                                            </tr>
                                            </div>
                                          <div style="padding-left:28px; ">
                                            <h4 style="font-weight: bold;font-size: 15px;"> PLS ARRANGE THE FOLLOWING SAMPLES. </h4>
                                          </div> 
                                      </div>
                                    </div> 
                                </div>     
        <div class="row" style="margin:5px auto;border:1px solid #ccc;" >
          {{-- <div style="border:1px solid #ccc;">   --}}
          <div class="col-md-3" >
          <p style="margin:0;padding:4px 10px"><strong >Buyer &nbsp;Name</strong></p>
          <p style="margin:0;padding:4px 10px"><strong >Style</strong></p>
          <p style="margin:0;padding:4px 10px"><strong >Order/Product No</strong></p>
          <p style="margin:0;padding:4px 10px"><strong >Garments &nbsp;Description</strong></p>
          <p style="margin:0;padding:4px 10px"><strong >Color</strong></p>
          <p style="margin:0;padding:4px 10px"><strong >Size</strong></p>
          <p style="margin:0;padding:4px 10px"><strong >Wash</strong></p>
          <p style="margin:0;padding:4px 10px"><strong >Type Of Sample</strong></p>
          <p style="margin:0;padding:4px 10px"><strong >Requesition Date</strong></p>
          <p style="margin:0;padding:4px 10px"><strong >Garment Delivery Date</strong></p>
        </div>
        <div class="col-md-3" style="border:1px solid #ccc;border-left-style: none;border-top-style: none;border-bottom-style: none;">
    <p style="margin:0;padding:4px 10px;"> <strong>:&nbsp;&nbsp;&nbsp;&nbsp;{{ $splview->buyer }}</strong></p>
      <p style="margin:0;padding:4px 10px"><strong>:&nbsp;&nbsp;&nbsp;&nbsp;{{ $splview->style }}</strong></p>
      <p style="margin:0;padding:4px 10px"><strong>:&nbsp;&nbsp;&nbsp;&nbsp;{{ $splview->prd_type_name }}</strong></p>
      <p style="margin:0;padding:4px 10px"><strong>:&nbsp;&nbsp;&nbsp;&nbsp;{{ $splview->style_description }}</strong></p>
      <p style="margin:0;padding:4px 10px"><strong>:&nbsp;&nbsp;&nbsp;&nbsp;{{ $splview->color }}</strong></p>
      <p style="margin:0;padding:4px 10px"><strong>:&nbsp;&nbsp;&nbsp;&nbsp;{{ $splview->size }}</strong></p>
      <p style="margin:0;padding:4px 10px"><strong>:&nbsp;&nbsp;&nbsp;&nbsp;{{ $splview->wash }}</strong></p>
      <p style="margin:0;padding:4px 10px"><strong>:&nbsp;&nbsp;&nbsp;&nbsp;{{ $splview->sample_name }}</strong></p>
      <p style="margin:0;padding:4px 10px"><strong>:&nbsp;&nbsp;&nbsp;&nbsp;{{ $splview->requisition_date }}</strong></p>
      <p style="margin:0;padding:4px 10px"><strong>:&nbsp;&nbsp;&nbsp;&nbsp;{{ $splview->send_date }}</strong></p>
        </div>
        {{-- </div> --}}
        <div class="col-md-3" style="border:1px solid #ccc; border-left-style: none;border-top-style: none;border-bottom-style: none;padding: 0;" >
                <table class="table table-bordered" >
                <thead>
                <tr>
                <th> Sample size</th>
                <th> Qty</th>
                </tr>

                </thead>
                <tbody>
                    @foreach($requsitsize as $key=>$value)
                    <tr>
                        <td> {{$key}} </td>
                        <td> {{$value}} </td>
                    </tr>
                    @endforeach
                    <tr>
                    <td style="font-weight: bold;">Total :</td> 
                    <td style="font-weight: bold;">{{ collect($requsitsize)->sum()}}</td> 
                    </tr>
                </tbody>
                </table>
        </div>
        {{-- class="table table-bordered" --}}
        <div class="col-md-3">
            <div class="row">
                                                <div class="col-md-12">
                                                            @if(count($styleImages) > 0)
                                                            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel" data-interval="2500">

                                                                <div class="carousel-inner" role="listbox">
                                                                    @foreach( $styleImages as $styleImage )
                                                                        <div class=" carousel-item {{ $loop->first ? 'active' : '' }}">   
                                                                            <div class="d-flex justify-content-center w-100 h-100 img-thumbnail">
                                                                                    <img class="img-fluid align-middle slide-image" src="{{ asset(!empty($styleImage->image)?$styleImage->image:'assets/images/avatars/profile-pic.jpg') }}" alt="No Image">
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
                                                                                <img width="60%" class="d-block img-fluid" src="{{ asset(!empty($stl->stl_img_link)?$stl->stl_img_link:'assets/images/avatars/profile-pic.jpg') }}" alt="No Image">
                                                                            </div>  
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                    @endif
                                                </div>
                                            </div>
        </div>
        </div>
                        </div>
                   </div>
        <div> {{-- BOM add --}}
          {!!$BomRender!!}   
        </div> {{-- End BOM --}}
                   </div>
 </div> <!--panel-body-->
            </div><!--main-content-inner-->
        </div><!--end main-content -->
        


                        {{-- <div class="row">
                            <div class="col-md-12">
                                <div id="accordion" class="accordion-style1 panel-group accordion-style2"> --}}
                                                <!-- Basic Information -->
                                                {{-- <div class="panel panel-default printArea bomClass">
                                                    <div class="panel-heading">
                                                        <h4 class="panel-title">
                                                            <a class="accordion-toggle" data-toggle="collapse"
                                                               data-parent="#accordion" href="#basicInfo"
                                                               aria-expanded="false">
                                                                <i class="bigger-110 las la-plus-circle"
                                                                   data-icon-hide="las la-minus-circle"
                                                                   data-icon-show="las la-plus-circle"></i>
                                                                BOM Information
                                                            </a>
                                                        </h4>
                                                    </div>
                                                    <div class="panel-collapse multi-collapse in collapse show"
                                                         id="basicInfo" aria-expanded="true" style="">
                                                        <div class="panel-body table-responsive">
                                                            <div class="profile-user-info">
                                                                <div class="widget-body">
                                                                    <div class="row" id="">
                                                                        <div class="col-sm-12">
                                                                            <table
                                                                                class="table custom-font-table"
                                                                                width="50%" cellpadding="0"
                                                                                cellspacing="0" border="0">
                                                                                <tr>
                                                                                    <th>Production Type</th>
                                                                                    <td>{{ (!empty($style->stl_type)?($style->stl_type == 'd' ? 'Development' : 'Bulk'):null) }}</td>
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
                                                                    </div>
                                                                </div>
                                                                <br>
                                                                <br>
                                                                <div class="widget-body" id="">
                                                                    <table id="bomItemTable"
                                                                           class="custom-font-table table table-bordered">
                                                                        <thead>
                                                                        <tr>
                                                                            <th>Main Category</th>
                                                                            <th>Item</th>
                                                                            <th>Item Code</th>
                                                                            <th>Description</th>
                                                                            <th width="80">Color</th>
                                                                            <th>Size/Width</th>
                                                                            <th width="80">Supplier</th>
                                                                            <th width="80">Article</th>
                                                                            <th>Composition</th>
                                                                            <th>Construction</th>
                                                                            <th width="80">UoM</th>
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
                                                                        <?php
                                                                        foreach ($styleCatMcats as $styleCatMcat) {?>
                                                                        <tr>
                                                                            <td>{{ $styleCatMcat->mcat_name}}</td>
                                                                            <td>{{ $styleCatMcat->item_name}}</td>
                                                                            <td>{{ $styleCatMcat->item_code}}</td>
                                                                            <td>{{ $styleCatMcat->item_description}}</td>
                                                                            <td width="80">{{ $styleCatMcat->clr_name}}</td>
                                                                            <td>{{ $styleCatMcat->size}}</td>
                                                                            <td width="80">{{ $styleCatMcat->sup_name}}</td>
                                                                            <td width="80">{{ $styleCatMcat->art_name}}</td>
                                                                            <td>{{ $styleCatMcat->comp_name}}</td>
                                                                            <td>{{ $styleCatMcat->construction_name}}</td>
                                                                            <td width="80">{{ $styleCatMcat->uom}}</td>
                                                                            <td>{{ $styleCatMcat->consumption}}</td>
                                                                            <td>{{ $styleCatMcat->extra_percent}}</td>
                                                                            <td><?= ($styleCatMcat->consumption * $styleCatMcat->extra_percent) / 100 ?></td>
                                                                           <!-- /* <td><?= $styleCatMcat->extra_percent / 100 ?></td> */ -->
                                                                           <!-- /* <td><?= $styleCatMcat->extra_percent != 0 ? $styleCatMcat->precost_unit_price + $styleCatMcat->extra_percent / 100 : 0  ?></td> */ -->
                                                                           <!-- /* <td><?= $styleCatMcat->extra_percent != 0 ? $styleCatMcat->precost_unit_price + (($styleCatMcat->consumption * $styleCatMcat->extra_percent) / 100) + $styleCatMcat->consumption: 0  ?></td> */ -->
                                                                            <td><?= $styleCatMcat->extra_percent != 0 ? (($styleCatMcat->consumption * $styleCatMcat->extra_percent) / 100) + $styleCatMcat->consumption: 0  ?></td> 
                                                                        </tr>
                                                                        <?php } ?>
                                                                        <?php } ?>
                                                                        </tbody>
                                                                    </table>
                                                                    <br>
                                                                    <br>
                                                                </div><!-- /.col -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                                
                                                <!-- Advance Information -->
                                                {{-- <div class="panel panel-default printArea costClass pnintNone">
                                                    
                                                    <div class="panel-heading pagebreak" >
                                                        
                                                        <h4 class="panel-title">
                                                            
                                                            <a class="accordion-toggle collapsed"
                                                               data-toggle="collapse" data-parent="#accordion"
                                                               href="#advanceInfo">
                                                                <i class="bigger-110 las la-plus-circle"
                                                                   data-icon-hide="las la-minus-circle"
                                                                   data-icon-show="las la-plus-circle"></i>
                                                                Costing Information
                                                            </a>
                                                        </h4>
                                                    </div>
                                                    <div class="panel-collapse collapse multi-collapse"
                                                         id="advanceInfo" >
                                                        <div id="costing" class="panel-body table-responsive">
                                                            <div class="profile-user-info">
                                                                <div class="widget-body" id="" >
                                                                    <table id="bomCostingTable"
                                                                           class="custom-font-table table table-bordered table-responsive">
                                                                        <thead>
                                                                        <tr>
                                                                            <th>Main Category</th>
                                                                            <th>Item</th>
                                                                            <th>Item Code</th>
                                                                            <th>Description</th>
                                                                            <th>Color</th>
                                                                            <th>Size / Width</th>
                                                                            <th>Article</th>
                                                                            <th>Composition</th>
                                                                            <th>Construction</th>
                                                                            <th>Supplier</th>
                                                                            <th>Consumption</th>
                                                                            <th>Extra (%)</th>
                                                                            <th>Unit</th>
                                                                            <th>Terms</th>
                                                                            <th>FOB</th>
                                                                            <th>L/C</th>
                                                                            <th>Freight</th>
                                                                            <th>Unit Price</th>
                                                                            <th>Total Price</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        <?php if(!empty($other_cost)){?>
                                                                        <?php if($other_cost->agent_fob != 0){?>
                                                                        <?php if(count($styleCatMcatFabs) > 0){?>
                                                                        <tr>

                                                                            <?php
                                                                            $netFab = 0;
                                                                            foreach ($styleCatMcatFabs as $styleCatMcat) {
                                                                            $thisUnit = $styleCatMcat->precost_unit_price;
                                                                            $thisTotal = ($thisUnit + $thisUnit * ($styleCatMcat->extra_percent / 100)) * $styleCatMcat->consumption;
                                                                            $netFab = $netFab + $thisTotal;
                                                                            ?>
                                                                            <td> {{ $styleCatMcat->mcat_name}}</td>
                                                                            <td>{{ $styleCatMcat->item_name}}</td>
                                                                            <td>{{ $styleCatMcat->item_code}}</td>
                                                                            <td>{{ $styleCatMcat->item_description}}</td>
                                                                            <td width="80">{{ $styleCatMcat->clr_name}}</td>
                                                                            <td>{{ $styleCatMcat->size}}</td>
                                                                            <td width="80">{{ $styleCatMcat->art_name}}</td>
                                                                            <td>{{ $styleCatMcat->comp_name}}</td>
                                                                            <td>{{ $styleCatMcat->construction_name}}</td>
                                                                            <td width="80">{{ $styleCatMcat->sup_name}}</td>
                                                                            <td>{{ $styleCatMcat->consumption}}</td>
                                                                            <td>{{ $styleCatMcat->extra_percent}}</td>
                                                                            <td width="80">{{ $styleCatMcat->uom}}</td>
                                                                            <td>{{ $styleCatMcat->bom_term}}</td>
                                                                            <td>{{ $styleCatMcat->precost_fob}}</td>
                                                                            <td>{{ $styleCatMcat->precost_lc}}</td>
                                                                            <td>{{ $styleCatMcat->precost_freight}}</td>
                                                                            <td>{{ $thisUnit}}</td>
                                                                            <th>{{ $thisTotal}}</th>
                                                                        </tr>
                                                                        <?php } ?>
                                                                        <tr>
                                                                        </tr>
                                                                        <tr>
                                                                            <th colspan="18"
                                                                                class="text-center"> Total
                                                                                Fabric Price
                                                                            </th>
                                                                            <th>{{ $netFab }}</th>
                                                                        </tr>
                                                                        <?php } ?>
                                                                        <?php if(count($styleCatMcatSwings) > 0){?>
                                                                        <tr>
                                                                            <?php
                                                                            $netSwings = 0;
                                                                            foreach ($styleCatMcatSwings as $styleCatMcat) {
                                                                            $thisUnit = $styleCatMcat->precost_unit_price;
                                                                            $thisTotal = ($thisUnit + $thisUnit * ($styleCatMcat->extra_percent / 100)) * $styleCatMcat->consumption;
                                                                            $netSwings = $netSwings + $thisTotal;
                                                                            ?>
                                                                            <td>{{ $styleCatMcat->mcat_name}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->item_name}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->item_code}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->item_description}}</td>
                                                                            <th width="80">
                                                                            {{ $styleCatMcat->clr_name}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->size}}</td>
                                                                            <th width="80">
                                                                            {{ $styleCatMcat->art_name}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->comp_name}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->construction_name}}</td>
                                                                            <th width="80">
                                                                            {{ $styleCatMcat->sup_name}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->consumption}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->extra_percent}}</td>
                                                                            <th width="80">
                                                                            {{ $styleCatMcat->uom}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->bom_term}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->precost_fob}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->precost_lc}}</td>
                                                                            <th>
                                                                            {{ $styleCatMcat->precost_freight}}</td>
                                                                            <th>
                                                                            {{ $thisUnit }} </td>
                                                                            <th>{{ $thisTotal }}</th>
                                                                        </tr>
                                                                        <?php } ?>
                                                                        <tr>
                                                                        </tr>
                                                                        <tr>
                                                                            <th colspan="18"
                                                                                class="text-center"> Total
                                                                                Sweing Accessories Price
                                                                            </th>
                                                                            <th>{{ $netSwings }}</th>
                                                                        </tr>
                                                                        <?php } ?>
                                                                        <?php if(count($styleCatMcatFinishing) > 0){?>
                                                                        <tr>
                                                                            <?php
                                                                            $netFinishing = 0;
                                                                            foreach ($styleCatMcatFinishing as $styleCatMcat) {
                                                                            $thisUnit = $styleCatMcat->precost_unit_price;
                                                                            $thisTotal = ($thisUnit + $thisUnit * ($styleCatMcat->extra_percent / 100)) * $styleCatMcat->consumption;
                                                                            $netFinishing = $netFinishing + $thisTotal;
                                                                            ?>
                                                                            <td>{{ $styleCatMcat->mcat_name}}</td>
                                                                            <td>{{ $styleCatMcat->item_name}}</td>
                                                                            <td>{{ $styleCatMcat->item_code}}</td>
                                                                            <td>{{ $styleCatMcat->item_description}}</td>
                                                                            <td width="80">{{ $styleCatMcat->clr_name}}</td>
                                                                            <td>{{ $styleCatMcat->size}}</td>
                                                                            <td width="80">{{ $styleCatMcat->art_name}}</td>
                                                                            <td>{{ $styleCatMcat->comp_name}}</td>
                                                                            <td>{{ $styleCatMcat->construction_name}}</td>
                                                                            <td width="80">{{ $styleCatMcat->sup_name}}</td>
                                                                            <td>{{ $styleCatMcat->consumption}}</td>
                                                                            <td>{{ $styleCatMcat->extra_percent}}</td>
                                                                            <td width="80">{{ $styleCatMcat->uom}}</td>
                                                                            <td>{{ $styleCatMcat->bom_term}}</td>
                                                                            <td>{{ $styleCatMcat->precost_fob}}</td>
                                                                            <td>{{ $styleCatMcat->precost_lc}}</td>
                                                                            <td>{{ $styleCatMcat->precost_freight}}</td>
                                                                            <td>{{ $thisUnit }} </td>
                                                                            <th>{{ $thisTotal }}</th>
                                                                        </tr>
                                                                        <?php } ?>
                                                                        <tr>
                                                                        </tr>
                                                                        <tr>
                                                                            <th colspan="18"
                                                                                class="text-center"> Total
                                                                                Finising Price
                                                                            </th>
                                                                            <th>{{ $netFinishing }}</th>
                                                                        </tr>
                                                                        <?php } ?>
                                                                        <?php if(count($special_operations) > 0) {?>
                                                                        <?php foreach ($special_operations as $special_operation) {?>
                                                                        <tr>
                                                                            <td colspan='10'
                                                                                class='text-center'>{{$special_operation->opr_name}}</td>
                                                                            <td>1</td>
                                                                            <td>0</td>
                                                                            <td>
                                                                                {{$special_operation->uom}}
                                                                            </td>
                                                                            <td colspan='4'></td>
                                                                            <td>
                                                                                {{$special_operation->unit_price}}
                                                                            </td>
                                                                            <td>
                                                                                {{$special_operation->unit_price}}
                                                                            </td>
                                                                        </tr>
                                                                        <?php } }?>
                                                                        <tr>
                                                                            <td colspan="10"
                                                                                class="text-center">Testing Cost
                                                                            </td>
                                                                            <td class="consumption">1</td>
                                                                            <td>0</td>
                                                                            <td>Piece</td>
                                                                            <td colspan="4"></td>
                                                                            <td>
                                                                                {{ isset($other_cost->testing_cost)?$other_cost->testing_cost:''}}
                                                                            </td>
                                                                            <td>
                                                                                {{ isset($other_cost->testing_cost)?$other_cost->testing_cost :''}}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="10"
                                                                                class="text-center">CM
                                                                            </td>
                                                                            <td class="consumption">1</td>
                                                                            <td>0</td>
                                                                            <td>Piece</td>
                                                                            <td colspan="4"></td>
                                                                            <td>
                                                                                {{ isset($other_cost->cm)?$other_cost->cm:''}}
                                                                            </td>
                                                                            <td>
                                                                                {{ isset($other_cost->cm)?$other_cost->cm:''}}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="10" class="text-right">
                                                                                Commertial Cost
                                                                            </td>
                                                                            <td>
                                                                            </td>
                                                                            <td colspan="6"
                                                                                class="text-left"></td>
                                                                            <td>
                                                                                {{ isset($other_cost->commercial_cost)?$other_cost->commercial_cost:''}}
                                                                            </td>
                                                                            <td>
                                                                                {{ isset($other_cost->commercial_cost)?$other_cost->commercial_cost:''}}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th colspan="18"
                                                                                class="text-center">Net FOB
                                                                            </th>
                                                                            <th>
                                                                                {{ isset($other_cost->net_fob)?$other_cost->net_fob:''}}
                                                                            </th>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="10" class="text-right">
                                                                                Buyer Commision
                                                                            </td>
                                                                            <td>
                                                                                {{ isset($other_cost->buyer_comission_percent)?$other_cost->buyer_comission_percent:''}}
                                                                            </td>
                                                                            <td colspan="6" class="text-left">
                                                                                %
                                                                            </td>
                                                                            <td>
                                                                                <?php
                                                                                $buyer_comm = number_format(($other_cost->net_fob * ($other_cost->buyer_comission_percent / 100)), 2, '.', '');
                                                                                ?>
                                                                                {{ $buyer_comm }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $buyer_comm }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th colspan="18"
                                                                                class="text-center">Buyer FOB
                                                                            </th>
                                                                            <th>
                                                                                {{ isset($other_cost->buyer_fob)?$other_cost->buyer_fob:''}}
                                                                            </th>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="10" class="text-right">
                                                                                Agent Commision
                                                                            </td>
                                                                            <td>
                                                                                {{ isset($other_cost->agent_comission_percent)?$other_cost->agent_comission_percent:''}}
                                                                            </td>
                                                                            <td colspan="6" class="text-left">
                                                                                %
                                                                            </td>
                                                                            <td>
                                                                                <?php
                                                                                $agent_comm = number_format(($other_cost->buyer_fob * ($other_cost->agent_comission_percent / 100)), 2, '.', '');
                                                                                ?>
                                                                                {{ $agent_comm }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $agent_comm }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th colspan="18"
                                                                                class="text-center">Agent FOB
                                                                            </th>
                                                                            <th>
                                                                                {{ isset($other_cost->agent_fob)?$other_cost->agent_fob:''}}
                                                                            </th>
                                                                        </tr>
                                                                        <tr>
                                                                            <th colspan="18"
                                                                                class="text-center">Total FOB
                                                                            </th>
                                                                            <th>
                                                                                {{ isset($other_cost->agent_fob)?$other_cost->agent_fob:''}}
                                                                            </th>
                                                                        </tr>
                                                                        <?php }else{?>
                                                                        <tr>
                                                                            <td colspan="19"><h4
                                                                                    class="text-center">No
                                                                                    Costing found for this
                                                                                    style</h4></td>
                                                                        </tr>
                                                                        <?php    } }else{ ?>
                                                                        <tr>
                                                                            <td colspan="19"><h4
                                                                                    class="text-center">No
                                                                                    Costing found for this
                                                                                    style</h4></td>
                                                                        </tr>
                                                                        <?php } ?>
                                                                        </tbody>
                                                                    </table>
                                                                    <br>
                                                                    <br>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                                <!-- Order Informantion  -->
                                                
                                            {{-- </div> --}}
                 
@push('js')
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
          
        <script type="text/javascript">
            $(document).ready(function () {
                /*
                * NEW BOM ITEM
                * -----------------------
                */
                function getItem(item_id, category_id) {
                    $.ajax({
                        url: "{{ url('merch/style_bom/get_item_data') }}",
                        data: {item_id, category_id},
                        success: function (data) {
                            $("#bomItemTable tbody").append(data);
                        },
                        error: function (xhr) {
                            console.log(xhr)
                        }
                    });
                }

                var modal = $("#newBomModal");
                $("body").on("click", "#newBomModalDone", function (e) {

                    // ------table actions----------
                    var table_item = [];
                    $("body #bomItemTable > tbody > tr").each(function (i, v) {
                        table_item.push($(this).attr("id"));
                    });

                    //-------- modal actions ------------------
                    modal.find('.modal-body input[type=checkbox]').each(function (i, v) {
                        var item = $(this).val();
                        var category = $(this).prev().val();
                        //--------------------------------------
                        if ($(this).prop("checked") == true) {
                            if (table_item.length == 0) {
                                getItem(item, category);
                            } else if (table_item.includes(item) == false) {
                                getItem(item, category);
                            }
                        } else {
                            $("body #bomItemTable > tbody").find('tr[id="' + item + '"]').remove();
                        }
                    });
                    modal.modal('hide');
                });

                // get item

                /*
                * --------CALCULATE TOTAL---------
                */
                $("body").on("keyup", ".calc", function () {
                    var consumption = $(this).parent().parent().find(".consumption").val();
                    var extra = $(this).parent().parent().find(".extra").val();
                    var qty = parseFloat(((parseFloat(consumption) / 100) * parseFloat(extra))).toFixed(2);
                    var total = (parseFloat(qty) + parseFloat(consumption)).toFixed(2);
                    $(this).parent().parent().find(".qty").val(qty);
                    $(this).parent().parent().find(".total").val(total);
                });


                /*
                * GET ARTICLE, COMPOSITION AND CONSTRUCTION
                * -------------------------------------------
                */
                $("body").on("change", ".supplier", function () {
                    var that = $(this);
                    // load article
                    $.ajax({
                        url: "{{ url('merch/style_bom/get_article_by_supplier') }}",
                        data: {
                            "supplier_id": that.val(),
                            "name": "mr_article_id[]",
                            "selected": "",
                            "option": {
                                "class": "form-control input-sm no-select",
                                "placeholder": "Select"
                            }
                        },
                        success: function (data) {
                            that.parent().next().html(data);
                        },
                        error: function (xhr) {
                            console.log(xhr)
                        }
                    });

                    // load composition
                    $.ajax({
                        url: "{{ url('merch/style_bom/get_composition_by_supplier') }}",
                        data: {
                            "supplier_id": that.val(),
                            "name": "mr_composition_id[]",
                            "selected": "",
                            "option": {
                                "class": "form-control input-sm no-select",
                                "placeholder": "Select"
                            }
                        },
                        success: function (data) {
                            that.parent().next().next().html(data);
                        },
                        error: function (xhr) {
                            console.log(xhr)
                        }
                    });

                    // load construction
                    $.ajax({
                        url: "{{ url('merch/style_bom/get_construction_by_supplier') }}",
                        data: {
                            "supplier_id": that.val(),
                            "name": "mr_construction_id[]",
                            "selected": "",
                            "option": {
                                "class": "form-control input-sm no-select",
                                "placeholder": "Select",
                                "data-validation": "required"
                            }
                        },
                        success: function (data) {
                            that.parent().next().next().next().html(data);
                        },
                        error: function (xhr) {
                            console.log(xhr)
                        }
                    });
                });


                /*
                * NEW ARTICLE
                * -------------------------------------------
                */
                $('.newArticleModal').on('show.bs.modal', function (e) {
                    var modal = $(this);
                    var button = $(e.relatedTarget);
                    var supplier_id = button.parent().parent().parent().parent().find(".supplier option:selected").val();
                    var supplier_name = button.parent().parent().parent().parent().find(".supplier option:selected").text();
                    modal.find("#supplier_id").val(supplier_id);
                    modal.find("#supplier_name").val(supplier_name);

                    // new article
                    $("#newArticle").on("submit", function (e) {
                        e.preventDefault();
                        var that = $(this);
                        $.ajax({
                            url: "{{ url('merch/style_bom/new_article') }}",
                            dataType: "json",
                            data: {
                                "supplier_id": that.find("#supplier_id").val(),
                                "article_name": that.find("#article_name").val(),
                                "name": "mr_article_id[]",
                                "selected": "",
                                "option": {
                                    "class": "form-control input-sm no-select article",
                                    "placeholder": "Select"
                                }
                            },
                            success: function (data) {
                                if (data.status) {
                                    button.parent().parent().parent().html(data.result);
                                    modal.find("#supplier_id").val("");
                                    modal.find("#supplier_name").val("");
                                    modal.find("#article_name").val("");
                                    modal.find(".message").html("");
                                    $('.newArticleModal').modal('hide');
                                    that.unbind('submit');
                                } else {
                                    modal.find(".message").html("<div class='alert alert-danger'>" + data.message + "</div>");
                                }
                            },
                            error: function (xhr) {
                                console.log(xhr)
                            }
                        });
                    });
                });

                /*
                * NEW COMPOSITION
                * -------------------------------------------
                */
                $('.newCompositionModal').on('show.bs.modal', function (e) {
                    var modal = $(this);
                    var button = $(e.relatedTarget);
                    var supplier_id = button.parent().parent().parent().parent().find(".supplier option:selected").val();
                    var supplier_name = button.parent().parent().parent().parent().find(".supplier option:selected").text();
                    modal.find("#supplier_id").val(supplier_id);
                    modal.find("#supplier_name").val(supplier_name);

                    // new article
                    $("#newComposition").on("submit", function (e) {
                        e.preventDefault();
                        var that = $(this);
                        $.ajax({
                            url: "{{ url('merch/style_bom/new_composition') }}",
                            dataType: "json",
                            data: {
                                "supplier_id": that.find("#supplier_id").val(),
                                "composition_name": that.find("#composition_name").val(),
                                "name": "mr_composition_id[]",
                                "selected": "",
                                "option": {
                                    "class": "form-control input-sm no-select article",
                                    "placeholder": "Select"
                                }
                            },
                            success: function (data) {
                                if (data.status) {
                                    button.parent().parent().parent().html(data.result);
                                    modal.find("#supplier_id").val("");
                                    modal.find("#supplier_name").val("");
                                    modal.find("#composition_name").val("");
                                    modal.find(".message").html("");
                                    $('.newCompositionModal').modal('hide');
                                    that.unbind('submit');
                                } else {
                                    modal.find(".message").html("<div class='alert alert-danger'>" + data.message + "</div>");
                                }
                            },
                            error: function (xhr) {
                                console.log(xhr)
                            }
                        });
                    });
                });


                /*
                * NEW CONSTRUCTION
                * -------------------------------------------
                */
                $('.newConstructionModal').on('show.bs.modal', function (e) {
                    var modal = $(this);
                    var button = $(e.relatedTarget);
                    var supplier_id = button.parent().parent().parent().parent().find(".supplier option:selected").val();
                    var supplier_name = button.parent().parent().parent().parent().find(".supplier option:selected").text();
                    modal.find("#supplier_id").val(supplier_id);
                    modal.find("#supplier_name").val(supplier_name);

                    // new article
                    $("#newConstruction").on("submit", function (e) {
                        e.preventDefault();
                        var that = $(this);
                        $.ajax({
                            url: "{{ url('merch/style_bom/new_construction') }}",
                            dataType: "json",
                            data: {
                                "supplier_id": that.find("#supplier_id").val(),
                                "construction_name": that.find("#construction_name").val(),
                                "name": "mr_construction_id[]",
                                "selected": "",
                                "option": {
                                    "class": "form-control input-sm no-select article",
                                    "placeholder": "Select"
                                }
                            },
                            success: function (data) {
                                if (data.status) {
                                    button.parent().parent().parent().html(data.result);
                                    modal.find("#supplier_id").val("");
                                    modal.find("#supplier_name").val("");
                                    modal.find("#construction_name").val("");
                                    modal.find(".message").html("");
                                    $('.newConstructionModal').modal('hide');
                                    that.unbind('submit');
                                } else {
                                    modal.find(".message").html("<div class='alert alert-danger'>" + data.message + "</div>");
                                }
                            },
                            error: function (xhr) {
                                console.log(xhr)
                            }
                        });
                    });
                });

                /*
                * COLOR LIST WITH BACKGROUND COLOR
                * -------------------------------------------
                */
                $("body").on("click", "select.color", function () {
                    $("body select.color option").each(function (i, v) {
                        $(this).css('background-color', $(this).text());
                    });
                    $(this).css('background-color', $(this).find('option:selected').text());
                });

            });
            // function myFunction() {
            //  $('#accordion .collapse').addClass("in");
            //  //document.body.style.visibility = 'hidden';
            //  //var WinPrint = window.open('', '', 'width=900,height=650');
            //  $('#costing ').removeClass("table-responsive");
            //    window.print();
            //     location.reload();

            //  }


  </script>
        
        <script type="text/javascript">
       function printMultipleDiv(){
        var divsToPrint = document.getElementsByClassName('printArea');
        var printContents = "";
        for (n = 0; n < divsToPrint.length; n++) 
        {
           printContents += divsToPrint[n].innerHTML+"<br>";
        }
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        }
 </script>

@endpush
@endsection
