let rootUrl = window.location.origin;
var i = $('table tr').length;
$(document).on('click', '.add-arrows', function () {
    // check exists empty item
    var lastId = i - 1;
    var flag = 0;
    var totalRow = $('.labelname').length;

    if (flag === 0) {
        
        html = '<tr id="itemRow_' + i + '">';
        html += '<td class="right-btn"><a class="btn btn-sm btn-outline-primary arrows-alt" data-toggle="tooltip" data-placement="top" title="" data-original-title="Right Click Action"><i class="las la-arrows-alt"></i></a><div class="context-menu" id="context-menu-file-" style="display:none;position:absolute;z-index:1;"><ul><li><a class="textblack arrows-context add-arrows" ><i class="las la-cart-plus"></i> Add Row</a></li><li><a class="textblack arrows-context remove-arrows"><i class="las la-trash"></i> Remove Row</a></li></ul></div></td>';
        
        // html += '<td>'+ i + '</td>';
        html += '<td><input type="text" name="label_name[]" id="labelname_'+ i + '" class="form-control labelname" placeholder="Enter label name" value="" autocomplete="off"></td>';

        html += '<td><select name="min_designation[]" id="mindesignation_'+ i + '" class="form-control filter1"><option value=""> - Choose Min Designation - </option>';
        $.each( designation, function( index, value ){
            html +='<option value="'+index+'">'+value+'</option>';
        });  
        html += '</select></td>';
        
        html += '<td><select name="max_designation[]" id="maxdesignation_'+ i + '" class="form-control filter1"><option value=""> - Choose Max Designation - </option>';
        $.each( designation, function( index, value ){
            html +='<option value="'+index+'">'+value+'</option>';
        });  
        html += '</select></td>';
        html += '<td><div class="iq-card-body"><div class="custom-control custom-radio custom-radio-color-checked inline"><input type="radio" id="active_'+ i + '" name="status'+ i + '" class="custom-control-input bg-success" checked value="1"><label class="custom-control-label" for="active_'+ i + '"> Active </label></div>&nbsp;<div class="custom-control custom-radio custom-radio-color-checked inline"><input type="radio" id="inactive_'+ i + '" name="status'+ i + '" class="custom-control-input bg-danger" value="0"><label class="custom-control-label" for="inactive_'+ i + '"> Inactive </label></div></div></td>';
        html += '<td><div class="access_label">';
        $.each( emp_type, function( index, value ){
            html +='<div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline"><input type="checkbox" name="access_emp_type'+i+'[]" class="custom-control-input bg-primary" value="'+index+'" id="accesslabel'+i+'-'+index+'" checked><label class="custom-control-label" for="accesslabel'+i+'-'+index+'"> '+value+' </label></div>';
        });                          
        html += '</div></td>';
        html += '<td><div class="final_label">';
        $.each( emp_type, function( index, value ){
            html +='<div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline"><input type="checkbox" name="approval_emp_type'+i+'[]" class="custom-control-input bg-primary" value="'+index+'" id="label'+i+'-'+index+'"><label class="custom-control-label" for="label'+i+'-'+index+'"> '+value+' </label></div>';
        });                          
        html += '</div></td>';
        html += '</tr>';
        $(this).parent().parent().parent().parent().parent().after(html);
        // $('#').append(html);
        $('#labelname_'+ i).focus();
        i++; 
    }

});

$(document).on('click', '.remove-arrows', function () {
    var isGood = confirm('Are you sure you want to remove this row?');
    if (isGood) {
        $(this).parent().parent().parent().parent().parent().remove();
        // saveBOM('remove');
    }
    // $(this).parent().parent().parent().parent().parent().remove();
})
$(document).on("contextmenu", ".right-btn", function (e) {
    // Show context menu
    $(".context-menu").hide();
    $(this).parent().find('.context-menu').toggle(100).css({
        display: "block",
        left: "20px"
    });

    // disable default context menu
    return false;
});

// Hide context menu
$(document).bind('contextmenu click', function () {
    $(".context-menu").hide();
});


$(document).on('keypress', function (e) {
    var that = document.activeElement;
    if (e.which == 13) {
        if ($(document.activeElement).attr('type') == 'submit') {
            return true;
        } else {
            e.preventDefault();
        }
    }
});

