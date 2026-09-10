<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ setting('company_name', config('app.name')) }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

    <style>
        :root {
            --brand: #2e7d32;
            --brand-dark: #1b5e20;
            --sidebar-width: 260px;
        }
        body { background: var(--bs-tertiary-bg); }
        .app-sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, var(--brand-dark), var(--brand));
            position: fixed;
            top: 0; left: 0; bottom: 0;
            overflow-y: auto;
            z-index: 1030;
            transition: transform .2s ease;
        }
        .app-sidebar .brand { color: #fff; font-weight: 700; padding: 1rem 1.25rem; }
        .app-sidebar .nav-link { color: rgba(255,255,255,.85); padding: .6rem 1.25rem; border-radius: 0; }
        .app-sidebar .nav-link.active, .app-sidebar .nav-link:hover { background: rgba(255,255,255,.12); color: #fff; }
        .app-sidebar .nav-link i { width: 20px; }
        .app-main { margin-left: var(--sidebar-width); min-height: 100vh; display: flex; flex-direction: column; }
        .app-topbar { position: sticky; top: 0; z-index: 1020; }
        @media (max-width: 991.98px) {
            .app-sidebar { transform: translateX(-100%); }
            .app-sidebar.show { transform: translateX(0); }
            .app-main { margin-left: 0; }
        }
        .stat-card { border-radius: .75rem; border: 0; }
    </style>
    @stack('styles')
</head>
<body>

<aside class="app-sidebar" id="appSidebar">
    <div class="brand d-flex align-items-center gap-2">
        <i class="bi bi-droplet-half fs-4"></i>
        <span>{{ setting('company_name', 'Milk Dairy ERP') }}</span>
    </div>
    <nav class="nav flex-column pb-4">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>

        @can('customers.view')
        <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
            <i class="bi bi-people me-2"></i> Customers
        </a>
        @endcan

        @can('daily-entries.view')
        <a class="nav-link {{ request()->routeIs('admin.daily-entries.*') ? 'active' : '' }}" href="{{ route('admin.daily-entries.index') }}">
            <i class="bi bi-journal-check me-2"></i> Daily Entries
        </a>
        @endcan

        @can('milk-rates.view')
        <a class="nav-link {{ request()->routeIs('admin.milk-rates.*') ? 'active' : '' }}" href="{{ route('admin.milk-rates.index') }}">
            <i class="bi bi-cash-coin me-2"></i> Milk Rates
        </a>
        @endcan

        @can('milk-purchases.view')
        <a class="nav-link {{ request()->routeIs('admin.milk-purchases.*') ? 'active' : '' }}" href="{{ route('admin.milk-purchases.index') }}">
            <i class="bi bi-box-arrow-in-down me-2"></i> Incoming Milk
        </a>
        @endcan

        @can('milk-stock.view')
        <a class="nav-link {{ request()->routeIs('admin.milk-stock.*') ? 'active' : '' }}" href="{{ route('admin.milk-stock.index') }}">
            <i class="bi bi-water me-2"></i> Milk Stock
        </a>
        @endcan

        @can('cash-sales.view')
        <a class="nav-link {{ request()->routeIs('admin.cash-sales.*') ? 'active' : '' }}" href="{{ route('admin.cash-sales.index') }}">
            <i class="bi bi-cash-stack me-2"></i> Cash Sales
        </a>
        @endcan

        @can('bills.view')
        <a class="nav-link {{ request()->routeIs('admin.bills.*') ? 'active' : '' }}" href="{{ route('admin.bills.index') }}">
            <i class="bi bi-receipt me-2"></i> Bills
        </a>
        @endcan

        @can('payments.view')
        <a class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" href="{{ route('admin.payments.index') }}">
            <i class="bi bi-credit-card me-2"></i> Payments
        </a>
        @endcan

        @can('expenses.view')
        <a class="nav-link {{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}" href="{{ route('admin.expenses.index') }}">
            <i class="bi bi-wallet2 me-2"></i> Expenses
        </a>
        @endcan

        @can('products.view')
        <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
            <i class="bi bi-box-seam me-2"></i> Products & Inventory
        </a>
        @endcan

        @can('employees.view')
        <a class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}" href="{{ route('admin.employees.index') }}">
            <i class="bi bi-person-badge me-2"></i> Employees
        </a>
        @endcan

        @can('routes.view')
        <a class="nav-link {{ request()->routeIs('admin.routes.*') ? 'active' : '' }}" href="{{ route('admin.routes.index') }}">
            <i class="bi bi-signpost-2 me-2"></i> Routes / Areas
        </a>
        @endcan

        @can('reports.view')
        <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
            <i class="bi bi-bar-chart-line me-2"></i> Reports
        </a>
        @endcan

        @can('users.view')
        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
            <i class="bi bi-person-gear me-2"></i> Users & Roles
        </a>
        @endcan

        @can('settings.view')
        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
            <i class="bi bi-gear me-2"></i> Settings
        </a>
        @endcan

        @can('activity-logs.view')
         <a class="nav-link {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}" href="{{ route('admin.audit.index') }}">
             <i class="bi bi-shield-check me-2"></i> Activity & Audit
         </a>
         @endcan
    </nav>
</aside>

<div class="app-main">
    <header class="app-topbar bg-body border-bottom">
        <div class="container-fluid d-flex align-items-center justify-content-between py-2">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <form class="d-none d-md-block" action="{{ route('admin.search') }}" method="GET">
                    <input type="search" name="q" class="form-control form-control-sm" style="width:280px"
                           placeholder="Search consumer ID, phone, name, invoice..." value="{{ request('q') }}">
                </form>
            </div>

            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary" id="themeToggle" title="Toggle theme">
                    <i class="bi bi-moon-stars"></i>
                </button>

                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary position-relative" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        @if(($unreadNotificationsCount ?? 0) > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $unreadNotificationsCount }}
                            </span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-2" style="width:320px">
                        <h6 class="dropdown-header">Notifications</h6>
                        @forelse(($recentNotifications ?? []) as $note)
                            <a class="dropdown-item small text-wrap" href="{{ $note->link ?? '#' }}">{{ $note->title }}</a>
                        @empty
                            <span class="dropdown-item small text-muted">No new notifications</span>
                        @endforelse
                    </div>
                </div>

                <div class="dropdown">
                    <button class="btn btn-sm btn-light d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-5"></i>
                        <span class="d-none d-sm-inline">{{ auth()->user()?->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text small text-muted">{{ auth()->user()?->getRoleNames()->join(', ') }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow-1 p-3 p-lg-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (isset($breadcrumbs))
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    @foreach ($breadcrumbs as $label => $url)
                        <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                            @if ($loop->last) {{ $label }} @else <a href="{{ $url }}">{{ $label }}</a> @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif

        @yield('content')
    </main>

    <footer class="text-center text-muted small py-3">
        &copy; {{ date('Y') }} {{ setting('company_name', config('app.name')) }}. All rights reserved.
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Sidebar toggle (mobile)
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.getElementById('appSidebar').classList.toggle('show');
    });

    // Dark / light mode toggle, persisted in localStorage
    const htmlEl = document.documentElement;
    const themeBtn = document.getElementById('themeToggle');
    const applyTheme = (theme) => {
        htmlEl.setAttribute('data-bs-theme', theme);
        themeBtn.innerHTML = theme === 'dark' ? '<i class="bi bi-sun"></i>' : '<i class="bi bi-moon-stars"></i>';
    };
    applyTheme(localStorage.getItem('theme') || 'light');
    themeBtn?.addEventListener('click', () => {
        const next = htmlEl.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        localStorage.setItem('theme', next);
        applyTheme(next);
    });

    // Global AJAX CSRF setup for jQuery
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    });
</script>
@stack('scripts')
</body>
</html>
