@extends('hr.layout')
@section('title', 'Eligible')
@push('css')
<style type="text/css">
     <style type="text/css">
        input[type=date]{
            position: relative;
        }
        input[type="date"]::-webkit-inner-spin-button,
        input[type="date"]::-webkit-calendar-picker-indicator {
            position: absolute;
            right:0;
            -webkit-appearance: none;
        }
        .table td {
            padding: 3px 5px;
        }
        table.table-head th{
            top: -1px;
            z-index: 10;
            vertical-align: middle !important;
        }
        .table th {
            padding: .5rem;
            vertical-align: middle !important;
        }
    .btn-primary.disabled, .btn-primary:disabled {
        color: #fff;
        background-color: #595f65;
        border-color: #53595f;
        background: #6c757d;
    }
  .iq-accordion-block{: ;
    padding: 10px 0;
  }
  .eligible-data .disburse-button{
    display: none;

  }
  .notifyjs-wrapper {
    z-index: 10000!important;
  }
</style>
@endpush
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li> 
                <li>
                    <a href="#"> Payroll </a>
                </li>
                <li class="active"> Increment Approval</li>
                <li class="top-nav-btn">
                    <a href="{{url('hr/payroll/increment-list')}}" class="btn btn-sm btn-primary"  data-toggle="tooltip" data-placement="top" title="" data-original-title="List of approved increments"><i class="fa fa-list"></i> List</a>
                    <a href="{{url('hr/payroll/increment-on-process')}}" class="btn btn-sm btn-success pull-right"  data-toggle="tooltip" data-placement="top" title="" data-original-title="List of Proposed increments"> <i class="lar la-pause-circle"></i> Proposed</a> &nbsp;
                    <a href="{{url('hr/payroll/increment-process')}}" target="_blank" class="btn btn-sm btn-warning"  data-toggle="tooltip" data-placement="top" title="" data-original-title="New Increment Propose increments"><i class="fa fa-plus"></i> New</a>
                     &nbsp;
                </li>
            </ul><!-- /.breadcrumb --> 
        </div>
        
        <div class="row">
            <div class="col">
              <form role="form" method="get" action="{{ url("hr/payroll/increment-eligible-filter") }}" id="formReport">
                @csrf
                <div class="iq-card" id="result-section">
                  <div class="iq-card-header d-flex mb-0">
                     <div class="iq-header-title w-100">
                        <div class="row">
                          <div style="width: 55%; float: left; margin-left: 15px; margin-top: 2px;">
                            <div id="result-section-btn">
                              {!!$unit_status!!}
                            </div>
                          </div>
                          
                          <div style="width: 40%; float: left">
                            <div class="row">
                              <div class="col-3 p-0">
                                  
                              </div>
                              <div class="col-4 pr-0">
                                <div class="format">
                                  <input type="hidden" name="type" value="running" id="increment_type">
                                  
                                </div>
                              </div>
                              <div class="col-5 pl-0">
                                <div class="text-right">
                                    <button type="button" class="btn btn-sm btn-primary hidden-print print-hidden eligible-print" onclick="printDiv('print-section')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report" style="display:none"><i class="las la-print"></i> </button>
                                  <a class="btn view no-padding clear-filter" data-toggle="tooltip" data-placement="top" title="" data-original-title="Clear Filter">
                                    <i class="las la-redo-alt" style="color: #f64b4b; border-color:#be7979"></i>
                                  </a>
                                  |
                                  <b>Eligible List </b>
                                  <a class="btn view no-padding filter" data-toggle="tooltip" data-placement="top" title="" data-original-title="Advanced Filter">
                                    <i class="fa fa-filter"></i>
                                  </a>
                                  
                                </div>
                              </div>
                            </div>
                            
                          </div>
                        </div>
                     </div>
                  </div>
                  
                </div>
              </form>
            </div>
        </div>
        <div class="iq-card">
            <div class="iq-card-body">
                <div class="approval-result" id="result-data">
                  
                </div>
            </div>
        </div>
    </div>
</div>
@section('right-nav')
    @php
        $filter_status = '';
    @endphp
    
    <hr class="mt-2">
    <div class="row">
      <div class="col">
            <div class="form-group has-float-label has-required">
                <input type="month" name="month" class="report_date form-control" id="month-year" placeholder=" Month-Year" value="{{ date('Y-m') }}" autocomplete="off" />
                <label for="month-year">Month</label>
            </div>
      </div>
    </div>
@endsection
  {{--  --}}
