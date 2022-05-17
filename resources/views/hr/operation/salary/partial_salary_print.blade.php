@extends('hr.layout')
@section('title', 'Partial Salary Print')

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
    height: 70px;
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
				<li class="active"> Partial Salary Print</li>
			</ul>
		</div>

		<div class="page-content"> 

			<div class="iq-accordion career-style mat-style  ">

				<div class="iq-card iq-accordion-block accordion-active">

					<div class="active-mat clearfix">

						<div class="container-fluid">

							<div class="row">
								<div class="col-sm-12"><a class="accordion-title"><span class="header-title"> Partial Salary Print </span> </a>
								</div>

							</div>
						</div>
					</div>

		<div class="accordion-details">
    		<div class="row1">
		        <div class="col-12">
		       
		                <div class="panel mb-0">
		                    <div class="panel-body pb-0">


		                    	<div class="row">
		                    		<div class=col-3>
		                    			<div class="form-group has-float-label select-search-group">
		                    				<select name="unit" class="form-control capitalize select-search" id="unit"  >

		                    					<option selected="" value="">Choose...</option>
		                    					@if(array_intersect(auth()->user()->unit_permissions(), [1,4,5]))
		                    					<option value="145">MBM + MFW + SRT</option>
		                    					<option value="14">MBM + MFW </option>
		                    					<option value="15">MBM + SRT </option>
		                    					@endif
		                    					@foreach($unitList as $key => $value)
		                    					<option value="{{ $key }}">{{ $value }}</option>
		                    					@endforeach

		                    				</select>
		                    				<label for="unit">Unit</label>
		                    			</div>
		                    			<div class="form-group has-float-label select-search-group">
		                    				<select name="location" class="form-control capitalize select-search" id="location">
		                    					<option selected="" value="">Choose Location...</option>
		                    					@foreach($locationList as $key => $value)
		                    					<option value="{{ $key }}">{{ $value }}</option>
		                    					@endforeach
		                    				</select>
		                    				<label for="location">Location</label>
		                    			</div>
		                    			<div class="form-group has-float-label select-search-group">
                                                    <select name="area" class="form-control capitalize select-search" id="area">
                                                        <option selected="" value="">Choose...</option>
                                                        @foreach($areaList as $key => $value)
                                                        <option value="{{ $key }}">{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label for="area">Area</label>
                                        </div>

                                        <div class="form-group has-float-label select-search-group">
                                                    <select name="department" class="form-control capitalize select-search" id="department" disabled>
                                                        <option selected="" value="">Choose...</option>
                                                    </select>
                                                    <label for="department">Department</label>
                                        </div>
		                    		</div>

		                    		<div class="col-3">
                                                
                                                <div class="form-group has-float-label select-search-group">
                                                    <select name="section" class="form-control capitalize select-search " id="section" disabled>
                                                        <option selected="" value="">Choose...</option>
                                                    </select>
                                                    <label for="section">Section</label>
                                                </div>
                                                <div class="form-group has-float-label select-search-group">
                                                    <select name="subSection" class="form-control capitalize select-search" id="subSection" disabled>
                                                        <option selected="" value="">Choose...</option> 
                                                    </select>
                                                    <label for="subSection">Sub Section</label>
                                                </div>
                                                <div class="form-group has-float-label select-search-group">
                                                    <select name="floor" class="form-control capitalize select-search" id="floor" disabled >
                                                        <option selected="" value="">Choose...</option>
                                                    </select>
                                                    <label for="floor">Floor</label>
                                                </div>
                                                <div class="form-group has-float-label select-search-group">
                                                    <select name="line" class="form-control capitalize select-search" id="line" disabled >
                                                        <option selected="" value="">Choose...</option>
                                                    </select>
                                                    <label for="line">Line</label>
                                                </div>
                                            </div> 



                                           <div class="col-3">
                                                
                                                <div class="form-group has-float-label select-search-group">
                                                    <select name="otnonot" class="form-control capitalize select-search" id="otnonot" >
                                                        <option selected="" value="">Choose...</option>
                                                        <option value="0">Non-OT</option>
                                                        <option value="1">OT</option>
                                                    </select>
                                                    <label for="otnonot">OT/Non-OT</label>
                                                </div>
                                                <div class="row">
                                                  <div class="col-5 pr-0">
                                                    <div class="form-group has-float-label has-required">
                                                      <input type="number" class="report_date min_sal form-control" id="min_sal" name="min_sal" placeholder="Min Salary" required="required" value="{{ $salaryMin }}" min="{{ $salaryMin}}" max="{{ $salaryMax}}" autocomplete="off" />
                                                      <label for="min_sal">Range From</label>
                                                    </div>
                                                  </div>
                                                  <div class="col-1 p-0">
                                                    <div class="c1DHiF text-center">-</div>
                                                  </div>
                                                  <div class="col-6">
                                                    <div class="form-group has-float-label has-required">
                                                      <input type="number" class="report_date max_sal form-control" id="max_sal" name="max_sal" placeholder="Max Salary" required="required" value="{{ $salaryMax }}" min="{{ $salaryMin}}" max="{{ $salaryMax}}" autocomplete="off" />
                                                      <label for="max_sal">Range To</label>
                                                    </div>
                                                  </div>
                                                </div>
                                                <div class="form-group has-float-label has-required">
                                                  <input type="number" class="perpage form-control" id="perpage" name="perpage" placeholder="Per Page" required="required" value="6" min="0" autocomplete="off" />
                                                  <label for="perpage">Per Page</label>
                                                </div>
                                                <div class="form-group has-float-label select-search-group">
                                                    <?php
                                                      $payType = ['cash'=>'Cash', 'rocket'=>'Rocket', 'bKash'=>'bKash', 'dbbl'=>'Duch-Bangla Bank Limited.'];
                                                    ?>
                                                    {{ Form::select('pay_status', $payType, null, ['placeholder'=>'Select Payment Type', 'class'=>'form-control capitalize select-search', 'id'=>'paymentType']) }}
                                                    <label for="paymentType">Payment Type</label>
                                                </div>
                                            </div>

                                            <div class="col-3">
                                                <div class="form-group has-float-label has-required">
                                                  <input type="month" class="report_date form-control" id="month" name="month_year" placeholder=" Month-Year"required="required" value="{{ date('Y-m', strtotime('0 month')) }}"autocomplete="off" />
                                                  <label for="month">Month</label>
                                                </div>

                                                <div class="form-group has-float-label select-search-group">
                                                    <select name="printtype" class="form-control capitalize "            id="printtype" >
                                                        <option value="T">TEST PRINT</option>
                                                        <option value="F">FINAL PRINT</option>
                                                    </select>
                                                    <label for="printtype">Print Type</label>
                                                </div>
                                                
                                                <div class="form-group has-float-label select-search-group" >
                                                    <select name="sheettype" class="form-control capitalize "  id="sheettype" >
                                                    <option value="S">Salary Sheet</option>
                                                    <option value="P">PaySlip</option>
                                                    </select>
                                                    <label for="sheettype">Sheet Type</label>
                                                </div>

                                                <div class="form-group">
                                                  <button class="btn btn-primary nextBtn btn-lg pull-right" onClick="printsalary()" type="submit" id="unitFromBtn" value="Submit">   <i class="fa fa-save"></i> Generate</button>

                                                {{--   <button onclick="multiple()" class="btn btn-primary nextBtn btn-lg pull-right" type="button" id="unitFromBtn"><i class="fa fa-save"></i> ss</button> --}}



                                                </div>
                                            </div> 

		                    	</div>   






							</div>  
						</div> 
				</div>
			</div>
		</div>

</div>
</div>



</div>

</div>



<div >    
	<div  class="main-content" >

		<div class="main-content-inner">
			<div class="breadcrumbs ace-save-state" id="breadcrumbs">
				<ul class="breadcrumb" id="print">
			{{-- ddddd --}}
			</ul>
			</div>
		</div>
	</div>
</div>
</div>







@push('js')


<script type="text/javascript">









// for salary print 
function printsalary(){
   
            printsalary1();                  
};


function printsalary1(id = null) {
    var location = $("#location").val();
    var unit = $("#unit").val();
    var min_sal = $("#min_sal").val();
    var max_sal = $("#max_sal").val();
    var perpage = $("#perpage").val();
    var month = $("#month").val();
    var sheettype = $("#sheettype").val();
    console.log(sheettype);
      if(month==='' || month==='null'){
                $.notify("Select month", 'error');
        }else if (perpage==='' || perpage==='null'){
                $.notify("Please select Valide Per Page", 'error');
        }
        else if (min_sal==='' || min_sal==='null'){
                $.notify("Please type min sal", 'error');
        }
        else if (max_sal==='' || max_sal==='null'){
                $.notify("Please type max sal", 'error');
            }
        else if (sheettype==='' || sheettype==='null'){
        $.notify("Please select sheet Type", 'error');
    }
        else if (unit==='' || unit==='null'){
        $.notify("Please select unit", 'error');
        
        }
        else{
            $('#print').html(loaderContent);
    $.ajax({
        type:'get',
        url: '{{url("hr/operation/partial-salary-printload")}}',           
        data:{
            'unit':$('#unit').val(),
            'location':$('#location').val(),
            'area':$('#area').val(),
            'department':$('#department').val(),
            'section':$('#section').val(),
            'subSection':$('#subSection').val(),
            'floor':$('#floor').val(),
            'line':$('#line').val(),
            'otnonot':$('#otnonot').val(),
            'min_sal':$('#min_sal').val(),
            'max_sal':$('#max_sal').val(),
            'perpage':$('#perpage').val(),
            'paymentType':$('#paymentType').val(),
            'month':$('#month').val(),
            'estatus':$('#estatus').val(),
            'printtype':$('#printtype').val(),
            'sheettype':$('#sheettype').val(),
           
        },
        success:function(data){
        	// console.log('Delete Success..');
            $('#print').html(data);
// $("#header").html( 'Partial Salary Process <br> Report Type : '+$("#Type option:selected").text()+'<br>'+$("#unit option:selected").text()
//     + $("#subsection option:selected").text() +'<br>' +$("#otnonot option:selected").text() + '<br> Report Run Date Between : '
//     +$('#from_date').val()  + ' And '+$('#to_date').val() );
// $("#sectionsubsection").html( $("#section option:selected").text() +'::::' + $("#subsection option:selected").text());
}
});

}
}



    $( "#floor" ).prop( "disabled", true );
    $( "#line" ).prop( "disabled", true );
    $( "#estatus" ).prop( "disabled", true );
    $( "#disbursed" ).prop( "disabled", true );


var loader = '<div class="progress"><div class="progress-bar progress-bar-info progress-bar-striped active" id="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">0%</div></div>';
    $(document).ready(function(){
        //salary range validation------------------

        $('#min_sal').on('change',function(){
            $('#max_sal').val('');

            if($('#min_sal').val() < 0){
                $('#min_sal').val('');
            }    
        });

        $('#max_sal').on('change',function(){
            if($('#max_sal').val() < 0){
                $('#max_sal').val('');
            }
            else{
                var end     = $(this).val();
                var start   = $('#min_sal').val();
                console.log('min:'+start+' '+'max:'+end);
                if(start == '' || start == null){
                    alert("Please enter Min-Salary first");
                    $('#max_sal').val('');
                }
                else{
                     if(parseFloat(end) < parseFloat(start)){
                        alert("Invalid!!\n Min-Salary is greater than Max-Salary");
                        $('#max_sal').val('');
                    }
                }
            }
        });
        //salary range validation end-----------------

        //month-Year validation------------------
        $('#form-date').on('dp.change',function(){
            $('#to-date').val( $('#form-date').val());    
        });

        $('#to-date, #form-date').on('dp.change',function(){
            var end     = new Date($('#to-date').val()) ;
            var start   = new Date($('#form-date').val());
            if(end < start){
                alert("Invalid!!\n From-Month-Year is latest than To-Month-Year");
                    $('#to-date').val('');
            }
        });
        //month-Year validation end---------------
    });
</script>

<script>
    var _token = $('input[name="_token"]').val();
    function printDiv(divName)
    { 
        var myWindow=window.open('','','width=800,height=800');
        myWindow.document.write(document.getElementById(divName).innerHTML); 
        myWindow.document.close();
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }
    // show error message
    function errorMsgRepeter(id, check, text){
        var flug1 = false;
        if(check == ''){
            $('#'+id).html('<label class="control-label status-label" for="inputError">* '+text+'<label>');
            flug1 = false;
        }else{
            $('#'+id).html('');
            flug1 = true;
        }
        return flug1;
    }

    function formatState (state) {
        //console.log(state.element);
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "/user/pages/images/flags";
        var $state = $(
        '<span><img /> <span></span></span>'
        );
        // Use .text() instead of HTML string concatenation to avoid script injection issues
        var targetName = state.name;
        $state.find("span").text(targetName);
        // $state.find("img").attr("src", baseUrl + "/" + state.element.value.toLowerCase() + ".png");
        return $state;
    };

    $('select.associates').select2({
        templateSelection:formatState,
        placeholder: 'Select Name or Associate\'s ID',
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
                            text: $("<span><img src='"+(item.as_pic ==null?'/assets/images/avatars/profile-pic.jpg':item.as_pic)+"' height='50px' width='auto'/> " + item.associate_name + "</span>"),
                            id: item.associate_id,
                            name: item.associate_name
                        }
                    })
                };
          },
          cache: true
        }
    });

    // Reuseable ajax function
    function ajaxOnChange(ajaxUrl, ajaxType, valueObject, successStoreId) {
        $.ajax({
            url : ajaxUrl,
            type: ajaxType,
            data: valueObject,
            success: function(data)
            {
                successStoreId.html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });
    }
    // HR Floor By Unit ID
    var unit = $("#unit");
    var floor = $("#floor")
    unit.on('change', function() {
        $( "#floor" ).prop( "disabled", false );
        ajaxOnChange('{{ url('hr/setup/getFloorListByUnitID') }}', 'get', {unit_id: $(this).val()}, floor);
        // line
        $.ajax({
           url : "{{ url('hr/reports/line_by_unit') }}",
           type: 'get',
           data: {unit : $(this).val()},
           success: function(data)
           {
                $('#line').removeAttr('disabled');
                $("#line").html(data);
           },
           error: function(reject)
           {
             console.log(reject);
           }
        });
    });


    //Load Department List By Area ID
    var area = $("#area");
    var department = $("#department");
    area.on('change', function() {
        $( "#department" ).prop( "disabled", false );
        ajaxOnChange('{{ url('hr/setup/getDepartmentListByAreaID') }}', 'get', {area_id: $(this).val()}, department);
    });

    //Load Section List by department
    var section = $("#section");
    department.on('change', function() {
        $( "#section" ).prop( "disabled", false );
        ajaxOnChange('{{ url('hr/setup/getSectionListByDepartmentID') }}', 'get', {area_id: area.val(), department_id: $(this).val()}, section);
    });

    //Load Sub Section List by Section
    var subSection = $("#subSection");
    section.on('change', function() {
        $( "#subSection" ).prop( "disabled", false );
        ajaxOnChange('{{ url('hr/setup/getSubSectionListBySectionID') }}', 'get', {area_id: area.val(), department_id: department.val(), section_id: $(this).val()}, subSection);
    });





$(document).on('change','#month', function(){
 
    var date = '{{date("Y-m")}}';

    if ($('#month').val()===date)
            {
             $( "#printtype" ).prop( "disabled", false );
            }
    else
        {
         $('#printtype').val('F').trigger('change');
         $( "#printtype" ).prop( "disabled", true );
        }

});

    



   
</script>
@endpush
@endsection