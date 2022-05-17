@extends('hr.layout')
@section('title', 'Bonus Process')
@section('main-content')
@push('css')
    <style>
        .display-3 {
            height: 60px;
            line-height: .3;
        }
        .card {
            --tw-bg-opacity: 1;
            --tw-text-opacity: 1;
            background-color: rgba(255,255,255,var(--tw-bg-opacity));
            border: 1px solid rgba(36,37,38,.08);
            border-radius: .9rem;
            box-shadow: 0 0 20px rgb(0 0 0 / 8%);
            color: rgba(34,41,47,var(--tw-text-opacity));
            margin-left: auto;
            margin-right: auto;
            max-width: 100%;
            overflow: hidden;
            position: relative;
            transition: all 1s;
        }
        .expanded-card.is-tooling .expanded-card-left {
            /*background: linear-gradient(
            0deg
            ,#8b60ed,#b372bd);*/
            background: linear-gradient(
            180deg
            ,#6edcc4,#1aab8b);
            height: 100%;
        }
        .expanded-card.is-testing .expanded-card-left {
            background: linear-gradient(
            180deg
            ,#6edcc4,#1aab8b);
            height: 100%;
        }
        .expanded-card.is-techniques .expanded-card-left {
            background: linear-gradient(
            180deg
            ,#21c8f6,#637bff);
            height: 100%;
        }
        .expanded-card.is-languages .expanded-card-left {
            background: linear-gradient(
            0deg
            ,#f19a1a,#ffc73c);
            height: 100%;
        }
        
        
        .tw-relative {
            position: relative;
        }
        .radial-progress-container {
            position: relative;
        }
        .radial-progress-inner {
            align-items: center;
            border-radius: 50%;
            bottom: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            left: 0;
            margin: 0 auto;
            position: absolute;
            right: 0;
            top: 0;
        }
        img[data-src].lazyloaded {
            opacity: 1;
        }
        
        .tw-w-full {
            width: 100%;
            margin-top: 5px;
            display: block;
        }
        .tw-rounded-full {
            border-radius: 9999px;
        }
        .tw-bg-black-transparent-10 {
            background-color: rgba(0,0,0,.1);
        }
        .amount{
            float: right;
        }
    </style>
@endpush
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
                <li class="active">Bonus Process</li>
            </ul><!-- /.breadcrumb --> 
        </div>
 
        <div class="page-content"> 
            <div class="row1">
              @if(count($unitGroup) > 0)
                @foreach($unitGroup as $key => $getUnitBonus)
                <div class="iq-card iq-card-block iq-card-stretch iq-card-height p-0">
                  <div class="iq-card-body iq-bg-primary rounded p-2">
                     <div class="text-center">
                        @php
                            $keyYear = explode('-', $key);
                            $r = $keyYear[0];
                            $y = $keyYear[1];
                        @endphp
                        <h4 class="card-title">{{ $bonusType[$r]['bonus_type_name']??'' }} - {{ $y }}</h4>
                     </div>
                  </div>
               </div>
                <div class="row justify-content-sm-center">
                  @foreach($getUnitBonus as $bonus) 

                    <div class="col-md-4 pr-0">
                        <div class="card expanded-card tw-border-none is-incomplete tw-flex tw-pr-4 is-tooling iq-mb-3">
                            <div class="row no-gutters">
                               <div class="col-5">
                                  <div class="expanded-card-left tw-mr-4 tw-rounded-xl tw-flex tw-flex-col tw-justify-between tw-items-center tw-py-5 p-2 tw-flex-shrink-0" style="box-shadow: rgba(0, 0, 0, 0.17) 0px 4px 9px 0px;">
                                        <h4 class="expanded-card-skill-button tw-w-full tw-bg-black-transparent-10 tw-rounded-full tw-py-2 tw-leading-none p-2 text-white uppercase text-center">{{ $unit[$bonus->unit_id]['hr_unit_short_name']??''}}</h4>
                                        
                                        <a class="tw-relative card-thumbnail tw-block tw-my-4 md:tw-my-0">

                                            {{-- <div class="radial-progress-container" style="height: 113px; width: 113px;     margin: 0 auto;">
                                                <div class="radial-progress-inner" style="width: 105px;">
                                                    
                                                    <img class=" ls-is-cached lazyloaded" data-src="{{ asset('assets/images/developer-practice.png')}}" alt="Developer Practice" width="98" height="98" src="{{ asset('assets/images/developer-practice.png')}}">
                                                </div>
                                                <svg class="radial-progress-bar" width="113" height="113" version="1.1" xmlns=""><defs><radialGradient id="radial-gradient4643.394709251333" fx="1" fy="0.5" cx="0.5" cy="0.5" r="0.65"><stop offset="30%" stop-color="white"></stop><stop offset="100%" stop-color="white"></stop></radialGradient></defs><circle r="52.5" cx="56.5" cy="56.5" fill="transparent" stroke="rgba(0, 0, 0, 0.2)" stroke-dasharray="329.8672286269283" stroke-dashoffset="0" stroke-linecap="round" style="height: 113px; width: 113px; stroke-width: 4px;"></circle><circle transform="rotate(270, 56.5,56.5)" r="52.5" cx="56.5" cy="56.5" fill="transparent" stroke="url(#radial-gradient4643.394709251333)" stroke-dasharray="329.8672286269283" stroke-dashoffset="329.8672286269283" stroke-linecap="round" style="height: 113px; width: 113px; stroke-width: 4px; stroke-dashoffset: 329.867; transition: stroke-dashoffset 1000ms linear 0s;"></circle></svg>
                                            </div> --}}
                                        </a>
                                        <div class="expanded-card-difficulty tw-w-full tw-text-center">
                                            <div class="tw-font-semibold tw-text-white tw-text-3xs tw-mb-2">
                                               <div class="text-center text-white">
                                                
                                                @if($bonus->amount != null && $bonus->amount > 0)
                                                 Bonus Amount : {{ $bonusType[$bonus->bonus_type_id]['eligible_month'] }}
                                                @else
                                                 Basic : {{ $bonus->percent_of_basic }}%
                                                @endif
                                                <br>
                                                Last Date : {{ \Carbon\Carbon::parse($bonus->cutoff_date)->subMonths($bonusType[$bonus->bonus_type_id]['eligible_month'])->toDateString() }}
                                                <br>
                                                Bonus Date : {{ $bonus->cutoff_date }}
                                                
                                              </div>
                                            </div>
                                            <br>
                                        </div>
                                        <div class="status-process">
                                        @php
                                            $btn = '';
                                        @endphp
                                        @if($bonus->approved_at != null)
                                            <p><i>Authorized by </i><br>
                                                <b> &nbsp; &nbsp; -  {{ $getUser[$bonus->approved_by]->name?? ''}}</b>
                                            </p>
                                            @if(auth()->user()->can('Bonus Sheet'))
                                                @php
                                                    $url = url("hr/payroll/bonus-disburse?bonus_type=$bonus->id");
                                                    $btn = 'Disburse';
                                                    $aClass = 'btn btn-dark mt-2';
                                                    $icon = '<i class="fa fa-check"></i>';
                                                @endphp
                                            @endif
                                        @else
                                            <p><i>Status </i><br></p>
                                            @if($bonus->hr_by == null || $bonus->hr_by == '')
                                                <b> &nbsp; &nbsp; -  Waiting for HR confirmation</b>
                                                @if(auth()->user()->can('Bonus Approval Hr'))
                                                    @php
                                                        $url = url("hr/operation/bonus-sheet-process-for-approval?bonus_sheet=$bonus->id&audit=1");
                                                        $btn = 'Process';
                                                        $aClass = 'btn btn-primary mt-2';
                                                        $icon = '<i class="fa fa-cogs"></i>';
                                                    @endphp
                                                
                                                @endif
                                            @elseif($bonus->audit_by == null || $bonus->audit_by == '')
                                                <b> &nbsp; &nbsp; -  Waiting for Audit confirmation</b>

                                                @if(auth()->user()->can('Bonus Approval Audit'))
                                                    @php
                                                        $url = url("hr/operation/bonus-sheet-process-for-approval?bonus_sheet=$bonus->id&audit=2");
                                                        $btn = 'Process';
                                                        $aClass = 'btn btn-primary mt-2';
                                                        $icon = '<i class="fa fa-cogs"></i>';
                                                    @endphp
                                                @endif
                                            @elseif($bonus->management_by == null || $bonus->management_by == '')
                                                
                                                <b> &nbsp; &nbsp; -  Waiting for Management confirmation</b>
                                                @if(auth()->user()->can('Bonus Approval Management'))
                                                    @php
                                                        $url = url("hr/operation/bonus-sheet-process-for-approval?bonus_sheet=$bonus->id&audit=3");
                                                        $btn = 'Process';
                                                        $aClass = 'btn btn-primary mt-2';
                                                        $icon = '<i class="fa fa-cogs"></i>';
                                                    @endphp
                                                @endif
                                            @endif
                                        @endif
                                        </div>
                                    </div>
                               </div>
                               <div class="col-7">
                                  <div class="card-body">

                                    <h4 class="card-title">
                                        <a class="bonus_single_rule" data-value="{{ json_encode($bonus) }}" data-ruleid="{{ $bonus->id }}" data-headline="{{ $bonusType[$r]['bonus_type_name']??'' }} - {{ $y }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Process History" style="font-size:16px;">
                                        {{ $bonusType[$bonus->bonus_type_id]['bonus_type_name']??'' }} - {{ $bonus->bonus_year }}
                                        </a>
                                    </h4>
                                    <div class="card-content">
                                         <table border="0" width="100%" class="p-3">
                                            <tr>
                                                <td style="width: 50%">OT Employee</td>
                                                <td style="width:40%; " class="after-load">: <span class="amount" id="ot_emp-{{ $key }}-{{ $bonus->unit_id }}">0</span></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 50%">Non-OT Employee</td>
                                                <td style="width:40%; " class="after-load">: <span class="amount" id="nonot_emp-{{ $key }}-{{ $bonus->unit_id }}" >0</span></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 50%"><b>Total Employee</b></td>
                                                <td style="width:40%; " class="after-load">: <span class="amount" id="total_emp-{{ $key }}-{{ $bonus->unit_id }}"><b>0</b></span></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 50%">OT Bonus</td>
                                                <td style="width:40%; " class="after-load">: <span class="amount" id="ot_bonus-{{ $key }}-{{ $bonus->unit_id }}">0</span></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 50%">Non-OT Bonus</td>
                                                <td style="width:40%; " class="after-load">: <span class="amount" id="nonot_bonus-{{ $key }}-{{ $bonus->unit_id }}">0</span></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 50%"><b>Total Bonus</b></td>
                                                <td style="width:40%; " class="after-load"><b>: <span class="amount" id="total_bonus-{{ $key }}-{{ $bonus->unit_id }}">0</span></b></td>
                                            </tr>
                                            
                                        </table>
                                        
                                        @if($btn != '')
                                        <a href="{{ $url }}" class="{{ $aClass }} pull-right mb-3">{!! $icon !!}  {{ $btn }}</a>
                                        @endif
                                    </div>
                                     
                                    
                                  </div>
                               </div>
                            </div>
                        </div>
                    </div>
                  @endforeach
                </div>
                @endforeach
                @else
                <div class="col">
                    <div class="panel w-100">
                      <div class="panel-body">
                          <h4 class="text-center">No Process Found!</h4>
                      </div>
                    </div>
                </div>
                @endif
           </div>
            
        </div> 
    </div> 
</div>
@include('hr.common.right-modal') 
@push('js')
    <script>
        $(document).ready(function(){ 
            $('.after-load span').html('<i class="ace-icon fa fa-spinner fa-spin orange bigger-30"></i>');
        });

        var response = [];
        var unit_data = <?php echo json_encode($unitBonus); ?>;
        jQuery.each(unit_data, function(index, value) {
            $.ajax({
                url: '{{ url('hr/payroll/bonus-sheet-by-bonus-rule-summery') }}',
                type: 'get',
                data: {
                    id: value.id,
                    group_unit: value.unit_id
                },
                success: function(res) {
                    console.log(res);
                    response[value.combine_year] = res;
                    $('#ot_emp-'+value.combine_year+'-'+value.unit_id).html(res.ot);
                    $('#nonot_emp-'+value.combine_year+'-'+value.unit_id).html(res.nonot);
                    $('#total_emp-'+value.combine_year+'-'+value.unit_id).html(res.total_emp);
                    $('#ot_bonus-'+value.combine_year+'-'+value.unit_id).html(res.ot_amount);
                    $('#nonot_bonus-'+value.combine_year+'-'+value.unit_id).html(res.nonot_amount);
                    $('#total_bonus-'+value.combine_year+'-'+value.unit_id).html(res.total_bonus);
                },
                error: function() {
                    console.log('error occored');
                }
            })
        });
        $(document).on('click', '.bonus_single_rule', function(){
            var ruleId = $(this).data('ruleid');
            var headline = $(this).data('headline')
            var value = $(this).data('value')
            $("#modal-title-common").html(headline);
            $('#right_modal_common').modal('show');
            $("#content-result-common").html(loaderContent);
            $.ajax({
                url: '{{ url("hr/payroll/bonus-sheet-process-history")}}',
                data: {
                  id: ruleId,
                  value: value
                },
                type: "GET",
                
                success: function(response){
                    // console.log(response);
                    if(response !== 'error'){
                        setTimeout(function(){
                            $("#content-result-common").html(response);
                        }, 1000);
                    }else{
                        console.log(response);
                    }
                }
            });

        });
    </script>
@endpush
@endsection