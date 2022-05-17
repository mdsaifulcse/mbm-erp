@extends('hr.layout')
@section('title', 'Partial Salary')

@section('main-content')
@push('js')
<style>
table tr p span{
    font-size: 10px !important;
}
table td, table th{
    vertical-align: top !important;
}
.table td {
    padding: 5px;
}
.panel-body {
    padding: 10px 8px;
}
h3 {
    font-size: 1rem;
}
h2, h2 b {
    font-size: 1.5rem;
}
#top-tab-list li a {
    border: 1px solid;
}
.iq-accordion.career-style .iq-accordion-block {
    margin-bottom: 15px;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    margin-top: 10px;
}
p span, p span font{
    font-size: 10px;
}
.iq-accordion-block{
    padding: 10px 0;
}

.select2-container--default .select2-selection--multiple {
    border-color: #d7dbda;
    height: 80px;
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
                    <a href="#">Operation</a>
                </li>
                <li class="active"> Partial Salary</li>
            </ul>
        </div>

        <div class="page-content"> 

            <div class="iq-accordion career-style mat-style  ">

                <div class="iq-card iq-accordion-block accordion-active">

                    <div class="active-mat clearfix">

                        <div class="container-fluid">

                            <div class="row">
                                <div class="col-sm-12"><a class="accordion-title"><span class="header-title"> Partial Salary Initialised </span> </a>
                                </div>
{{--     <div class="col-sm-6"><a class="accordion-title"><span class="header-title"> For Ot Holder </span> </a>
</div> --}}
</div>
</div>
</div>

<div class="accordion-details">
    <div class="row1">
        <div class="col-12">
            {{-- <form class="" role="form" id="unitWiseSalary">  --}}
             <form  action="{{url('hr/operation/partial-salary-create')}}" method="post" enctype="multipart/form-data" autocomplete="off" >
                {{csrf_field()}}
                <div class="panel mb-0">

                    <div class="panel-body pb-0">
                        <div class="row">
                     


                            <div class="col-3">


                                    <input type="text" class="form-control capitalize select-search" id="id" name="id"  hidden value="">                       
                                


                                <div class="form-group has-float-label select-search-group">
                                    
                                    <select name="unit" class="form-control capitalize select-search" id="unit" >
                                       {{--           <select name="unit[]" class="form-control capitalize select-search" id="unit" multiple> --}}

                                        {{-- <option selected="" value="">All Units</option> --}}
                     {{--    @if(array_intersect(auth()->user()->unit_permissions(), [1,4,5]))
                            <optionFW + SRT</option>
                            <option value="14">MBM + MFW </option>
                            <option value="15">MBM + SRT </option>
                            @endif --}}
                            @foreach($unitList as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach

                        </select>
                        <label for="unit">Unit</label>
                    </div>



                    <div class="form-group has-float-label select-search-group">
                        <select name="otnonot" class="form-control capitalize select-search" id="otnonot" >
                           {{--  <option selected="" value="">Choose...</option> --}}
                           <option value="0">Non-OT</option>
                           <option value="1">OT</option>
                       </select>
                       <label for="otnonot">OT/Non-OT</label>
                   </div>
                   {{-- value="{{ old('from_date')}}" --}}

                   <div class="form-group has-float-label has-required">
                    <input type="date" class="report_date datepicker form-control" id="from_date" name="from_date" placeholder="Y-m-d" required="required" value="{{(old('from_date')??date('Y-m-d'))  }}" autocomplete="off" min="{{ date('Y-m-01') }}" max="{{ date('Y-m-t') }}"   />
                    <label for="from_date">Date From</label>
                </div>

                <div class="form-group has-float-label has-required">
                  <input type="date" class="report_date datepicker form-control" id="to_date" name="to_date" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" 
                  min="{{ date('Y-m-01') }}" max="{{ date('Y-m-t') }}" />
                  <label for="to_date">Date To</label>
              </div>







          </div>

          <div class="col-3">


           <div class="form-group has-float-label select-search-group" style="height:85px">
            <select name="location[]" class="form-control capitalize select-search" id="location" multiple >
                {{-- <option selected="" value="">All Location</option> --}}
                @foreach($locationList as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            <label for="location">Location</label>
        </div>

        <div class="form-group has-float-label select-search-group"style="height:80px">
            <select name="area[]" class="form-control capitalize select-search" id="area" multiple>
                {{-- <option selected="" value="">All Area</option> --}}
                @foreach($areaList as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            <label for="area">Area</label>
        </div>





    </div>
    <div class="col-3">
        <div class="form-group has-float-label select-search-group">
            <select name="otgiven" class="form-control capitalize select-search" id="otgiven" >
               {{--  <option selected="" value="">Choose...</option> --}}
               <option value="YES">YES</option>
               <option value="NO">NO</option>
           </select>
           <label for="otgiven">OT Allow</label>
       </div>

       <div class="form-group has-float-label has-required">
        <input type="date" class="report_date datepicker form-control" id="from_date1" name="from_date1" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" 
        min="{{ date('Y-m-01') }}" max="{{ date('Y-m-t') }}"/>
        <label for="from_date1">Date From</label>
    </div>


    <div class="form-group has-float-label has-required">
      <input type="date" class="report_date datepicker form-control" id="to_date1" name="to_date1" placeholder="Y-m-d" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" min="Y-m-01" max="Y-m-t" 
      min="{{ date('Y-m-01') }}" max="{{ date('Y-m-t') }}"/>
      <label for="to_date1">Date To</label>
  </div>



</div>   

<div class="col-3">
    <div class="form-group has-float-label select-search-group">
       <input type="number" class="report_date datepicker form-control" id="salary" name="salary" placeholder="0" required="required" value="600000" autocomplete="off" />
       <label for="salary">Salary Less Than</label>
   </div>



   <div class="form-group">
     <button class="btn btn-primary nextBtn btn-lg pull-right" type="submit" id="btn_save" value="Submit"><i class="fa fa-save"></i> Save</button>
 </div>



    <div class="form-group">
     <input type="button" class="btn btn-success nextBtn btn-lg pull-right"   id="btn_update" value="Update" style="width:82px">
    </div>

<br><br>

    <div class="form-group">
     <input type="button" class="btn btn-warning nextBtn btn-lg pull-right"   id="btn_clear" value="Clear " style="width:82px">
    </div>


</div>  


</div> 


</div>
</div>


<!-- PAGE CONTENT ENDS -->
</div>
<!-- /.col -->
</form>
</div>
</div>
</div>
{{-- </form>    --}}
</div>


</div><!-- /.page-content -->
</div>
</div>


<div id="print">    
    <div  class="main-content" >

        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">

{{--    <h4  style="margin:4px 10px; font-weight: bold; text-left: center;">
<p id="header" style="width:auto;text-left:center;">

</p> 

ddd

</h4> --}}
<div calss="container" style="width:100%;">
   
    <div class="row">
        <div class="col-3"> 
            <div class="form-group has-float-label select-search-group">
                <select name="unit1" class="form-control capitalize select-search" id="unit1" >
                    {{-- <option selected="" value="">All Units</option> --}}
                    @if(array_intersect(auth()->user()->unit_permissions(), [1,4,5]))
                    {{-- <optionFW + SRT</option> --}}
                        <option value="145">MBM +MBM2 + MFW </option>
                        @endif
                        @foreach($unitList as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach

                    </select>
                    <label for="unit">Unit</label>
                </div>
            </div>    
            <div class="col-2"> 
                <div class="form-group has-float-label select-search-group">
     
                     <select id="month" name="month" class="form-control capitalize select-search"  >

                        @foreach($monthyear as $key => $value)
                        
                        <option value="{{$value}}">{{$value}}</option>

                        @endforeach
                    </select>
                      <label for="Month">Month</label>
                </div>
            </div>

            <div class="col-2"> 

               <div class="form-group">
                 {{--                  <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" onClick="filter()"><i class="fa fa-save"></i> Save</button> --}}
                 <button onClick="filter()" class="btn btn-primary nextBtn btn-lg pull-right" type="submit" id="View" value="Submit"><i class="fa fa-save"></i> View</button>

             </div>

         </div>



     


 </div>
</div>




<div class="container" id="tabledata">
    {{-- Ajax data return with table  --}}

    {{-- @include('hr.operation.salary.partial_salary_loadtabledata') --}}
</div>

<div class="container" id="tabledata1">
    {{-- Ajax data return with table  --}}

    {{-- @include('hr.operation.salary.partial_salary_loadtabledata') --}}
</div>






</ul>

</div>

</div>

</div>


</div>







@push('js')


<script type="text/javascript">



    $( "#from_date1" ).prop( "disabled", true );
    $( "#to_date1" ).prop( "disabled", true );
    $( "#otgiven" ).prop( "disabled", true );
    $( "#btn_update" ).hide();
    $('#otgiven').val('NO').trigger('change');

//   $("#month").datepicker({
//     viewMode: 'years',
//      format: 'mm-yyyy'
// });


$(document).on('change','#otgiven222,#otnonot', function(){
    call1($(this).val());

});

function call1(id = null) {
    if(id==0){
        $( "#otgiven" ).prop( "disabled", true );
        $( "#location" ).prop( "disabled", false );
        $( "#area" ).prop( "disabled", false );
        $( "#salary" ).prop( "disabled", false );



    }else{

        $( "#from_date1" ).prop( "disabled", true );
        $( "#to_date1" ).prop( "disabled", true );
        $( "#otgiven" ).prop( "disabled", false );
        $( "#location" ).prop( "disabled", true );
        $( "#area" ).prop( "disabled", true );
        $( "#salary" ).prop( "disabled", true );
        $('#otgiven').val('NO').trigger('change');
    }

}

$(document).on('change','#otgiven,#otnonotxxx', function(){
    call2($(this).val());
});


function call2(id = null) {
    if(id =='YES'){
        $( "#from_date1" ).prop( "disabled", false );
        $( "#to_date1" ).prop( "disabled", false );
    }else{
        $( "#from_date1" ).prop( "disabled", true );
        $( "#to_date1" ).prop( "disabled", true );

    }

}


$(document).on('click','.btn_delete', function(){
     if(confirm("Are you sure you want to Delete it now..?")){
         call_delete($(this).data('id'));
    }
    else{
        return false;
    }    
});

function call_delete(id = null) {
    
    $.ajax({
        type:'get',
        url: '{{url("hr/operation/partial-salary-data-delete/")}}/'+id,           
        data:{
        
        },
        success:function(data){
            if (data=='success') {
                $('.delete-'+id).remove();
            }
            // console.log('Delete Success..');
            // $('#tabledata').html(data);
}
});
}




$("#btn_save").click(function(){
    if(confirm("Are you sure you want to save it now..?")){
    }
    else{
        return false;
    }
});



$(document).on('change','#unit1,#month', function(){
  callAjax($(this).val());   
   $( "#tabledata1" ).hide();
  });

function filter(){
   
    callAjax();   
};


function callAjax(id = null) {
     clear_all();
    $('#tabledata').html(loaderContent);
    $.ajax({
        type:'get',
        url: '{{url("hr/operation/partial-salary-data-load")}}',           
        data:{
            'unit1':$('#unit1').val(),
            'month':$('#month').val()
        },
        success:function(data){
            $('#tabledata').html(data);
// $("#header").html( 'Partial Salary Process <br> Report Type : '+$("#Type option:selected").text()+'<br>'+$("#unit option:selected").text()
//     + $("#subsection option:selected").text() +'<br>' +$("#otnonot option:selected").text() + '<br> Report Run Date Between : '
//     +$('#from_date').val()  + ' And '+$('#to_date').val() );
// $("#sectionsubsection").html( $("#section option:selected").text() +'::::' + $("#subsection option:selected").text());
}
});
}


$(document).on('click','.Process', function(){
     if(confirm("Are you sure you want to process it now..?")){
         find_emp($(this).data('id'));
         $( "#tabledata1" ).show();
    }
    else{
        return false;
    }    
});

function find_emp(id = null) {
    $.ajax({
        type:'get',  
        url: '{{url("hr/operation/partial-salary-find-emp/")}}/'+id,   
      
        data:{
        },
        success:function(data){
             $('#tabledata1').html(data);

           
}
});
}

$(document).on('click','.unitdlt', function(){
         unitdtls($(this).data('id'));  
          $( "#tabledata1" ).show();
});

function unitdtls(id = null) {
    // $('#tabledata').html(loaderContent);
    $.ajax({
        type:'get',  
        url: '{{url("hr/operation/partial-salary-unitdtl/")}}/'+id,   
      
        data:{
            'month':$('#month').val(),
        },
        success:function(data){

             $('#tabledata1').html(data);
           
}
});
}

 $(document).on('click','#Approval', function(){
    if(confirm("Are you sure you want submit for approval..?")){
    }
    else{
        return false;
    }
});


    $(document).on('click','.Approval', function(){
    approve_summit($(this).data('id'));  
    });

    function approve_summit(id = null) {
    // $('#tabledata').html(loaderContent);
    $.ajax({
    type:'get',  
    url: '{{url("hr/operation/partial-salary-submitforapprove/")}}/'+id,   

    data:{
    'month':$('#month').val(),
    },
    success:function(data){
    $.notify(data.msg, data.type)
    if (data.type=='success') {
     callAjax();   
    }
    $('#tabledata1').html(data);

    }
    });
    }



// $(document).on('click','.locksalary', function(){
//     if(confirm("Are you sure you want lock salary for audit..?")){
//     }
//     else{
//         return false;
//     }
// });


    $(document).on('click','#locksalary', function(){
           if(confirm("Are you sure you want lock salary for audit..?")){
            locksalary($(this).data('id'));  
            }
            else{
                 return false;
            }
    
    });

    function locksalary(id = null) {
    // $('#tabledata').html(loaderContent);
    $.ajax({
    type:'get',  
    url: '{{url("hr/operation/partial-salary-locksalary/")}}/'+id,   
    data:{
    'month':$('#month').val(),
    },
    success:function(data){
    $.notify(data.msg, data.type)
    if (data.type=='success') {
     callAjax();   
    }
    $('#tabledata1').html(data);

    }
    });
    }


$(document).on('click','#updatedata', function(){
    updatedata2($(this).data('id'));  
    });

    function updatedata2(id = null) {
    // $('#tabledata').html(loaderContent);
    $.ajax({
    type:'get',  
    url: '{{url("hr/operation/partial-salary-getupdatedata/")}}/'+id,   
    data:{
    'month':$('#month').val(),
    },
    success:function(data){
    // $.notify(data.msg, data.type)
    // if (data.type=='success') {
    //  callAjax();   
    // }
    // $('#tabledata1').html(data);
    if (data.master.ot_give_status==='N'){
         var x='NO';
         var today = new Date();
         var date = '{{date("Y-m-d")}}';
         var y=date;
         var y1=date;
    }else{
         var x='YES';
         var y=data.master.ot_from_date;
         var y1=data.master.ot_to_date;
    }

    $('#id').val(data.master.id).trigger('change');
    $('#unit').val(data.master.unit_id).trigger('change');
    $('#otnonot').val(data.master.as_status).trigger('change');
    $('#from_date').val(data.master.salary_from_date);
    $('#to_date').val(data.master.salary_to_date);
    $('#location').val(data.location).trigger('change');
    $('#area').val(data.area).trigger('change');
    $('#otgiven').val(x).trigger('change');
    $('#from_date1').val(y);
    $('#to_date1').val(y1);
    $('#salary').val(data.master.salary_below);
    // $('#location').val('').trigger('change');
    $( "#btn_update" ).show();
    $( "#btn_save" ).hide();
    

    }
    });
    }



    $(document).on('click','#btn_clear', function(){
        clear_all();
        });

 function clear_all(id = null) {
    $('#id').val('');
    $('#unit').val(1).trigger('change');
    $('#otnonot').val(0).trigger('change');
    $('#from_date').val('{{date("Y-m-d")}}');
    $('#to_date').val('{{date("Y-m-d")}}');
    $('#location').val('').trigger('change');
    $('#area').val('').trigger('change');
    $('#otgiven').val('NO').trigger('change');
    $('#from_date1').val('{{date("Y-m-d")}}');
    $('#to_date1').val('{{date("Y-m-d")}}');
    $('#salary').val(600000);
    // $('#location').val('').trigger('change');
    $( "#btn_update" ).hide();
    $( "#btn_save" ).show(); 
}
    

    

    $(document).on('click','#btn_update', function(){
         if(confirm("Are you sure  want to update it now")){
            btn_update();  
            }
            else{
                 return false;
            }
    
    });

    function btn_update(id = null) {
    // $('#tabledata').html(loaderContent);
    $.ajax({
    type:'post',  
    url: '{{url("hr/operation/partial-salary-updatedata")}}',   
    data:{
    // 'month':$('#month').val(),
    '_token': '{{csrf_token()}}',
    'id':$('#id').val(),
    'unit':$('#unit').val(),
    'otnonot':$('#otnonot').val(),
    'from_date':$('#from_date').val(),
    'to_date':$('#to_date').val(),
    'location':$('#location').val(),
    'area':$('#area').val(),
    'otgiven':$('#otgiven').val(),
    'from_date1':$('#from_date1').val(),
    'to_date1':$('#to_date1').val(),
    'salary':$('#salary').val(),
    },
    success:function(data){
        console.log(data);
    $.notify(data.msg, data.type)
    if (data.type=='success') {
    clear_all();
    callAjax();   
    }
    // else{
    //     if(data.messages && data.messages.length > 0){
    //         $.each(data.messages, function(index, val) {
    //             $.notify(val, data.type)
    //         });
    //     }
    // }
    // $('#tabledata1').html(data);

    }
    });
    }

</script>
@endpush
@endsection