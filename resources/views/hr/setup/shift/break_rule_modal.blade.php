@php 

$days = [
    'Fri' => 'Friday',
    'Sat' => 'Saturday',
    'Sun' => 'Sunday',
    'Mon' => 'Monday',
    'Tue' => 'Tuesday',
    'Wed' => 'Wednesday',
    'Thu' => 'Thursday'
];

$designation = collect(designation_by_id())->pluck('hr_designation_name','hr_designation_id');

@endphp
<div class="modal right fade" id="extra_rule" tabindex="-1" role="dialog" aria-labelledby="extra_ruleLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="extra_ruleLabel"><strong>Add Extra Rule</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <div class="modal-body">
            <form class="extra-break" >
                @php
                    
                @endphp
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group  has-float-label select-search-group d-block" style="height:85px !important;">
                            {{ Form::select('rule_days[]', $days, [], ['id'=>'rule_days', 'class'=> 'form-control', 'multiple']) }} 
                            <label  for="rule_days"> Select Days  </label>
                        </div>
                        <div class="form-group has-float-label has-required">
                            <input type="text" name="rule_break_time" id="rule_break_time" class=" form-control" value="" required />
                            <label  for="rule_break_time">Break Minute</label>
                        </div>
                        
                        <div class="form-group has-float-label">
                            <input type="date" name="rule_start_date" id="rule_start_date" class=" form-control" value="" />
                            <label  for="rule_start_date">Start Date</label>
                        </div>
                        
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group has-float-label select-search-group d-block" style="height:85px !important;">
                            {{ Form::select('rule_designation[]', $designation, [], ['id'=>'rule_designation', 'class'=> 'form-control', 'multiple']) }} 
                            <label  for="rule_designation">Select Designation  </label>
                        </div>
                        <div class="form-group has-float-label">
                            <input type="text" name="rule_break_start" id="rule_break_start" class=" form-control time" value=""  />
                            <label  for="rule_break_start">Start Time</label>
                        </div>
                        
                        <div class="form-group has-float-label">
                            <input type="date" name="rule_end_date" id="rule_end_date" class=" form-control" value=""  />
                            <label  for="rule_end_date">End Date</label>
                        </div>
                    </div>
                </div>
                <div class="form-group text-right">
                    <button id="add-break" type="button" class="btn btn-primary">Add Break</button>
                </div>
            </form>
        </div>
    </div>
  </div>
</div>