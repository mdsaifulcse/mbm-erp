@php $billSeg = request()->segment(3); @endphp
<div class="iq-card">
     <div class="iq-card-body">
       <div class="iq-email-list">
          <button class="btn btn-primary btn-lg btn-block mb-3 font-size-16 p-2" data-target="#compose-email-popup" data-toggle="modal">Bill</button>
            <div class="iq-email-ui nav flex-column nav-pills">
                <li class="nav-link {{ $billSeg == 'bill-setting'?'active':'' }}"  >
                    <a href="{{ url('hr/setup/bill-setting') }}"><i class="las la-city"></i>Settings</a>
                </li>
                
                <li class="nav-link {{ $billSeg == 'bill-type'?'active':'' }}"  >
                    <a href="{{ url('hr/setup/bill-type') }}"><i class="las la-city"></i>Type</a>
                </li>
                
            </div>
       </div>
     </div>
  </div>