@include('common.right-navbar')
@push('js')
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript"> 
    function activityProcess() {
              
        var unit = $('select[name="unit"]').val();
        var location = $('select[name="location"]').val();
        var area = $('select[name="area"]').val();
        var month = $('input[name="date"]').val();
        var format = $('input[name="report_format"]').val();
        if($('select[name="type"]').val() !== undefined){
          var type = $('select[name="type"]').val();
        }else{
          var type = 'running';
        }
        var form = $("#activityReport");
        var flag = 0;

        if(month === ''){
            flag = 1;
            $.notify('Select required field', 'error');
        }

        if(flag === 0){
            var data = form.serialize()+'&type='+type;
            $('.app-loader').show();
            $.ajax({
                type: "GET",
                url: '{{ url("hr/payroll/increment-eligible") }}',
                data: data, 
                success: function(response)
                {
                    if(response !== 'error'){
                        $(".approval-result").html(response);
                        $('.submission-button').attr('disabled','disabled');
                    }
                    $('.app-loader').hide();
                },
                error: function (reject) {
                }
            });

        }else{
            $(".approval-result").html('');
        }
    }

    function individual() {
       
        $('.app-loader').show();
        $.ajax({
            type: "GET",
            url: '{{ url("hr/payroll/increment-employeewise") }}',
            data: {
                associate_id : $('#as_id').val()
            }, 
            success: function(response)
            {
                if(response !== 'error'){
                    $(".approval-result").html(response);
                    $('.submission-button').attr('disabled','disabled');
                }
                $('.app-loader').hide();
            },
            error: function (reject) {
                $(".approval-result").html('');
            }
        });

    }

    $.fn.getForm2obj = function() {
      var _ = {};
      $.map(this.serializeArray(), function(n) {
        const keys = n.name.match(/[a-zA-Z0-9_]+|(?=\[\])/g);
        if (keys.length > 1) {
          let tmp = _;
          pop = keys.pop();
          for (let i = 0; i < keys.length, j = keys[i]; i++) {
            tmp[j] = (!tmp[j] ? (pop == '') ? [] : {} : tmp[j]), tmp = tmp[j];
          }
          if (pop == '') tmp = (!Array.isArray(tmp) ? [] : tmp), tmp.push(n.value);
          else tmp[pop] = n.value;
        } else _[keys.pop()] = n.value;
      });
      return _;
    }
    
    $(document).on('submit','#increment-action', function(e) {
        e.preventDefault();
        var count = 0;
        $('.increment-amount').each(function( index ) {
            if($(this).data('checked') == 1){
                if(isNaN($(this).val()) || $(this).val() == 0 || $(this).val() == ''){
                    count++;
                }

            }
        });
        if(count > 1 ){
            if(confirm(count + ' selected employee(s) missing increment data. Do you want to ignore and continue?')){
                takeAction();
            }
        }else{
            takeAction();
        }
        
        
    });

    function takeAction()
    {
        $('.app-loader').show();
        var data = $('#increment-action').getForm2obj(),
            level = $('#increment-action').data('level');

        const chunksize = 300;

        const increment = Object.keys(data.increment).reduce((c, k, i) => {
          if (i % chunksize == 0) {
            c.push(Object.fromEntries([[k, data.increment[k]]]));
          } else {
            c[c.length - 1][k] = data.increment[k];
          }
          return c;
        }, []);

        // apply promise for one after one request
        var promises = [];
        for (i = 0; i < increment.length; i++) {

            var request = $.ajax({
                type: "POST",
                url: '{{ url("hr/payroll/increment-action-approval") }}',
                data : {
                    _token : data._token,
                    increment : increment[i],
                    level : level
                },
                success: function(res)
                {
                    // console.log(res);
                    $.notify('Increment data approved','success');
                    if(res.status != 'success'){
                        $.notify(res.msg,'error');
                    }else{
                        window.location.reload();
                    }
                    //getApprovalData();
                    $('#processed-data').html(res.data);
                    $(".approval-result").html('');
                    
                },
                error: function (reject) {
                }
            });

            promises.push(request);

        }

        $.when.apply(null, promises).done(function() {
            $("#increment-data").html('');
            $('.app-loader').hide();
        });
    }
    $(document).on("change",'#report_type', function(){
        var type = $(this).val();
        $("#increment_type").val(type).change();
        advFilter();
    });
    $(document).ready(function(){
        // change unit
        $('#activityReport').on('submit', function(e) {
              e.preventDefault();
              activityProcess();
        });

        $('#unit').on("change", function(){
            $.ajax({
                url : "{{ url('hr/attendance/floor_by_unit') }}",
                type: 'get',
                data: {unit : $(this).val()},
                success: function(data)
                {
                    $('#floor_id').removeAttr('disabled');
                    
                    $("#floor_id").html(data);
                },
                error: function(reject)
                {
                   console.log(reject);
                }
            });

            //Load Line List By Unit ID
            $.ajax({
               url : "{{ url('hr/reports/line_by_unit') }}",
               type: 'get',
               data: {unit : $(this).val()},
               success: function(data)
               {
                    $('#line_id').removeAttr('disabled');
                    $("#line_id").html(data);
               },
               error: function(reject)
               {
                 console.log(reject);
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

        //Load Section List By department ID
        $('#department').on("change", function(){
            $.ajax({
               url : "{{ url('hr/setup/getSectionListByDepartmentID') }}",
               type: 'get',
               data: {area_id: $("#area").val(), department_id: $(this).val()},
               success: function(data)
               {
                    $('#section').removeAttr('disabled');
                    
                    $("#section").html(data);
               },
               error: function(reject)
               {
                 console.log(reject);
               }
            });
        });

        //Load Sub Section List by Section
        $('#section').on("change", function(){
           $.ajax({
             url : "{{ url('hr/setup/getSubSectionListBySectionID') }}",
             type: 'get',
             data: {
               area_id: $("#area").val(),
               department_id: $("#department").val(),
               section_id: $(this).val()
             },
             success: function(data)
             {
                $('#subSection').removeAttr('disabled');
                
                $("#subSection").html(data);
             },
             error: function(reject)
             {
               console.log(reject);
             }
           });
        });

        //Filter User
        $("body").on("keyup", "#AssociateSearch", function() {
            var value = $(this).val().toLowerCase();
            // $('#AssociateTable tr input:checkbox').prop('checked', false);
            $('#AssociateTable tr').removeAttr('class');
            $("#AssociateTable #user_info tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                if($(this).text().toLowerCase().indexOf(value) > -1) {
                    $(this).attr('class','add');
                    var numberOfChecked = $('#AssociateTable tr.add input:checkbox:checked').length;
                    var numberOfCheckBox = $('#AssociateTable tr.add input:checkbox').length;
                    if(numberOfChecked == numberOfCheckBox) {
                        $('#checkAll').prop('checked', true);
                    } else {
                        $('#checkAll').prop('checked', false);
                    }
                }
            });
        });

        var userInfo = $("#user_info");
        var userFilter = $("#user_filter");
        var emp_type = $("select[name=emp_type]");
        var unit     = $("select[name=unit]");
        var date     = $('input[name=effective_date]'); 
        $(".filter").on('change', function(){ 
            userInfo.html('<tr><th colspan="3" style=\"text-align: center; font-size: 14px; color: green;\">Searching Please Wait...</th></tr>');
            $.ajax({
                url: '{{ url("hr/payroll/get_associate") }}',
                data: {
                    emp_type: emp_type.val(),
                    unit: unit.val(),
                    // date: date.val(),
                },
                success: function(data)
                { 
                    if(data.result == ""){
                        $('#totalEmp').text('0');
                        $('#selectEmp').text('0');
                        userInfo.html('<tr><th colspan="3" style=\"text-align: center; font-size: 14px; color:red;\">No Data Found</th></tr>');    
                    }
                    else{
                        userInfo.html(data.result);
                        totalemp = data.total;
                        //$('#selectEmp').text(totalempcount);
                        $('#totalEmp').text(data.total);
                    }
                    userFilter.html(data.filter);
                },
                error:function(xhr)
                {
                    console.log('Employee Type Failed');
                }
            });
        }); 

        $('#checkAll').click(function(){
            var checked =$(this).prop('checked');
            var selectemp = 0;
            if(!checked) {
                selectemp = $('#AssociateTable tr.add input:checkbox:checked').length;
                selectemp = totalempcount - selectemp;
                totalempcount = 0;
            } else {
                selectemp = $('#AssociateTable tr.add input:checkbox:not(:checked)').length;
            }
            $('#AssociateTable tr.add input:checkbox').prop('checked', checked);
            totalempcount = totalempcount+selectemp;
            $('#selectEmp').text(totalempcount);
        });
    });


    $(document).on('keypress','#AssociateSearch',function(e){
        if (e.keyCode === 13 || e.which === 13) {
            e.preventDefault();
            return false;
        }
    });

    $("body").on("keyup", "#AssociateSearch", function(e) {
        var v = $(this).val();
        if(v){
            if(e.keyCode == 13){
                $("#increment-table tbody tr").addClass("hide");
                $("tr[class*='"+v+"']").removeClass("hide"); 
                $("#check-all").addClass("hide"); 
            }
        }else{
            $("#increment-table tbody tr").removeClass("hide");
            $("#check-all").removeClass("hide"); 
        }
        
    });

    $(".cancel_details").click(function() {
        $(".overlay-modal-details, .show_item_details_modal").fadeOut("slow", function() {
          /*Remove inline styles*/

          $(".overlay-modal, .item_details_dialog").removeAttr("style");
          $('body').css('overflow', 'unset');
        });
    });

    $('#checkAll').click(function(){
        var checked =$(this).prop('checked');
        var selectemp = 0;
        if(!checked) {
            selectemp = $('input:checkbox:checked').length;
            
        } else {
            selectemp = $('input:checkbox:not(:checked)').length;
        }
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

    function checkSingle(as_id)
    {
        if($('#check_'+as_id).is(':checked')){
            $('#inc_'+as_id).data('checked',1);
        }else{
            $('#inc_'+as_id).data('checked',0);
        }
        getSum();
    }

    function calculateInc()
    {
        var per = $('#inc_percent').val(), total = 0, emp = 0;
        
        if(per){
            $('.increment-amount').each(function( index ) {
                if($(this).data('checked') == 1){
                    var t = Math.ceil(($(this).data('salary') - 1850)*(per/100));
                    $(this).val(t);
                    var sal = $(this).data('salary'),
                    nes = parseInt(sal) + parseInt(t);

                    if(isNaN(t) || t === '' ){
                      own = nes = '';
                    }
                    $(this).parent().next().children().text(parseFloat(per).toFixed(2)),
                    $(this).parent().next().next().children().text(nes);
                    total += t;
                    emp++;
                }
            });
        }else{
            $('.increment-amount').each(function( index ) {
                $(this).val('');
                $(this).parent().next().children().text(''),
                $(this).parent().next().next().children().text('');
            });
        }
        // $('.total-amount').text(total);
        var salary = $("#currentSalary").val();
        var totalS = parseFloat(salary) + parseFloat(total);
        $('.total-top-amount').text(totalS);
        $('.total-amount').text(total);
        $('.total-employee').text(emp);
        
    }

    function getSum()
    {
        var total = 0; 
        var emp = 0; 
        $('.increment-amount').each(function( index ) {
            if($(this).val() && $(this).data('checked') == 1){
                total += parseInt($(this).val()); 
                emp++; 
            }
        });
        var salary = $("#currentSalary").val();
        var totalS = parseFloat(salary) + parseFloat(total);
        $('.total-top-amount').text(totalS);
        $('.total-amount').text(total);
        $('.total-employee').text(emp);
    }

    $(document).on('keyup','#inc_percent', function(){
        calculateInc();
    });
    $(document).on('keyup','.increment-amount',function(){
        // calculate %
        var sal = $(this).data('salary'),
            bsal= parseInt(sal) - 1850,
            val = $(this).val(),
            per = parseFloat((val / bsal)*100).toFixed(2),
            nes = parseInt(sal) + parseInt(val);
        if(isNaN(val) || val === '' ){
          per = nes = '';
          $(this).css({'border-color':'#d7dbda'});
          //$(this).notify('Please uncheck the checkbox', 'error');
        }else{
          $(this).css({'border-color':'#46b4c0'}); 
        }
        $(this).parent().next().children().text(per),
        $(this).parent().next().next().children().text(nes);
        getSum();
    });

    function checkAllGroupIncrement(val){
        var id = '';
      if($(val).is(':checked')){
        $('.checkbox-inc').prop("checked", true);
        $('.increment-amount').data('checked',1);
       
      }else{
        $('.checkbox-inc').prop("checked", false);
        $('.increment-amount').data('checked',0);
      }
      getSum();
    }

    function getApprovalData(unit_id = null, employee_type = null)
    {
        $('.app-loader').show();
        $.ajax({
            type: "POST",
            url: '{{ url("hr/payroll/increment/get-approval-data") }}',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data : {
                unit : unit_id,
                employee_type : employee_type
            },
            success: function(res)
            {
                $('.approval-result').html(res);
                getSum();
                $('.app-loader').hide();
                $('.eligible-print').show();
            },
            error: function (reject) {
                 $('.app-loader').hide();
            }
        });   
      }

      $(document).on('change','#employee_type', function(){

        getApprovalData($('#unit_approv').val(),$(this).val());
      })

        $(document).ready(function() {
            $('#activityReport').on('submit', function(e) {
                  e.preventDefault();
                  activityProcess();
            });
            //getApprovalData();
            $("input[type=number]").addClass('inputnumber');
            $("input[type=number]").on("focus", function() {
                $(this).on("keydown", function(event) {
                    if (event.keyCode === 38 || event.keyCode === 40 || event.keyCode === 69) {
                    event.preventDefault();
                }
        });
        $(this).on("mousewheel", function(event) {
            event.preventDefault();
        });
      });
    });
    
</script>
@endpush

@endsection
