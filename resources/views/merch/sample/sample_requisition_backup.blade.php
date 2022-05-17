@extends('merch.layout')
@section('title', 'Rfp')
@section('main-content')
@push('css')

<style>
.ui-autocomplete {
  position: absolute;
  z-index: 2150000000 !important;
  cursor: default;
  border: 2px solid #ccc;
  padding: 5px 0;
  border-radius: 2px;
}
.close-button {
  position: absolute;
  z-index: 100;
  right: 5px;
  border: none;
  padding: 4px 6px;
  color: #fff;
  font-size: 13px;
  top: -10px;
  background: rgb(8 155 171);
  border-radius: 50%;
  font-weight: 500;
}
.opr-item{
  border: 1px solid #d1d1d1;
  margin: 3px;
}


@media only screen and (max-width: 767px) {

  .modal{margin-top: 45px;}
  .checkbox label input[type=checkbox].ace+.lbl, .radio label input[type=radio].ace+.lbl{margin-left: 10px;}
  input[type=checkbox].ace+.lbl, input[type=radio].ace+.lbl{ margin-left: 10px; }

}
@media only screen and (max-width: 480px) {

  .modal{margin-top: 85px;}
  .checkbox label input[type=checkbox].ace+.lbl, .radio label input[type=radio].ace+.lbl{margin-left: 10px;}
  input[type=checkbox].ace+.lbl, input[type=radio].ace+.lbl{margin-left: 10px;}
  .modalDiv .col-xs-8 {width: 100% !important; padding-top: 10px;}
  .modalDiv .col-xs-4 {padding-left: 0px;}

}

.slide_upload {
  width: auto;
  height: 100px;
  position: relative;
  cursor: pointer;
  background: #eee;
  border: 1px solid rgb(8 155 171);
  border-radius: 5px;
  overflow: hidden;
}
.slide_upload img {
  width: 100%;
  padding: 2px;
  object-fit: cover;
}
.slide_upload::before{content: "+";position: absolute;top: 50%;color: rgb(8 155 171);left: 50%;font-size: 52px;margin-left: -17px;margin-top: -37px;}

span.toggle-handle.btn.btn-light {
  width: 44px !important;
}

</style>

