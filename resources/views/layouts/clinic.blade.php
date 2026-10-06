<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة تحكم عيادة الأسنان | Dental Pro ERP')</title>

    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🦷</text></svg>">
    
    <!-- Vite Assets (Bootstrap 5, SCSS, jQuery) -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        .clinic-sidebar {
            width: 270px;
            min-height: 100vh;
            background: #0f172a;
            color: #94a3b8;
            transition: all 0.3s;
            z-index: 1000;
        }
        .clinic-sidebar .nav-link {
            color: #94a3b8;
            padding: 0.75rem 1rem;
            border-radius: 0.65rem;
            margin: 0.2rem 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.925rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .clinic-sidebar .nav-link:hover,
        .clinic-sidebar .nav-link.active {
            color: #ffffff;
            background: rgba(2, 132, 199, 0.2);
            border-right: 4px solid #0284c7;
        }
        .clinic-sidebar .nav-link.active {
            background: #0284c7;
            border-right: none;
        }
        .clinic-content-wrapper {
            flex: 1;
            min-height: 100vh;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
        }
        .clinic-topbar {
            height: 70px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }
        #searchResultsDropdown {
            position: absolute;
            top: 100%;
            right: 0;
            left: 0;
            z-index: 1050;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-height: 320px;
            overflow-y: auto;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-light">

<div class="d-flex">
    <!-- Sidebar -->
    <aside class="clinic-sidebar d-flex flex-column flex-shrink-0 shadow">
        <!-- Logo -->
        <div class="p-3 border-bottom border-secondary d-flex align-items-center justify-content-between">
            <a href="{{ route('clinic.dashboard') }}" class="d-flex align-items-center gap-2 text-white text-decoration-none">
                <span class="clinic-stat-icon bg-primary text-white" style="width: 42px; height: 42px;">
                    <i class="bi bi-heart-pulse-fill"></i>
                </span>
                <div>
                    <span class="fs-5 fw-bold d-block text-white">دنتال<span class="text-info">برو</span></span>
                    <span class="small text-secondary" style="font-size: 0.75rem;">نظام إدارة العيادات الشامل</span>
                </div>
            </a>
        </div>

        <!-- Navigation Links -->
        <ul class="nav nav-pills flex-column mb-auto py-3">
            <li class="nav-item">
                <a href="{{ route('clinic.dashboard') }}" class="nav-link {{ request()->routeIs('clinic.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 fs-5"></i>
                    <span>لوحة القيادة الرئيسية</span>
                </a>
            </li>

            @if(auth()->user()->isAdmin() || auth()->user()->isReceptionist() || auth()->user()->isDoctor())
            <li class="nav-item">
                <a href="{{ route('clinic.appointments.index') }}" class="nav-link {{ request()->routeIs('clinic.appointments.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar2-week fs-5"></i>
                    <span>المواعيد والاستقبال</span>
                </a>
            </li>
            @endif

            <li class="nav-item">
                <a href="{{ route('clinic.patients.index') }}" class="nav-link {{ request()->routeIs('clinic.patients.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill fs-5"></i>
                    <span>سجل المرضى (EMR)</span>
                </a>
            </li>

            @if(auth()->user()->isAdmin() || auth()->user()->isDoctor())
            <li class="nav-item">
                <a href="{{ route('clinic.dashboard') }}#odontogram-section" class="nav-link">
                    <i class="bi bi-grid-3x3-gap-fill fs-5 text-warning"></i>
                    <span class="fw-bold text-white">مخطط الأسنان (Odontogram)</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('clinic.labs.index') }}" class="nav-link {{ request()->routeIs('clinic.labs.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam fs-5"></i>
                    <span>طلبيات المعامل والتركيبات</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isAccountant())
            <li class="nav-item">
                <a href="{{ route('clinic.billing.index') }}" class="nav-link {{ request()->routeIs('clinic.billing.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt-cutoff fs-5"></i>
                    <span>الفواتير والأقساط</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->isAdmin())
            <li class="nav-item">
                <a href="{{ route('clinic.users.index') }}" class="nav-link {{ request()->routeIs('clinic.users.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock-fill fs-5 text-warning"></i>
                    <span class="fw-bold">المستخدمين والأمان (RBAC)</span>
                </a>
            </li>
            @endif
        </ul>

        <!-- Bottom Link to Public Web & User Profile -->
        <div class="p-3 border-top border-secondary">
            <div class="d-flex align-items-center gap-2 text-white mb-2">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px;">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div class="overflow-hidden">
                    <div class="fw-bold small text-truncate text-white">{{ auth()->user()->name }}</div>
                    <div class="badge bg-secondary-subtle text-light px-1" style="font-size: 0.7rem;">{{ auth()->user()->role->label() }}</div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-info btn-sm flex-fill rounded-pill">
                    <i class="bi bi-globe me-1"></i> الموقع
                </a>
                <form action="{{ route('logout') }}" method="POST" class="flex-fill">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill">
                        <i class="bi bi-box-arrow-right me-1"></i> خروج
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="clinic-content-wrapper">
        <!-- Topbar -->
        <header class="clinic-topbar px-4 d-flex align-items-center justify-content-between sticky-top">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-md-none" id="toggleSidebar">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <!-- Live Patient Search Box -->
                <div class="position-relative" style="width: 340px;">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="topbarPatientSearch" class="form-control bg-light border-0" placeholder="بحث سريع عن مريض بالاسم أو الجوال..." autocomplete="off">
                    </div>
                    <div id="searchResultsDropdown" class="d-none p-2 shadow">
                        <div id="searchResultsList" class="list-group list-group-flush small"></div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#newPatientGlobalModal">
                    <i class="bi bi-person-plus-fill me-1"></i> مريض جديد
                </button>
                <a href="{{ route('clinic.appointments.index') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                    <i class="bi bi-calendar-plus me-1"></i> المواعيد
                </a>
                
                <div class="dropdown ms-2">
                    <button class="btn btn-light position-relative rounded-circle p-2" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                            3
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-start shadow border-0 p-2" style="width: 280px;">
                        <li><h6 class="dropdown-header">تنبيهات العيادة</h6></li>
                        <li><a class="dropdown-item small py-2 rounded" href="{{ route('clinic.dashboard') }}">🦷 مريض في الانتظار (فهد العتيبي)</a></li>
                        <li><a class="dropdown-item small py-2 rounded" href="{{ route('clinic.dashboard') }}">📦 استلام تركيبة زيركون من معمل النخبة</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-4 mb-0 rounded-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show m-4 mb-0 rounded-4 shadow-sm" role="alert">
            <i class="bi bi-exclamation-octagon-fill me-2"></i> يرجى التأكد من البيانات المدخلة:
            <ul class="mb-0 mt-1 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Body Content -->
        <main class="p-4 flex-grow-1">
            @yield('content')
        </main>
    </div>
</div>

<!-- GLOBAL MODAL: Quick Add New Patient -->
<div class="modal fade" id="newPatientGlobalModal" tabindex="-1" aria-labelledby="newPatientGlobalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-person-plus-fill text-primary me-2"></i> فتح ملف مريض جديد
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('clinic.patients.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">اسم المريض الثلاثي <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="مثال: خالد ناصر المطيري" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">رقم الجوال <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" placeholder="05XXXXXXXX" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">رقم الهوية / الإقامة</label>
                            <input type="text" name="national_id" class="form-control" placeholder="10XXXXXXXX">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">الجنس</label>
                            <select name="gender" class="form-select">
                                <option value="male">ذكر</option>
                                <option value="female">أنثى</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">تاريخ الميلاد</label>
                            <input type="date" name="date_of_birth" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-danger">
                            <i class="bi bi-exclamation-triangle-fill"></i> الحساسيات الدوائية (إن وجدت)
                        </label>
                        <input type="text" name="allergies" class="form-control" placeholder="مثال: حساسية بنسلين، حساسية لاتكس">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">الأمراض المزمنة</label>
                        <input type="text" name="chronic_diseases" class="form-control" placeholder="مثال: سكري، ضغط، سيولة دم">
                    </div>
                </div>
                <div class="modal-footer border-top bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> حفظ وفتح الملف الطبي
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Live Search in Topbar via jQuery
    $(function () {
        let searchTimeout;
        $('#topbarPatientSearch').on('input', function () {
            clearTimeout(searchTimeout);
            const query = $(this).val().trim();
            const dropdown = $('#searchResultsDropdown');
            const list = $('#searchResultsList');

            if (query.length < 2) {
                dropdown.addClass('d-none');
                return;
            }

            searchTimeout = setTimeout(function () {
                $.ajax({
                    url: "{{ route('clinic.patients.search') }}",
                    data: { q: query },
                    success: function (data) {
                        list.empty();
                        if (data.length === 0) {
                            list.append('<div class="p-3 text-muted text-center">لا توجد نتائج مطابقة</div>');
                        } else {
                            data.forEach(function (pt) {
                                const item = `
                                    <a href="/clinic/patients/${pt.id}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2">
                                        <div>
                                            <span class="fw-bold text-dark">${pt.name}</span>
                                            <span class="d-block text-muted small" style="font-size:0.75rem;">${pt.phone}</span>
                                        </div>
                                        <span class="badge bg-light text-primary border">#${pt.file_number}</span>
                                    </a>
                                `;
                                list.append(item);
                            });
                        }
                        dropdown.removeClass('d-none');
                    }
                });
            }, 250);
        });

        // Hide search dropdown on click outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('#topbarPatientSearch, #searchResultsDropdown').length) {
                $('#searchResultsDropdown').addClass('d-none');
            }
        });
    });
</script>

@stack('scripts')
</body>
</html>
