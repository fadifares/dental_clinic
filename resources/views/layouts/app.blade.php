<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dental Pro ERP | المنظومة السحابية الذكية لإدارة عيادات ومراكز طب الأسنان')</title>
    <meta name="description" content="منظومة Dental Pro ERP السحابية المتكاملة لطب وجراحة الأسنان: السجل الطبي الرقمي EMR، مخطط الأسنان التفاعلي 32 سن، الفوترة الإلكترونية ZATCA، وإدارة المواعيد والمعامل.">

    <!-- Google Fonts & Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🦷</text></svg>">
    
    <!-- Core Vendor Scripts (Synchronous for inline Blade scripts) -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <!-- Vite Assets (Bootstrap 5, SCSS & jQuery) -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100 bg-white text-dark">

    <!-- Top Announcement Bar -->
    <div class="bg-gradient text-white py-2 small d-none d-md-block" style="background: linear-gradient(90deg, #0b192c 0%, #0369a1 50%, #0d9488 100%);">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark px-2 py-1 fw-bold rounded-pill">جديد V4.2</span>
                <span class="fw-medium">🚀 مخطط الأسنان الرقمي التفاعلي 32 سن + الربط المعتمد مع هيئة الزكاة والضريبة (ZATCA المرحلة 2) متاح الآن!</span>
            </div>
            <div class="d-flex gap-3 align-items-center">
                <a href="https://wa.me/966500000000?text=مرحباً، أرغب بالاستفسار عن برنامج Dental Pro ERP لعيادات الأسنان" target="_blank" class="text-white text-decoration-none small">
                    <i class="bi bi-whatsapp text-success me-1"></i> استشارة المبيعات: +966500000000
                </a>
                <span class="text-white-50">|</span>
                @auth
                    <a href="{{ route('clinic.dashboard') }}" class="badge bg-light text-primary px-3 py-1 text-decoration-none rounded-pill fw-bold">
                        <i class="bi bi-speedometer2 me-1"></i> لوحة التحكم ({{ auth()->user()->name }})
                    </a>
                @else
                    <a href="{{ route('login') }}" class="badge bg-light text-primary px-3 py-1 text-decoration-none rounded-pill fw-bold">
                        <i class="bi bi-box-arrow-in-left me-1"></i> تسجيل الدخول للكادر
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg clinic-glass-nav sticky-top py-3" style="box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06);">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold fs-4 text-dark" href="{{ url('/') }}">
                <span class="d-inline-flex align-items-center justify-content-center rounded-3 shadow-sm text-white" style="width: 44px; height: 44px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                    <i class="bi bi-heart-pulse-fill fs-5"></i>
                </span>
                <div>
                    <span class="fw-extrabold text-dark tracking-tight">Dental<span class="text-primary">Pro</span> <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6 py-0 px-2 rounded-pill align-middle">ERP</span></span>
                    <span class="d-block text-muted fw-normal" style="font-size: 0.72rem; letter-spacing: -0.2px;">المنظومة السحابية الذكية لعيادات الأسنان</span>
                </div>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarClinic" aria-controls="navbarClinic" aria-expanded="false" aria-label="تبديل القائمة">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarClinic">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link active text-primary" href="{{ url('/') }}">الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">مميزات البرنامج</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#odontogram-demo">المخطط التفاعلي</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#screens">شاشات النظام</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#workflow">رحلة المريض</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#calculator">حاسبة التوفير</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">الباقات والأسعار</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">الأسئلة الشائعة</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="#demo-modal-trigger" data-bs-toggle="modal" data-bs-target="#requestDemoModal" class="btn btn-outline-primary px-3 py-2 rounded-pill fw-bold shadow-sm d-none d-sm-inline-flex align-items-center gap-1">
                        <i class="bi bi-play-circle-fill"></i> طلب ديمو حي
                    </a>

                    @auth
                        <a href="{{ route('clinic.dashboard') }}" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-1">
                            <i class="bi bi-speedometer2"></i> لوحة العيادة
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-1">
                            <i class="bi bi-box-arrow-in-left"></i> دخول النظام
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Content Area -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Master SaaS Footer -->
    <footer class="bg-dark text-light pt-5 pb-4 mt-0 border-top border-secondary position-relative" style="background-color: #0b1329 !important;">
        <div class="container">
            <div class="row g-4 pb-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-3 shadow-sm text-white" style="width: 44px; height: 44px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                            <i class="bi bi-heart-pulse-fill fs-5"></i>
                        </span>
                        <div>
                            <h4 class="fw-bold mb-0 text-white">Dental<span class="text-primary">Pro</span> <span class="badge bg-primary text-white fs-6 py-0 px-2 rounded-pill align-middle">ERP</span></h4>
                            <span class="small text-white-50">المنظومة السحابية المتكاملة لطب وجراحة الأسنان</span>
                        </div>
                    </div>
                    <p class="text-light text-opacity-75 small leading-relaxed" style="color: #cbd5e1 !important; line-height: 1.8;">
                        النظام السحابي الأول المصمم خصيصاً لأطباء ومراكز طب الأسنان لتبسيط دورة العمل اليومية، من السجل الطبي ومخطط الأسنان الرقمي وحتى الفوترة الإلكترونية المعتمدة ومتابعة أرباح العيادة.
                    </p>
                    <div class="d-flex flex-wrap gap-2 pt-2">
                        <span class="badge bg-secondary text-white small px-2 py-1"><i class="bi bi-shield-check text-success me-1"></i> معتمد ZATCA</span>
                        <span class="badge bg-secondary text-white small px-2 py-1"><i class="bi bi-lock-fill text-warning me-1"></i> تشفير 256-Bit</span>
                        <span class="badge bg-secondary text-white small px-2 py-1"><i class="bi bi-cloud-check-fill text-info me-1"></i> نسخ سحابي يومي</span>
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <h6 class="fw-bold text-white mb-3 border-bottom border-secondary pb-2">وحدات البرنامج</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2">
                        <li><a href="#odontogram-demo" class="text-light text-opacity-75 text-decoration-none hover-white" style="color: #cbd5e1 !important;">مخطط الأسنان التفاعلي</a></li>
                        <li><a href="#features" class="text-light text-opacity-75 text-decoration-none" style="color: #cbd5e1 !important;">الملف الطبي الإلكتروني EMR</a></li>
                        <li><a href="#features" class="text-light text-opacity-75 text-decoration-none" style="color: #cbd5e1 !important;">إدارة المواعيد والاستقبال</a></li>
                        <li><a href="#features" class="text-light text-opacity-75 text-decoration-none" style="color: #cbd5e1 !important;">الفوترة وأقساط العلاج</a></li>
                        <li><a href="#features" class="text-light text-opacity-75 text-decoration-none" style="color: #cbd5e1 !important;">إدارة طلبات المعامل الخارجية</a></li>
                        <li><a href="#features" class="text-light text-opacity-75 text-decoration-none" style="color: #cbd5e1 !important;">المصروفات والأرباح</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-2">
                    <h6 class="fw-bold text-white mb-3 border-bottom border-secondary pb-2">روابط سريعة</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2" style="color: #cbd5e1 !important;">
                        <li><a href="#screens" class="text-light text-opacity-75 text-decoration-none" style="color: #cbd5e1 !important;">شاشات البرنامج</a></li>
                        <li><a href="#workflow" class="text-light text-opacity-75 text-decoration-none" style="color: #cbd5e1 !important;">رحلة المريض بالعيادة</a></li>
                        <li><a href="#calculator" class="text-light text-opacity-75 text-decoration-none" style="color: #cbd5e1 !important;">حاسبة توفير العيادة</a></li>
                        <li><a href="#pricing" class="text-light text-opacity-75 text-decoration-none" style="color: #cbd5e1 !important;">باقات الاشتراك</a></li>
                        <li><a href="#faq" class="text-light text-opacity-75 text-decoration-none" style="color: #cbd5e1 !important;">الأسئلة الشائعة</a></li>
                        <li><a href="{{ route('login') }}" class="text-light text-opacity-75 text-decoration-none" style="color: #cbd5e1 !important;">بوابة الكادر الطبي</a></li>
                    </ul>
                </div>

                <div class="col-lg-4">
                    <h6 class="fw-bold text-white mb-3 border-bottom border-secondary pb-2">احصل على استشارة أو ديمو مجاني</h6>
                    <p class="small mb-3" style="color: #cbd5e1 !important;">فريق استشاريي الأنظمة الطبية جاهز للإجابة على استفساراتك وتقديم عرض حي مخصص لعيادتك.</p>
                    <div class="d-flex flex-column gap-2 mb-3">
                        <a href="https://wa.me/966500000000?text=مرحباً، أود حجز عرض توضيحي مباشر لنظام Dental Pro ERP" target="_blank" class="btn btn-success w-100 rounded-pill py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-whatsapp fs-5"></i> تواصل عبر الواتساب المباشر
                        </a>
                        <button type="button" data-bs-toggle="modal" data-bs-target="#requestDemoModal" class="btn btn-outline-light w-100 rounded-pill py-2 fw-semibold">
                            <i class="bi bi-calendar-event me-1"></i> طلب موعد اتصال وعرض ديمو
                        </button>
                    </div>
                    <div class="small text-muted d-flex align-items-center gap-3">
                        <span><i class="bi bi-envelope text-info me-1"></i> info@dentalpro-erp.com</span>
                        <span><i class="bi bi-telephone text-info me-1"></i> 920001234</span>
                    </div>
                </div>
            </div>

            <hr class="border-secondary my-3" style="opacity: 0.25;">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small" style="color: #94a3b8 !important;">
                <div>جميع الحقوق محفوظة © {{ date('Y') }} - منظومة <strong>Dental Pro ERP</strong> لإدارة عيادات ومراكز الأسنان السحابية.</div>
                <div class="mt-2 mt-md-0 d-flex gap-3">
                    <a href="#faq" class="text-muted text-decoration-none">سياسة الخصوصية وأمان البيانات</a>
                    <span>•</span>
                    <a href="#faq" class="text-muted text-decoration-none">شروط الاستخدام والخدمة</a>
                    <span>•</span>
                    <span class="text-white-50">Laravel 11 • Cloud Native</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>