<div class="main-content">
  <div class="main-content-inner">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
      <ul class="breadcrumb">
        <li>
          <i class="ace-icon fa fa-home home-icon"></i>
          <a href="#">Merchandising</a>
        </li>
        <li>
          <a href="#">Sample</a>
        </li>
        <li class="active">Sample Requisition</li>
      </ul><!-- /.breadcrumb -->

    </div>
    @include('inc/message')
    <div class="panel">
      <div class="panel-body">

        <div class="style_section">
          <form action="{{url('merch/sample/sample_req_store')}}" method="post" >
            @csrf
            <div class="row">
              <div class="col-sm-9">
                <div class="row mt-3">

                  <div class="col-sm-4" id="buyerSection">

                    @php
                    if (request()->bNewId) {
                      $bNewId = request()->bNewId;
                    }
                    @endphp

                    <div class="form-group has-float-label select-search-group has-required">
                      {{ Form::select('buyer', $buyer, null, ['id'=> 'buyer', 'placeholder' => 'Select Buyer', 'required' => 'required']) }}
                      <label for="buyer" >Buyer</label>
                    </div>

                    <div class="form-group has-float-label select-search-group has-required">

                      {{ Form::select('mr_brand_br_id', $brand, null, ['id'=> 'mr_brand_br_id', 'placeholder' => 'Select Brand', 'required' => 'required']) }}
                      <label for="mr_brand_br_id" >Brand</label>
                    </div>

                    <div class="form-group has-float-label select-search-group">

                      {{ Form::select('prd_type_id', $productType, null, ['placeholder'=>'Select Product Type', 'class'=> 'col-xs-12  form-control', 'id'=>'prd_type_id', 'required' => 'required']) }}
                      <label for="prd_type_id" > Product Type  </label>
                    </div>
                    <div class="form-group has-float-label select-search-group">
                      {{ Form::select('gmt_id', $garmentsType, null, ['placeholder'=>'Please Select Garments Type', 'id'=>'gmt_id', 'class'=> 'form-control', 'required' => 'required']) }}
                      <label for="gmt_id" > Garments Type  </label>
                      @hasanyrole("Super Admin|merchandiser")
                      <div class="col-sm-1 col-xs-1" style="padding-left: 0px;position: absolute;z-index: 10;top: 3px;right: -10px;">
                        <button class="addart btn btn-sm btn-primary" style=" padding-bottom: 2px; padding-right: 0px; padding-left: 1px; display: none;" data-toggle="modal" data-target="#new_garments_type" id="new_garments_type_btn_id" type="button"><i class="fa fa-plus"></i></button>
                      </div>
                      @endhasanyrole
                    </div>


                    <div class="form-group has-float-label select-search-group">
                      <select  name="product_category" id="product_category" class="form-control">  
                          <option  selected="" disabled="" value=""> Select Product Category </option>
                          <option value="1">Good</option>
                          <option value="2">Batter</option>
                          <option value="3">Best</option>
                       </select>
                      <label for="product_category" > Product Category  </label>
                    </div>

                    <div class="form-group has-float-label select-search-group has-required">
                      {{ Form::select('se_id', $season, null, ['placeholder'=>'Please Select season', 'id'=>'se_id', 'class'=> 'form-control col-xs-12 ', 'required' => 'required']) }}
                      <label for="season_id"> Season </label>
                    </div>

                    <div class="form-group has-float-label select-search-group">
                      {{ Form::select('mr_sample_style[]', $sampleType, null, ['id'=>'mr_sample_style', 'class'=> 'form-control ','multiple']) }}
                      <label for="mr_sample_style"> Sample Type  </label>
                    </div>

                    <div class="form-group">
                      <button style="width: 100px;" class="btn btn-success" type="submit">
                        Save  &nbsp;
                      </button>
                    </div>
                  </div>
                  <div class="col-sm-4" id="buyerSection">

                    <div class="form-group has-float-label select-search-group has-required">
                      {{ Form::select('mr_style', $style, null, ['id'=> 'mr_style', 'placeholder' => 'Select Style', 'required' => 'required']) }}
                      <label for="mr_style" >Style</label>
                    </div>

                    <div class="form-group has-float-label">
                      <input type="text" id="stl_code" name="stl_code" placeholder="Enter Value" class=" form-control" autocomplete="off" required="number" required-allowing="float"/>
                      <label for="stl_code" > Style Code </label>
                    </div>

                    <div class="form-group has-float-label">
                      <input type="text" id="stl_des" name="stl_des" placeholder="Enter Value" class=" form-control" autocomplete="off" required="number" required-allowing="float"/>
                      <label for="stl_des" > Style Description </label>
                    </div>
                    <div class="form-group has-float-label">
                      <input type="text" id="color" name="color" placeholder="Enter Value" class=" form-control" autocomplete="off" required="number" required-allowing="float"/>
                      <label for="color" > Color </label>
                    </div>

                    <div class="form-group has-float-label">
                      <input type="text" id="size" name="size" placeholder="Enter Value" class=" form-control" autocomplete="off" required="number" required-allowing="float"/>
                      <label for="size" > Size/Width </label>
                    </div>

                    <div class="form-group has-float-label">
                      <input type="text" id="qty_id" name="qty_id" placeholder="Enter Value" class=" form-control" autocomplete="off" required="number" required-allowing="float"/>
                      <label for="qty_id" > Quantity </label>
                    </div>
                    
                    <div class="form-group has-float-label has-required">
                      <input type="date" class="report_date datepicker form-control" id="report_from" name="send_date" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" />
                      <label for="send_date">Send date</label>
                    </div>

                  </div>

                  <div class="col-sm-4" id="buyerSection">
                    <div class="form-group has-float-label select-search-group has-required">

                      {{ Form::select('Supplier_id',$supplier, null, ['placeholder'=>'Select Supplier', 'id'=>'supplier_id', 'class'=> 'form-control col-xs-12 ', 'required' => 'required']) }}

                      <label for="Supplier_id"> Supplier </label>
                    </div>

                    <div class="form-group has-float-label select-search-group has-required">
                      {{ Form::select('artical_id',$artical, null, ['placeholder'=>'Please Select artical', 'id'=>'artical_id', 'class'=> 'form-control col-xs-12 articals ', 'required' => 'required']) }}
                      <label for="artical_id"> Artical </label>
                    </div>
                  

                    {{-- <div class="form-group has-float-label select-search-group has-required">
                      {{ Form::select('artical_id',$artical, null, ['placeholder'=>'Please Select artical', 'id'=>'artical_id', 'class'=> 'form-control col-xs-12 ', 'required' => 'required']) }}
                      <label for="artical_id"> Construction </label>
                    </div> --}}

                    <input type="hidden" name="consumption_id">
                    <div class="form-group has-float-label has-required ">
                        <input type="text" name="composition" id="composition" placeholder="No composition"  readonly  class="form-control" />
                        <label for="composition"> composition </label>
                    </div>

                    <div class="form-group has-float-label select-search-group has-required">
                      {{ Form::select('sample_man',$sample_man, null, ['placeholder'=>'Please Select sample man', 'id'=>'sample_man', 'class'=> 'form-control col-xs-12 ', 'required' => 'required']) }}
                      <label for="sample_man"> Responsible Sample Man </label>
                    </div>

                    <div class="form-group has-float-label  wash" >
                      {{-- style="display:none;" --}}
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" id="washTypeModalId" data-target="#washTypeSelectModal" style="border-radius: 5px;">Select Wash Type</button>
                                       @hasanyrole("Super Admin|merchandiser")
                                            <a href="{{ url('merch/setup/wash_type') }}" class="btn btn-sm btn-info" target="_blank"><i class="fa fa-plus"></i></a>
                                        @endhasanyrole
                    </div>
                    <div  id="show_selected_wash_type" class="mb-3"></div>

                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- Select Wash Data  -->

