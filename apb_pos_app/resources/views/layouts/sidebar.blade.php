<aside class="sidebar sidebar-default sidebar-white sidebar-base navs-rounded-all ">
    <div class="sidebar-header d-flex align-items-center justify-content-start">
        <a href="{{ url('') }}" class="navbar-brand">

            <!--Logo start-->
            <div class="logo-main">
                <div class="logo-normal">
                    <svg class=" icon-30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="-0.757324" y="19.2427" width="28" height="4" rx="2"
                            transform="rotate(-45 -0.757324 19.2427)" fill="currentColor" />
                        <rect x="7.72803" y="27.728" width="28" height="4" rx="2"
                            transform="rotate(-45 7.72803 27.728)" fill="currentColor" />
                        <rect x="10.5366" y="16.3945" width="16" height="4" rx="2"
                            transform="rotate(45 10.5366 16.3945)" fill="currentColor" />
                        <rect x="10.5562" y="-0.556152" width="28" height="4" rx="2"
                            transform="rotate(45 10.5562 -0.556152)" fill="currentColor" />
                    </svg>
                </div>
                <div class="logo-mini">
                    <svg class=" icon-30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="-0.757324" y="19.2427" width="28" height="4" rx="2"
                            transform="rotate(-45 -0.757324 19.2427)" fill="currentColor" />
                        <rect x="7.72803" y="27.728" width="28" height="4" rx="2"
                            transform="rotate(-45 7.72803 27.728)" fill="currentColor" />
                        <rect x="10.5366" y="16.3945" width="16" height="4" rx="2"
                            transform="rotate(45 10.5366 16.3945)" fill="currentColor" />
                        <rect x="10.5562" y="-0.556152" width="28" height="4" rx="2"
                            transform="rotate(45 10.5562 -0.556152)" fill="currentColor" />
                    </svg>
                </div>
            </div>
            <!--logo End-->

            <h4 class="logo-title">{{ config('app.name') }}</h4>
        </a>
        <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
            <i class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.25 12.2744L19.25 12.2744" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M10.2998 18.2988L4.2498 12.2748L10.2998 6.24976" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </i>
        </div>
    </div>

    <div class="pt-0 sidebar-body data-scrollbar">
        <div class="sidebar-list">

            <ul class="navbar-nav iq-main-menu" id="sidebar-menu">

                {{-- DASHBOARD --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <i class="icon">
                            <i class="ri-dashboard-line"></i>
                        </i>
                        <span class="item-name">Dashboard</span>
                    </a>
                </li>

                {{-- MASTER DATA --}}
                <li>
                    <hr class="hr-horizontal">
                </li>

                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#">
                        <span class="default-icon">MASTER DATA</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('product-categories.*') ? 'active' : '' }}"
                        href="{{ route('product-categories.index') }}">
                        <i class="icon">
                            <i class="ri-folder-line"></i>
                        </i>
                        <span class="item-name">Product Category</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('brands.*') ? 'active' : '' }}"
                        href="{{ route('brands.index') }}">
                        <i class="icon">
                            <i class="ri-registered-line"></i>
                        </i>
                        <span class="item-name">Brand</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}"
                        href="{{ route('units.index') }}">
                        <i class="icon">
                            <i class="ri-custom-size"></i>
                        </i>
                        <span class="item-name">Unit</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
                        href="{{ route('products.index') }}">
                        <i class="icon">
                            <i class="ri-shopping-bag-line"></i>
                        </i>
                        <span class="item-name">Products</span>
                    </a>
                </li>

                {{-- INVENTORY --}}
                <li>
                    <hr class="hr-horizontal">
                </li>

                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#">
                        <span class="default-icon">INVENTORY</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="icon">
                            <i class="ri-archive-line"></i>
                        </i>
                        <span class="item-name">Stock Adjustment</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}"
                        href="{{ route('purchase-orders.index') }}">
                        <i class="icon">
                            <i class="ri-truck-line"></i>
                        </i>
                        <span class="item-name">Purchase Order</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('goods-receipts.*') ? 'active' : '' }}"
                        href="{{ route('goods-receipts.index') }}">
                        <i class="icon">
                            <i class="ri-inbox-archive-line"></i>
                        </i>
                        <span class="item-name">Goods Receipt</span>
                    </a>
                </li>

                {{-- SALES --}}
                <li>
                    <hr class="hr-horizontal">
                </li>

                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#">
                        <span class="default-icon">SALES</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="icon">
                            <i class="ri-shopping-cart-line"></i>
                        </i>
                        <span class="item-name">Sales</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="icon">
                            <i class="ri-file-list-3-line"></i>
                        </i>
                        <span class="item-name">Sales History</span>
                    </a>
                </li>

                {{-- CUSTOMER --}}
                <li>
                    <hr class="hr-horizontal">
                </li>

                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#">
                        <span class="default-icon">CUSTOMER</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}"
                        href="{{ route('customers.index') }}">
                        <i class="icon">
                            <i class="ri-user-3-line"></i>
                        </i>
                        <span class="item-name">Customers</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}"
                        href="{{ route('suppliers.index') }}">
                        <i class="icon">
                            <i class="ri-user-3-line"></i>
                        </i>
                        <span class="item-name">Suppliers</span>
                    </a>
                </li>

                {{-- REPORT --}}
                <li>
                    <hr class="hr-horizontal">
                </li>

                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#">
                        <span class="default-icon">REPORT</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="icon">
                            <i class="ri-bar-chart-box-line"></i>
                        </i>
                        <span class="item-name">Sales Report</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="icon">
                            <i class="ri-pie-chart-line"></i>
                        </i>
                        <span class="item-name">Profit Report</span>
                    </a>
                </li>

                {{-- SETTINGS --}}
                <li>
                    <hr class="hr-horizontal">
                </li>

                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#">
                        <span class="default-icon">SETTINGS</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('outlets.*') ? 'active' : '' }}"
                        href="{{ route('outlets.index') }}">
                        <i class="icon">
                            <i class="ri-store-2-line"></i>
                        </i>
                        <span class="item-name">Outlet</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                        href="{{ route('users.index') }}">
                        <i class="icon">
                            <i class="ri-user-settings-line"></i>
                        </i>
                        <span class="item-name">Users</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>

    <div class="sidebar-footer"></div>
</aside>
