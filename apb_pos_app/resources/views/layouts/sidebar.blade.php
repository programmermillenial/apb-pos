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

            @php
                $menuUser = auth()->user();
                $canSeeMaster = $menuUser && (
                    $menuUser->canAccessMenu('product-categories') ||
                    $menuUser->canAccessMenu('brands') ||
                    $menuUser->canAccessMenu('units') ||
                    $menuUser->canAccessMenu('products')
                );
                $canSeeInventory = $menuUser && (
                    $menuUser->canAccessMenu('stock-adjustments') ||
                    $menuUser->canAccessMenu('stock-opnames') ||
                    $menuUser->canAccessMenu('purchase-orders') ||
                    $menuUser->canAccessMenu('goods-receipts') ||
                    $menuUser->canAccessMenu('stock-transfers')
                );
                $canSeeSales = $menuUser && (
                    $menuUser->canAccessMenu('sales') ||
                    $menuUser->canAccessMenu('sales-history')
                );
                $canSeeCustomer = $menuUser && (
                    $menuUser->canAccessMenu('customers') ||
                    $menuUser->canAccessMenu('suppliers')
                );
                $canSeeReport = $menuUser && $menuUser->canAccessMenu('reports');
                $canSeeSettings = $menuUser && (
                    $menuUser->canAccessMenu('setting') ||
                    $menuUser->canAccessMenu('outlets') ||
                    $menuUser->canAccessMenu('users')
                );
            @endphp

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

                @if ($canSeeMaster)
                {{-- MASTER DATA --}}
                <li>
                    <hr class="hr-horizontal">
                </li>

                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#">
                        <span class="default-icon">MASTER DATA</span>
                    </a>
                </li>

                @if ($menuUser->canAccessMenu('product-categories'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('product-categories.*') ? 'active' : '' }}"
                        href="{{ route('product-categories.index') }}">
                        <i class="icon">
                            <i class="ri-folder-line"></i>
                        </i>
                        <span class="item-name">Product Category</span>
                    </a>
                </li>
                @endif

                @if ($menuUser->canAccessMenu('brands'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('brands.*') ? 'active' : '' }}"
                        href="{{ route('brands.index') }}">
                        <i class="icon">
                            <i class="ri-registered-line"></i>
                        </i>
                        <span class="item-name">Brand</span>
                    </a>
                </li>
                @endif

                @if ($menuUser->canAccessMenu('units'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}"
                        href="{{ route('units.index') }}">
                        <i class="icon">
                            <i class="ri-custom-size"></i>
                        </i>
                        <span class="item-name">Unit</span>
                    </a>
                </li>
                @endif

                @if ($menuUser->canAccessMenu('products'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
                        href="{{ route('products.index') }}">
                        <i class="icon">
                            <i class="ri-shopping-bag-line"></i>
                        </i>
                        <span class="item-name">Products</span>
                    </a>
                </li>
                @endif
                @endif

                @if ($canSeeInventory)
                {{-- INVENTORY --}}
                <li>
                    <hr class="hr-horizontal">
                </li>

                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#">
                        <span class="default-icon">INVENTORY</span>
                    </a>
                </li>

                @if ($menuUser->canAccessMenu('stock-adjustments'))
                <li class="nav-item">
                     <a class="nav-link {{ request()->routeIs('stock-adjustments.*') ? 'active' : '' }}"
                        href="{{ route('stock-adjustments.index') }}">
                        <i class="icon">
                            <i class="ri-archive-line"></i>
                        </i>
                        <span class="item-name">Stock Adjustment</span>
                    </a>
                </li>
                @endif

                @if ($menuUser->canAccessMenu('stock-opnames'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('stock-opnames.*') ? 'active' : '' }}"
                        href="{{ route('stock-opnames.index') }}">
                        <i class="icon">
                            <i class="ri-file-list-2-line"></i>
                        </i>
                        <span class="item-name">Stock Opname</span>
                    </a>
                </li>
                @endif

                @if ($menuUser->canAccessMenu('purchase-orders'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}"
                        href="{{ route('purchase-orders.index') }}">
                        <i class="icon">
                            <i class="ri-truck-line"></i>
                        </i>
                        <span class="item-name">Purchase Order</span>
                    </a>
                </li>
                @endif

                @if ($menuUser->canAccessMenu('goods-receipts'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('goods-receipts.*') ? 'active' : '' }}"
                        href="{{ route('goods-receipts.index') }}">
                        <i class="icon">
                            <i class="ri-inbox-archive-line"></i>
                        </i>
                        <span class="item-name">Goods Receipt</span>
                    </a>
                </li>
                @endif

                @if ($menuUser->canAccessMenu('stock-transfers'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('stock-transfers.*') ? 'active' : '' }}"
                        href="{{ route('stock-transfers.index') }}">
                        <i class="icon">
                            <i class="ri-file-transfer-line"></i>
                        </i>
                        <span class="item-name">Stock Transfer</span>
                    </a>
                </li>
                @endif
                @endif

                @if ($canSeeSales)
                {{-- SALES --}}
                <li>
                    <hr class="hr-horizontal">
                </li>

                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#">
                        <span class="default-icon">SALES</span>
                    </a>
                </li>

                @if ($menuUser->canAccessMenu('sales'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('sales.index') ? 'active' : '' }}"
                        href="{{ route('sales.index') }}">
                        <i class="icon">
                            <i class="ri-shopping-cart-line"></i>
                        </i>
                        <span class="item-name">Sales</span>
                    </a>
                </li>
                @endif

                @if ($menuUser->canAccessMenu('sales-history'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('sales.history', 'sales.show') ? 'active' : '' }}"
                        href="{{ route('sales.history') }}">
                        <i class="icon">
                            <i class="ri-file-list-3-line"></i>
                        </i>
                        <span class="item-name">Sales History</span>
                    </a>
                </li>
                @endif
                @endif

                @if ($canSeeCustomer)
                {{-- CUSTOMER --}}
                <li>
                    <hr class="hr-horizontal">
                </li>

                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#">
                        <span class="default-icon">CUSTOMER</span>
                    </a>
                </li>

                @if ($menuUser->canAccessMenu('customers'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}"
                        href="{{ route('customers.index') }}">
                        <i class="icon">
                            <i class="ri-user-3-line"></i>
                        </i>
                        <span class="item-name">Customers</span>
                    </a>
                </li>
                @endif

                @if ($menuUser->canAccessMenu('suppliers'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}"
                        href="{{ route('suppliers.index') }}">
                        <i class="icon">
                            <i class="ri-user-3-line"></i>
                        </i>
                        <span class="item-name">Suppliers</span>
                    </a>
                </li>
                @endif
                @endif

                @if ($canSeeReport)
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
                    <a class="nav-link {{ request()->routeIs('reports.sales*') ? 'active' : '' }}"
                        href="{{ route('reports.sales') }}">
                        <i class="icon">
                            <i class="ri-bar-chart-box-line"></i>
                        </i>
                        <span class="item-name">Sales Report</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.profit*') ? 'active' : '' }}"
                        href="{{ route('reports.profit') }}">
                        <i class="icon">
                            <i class="ri-pie-chart-line"></i>
                        </i>
                        <span class="item-name">Profit Report</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.stock') || request()->routeIs('reports.stock.csv') ? 'active' : '' }}"
                        href="{{ route('reports.stock') }}">
                        <i class="icon">
                            <i class="ri-archive-line"></i>
                        </i>
                        <span class="item-name">Stock Report</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.stock-movement*') ? 'active' : '' }}"
                        href="{{ route('reports.stock-movement') }}">
                        <i class="icon">
                            <i class="ri-arrow-left-right-line"></i>
                        </i>
                        <span class="item-name">Stock Movement</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.low-stock*') ? 'active' : '' }}"
                        href="{{ route('reports.low-stock') }}">
                        <i class="icon"><i class="ri-alarm-warning-line"></i></i>
                        <span class="item-name">Low Stock</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.slow-moving*') ? 'active' : '' }}"
                        href="{{ route('reports.slow-moving') }}">
                        <i class="icon"><i class="ri-timer-line"></i></i>
                        <span class="item-name">Slow Moving</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.fast-moving*') ? 'active' : '' }}"
                        href="{{ route('reports.fast-moving') }}">
                        <i class="icon"><i class="ri-rocket-line"></i></i>
                        <span class="item-name">Fast Moving</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.stock-aging*') ? 'active' : '' }}"
                        href="{{ route('reports.stock-aging') }}">
                        <i class="icon"><i class="ri-calendar-event-line"></i></i>
                        <span class="item-name">Stock Aging</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.stock-valuation*') ? 'active' : '' }}"
                        href="{{ route('reports.stock-valuation') }}">
                        <i class="icon"><i class="ri-money-dollar-circle-line"></i></i>
                        <span class="item-name">Stock Valuation</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.stock-discrepancy*') ? 'active' : '' }}"
                        href="{{ route('reports.stock-discrepancy') }}">
                        <i class="icon"><i class="ri-scales-3-line"></i></i>
                        <span class="item-name">Stock Discrepancy</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.transfer-pending*') ? 'active' : '' }}"
                        href="{{ route('reports.transfer-pending') }}">
                        <i class="icon"><i class="ri-truck-line"></i></i>
                        <span class="item-name">Transfer Pending</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.stock-card*') ? 'active' : '' }}"
                        href="{{ route('reports.stock-card') }}">
                        <i class="icon"><i class="ri-file-list-3-line"></i></i>
                        <span class="item-name">Stock Card</span>
                    </a>
                </li>
                @endif

                @if ($canSeeSettings)
                {{-- SETTINGS --}}
                <li>
                    <hr class="hr-horizontal">
                </li>

                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#">
                        <span class="default-icon">SETTINGS</span>
                    </a>
                </li>

                @if ($menuUser->canAccessMenu('setting'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('setting.*') ? 'active' : '' }}"
                        href="{{ route('setting.index') }}">
                        <i class="icon">
                            <i class="ri-settings-3-line"></i>
                        </i>
                        <span class="item-name">Store Setting</span>
                    </a>
                </li>
                @endif

                @if ($menuUser->canAccessMenu('outlets'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('outlets.*') ? 'active' : '' }}"
                        href="{{ route('outlets.index') }}">
                        <i class="icon">
                            <i class="ri-store-2-line"></i>
                        </i>
                        <span class="item-name">Outlet</span>
                    </a>
                </li>
                @endif

                @if ($menuUser->canAccessMenu('users'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                        href="{{ route('users.index') }}">
                        <i class="icon">
                            <i class="ri-user-settings-line"></i>
                        </i>
                        <span class="item-name">Users</span>
                    </a>
                </li>
                @endif
                @endif

            </ul>
        </div>
    </div>

    <div class="sidebar-footer"></div>
</aside>