<div class="modal fade" id="washTypeSelectModal" tabindex="-1" role="dialog" aria-labelledby="sizeLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Wash Type</h4>
      </div>
      <div class="modal-body" style="padding:0 15px">
        <div class="row" id="washTypeModalBody" style="padding: 20px;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
        <button type="button" id="washTypeSelectModalDone" class="btn btn-primary btn-sm">Done</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">

//Show Selected Wash Type from Modal

    var wmodal = $("#washTypeSelectModal");
    $("body").on("click", "#washTypeSelectModalDone", function(e) {
        var data="";
        var tr_end = 0;
        //-------- modal actions ------------------
        data += '<table class="table table-bordered" style="margin-bottom:0px;">';

        data += '<tbody>';
        wmodal.find('.modal-body input[type=checkbox]').each(function(i,v) {
            if ($(this).prop("checked") == true) {
                if((i/10) % 1 === 0) {
                    data += '<tr>';
                    tr_end = i+9;
                }
                data += '<td style="border-bottom: 1px solid lightgray;">'+$(this).next().text()+'</td>';
                data+= '<input type="hidden" name="wash[]" class="washType" value="'+$(this).val()+'"></input>';
                if(tr_end == 10) {
                    data += '</tr>';
                }
            }
        });
        data += '</tbody>';
        data += '</table>';
        wmodal.modal('hide');
        console.log(data);
        $("#show_selected_wash_type").html(data);
    });

    // Data select wash

    $('#washTypeModalId').on('click', function() {
        var checkedWashList = [];
        $('input.washType').each(function(i,v) {
            if($(this).val()) {
                checkedWashList[i] = $(this).val();
            }
        });
        $.ajax({
            url : "{{ url('merch/sample/washgroup') }}",
            // merch/sample/washgroup  //--// merch/style/fetchwashgroup
            type: 'post',
            data: {
             checkedWash: checkedWashList
         },
         headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        dataType: 'json',
        success: function(data)
        {
         $('#washTypeModalBody').html(data);
     },
     error: function(error)
     {
        // alert('failed...');
        console.log(error)
    }
    });
    });

    // Consumption

    $(document).ready(function()
{  
   
    $('#dataTables').DataTable({
            pagingType: "full_numbers" ,
    });


        

    //Composition search 
    $("body").on('change', ".articals", function(){
        $.ajax({
            url: '{{ url("merch/sample/sample_requisition_consumption") }}',
            type: 'get',
            dataType: 'json',
            data: {artical_id: $(this).val()},
            success: function(data)
            {
              console.log(data.composition);
              // $('#composition').html(data.composition);
              $("input[name=composition]").val(data.composition);
            },
            // { 
            //     $('#ASS').text(data.designation);
            //     if (data.status)
            //     { 
            //         $('#avatar').attr('src',data.as_pic);
            //         $('#user-name').text(data.as_name);
            //         $('#designation').text(data.previous_designation);
            //         $('#section').text(data.section);
            //         $('.promotion-history').html(data.history);

            //         $("select[name=current_designation_id").html("").append(data.designation);
            //         $('select[name=current_designation_id').trigger('change'); 

            //         $("input[name=eligible_date]").val(data.eligible_date);
            //         $("input[name=previous_designation]").val(data.previous_designation);
            //         $("input[name=previous_designation_id]").val(data.previous_designation_id);
            //         $(".output").addClass("hide");
            //         $('.app-loader').hide();
            //     }
            //     else
            //     {
            //         $("input[name=eligible_date]").val(""); 
            //         $("input[name=previous_designation]").val("");
            //         $("input[name=previous_designation_id]").val("");
            //         $(".output").removeClass("hide").addClass("alert-danger").html(data.error);
            //         $('.app-loader').hide();
            //     }         
            // },
            error: function(xhr)
            {
                // $('.app-loader').hide();
                console.log("error");
            }
        });        
    });

});
    

</script>
@endsection




