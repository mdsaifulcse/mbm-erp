@extends('hr.layout')
@section('title', 'Monthly Salary')

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
                <li class="active"> Monthly Salary</li>
            </ul>
        </div>
        @php
            $library['unit'] = unit_by_id();
            $library['area'] = area_by_id();
            $library['department'] = department_by_id();
            $library['section'] = section_by_id();
            $library['subsection'] = subSection_by_id();
            $library['floor'] = floor_by_id();
            $library['line'] = line_by_id();
            $unit = collect(unit_by_id())->map(function($q){
                return [
                    'label' => $q['hr_unit_name'],
                    'id' => $q['hr_unit_id']
                ];
            });
        @endphp
        <div class="page-content"> 
            {{-- <salary-disburse :library="{{ json_encode($library) }}" :unit='{{ $unit }}'></salary-disburse> --}}
            <div class="iq-accordion career-style mat-style  ">
                <div class="iq-card iq-accordion-block">
                   <div class="active-mat clearfix">
                      <div class="container-fluid">
                         <div class="row">
                            <div class="col-sm-12"><a class="accordion-title"><span class="header-title"> Employee Wise </span> </a></div>
                         </div>
                      </div>
                   </div>
                   <div class="accordion-details">
                      <div class="row1">
                          <div class="col-12">
                              <form class="" role="form" id="employeeWiseSalary">
                                  <div class="panel">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-sm-5">
                                                    <div class="form-group has-float-label has-required select-search-group">
                                                        {{ Form::select('as_id[]', [],'', ['id'=>'as_id', 'class'=> 'allassociates form-control select-search no-select', 'multiple'=>"multiple",'style', 'data-validation'=>'required']) }}
                                                        <label for="as_id">Employees</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-1 pr-0">
                                                  <div class="form-group has-float-label has-required">
                                                      <input type="number" class="perpage form-control" id="siperpage" name="perpage" placeholder="Per Page" required="required" value="1" min="1" autocomplete="off" />
                                                      <label for="siperpage">Per Page</label>
                                                  </div>
                                                </div>
                                                <div class="col-sm-2 pr-0">
                                                    <div class="form-group has-float-label select-search-group has-required">
                                                        <select name="sheet" class="form-control capitalize select-search" id="emp-sheet" required="required">
                                                            <option selected="" value="">Choose...</option>
                                                            <option value="0" selected="">Salary Sheet</option>
                                                            <option value="1">Payslip</option>
                                                        </select>
                                                        <label for="emp-sheet">Sheet</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group has-float-label has-required">
                                                      <input type="month" class="report_date form-control" id="emp-month" name="year_month" placeholder=" Month-Year"required="required" value="{{ date('Y-m', strtotime('-1 month')) }}"autocomplete="off" />
                                                      <label for="emp-month">Month</label>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="emp_status[]" value="1">
                                                <input type="hidden" name="emp_status[]" value="2">
                                                <input type="hidden" name="emp_status[]" value="3">
                                                <input type="hidden" name="emp_status[]" value="4">
                                                <input type="hidden" name="emp_status[]" value="5">
                                                <input type="hidden" name="emp_status[]" value="6">
                                                <div class="col-sm-2">
                                                    <button onclick="individual()" type="button" class="btn btn-primary btn-sm" id="individualBtn"><i class="fa fa-save"></i> Generate</button>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                  </div>
                              </form>
                          </div>
                      </div>
                   </div>
                </div>
                <div class="iq-card iq-accordion-block accordion-active">
                   <div class="active-mat clearfix">
                      <div class="container-fluid">
                         <div class="row">
                            <div class="col-sm-12"><a class="accordion-title"><span class="header-title"> Unit Wise </span> </a></div>
                         </div>
                      </div>
                   </div>
                   <div class="accordion-details">
                      <div class="row1">
                        <div class="col-12">
                            <form class="" role="form" id="unitWiseSalary"> 
                                <div class="panel mb-0">
                                    
                                    <div class="panel-body pb-0">
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="form-group has-float-label select-search-group">
                                                    <select name="unit[]" class="form-control capitalize select-search" id="unit" >

                                                        <option selected="" value="">Choose...</option>
                                                        
                                                        @foreach($unitList as $key => $value)
                                                        <option value="{{ $key }}">{{ $value }}</option>
                                                        @endforeach
                                                        
                                                    </select>
                                                  <label for="unit">Unit</label>
                                                </div>
                                                <div class="form-group has-float-label select-search-group">
                                                    <select name="location[]" class="form-control capitalize select-search" id="location">
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
                                                    <select name="floor_id" class="form-control capitalize select-search" id="floor" disabled >
                                                        <option selected="" value="">Choose...</option>
                                                    </select>
                                                    <label for="floor">Floor</label>
                                                </div>
                                                <div class="form-group has-float-label select-search-group">
                                                    <select name="line_id" class="form-control capitalize select-search" id="line" disabled >
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
                                                      $payType = ['cash'=>'Cash', 'rocket'=>'Rocket', 'bKash'=>'bKash', 'dbbl'=>'Dutch-Bangla Bank Limited.'];
                                                    ?>
                                                    {{ Form::select('pay_status', $payType, null, ['placeholder'=>'Select Payment Type', 'class'=>'form-control capitalize select-search', 'id'=>'paymentType']) }}
                                                    <label for="paymentType">Payment Type</label>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group has-float-label has-required">
                                                  <input type="month" class="report_date form-control" id="month" name="year_month" placeholder=" Month-Year"required="required" value="{{ date('Y-m', strtotime('-1 month')) }}"autocomplete="off" />
                                                  <label for="month">Month</label>
                                                </div>
                                                <div class="form-group has-float-label select-search-group">
                                                    <?php
                                                      $status = ['1'=>'Active','25' => 'Left & Resign','2'=>'Resign','3'=>'Terminate','4'=>'Suspend','5'=>'Left', '6'=> 'Maternity'];
                                                    ?>
                                                    {{ Form::select('emp_status[]', $status, 1, ['placeholder'=>'Select Employee Status ', 'class'=>'form-control capitalize select-search', 'id'=>'estatus']) }}
                                                    <label for="estatus">Status</label>
                                                </div>
                                                
                                                <div class="form-group has-float-label select-search-group">
                                                    <?php
                                                      $status = ['0'=>'No','1'=>'Yes'];
                                                    ?>
                                                    {{ Form::select('disbursed', $status, null, ['placeholder'=>'Select Salary Status ', 'class'=>'form-control capitalize select-search', 'id'=>'disbursed']) }}
                                                    <label for="disbursed">Disbursed</label>
                                                </div>

                                                <div class="form-group has-float-label select-search-group has-required">
                                                    <select name="sheet" class="form-control capitalize select-search" id="sheet" required="required">
                                                        <option selected="" value="">Choose...</option>
                                                        <option value="0" selected="">Salary Sheet</option>
                                                        <option value="1">Payslip</option>
                                                    </select>
                                                    <label for="sheet">Sheet</label>
                                                </div>
                                                <div class="form-group">
                                                  <button onclick="multiple()" class="btn btn-primary nextBtn btn-lg pull-right" type="button" id="unitFromBtn"><i class="fa fa-save"></i> Generate</button>
                                                </div>
                                            </div>   
                                        </div>
                                        
                                    </div>
                                </div>
                                
                            </form>
                            <!-- PAGE CONTENT ENDS -->
                        </div>
                        <!-- /.col -->
                    </div>
                   </div>
                </div>
                
            </div>
            <input type="hidden" value="0" id="setFlug">
            <div class="row">
                <div class="col h-min-400">
                    <div id="result-process-bar" style="display: none;">
                        <div class="iq-card">
                            <div class="iq-card-body">
                                
                                <div class="" id="result-show">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-info progress-bar-striped active" id="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                          0%
                                        </div>
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
<script type="text/javascript">
    
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

    // Reusable Ajax function
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

    // disbursed sheet
    function disburseSheet(form)
    {
        $("#result-process-bar").show();
        $("#result-show").html(loader);
        $('#setFlug').val(0);
        processbar(0);
        $.ajax({
            type: "get",
            url: '{{ url("hr/operation/unit-wise-salary-sheet")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            data: form.serialize(), // serializes the form's elements.
            dataType: "json",
            success: function(response)
            {
              // console.log(response);
              if(response !== 'error'){
                  $('#setFlug').val(1); 
                  processbar('success');
                  setTimeout(() => {
                      $("#result-show").html(response.view);
                  }, 1400);
              }else{
                  $('#setFlug').val(2); 
                  processbar('error');
              }
            },
            error: function (reject) {
                console.log(reject);
                processbar('error');
                $('#setFlug').val(2); 
            }
        });
    }

    //individual salary sheet
    function individual() 
    {
        var form = $("#employeeWiseSalary");
        var employee = $("#as_id").val();
        var month = $("#emp-month").val();
        var sheet = $("#emp-sheet").val();
        if(employee.length > 0 && month !== '' && sheet !== ''){
            disburseSheet(form)
        }else{
            $("#result-process-bar").hide();
            if(employee.length === 0){
                $.notify("Please Select At Least One Employee", 'error');
            }
            if(month === null){
                $.notify("Please Select Month", 'error');
            }
            if(sheet === ''){
                $.notify("Please Select Sheet", 'error');
            }
        }
    }

    //multiple salary sheet
    function multiple() 
    {
        var form = $("#unitWiseSalary");
        var unit = $("#unit").val();
        var location = $('select[name="location"]').val();
        var month = $("#month").val();
        var sheet = $("#sheet").val();
        if((unit !== '' || location !== '') && month !== '' && sheet !== ''){
            disburseSheet(form);
        }else{
            $("#result-process-bar").hide();
            if((unit === '' || location === '')){
                $.notify("Select Unit Or Location", 'error');
            }
            if(month === ''){
                $.notify("Please Select Month", 'error');
            }
            if(sheet === ''){
                $.notify("Please Select Sheet", 'error');
            }
        }
    }

    var incValue = 1;
    
    function processbar(percentage) {
        var setFlug = $('#setFlug').val();
        if(parseInt(setFlug) === 1){
            var percentageVaule = 99;
            $('#progress-bar').html(percentageVaule+'%');
            $('#progress-bar').css({width: percentageVaule+'%'});
            $('#progress-bar').attr('aria-valuenow', percentageVaule+'%');
            
            setTimeout(() => {
                percentageVaule = 0;
                percentage = 0;
                $('#progress-bar').html(percentageVaule+'%');
                $('#progress-bar').css({width: percentageVaule+'%'});
                $('#progress-bar').attr('aria-valuenow', percentageVaule+'%');
                //$("#result-process-bar").css('display', 'none');
                $("#result-show").html(loaderContent);
            }, 300);
        }else if(parseInt(setFlug) === 2){
            console.log('error');
        }else{
            // set percentage in progress bar
            percentage = parseFloat(parseFloat(percentage) + parseFloat(incValue)).toFixed(2);
            $('#progress-bar').html(percentage+'%');
            $('#progress-bar').css({width: percentage+'%'});
            $('#progress-bar').attr('aria-valuenow', percentage+'%');
            if(percentage < 40 ){
                incValue = 1;
                // processbar(percentage);
            }else if(percentage < 60){
                incValue = 0.8;
            }else if(percentage < 75){
                incValue = 0.5;
            }else if(percentage < 85){
                incValue = 0.2;
            }else if(percentage < 98){
                incValue = 0.1;
            }else{
                return false;
            }
            setTimeout(() => {
                processbar(percentage);
            }, 1000);
        }

    }
    $(".overlay-modal, .item_details_dialog").css("opacity", 0);
    /*Remove inline styles*/
    $(".overlay-modal, .item_details_dialog").removeAttr("style");
    /*Set min height to 90px after  has been set*/
    detailsheight = $(".item_details_dialog").css("min-height", "115px");
    $(document).on('click','.disbursed_salary',function(){
        let id = $(this).data('id');
        let associateId = $(this).data('eaid');
        let date = $(this).data('date');
        let name = $(this).data('name');
        let post = $(this).data('post');
        $("#modal-id").val(id);
        $("#modal-associateId").val(associateId);
        $("#modal-month").val($(this).data('month'));
        $("#modal-year").val($(this).data('year'));
        $('#disbursed_name').html(name);
        $('#disbursed_post').html(post);
        $('#disbursed_id').html('আইডি: '+associateId);
        $('#disbursed_body').html(date+' মাসের বেতন প্রদান করা হচ্ছে । ');
        /*Show the dialog overlay-modal*/
        $(".overlay-modal-details").show();
        $(".inner_body").show();
        /*Animate Dialog*/
        $(".show_item_details_modal").css("width", "225").animate({
          "opacity" : 1,
          height : detailsheight,
          width : "40%"
        }, 600, function() {
          /*When animation is done show inside content*/
          $(".fade-box").show();
        });
        // 
        
    });
    $(document).on('click','#confirm-disbursed',function(){
        let associate_id = $("#modal-associateId").val();
        let month = $("#modal-month").val();
        let year = $("#modal-year").val();
        let select_id = $("#modal-id").val();
        //alert(id);
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: '/hr/reports/employee-salary-disbursed',
            type: "post",
            data: { _token : _token,
                as_id: associate_id,
                year: year,
                month: month
            },
            success: function(response){
                console.log(response);
                if(response.status === 'success'){
                    $("#"+select_id).html( $.trim(response.value) ).effect('highlight',{},2500);
                }

            }
        });

        $(".overlay-modal-details, .show_item_details_modal").fadeOut("slow", function() {
          $(".overlay-modal, .item_details_dialog").removeAttr("style");
          $('body').css('overflow', 'unset');
        });
    });
    $(document).on('click','.cancel_details',function(){
        $(".overlay-modal-details, .show_item_details_modal").fadeOut("slow", function() {
          /*Remove inline styles*/

          $(".overlay-modal, .item_details_dialog").removeAttr("style");
          $('body').css('overflow', 'unset');
        });
    });
</script>
@endpush
@endsection