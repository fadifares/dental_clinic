<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة تحكم مدير المنصة (App Manager) | Dental SaaS')</title>

    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>⚡</text></svg>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- Chart.js for High-Performance Visual Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --app-bg: #090d16;
            --app-surface: #111827;
            --app-surface-border: #1f2937;
            --app-primary: #38bdf8;
            --app-primary-dark: #0284c7;
            --app-accent: #818cf8;
            --app-success: #34d399;
            --app-warning: #fbbf24;
            --app-danger: #f87171;
            --app-text: #f3f4f6;
            --app-muted: #9ca3af;
        }

        body {
            background-color: var(--app-bg);
            color: var(--app-text);
            font-family: 'IBM Plex Sans Arabic', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .saas-navbar {
            background-color: rgba(17, 24, 39, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--app-surface-border);
        }

        .saas-card {
            background-color: var(--app-surface);
            border: 1px solid var(--app-surface-border);
            border-radius: 1rem;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .saas-card:hover {
            border-color: rgba(56, 189, 248, 0.4);
        }

        .saas-badge-active {
            background: rgba(52, 211, 153, 0.15);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.3);
        }

        .saas-badge-suspended {
            background: rgba(248, 113, 113, 0.15);
            color: #f87171;
            border: 1px solid rgba(248, 113, 113, 0.3);
        }

        .table-dark-custom {
            --bs-table-bg: transparent;
            --bs-table-color: var(--app-text);
            --bs-table-border-color: var(--app-surface-border);
        }

        .table-dark-custom th {
            color: var(--app-muted);
            font-size: 0.825rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .form-control-dark, .form-select-dark {
            background-color: #0f172a;
            border: 1px solid #334155;
            color: #f8fafc;
            border-radius: 0.5rem;
        }

        .form-control-dark:focus, .form-select-dark:focus {
            background-color: #0f172a;
            border-color: var(--app-primary);
            color: #ffffff;
            box-shadow: 0 0 0 0.25rem rgba(56, 189, 248, 0.2);
        }

        .btn-saas-primary {
            background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);
            border: none;
            color: #ffffff;
            font-weight: 600;
            border-radius: 0.6rem;
        }

        .btn-saas-primary:hover {
            background: linear-gradient(135deg, #0369a1 0%, #0284c7 100%);
            color: #ffffff;
        }

        .modal-content-dark {
            background-color: #111827;
            border: 1px solid #374151;
            color: #f3f4f6;
            border-radius: 1rem;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- SaaS Navigation Topbar -->
    <nav class="navbar navbar-expand-lg saas-navbar sticky-top py-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2 text-white fw-bold" href="{{ route('appmanager.index') }}">
                <span class="rounded-3 bg-primary bg-gradient d-flex align-items-center justify-content-center text-white shadow-sm" style="width: 38px; height: 38px;">
                    <i class="bi bi-grid-1x2-fill"></i>
                </span>
                <div>
                    <span class="fs-5">Dental<span class="text-info">SaaS</span></span>
                    <span class="badge bg-info-subtle text-info border border-info-subtle ms-1" style="font-size: 0.65rem;">مدير التطبيق App Manager</span>
                </div>
            </a>

            <div class="d-flex align-items-center gap-2">
                <!-- Hosting Migrations Button -->
                <form action="{{ route('appmanager.migrate') }}" method="POST" class="d-inline" onsubmit="return confirm('هل تريد تشغيل ترحيل قاعدة البيانات (php artisan migrate) على الاستضافة الآن؟')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-3" title="ترقية قاعدة البيانات على الاستضافة">
                        <i class="bi bi-database-fill-gear me-1"></i> تشغيل الترحيل (DB Migrate)
                    </button>
                </form>

                <!-- Link to Clinic Dashboard -->
                <a href="{{ route('clinic.dashboard') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                    <i class="bi bi-hospital me-1"></i> العودة للعيادة
                </a>

                <!-- User profile / logout -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-dark border border-secondary rounded-pill px-3 dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-shield-lock-fill text-warning"></i>
                        <span>{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow">
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> تسجيل الخروج
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="flex-grow-1 py-4">
        <div class="container-fluid px-4">
            <!-- Flash Notifications -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert" style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4) !important;">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i> {!! session('success') !!}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert" style="background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.4) !important;">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i> {!! session('error') !!}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert" style="background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.4) !important;">
                    <ul class="mb-0 small">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-3 mt-auto border-top border-dark text-center text-muted small">
        Dental Pro SaaS Multi-Tenancy Enterprise &copy; {{ date('Y') }} • منصة إدارة العيادات الطبية السحابية
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

