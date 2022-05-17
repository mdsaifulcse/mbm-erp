<div class="modal right fade" id="right_modal_jobcard" tabindex="-1" role="dialog" aria-labelledby="right_modal_jobcard">
    <div class="modal-dialog modal-lg right-modal-width" role="document" > 
        <div class="modal-content">
            <div class="modal-header">
                <a class="view prev_btn-job" data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Back to Report">
                    <i class="las la-chevron-left"></i>
                </a>
                <h5 class="modal-title right-modal-title text-center capitalize" id="modal-title-right"> &nbsp; </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding-top: 0;">
                <div class="offset-1 col-10 h-min-400">
                    <div class="modal-content-result" id="content-result">
                        <form class="extra-break" >
                        @php
                            
                        @endphp
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group has-required has-float-label select-search-group d-block" style="height:85px !important;">
                                    {{ Form::select('rule_days[]', $days, [], ['id'=>'rule_days', 'class'=> 'form-control', 'required'=>'required', 'multiple']) }} 
                                    <label  for="rule_days"> Days  </label>
                                </div>
                                <div class="form-group has-float-label">
                                    <input type="text" name="rule_break_time" id="rule_break_time" class=" form-control" value="" />
                                    <label  for="rule_break_time">Break Minute</label>
                                </div>
                                
                                <div class="form-group has-float-label">
                                    <input type="date" name="rule_start_date" id="rule_start_date" class=" form-control" value="" />
                                    <label  for="rule_start_date">Start Date</label>
                                </div>
                                
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group has-required has-float-label select-search-group d-block" style="height:85px !important;">
                                    {{ Form::select('rule_designation[]', $designation, [], ['id'=>'rule_designation', 'class'=> 'form-control', 'required'=>'required', 'multiple']) }} 
                                    <label  for="rule_designation"> Designation  </label>
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
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
