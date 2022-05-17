@extends('hr.layout')
@section('title', 'File Tag')
@section('main-content')
@push('css')
<style type="text/css">
    @media only screen and (max-width: 771px) {

        .file_tag_field {margin-bottom: 10px;}
}
</style>
@endpush
<div class="main-content">
	<div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                   <a href="/"><i class="fa fa-home home-icon"></i>Human Resource</a> 
                </li>
                <li>
                    <a href="#">Employee</a>
                </li>
                <li class="active">File Tag</li>
            </ul><!-- /.breadcrumb --> 
        </div>


		<div class="panel p-3 pb-0"> 
			<div class="panel-heading">
				<h6>Employee File Tag</h6>
			</div>
			<div class="panel-body">
				
	            {{ Form::open(['url'=>'', 'class'=>'row', 'id'=>'IdCard']) }}
				<div class="col-6">
					<div class="row">
						<div class="col-6">
							<div class="form-group has-float-label select-search-group">
	                        	{{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'class'=> 'form-control ']) }} 
	                        	<label>Unit</label>
							</div> 
						</div>
						<div class="col-6 file_tag_field">
							<div class="form-group has-float-label select-search-group">
								
		                        {{ Form::select('emp_type', $employeeTypes, null, ['placeholder'=>'Select Employee Type', 'class'=> 'form-control ']) }}  
		                        <label>Employee Type</label>
							</div>
						</div>
						
					</div>

					<div class="row">
						<div class="col-6 file_tag_field">
							<div class="form-group has-float-label select-search-group">
								{{ Form::select('floor', [], null, ['placeholder'=>'Select Floor', 'class'=>'form-control ']) }}
								<label>Floor</label>
							</div>   
						</div>
						<div class="col-6">
							<div class="form-group has-float-label select-search-group">
								{{ Form::select('line', [], null, ['placeholder'=>'Select Line', 'class'=>'form-control ']) }} 
								<label>Line</label>
							</div>   
						</div>  
					</div>

					<div class="row">
						<div class="col-6 file_tag_field">
							<div class="form-group has-float-label">
								<input type="date"  name="doj_from" id="doj_from" class=" form-control " placeholder="Date of Join From" >
								<label>DOJ From</label>
							</div>
							 
						</div>
						<div class="col-6 file_tag_field">
							<div class="form-group has-float-label">
								<input type="date" name="doj_to" id="doj_to" class="datepicker form-control " placeholder="Date of Join To" >
								<label>DOJ To</label>
							</div> 
							<div class="form-group" style="margin:10px 0px; display: none;">
		                        
							</div>
						</div>
						<div class="col-sm-12 d-block">
							<hr>
							<div class="form-group has-float-label select-search-group" style="height: 100px;">
	                            {{ Form::select('as_id[]', [],'', ['id'=>'as_id', 'class'=> 'allassociates form-control select-search no-select filter', 'multiple'=>"multiple",'style', 'data-validation'=>'required']) }}
	                            <label for="as_id">Employees</label>
	                        </div>
						</div>  
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<button type="button" class="btn btn-primary btn filter" type="button" style="width: 100px;">
			                        <i class="ace-icon fa fa-search"></i>  Filter
			                    </button>
		                    	<button type="submit" class="btn btn-primary btn ck" type="button" style="display: none;">
	                            	Generate File Tag
	                        	</button>  &nbsp;&nbsp;
								<div id="printBtn" style="display:inline-block;"></div>
							</div> 
						</div>
					</div>
								

							
				</div>


				<div class="col-6" style="margin-top: -16px;padding-left: 20px;padding-right: 15px;border-top: 1px solid rgb(206 235 238);">
					<div class="row" style="height: 300px; overflow: auto;">
	                    <table id="AssociateTable" class="table header-fixed1 table-compact table-bordered col-sm-12">
	                        <thead>
	                           
	                            <tr>
		                            <th class="sticky-th" style="width:5%;"><input type="checkbox" id="checkAll"/></th>
		                            <th class="sticky-th" style="width: 30%">Associate ID</th>
		                            <th class="sticky-th" style="width: 25%">Oracle ID</th>
		                            <th class="sticky-th" style="width: 40%">Name</th>
	                            </tr>
	                             <tr>
	                                <th class="sticky-th" colspan="5" id="user_filter" style="top: 40px;"></th>
	                            </tr>
	                            
	                        </thead>
	                        <tbody id="associateList">
	                        </tbody>
	                    </table>
	                </div> 
				</div>
				{{ Form::close() }}
			</div>
		</div>

		<div class="panel">
			<div id="idCardPrint" style="overflow-y: scroll; height:1200px; border: 1px solid whitesmoke; " hidden></div> 
		</div>
	</div>
