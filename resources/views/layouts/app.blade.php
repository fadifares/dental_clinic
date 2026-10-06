<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'مركز دنتال كير لطب وزراعة الأسنان')</title>

    <!-- Google Fonts & Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🦷</text></svg>">
    
    <!-- Core Vendor Scripts (Synchronous for inline Blade scripts) -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <!-- Vite Assets (Bootstrap 5, SCSS & jQuery) -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Top Info Bar -->
    <div class="bg-dark text-white py-1 small d-none d-md-block border-bottom border-secondary">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex gap-3">
                <span><i class="bi bi-geo-alt-fill text-info me-1"></i> الرياض، طريق الملك فهد - برج الماسة الطبي</span>
                <span><i class="bi bi-clock-fill text-info me-1"></i> السبت - الخميس: 9:00 ص - 10:00 م</span>
            </div>
            <div class="d-flex gap-3 align-items-center">
                <a href="tel:+966500000000" class="text-white text-decoration-none">
                    <i class="bi bi-telephone-fill text-success me-1"></i> 920001234
                </a>
                <span class="text-muted">|</span>
                @auth
                    <a href="{{ route('clinic.dashboard') }}" class="badge bg-primary text-white text-decoration-none">
                        <i class="bi bi-shield-lock-fill me-1"></i> لوحة العيادة ({{ auth()->user()->name }})
                    </a>
                @else
                    <a href="{{ route('login') }}" class="badge bg-primary text-white text-decoration-none">
                        <i class="bi bi-shield-lock-fill me-1"></i> تسجيل الدخول
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg clinic-glass-nav sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold fs-4 text-dark" href="{{ url('/') }}">
                <span class="clinic-stat-icon bg-primary text-white shadow-sm">
                    <i class="bi bi-heart-pulse-fill"></i>
                </span>
                <span>
                    دنتال<span class="text-primary">كير</span>
                    <span class="d-block small text-muted fw-normal fs-6">عيادات طب وتجميل الأسنان</span>
                </span>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarClinic" aria-controls="navbarClinic" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarClinic">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-medium gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ url('/') }}">الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">خدماتنا الطبية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#before-after">قبل وبعد</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#doctors">كادر الأطباء</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">آراء المرضى</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="#booking-section" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm">
                        <i class="bi bi-calendar-check-fill me-1"></i> احجز موعدك الآن
                    </a>
                    @auth
                        <a href="{{ route('clinic.dashboard') }}" class="btn btn-outline-dark px-3 py-2 rounded-pill fw-semibold">
                            <i class="bi bi-speedometer2 me-1"></i> لوحة التحكم
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-dark px-3 py-2 rounded-pill fw-semibold">
                            <i class="bi bi-box-arrow-in-left me-1"></i> دخول الكادر الطبي
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

    <!-- Footer -->
    <footer class="bg-dark text-light pt-5 pb-4 mt-5 border-top border-secondary">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="clinic-stat-icon bg-primary text-white">
                            <i class="bi bi-heart-pulse-fill"></i>
                        </span>
                        <h4 class="fw-bold mb-0">مركز دنتال<span class="text-primary">كير</span></h4>
                    </div>
                    <p class="text-secondary small leading-relaxed">
                        نقدم أرقى معايير طب الأسنان العلاجي والتجميلي باستخدام أحدث أجهزة المسح ثلاثي الأبعاد وتقنيات زراعة الأسنان الفورية وتصميم الابتسامة الرقمية (Digital Smile Design).
                    </p>
                    <div class="d-flex gap-2 text-white fs-5 mt-3">
                        <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <h6 class="fw-bold text-white mb-3">روابط سريعة</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2">
                        <li><a href="{{ url('/') }}" class="text-secondary text-decoration-none hover-light">الرئيسية</a></li>
                        <li><a href="#services" class="text-secondary text-decoration-none hover-light">الخدمات</a></li>
                        <li><a href="#doctors" class="text-secondary text-decoration-none hover-light">الأطباء</a></li>
                        <li><a href="#booking-section" class="text-secondary text-decoration-none hover-light">حجز المواعيد</a></li>
                        <li><a href="{{ route('clinic.dashboard') }}" class="text-secondary text-decoration-none hover-light">بوابة الموظفين</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-3">
                    <h6 class="fw-bold text-white mb-3">الخدمات المتميزة</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2 text-secondary">
                        <li>زراعة الأسنان الألمانية الفورية</li>
                        <li>ابتسامة هوليود وفينير إيماكس</li>
                        <li>تقويم الأسنان الشفاف (Invisalign)</li>
                        <li>علاج الجذور المجهري بجلسة واحدة</li>
                        <li>تبييض الأسنان بالليزر والتنظيف العميق</li>
                    </ul>
                </div>

                <div class="col-lg-3">
                    <h6 class="fw-bold text-white mb-3">اتصل بنا</h6>
                    <p class="small text-secondary mb-2"><i class="bi bi-geo-alt text-primary me-2"></i> الرياض - طريق الملك فهد</p>
                    <p class="small text-secondary mb-2"><i class="bi bi-telephone text-primary me-2"></i> 920001234 / 0500000000</p>
                    <p class="small text-secondary mb-3"><i class="bi bi-envelope text-primary me-2"></i> info@dentalcare-clinic.com</p>
                    <a href="https://wa.me/966500000000" target="_blank" class="btn btn-success btn-sm w-100 rounded-pill">
                        <i class="bi bi-whatsapp me-1"></i> تواصل فوري عبر واتساب
                    </a>
                </div>
            </div>

            <hr class="border-secondary my-4">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small text-secondary">
                <div>جميع الحقوق محفوظة © {{ date('Y') }} - نظام ومركز دنتال كير الطبي.</div>
                <div class="mt-2 mt-md-0">
                    <span class="text-light">بني بأحدث معايير الويب: Laravel 11 • Bootstrap 5 • jQuery</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
