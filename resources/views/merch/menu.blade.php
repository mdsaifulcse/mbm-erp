@php
    $user = auth()->user();
    $segment1 = request()->segment(1);
    $segment2 = request()->segment(2);
    $segment3 = request()->segment(3);
    $segment4 = request()->segment(4);
@endphp

<nav class="iq-sidebar-menu">
    <ul id="iq-sidebar-toggle" class="iq-menu">

        <li>
            <a href="{{ url('/') }}" class="iq-waves-effect"><i class="las la-home"></i><span>Dashboard</span></a>
        </li>
        <li class="{{ $segment2 == ''?'active':'' }}">
            <a href="{{ url('/merchandising') }}" class="iq-waves-effect"><i class="lab la-opencart"></i><span>Merchandising Dashboard</span></a>
        </li>

        <!-- Merchandising Sub menu start -->

        @if(auth()->user()->canany([]) || $user->hasRole('Super Admin'))

            <li class="@if($segment2 == 'style') active @endif">
                <a href="#style" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i
                        class="las la-tshirt"></i><span>Style</span><i
                        class="las la-angle-right iq-arrow-right"></i></a>
                <ul id="style" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                    @if($user->can('New Style') || $user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'style' && $segment3=='create') active @endif">
                            <a href="{{ url('merch/style/create') }}"><i class="las la-folder-plus"></i> New Style</a>
                        </li>
                    @endif
                    @if($user->can('Style List' ) || $user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'style' && $segment3=='style_list') active @endif">
                            <a href="{{ url('merch/style/style_list') }}"><i class="las la-list-ol"></i> Style Bom & Costing List</a>
                        </li>
                    @endif
                   {{--  @if($user->can('Style List' ) || $user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'style' && $segment3=='style_list') active @endif">
                            <a href="{{ url('merch/style/style_list') }}"><i class="las la-list-ol"></i> Style Bom Create</a>
                        </li>
                    @endif --}}
                </ul>
            </li>
        @endif

     {{-- Sample start --}}
        @if(auth()->user()->canany([]) || $user->hasRole('Super Admin'))

            <li class="@if($segment2 == 'sample') active @endif">
                <a href="#sample" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i
                        class="las fa-shopping-cart"></i><span>Sample</span><i
                        class="las la-angle-right iq-arrow-right"></i></a>
                <ul id="sample" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                    @if($user->can('Rfp') || $user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'sample' && $segment3=='create') active @endif">
                            <a href="{{ url('merch/sample/sample_requisition') }}"><i class="las la-book-medical"></i> Sample Requisition</a>
                            
                        </li>
                    @endif
                     @if($user->can('Sample List') || $user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'sample' && $segment3==' ') active @endif">
                            <a href="{{ url('merch/sample/sample_requisition_list') }}"><i class="las la-list-ol"></i> Sample List</a>
                            {{-- <i class="fas fa-shopping-cart"></i> --}}
                        </li>
                    @endif
                  
                </ul>
            </li>
        @endif

        <li class="{{ $segment2 == 'reservation'?'active':'' }}">
            <a href="{{ url('merch/reservation')}}" class="iq-waves-effect"><i class="las la-list-ol"></i><span>Reservation</span></a>
        </li>

        @if(auth()->user()->canany([]) || $user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'orders') active @endif">
                <a href="#order" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i
                        class="lab la-first-order"></i><span>Order</span><i
                        class="las la-angle-right iq-arrow-right"></i></a>
                <ul id="order" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

                    @if($user->can('Order Entry' ) || $user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'orders' && $segment3=='create') active @endif">
                            <a href="{{ url('merch/orders/create') }}"><i class="las la-folder-plus"></i> New Order</a>
                        </li>
                    @endif
                    @if($user->can('Order List' ) || $user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'orders' && $segment3=='') active @endif">
                            <a href="{{ url('merch/orders') }}"><i class="las la-list-ol"></i> Order Bom & Costing List</a>
                        </li>
                    @endif

                </ul>
            </li>
        @endif
        @if(auth()->user()->canany([]) || $user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'po') active @endif">
                <a href="#po" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i
                        class="las la-shopping-cart"></i><span>PO</span><i
                        class="las la-angle-right iq-arrow-right"></i></a>
                <ul id="po" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

                    <li class="@if($segment2 == 'po' && $segment3=='create') active @endif">
                        <a href="{{ url('merch/po/create')}}"><i class="las la-folder-plus"></i> Create PO</a>
                    </li>

                    <li class="@if($segment2 == 'po' && $segment3=='') active @endif">
                        <a href="{{ url('merch/po')}}"><i class="las la-list-ol"></i> PO List</a>
                    </li>

                </ul>
            </li>
        @endif
        @if(auth()->user()->canany([]) || $user->hasRole('Super Admin'))

            <li class="@if($segment2 == 'time_action') active @endif">
                <a href="#time_action" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i
                        class="las la-business-time"></i><span>Time and Action</span><i
                        class="las la-angle-right iq-arrow-right"></i></a>
                <ul id="time_action" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                    @if($user->can('New Style') || $user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'time_action' && $segment3=='tna_order_list') active @endif">
                            <a href="{{url('merch/time_action/tna_order_list')}}"><i class="las la-folder-plus"></i>
                                Order TNA List</a>
                        </li>
                    @endif
                    @if($user->can('Style List' ) || $user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'time_action' && $segment3=='tna_order') active @endif">
                            <a href="{{url('merch/time_action/tna_order')}}"><i class="las la-list-ol"></i> Order
                                TNA</a>
                        </li>
                    @endif


                    <li class="@if($segment2 == 'time_action' && $segment3=='tna_status') active @endif">
                        <a href="{{url('merch/time_action/tna_status')}}" class="iq-waves-effect"><i
                                class="las la-clipboard-list"></i>TNA Status</a>
                    </li>

                </ul>
            </li>
        @endif
        {{-- <li class="{{ $segment2 == 'order_bom'?'active':'' }}">
           <a href="{{ url('/merch/order_bom') }}" class="iq-waves-effect"><i class="las la-clipboard-list"></i><span>Order BOM</span></a>
        </li>
        <li class="{{ $segment2 == 'order_costing'?'active':'' }}">
           <a href="{{ url('/merch/order_costing') }}" class="iq-waves-effect"><i class="las la-clipboard-list"></i><span>Order Costing</span></a>
        </li> --}}

        <li class="{{ $segment2 == 'order-breakdown'?'active':'' }}">
            <a href="{{ url('merch/order_breakdown')}}" class="iq-waves-effect"><i class="las la-list-ol"></i><span>Order Breakdown</span></a>
        </li>

        @if(auth()->user()->canany([]) || $user->hasRole('Super Admin'))

            <li class="@if($segment2 == 'order_booking') active @endif">
                <a href="#order_booking" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i
                        class="las la-book-medical"></i><span>Order Booking</span><i
                        class="las la-angle-right iq-arrow-right"></i></a>
                <ul id="order_booking" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                    @if($user->can('New Style') || $user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'order_po_booking' && $segment3=='showForm') active @endif">
                            <a href="{{url('merch/order_po_booking/showForm')}}"><i class="las la-folder-plus"></i>
                                Create Order Booking</a>
                        </li>
                    @endif
                    @if($user->can('Style List' ) || $user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'order_po_booking') active @endif">
                            <a href="{{url('merch/order_po_booking')}}"><i class="las la-list-ol"></i> Order Booking
                                List</a>
                        </li>
                    @endif
                </ul>
            </li>

        @endif


        @if(auth()->user()->canany([]) || $user->hasRole('Super Admin'))

            <li class="@if($segment2 == 'pi') active @endif">
                <a href="#pi" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i
                        class="las la-shopping-bag"></i><span>PI</span><i class="las la-angle-right iq-arrow-right"></i></a>
                <ul id="pi" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                    @if($user->can('New Style') || $user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'style' && $segment3=='style_new') active @endif">
                            <a href="{{url('merch/proforma_invoice/form')}}"><i class="las la-folder-plus"></i> Create
                                PI</a>
                        </li>
                    @endif
                    @if($user->can('Style List' ) || $user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'style' && $segment3=='style_list') active @endif">
                            <a href="{{url('merch/proforma_invoice')}}"><i class="las la-list-ol"></i> PI List</a>
                        </li>
                    @endif
                </ul>
            </li>

        @endif

        <li class="{{ $segment2 == 'costing-compare'?'active':'' }}">
            <a href="{{url('merch/costing-compare')}}" class="iq-waves-effect"><i class="las la-list-ol"></i><span>Costing Compare</span></a>
        </li>
        @if(auth()->user()->canany([]) || $user->hasRole('Super Admin'))

            <li class="@if($segment2 == 'sales-contract') active @endif">
                <a href="#sales_contract" class="iq-waves-effect collapsed" data-toggle="collapse"
                   aria-expanded="false"><i class="las la-address-book"></i><span>Sales Contract</span><i
                        class="las la-angle-right iq-arrow-right"></i></a>
                <ul id="sales_contract" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                    @if($user->can('New Style') || $user->hasRole('Super Admin'))

                        <li class="@if($segment2 == 'sales_contract' && $segment3=='sales_contract_entry') active @endif">
                            <a href="{{url('merch/sales_contract/sales_contract_entry')}}"><i
                                    class="las la-folder-plus"></i> Create Sales Contract</a>
                        </li>
                    @endif
                    @if($user->can('Style List' ) || $user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'sales_contract' && $segment3=='sales_contract_list') active @endif">
                            <a href="{{url('merch/sales_contract/sales_contract_list')}}"><i class="las la-list-ol"></i>
                                Sales Contract List</a>
                        </li>
                    @endif
                </ul>
            </li>

        @endif
        <li class="{{ $segment2 == 'reports'?'active':'' }}">
            <a href="#" class="iq-waves-effect" style="color: #c9c9c9;"><i
                    class="las la-file-invoice"></i><span>Reports</span></a>
        </li>
        
        {{-- @if(auth()->user()->canany([]) || $user->hasRole('Super Admin'))

            <li class="@if($segment2 == 'reports') active @endif">
                <a href="#merch-reports" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i
                        class="las la-file-invoice"></i><span>Reports</span><i class="las la-angle-right iq-arrow-right"></i></a>
                <ul id="merch-reports" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                    @if($user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'reports' && $segment3=='style_details') active @endif">
                            <a href="{{ url('merch/reports/style_details') }}"><i class="las la-folder-plus"></i> Style Details</a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif --}}
        
        @if(auth()->user()->canany([]) || $user->hasRole('Super Admin'))

            <li class="@if($segment2 == 'setup') active @endif">
                <a href="#merch-setup" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i
                        class="las la-cog"></i><span>Setup</span><i class="las la-angle-right iq-arrow-right"></i></a>
                <ul id="merch-setup" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                    @if($user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'setup' && $segment3=='sampletype') active @endif">
                            <a href="{{ url('merch/setup/sampletype') }}"><i class="las la-folder-plus"></i> Sample Type</a>
                        </li>
                    @endif

                    @if($user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'setup' && $segment3=='buyer_info') active @endif">
                            <a href="{{ url('merch/setup/buyer_info') }}"><i class="las la-folder-plus"></i> Buyer </a>
                        </li>
                    @endif

                    @if($user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'setup' && $segment3=='productsize') active @endif">
                            <a href="{{ url('merch/setup/productsize') }}"><i class="las la-folder-plus"></i> Size Group</a>
                        </li>
                    @endif

                    @if($user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'setup' && $segment3=='product_type') active @endif">
                            <a href="{{ url('merch/setup/product_type') }}"><i class="las la-folder-plus"></i> Product
                                Type</a>
                        </li>
                    @endif

                    @if($user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'setup' && $segment3=='garments_type') active @endif">
                            <a href="{{ url('merch/setup/garments_type') }}"><i class="las la-folder-plus"></i> Garments
                                Type</a>
                        </li>
                    @endif

                    @if($user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'setup' && $segment3=='season') active @endif">
                            <a href="{{ url('merch/setup/season') }}"><i class="las la-folder-plus"></i> Season</a>
                        </li>
                    @endif

                    @if($user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'setup' && $segment3=='supplier') active @endif">
                            <a href="{{ url('merch/setup/supplier') }}"><i class="las la-folder-plus"></i> Supplier</a>
                        </li>
                    @endif

                    @if($user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'setup' && $segment3=='item') active @endif">
                            <a href="{{ url('merch/setup/item') }}"><i class="las la-folder-plus"></i> Materials
                                Item</a>
                        </li>
                    @endif

                    @if($user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'setup' && $segment3=='article') active @endif">
                            <a href="{{ url('merch/setup/article') }}"><i class="las la-folder-plus"></i> Article </a>
                        </li>
                    @endif

                    @if($user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'setup' && $segment3=='operation') active @endif">
                            <a href="{{ url('merch/setup/operation') }}"><i class="las la-folder-plus"></i>
                                Operation</a>
                        </li>
                    @endif

                    @if($user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'setup' && $segment3=='spmachine') active @endif">
                            <a href="{{ url('merch/setup/spmachine') }}"><i class="las la-folder-plus"></i> Special
                                Machine</a>
                        </li>
                    @endif

                    @if($user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'setup' && $segment3=='wash_category') active @endif">
                            <a href="{{ url('merch/setup/wash_category') }}"><i class="las la-folder-plus"></i> Wash
                                Category</a>
                        </li>
                    @endif

                    @if($user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'setup' && $segment3=='wash_type') active @endif">
                            <a href="{{ url('merch/setup/wash_type') }}"><i class="las la-folder-plus"></i> Wash
                                Type</a>
                        </li>
                    @endif

                    @if($user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'setup' && $segment3=='tna_library') active @endif">
                            <a href="{{ url('merch/setup/tna_library') }}"><i class="las la-folder-plus"></i> TNA
                                Library</a>
                        </li>
                    @endif

                    @if($user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'setup' && $segment3=='tna_template') active @endif">
                            <a href="{{ url('merch/setup/tna_template') }}"><i class="las la-folder-plus"></i> TNA
                                Template</a>
                        </li>
                    @endif

                    @if($user->hasRole('Super Admin'))
                        <li class="@if($segment2 == 'setup' && $segment3=='approval') active @endif">
                            <a href="{{ url('merch/setup/approval') }}"><i class="las la-folder-plus"></i> Approval
                                Hierarchy</a>
                        </li>
                    @endif


                </ul>
            </li>
        @endif

        <li class="@if($segment2 == 'profile' || $segment2 == 'user') active @endif">
            <a href="#profile" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i
                    class="las la-tshirt"></i><span>Profile</span><i class="las la-angle-right iq-arrow-right"></i></a>
            <ul id="profile" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                <li class="@if($segment2 == 'profile' ) active @endif">
                    <a href="{{url('profile')}}"><i class="las la-folder-plus"></i>My Profile</a>
                </li>
                <li class="@if($segment2 == 'user') active @endif">
                    <a href="{{url('user/change-password')}}"><i class="las la-list-ol"></i> Account settings</a>
                </li>
            </ul>
        </li>

        <li>
            <a href="{{ url('/') }}" class="iq-waves-effect" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> <span><i
                        class="las la-sign-out-alt"></i> {{ __('Sign out') }}</span></a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>

    </ul>
</nav>
