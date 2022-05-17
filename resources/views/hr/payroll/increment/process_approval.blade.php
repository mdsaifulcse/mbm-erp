<form id="increment-action" action="{{url('hr/payroll/increment-action')}}" method="post"  data-level="{{$set['field']}}"> 
    @csrf 
    <div class="row">
        <div class="col-sm-3">
            
            <div style="padding-left: 15px;">
                <input type="hidden" id="currentSalary" value="{{ $totalSalary }}">
                <h4 class="text-center">Increment Cost Summary</h4>
                <table border="0" style="width: 100%;">
                    <tr>
                        <td style="width: 80%;">Total Current Amount: </td>
                        <td id="" class="text-primary text-right">{{ bn_money($totalSalary) }}</td>
                    </tr>
                    <tr>
                        <td style="width: 80%;">HR Proposed Amount: </td>
                        <td id="" class="text-primary text-right">{{ bn_money($totalSalary + $level1Data['salary']) }}</td>
                    </tr>
                    @if(in_array($set['step'], [1]))
                       
                        <tr>
                            <td style="width: 80%;">HR Head Review Amount:</td>
                            <td class="total-top-amount text-primary text-right"></td>
                        </tr> 
                    @endif
                    @if(in_array($set['step'], [2,3]))
                        <tr>
                            <td style="width: 80%;">HR Head Review Amount: </td>
                            <td id="" class="text-primary text-right">{{ bn_money($totalSalary + $level2Data['salary']) }}</td>
                        </tr>
                    @endif

                    @if(in_array($set['step'], [2]))
                       
                        <tr>
                            <td style="width: 80%;">Senior Management Review Amount:</td>
                            <td class="total-top-amount text-primary text-right"></td>
                        </tr> 
                    @endif
                    @if(in_array($set['step'], [3]))
                       <tr>
                            <td style="width: 80%;">Senior Management Review Amount: </td>
                            <td id="" class="text-primary text-right">{{ bn_money($totalSalary + $level3Data['salary']) }}</td>
                        </tr>
                        <tr>
                            <td style="width: 80%;">Top Management Approve Amount:</td>
                            <td class="total-top-amount text-primary text-right"></td>
                        </tr> 
                    @endif
                    
                </table>
            </div>
        </div>
        <div class="col-sm-6">
            <p class="text-center">
                <strong style="font-size: 18px;">
                @if(isset($un))
                    {{$un}}
                @else
                    Increment Approval: {{$set['type']}}
                @endif
        
                </strong></p>
            
            <p style="font-weight: bold;margin-left: 10px;text-align: center;" >No of Employee: <b class="total-employee text-primary"></b> &nbsp;&nbsp;Total Amount : <b class="total-amount text-primary"></b></p>
            <br>
            <div class="row justify-content-center hidden-print" id="color-code">
                <div class="offset-2 col-8">
                    <ul class="color-bar m-0 p-0">
                        @if(in_array($set['step'], [1,2,3]))
                             
                            <li><span class="color-label bg-success1"></span><span class="lib-label"> {{ $level1Data['count'] }} Proposed </span></li>
                        @endif
                        @if(in_array($set['step'], [2,3]))
                             
                            <li><span class="color-label bg-success2"></span><span class="lib-label"> {{ $level2Data['count'] }} Review</span></li> 
                        @endif
                       
                        @if(in_array($set['step'], [3]))
                            <li><span class="color-label bg-success3"></span><span class="lib-label"> {{ $level3Data['count'] }} Approved</span></li> 
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-sm-2">

            <div class="form-group has-float-label selet-search-group" >
                <select id="employee_type" name="employee_type" class="form-control" >
                    <option>Select Type</option>
                    <option value="All" @if($input['employee_type'] == 'all')selected @endif>All</option>
                    <option value="management" @if($input['employee_type'] == 'management')selected @endif>Management & Staff</option>
                    <option value="worker" @if($input['employee_type'] == 'worker')selected @endif>Worker</option>
                </select>
                <label for="employee_type">Employee Type</label>
            </div>

        </div>

        <div class="col-sm-1 pl-0">
            <div class="form-group has-float-label" data-toggle="tooltip" data-placement="top" title="" data-original-title="Changing any value will recalculate increment amount of each employee!">
            <input type="text" class="inc_percent text-center form-control" id="inc_percent" name="inc_percent" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" value="" autocomplete="off"  />
            <label for="inc_percent">Increment %</label>
            <input type="hidden" id="unit_approv" value="{{$input['unit']}}">
        </div>
        </div>
    </div>
     
    <br>
    <div id="print-section">
        <div id="incr-print" style="height: 400px;overflow-y: auto;">
                    
            <style>
                .designation-boss .select2-container .select2-selection--single {
                    height: 25px!important;
                }
                .designation-boss .select2-container--default .select2-selection--single .select2-selection__arrow b {
                    top: 10px;

                }
                .designation-boss{
                    height: 25px !important;
                }
                .designation-boss .select2-container--default .select2-selection--single .select2-selection__rendered{
                    height: 25px !important;
                }
                .designation-boss .select2-container--default .select2-selection--single .select2-selection__arrow {
                        height: 25px;
                    }
                .designation-boss .select2-container--default .select2-selection--single .select2-selection__rendered {
                    color: #414141;
                    line-height: 25px;
                }
                .signature{
                    display: none;
                }
                .table td, table.table-head th {
                    padding: 3px !important;
                    font-size: 11px;
                }
                table.table-head th, .table thead th {
                    top: -1px;
                    z-index: 10;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #dee2e6 !important;
                }
                .table th {
                    padding: 3px !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #dee2e6;
                    border-top: 1px solid #dee2e6;
                    border-bottom: 1px solid #dee2e6;
                    text-align: center;
                }
                table.table-head th{
                    box-shadow: none;
                    background: #ececec;
                }
                .color-bar li {
                    width: 32.5%;
                }
                .text-yellow{
                    color: yellow !important;
                }
                .text-shad{
                    color: #1b1919 !important;
                }
                
                .bg-success1{
                    background: #ceebee !important;
                    border-bottom: 1px solid #ccc;
                }
                .bg-success2{
                    background: #9dd1d8 !important;
                    border-bottom: 1px solid #ccc;
                }
                .bg-success3{
                    background: #0db3c6 !important;
                    border-bottom: 1px solid #ccc;
                }
                table.table-head th {
                    font-weight: 600;
                }
                @media print {
                    #color-code{display: none}
                    .show-on-print{
                        display: block!important;
                    }
                    #incr-print{
                        height: auto !important;
                        width: 100%;
                    }
                    
                    .pagebreak {
                        page-break-before: always !important;
                    }
                    @page {
                        size: landscape;
                    }
                    .designation-boss{display: none;}
                    .print-hidden{display: none;}
                    .badge{display: none;}
                    input{
                        border: 0 !important;
                        text-align: right;
                        max-width: 80px;
                    }
                    tfoot{
                        display: none;
                    }
                    * {
                        font-size: 11px !important;
                        font-weight: normal;
                    }
                    .disburse-button{
                        display: none;
                    }
                    .signature{
                        display: block;
                    }
                    input[type="date"]::-webkit-inner-spin-button,
                    input[type="date"]::-webkit-calendar-picker-indicator {
                        display: none;
                    }
                    @import url(https://fonts.googleapis.com/css?family=Poppins:200,200i,300,400,500,600,700,800,900&amp;display=swap);
                    body {
                        font-family: Poppins,sans-serif;
                    }
                    tfoot td{
                        position: relative !important;
                    }
                    
                }
                .flex-chunk{
                    min-width: 40px;margin-right: 2px;border-right: 1px solid;padding-right: 2px;
                }
                .flex-chunk:last-child{
                    margin-right: 0px;border-right: 0px solid;padding-right: 0px;
                }
                
            </style> 
            <div class="show-on-print text-center" style="display:none;text-align: center;">
                <h1>Increment Sheet</h1>
                <h1>{{$un}}</h1>
                <p>No of Employee: <span class="total-employee text-primary"></span> &nbsp;&nbsp;Total Amount : <span class="total-amount text-primary"></span></p>
                <br><br>
            </div>
                   
            <table id="increment-table" class="table table-head table-hover" style="width:98%;border:1px solid #ccc;font-size:9px;position: relative;" cellpadding="2" cellspacing="0" border="1" align="center">
                <thead>
                    
                    <tr style="text-align: center;" >
                        <th rowspan="2" width="10" >SL.</th>
                        <th rowspan="2" width="" >Employee</th>
                        {{-- <th rowspan="2">Name</th> --}}
                        <th rowspan="2">Designation</th>
                        <th rowspan="2">Department</th>
                        <th rowspan="2">Section</th>
                        <th rowspan="2">DOJ</th>
                        <th rowspan="2">Present <br> Salary</th>
                        <th colspan="2" style="white-space: nowrap;border-bottom: 1px solid #c0c0c0 !important;">Last Increment</th>
                        {{-- <th rowspan="2">Effective <br> Date(mm/dd/YY)</th> --}}
                        @if(in_array($set['step'], [1,2,3]))
                        <th colspan="3" style="white-space: nowrap;border-bottom: 1px solid #c0c0c0 !important;" class="bg-success1 text-black">HR Proposed</th>
                        @endif
                        @if(in_array($set['step'], [2,3]))
                        <th colspan="2" style="white-space: nowrap;border-bottom: 1px solid #c0c0c0 !important;" class="bg-success2 text-shad">HR Head Review</th>
                        @endif
                        @if(in_array($set['step'], [3]))
                        <th colspan="2" style="white-space: nowrap;border-bottom: 1px solid #c0c0c0 !important;" class="bg-success3 text-white">Senior Management Review</th>
                        @endif
                        <th colspan="5" style="white-space: nowrap;border-bottom: 1px solid #c0c0c0 !important;">{{$set['type']}} Approve</th>
                        <th rowspan="2" class="disburse-button" width="40" >
                            <input type="checkbox" class="checkBoxGroup" onclick="checkAllGroupIncrement(this)" id="check-all"  checked />
                        </th>
                    </tr>
                    <tr>
                        <th style="top:25px;">Amount</th>
                        <th style="top:25px;white-space: nowrap;">Date</th>
                        @if(in_array($set['step'], [1,2,3]))
                        <th style="top:25px;width:60px;" class="bg-success1 text-black">Amount</th>
                        <th style="top:25px;width:40px;" class="bg-success1 text-black">%</th>
                        <th style="top:25px;width:60px;" class="bg-success1 text-black">Designation</th>
                        @endif
                        @if(in_array($set['step'], [2,3]))
                        <th style="top:25px;width:60px;" class="bg-success2 text-shad">Amount</th>
                        <th style="top:25px;width:40px;" class="bg-success2 text-shad">%</th>
                        @endif
                        @if(in_array($set['step'], [3]))
                        <th style="top:25px;width:60px;" class="bg-success3 text-white">Amount</th>
                        <th style="top:25px;width:40px;" class="bg-success3 text-white">%</th>
                        @endif
                        
                        <th style="top:25px;width:100px;">Amount</th>
                        <th style="top:25px;width:40px;">%</th>
                        <th style="top:25px;width:60px;">Gross</th>
                        <th style="top:25px;width:60px;">Effective Date</th>
                        <th style="top:25px;width:120px;">Designation</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 0; @endphp
                    @foreach($increment as $k => $list)
                        <tr class="row_{{ $list->associate_id }} row_{{ $list->as_oracle_code }}">
                            <td style="text-align: center;">
                                {{ ++$i }}
                                
                            </td>
                            <td>
                                {{-- <b style="font-size:11px;margin:0;padding:0;"> --}}
                                    {{ $list->as_name }}
                                    <br>
                                    {{ $list->associate_id }}
                                {{-- </b> --}}
                            </td>
                            
                            {{-- <td>
                                <p class="yearly-activity text-primary" style="margin:0;padding:0;cursor: pointer;font-size: 11px;" data-id="{{ $list->as_id}}" data-eaid="{{ $list->associate_id }}" data-ename="{{ $list->as_name }}" data-edesign="{{ $designation[$list->as_designation_id]['hr_designation_name']}}" data-image="{{emp_profile_picture($list)}}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Preview yearly activity report">{{ $list->as_name }} </p>
                                
                            </td> --}}
                            <td>
                                {{ $designation[$list->as_designation_id]['hr_designation_name']}}
                               
                            </td>
                            
                            <td>
                               {{ $department[$list->as_department_id]['hr_department_name']??''}}
                            </td>
                            <td>
                                {{ $section[$list->as_section_id]['hr_section_name']??''}}<br>
                            </td>
                            <td>
                                <p style="margin:0;padding:0;white-space: nowrap;">
                                    @php
                                        $doj = date('d-M-y', strtotime($list->as_doj));
                                    @endphp
                                    {{ $doj }}
                                </p>
                            </td>

                            
                            <td style="text-align: right;padding-right:10px;">
                                {{ $list->current_salary }}
                            </td>
                            <td style="text-align: right;">
                                @php
                                $incr = '';
                                if(isset($last_increment[$list->associate_id])){
                                    $incr = $last_increment[$list->associate_id];
                                }
                                @endphp
                                @if($incr != '')
                                    {{$incr->increment_amount}}
                                @endif
                            </td>
                            <td style="text-align: center;white-space: nowrap;">
                                @if($incr != '')
                                    @if($incr->effective_date)
                                        {{date('d-M-y',strtotime($incr->effective_date))}}
                                    @endif
                                @endif
                            </td>
                            
                            @if(in_array($set['step'], [1,2,3]))
                            <td style="text-align: right;" class="bg-success1 text-black">
                                @php 
                                    $proposed = $list->increment_amount??'';
                                    $percent = '';
                                    if($proposed){

                                        $percent  = round(($proposed/ ($list->current_salary -1850))*100,2);
                                    }
                                    $des = $users[$list->prepared_by]->as_designation_id??null;
                                    $desName = $des != null?($designation[$des]['hr_designation_name']??''):'';
                                    $hrUser = $users[$list->prepared_by]->name.' <br> '.$desName;
                                @endphp
                                <a data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $hrUser }}">{{ ($proposed) }}</a>
                            </td>
                            <td style="text-align: center;" class="bg-success1 text-black">
                                {{$percent}}
                            </td>
                            <td style="text-align: center;" class="bg-success1 text-black">
                                {{ $list->initial_designation_id != null?($designation[$list->initial_designation_id]['hr_designation_name']??''):'' }}
                            </td>
                            @endif
                            @if(in_array($set['step'], [2,3]))
                            <td style="text-align: right;" class="bg-success2 text-shad">
                                @php 
                                    $proposed = $list->level_1_amount??'';
                                    $percent = '';
                                    if($proposed){

                                        $percent  = round(($proposed/ ($list->current_salary -1850))*100,2);
                                    }
                                $des = $users[$list->level_1_approval]->as_designation_id??null;
                                    $desName = $des != null?($designation[$des]['hr_designation_name']??''):'';
                                    $hrUser1 = $users[$list->level_1_approval]->name.' <br> '.$desName;
                                    $checkAmount = ($list->increment_amount - $list->level_1_amount) >= 0?(($list->increment_amount - $list->level_1_amount) > 0?'text-yellow':'text-black'):'text-red'; 
                                @endphp
                                <a data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $hrUser1 }}" class="{{ $checkAmount }}">{{ ($proposed) }}</a>
                            </td>
                            <td style="text-align: center;" class="bg-success2 text-shad">
                                {{$percent}}
                            </td>
                            @endif
                            @if(in_array($set['step'], [3]))
                            <td style="text-align: right;" class="bg-success3 text-white">
                                @php 
                                    $proposed = ($list->level_2_amount??'');
                                    $percent = '';
                                    if($proposed){

                                        $percent  = round(($proposed/ ($list->current_salary -1850))*100,2);
                                    }
                                    $des = $users[$list->level_2_approval]->as_designation_id??null;
                                    $desName = $des != null?($designation[$des]['hr_designation_name']??''):'';
                                    $hrUser2 = $users[$list->level_2_approval]->name.' <br> '.$desName;
                                    $checkAmount = ($list->increment_amount - $list->level_2_amount) >= 0?(($list->increment_amount - $list->level_2_amount) > 0?'text-yellow':'text-white'):'text-red';
                                @endphp
                                <a data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $hrUser2 }}" class="{{ $checkAmount }}">{{ ($proposed) }}</a>
                            </td>
                            <td style="text-align: center;" class="bg-success3 text-white">
                                {{$percent}}
                            </td>
                            @endif
                            
                            <td style="text-align: center;position: relative;">
                                <input id="incemptype_{{$list->associate_id}}" type="hidden" name="increment['{{ $list->id }}']['emp_type']" value="{{ $list->as_emp_type_id }}">

                                <input id="inc_{{$list->associate_id}}" type="text" name="increment['{{ $list->id }}']['amount']" class="form-control text-center increment-amount " style="width:100px;margin:auto;height:25px;font-size: 11px;@if($proposed)border-color:#21C4987F; @endif" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"  data-salary="{{ $list->current_salary }}" value="{{ ($proposed) }}" data-checked="1" >
                            </td>

                            <td style="text-align: center;">
                                <span class="proposed-percent">{{$percent}}</span>
                            </td>
                            <td style="text-align: right;">
                                <span class="proposed-gross">
                                    @if($proposed)  
                                        {{($proposed + $list->current_salary) }}
                                    @endif                                 
                                </span>
                            </td>
                            <td>
                                
                                <input type="date" min="2020-01-01" name="increment['{{ $list->id }}']['effective_date']" class="form-control" value="@if($list->effective_date != null && $list->effective_date !='0000-00-00'){{$list->effective_date}}@endif">
                            </td>
                            <td style="width:140px;vertical-align: middle;">
                                <input type="hidden" name="increment[{{$list->associate_id}}][prev_desgn]" value="{{$list->as_designation_id}}">
                                <div class="form-group has-float-label  select-search-group designation-boss" style="    margin-bottom: 0;">
                                @if($list->as_emp_type_id == 1)
                                      {{ Form::select('increment['. $list->id.'][desgn]', $management, $list->designation_id, ['placeholder'=>'No Promotion', 'id'=>'gender'.$list->associate_id, 'class'=> 'form-control designation', 'style' => 'height:25px !important;']) }} 
                                @else
                                    {{ Form::select('increment['. $list->id.'][desgn]', $worker, $list->designation_id, ['placeholder'=>'No Promotion', 'id'=>'gender'.$list->associate_id, 'class'=> 'form-control designation', 'style' => 'height:25px !important;']) }} 
                                @endif
                               </div>

                            </td>
                               
                            <td class="disburse-button" id="" style="text-align: center;">
                                <input type='checkbox' class="checkbox-inc" style="margin: 0 auto;" onclick="checkSingle('{{$list->associate_id}}')"  name="increment[{{ $list->id }}][status]" id="check_{{$list->associate_id}}" checked />
                                
                            </td>
                        </tr>
                            
                    @endforeach
                    
                </tbody>
                @if(count($increment) > 0)
                    <tfoot>
                        
                        <tr>
                            <td colspan="@if($set['step'] == 2) 14 @elseif($set['step'] == 3) 16 @else 12 @endif" style="text-align: right;position: sticky;background: #ececec;bottom: -1px; z-index: 100;"><b>Total</b></td>
                            <td style="text-align: center;position: sticky;background: #ececec;bottom: -1px; z-index: 100;">
                                <b class="total-amount">0</b>
                            </td>
                            <td class="text-center" colspan="5" style="position: sticky;background: #ececec;bottom: -1px; z-index: 100;">
                                
                            </td>
                        </tr>
                        <tr>
                            <td colspan="@if($set['step'] == 2) 20 @elseif($set['step'] == 3) 22 @else 18 @endif" class="text-right" style="text-align: center;position: sticky;background: #ececec;bottom: -1px; z-index: 100;padding: 10px !important;">
                                <input type="hidden" name="next_process" value="{{$set['next']}}" class="btn btn-primary">
                                @canany(['Increment Approval', 'Increment Process 2', 'Increment Process 1'])
                                    <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> @if($set['step']==3) Approve @else Process @endif</button>
                                @endcan
                            </td>
                        </tr>
                    </tfoot>
                @else
                    <tr><td colspan="@if($set['step'] == 2) 20 @elseif($set['step'] == 3) 22 @else 18 @endif" class="text-center">No eligible employee found!</td></tr>
                @endif
            </table>
        </div> 
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function() {
        $('.designation').select2();
    });
</script>