</div>   
@push('js')       
<script type="text/javascript">
$(document).ready(function(){
	$(document).on('click','.associate-select, #checkAll', function(){
		var checkedItemsAsString = $('[class*="associate-select"]:checked').map(function() { return $(this).val().toString(); } ).get().join(",");
		if(checkedItemsAsString) {
			$('.ck').show();
		} else {
			$('.ck').hide();
		}
	});
	//date validation------------------
    $('#doj_from').on('dp.change',function(){
        $('#doj_to').val($('#doj_from').val());    
    });

    $('#doj_to').on('dp.change',function(){
        var end     = new Date($(this).val());
        var start   = new Date($('#doj_from').val());
        if(start == '' || start == null){
            alert("Please enter From-Date first");
            $('#doj_to').val('');
        }
        else{
             if(end < start){
                alert("Invalid!!\n From-Date is latest than To-Date");
                $('#doj_to').val('');
            }
        }
    });
    //date validation end---------------

	$('body').on('click','.ck',function(){
    	  $('#idCardPrint').removeAttr('hidden');	
    	  $('html, body').animate({
          scrollTop: $('#idCardPrint').offset().top
          }, 700);
    });

	//check - uncheck
	$('#checkAll').click(function(){
	   var checked =$(this).prop('checked');
	   $('input:checkbox').prop('checked', checked);
	}); 

	$('body').on('click', 'input:checkbox', function() {
	   if(!this.checked) {
	       $('#checkAll').prop('checked', false);
	   }
	   else {
	       var numChecked = $('input:checkbox:checked:not(#checkAll)').length;
	       var numTotal = $('input:checkbox:not(#checkAll)').length;
	       if(numTotal == numChecked) {
	           $('#checkAll').prop('checked', true);
	       }
	   }
	});

	//Filter User
    $("body").on("keyup", "#AssociateSearch", function() {
        var value = $(this).val().toLowerCase(); 
        $("#AssociateTable #associateList tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

	// emp_type 
	var associateList = $("#associateList");
	var user_filter   = $("#user_filter");
	var emp_type = $("select[name=emp_type]");
	var unit     = $("select[name=unit]");
	var floor    = $("select[name=floor]");
	var line     = $("select[name=line]");
	// floor list by unit
	unit.on('change', function(){
		$.ajax({
			url: '{{ url("hr/recruitment/employee/idcard/floor_list_by_unit") }}',
			data: {
				unit: unit.val(),
			},
			success: function(data)
			{
				floor.html(data.floorList);   
				line.html('');   
				printBtn.html('');
				idCardPrint.html('');
			},
			error:function(xhr)
			{
				console.log('Unit Failed');
			}
		});
	});

	// line list by floor & unit id
	floor.on('change', function(){
		$.ajax({
			url: '{{ url("hr/recruitment/employee/idcard/line_list_by_unit_floor") }}',
			data: {
				unit: unit.val(),
				floor: floor.val(),
			},
			success: function(data)
			{
				line.html(data.lineList); 
				printBtn.html('');
				idCardPrint.html('');
			},
			error:function(xhr)
			{
				console.log('Employee Type Failed');
			}
		});
	});

	// find_associate
	$("body").on('click', ".filter", function(){
		$('#idCardPrint').attr('hidden','hidden');
		$('.app-loader').show();
		$.ajax({
			url: '{{ url("hr/recruitment/employee/idcard/filter") }}',
			data: {
				emp_type: $("select[name=emp_type]").val(),
				unit: $("select[name=unit]").val(),
				floor: $("select[name=floor]").val(),
				line: $("select[name=line]").val(), 
				associate_id: $("#as_id").val(), 
				doj_from: $("input[name=doj_from]").val(), 
				doj_to: $("input[name=doj_to]").val() 
			},
			success: function(data)
			{
				associateList.html(data.result); 
				user_filter.html(data.filter); 
				printBtn.html('');
				idCardPrint.html('');
				$('.app-loader').hide();
			},
			error:function(xhr)
			{
				console.log('Failed');
				$('.app-loader').hide();
			}
		});
	});


		//submit 
	var IdCard = $("#IdCard");
	var idCardPrint = $("#idCardPrint");
	var printBtn = $("#printBtn");
	IdCard.on('submit', function(e){
		e.preventDefault();
		var checkedItemsAsString = $('[class*="associate-select"]:checked').map(function() { return $(this).val().toString(); } ).get().join(",");
		if(checkedItemsAsString) {

			$('.app-loader').show();
	    	var formdata = new FormData($(this)[0]);
	    	idCardPrint.html('<center><table class"col-12"><thead><th><h4>Please Wait...</th></h4></thead></table></center>');
			$.ajax({
				url  : '{{ url("hr/reports/filetag/search") }}',
				type : $(this).attr('method'),
				dataType : 'json',
		        processData: false,
		        contentType: false,
				data : formdata,
				success:function(data)
				{
					// console.log(data);
					printBtn.html(data.printbutton);
					idCardPrint.html(data.filetag); 
					$('.app-loader').hide();
				},
				error:function(data)
				{
					idCardPrint.html(data.responseText);
					$('.app-loader').hide();
				}
			});
		}else{
			$.notify('Please select an associate id!', 'error');
		}
	});
});


function printContent(el)
{
	var data = document.getElementById(el).innerHTML;
	var mywindow = window.open('', 'ID CARD', 'height=800,width=800');
	mywindow.document.write('<html><head><title></title></head>');
	mywindow.document.write('<body>');
	mywindow.document.write(data);
	mywindow.document.write('<body></html>');
	mywindow.focus();
	mywindow.print();
	mywindow.close();  
}
</script>
@endpush
@endsection