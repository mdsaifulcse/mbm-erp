<div class="iq-sidebar">
  <input type="hidden" value="{{ url('/') }}" id="base_url">
  <div class="iq-sidebar-logo d-flex justify-content-between">
    <a href="{{ url('/') }}">
       <img src="{{ asset('images/mbm-logo-w.png') }}" class="img-fluid" alt="MBM">
       {{-- <span>MBM</span> --}}
    </a>
    <div class="iq-menu-bt-sidebar">
       <div class="iq-menu-bt align-self-center">
          <div class="wrapper-menu">
             <div class="main-circle"><i class="las la-ellipsis-h"></i></div>
             <div class="hover-circle"><i class="las la-ellipsis-v"></i></div>
          </div>
       </div>
    </div>
 </div>
 <div id="sidebar-scrollbar">
   
   @php
   $user = auth()->user();
   $segment1 = request()->segment(1);
   $segment2 = request()->segment(2);
   $segment3 = request()->segment(3);
   $segment4 = request()->segment(4);
   $segment5 = request()->segment(5);
   @endphp

   <nav class="iq-sidebar-menu">
      <ul id="iq-sidebar-toggle" class="iq-menu">
        
         <li>
            <a href="{{ url('/') }}" class="iq-waves-effect"><i class="las la-home"></i><span>Dashboard</span></a>
         </li>
         <li class="{{ $segment2 == ''?'active':'' }}">
            <a href="{{ url('/pms') }}" class="iq-waves-effect"><i class="las la-users"></i><span>PMS Dashboard</span></a>
         </li>

         <li class="{{ $segment2 == 'requisition'?'active':'' }}">
            <a href="#Requisition" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-list"></i><span>Requisition</span><i class="las la-angle-right iq-arrow-right"></i></a>

            <ul id="Requisition" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

               <li class="@if($segment2 == 'requisition' && $segment3=='type') active @endif">
                  <a  href="{{ route('pms.requisition.type.index') }}"><i class="las la-arrow-right"></i>{{ __('Requisition Type') }}</a>
               </li>
               
            </ul>
         </li>


         <li class="{{ $segment2 == 'store-manage'?'active':'' }}">
            <a href="#store" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-list"></i><span>{{ __('Store Manage') }}</span><i class="las la-angle-right iq-arrow-right"></i></a>
            <ul id="store" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

               <li class="@if($segment2 == 'store-manage' && $segment3=='store-requistion') active @endif">
                  <a  href="{{ route('pms.store-manage.store-requistion-list') }}"><i class="las la-arrow-right"></i>{{ __('Requisition') }}</a>
               </li>
            </ul>
         </li>

         <li class="{{ $segment2 == 'rfp'?'active':'' }}">
            <a href="#RFP" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-list"></i><span>{{ __('RFP') }}</span><i class="las la-angle-right iq-arrow-right"></i></a>
            <ul id="RFP" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

                <li class="@if($segment2 == 'rfp' && $segment3=='requisitions') active @endif">
                  <a  href="{{ route('pms.rfp.requisitions.list') }}"><i class="las la-arrow-right"></i>{{ __('Requisition List') }}</a>
               </li>

                 <li class="@if($segment2 == 'rfp' && $segment3=='request-proposal') active @endif">
                  <a  href="{{ route('pms.rfp.request-proposal.create') }}"><i class="las la-arrow-right"></i>{{ __('Proposal') }}</a>
               </li>

               <li class="@if($segment2 == 'rfp' && $segment3=='request-proposal') active @endif">
                  <a  href="{{ route('pms.rfp.request-proposal.index') }}"><i class="las la-arrow-right"></i>{{ __('Proposal List') }}</a>
               </li>
            </ul>
         </li>

         <li class="{{ $segment2 == 'inventory'?'active':'' }}">
            <a href="#Inventory" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-list"></i><span>{{ __('Inventory') }}</span><i class="las la-angle-right iq-arrow-right"></i></a>
            <ul id="Inventory" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

               <li class="@if($segment2 == 'inventory' && $segment3=='inventory-summery') active @endif"><a  href="{{route('pms.inventory.inventory-summery.index')}}"><i class="las la-arrow-right"></i>{{ __('Inventory Summery') }}</a></li>

               <li><a href="#"><i class="las la-arrow-right"></i>{{ __('Inventroy Log/Transection') }}</a></li>
            </ul>
         </li>


          <li class="{{ $segment2 == 'product-management'?'active':'' }}">
            <a href="#Product-Management" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-list"></i><span>{{ __('Product Manage') }}</span><i class="las la-angle-right iq-arrow-right"></i></a>
            <ul id="Product-Management" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

              <li class="@if($segment2 == 'product-management' && $segment3=='brand') active @endif"><a href="{{ route('pms.product-management.brand.index') }}" class="iq-waves-effect"><i class="las la-dollar-sign"></i><span>Brand</span></a></li>

              <li class="@if($segment2 == 'product-management' && $segment3=='category') active @endif"><a href="{{ route('pms.product-management.category.index') }}" class="iq-waves-effect"><i class="las la-city"></i>Category</a></li>

              <li class="@if($segment2 == 'product-management' && $segment3=='product') active @endif"><a href="{{ route('pms.product-management.product.index') }}" class="iq-waves-effect"><i class="las la-shopping-basket"></i><span>{{ __('Product') }}</span></a></li>
               
            </ul>
         </li>

         <li class="{{ $segment2 == 'quotation'?'active':'' }}">
            <a href="#masterQuotations" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-list"></i><span>{{ __('Quotations Manage') }}</span><i class="las la-angle-right iq-arrow-right"></i></a>
            <ul id="masterQuotations" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

                <li class="@if($segment2 == 'quotation' && $segment3=='index') active @endif"><a href="{{ route('pms.quotation.quotations.index') }}" class="iq-waves-effect"><i class="las la-shopping-basket"></i><span>{{ __('Quotations') }}</span></a></li>
            </ul>
         </li>

         <li class="@if($segment1 == 'pms' && $segment2=='supplier') active @endif">
            <a href="{{ route('pms.supplier.index') }}" class="iq-waves-effect"><i class="las la-list"></i><span>Supplier</span></a>
         </li>  

        <li class="">
            <a href="#masterSettings" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-list"></i><span>{{ __('Master Settings') }}</span><i class="las la-angle-right iq-arrow-right"></i></a>
            <ul id="masterSettings" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li class="">
                  <a href="#" class="iq-waves-effect"><i class="las la-project-diagram"></i><span>Project</span></a>
               </li>
                <li class="@if($segment1 == 'pms' && $segment2=='warehouse') active @endif">
                  <a href="{{ route('pms.warehouse.index') }}" class="iq-waves-effect"><i class="las la-school"></i><span>{{ __('Location') }}</span></a>
               </li>

               <li class="">
                  <a  href="#"><i class="las la-arrow-right"></i>{{ __('Settings') }}</a>
               </li>
            </ul>
         </li>

         <li class="">
            <a href="#masterAcl" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-list"></i><span>{{ __('ACL') }}</span><i class="las la-angle-right iq-arrow-right"></i></a>
            <ul id="masterAcl" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

               <li class="">
                  <a  href="#"><i class="las la-arrow-right"></i>{{ __('Role') }}</a>
               </li>
               <li class="">
                  <a  href="#"><i class="las la-arrow-right"></i>{{ __('Menu') }}</a>
               </li>
               <li class="">
                  <a  href="#"><i class="las la-arrow-right"></i>{{ __('Permission') }}</a>
               </li>
            </ul>
         </li>

         
      </ul>
   </nav>

   <div class="p-3"></div>
</div>
</div>