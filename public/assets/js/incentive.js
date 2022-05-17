//adds extra table rows
var i=$('#table-body tr').length;
$(".addmore").on('click',function(){
	// check exists empty item
	var lastId = i;
	var lastItem = $("#associate_"+lastId).val();
	if(lastItem !== ''){
		++i;
		html = '<tr id="itemRow_'+i+'">';
		html += '<td><button class="btn btn-sm btn-outline-danger delete" type="button" id="deleteItem'+i+'" onClick="deleteItem(this.id)"><i class="las la-trash"></i></button></td>';
		html += '<td class="index">'+i+'</td>';
		html += '<td><input type="text" data-type="associateid" name="associate[]" id="associate_'+i+'" class="form-control autocomplete_txt" autofocus="autofocus" autocomplete="off" ><div id="extrainput_'+i+'"></div></td>';
		html += '<td><input type="text" name="amount[]" step="any" id="amount_'+i+'" class="form-control amount" autocomplete="off" value="0" onClick="this.select()" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"></td>';
		html += '<td><input type="text" id="name_'+i+'" class="form-control" readonly></td>';
		html += '<td><input type="text" id="designation_'+i+'" class="form-control " readonly></td>';
		html += '<td><input type="text" id="department_'+i+'" class="form-control " readonly></td>';
		html += '<td><input type="text" id="floor_'+i+'" class="form-control " readonly></td>';
		html += '<td><input type="text" id="line_'+i+'" class="form-control " readonly></td>';
		
		html += '</tr>';
		$('#table-body').append(html);
		$('#associate_'+i).focus();
		autoIndexing();
		//i++;
	}else{
		$('#associate_'+lastId).focus();
	}
	
});

$(document).on('keyup blur', '.amount', function(e) {
	calculateAmount();
	if( e.which == 13 ){
		$(".addmore").click();
	}
});
function calculateAmount(){
	let amount = 0;
	$(".amount").each(function(i, v) {
        if($(this).val() != '' )amount += parseFloat( $(this).val() );
    });
	
    $("#total").html(amount);
}
function autoIndexing(){
	let ind = 1;
	$('.index').each(function() {
        $(this).html(ind);
        ind++
    });
}

function deleteItem(itemId) {
	$("#"+itemId).parent().parent().remove();
	autoIndexing();
	calculateAmount();
}

var base_url = window.location.origin;
//auto-complete script
$(document).on('focus keyup','.autocomplete_txt',function(){
	type = $(this).data('type');
	typeId = $(this).attr('id');
	var date = $("#incentive-date").val();
	// console.log(typeId);
	inputIdSplit = typeId.split("_");

	if(type =='associateid' )autoTypeNo=0;
	if(type =='empname' )autoTypeNo=1; 	
	
	$(this).autocomplete({
		source: function( request, response ) {
			$.ajax({
				url : base_url+'/hr/payroll/incentive-bonus-employee',
				//dataType: "json",
				method: 'get',
				data: {
				  keyvalue: request.term,
				  date:date
				},
				 success: function( data ) {
					response( $.map( data, function( item ) {
					 	if(type =='associateid') autoTypeShow = item.associate;
					 	
						return {
							label: autoTypeShow+' - '+item.as_oracle_code+' - '+item.name,
							value: autoTypeShow,
							data : item
						}
					}));
				}
			});
		},
		autoFocus: true,	      	
		minLength: 0,
		select: function( event, ui ) {
			// console.log(ui.item.data);
			var item = ui.item.data;						
			id_arr = $(this).attr('id');
	  		id = id_arr.split("_");
	  		if($("#asid_"+item.as_id).length && $("#asid_"+item.as_id).val().length){
	  			$.notify('Already selected this employee', 'error');
                setTimeout(function(){
                    $('#associate_'+id[1]).val('');
                }, 400);
	  		}else{
	  			$('#extrainput_'+id[1]).html('<input type="hidden" name="as_id[]" id="asid_'+item.as_id+'" value="'+item.as_id+'">');
	  			$('#associate_'+id[1]).val(item.associate);
				// $('#asid_'+id[1]).val(item.as_id);
				$('#name_'+id[1]).val(item.name);
				$('#designation_'+id[1]).val(item.designation);
				$('#department_'+id[1]).val(item.department);
				$('#line_'+id[1]).val(item.line);
				$('#floor_'+id[1]).val(item.floor);
				$('#amount_'+id[1]).val(item.amount);
				
				setTimeout(function() { $('#amount_'+id[1]).focus().select(); }, 200);
				$(".addmore").click();
				calculateAmount();
	  		}
			
		}		      	
	});
});


//It restrict the non-numbers
var specialKeys = new Array();
specialKeys.push(8,46); //Backspace
function IsNumeric(e) {
    var keyCode = e.which ? e.which : e.keyCode;
    //console.log( keyCode );
    var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) != -1);
    return ret;
}
