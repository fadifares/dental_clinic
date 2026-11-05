<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة تحكم عيادة الأسنان | Dental Pro ERP')</title>

    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🦷</text></svg>">
    
    <!-- Core Vendor Scripts (Synchronous for inline Blade scripts) -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <!-- Vite Assets (Bootstrap 5, SCSS, jQuery) -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        .clinic-sidebar {
            width: 275px;
            height: 100vh;
            height: 100dvh;
            max-height: 100dvh;
            background: #0f172a;
            color: #94a3b8;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1055;
            position: sticky;
            top: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden !important;
        }
        @media (max-width: 991.98px) {
            .clinic-sidebar {
                position: fixed !important;
                top: 0;
                bottom: 0;
                right: 0;
                width: 285px;
                max-width: 85vw;
                height: 100vh;
                height: 100dvh;
                max-height: 100dvh;
                transform: translateX(100%);
                box-shadow: -5px 0 30px rgba(0,0,0,0.5);
            }
            .clinic-sidebar.show {
                transform: translateX(0);
            }
            .sidebar-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(15, 23, 42, 0.65);
                backdrop-filter: blur(3px);
                z-index: 1050;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.3s ease;
            }
            .sidebar-backdrop.show {
                opacity: 1;
                pointer-events: auto;
            }
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
        .sidebar-menu-scroll {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
        }
        .sidebar-footer {
            flex-shrink: 0;
            background: #0b1329;
            z-index: 10;
        }
        .clinic-content-wrapper {
            flex: 1;
            min-width: 0;
            width: 100%;
            min-height: 100vh;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        .clinic-topbar {
            min-height: 65px;
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
        body.overflow-hidden-mobile {
            overflow: hidden !important;
        }
        @media print {
            .clinic-sidebar,
            .clinic-topbar,
            .sidebar-backdrop,
            .d-print-none,
            #sidebarBackdrop {
                display: none !important;
            }
            .clinic-content-wrapper {
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                width: 100% !important;
                min-height: auto !important;
                overflow: visible !important;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
        }

        /* Dark Mode Theme Styles */
        body.dark-mode {
            background-color: #0b1120 !important;
            color: #cbd5e1 !important;
        }
        body.dark-mode .clinic-content-wrapper {
            background-color: #0b1120 !important;
        }
        body.dark-mode .clinic-topbar {
            background-color: #0f172a !important;
            border-color: #1e293b !important;
        }
        body.dark-mode .clinic-card,
        body.dark-mode .card,
        body.dark-mode .modal-content,
        body.dark-mode .dropdown-menu {
            background-color: #0f172a !important;
            border-color: #1e293b !important;
            color: #cbd5e1 !important;
        }
        body.dark-mode .text-dark,
        body.dark-mode h1,
        body.dark-mode h2,
        body.dark-mode h3,
        body.dark-mode h4,
        body.dark-mode h5,
        body.dark-mode h6 {
            color: #f1f5f9 !important;
        }
        body.dark-mode .text-muted {
            color: #94a3b8 !important;
        }
        body.dark-mode .bg-light,
        body.dark-mode .table-light {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
        }
        body.dark-mode .table {
            color: #cbd5e1 !important;
            border-color: #334155 !important;
        }
        body.dark-mode .table td,
        body.dark-mode .table th {
            border-color: #1e293b !important;
            color: #cbd5e1 !important;
        }
        body.dark-mode .form-control,
        body.dark-mode .form-select {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }
        body.dark-mode .btn-light {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }
        body.dark-mode .border,
        body.dark-mode .border-bottom,
        body.dark-mode .border-top {
            border-color: #1e293b !important;
        }

        /* RTL Modal & Alert Close Button Positioning */
        [dir="rtl"] .modal-header,
        html[dir="rtl"] .modal-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
        }
        [dir="rtl"] .modal-header .btn-close,
        html[dir="rtl"] .modal-header .btn-close {
            margin-right: auto !important;
            margin-left: 0 !important;
            float: left;
        }
        [dir="rtl"] .alert-dismissible .btn-close,
        html[dir="rtl"] .alert-dismissible .btn-close {
            right: auto !important;
            left: 0 !important;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-light">

<!-- Mobile Sidebar Backdrop -->
<div class="sidebar-backdrop d-lg-none d-print-none" id="sidebarBackdrop"></div>

<div class="d-flex w-100 position-relative">
    <!-- Sidebar -->
    <aside class="clinic-sidebar shadow d-print-none">
        <!-- Logo & Mobile Close Button -->
        <div class="p-3 border-bottom border-secondary d-flex align-items-center justify-content-between flex-shrink-0">
            <a href="{{ route('clinic.dashboard') }}" class="d-flex align-items-center gap-2 text-white text-decoration-none">
                <span class="clinic-stat-icon bg-primary text-white" style="width: 42px; height: 42px;">
                    <i class="bi bi-heart-pulse-fill"></i>
                </span>
                <div>
                    <span class="fs-5 fw-bold d-block text-white">دنتال<span class="text-info">برو</span></span>
                    <span class="small text-secondary" style="font-size: 0.75rem;">نظام إدارة العيادات الشامل</span>
                </div>
            </a>
            <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-circle d-lg-none p-1" id="closeSidebar" aria-label="إغلاق القائمة">
                <i class="bi bi-x-lg fs-6"></i>
            </button>
        </div>

        <!-- Scrollable Navigation Links -->
        <div class="sidebar-menu-scroll">
            <ul class="nav nav-pills flex-column py-2">
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
                <li class="nav-item">
                    <a href="{{ route('clinic.expenses.index') }}" class="nav-link {{ request()->routeIs('clinic.expenses.*') ? 'active' : '' }}">
                        <i class="bi bi-wallet2 fs-5 text-warning"></i>
                        <span>المصاريف والنفقات</span>
                    </a>
                </li>
                @endif

                <li class="nav-item">
                    <a href="{{ route('clinic.analytics') }}" class="nav-link {{ request()->routeIs('clinic.analytics') ? 'active' : '' }}">
                        <i class="bi bi-graph-up-arrow fs-5 text-success"></i>
                        <span>الإحصائيات والتحليلات</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('clinic.profile') }}" class="nav-link {{ request()->routeIs('clinic.profile') ? 'active' : '' }}">
                        <i class="bi bi-person-gear fs-5 text-primary"></i>
                        <span>الملف الشخصي والأمان</span>
                    </a>
                </li>

                @if(auth()->user()->isAdmin())
                <li class="nav-item">
                    <a href="{{ route('clinic.users.index') }}" class="nav-link {{ request()->routeIs('clinic.users.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock-fill fs-5 text-warning"></i>
                        <span class="fw-bold">المستخدمين والأمان (RBAC)</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('clinic.settings.index') }}" class="nav-link {{ request()->routeIs('clinic.settings.*') ? 'active' : '' }}">
                        <i class="bi bi-gear-fill fs-5 text-info"></i>
                        <span>إعدادات النظام والعيادة</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>

        <!-- Permanently Visible Bottom User Profile & Logout Section -->
        <div class="sidebar-footer p-3 border-top border-secondary">
            <a href="{{ route('clinic.profile') }}" class="d-flex align-items-center gap-2 text-white mb-2 text-decoration-none p-1 rounded" title="تعديل الملف الشخصي وكلمة المرور">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 38px; height: 38px;">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div class="overflow-hidden flex-grow-1">
                    <div class="fw-bold small text-truncate text-white d-flex align-items-center justify-content-between">
                        <span>{{ auth()->user()->name }}</span>
                        <i class="bi bi-gear-fill text-white-50" style="font-size: 0.75rem;"></i>
                    </div>
                    <div class="badge bg-secondary-subtle text-light px-1 text-truncate" style="font-size: 0.7rem; max-width: 150px;" title="{{ auth()->user()->getRoleLabelsString() }}">
                        {{ auth()->user()->getRoleLabelsString() }}
                    </div>
                </div>
            </a>

            <div class="d-flex gap-2">
                <a href="{{ route('clinic.profile') }}" class="btn btn-outline-info btn-sm flex-fill rounded-pill text-truncate" title="الملف الشخصي وكلمة المرور">
                    <i class="bi bi-person-gear me-1"></i> حسابي
                </a>
                <form action="{{ route('logout') }}" method="POST" class="flex-fill mb-0">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm w-100 rounded-pill shadow-sm fw-bold">
                        <i class="bi bi-box-arrow-right me-1"></i> خروج
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="clinic-content-wrapper">
        <!-- Topbar -->
        <header class="clinic-topbar px-3 px-md-4 py-2 d-flex align-items-center justify-content-between sticky-top d-print-none">
            <div class="d-flex align-items-center gap-2 gap-md-3 flex-grow-1 flex-md-grow-0" style="max-width: 420px;">
                <button class="btn btn-light border d-lg-none shadow-sm px-2 py-1" id="toggleSidebar" aria-label="فتح القائمة الجانبية">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <!-- Live Patient Search Box -->
                <div class="position-relative flex-grow-1">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="topbarPatientSearch" class="form-control bg-light border-0 small" placeholder="بحث عن مريض..." autocomplete="off">
                    </div>
                    <div id="searchResultsDropdown" class="d-none p-2 shadow">
                        <div id="searchResultsList" class="list-group list-group-flush small"></div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-1 gap-sm-2 ms-2">
                <button class="btn btn-outline-primary btn-sm rounded-pill px-2 px-md-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#newPatientGlobalModal" title="فتح ملف مريض جديد">
                    <i class="bi bi-person-plus-fill"></i>
                    <span class="d-none d-sm-inline ms-1">مريض جديد</span>
                </button>
                <a href="{{ route('clinic.appointments.index') }}" class="btn btn-primary btn-sm rounded-pill px-2 px-md-3 shadow-sm" title="المواعيد والاستقبال">
                    <i class="bi bi-calendar-plus"></i>
                    <span class="d-none d-sm-inline ms-1">المواعيد</span>
                </a>
                

                <!-- Dark / Light Mode Toggle Button -->
                <button class="btn btn-light rounded-circle p-2 shadow-sm border ms-1 d-flex align-items-center justify-content-center" id="themeToggleBtn" type="button" title="تبديل الوضع الليلي والنهاري" aria-label="الوضع الليلي والنهاري" style="width: 38px; height: 38px;">
                    <i class="bi bi-moon-stars-fill fs-5" id="themeToggleIcon"></i>
                </button>

                <!-- Direct Topbar User Profile & Logout Dropdown -->
                <div class="dropdown ms-1">
                    <button class="btn btn-light rounded-circle p-1 shadow-sm border d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" type="button" data-bs-toggle="dropdown" aria-label="حساب المستخدم">
                        <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 30px; height: 30px; font-size: 0.85rem;">
                            {{ mb_substr(auth()->user()->name, 0, 1) }}
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-start shadow border-0 p-2" style="min-width: 220px;">
                        <li class="px-2 py-1 border-bottom mb-2">
                            <span class="fw-bold d-block text-dark small text-truncate">{{ auth()->user()->name }}</span>
                            <span class="badge bg-secondary-subtle text-dark" style="font-size: 0.7rem;">{{ auth()->user()->getRoleLabelsString() }}</span>
                        </li>
                        <li>
                            <a class="dropdown-item small py-2 rounded {{ request()->routeIs('clinic.profile') ? 'active bg-primary text-white' : '' }}" href="{{ route('clinic.profile') }}">
                                <i class="bi bi-person-gear me-2 text-primary"></i> الملف الشخصي والأمان
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item small py-2 rounded" href="{{ url('/') }}" target="_blank">
                                <i class="bi bi-globe me-2 text-info"></i> زيارة الموقع الخارجي
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="mb-0">
                                @csrf
                                <button type="submit" class="dropdown-item small py-2 rounded text-danger fw-bold">
                                    <i class="bi bi-box-arrow-right me-2"></i> تسجيل الخروج
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('whatsapp_url'))
        <div class="m-4 mb-0 p-4 rounded-4 shadow-sm border" style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border-color: #86efac !important;">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <span class="badge bg-success rounded-pill px-3 py-1"><i class="bi bi-check-circle-fill me-1"></i> تم تأكيد الحجز</span>
                        @if(session('email_sent'))
                            <span class="badge bg-info-subtle text-info border rounded-pill px-3 py-1">
                                <i class="bi bi-calendar2-check-fill me-1"></i> تم إرسال بطاقة التقويم (.ics) وتنبيه 24 ساعة للبريد
                            </span>
                        @else
                            <span class="badge bg-light text-muted border rounded-pill px-3 py-1">
                                <i class="bi bi-info-circle me-1"></i> لم يُسجل بريد للمريض
                            </span>
                        @endif
                    </div>
                    <h5 class="fw-bold text-dark mb-1">
                        موعد المريض: {{ session('whatsapp_patient_name') }} 
                        <span class="text-muted fs-6 fw-normal">({{ session('appointment_date') }} الساعة {{ session('appointment_time') }})</span>
                    </h5>
                    <p class="text-muted small mb-0">
                        @if(session('email_sent'))
                            تم حفظ الجلسة وإرسال بطاقة iCalendar إلى بريد المريض لتثبيتها في تقويم هاتفه. يمكنك إرسال تفاصيل الموعد إلى واتساب المريض بنقرة واحدة:
                        @else
                            تم حفظ الجلسة بنجاح. يمكنك إرسال تفاصيل الموعد وتأكيده مباشرة إلى واتساب المريض بنقرة واحدة:
                        @endif
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <a href="{{ session('whatsapp_url') }}" target="_blank" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="bi bi-whatsapp fs-5"></i>
                        <span>إرسال عبر واتساب ({{ session('whatsapp_patient_phone') }})</span>
                    </a>
                </div>
            </div>
        </div>
        @elseif(session('success'))
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
    <div class="modal-dialog modal-dialog-centered modal-lg">
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
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">اسم المريض الثلاثي <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="مثال: خالد ناصر المطيري" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">رقم الجوال <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" placeholder="05XXXXXXXX" required dir="ltr">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">البريد الإلكتروني (لتذكيرات المواعيد وبطاقة التقويم)</label>
                            <input type="email" name="email" class="form-control" placeholder="patient@example.com" dir="ltr">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">رقم الهوية / الإقامة</label>
                            <input type="text" name="national_id" class="form-control" placeholder="10XXXXXXXX">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">الجنس <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <option value="male">ذكر</option>
                                <option value="female">أنثى</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">تاريخ الميلاد</label>
                            <input type="date" name="date_of_birth" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold text-danger">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> الحساسيات الدوائية (إن وجدت)
                            </label>
                            <input type="text" name="allergies" class="form-control" placeholder="مثال: حساسية بنسلين، حساسية لاتكس">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">
                                <i class="bi bi-heart-pulse-fill text-primary me-1"></i> الأمراض المزمنة
                            </label>
                            <input type="text" name="chronic_diseases" class="form-control" placeholder="مثال: سكري، ضغط، سيولة دم">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="bi bi-check-lg me-1"></i> حفظ وفتح الملف الطبي
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('whatsapp_url'))
<!-- MODAL: Post-Appointment Confirmation & WhatsApp Trigger -->
<div class="modal fade" id="appointmentSuccessModal" tabindex="-1" aria-labelledby="appointmentSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg text-center p-4">
            <div class="mb-3">
                <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center" style="width: 72px; height: 72px;">
                    <i class="bi bi-calendar2-check-fill fs-1"></i>
                </div>
            </div>
            <h4 class="fw-bold text-dark mb-1">تم تأكيد حجز الموعد بنجاح!</h4>
            <p class="text-muted small mb-3">
                المريض: <strong class="text-dark">{{ session('whatsapp_patient_name') }}</strong><br>
                <span>📅 {{ session('appointment_date') }} &bull; ⏰ {{ session('appointment_time') }}</span>
                @if(session('doctor_name'))
                    <br><span class="text-secondary">👨‍⚕️ د. {{ session('doctor_name') }} ({{ session('service_type') }})</span>
                @endif
            </p>

            @if(session('email_sent'))
            <div class="alert alert-info-subtle border border-info-subtle text-start small py-2 px-3 mb-3 rounded-3">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-envelope-check-fill text-info fs-5 mt-1"></i>
                    <div>
                        <strong class="text-info-emphasis">تم إرسال بطاقة التقويم (.ics):</strong>
                        <div class="text-muted" style="font-size: 0.8rem;">
                            أُرسلت دعوة التقويم التفاعلية لبريد المريض (<span class="font-monospace text-dark">{{ session('patient_email') }}</span>) متضمنة تنبيهاً تذكيرياً تلقائياً قبل الموعد بـ 24 ساعة.
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="d-grid gap-2">
                <a href="{{ session('whatsapp_url') }}" target="_blank" class="btn btn-success btn-lg rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-whatsapp fs-4"></i>
                    <span>إرسال تفاصيل الموعد عبر واتساب</span>
                </a>
                <button type="button" class="btn btn-light rounded-pill text-muted small py-2" data-bs-dismiss="modal">
                    إغلاق ومتابعة
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    // Auto-show Post Appointment Confirmation Modal
    @if(session('whatsapp_url'))
    document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('appointmentSuccessModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });
    @endif

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

        // Responsive Mobile Sidebar Toggle & Backdrop
        function openSidebar() {
            $('.clinic-sidebar').addClass('show');
            $('#sidebarBackdrop').addClass('show');
            $('body').addClass('overflow-hidden-mobile');
        }

        function closeSidebar() {
            $('.clinic-sidebar').removeClass('show');
            $('#sidebarBackdrop').removeClass('show');
            $('body').removeClass('overflow-hidden-mobile');
        }

        $('#toggleSidebar').on('click', function (e) {
            e.stopPropagation();
            if ($('.clinic-sidebar').hasClass('show')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        $('#closeSidebar, #sidebarBackdrop').on('click', function () {
            closeSidebar();
        });

        // Auto close on small screens when a navigation link is clicked
        $('.clinic-sidebar .nav-link').on('click', function () {
            if ($(window).width() < 992) {
                closeSidebar();
            }
        });

        // Close on ESC key
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $('.clinic-sidebar').hasClass('show')) {
                closeSidebar();
            }
        });

        // Theme Toggle (Dark / Light mode)
        const savedTheme = localStorage.getItem('dental_theme') || 'light';
        if (savedTheme === 'dark') {
            $('body').addClass('dark-mode');
            $('#themeToggleIcon').removeClass('bi-moon-stars-fill').addClass('bi-sun-fill text-warning');
        }

        $('#themeToggleBtn').on('click', function () {
            $('body').toggleClass('dark-mode');
            const isDark = $('body').hasClass('dark-mode');
            localStorage.setItem('dental_theme', isDark ? 'dark' : 'light');
            if (isDark) {
                $('#themeToggleIcon').removeClass('bi-moon-stars-fill').addClass('bi-sun-fill text-warning');
            } else {
                $('#themeToggleIcon').removeClass('bi-sun-fill text-warning').addClass('bi-moon-stars-fill');
            }
        });
    });
</script>

@stack('scripts')
</body>
</html>