$(document).on('click', '.action-btn', function() {
    type = $(this).data('type');
    $('#right_modal_common').modal('show');
    $('#modal-title-common').html(type);
    $("#content-result-common").html(loaderContent);
    var url = '';
    if(type === 'Approval Process Create'){
      url = '/hr/administrator/approval-process/create';
    }else if(type === 'Approval Process Edit'){
        var id = $(this).data('id');
        url = '/hr/administrator/approval-process/'+id+'/edit';
    }
    $.ajax({
        type: "GET",
        url: rootUrl+url,
        success: function(response)
        {
          // console.log(response);
          if(response !== 'error'){
            $('#content-result-common').html(response);
            $('.filter').select2({
                dropdownParent: $('#right_modal_item')
            });
            
            
          }else{
            $('#content-result-common').html('<h4 class="text-center">Something wrong, please close and try again!</h4>');
          }
        },
        error: function (reject) {
          console.log(reject);
        }
    });
    
});

$(document).on('contextmenu', 'input', function (event) {
    return false;
});


function changeSaveApproval() {
    var curStep = $("#itemForm"),
      curInputs = curStep.find("input[type='text'],input[type='hidden'],input[type='number'],input[type='checkbox'],input[type='radio'],textarea,select"),
      isValid = true;
    $(".form-group").removeClass("has-error");
    for (var i = 0; i < curInputs.length; i++) {
       if (!curInputs[i].validity.valid) {
          isValid = false;
          $(curInputs[i]).closest(".form-group").addClass("has-error");
       }
    }
    var flag = 0;
    $('.labelname').each(function(i, obj) {
      if($(this).val().length > 0){
        flag = 1;
        return;
      }
    });
    if(flag === 1){
        var form = $("#itemForm");
        if (isValid){
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: rootUrl+'/hr/administrator/approval-process',
                data: form.serialize(), // serializes the form's elements.
                success: function(response)
                {
                    console.log(response);
                    $(".app-loader").hide();

                    $.each( response.message, function( index, value ){
                      $.notify(value, response.type);
                    });
                    if(response.type === 'success'){
                        $('.close').click();
                        window.location.reload();
                    }
                    
                },
                error: function (reject) {
                  $(".app-loader").hide();
                  // console.log(reject);
                  if( reject.status === 400) {
                      var data = $.parseJSON(reject.responseText);
                       $.notify(data.message, data.type);
                  }else if(reject.status === 422){
                    var data = $.parseJSON(reject.responseText);
                    var errors = data.errors;
                    // console.log(errors);
                    for (var key in errors) {
                      var value = errors[key];
                      $.notify(value[0], 'error');
                    }

                  }
                }
            });
        }else{
          $(".app-loader").hide();
          $.notify("Some field are required", 'error');
        }
    }else{
        $.notify('No Label Found!', 'error');
        $(".app-loader").hide();
    }
};

function changeUpdateApproval() {
    var curStep = $("#itemForm"),
      curInputs = curStep.find("input[type='text'],input[type='hidden'],input[type='number'],input[type='checkbox'],input[type='radio'],textarea,select"),
      isValid = true;
    $(".form-group").removeClass("has-error");
    for (var i = 0; i < curInputs.length; i++) {
       if (!curInputs[i].validity.valid) {
          isValid = false;
          $(curInputs[i]).closest(".form-group").addClass("has-error");
       }
    }
    var flag = 0;
    $('.labelname').each(function(i, obj) {
      if($(this).val().length > 0){
        flag = 1;
        return;
      }
    });
    if(flag === 1){
        var form = $("#itemForm");
        if (isValid){
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: rootUrl+'/hr/administrator/approval-process',
                data: form.serialize(), // serializes the form's elements.
                success: function(response)
                {
                    console.log(response);
                    $(".app-loader").hide();

                    // $.each( response.message, function( index, value ){
                    //   $.notify(value, response.type);
                    // });
                    // if(response.type === 'success'){
                    //     $('.close').click();
                    // }
                    
                },
                error: function (reject) {
                  $(".app-loader").hide();
                  // console.log(reject);
                  if( reject.status === 400) {
                      var data = $.parseJSON(reject.responseText);
                       $.notify(data.message, data.type);
                  }else if(reject.status === 422){
                    var data = $.parseJSON(reject.responseText);
                    var errors = data.errors;
                    // console.log(errors);
                    for (var key in errors) {
                      var value = errors[key];
                      $.notify(value[0], 'error');
                    }

                  }
                }
            });
        }else{
          $(".app-loader").hide();
          $.notify("Some field are required", 'error');
        }
    }else{
        $.notify('No Label Found!', 'error');
        $(".app-loader").hide();
    }
};

