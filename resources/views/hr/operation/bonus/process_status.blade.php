
<style type="text/css">
    .iq-bg-danger.tab-link-inline i {
        background: #ec8886 !important;
    }
    #top-tab-list li i {
        height: 40px;
        width: 40px;
        line-height: 40px;
    }
    blockquote {
      text-indent: 9px;
      border-left: 2px solid #cecece;
    }
    @supports ( hanging-punctuation: first) {
      blockquote {
        text-indent: 0;
        hanging-punctuation: first;
      }
    }

    blockquote::before {
        content: open-quote;
    }
    blockquote::after {
        content: close-quote;
    }
    blockquote {
        quotes: "“" "”" "‘" "’";
    }
    .media-support-info h6 {
      font-size: 13px;
    }
    #top-tab-list li a {
      background: #eff7f8;
      color: #3c3c3c;
    }
    .audit-stauts-de{
      position: relative;
      padding: 15px 30px;
      bottom: 0;
    }
    .modal-body{
      background: #e9e9e9;
      padding: 0 !important;
    }
    #top-tab-list {
      margin: 0 -10px 10px !important;
    }
</style>
<div class="row p-3">
  @if(count($auditHistory) > 0)
  <div class="col-sm-9">
    <div class="iq-card " style="height:98%">
      <div class="iq-card-body ">


        @php $msg = ''; $link = ''; $icon = 'la-exclamation-circle'; $status = 'danger'; @endphp
        @php 
          $bonus = (object)$bonus;
          $hrEmployee = $users[$bonus->hr_by]->employee??'';
          $auditEmployee = $users[$bonus->audit_by]->employee??'';
          $managementEmployee = $users[$bonus->management_by]->employee??'';
          $button = 'Process Bonus';
        @endphp
        <form class="form">
          @if($bonus->hr_by == null || $bonus->hr_by == '')
            @php 
              $icon = 'la-fingerprint'; $status = 'danger';
              $msg = 'Bonus Of <strong>'. $bonusType[$bonus->bonus_type_id]['bonus_type_name'].' - '.$bonus->bonus_year .'</strong> has not Process yet!';
              $department = 'HR';
              if(Auth::user()->can('Bonus Approval Hr')){
                $link = '<h3><a href="'.url("hr/operation/bonus-sheet-process-for-approval?bonus_sheet=$bonus->id&audit=1").'" class="btn btn-lg btn-primary"><i class="las la-hand-point-right"></i> '.$button.'</a></h3>';
              }
            @endphp
          @elseif($bonus->audit_by == null || $bonus->audit_by == '')
            @php
              $icon = 'la-clipboard-check'; $status = 'success';
              $msg = 'Bonus Of <strong>'. $bonusType[$bonus->bonus_type_id]['bonus_type_name'].' - '.$bonus->bonus_year .'</strong> is now at Audit Department for verification';
              $department = 'Audit'; 
              if(Auth::user()->can('Bonus Approval Audit')){
                $link = '<h3><a href="'.url("hr/operation/bonus-sheet-process-for-approval?bonus_sheet=$bonus->id&audit=2").'" class="btn btn-lg btn-primary"><i class="las la-hand-point-right"></i> '.$button.'</a></h3>';
              }
            @endphp
          @elseif($bonus->management_by == null || $bonus->management_by == '')
            @php
              $icon = 'la-user-check'; $status = 'success';
              $msg = 'Bonus Of <strong>'. $bonusType[$bonus->bonus_type_id]['bonus_type_name'].' - '.$bonus->bonus_year .'</strong> is waiting for Management confirmation'; 
              $department = 'Management'; 
              if(Auth::user()->can('Bonus Approval Management')){
                $link = '<h3><a href="'.url("hr/operation/bonus-sheet-process-for-approval?bonus_sheet=$bonus->id&audit=3").'" class="btn btn-lg btn-primary"><i class="las la-hand-point-right"></i> '.$button.'</a></h3>';
              }
            @endphp
          @endif
          
          <div class="text-center">
              
              <i class="las {{$icon}} f-80 text-{{$status}}"></i>
              <br>
              <p style="font-size: 14px;">{!! $msg !!}</p>
             
              <h4>{!! $link !!}</h4>
              <br>
          </div>
       </form>
       <div class="row justify-content-center">
            <div class="col audit-stauts-de">
              
               <ul id="top-tab-list" class="p-0">
                   <li class="" id="account">
                      
                      <a class="tab-link-inline {{ $bonus->hr_by == null || $bonus->hr_by == ''?'iq-bg-danger':'iq-bg-primary' }}" data-toggle="tooltip" title="">
                      <i class="las la-fingerprint"></i>
                      <span class="f-16">HR
                          
                      </span>

                      </a>
                      @if($bonus->hr_by != null && $bonus->hr_by != '')
                      <a class="text-right">
                        <span> Authorized by <hr>-  {{ $hrEmployee->as_name??'' }} <br> {{$designation[$hrEmployee->as_designation_id]['hr_designation_name']??''}} </span>
                      </a>
                      @endif
                   </li>
                   <li id="audit" class="">
                      <a class="tab-link-inline {{ $bonus->audit_by == null || $bonus->audit_by == ''?'iq-bg-danger':'iq-bg-primary' }}" data-toggle="tooltip" title="">
                      <i class="las la-clipboard-check"></i><span class="f-16">Audit</span>
                      </a>
                      @if($bonus->audit_by != null && $bonus->audit_by != '')
                      <a class="text-right">
                        <span> Authorized by <hr>-  {{ $auditEmployee->as_name??'' }} <br> {{$designation[$auditEmployee->as_designation_id]['hr_designation_name']??''}} </span>
                      </a>
                      @endif
                   </li>
                   
                   <li id="management" class="">
                      <a class="tab-link-inline {{ $bonus->management_by == null || $bonus->management_by == ''?'iq-bg-danger':'iq-bg-primary' }}" data-toggle="tooltip" title="">
                      <i class="las la-user-check"></i><span class="f-16">Management</span>
                      </a>
                      @if($bonus->management_by != null && $bonus->management_by != '')
                      <a class="text-right">
                        <span> Authorized by <hr>-  {{ $managementEmployee->as_name??'' }} <br> {{$designation[$managementEmployee->as_designation_id]['hr_designation_name']??''}} </span>
                      </a>
                      @endif
                   </li>
                </ul>
            </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-3 pl-0">
    <div class="iq-card min-h-415">
      <div class="iq-card-body mh-410">
        <ul class="speciality-list m-0 p-0">
          @if(count($auditHistory) > 0)
          @foreach($auditHistory as $history)
            @php
              if($history->stage == 1){
                $po = 'HR';
              }elseif($history->stage == 2){
                $po = 'Audit';
              }elseif($history->stage == 4){
                $po = 'Management';
              }  
            @endphp
            @if($history->status == 1)
            <li class="d-flex mb-4 align-items-center">
             <div class="user-img img-fluid">
                 <a href="#" class="iq-bg-success">
                    <i class="las f-18 la-check-circle"></i>
                 </a>
              </div>
             <div class="media-support-info ml-3">
                
                <h6>
                  {{ $po }}
                  <small class="float-right mt-1">{{ $history->created_at }}</small>
                </h6>
                <h6 class="capitalize">- {{ $users[$history->audit_by]->employee->as_name??'' }}</h6>
                @if($history->comment != null)
                <span class="mb-0">
                <blockquote style="padding: 0 5px;">{{ $history->comment }}</blockquote>
                </span>
                @endif
             </div>
            </li>
            @else
            <li class="d-flex mb-4 align-items-center">
             <div class="user-img img-fluid">
                 <a href="#" class="iq-bg-danger">
                    <i class="las f-18 la-times-circle"></i>
                 </a>
              </div>
              <div class="media-support-info ml-3">
                <h6>
                  {{ $po }}
                  <small class="float-right mt-1 font-italic">{{ $history->created_at }}</small>
                </h6>
                <h6 class="capitalize">- {{ $users[$history->audit_by]->employee->as_name??'' }}</h6>
                @if($history->comment != null)
                <span class="mb-0">
                <blockquote style="padding: 0 5px;">{{ $history->comment }}</blockquote>
                </span>
                @endif
             </div>
            </li>
            @endif
          @endforeach
          @endif
       </ul>
      </div>
    </div>
  </div>
  @else
    <div class="iq-card w-100" style="height:100%;">
      <div class="iq-card-body ">
        <h4 class="text-center"> No History Found!</h4>
      </div>
    </div>
  @endif
</div>

