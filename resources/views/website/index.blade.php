@extends('layouts.app')

@section('title', 'Dental Pro ERP | المنظومة السحابية المتكاملة لطب وجراحة الأسنان')

@push('styles')
<style>
    /* ==========================================================
       DENTAL PRO ERP - ELITE SAAS LANDING PAGE STYLES & ANIMATIONS
       ========================================================== */
    
    :root {
        --d-primary: #0284c7;
        --d-primary-hover: #0369a1;
        --d-primary-light: #e0f2fe;
        --d-teal: #0d9488;
        --d-teal-light: #ccfbf1;
        --d-dark: #0b1329;
        --d-slate: #1e293b;
        --d-gray-bg: #f8fafc;
        --d-border: #e2e8f0;
        --d-glow: rgba(2, 132, 199, 0.25);
    }

    body {
        background-color: #ffffff;
        color: #1e293b;
        font-family: 'IBM Plex Sans Arabic', 'Plus Jakarta Sans', system-ui, sans-serif;
        overflow-x: hidden;
    }

    /* Keyframe Animations */
    @keyframes pulseGlow {
        0%, 100% {
            box-shadow: 0 0 25px rgba(2, 132, 199, 0.35);
            transform: scale(1);
        }
        50% {
            box-shadow: 0 0 45px rgba(13, 148, 136, 0.5);
            transform: scale(1.02);
        }
    }

    @keyframes floatSlow {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    @keyframes floatBadge1 {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-8px) rotate(-1.5deg); }
    }

    @keyframes floatBadge2 {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(8px) rotate(1.5deg); }
    }

    @keyframes toothSelect {
        0% { transform: scale(0.9); }
        50% { transform: scale(1.15); }
        100% { transform: scale(1); }
    }

    .anim-float-1 { animation: floatBadge1 5s ease-in-out infinite; }
    .anim-float-2 { animation: floatBadge2 6s ease-in-out infinite; }
    .anim-float-slow { animation: floatSlow 7s ease-in-out infinite; }

    /* Hero Background Mesh */
    .saas-hero-bg {
        position: relative;
        background: radial-gradient(circle at 85% 15%, rgba(2, 132, 199, 0.12) 0%, transparent 45%),
                    radial-gradient(circle at 15% 85%, rgba(13, 148, 136, 0.12) 0%, transparent 45%),
                    radial-gradient(circle at 50% 50%, rgba(240, 249, 255, 0.8) 0%, transparent 80%),
                    #ffffff;
        overflow: hidden;
    }

    .saas-hero-bg::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(2, 132, 199, 0.08) 1px, transparent 1px);
        background-size: 28px 28px;
        pointer-events: none;
        opacity: 0.6;
    }

    /* Gradients & Text */
    .saas-title-gradient {
        background: linear-gradient(135deg, #0b1329 20%, #0284c7 65%, #0d9488 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .saas-pill-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(2, 132, 199, 0.08);
        border: 1px solid rgba(2, 132, 199, 0.25);
        color: #0284c7;
        padding: 0.45rem 1.15rem;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 0.85rem;
        backdrop-filter: blur(8px);
    }

    .saas-pill-pulse {
        width: 8px;
        height: 8px;
        background-color: #0284c7;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(2, 132, 199, 0.7);
        animation: pulseDot 2s infinite;
    }

    @keyframes pulseDot {
        0% { box-shadow: 0 0 0 0 rgba(2, 132, 199, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(2, 132, 199, 0); }
        100% { box-shadow: 0 0 0 0 rgba(2, 132, 199, 0); }
    }

    /* Hero Mockup Browser Frame */
    .saas-browser-frame {
        background: #0b1329;
        border-radius: 1.25rem;
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 25px 60px -15px rgba(11, 19, 41, 0.35), 0 0 40px rgba(2, 132, 199, 0.15);
        overflow: hidden;
        position: relative;
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s ease;
    }

    .saas-browser-header {
        background: #0f172a;
        padding: 0.75rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .saas-dot {
        width: 11px;
        height: 11px;
        border-radius: 50%;
        display: inline-block;
    }
    .saas-dot-red { background: #ef4444; }
    .saas-dot-yellow { background: #f59e0b; }
    .saas-dot-green { background: #10b981; }

    .saas-browser-url {
        background: rgba(255, 255, 255, 0.08);
        color: #94a3b8;
        border-radius: 0.5rem;
        padding: 0.2rem 1rem;
        font-size: 0.75rem;
        font-family: monospace;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        max-width: 340px;
        width: 100%;
    }

    .saas-hero-image-wrap {
        position: relative;
        background: #f1f5f9;
        overflow: hidden;
    }

    .saas-hero-image {
        width: 100%;
        height: auto;
        display: block;
        transition: transform 0.6s ease;
    }

    .saas-floating-chip {
        position: absolute;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 1rem;
        padding: 0.85rem 1.25rem;
        box-shadow: 0 20px 35px -5px rgba(15, 23, 42, 0.15);
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    /* Screen Showcase Tabs */
    .screen-tab-btn {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-weight: 700;
        padding: 0.75rem 1.15rem;
        border-radius: 0.75rem;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        text-align: right;
        font-size: 0.88rem;
    }

    .screen-tab-btn:hover {
        background: #f8fafc;
        color: #0284c7;
        border-color: #cbd5e1;
    }

    .screen-tab-btn.active {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff;
        border-color: #0284c7;
        box-shadow: 0 8px 18px -4px rgba(2, 132, 199, 0.4);
    }

    .screen-tab-btn.active i {
        color: #ffffff !important;
    }

    /* Interactive Odontogram Sandbox */
    .sandbox-container {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border: 2px solid #e2e8f0;
        border-radius: 1.5rem;
        padding: 2rem;
        box-shadow: 0 20px 40px -10px rgba(15, 23, 42, 0.08);
        position: relative;
    }

    .sandbox-tooth-btn {
        width: 44px;
        height: 60px;
        background: #ffffff;
        border: 2px solid #cbd5e1;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        padding: 4px 2px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        user-select: none;
        position: relative;
    }

    .sandbox-tooth-btn:hover {
        transform: translateY(-5px);
        border-color: #0284c7;
        box-shadow: 0 8px 16px -2px rgba(2, 132, 199, 0.25);
    }

    .sandbox-tooth-btn .tooth-badge-num {
        font-size: 0.7rem;
        font-weight: 800;
        color: #64748b;
    }

    .sandbox-tooth-btn .tooth-icon-shape {
        font-size: 1.15rem;
        line-height: 1;
        transition: transform 0.2s ease;
    }

    .sandbox-tooth-btn .status-indicator-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #cbd5e1;
    }

    /* Tooth Status Colors */
    .sandbox-tooth-btn.status-healthy {
        background: #f0fdf4;
        border-color: #22c55e;
    }
    .sandbox-tooth-btn.status-healthy .status-indicator-dot { background: #22c55e; }
    .sandbox-tooth-btn.status-healthy .tooth-badge-num { color: #166534; }

    .sandbox-tooth-btn.status-caries {
        background: #fef2f2;
        border-color: #ef4444;
        animation: toothSelect 0.35s ease;
    }
    .sandbox-tooth-btn.status-caries .status-indicator-dot { background: #ef4444; }
    .sandbox-tooth-btn.status-caries .tooth-badge-num { color: #991b1b; }

    .sandbox-tooth-btn.status-filling {
        background: #f0f9ff;
        border-color: #0284c7;
        animation: toothSelect 0.35s ease;
    }
    .sandbox-tooth-btn.status-filling .status-indicator-dot { background: #0284c7; }
    .sandbox-tooth-btn.status-filling .tooth-badge-num { color: #075985; }

    .sandbox-tooth-btn.status-endo {
        background: #fefce8;
        border-color: #eab308;
        animation: toothSelect 0.35s ease;
    }
    .sandbox-tooth-btn.status-endo .status-indicator-dot { background: #eab308; }
    .sandbox-tooth-btn.status-endo .tooth-badge-num { color: #854d0e; }

    .sandbox-tooth-btn.status-crown {
        background: #faf5ff;
        border-color: #a855f7;
        animation: toothSelect 0.35s ease;
    }
    .sandbox-tooth-btn.status-crown .status-indicator-dot { background: #a855f7; }
    .sandbox-tooth-btn.status-crown .tooth-badge-num { color: #6b21a8; }

    .sandbox-tooth-btn.status-implant {
        background: #f0fdfa;
        border-color: #0d9488;
        animation: toothSelect 0.35s ease;
    }
    .sandbox-tooth-btn.status-implant .status-indicator-dot { background: #0d9488; }
    .sandbox-tooth-btn.status-implant .tooth-badge-num { color: #115e59; }

    /* Action Picker Pills */
    .status-picker-pill {
        border-radius: 9999px;
        padding: 0.5rem 1rem;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s ease;
        background: #f1f5f9;
        color: #475569;
    }

    .status-picker-pill.active {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        transform: scale(1.04);
    }

    /* Cards & Features */
    .saas-feature-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 1.25rem;
        padding: 1.75rem;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .saas-feature-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 35px -10px rgba(2, 132, 199, 0.15);
        border-color: #bae6fd;
    }

    .saas-feature-icon-box {
        width: 54px;
        height: 54px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1.25rem;
        transition: transform 0.3s ease;
    }

    .saas-feature-card:hover .saas-feature-icon-box {
        transform: scale(1.1) rotate(-4deg);
    }

    /* Role Dashboard Cards */
    .role-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 1.25rem;
        padding: 1.75rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .role-card:hover {
        border-color: #0284c7;
        box-shadow: 0 15px 30px -10px rgba(2, 132, 199, 0.18);
        transform: translateY(-4px);
    }

    /* Pricing Cards */
    .pricing-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 1.5rem;
        padding: 2.5rem 2rem;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .pricing-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 45px -10px rgba(15, 23, 42, 0.12);
    }

    .pricing-card.featured {
        background: linear-gradient(180deg, #ffffff 0%, #f0f9ff 100%);
        border: 2px solid #0284c7;
        box-shadow: 0 20px 40px -10px rgba(2, 132, 199, 0.22);
    }

    .pricing-badge-popular {
        position: absolute;
        top: -14px;
        right: 50%;
        transform: translateX(50%);
        background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);
        color: #ffffff;
        font-weight: 800;
        font-size: 0.75rem;
        padding: 0.35rem 1.25rem;
        border-radius: 9999px;
        box-shadow: 0 6px 15px rgba(2, 132, 199, 0.4);
    }

    /* ROI Calculator Sliders */
    .roi-slider {
        -webkit-appearance: none;
        width: 100%;
        height: 8px;
        border-radius: 5px;
        background: #e2e8f0;
        outline: none;
    }
    .roi-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #0284c7;
        cursor: pointer;
        box-shadow: 0 0 10px rgba(2, 132, 199, 0.5);
        border: 3px solid #ffffff;
    }

    /* Simulated UI Screen Container */
    .simulated-ui-wrap {
        background: #ffffff;
        border-radius: 0.85rem;
        padding: 1.25rem;
        min-height: 420px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    }
</style>
@endpush

@section('content')

<!-- ==========================================================
     1. HERO SECTION: HIGH-IMPACT SAAS PRESENTATION
     ========================================================== -->
<section class="saas-hero-bg py-5 pt-lg-5 pb-lg-6 position-relative">
    <div class="container py-lg-4">
        <!-- Eyebrow Badge & Announcement -->
        <div class="text-center mb-4">
            <div class="saas-pill-badge mb-2">
                <span class="saas-pill-pulse"></span>
                <span>المنظومة السحابية المتكاملة لطب وجراحة وتجميل الأسنان | Dental Pro ERP</span>
                <span class="badge bg-primary text-white rounded-pill px-2 py-0 ms-1">V4.2</span>
            </div>
            <h1 class="display-4 fw-extrabold text-dark lh-base mt-2 mb-3">
                أدر عيادتك لطب الأسنان بذكاء وسرعة <br class="d-none d-lg-block">
                <span class="saas-title-gradient">من السجل الرقمي والمخطط حتى الفوترة الضريبية</span>
            </h1>
            <p class="lead text-muted max-w-750 mx-auto fs-6 mb-4" style="max-width: 820px; line-height: 1.8;">
                منظومة سحابية متكاملة مصممة خصيصاً لأطباء ومجمعات طب الأسنان. تشمل السجل الطبي الإلكتروني EMR، مخطط الأسنان الرقمي 32 سن (Odontogram)، الفوترة المعتمدة من ZATCA، تذكيرات واتساب الآلية، إدارة طلبيات المعامل والتركيبات، تتبع المصروفات والأرباح، وأدوار محكمة للطبيب، الاستقبال، والمحاسب.
            </p>

            <!-- Action Buttons -->
            <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
                <a href="#requestDemoModal" data-bs-toggle="modal" class="btn btn-primary btn-lg rounded-pill px-4 py-3 shadow-lg fw-bold d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                    <i class="bi bi-rocket-takeoff-fill fs-5"></i> ابدأ تجربتك المجانية (14 يوماً)
                </a>
                <a href="#odontogram-demo" class="btn btn-outline-dark btn-lg rounded-pill px-4 py-3 fw-bold d-inline-flex align-items-center gap-2">
                    <i class="bi bi-cpu-fill text-primary"></i> جرب المخطط التفاعلي مباشرة
                </a>
                <a href="#screens" class="btn btn-light btn-lg rounded-pill px-4 py-3 fw-semibold border d-inline-flex align-items-center gap-2 text-secondary">
                    <i class="bi bi-images text-info"></i> استعرض شاشات البرنامج
                </a>
            </div>

            <!-- Trust Markers -->
            <div class="d-flex flex-wrap justify-content-center align-items-center gap-3 gap-md-4 small text-muted pt-2">
                <span class="d-flex align-items-center gap-1">
                    <i class="bi bi-shield-fill-check text-success fs-5"></i> معتمد ومطابق لـ ZATCA المرحلة 2
                </span>
                <span class="d-none d-md-inline text-muted">•</span>
                <span class="d-flex align-items-center gap-1">
                    <i class="bi bi-lock-fill text-warning fs-5"></i> تشفير طبي 256-Bit HIPAA
                </span>
                <span class="d-none d-md-inline text-muted">•</span>
                <span class="d-flex align-items-center gap-1">
                    <i class="bi bi-whatsapp text-success fs-5"></i> تذكيرات واتساب مؤتمتة
                </span>
                <span class="d-none d-md-inline text-muted">•</span>
                <span class="d-flex align-items-center gap-1">
                    <i class="bi bi-star-fill text-warning fs-5"></i> تقييم 4.9/5 من أكثر من 350+ عيادة
                </span>
            </div>
        </div>

        <!-- Realistic Interactive macOS App Mockup Frame with Actual Dashboard Screenshot -->
        <div class="row justify-content-center mt-5">
            <div class="col-lg-11 col-xl-10 position-relative">
                
                <!-- Floating Glassmorphism Badge 1 (Top Left in RTL) -->
                <div class="saas-floating-chip anim-float-1 d-none d-md-flex" style="top: -20px; left: -25px;">
                    <div class="rounded-circle bg-success-subtle p-2 text-success d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-graph-up-arrow fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark fs-6">+38.5% نمو الإيرادات</div>
                        <div class="small text-muted" style="font-size: 0.75rem;">تحصيل فوري ومتابعة الأقساط</div>
                    </div>
                </div>

                <!-- Floating Glassmorphism Badge 2 (Bottom Right in RTL) -->
                <div class="saas-floating-chip anim-float-2 d-none d-md-flex" style="bottom: 25px; right: -25px;">
                    <div class="rounded-circle bg-primary-subtle p-2 text-primary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-whatsapp fs-5 text-success"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark fs-6">96% نسبة حضور المواعيد</div>
                        <div class="small text-muted" style="font-size: 0.75rem;">تذكير واتساب آلي قبل 24 ساعة</div>
                    </div>
                </div>

                <!-- Browser Container Frame -->
                <div class="saas-browser-frame shadow-2xl">
                    <div class="saas-browser-header">
                        <div class="d-flex gap-2">
                            <span class="saas-dot saas-dot-red"></span>
                            <span class="saas-dot saas-dot-yellow"></span>
                            <span class="saas-dot saas-dot-green"></span>
                        </div>
                        <div class="saas-browser-url mx-auto text-truncate">
                            <i class="bi bi-lock-fill text-success"></i> https://app.dentalpro-cloud.com/clinic/dashboard
                        </div>
                        <div class="text-white-50 small d-none d-sm-block">
                            <i class="bi bi-shield-check text-info me-1"></i> سحابي مشفر
                        </div>
                    </div>

                    <!-- Screenshot Image with Interactive Overlay Trigger -->
                    <div class="saas-hero-image-wrap position-relative">
                        <img src="{{ asset('images/screenshots/dashboard.png') }}" 
                             alt="لوحة تحكم برنامج عيادات الأسنان Dental Pro ERP" 
                             class="saas-hero-image img-fluid w-100" 
                             style="cursor: zoom-in;"
                             data-bs-toggle="modal" 
                             data-bs-target="#imageLightboxModal"
                             data-title="لوحة التحكم والمؤشرات الحيوية الشاملة للعيادة"
                             data-img="{{ asset('images/screenshots/dashboard.png') }}">
                        
                        <div class="position-absolute bottom-0 start-0 end-0 p-3 bg-dark bg-opacity-75 text-white d-flex justify-content-between align-items-center backdrop-blur" style="backdrop-filter: blur(8px);">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary">شاشة حية من النظام</span>
                                <span class="small">لوحة المؤشرات الذكية KPI، المواعيد الحية، وتدفق الإيرادات اللحظي</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1" data-bs-toggle="modal" data-bs-target="#imageLightboxModal" data-title="لوحة التحكم والمؤشرات الحيوية الشاملة للعيادة" data-img="{{ asset('images/screenshots/dashboard.png') }}">
                                <i class="bi bi-arrows-fullscreen me-1"></i> تكبير الشاشة
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

<!-- ==========================================================
     2. METRICS & IMPACT RIBBON (أرقام وإحصائيات موثوقة)
     ========================================================== -->
<section class="py-4 border-top border-bottom bg-light">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="p-3">
                    <div class="display-6 fw-extrabold text-primary mb-1">+350</div>
                    <div class="fw-bold text-dark">عيادة ومجمع أسنان نشط</div>
                    <div class="small text-muted">في المملكة ودول الخليج ومصر</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3">
                    <div class="display-6 fw-extrabold text-success mb-1">45 دقيقة</div>
                    <div class="fw-bold text-dark">توفير يومي لكل طبيب</div>
                    <div class="small text-muted">سرعة التوثيق والمخطط الرقمي</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3">
                    <div class="display-6 fw-extrabold text-info mb-1">+500K</div>
                    <div class="fw-bold text-dark">موعد وسجل مريض مُدار</div>
                    <div class="small text-muted">بأعلى معايير الأمان السحابي</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3">
                    <div class="display-6 fw-extrabold text-warning mb-1">99.9%</div>
                    <div class="fw-bold text-dark">جاهزية واستقرار سحابي</div>
                    <div class="small text-muted">نسخ احتياطي فوري ودعم 24/7</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================
     3. INTERACTIVE ODONTOGRAM SANDBOX: LIVE PLAYABLE DEMO!
     ========================================================== -->
<section class="py-5 bg-white position-relative" id="odontogram-demo">
    <div class="container py-lg-4">
        <div class="text-center max-w-750 mx-auto mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-magic me-1"></i> تجربة حية تفاعلية مباشرة
            </span>
            <h2 class="display-6 fw-extrabold text-dark mt-2">
                مخطط الأسنان الرقمي الذكي (Odontogram)
            </h2>
            <p class="text-muted fs-6">
                جرب النظام بنفسك الآن! اختر الحالة الطبية واضغط على أي سن لتشهد كيف يوثق الطبيب الإجراء السريري ويحسب تكلفة العلاج فوراً.
            </p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="sandbox-container">
                    
                    <!-- Status Selector Controls -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-palette-fill text-primary fs-5"></i>
                            <span class="fw-bold text-dark">اختر حالة السن المراد فحصها:</span>
                        </div>

                        <div class="d-flex flex-wrap gap-2" id="statusPickerGroup">
                            <button type="button" class="status-picker-pill active border-danger text-danger bg-danger-subtle" data-status="caries" data-name="تسوس عميق" data-cost="250">
                                <i class="bi bi-record-circle-fill me-1"></i> تسوس عميق (Caries)
                            </button>
                            <button type="button" class="status-picker-pill" data-status="filling" data-name="حشوة تجميلية" data-cost="350">
                                <i class="bi bi-circle-fill text-primary me-1"></i> حشوة كمبوزيت (Filling)
                            </button>
                            <button type="button" class="status-picker-pill" data-status="endo" data-name="علاج عصب وجذور" data-cost="750">
                                <i class="bi bi-lightning-charge-fill text-warning me-1"></i> علاج عصب (Endo)
                            </button>
                            <button type="button" class="status-picker-pill" data-status="crown" data-name="تلبيسة زيركون" data-cost="1200">
                                <i class="bi bi-gem text-purple me-1" style="color: #9333ea;"></i> تاج زيركون (Crown)
                            </button>
                            <button type="button" class="status-picker-pill" data-status="implant" data-name="زراعة سن" data-cost="2800">
                                <i class="bi bi-shield-plus text-teal me-1" style="color: #0d9488;"></i> زراعة سن (Implant)
                            </button>
                            <button type="button" class="status-picker-pill" data-status="healthy" data-name="سن سليم" data-cost="0">
                                <i class="bi bi-check-circle-fill text-success me-1"></i> سليم (Healthy)
                            </button>
                        </div>
                    </div>

                    <!-- Dental Notation Info Banner -->
                    <div class="d-flex justify-content-between align-items-center px-2 mb-2 small text-muted">
                        <span><i class="bi bi-info-circle me-1 text-primary"></i> الفك العلوي (Upper Arch - الأسنان من 18 إلى 28)</span>
                        <span class="badge bg-light text-secondary border">نظام ترقيم FDI الدولي</span>
                    </div>

                    <!-- Upper Dental Arch -->
                    <div class="d-flex justify-content-center gap-1 gap-sm-2 flex-nowrap overflow-x-auto py-2 mb-4" id="upperArch">
                        <!-- Teeth 18 to 11 -->
                        @foreach([18, 17, 16, 15, 14, 13, 12, 11] as $tooth)
                            <div class="sandbox-tooth-btn status-healthy" data-tooth="{{ $tooth }}" title="سن رقم {{ $tooth }}">
                                <span class="tooth-badge-num">{{ $tooth }}</span>
                                <span class="tooth-icon-shape">🦷</span>
                                <span class="status-indicator-dot"></span>
                            </div>
                        @endforeach
                        <div class="vr mx-2 text-secondary opacity-50" style="height: 55px;"></div>
                        <!-- Teeth 21 to 28 -->
                        @foreach([21, 22, 23, 24, 25, 26, 27, 28] as $tooth)
                            <div class="sandbox-tooth-btn status-healthy" data-tooth="{{ $tooth }}" title="سن رقم {{ $tooth }}">
                                <span class="tooth-badge-num">{{ $tooth }}</span>
                                <span class="tooth-icon-shape">🦷</span>
                                <span class="status-indicator-dot"></span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Lower Arch Info Banner -->
                    <div class="d-flex justify-content-between align-items-center px-2 mb-2 small text-muted">
                        <span><i class="bi bi-info-circle me-1 text-primary"></i> الفك السفلي (Lower Arch - الأسنان من 48 إلى 38)</span>
                        <span class="text-muted">اضغط على أي سن لتطبيق الفحص</span>
                    </div>

                    <!-- Lower Dental Arch -->
                    <div class="d-flex justify-content-center gap-1 gap-sm-2 flex-nowrap overflow-x-auto py-2 mb-4" id="lowerArch">
                        <!-- Teeth 48 to 41 -->
                        @foreach([48, 47, 46, 45, 44, 43, 42, 41] as $tooth)
                            <div class="sandbox-tooth-btn status-healthy" data-tooth="{{ $tooth }}" title="سن رقم {{ $tooth }}">
                                <span class="tooth-badge-num">{{ $tooth }}</span>
                                <span class="tooth-icon-shape">🦷</span>
                                <span class="status-indicator-dot"></span>
                            </div>
                        @endforeach
                        <div class="vr mx-2 text-secondary opacity-50" style="height: 55px;"></div>
                        <!-- Teeth 31 to 38 -->
                        @foreach([31, 32, 33, 34, 35, 36, 37, 38] as $tooth)
                            <div class="sandbox-tooth-btn status-healthy" data-tooth="{{ $tooth }}" title="سن رقم {{ $tooth }}">
                                <span class="tooth-badge-num">{{ $tooth }}</span>
                                <span class="tooth-icon-shape">🦷</span>
                                <span class="status-indicator-dot"></span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Interactive Live Feedback Summary Bar -->
                    <div class="p-3 bg-white rounded-3 border d-flex flex-wrap align-items-center justify-content-between gap-3 shadow-sm">
                        <div class="d-flex align-items-center gap-3">
                            <span class="rounded-circle bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-clipboard2-pulse fs-5"></i>
                            </span>
                            <div>
                                <div class="fw-bold text-dark small" id="lastActionLabel">آخر إجراء سريري: تم تحديد تسوس على السن 16</div>
                                <div class="text-muted small" style="font-size: 0.8rem;">
                                    عدد الأسنان المحدثة: <span id="treatedTeethCount" class="fw-bold text-primary">0</span> من أصل 32 سن
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="text-end">
                                <span class="small text-muted d-block">إجمالي تكلفة الخطة المقدرة:</span>
                                <span class="fw-extrabold text-success fs-5" id="totalEstimatedCost">0 ر.س</span>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="resetSandboxBtn">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> إعادة ضبط
                            </button>
                        </div>
                    </div>

                    <!-- Feature Callout -->
                    <div class="row g-3 mt-3 pt-3 border-top text-center text-md-start">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check2-circle text-success fs-5"></i>
                                <span class="small fw-semibold text-dark">ربط فوري مع ملف المريض EMR</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check2-circle text-success fs-5"></i>
                                <span class="small fw-semibold text-dark">توليد فواتير وأقساط الخطة العلاجية آلياً</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check2-circle text-success fs-5"></i>
                                <span class="small fw-semibold text-dark">طباعة تقرير طبي ملون للمريض</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================
     4. SYSTEM MODULES & REAL SCREENSHOT TOUR (شاشات البرنامج)
     ========================================================== -->
<section class="py-5 bg-light position-relative" id="screens">
    <div class="container py-lg-4">
        <div class="text-center max-w-750 mx-auto mb-5">
            <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-camera-fill me-1"></i> جولة داخل البرنامج الحقيقي
            </span>
            <h2 class="display-6 fw-extrabold text-dark mt-2">
                واجهات فائقة البساطة والقوة مصممة لسرعة العمل
            </h2>
            <p class="text-muted fs-6">
                شاهد لقطات شاشات حقيقية من واجهات نظام Dental Pro ERP، تم تصميمها لتقليل النقرات وتسهيل وصول الطبيب وموظف الاستقبال والمحاسب للمعلومة في أجزاء من الثانية.
            </p>
        </div>

        <!-- Screen Navigation Tabs (All 8 Modules!) -->
        <div class="d-flex flex-wrap justify-content-center gap-2 mb-4" id="screenTourNav">
            <button class="screen-tab-btn active" data-target="#tab-dashboard" data-img="{{ asset('images/screenshots/dashboard.png') }}" data-title="لوحة التحكم والتحليلات اللحظية">
                <i class="bi bi-speedometer2 text-primary fs-5"></i> لوحة التحكم والتحليلات
            </button>
            <button class="screen-tab-btn" data-target="#tab-odontogram" data-img="{{ asset('images/screenshots/odontogram.png') }}" data-title="مخطط الأسنان الرقمي التفاعلي وملف المريض">
                <i class="bi bi-cpu text-info fs-5"></i> مخطط الأسنان التفاعلي EMR
            </button>
            <button class="screen-tab-btn" data-target="#tab-patients" data-img="{{ asset('images/screenshots/patients.png') }}" data-title="دليل وسجلات المرضى الطبية">
                <i class="bi bi-people-fill text-success fs-5"></i> دليل وسجلات المرضى
            </button>
            <button class="screen-tab-btn" data-target="#tab-appointments" data-img="{{ asset('images/screenshots/appointments.png') }}" data-title="إدارة المواعيد والاستقبال والأجندة">
                <i class="bi bi-calendar2-check-fill text-warning fs-5"></i> إدارة المواعيد والأجندة
            </button>
            <button class="screen-tab-btn" data-target="#tab-billing" data-img="{{ asset('images/screenshots/billing.png') }}" data-title="الفوترة ونظام الأقساط والربط مع ZATCA">
                <i class="bi bi-receipt-cutoff text-danger fs-5"></i> الفوترة والأقساط و ZATCA
            </button>
            <button class="screen-tab-btn" data-target="#tab-labs" data-title="طلبيات معامل الأسنان والتركيبات وتحديد الألوان">
                <i class="bi bi-box-seam-fill text-teal fs-5" style="color: #0d9488;"></i> طلبيات المعامل والتركيبات
            </button>
            <button class="screen-tab-btn" data-target="#tab-expenses" data-title="سجل مصاريف ونفقات العيادة وصافي الأرباح">
                <i class="bi bi-wallet2 text-warning fs-5"></i> المصروفات وصافي الأرباح
            </button>
            <button class="screen-tab-btn" data-target="#tab-users" data-title="إدارة المستخدمين وصلاحيات الوصول والأمان">
                <i class="bi bi-shield-lock-fill text-secondary fs-5"></i> الصلاحيات والأمان
            </button>
        </div>

        <!-- Screen Display Card Frame -->
        <div class="row g-4 align-items-center mt-2">
            <!-- Screen Screenshot / Interactive Preview Container -->
            <div class="col-lg-7">
                <div class="saas-browser-frame shadow-lg">
                    <div class="saas-browser-header">
                        <div class="d-flex gap-2">
                            <span class="saas-dot saas-dot-red"></span>
                            <span class="saas-dot saas-dot-yellow"></span>
                            <span class="saas-dot saas-dot-green"></span>
                        </div>
                        <div class="saas-browser-url text-truncate" id="activeScreenUrl">
                            https://app.dentalpro.com/clinic/dashboard
                        </div>
                        <button type="button" class="btn btn-sm btn-link text-white-50 p-0 text-decoration-none ms-auto" id="zoomCurrentScreenBtn">
                            <i class="bi bi-arrows-fullscreen"></i>
                        </button>
                    </div>

                    <!-- Screen Visual Container: Either real image or simulated rich UI card -->
                    <div class="position-relative overflow-hidden" id="screenVisualHolder" style="min-height: 420px;">
                        <!-- Image Container for Screenshots -->
                        <img id="activeScreenImage" 
                             src="{{ asset('images/screenshots/dashboard.png') }}" 
                             alt="شاشة نظام دنتال برو" 
                             class="img-fluid w-100" 
                             style="cursor: zoom-in; transition: opacity 0.3s ease;">

                        <!-- Simulated UI for Labs (Shown when Tab Labs is active) -->
                        <div id="simulatedLabsUi" class="simulated-ui-wrap d-none">
                            <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-box-seam text-teal me-1" style="color: #0d9488;"></i> طلبيات معامل الأسنان والتركيبات (Dental Lab Orders)</h6>
                                    <small class="text-muted">تتبع تصنيع التيجان، الجسور، ودرجات الألوان (Shades)</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">+ طلبية معمل جديدة</span>
                            </div>
                            <div class="table-responsive small">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>المريض والسن</th>
                                            <th>المعمل الخارجي</th>
                                            <th>نوع التركيبة</th>
                                            <th>درجة اللون (Shade)</th>
                                            <th>تاريخ التسليم</th>
                                            <th>الحالة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>محمد السالم</strong> <span class="badge bg-light text-dark">#16</span></td>
                                            <td>معمل النخبة للأسنان</td>
                                            <td>تاج زيركون (Zirconia)</td>
                                            <td><span class="badge bg-secondary">A2</span></td>
                                            <td>2026-10-12</td>
                                            <td><span class="badge bg-warning text-dark"><i class="bi bi-tools me-1"></i> قيد التصنيع</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>سارة خالد</strong> <span class="badge bg-light text-dark">#11, #21</span></td>
                                            <td>معمل رويال ديجيتال</td>
                                            <td>فينير إيماكس (E-Max)</td>
                                            <td><span class="badge bg-info text-white">BL1 (Bleach)</span></td>
                                            <td>2026-10-10</td>
                                            <td><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> جاهزة للاستلام</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>عبدالله الدوسري</strong> <span class="badge bg-light text-dark">#36</span></td>
                                            <td>معمل سمايل كير</td>
                                            <td>دعامة زراعة + تاج</td>
                                            <td><span class="badge bg-secondary">A3</span></td>
                                            <td>2026-10-08</td>
                                            <td><span class="badge bg-secondary"><i class="bi bi-check2-all me-1"></i> تم التركيب</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3 p-2 bg-light rounded-3 d-flex justify-content-between small text-muted">
                                <span><i class="bi bi-cash-coin text-success me-1"></i> حساب تكلفة المعمل وهامش ربح العيادة آلياً</span>
                                <span class="fw-bold text-teal" style="color: #0d9488;">تنبيه فوري بموعد وصول التركيبة قبل جلسة المريض</span>
                            </div>
                        </div>

                        <!-- Simulated UI for Expenses (Shown when Tab Expenses is active) -->
                        <div id="simulatedExpensesUi" class="simulated-ui-wrap d-none">
                            <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-wallet2 text-warning me-1"></i> سجل مصاريف ونفقات العيادة (Clinic Expenses)</h6>
                                    <small class="text-muted">متابعة النفقات التشغيلية، فواتير المشتريات، وصافي الأرباح</small>
                                </div>
                                <span class="badge bg-danger rounded-pill">+ تسجيل مصروف جديد</span>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-4">
                                    <div class="p-2 bg-danger-subtle rounded-2 text-center">
                                        <small class="text-muted d-block">مصاريف الشهر</small>
                                        <strong class="text-danger fs-6">14,250 ر.س</strong>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 bg-success-subtle rounded-2 text-center">
                                        <small class="text-muted d-block">إجمالي المقبوضات</small>
                                        <strong class="text-success fs-6">68,500 ر.س</strong>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 bg-primary-subtle rounded-2 text-center">
                                        <small class="text-muted d-block">صافي الربح التقديري</small>
                                        <strong class="text-primary fs-6">54,250 ر.س</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive small">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>البند والتصنيف</th>
                                            <th>المبلغ</th>
                                            <th>طريقة الدفع</th>
                                            <th>المرفق (الإيصال)</th>
                                            <th>التاريخ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>خامات كمبوزيت ومخدر</strong> <span class="badge bg-light text-dark">مستلزمات طبية</span></td>
                                            <td class="fw-bold text-danger">3,400 ر.س</td>
                                            <td>تحويل بنكي</td>
                                            <td><span class="badge bg-info-subtle text-info"><i class="bi bi-image me-1"></i> إيصال مضغوط HD</span></td>
                                            <td>2026-10-09</td>
                                        </tr>
                                        <tr>
                                            <td><strong>إيجار مقر العيادة (دفعة شهرية)</strong> <span class="badge bg-light text-dark">إيجار ومرافق</span></td>
                                            <td class="fw-bold text-danger">8,000 ر.س</td>
                                            <td>شيك بنكي</td>
                                            <td><span class="badge bg-info-subtle text-info"><i class="bi bi-file-earmark-pdf me-1"></i> عقد وسند</span></td>
                                            <td>2026-10-01</td>
                                        </tr>
                                        <tr>
                                            <td><strong>صيانة دورية لجهاز الأوتوكلاف</strong> <span class="badge bg-light text-dark">صيانة وتعقيم</span></td>
                                            <td class="fw-bold text-danger">850 ر.س</td>
                                            <td>نقدي</td>
                                            <td><span class="badge bg-info-subtle text-info"><i class="bi bi-image me-1"></i> فاتورة فني</span></td>
                                            <td>2026-10-04</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Simulated UI for Users & Security (Shown when Tab Users is active) -->
                        <div id="simulatedUsersUi" class="simulated-ui-wrap d-none">
                            <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-shield-lock text-primary me-1"></i> إدارة المستخدمين وصلاحيات الأمان (RBAC & Security)</h6>
                                    <small class="text-muted">التحكم بكادر العمل، الصلاحيات الدقيقة، وعمولات الأطباء</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">+ إضافة مستخدم جديد</span>
                            </div>
                            <div class="table-responsive small">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>الاسم والكادر</th>
                                            <th>الدور والصلاحية</th>
                                            <th>التخصص / العمولة</th>
                                            <th>الحالة</th>
                                            <th>إجراءات الأمان</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>د. أحمد السالم</strong> <span class="text-muted d-block" style="font-size: 0.72rem;">ahmed@clinic.com</span></td>
                                            <td><span class="badge bg-primary">طبيب أسنان (Doctor)</span></td>
                                            <td>جراحة وزراعة <span class="badge bg-success-subtle text-success">عمولة 35%</span></td>
                                            <td><span class="badge bg-success">نشط</span></td>
                                            <td><span class="btn btn-sm btn-outline-secondary py-0">تعديل الصلاحية</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>م. ريم الشهري</strong> <span class="text-muted d-block" style="font-size: 0.72rem;">reem@clinic.com</span></td>
                                            <td><span class="badge bg-info text-white">استقبال (Receptionist)</span></td>
                                            <td>مكتب الاستقبال والمواعيد</td>
                                            <td><span class="badge bg-success">نشط</span></td>
                                            <td><span class="btn btn-sm btn-outline-secondary py-0">تعديل الصلاحية</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>أ. سامي المنصور</strong> <span class="text-muted d-block" style="font-size: 0.72rem;">sami@clinic.com</span></td>
                                            <td><span class="badge bg-warning text-dark">محاسب (Accountant)</span></td>
                                            <td>الفوترة والمصروفات والتقارير</td>
                                            <td><span class="badge bg-success">نشط</span></td>
                                            <td><span class="btn btn-sm btn-outline-secondary py-0">تعديل الصلاحية</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>د. فهد الغامدي</strong> <span class="text-muted d-block" style="font-size: 0.72rem;">fahad@clinic.com</span></td>
                                            <td><span class="badge bg-secondary">مدير النظام (Admin)</span></td>
                                            <td>تحكم كامل وإعدادات العيادة</td>
                                            <td><span class="badge bg-success">نشط</span></td>
                                            <td><span class="badge bg-light text-dark">حساب رئيسي</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dynamic Screen Explanation Content -->
            <div class="col-lg-5">
                <div class="clinic-card p-4 p-md-5 h-100 d-flex flex-column justify-content-center shadow-sm">
                    
                    <!-- Content for Tab: Dashboard -->
                    <div class="screen-content-pane" id="pane-dashboard">
                        <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill fw-bold mb-3">
                            <i class="bi bi-speedometer2 me-1"></i> لوحة القيادة الذكية
                        </span>
                        <h3 class="fw-extrabold text-dark mb-3">نظرة شاملة ومباشرة على نبض عيادتك</h3>
                        <p class="text-muted small leading-relaxed mb-4">
                            تمنح الإدارة والطبيب نظرة فورية على أداء اليوم: عدد المرضى في الانتظار، الإيرادات المحصلة، الحالات المكتملة، وتنبيهات طلبات المعامل المتأخرة.
                        </p>
                        <ul class="list-unstyled d-flex flex-column gap-3 mb-4">
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-primary fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">مؤشرات الأداء الحيوية (KPIs):</strong>
                                    <span class="text-muted small">متابعة الإيرادات اليومية والشهرية ونسبة إشغال كراسي العيادة.</span>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-primary fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">لوحات قيادة مخصصة حسب الدور:</strong>
                                    <span class="text-muted small">شاشة مخصصة للطبيب، شاشة سريعة لموظف الاستقبال، وشاشة مالية للمحاسب.</span>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-primary fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">تنبيهات فورية وذكية:</strong>
                                    <span class="text-muted small">إشعارات بالمواعيد القادمة وحالات الفواتير غير المسددة.</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Content for Tab: Odontogram & EMR -->
                    <div class="screen-content-pane d-none" id="pane-odontogram">
                        <span class="badge bg-info-subtle text-info px-3 py-1 rounded-pill fw-bold mb-3">
                            <i class="bi bi-cpu me-1"></i> السجل السريري الرقمي
                        </span>
                        <h3 class="fw-extrabold text-dark mb-3">مخطط 32 سن تفاعلي بضغطة زر واحدة</h3>
                        <p class="text-muted small leading-relaxed mb-4">
                            ودّع الأوراق ورسومات الأسنان اليدوية! وثّق كل إجراء على السن وسطوحه بنظام FDI المعتمد عالمياً، مع حفظ فوري في السجل الطبي للمريض.
                        </p>
                        <ul class="list-unstyled d-flex flex-column gap-3 mb-4">
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-info fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">تلوين فوري لحالات الأسنان:</strong>
                                    <span class="text-muted small">تمييز لوني واضح بين التسوس، الحشوات، التيجان، الزراعة، وعلاج الجذور.</span>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-info fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">تاريخ التعديلات لكل سن:</strong>
                                    <span class="text-muted small">سجل زمني يوضح الطبيب المعالج وتاريخ كل حشوة أو إجراء تم على السن.</span>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-info fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">إنشاء خطط العلاج التقديرية:</strong>
                                    <span class="text-muted small">طباعة عرض سعر وخطة علاجية للمريض بلمسة زر واحدة.</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Content for Tab: Patients -->
                    <div class="screen-content-pane d-none" id="pane-patients">
                        <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill fw-bold mb-3">
                            <i class="bi bi-people-fill me-1"></i> الملف الطبي للمريض EMR
                        </span>
                        <h3 class="fw-extrabold text-dark mb-3">سجل شامل وتاريخ مرضي محمي</h3>
                        <p class="text-muted small leading-relaxed mb-4">
                            ملف إلكتروني موحد لكل مريض يحتوي على معلوماته الشخصية، التنبيهات الطبية، الحساسية، الأمراض المزمنة، وسجل الزيارات السابقة.
                        </p>
                        <ul class="list-unstyled d-flex flex-column gap-3 mb-4">
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-success fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">تنبيهات الحساسية والأمراض المزمنة:</strong>
                                    <span class="text-muted small">شريط أحمر بارز ينبه الطبيب فور فتح الملف لوجود حساسية بنسلين أو أمراض ضغط وسكر.</span>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-success fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">أرشيف صور الأشعة والملفات:</strong>
                                    <span class="text-muted small">إمكانية رفع وحفظ صور الأشعة البانورامية وتقرير المختبر والتقويم.</span>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-success fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">بحث سريع بالاسم ورقم الهوية والهاتف:</strong>
                                    <span class="text-muted small">استرجاع ملف المريض في أقل من نصف ثانية من أي جهاز بالعيادة.</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Content for Tab: Appointments -->
                    <div class="screen-content-pane d-none" id="pane-appointments">
                        <span class="badge bg-warning-subtle text-warning px-3 py-1 rounded-pill fw-bold mb-3">
                            <i class="bi bi-calendar2-check me-1"></i> المواعيد والأجندة
                        </span>
                        <h3 class="fw-extrabold text-dark mb-3">ودّع تعارض المواعيد وفوضى الاستقبال</h3>
                        <p class="text-muted small leading-relaxed mb-4">
                            جدول حجوزات ذكي يتيح لمكتب الاستقبال تنسيق مواعيد العيادات، تتبع فترات الانتظار، وإرسال تنبيهات تلقائية للمرضى عبر الواتساب.
                        </p>
                        <ul class="list-unstyled d-flex flex-column gap-3 mb-4">
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-warning fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">تقليل نسبة التخلف (No-Shows) بـ 85%:</strong>
                                    <span class="text-muted small">تذكير تلقائي مؤتمت يرسل للمريض مع رابط تأكيد الحضور أو الاعتذار.</span>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-warning fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">تتبع دور الحضور وصالة الانتظار:</strong>
                                    <span class="text-muted small">حالات فورية: مسجل، حضر، في غرفة الكشف، تم الانتهاء، ملغي.</span>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-warning fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">جدول أطباء مخصص:</strong>
                                    <span class="text-muted small">أجندة منفصلة لكل طبيب مع إمكانية التبديل بنقرة زر واحدة.</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Content for Tab: Billing -->
                    <div class="screen-content-pane d-none" id="pane-billing">
                        <span class="badge bg-danger-subtle text-danger px-3 py-1 rounded-pill fw-bold mb-3">
                            <i class="bi bi-receipt-cutoff me-1"></i> الفوترة والأقساط و ZATCA
                        </span>
                        <h3 class="fw-extrabold text-dark mb-3">محاسبة طبية دقيقة متوافقة نظامياً</h3>
                        <p class="text-muted small leading-relaxed mb-4">
                            إصدار فواتير ضريبية مبسطة معتمدة بـ QR Code متوافق مع هيئة الزكاة والضريبة والجمارك (ZATCA)، مع جدول أقساط مرن لحالات التقويم والزراعة.
                        </p>
                        <ul class="list-unstyled d-flex flex-column gap-3 mb-4">
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-danger fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">فواتير إلكترونية متوافقة 100%:</strong>
                                    <span class="text-muted small">توليد الفاتورة مع رمز الاستجابة السريع المشفر وحساب الضريبة تلقائياً.</span>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-danger fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">جدولة الأقساط والدفعات:</strong>
                                    <span class="text-muted small">توزيع تكلفة العلاج على جلسات ومتابعة المتبقي والمستحق بدقة.</span>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-danger fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">حساب نسب وعمولات الأطباء:</strong>
                                    <span class="text-muted small">حساب مستحقات كل طبيب بناء على الإجراءات المحصلة بدون تدخل يدوي.</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Content for Tab: Labs -->
                    <div class="screen-content-pane d-none" id="pane-labs">
                        <span class="badge bg-teal-subtle text-teal px-3 py-1 rounded-pill fw-bold mb-3" style="background: #ccfbf1; color: #0d9488;">
                            <i class="bi bi-box-seam me-1"></i> معامل الأسنان والتركيبات
                        </span>
                        <h3 class="fw-extrabold text-dark mb-3">تتبع كامل لتركيبات الأسنان ودرجات الألوان</h3>
                        <p class="text-muted small leading-relaxed mb-4">
                            إدارة متقدمة لطلبات التركيبات الخارجية مع المعامل؛ من توثيق نوع المادة (زيركون، إيماكس، بورسلين) ودرجة اللون (Shades) وحتى موعد التسليم للعيادة.
                        </p>
                        <ul class="list-unstyled d-flex flex-column gap-3 mb-4">
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-teal fs-5 mt-1" style="color: #0d9488;"></i>
                                <div>
                                    <strong class="text-dark d-block">تحديد درجات اللون بدقة (Shade Guide):</strong>
                                    <span class="text-muted small">تسجيل كود اللون المطلوب (A1, A2, B1, BL1) ورقم السن المعني لتفادي أي أخطاء في التصنيع.</span>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-teal fs-5 mt-1" style="color: #0d9488;"></i>
                                <div>
                                    <strong class="text-dark d-block">تتبع المراحل مع المعمل لحظة بلحظة:</strong>
                                    <span class="text-muted small">حالات فورية: قيد التصنيع بالمعمل، جاهز للاستلام، تم التركيب والتسليم للمريض.</span>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-teal fs-5 mt-1" style="color: #0d9488;"></i>
                                <div>
                                    <strong class="text-dark d-block">حساب تكاليف المعمل وهامش الربح:</strong>
                                    <span class="text-muted small">حساب التكلفة الفعلية ومقارنتها بسعر البيع للمريض لحساب ربحية قسم التركيبات بدقة.</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Content for Tab: Expenses -->
                    <div class="screen-content-pane d-none" id="pane-expenses">
                        <span class="badge bg-warning-subtle text-warning px-3 py-1 rounded-pill fw-bold mb-3">
                            <i class="bi bi-wallet2 me-1"></i> المصروفات وصافي الأرباح
                        </span>
                        <h3 class="fw-extrabold text-dark mb-3">ضبط شامل لنفقات العيادة وإيصالات الدفع</h3>
                        <p class="text-muted small leading-relaxed mb-4">
                            توثيق كافة النفقات التشغيلية؛ من إيجارات ورواتب ومشتريات خامات طبية، مع تقنية الضغط الذكي لصور الفواتير لحفظ مساحة التخزين.
                        </p>
                        <ul class="list-unstyled d-flex flex-column gap-3 mb-4">
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-warning fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">تصنيف دقيق للمصروفات:</strong>
                                    <span class="text-muted small">تبويب النفقات (مستلزمات أسنان، صيانة وتعقيم، رواتب، إيجارات ومرافق، تسويق).</span>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-warning fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">ضغط وحفظ صور الإيصالات آلياً:</strong>
                                    <span class="text-muted small">رفع فواتير الموردين مع ضغط تلقائي ذكي يحافظ على جودة القراءة ويوفر سرعة تحميل هائلة.</span>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-warning fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">حساب صافي الأرباح اللحظي:</strong>
                                    <span class="text-muted small">طرح المصروفات وتكاليف المعامل من المقبوضات لإظهار صافي الربح الفعلي لإدارة العيادة.</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Content for Tab: Users & Security -->
                    <div class="screen-content-pane d-none" id="pane-users">
                        <span class="badge bg-secondary-subtle text-dark px-3 py-1 rounded-pill fw-bold mb-3">
                            <i class="bi bi-shield-lock me-1"></i> الصلاحيات والأمان (RBAC)
                        </span>
                        <h3 class="fw-extrabold text-dark mb-3">أمان مشفر وصلاحيات دقيقة لكل موظف</h3>
                        <p class="text-muted small leading-relaxed mb-4">
                            تحكم كامل في كادر العيادة؛ تحديد أدوار وصلاحيات واضحة لكل فرد لحماية خصوصية وسرية السجلات الطبية والمالية.
                        </p>
                        <ul class="list-unstyled d-flex flex-column gap-3 mb-4">
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-dark fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">4 أدوار أمنية محكمة:</strong>
                                    <span class="text-muted small">صلاحيات مخصصة للمدير (Admin)، طبيب الأسنان (Doctor)، الاستقبال (Receptionist)، والمحاسب (Accountant).</span>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-dark fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">تجميد وتنشيط الحسابات بضغطة زر:</strong>
                                    <span class="text-muted small">إمكانية تعطيل وصول أي موظف فوراً لحماية أمان العيادة بدون حذف سجلاته القديمة.</span>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-dark fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark d-block">حفظ خصوصية السجلات المالية:</strong>
                                    <span class="text-muted small">منع الطاقم الطبي والاستقبال من الاطلاع على تقارير الأرباح والمصروفات الخاصة بالإدارة.</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Open Full Lightbox Button -->
                    <div class="pt-3 border-top mt-auto">
                        <button type="button" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm" id="openActiveLightboxBtn">
                            <i class="bi bi-arrows-fullscreen me-2"></i> استعراض الشاشة الحالية بدقة كاملة
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>
</section>

<!-- ==========================================================
     5. DEDICATED SECTION: 4 TAILORED ROLE DASHBOARDS
     ========================================================== -->
<section class="py-5 bg-white position-relative border-top border-bottom">
    <div class="container py-lg-4">
        <div class="text-center max-w-750 mx-auto mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-person-workspace me-1"></i> بيئة عمل مخصصة لكل تخصص
            </span>
            <h2 class="display-6 fw-extrabold text-dark mt-2">
                4 لوحات تحكم متخصصة مصممة لكل فرد في طاقم العيادة
            </h2>
            <p class="text-muted fs-6">
                لا نضع الجميع في شاشة واحدة مزدحمة! كل مستخدم في عيادتك يحصل على لوحة قيادة ذكية ومبسطة تركز حصراً على مهامه اليومية.
            </p>
        </div>

        <div class="row g-4">
            <!-- 1. Doctor Dashboard -->
            <div class="col-md-6 col-lg-3">
                <div class="role-card h-100">
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 d-inline-flex mb-3">
                        <i class="bi bi-heart-pulse-fill fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">لوحة طبيب الأسنان</h5>
                    <span class="badge bg-primary-subtle text-primary mb-3">Doctor Dashboard</span>
                    <p class="text-muted small mb-3">
                        شاشة سريرية فورية تركز على راحة الطبيب أثناء الكشف وتقديم العلاج:
                    </p>
                    <ul class="list-unstyled small text-secondary d-flex flex-column gap-2 mb-0">
                        <li><i class="bi bi-check2 text-primary me-1"></i> قائمة المرضى المنتظرين بصالة الاستقبال</li>
                        <li><i class="bi bi-check2 text-primary me-1"></i> جدول مواعيد وجلسات الطبيب لليوم</li>
                        <li><i class="bi bi-check2 text-primary me-1"></i> مخطط الأسنان FDI السريع بنقرة زر</li>
                        <li><i class="bi bi-check2 text-primary me-1"></i> متابعة طلبيات المعامل والتركيبات الجاهزة</li>
                        <li><i class="bi bi-check2 text-primary me-1"></i> تقرير الحالات المنجزة ونسبة العمولة</li>
                    </ul>
                </div>
            </div>

            <!-- 2. Receptionist Dashboard -->
            <div class="col-md-6 col-lg-3">
                <div class="role-card h-100">
                    <div class="rounded-circle bg-info-subtle text-info p-3 d-inline-flex mb-3">
                        <i class="bi bi-calendar2-week-fill fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">لوحة مكتب الاستقبال</h5>
                    <span class="badge bg-info-subtle text-info mb-3">Reception Desk</span>
                    <p class="text-muted small mb-3">
                        واجهة سريعة وخفيفة لتنظيم تدفق المرضى والحجوزات دون أي انتظار:
                    </p>
                    <ul class="list-unstyled small text-secondary d-flex flex-column gap-2 mb-0">
                        <li><i class="bi bi-check2 text-info me-1"></i> تسجيل وصول المريض بنقرة واحدة</li>
                        <li><i class="bi bi-check2 text-info me-1"></i> إدارة صالة الانتظار وتوزيع غرف الكشف</li>
                        <li><i class="bi bi-check2 text-info me-1"></i> حجز سريع للمواعيد الجديدة والمستعجلة</li>
                        <li><i class="bi bi-check2 text-info me-1"></i> تذكير المرضى آلياً عبر الواتساب</li>
                        <li><i class="bi bi-check2 text-info me-1"></i> بحث فوري عن الملفات برقم الهاتف والاسم</li>
                    </ul>
                </div>
            </div>

            <!-- 3. Accountant Dashboard -->
            <div class="col-md-6 col-lg-3">
                <div class="role-card h-100">
                    <div class="rounded-circle bg-warning-subtle text-warning p-3 d-inline-flex mb-3">
                        <i class="bi bi-receipt-cutoff fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">لوحة المحاسب والمالية</h5>
                    <span class="badge bg-warning-subtle text-warning mb-3">Accountant Hub</span>
                    <p class="text-muted small mb-3">
                        مركز تحكم مالي دقيق لمتابعة المقبوضات والأقساط والامتثال الضريبي:
                    </p>
                    <ul class="list-unstyled small text-secondary d-flex flex-column gap-2 mb-0">
                        <li><i class="bi bi-check2 text-warning me-1"></i> مقبوضات اليوم النقدية والشبكة والتحويلات</li>
                        <li><i class="bi bi-check2 text-warning me-1"></i> متابعة الأقساط المتأخرة والديون المستحقة</li>
                        <li><i class="bi bi-check2 text-warning me-1"></i> إصدار الفواتير الإلكترونية ZATCA وسندات القبض</li>
                        <li><i class="bi bi-check2 text-warning me-1"></i> تتبع المصروفات التشغيلية وإيصالات الدفع</li>
                        <li><i class="bi bi-check2 text-warning me-1"></i> حساب عمولات ومستحقات كل طبيب آلياً</li>
                    </ul>
                </div>
            </div>

            <!-- 4. Admin Dashboard -->
            <div class="col-md-6 col-lg-3">
                <div class="role-card h-100">
                    <div class="rounded-circle bg-success-subtle text-success p-3 d-inline-flex mb-3">
                        <i class="bi bi-speedometer2 fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">لوحة المدير والمالك</h5>
                    <span class="badge bg-success-subtle text-success mb-3">Executive Admin</span>
                    <p class="text-muted small mb-3">
                        رؤية بانورامية استراتيجية تمنح الإدارة السيطرة الكاملة على نمو العيادة:
                    </p>
                    <ul class="list-unstyled small text-secondary d-flex flex-column gap-2 mb-0">
                        <li><i class="bi bi-check2 text-success me-1"></i> المؤشرات الحيوية الشاملة (KPIs) ونمو الإيرادات</li>
                        <li><i class="bi bi-check2 text-success me-1"></i> تقارير إنتاجية الأطباء ونسب الإنجاز</li>
                        <li><i class="bi bi-check2 text-success me-1"></i> صافي الأرباح الشهرية بعد خصم المصاريف</li>
                        <li><i class="bi bi-check2 text-success me-1"></i> إدارة المستخدمين وصلاحيات الوصول والأمان</li>
                        <li><i class="bi bi-check2 text-success me-1"></i> تخصيص العملة، الضرائب، وإعدادات المركز</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================
     6. CORE FEATURES & MODULES GRID (12 مزية شاملة)
     ========================================================== -->
<section class="py-5 bg-light position-relative" id="features">
    <div class="container py-lg-4">
        <div class="text-center max-w-750 mx-auto mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-stars me-1"></i> المنظومة الشاملة المتكاملة
            </span>
            <h2 class="display-6 fw-extrabold text-dark mt-2">
                12 وحدة متخصصة تغطي كل تفصيلة في إدارة عيادة الأسنان
            </h2>
            <p class="text-muted fs-6">
                صممت Dental Pro ERP لتغني عيادتك عن استخدام عدة برامج متفرقة. كل أداة تحتاجها موجودة ومترابطة سحابياً.
            </p>
        </div>

        <div class="row g-4">
            <!-- 1. EMR -->
            <div class="col-md-6 col-lg-3">
                <div class="saas-feature-card">
                    <div class="saas-feature-icon-box bg-primary-subtle text-primary">
                        <i class="bi bi-clipboard2-pulse-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">الملف الطبي الرقمي EMR</h5>
                    <p class="text-muted small mb-3">
                        سجل مريض شامل، التاريخ المرضي، الحساسية الدوائية، الأمراض المزمنة، ورفع صور الأشعة السينية والبانوراما.
                    </p>
                    <span class="badge bg-light text-primary border small">أمان طبي كامل</span>
                </div>
            </div>

            <!-- 2. Odontogram -->
            <div class="col-md-6 col-lg-3">
                <div class="saas-feature-card">
                    <div class="saas-feature-icon-box bg-info-subtle text-info">
                        <i class="bi bi-cpu-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">مخطط الأسنان FDI التفاعلي</h5>
                    <p class="text-muted small mb-3">
                        تخطيط تفاعلي للأسنان الـ 32 بنظام FDI الدولي، توثيق فوري للتسوس، الحشوات، التيجان، والزراعة بنقرة واحدة.
                    </p>
                    <span class="badge bg-light text-info border small">تحديث فوري بالثواني</span>
                </div>
            </div>

            <!-- 3. Appointments -->
            <div class="col-md-6 col-lg-3">
                <div class="saas-feature-card">
                    <div class="saas-feature-icon-box bg-success-subtle text-success">
                        <i class="bi bi-calendar2-week-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">الأجندة والمواعيد الذكية</h5>
                    <p class="text-muted small mb-3">
                        جدولة مواعيد الأطباء، غرف الكشف، قائمة الانتظار، وتتبع تدفق المرضى من صالة الاستقبال إلى غرفة المعاينة.
                    </p>
                    <span class="badge bg-light text-success border small">منع التداخل 100%</span>
                </div>
            </div>

            <!-- 4. WhatsApp Bot -->
            <div class="col-md-6 col-lg-3">
                <div class="saas-feature-card">
                    <div class="saas-feature-icon-box bg-warning-subtle text-warning">
                        <i class="bi bi-whatsapp"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">تذكيرات واتساب التلقائية</h5>
                    <p class="text-muted small mb-3">
                        إرسال رسائل تذكير آلية للمرضى قبل الموعد بـ 24 ساعة، مما يقلل حالات التخلف عن الحضور بنسبة تتجاوز 85%.
                    </p>
                    <span class="badge bg-light text-warning border small">توفير وقت الاستقبال</span>
                </div>
            </div>

            <!-- 5. ZATCA e-Invoicing -->
            <div class="col-md-6 col-lg-3">
                <div class="saas-feature-card">
                    <div class="saas-feature-icon-box bg-danger-subtle text-danger">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">الفواتير الإلكترونية ZATCA</h5>
                    <p class="text-muted small mb-3">
                        فواتير ضريبية مبسطة مع رمز استجابة سريع QR Code مشفر متوافق بالكامل مع هيئة الزكاة والضريبة والجمارك.
                    </p>
                    <span class="badge bg-light text-danger border small">مطابق للمرحلة الثانية</span>
                </div>
            </div>

            <!-- 6. Installments -->
            <div class="col-md-6 col-lg-3">
                <div class="saas-feature-card">
                    <div class="saas-feature-icon-box bg-purple-subtle text-purple" style="background: #f3e8ff; color: #9333ea;">
                        <i class="bi bi-credit-card-2-front-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">إدارة الأقساط والدفعات</h5>
                    <p class="text-muted small mb-3">
                        جدولة أقساط خطط العلاج الطويلة كالتقويم والزراعة، إصدار سندات القبض، وتتبع الدفعات المتأخرة والمسددة.
                    </p>
                    <span class="badge bg-light text-secondary border small">تحصيل مالي سلس</span>
                </div>
            </div>

            <!-- 7. Lab Orders & Shade Guide -->
            <div class="col-md-6 col-lg-3">
                <div class="saas-feature-card">
                    <div class="saas-feature-icon-box bg-teal-subtle text-teal" style="background: #ccfbf1; color: #0d9488;">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">طلبيات المعامل ودرجات الألوان</h5>
                    <p class="text-muted small mb-3">
                        تتبع تركيبات الأسنان (زيركون، إيماكس، جسور) مع تحديد درجات اللون (Shade Guide: A1, A2, BL1) وتواريخ الاستلام.
                    </p>
                    <span class="badge bg-light text-secondary border small">تسليم بدون تأخير</span>
                </div>
            </div>

            <!-- 8. Expenses & Net Profit -->
            <div class="col-md-6 col-lg-3">
                <div class="saas-feature-card">
                    <div class="saas-feature-icon-box bg-warning-subtle text-dark" style="background: #fef3c7; color: #d97706;">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">المصروفات وصافي الأرباح</h5>
                    <p class="text-muted small mb-3">
                        توثيق مصاريف العيادة (إيجار، خامات، صيانة، رواتب)، وإرفاق صور الإيصالات مع احتساب صافي الأرباح شهرياً وسنوياً.
                    </p>
                    <span class="badge bg-light text-warning border small">رقابة مالية تامة</span>
                </div>
            </div>

            <!-- 9. Compressed Image Engine -->
            <div class="col-md-6 col-lg-3">
                <div class="saas-feature-card">
                    <div class="saas-feature-icon-box bg-info-subtle text-info">
                        <i class="bi bi-file-earmark-zip-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">تقنية الضغط الذكي للمرفقات</h5>
                    <p class="text-muted small mb-3">
                        ضغط فائق للصور والإيصالات وأشعة الأسنان لتوفير مساحة التخزين السحابية وتسريع تصفح الملفات الطبية لحظياً.
                    </p>
                    <span class="badge bg-light text-info border small">سرعة فائقة وخفة</span>
                </div>
            </div>

            <!-- 10. Doctor Commissions Engine -->
            <div class="col-md-6 col-lg-3">
                <div class="saas-feature-card">
                    <div class="saas-feature-icon-box bg-success-subtle text-success">
                        <i class="bi bi-pie-chart-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">حساب عمولات ونسب الأطباء</h5>
                    <p class="text-muted small mb-3">
                        احتساب فوري لنسب الأطباء وعمولات العمليات بمجرد سداد الفاتورة، مع تقرير مفصل لكل طبيب يمنع أي لبس حسابي.
                    </p>
                    <span class="badge bg-light text-success border small">شفافية مطلقة</span>
                </div>
            </div>

            <!-- 11. Multi-Currency & Clinic Profile -->
            <div class="col-md-6 col-lg-3">
                <div class="saas-feature-card">
                    <div class="saas-feature-icon-box bg-primary-subtle text-primary">
                        <i class="bi bi-currency-exchange"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">العملات المتعددة والتهيئة</h5>
                    <p class="text-muted small mb-3">
                        دعم لكافة العملات (ر.س، ج.م، د.إ، د.ك، $) وطرق الدفع (نقدي، مدى، تحويل، محافظ) مع تخصيص شعار وترويسة العيادة.
                    </p>
                    <span class="badge bg-light text-primary border small">تخصيص كامل</span>
                </div>
            </div>

            <!-- 12. Multi-Role RBAC & HIPAA Security -->
            <div class="col-md-6 col-lg-3">
                <div class="saas-feature-card">
                    <div class="saas-feature-icon-box bg-secondary-subtle text-dark">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">أمان مشفر وصلاحيات RBAC</h5>
                    <p class="text-muted small mb-3">
                        صلاحيات دقيقة للأطباء، الاستقبال، والمحاسب، مع تجميد الحسابات بنقرة زر وحماية البيانات الطبية وفق معايير HIPAA.
                    </p>
                    <span class="badge bg-light text-dark border small">حماية وسرية تامة</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================
     7. CLINIC WORKFLOW / PATIENT JOURNEY (رحلة المريض)
     ========================================================== -->
<section class="py-5 bg-white position-relative border-bottom" id="workflow">
    <div class="container py-lg-4">
        <div class="text-center max-w-750 mx-auto mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-arrow-repeat me-1"></i> دورة عمل متكاملة
            </span>
            <h2 class="display-6 fw-extrabold text-dark mt-2">
                كيف يدعم Dental Pro عيادتك في كل خطوة مع المريض؟
            </h2>
            <p class="text-muted fs-6">
                من لحظة اتصال المريض وحتى اكتمال علاجه وسداد الفاتورة، كل مرحلة مؤتمتة ومترابطة بسلاسة تامة.
            </p>
        </div>

        <div class="row g-4 position-relative">
            <!-- Step 1 -->
            <div class="col-md-6 col-lg">
                <div class="clinic-card p-4 h-100 text-center text-md-start">
                    <div class="badge bg-primary text-white fs-6 rounded-pill px-3 py-1 mb-3">الخطوة 1</div>
                    <h5 class="fw-bold text-dark">الحجز الذكي وتأكيد الواتساب</h5>
                    <p class="text-muted small mb-0">
                        تسجيل الموعد في ثوانٍ مع اختيار الطبيب والعيادة، وإرسال رسالة ترحيبية وتذكير فوري للمريض عبر الواتساب.
                    </p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="col-md-6 col-lg">
                <div class="clinic-card p-4 h-100 text-center text-md-start">
                    <div class="badge bg-info text-white fs-6 rounded-pill px-3 py-1 mb-3">الخطوة 2</div>
                    <h5 class="fw-bold text-dark">الاستقبال والملف الطبي</h5>
                    <p class="text-muted small mb-0">
                        فتح السجل الطبي، تسجيل التنبيهات الصحية وحساسية الأدوية، ونقل المريض لصالة الانتظار بضغطة زر.
                    </p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="col-md-6 col-lg">
                <div class="clinic-card p-4 h-100 text-center text-md-start">
                    <div class="badge bg-success text-white fs-6 rounded-pill px-3 py-1 mb-3">الخطوة 3</div>
                    <h5 class="fw-bold text-dark">الكشف ومخطط الأسنان</h5>
                    <p class="text-muted small mb-0">
                        يقوم الطبيب بتحديد الإجراء على مخطط الأسنان التفاعلي (تسوس، علاج عصب، تقويم) وتوليد خطة العلاج.
                    </p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="col-md-6 col-lg">
                <div class="clinic-card p-4 h-100 text-center text-md-start">
                    <div class="badge bg-warning text-dark fs-6 rounded-pill px-3 py-1 mb-3">الخطوة 4</div>
                    <h5 class="fw-bold text-dark">أمر المعمل وجدولة الجلسات</h5>
                    <p class="text-muted small mb-0">
                        إرسال طلب التركيبات لمعمل الأسنان مع تحديد درجة اللون والقياسات، وتحديد مواعيد الجلسات القادمة.
                    </p>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="col-md-6 col-lg">
                <div class="clinic-card p-4 h-100 text-center text-md-start">
                    <div class="badge bg-danger text-white fs-6 rounded-pill px-3 py-1 mb-3">الخطوة 5</div>
                    <h5 class="fw-bold text-dark">الفاتورة الضريبية ZATCA</h5>
                    <p class="text-muted small mb-0">
                        إصدار فاتورة ضريبية رسمية بـ QR Code وسند قبض، مع إمكانية تجزئة الدفعات ضمن خطة أقساط معتمدة.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================
     8. MASTER FEATURES MATRIX (مصفوفة الإمكانيات الشاملة)
     ========================================================== -->
<section class="py-5 bg-light position-relative">
    <div class="container py-lg-4">
        <div class="text-center max-w-750 mx-auto mb-5">
            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-check2-all me-1"></i> فحص المواصفات الشامل
            </span>
            <h2 class="display-6 fw-extrabold text-dark mt-2">
                مصفوفة إمكانيات ومميزات برنامج Dental Pro الكاملة
            </h2>
            <p class="text-muted fs-6">
                قائمة تفصيلية تؤكد تغطية النظام لكافة احتياجات عيادتك الطبية والإدارية والمالية بنسبة 100%.
            </p>
        </div>

        <div class="row g-4">
            <!-- 1. Clinical -->
            <div class="col-md-6 col-lg-3">
                <div class="clinic-card p-4 h-100 bg-white">
                    <div class="d-flex align-items-center gap-2 mb-3 text-primary">
                        <i class="bi bi-heart-pulse fs-4"></i>
                        <h6 class="fw-bold text-dark mb-0">السجل السريري والطبي</h6>
                    </div>
                    <ul class="list-unstyled small d-flex flex-column gap-2 text-secondary mb-0">
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> مخطط تفاعلي لـ 32 سن (FDI)</li>
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> 6 حالات سريرية ملونة لكل سن</li>
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> توثيق أسطح السن التفصيلية</li>
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> شريط التنبيهات للحساسية والضغط</li>
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> أرشفة الأشعة البانورامية والمستندات</li>
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> توليد وطباعة خطط العلاج الملونة</li>
                    </ul>
                </div>
            </div>

            <!-- 2. Reception & Appointments -->
            <div class="col-md-6 col-lg-3">
                <div class="clinic-card p-4 h-100 bg-white">
                    <div class="d-flex align-items-center gap-2 mb-3 text-info">
                        <i class="bi bi-calendar2-check fs-4"></i>
                        <h6 class="fw-bold text-dark mb-0">الاستقبال والمواعيد</h6>
                    </div>
                    <ul class="list-unstyled small d-flex flex-column gap-2 text-secondary mb-0">
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> أجندة وجدول مخصص لكل طبيب</li>
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> إدارة طابور صالة الانتظار الحية</li>
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> حالات تدفق المريض (مجدول، حضر...)</li>
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> تذكيرات واتساب مؤتمتة للمرضى</li>
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> بحث فوري بالاسم والجوال والهوية</li>
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> تقليل الغياب والتخلف بنسبة 85%</li>
                    </ul>
                </div>
            </div>

            <!-- 3. Billing & ZATCA -->
            <div class="col-md-6 col-lg-3">
                <div class="clinic-card p-4 h-100 bg-white">
                    <div class="d-flex align-items-center gap-2 mb-3 text-danger">
                        <i class="bi bi-receipt-cutoff fs-4"></i>
                        <h6 class="fw-bold text-dark mb-0">المحاسبة و ZATCA</h6>
                    </div>
                    <ul class="list-unstyled small d-flex flex-column gap-2 text-secondary mb-0">
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> فواتير ضريبية معتمدة مع QR Code</li>
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> سندات قبض وصرف فورية</li>
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> جدولة الأقساط (تقويم وزراعة)</li>
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> حساب عمولات الأطباء آلياً</li>
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> طرق دفع متعددة (نقدي، مدى، تحويل)</li>
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> دعم العملات المتعددة وموضع الرمز</li>
                    </ul>
                </div>
            </div>

            <!-- 4. Labs, Expenses & Tech -->
            <div class="col-md-6 col-lg-3">
                <div class="clinic-card p-4 h-100 bg-white">
                    <div class="d-flex align-items-center gap-2 mb-3 text-warning">
                        <i class="bi bi-gear-wide-connected fs-4"></i>
                        <h6 class="fw-bold text-dark mb-0">المعامل والمصروفات والأمان</h6>
                    </div>
                    <ul class="list-unstyled small d-flex flex-column gap-2 text-secondary mb-0">
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> تتبع طلبيات المعامل ودرجات الألوان</li>
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> توثيق المصروفات وصافي الأرباح</li>
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> ضغط ذكي فوري لصور الإيصالات</li>
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> 4 صلاحيات أمنية محكمة (RBAC)</li>
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> توافق كامل مع الآيباد والتابلت</li>
                        <li><i class="bi bi-check-circle-fill text-success me-1"></i> سحابي 100% مع نسخ احتياطي يومي</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================
     9. INTERACTIVE ROI & SAVINGS CALCULATOR (حاسبة العائد)
     ========================================================== -->
<section class="py-5 bg-white position-relative" id="calculator">
    <div class="container py-lg-4">
        <div class="clinic-card p-4 p-md-5 border-2 shadow-xl" style="background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 100%); border-color: #bae6fd;">
            <div class="row align-items-center g-5">
                
                <div class="col-lg-6">
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold mb-3">
                        <i class="bi bi-calculator-fill me-1"></i> حاسبة توفير العيادة الذكية
                    </span>
                    <h2 class="display-6 fw-extrabold text-dark mb-3">
                        احسب الوقت والإيرادات التي سيوفرها لك Dental Pro شهرياً
                    </h2>
                    <p class="text-muted small leading-relaxed mb-4">
                        حرك المؤشرات أدناه وفقاً لحجم عيادتك لترى الأثر المالي والزمني المباشر الذي يحققه النظام لطاقمك الطبي وإدارتك.
                    </p>

                    <!-- Slider 1: Chairs / Doctors -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-dark mb-0">عدد كراسي / أطباء العيادة:</label>
                            <span class="badge bg-primary fs-6 px-3 py-1" id="chairsValueBadge">3 أطباء</span>
                        </div>
                        <input type="range" class="roi-slider" id="chairsRange" min="1" max="15" value="3">
                        <div class="d-flex justify-content-between text-muted small mt-1">
                            <span>طبيب واحد (عيادة مستقلة)</span>
                            <span>15 طبيب (مجمع طبي كبير)</span>
                        </div>
                    </div>

                    <!-- Slider 2: Daily Patients -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-dark mb-0">متوسط عدد المرضى يومياً:</label>
                            <span class="badge bg-info text-white fs-6 px-3 py-1" id="patientsValueBadge">30 مريض</span>
                        </div>
                        <input type="range" class="roi-slider" id="patientsRange" min="10" max="150" step="5" value="30">
                        <div class="d-flex justify-content-between text-muted small mt-1">
                            <span>10 مرضى / يوم</span>
                            <span>150 مريض / يوم</span>
                        </div>
                    </div>
                </div>

                <!-- Calculator Real-Time Outputs -->
                <div class="col-lg-6">
                    <div class="bg-white p-4 p-md-5 rounded-4 border shadow-sm">
                        <h5 class="fw-bold text-dark mb-4 border-bottom pb-3">العائد والوفر الشهري المتوقع لعيادتك:</h5>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3 text-center">
                                    <div class="display-6 fw-extrabold text-primary mb-1" id="hoursSavedOutput">54</div>
                                    <div class="fw-bold text-dark small">ساعة عمل موفرة شهرياً</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">في توثيق الملفات والاستقبال</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3 text-center">
                                    <div class="display-6 fw-extrabold text-success mb-1" id="revenueProtectedOutput">8,400</div>
                                    <div class="fw-bold text-dark small">ر.س إيراد محمي شهرياً</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">بفضل منع غياب وتخلف المرضى</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="p-3 bg-success-subtle rounded-3 d-flex align-items-center justify-content-between text-success">
                                    <div>
                                        <div class="fw-bold">نسبة تسريع تحصيل الفواتير والأقساط:</div>
                                        <div class="small text-success-emphasis">متابعة آلية لمستحقات المرضى وتقليل الديون المعدومة</div>
                                    </div>
                                    <div class="fs-3 fw-extrabold">+42%</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-2 text-center">
                            <a href="#requestDemoModal" data-bs-toggle="modal" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                                <i class="bi bi-calendar-check-fill me-1"></i> احصل على استشارة مجانية وعرض حي لعيادتك
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- ==========================================================
     10. TRADITIONAL VS DENTAL PRO ERP (مقارنة صريحة)
     ========================================================== -->
<section class="py-5 bg-white border-top border-bottom">
    <div class="container py-lg-4">
        <div class="text-center max-w-750 mx-auto mb-5">
            <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-arrow-left-right me-1"></i> مقارنة الفارق الحقيقي
            </span>
            <h2 class="display-6 fw-extrabold text-dark mt-2">
                الفرق بين الطرق التقليدية والعمل بنظام Dental Pro ERP
            </h2>
            <p class="text-muted fs-6">
                انظر كيف تتحول تفاصيل العمل اليومية المعقدة إلى خطوات رقمية سلسة ودقيقة.
            </p>
        </div>

        <div class="row g-4 align-items-stretch">
            <!-- Traditional Method Column -->
            <div class="col-md-6">
                <div class="p-4 p-md-5 rounded-4 border bg-danger-subtle bg-opacity-25 h-100 border-danger-subtle">
                    <div class="d-flex align-items-center gap-2 mb-4 text-danger">
                        <i class="bi bi-x-circle-fill fs-3"></i>
                        <h4 class="fw-bold mb-0">الطرق التقليدية والورقية</h4>
                    </div>

                    <ul class="list-unstyled d-flex flex-column gap-3 text-secondary mb-0">
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-dash-circle text-danger mt-1"></i>
                            <span>ملفات ورقية معرضة للتلف والضياع وتأخير طويل للبحث عن ملف المريض.</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-dash-circle text-danger mt-1"></i>
                            <span>تخلف المرضى عن المواعيد بنسبة 20-30% بسبب نسيان الموعد وغياب التذكير.</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-dash-circle text-danger mt-1"></i>
                            <span>أخطاء بشرية في حسابات ضريبة القيمة المضافة ومخاطر عدم التوافق مع الزكاة.</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-dash-circle text-danger mt-1"></i>
                            <span>صعوبة تتبع تركيبات الأسنان وحالات المعمل مما يسبب إحراجاً مع المريض.</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-dash-circle text-danger mt-1"></i>
                            <span>خلافات مستمرة مع الأطباء حول حساب نسب العمل والعمولات يدوياً.</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Dental Pro ERP Modern Way -->
            <div class="col-md-6">
                <div class="p-4 p-md-5 rounded-4 border bg-success-subtle bg-opacity-25 h-100 border-success-subtle">
                    <div class="d-flex align-items-center gap-2 mb-4 text-success">
                        <i class="bi bi-check-circle-fill fs-3"></i>
                        <h4 class="fw-bold mb-0">مع منظومة Dental Pro ERP</h4>
                    </div>

                    <ul class="list-unstyled d-flex flex-column gap-3 text-dark mb-0">
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-success mt-1"></i>
                            <span>ملف طبي سحابي فوري EMR يُفتح في ثانية واحدة مع مخطط أسنان ملون 32 سن.</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-success mt-1"></i>
                            <span>تذكيرات آلية عبر الواتساب تقلل غياب المرضى إلى أقل من 4% فقط.</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-success mt-1"></i>
                            <span>فواتير ضريبية فورية بـ QR Code متوافق 100% مع هيئة الزكاة والضريبة (ZATCA).</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-success mt-1"></i>
                            <span>نظام متكامل لتتبع طلبات المعمل الخارجية وتواريخ الاستلام بدقة متناهية.</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-success mt-1"></i>
                            <span>حساب آلي دقيق لنسب وعمولات كل طبيب فور سداد المريض لمنع أي أخطاء.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================
     11. PRICING & SUBSCRIPTION PLANS (الباقات والأسعار)
     ========================================================== -->
<section class="py-5 bg-light position-relative" id="pricing">
    <div class="container py-lg-4">
        <div class="text-center max-w-750 mx-auto mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-tag-fill me-1"></i> أسعار شفافة ومناسبة
            </span>
            <h2 class="display-6 fw-extrabold text-dark mt-2">
                باقات مرنة تناسب كل حجم عيادة
            </h2>
            <p class="text-muted fs-6">
                سواء كنت طبيباً مستقلاً في عيادة خاصة أو مجمعاً طبياً متعدد العيادات والأطباء، لدينا الخطة المثالية لنمو مركزك.
            </p>

            <!-- Billing Cycle Switcher Toggle -->
            <div class="d-inline-flex align-items-center gap-3 p-2 bg-white rounded-pill border shadow-sm mt-3">
                <span class="fw-semibold small px-2" id="monthlyLabel">دفع شهري</span>
                <div class="form-check form-switch m-0 d-flex align-items-center">
                    <input class="form-check-input fs-5 m-0" type="checkbox" id="pricingCycleSwitch" style="cursor: pointer;">
                </div>
                <span class="fw-semibold small px-2 text-primary" id="annualLabel">
                    دفع سنوي <span class="badge bg-success-subtle text-success rounded-pill">وفر 20%</span>
                </span>
            </div>
        </div>

        <div class="row g-4 align-items-stretch">
            
            <!-- Plan 1: Solo Clinic -->
            <div class="col-lg-4">
                <div class="pricing-card">
                    <h5 class="fw-bold text-dark mb-1">العيادة المستقلة (Solo)</h5>
                    <p class="text-muted small mb-4">مثالية لعيادة الطبيب الخاص بعيادة واحدة.</p>

                    <div class="mb-4">
                        <span class="display-5 fw-extrabold text-dark price-val" data-monthly="199" data-annual="159">199</span>
                        <span class="text-muted">ر.س / شهرياً</span>
                        <div class="small text-muted mt-1 billing-note">تُدفع شهرياً</div>
                    </div>

                    <ul class="list-unstyled d-flex flex-column gap-3 small mb-4 flex-grow-1">
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>طبيب واحد + حساب استقبال</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>ملفات وسجلات مرضى غير محدودة</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>مخطط الأسنان التفاعلي Odontogram</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>الفواتير الإلكترونية المعتمدة ZATCA</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 text-muted">
                            <i class="bi bi-x-circle text-muted"></i>
                            <span>إدارة طلبات المعامل الخارجية</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 text-muted">
                            <i class="bi bi-x-circle text-muted"></i>
                            <span>حسابات عمولات الأطباء المتعددين</span>
                        </li>
                    </ul>

                    <a href="#requestDemoModal" data-bs-toggle="modal" class="btn btn-outline-primary w-100 py-3 rounded-pill fw-bold">
                        ابدأ تجربة مجانية 14 يوماً
                    </a>
                </div>
            </div>

            <!-- Plan 2: Pro Dental Center (Featured) -->
            <div class="col-lg-4">
                <div class="pricing-card featured">
                    <span class="pricing-badge-popular">الباقة الأكثر طلباً ⭐</span>
                    <h5 class="fw-bold text-dark mb-1">المجمع الطبي (Pro Center)</h5>
                    <p class="text-muted small mb-4">للمراكز والعيادات متعددة الأطباء والتخصصات.</p>

                    <div class="mb-4">
                        <span class="display-5 fw-extrabold text-primary price-val" data-monthly="399" data-annual="319">399</span>
                        <span class="text-muted">ر.س / شهرياً</span>
                        <div class="small text-muted mt-1 billing-note">تُدفع شهرياً</div>
                    </div>

                    <ul class="list-unstyled d-flex flex-column gap-3 small mb-4 flex-grow-1">
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>حتى 5 أطباء + مستخدمين غير محدودين</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>تذكيرات آلية مؤتمتة عبر الواتساب</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>مخطط الأسنان الرقمي لكافة التخصصات</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>نظام إدارة ومتابعة طلبات معامل الأسنان</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>حساب تلقائي لنسب وعمولات الأطباء</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>جدولة أقساط التقويم والزراعة وسندات القبض</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>نسخ احتياطي سحابي فوري ودعم فني مخصص</span>
                        </li>
                    </ul>

                    <a href="#requestDemoModal" data-bs-toggle="modal" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                        ابدأ تجربتك المجانية الآن
                    </a>
                </div>
            </div>

            <!-- Plan 3: Enterprise -->
            <div class="col-lg-4">
                <div class="pricing-card">
                    <h5 class="fw-bold text-dark mb-1">المستشفيات والمراكز الكبرى</h5>
                    <p class="text-muted small mb-4">للمستشفيات المتخصصة والفروع الطبية المتعددة.</p>

                    <div class="mb-4">
                        <span class="display-5 fw-extrabold text-dark price-val" data-monthly="799" data-annual="639">799</span>
                        <span class="text-muted">ر.س / شهرياً</span>
                        <div class="small text-muted mt-1 billing-note">تُدفع شهرياً</div>
                    </div>

                    <ul class="list-unstyled d-flex flex-column gap-3 small mb-4 flex-grow-1">
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>عدد غير محدود من الأطباء والفروع</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>تخصيص كامل للشاشات وهوية العيادة</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>ربط سحابي متقدم عبر الـ API</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>مدير حساب مخصص وتدريب ميداني للكادر</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>أولوية قصوى في الدعم الفني على مدار 24/7</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>اتفاقية مستوى الخدمة (SLA 99.9%)</span>
                        </li>
                    </ul>

                    <a href="#requestDemoModal" data-bs-toggle="modal" class="btn btn-outline-dark w-100 py-3 rounded-pill fw-bold">
                        طلب عرض مخصص للمؤسسات
                    </a>
                </div>
            </div>

        </div>

        <div class="text-center mt-4 text-muted small">
            <i class="bi bi-shield-check text-success me-1"></i> كافة الباقات تشمل فترة تجريبية مجانية 14 يوماً • لا يلزم إدخال بطاقة ائتمان • إمكانية الإلغاء أو الترقية في أي وقت.
        </div>
    </div>
</section>

<!-- ==========================================================
     12. TESTIMONIALS & CLINIC SUCCESS STORIES (شهادات الأطباء)
     ========================================================== -->
<section class="py-5 bg-white position-relative">
    <div class="container py-lg-4">
        <div class="text-center max-w-750 mx-auto mb-5">
            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-chat-quote-fill me-1"></i> ثقة كبار الأطباء والمدراء
            </span>
            <h2 class="display-6 fw-extrabold text-dark mt-2">
                ماذا يقول زملاؤك أطباء الأسنان عن تجربتهم؟
            </h2>
            <p class="text-muted fs-6">
                شهادات حقيقية من استشاريين وملاك مراكز طب أسنان اعتمدوا Dental Pro ERP ونقلوا عياداتهم إلى مستوى تنظيمي جديد.
            </p>
        </div>

        <div class="row g-4">
            <!-- Review 1 -->
            <div class="col-md-4">
                <div class="clinic-card p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="text-warning mb-3">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p class="text-muted small leading-relaxed mb-4">
                            "مخطط الأسنان التفاعلي أحدث فارقاً مذهلاً في عيادتي. خلال ثوانٍ معدودة أوثق الحالات لمرضى الزراعة والتركيبات. البرنامج سريع جداً ولا يعلق حتى مع آلاف الصور وسجلات المرضى."
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-3 pt-3 border-top">
                        <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            د.خ
                        </div>
                        <div>
                            <div class="fw-bold text-dark small">د. خالد القحطاني</div>
                            <div class="text-muted" style="font-size: 0.75rem;">استشاري جراحة وزراعة الأسنان - الرياض</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review 2 -->
            <div class="col-md-4">
                <div class="clinic-card p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="text-warning mb-3">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p class="text-muted small leading-relaxed mb-4">
                            "أكبر تحدٍ كان لدينا هو تتبع طلبات معامل الأسنان وتأخر استلام تركيبات الفينير. مع Dental Pro أصبحنا نعرف حالة كل تركيبة بالمعمل فوراً، بالإضافة لحل معضلة الفواتير الإلكترونية ZATCA."
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-3 pt-3 border-top">
                        <div class="rounded-circle bg-success-subtle text-success fw-bold d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            د.س
                        </div>
                        <div>
                            <div class="fw-bold text-dark small">د. سارة المنصور</div>
                            <div class="text-muted" style="font-size: 0.75rem;">أخصائية تجميل الأسنان والفينير - جدة</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review 3 -->
            <div class="col-md-4">
                <div class="clinic-card p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="text-warning mb-3">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p class="text-muted small leading-relaxed mb-4">
                            "تذكيرات الواتساب التلقائية وحدها أنقذت مجمعنا من خسارة مواعيد بعشرات الآلاف شهرياً. نسبة حضور المرضى ارتفعت لأكثر من 95% وموظفو الاستقبال أصبحوا أكثر تركيزاً وراحة."
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-3 pt-3 border-top">
                        <div class="rounded-circle bg-info-subtle text-info fw-bold d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            د.م
                        </div>
                        <div>
                            <div class="fw-bold text-dark small">د. محمد المهيدب</div>
                            <div class="text-muted" style="font-size: 0.75rem;">المدير الطبي لمجمع الابتسامة التخصصي - الخبر</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================
     13. FAQ SECTION (الأسئلة الشائعة)
     ========================================================== -->
<section class="py-5 bg-light position-relative" id="faq">
    <div class="container py-lg-4">
        <div class="text-center max-w-750 mx-auto mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-question-circle-fill me-1"></i> إجابات وافية
            </span>
            <h2 class="display-6 fw-extrabold text-dark mt-2">
                الأسئلة الشائعة حول نظام Dental Pro
            </h2>
            <p class="text-muted fs-6">
                كل ما تحتاج لمعرفته حول التوافق، الأجهزة، نقل البيانات، والاشتراك.
            </p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="accordion clinic-accordion" id="faqAccordion">
                    
                    <!-- Q1 -->
                    <div class="accordion-item mb-3 border rounded-3 overflow-hidden shadow-sm">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                هل النظام متوافق مع متطلبات هيئة الزكاة والضريبة والجمارك (ZATCA المرحلة الثانية)؟
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small leading-relaxed">
                                نعم، بالتأكيد! نظام Dental Pro ERP يدعم الفوترة الإلكترونية المعتمدة 100% ويقوم بإصدار فواتير ضريبية مبسطة تتضمن رمز الاستجابة السريع المشفر (Cryptographic QR Code) المتطابق مع كافة معايير المرحلة الثانية ويسهل ربطه ورفع الفواتير.
                            </div>
                        </div>
                    </div>

                    <!-- Q2 -->
                    <div class="accordion-item mb-3 border rounded-3 overflow-hidden shadow-sm">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                هل يمكن استخدام البرنامج على أجهزة الآيباد والتابلت داخل غرفة الكشف؟
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small leading-relaxed">
                                نعم، النظام مصمم بتجاوب كامل مع كافة الأجهزة اللوحية (iPad / Android Tablets) والهواتف الذكية. يستطيع طبيب الأسنان استخدام التابلت بجانب كرسي المريض لتعديل مخطط الأسنان واستعراض صور الأشعة بسهولة تامة عبر المتصفح.
                            </div>
                        </div>
                    </div>

                    <!-- Q3 -->
                    <div class="accordion-item mb-3 border rounded-3 overflow-hidden shadow-sm">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                هل يقدم البرنامج دعماً لطلبات معامل الأسنان وتحديد درجات الألوان (Shades)؟
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small leading-relaxed">
                                نعم، يتضمن النظام وحدة متخصصة بالكامل لطلبيات معامل الأسنان الخارجية؛ تسجل أرقام الأسنان، نوع الخامة (زيركون، إيماكس، بورسلين، تقويم)، دليل درجات اللون (Shades: A1, A2, BL1)، وتتبع حالة التصنيع وتاريخ الاستلام المتوقع.
                            </div>
                        </div>
                    </div>

                    <!-- Q4 -->
                    <div class="accordion-item mb-3 border rounded-3 overflow-hidden shadow-sm">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                كيف يتم حساب عمولات ونسب الأطباء في النظام؟
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small leading-relaxed">
                                يمكنك في صفحة إدارة المستخدمين تحديد نسبة العمولة لكل طبيب بشكل مستقل. بمجرد تحصيل الفاتورة من المريض، يقوم النظام بحساب مستحقات الطبيب وعمولته بدقة ويظهرها في تقرير مفصل فوري للطبيب وللإدارة لمنع أي خلافات مالية.
                            </div>
                        </div>
                    </div>

                    <!-- Q5 -->
                    <div class="accordion-item mb-3 border rounded-3 overflow-hidden shadow-sm">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                هل يدعم البرنامج العملات المختلفة وتخصيص بيانات الفواتير؟
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small leading-relaxed">
                                نعم، توفر شاشة الإعدادات دعماً لأي عملة (ريال سعودي، جنيه مصري، درهم، دينار، دولار...) مع تحديد موضع الرمز، وتخصيص اسم المركز، الشعار، والرقم الضريبي ليظهر رسمياً على كافة مطبوعات الفواتير وسندات القبض.
                            </div>
                        </div>
                    </div>

                    <!-- Q6 -->
                    <div class="accordion-item mb-3 border rounded-3 overflow-hidden shadow-sm">
                        <h2 class="accordion-header" id="headingSix">
                            <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                هل يحتاج البرنامج إلى خوادم داخلية أو تركيب معقد؟
                            </button>
                        </h2>
                        <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small leading-relaxed">
                                لا على الإطلاق! برنامج Dental Pro ERP هو نظام سحابي 100% (Cloud SaaS)، يعمل مباشرة عبر الإنترنت من أي جهاز دون الحاجة لشراء خوادم باهظة الثمن أو توظيف مهندسي شبكات. يتم تحديث النظام وعمل النسخ الاحتياطية آلياً يومياً.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================
     14. HIGH-CONVERTING FINAL CTA BANNER
     ========================================================== -->
<section class="py-5 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #0b1329 0%, #0369a1 50%, #0d9488 100%);">
    <div class="container py-lg-5 position-relative z-1 text-center">
        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3">
            <i class="bi bi-lightning-charge-fill me-1"></i> ابدأ الترقية الرقمية اليوم
        </span>
        <h2 class="display-5 fw-extrabold mb-3">
            جاهز لنقل عيادتك إلى العصر الرقمي المتطور؟
        </h2>
        <p class="lead max-w-700 mx-auto text-light text-opacity-90 fs-6 mb-4" style="max-width: 650px; line-height: 1.8;">
            انضم الآن إلى مئات الأطباء والمراكز الرائدة. ابدأ نسختك التجريبية المجانية لمدة 14 يوماً بدون أي التزام أو بطاقة ائتمان.
        </p>

        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="#requestDemoModal" data-bs-toggle="modal" class="btn btn-light btn-lg rounded-pill px-5 py-3 fw-bold text-primary shadow-lg d-inline-flex align-items-center gap-2">
                <i class="bi bi-rocket-takeoff-fill"></i> ابدأ تجربتك المجانية الآن
            </a>
            <a href="https://wa.me/966500000000?text=مرحباً، أود التحدث مع مستشار أنظمة طب الأسنان حول Dental Pro ERP" target="_blank" class="btn btn-outline-light btn-lg rounded-pill px-4 py-3 fw-bold d-inline-flex align-items-center gap-2">
                <i class="bi bi-whatsapp text-success fs-5"></i> تواصل مع مستشار الأنظمة
            </a>
        </div>

        <div class="d-flex justify-content-center gap-4 text-light text-opacity-75 small mt-4 pt-2">
            <span><i class="bi bi-check2 text-warning me-1"></i> 14 يوم تجربة مجانية</span>
            <span><i class="bi bi-check2 text-warning me-1"></i> تفعيل فوري للحساب</span>
            <span><i class="bi bi-check2 text-warning me-1"></i> دعم وتدريب مجاني</span>
        </div>
    </div>
</section>

<!-- ==========================================================
     15. MODALS: LIGHTBOX & LIVE DEMO REQUEST
     ========================================================== -->

<!-- Lightbox Modal for High-Res Screenshots -->
<div class="modal fade" id="imageLightboxModal" tabindex="-1" aria-labelledby="imageLightboxTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark border-0 rounded-4 overflow-hidden shadow-2xl">
            <div class="modal-header border-secondary text-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary">Dental Pro ERP</span>
                    <h5 class="modal-title fw-bold mb-0" id="imageLightboxTitle">شاشة البرنامج</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body p-0 text-center bg-black">
                <img id="imageLightboxImg" src="" alt="شاشة البرنامج" class="img-fluid lightbox-img">
            </div>
            <div class="modal-footer border-secondary text-white py-2 justify-content-between">
                <span class="small text-muted">واجهة حقيقية من المنظومة السحابية Dental Pro ERP</span>
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<!-- Request Demo / Free Trial Modal -->
<div class="modal fade" id="requestDemoModal" tabindex="-1" aria-labelledby="requestDemoModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-2xl">
            <div class="modal-header bg-gradient text-white p-4" style="background: linear-gradient(135deg, #0b1329 0%, #0284c7 100%);">
                <div>
                    <span class="badge bg-warning text-dark px-2 py-1 rounded-pill small mb-1">ديمو حي مخصص</span>
                    <h5 class="modal-title fw-bold mb-0" id="requestDemoModalTitle">طلب تجربة مجانية وعرض توضيحي</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>

            <div class="modal-body p-4">
                <form id="leadRequestForm">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">اسم الطبيب أو مدير المركز</label>
                        <div class="clinic-input-icon">
                            <i class="bi bi-person"></i>
                            <input type="text" class="form-control" id="leadName" placeholder="د. أحمد عبدالله" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">اسم العيادة أو المجمع الطبي</label>
                        <div class="clinic-input-icon">
                            <i class="bi bi-hospital"></i>
                            <input type="text" class="form-control" id="leadClinic" placeholder="عيادات النخبة لطب الأسنان" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">رقم الجوال (واتساب للتواصل)</label>
                        <div class="clinic-input-icon">
                            <i class="bi bi-whatsapp text-success"></i>
                            <input type="tel" class="form-control" id="leadPhone" placeholder="05XXXXXXXX" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-dark">عدد أطباء العيادة</label>
                            <select class="form-select" id="leadChairs">
                                <option value="1">طبيب واحد (عيادة)</option>
                                <option value="2-3" selected>2 إلى 3 أطباء</option>
                                <option value="4-6">4 إلى 6 أطباء</option>
                                <option value="7+">أكثر من 7 أطباء</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-dark">المدينة</label>
                            <input type="text" class="form-control" id="leadCity" placeholder="الرياض، جدة..." required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm mt-3" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                        <i class="bi bi-check2-circle me-1"></i> تأكيد الطلب وبدء التجربة المجانية
                    </button>
                </form>

                <!-- Success Confirmation Message -->
                <div id="leadSuccessAlert" class="alert alert-success d-none mt-3 rounded-3 p-3 text-center">
                    <i class="bi bi-check-circle-fill fs-3 text-success d-block mb-1"></i>
                    <h6 class="fw-bold mb-1">تم استلام طلبك بنجاح!</h6>
                    <p class="small mb-3">سيتواصل معك مستشار الأنظمة الطبية خلال دقائق عبر الواتساب لتفعيل حسابك التجريبي.</p>
                    <a id="leadDirectWhatsappBtn" href="#" target="_blank" class="btn btn-success btn-sm rounded-pill px-4 fw-bold">
                        <i class="bi bi-whatsapp me-1"></i> فتح المحادثة الفورية عبر واتساب
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // ==========================================================
        // 1. SCREEN TOUR TAB SWITCHER (8 MODULES)
        // ==========================================================
        const screenTabs = document.querySelectorAll('.screen-tab-btn');
        const activeScreenImg = document.getElementById('activeScreenImage');
        const activeScreenUrl = document.getElementById('activeScreenUrl');
        const openActiveLightboxBtn = document.getElementById('openActiveLightboxBtn');
        const zoomCurrentScreenBtn = document.getElementById('zoomCurrentScreenBtn');
        const simLabs = document.getElementById('simulatedLabsUi');
        const simExpenses = document.getElementById('simulatedExpensesUi');
        const simUsers = document.getElementById('simulatedUsersUi');

        const screenUrlMap = {
            '#tab-dashboard': 'https://app.dentalpro.com/clinic/dashboard',
            '#tab-odontogram': 'https://app.dentalpro.com/clinic/patients/1 (Odontogram)',
            '#tab-patients': 'https://app.dentalpro.com/clinic/patients',
            '#tab-appointments': 'https://app.dentalpro.com/clinic/appointments',
            '#tab-billing': 'https://app.dentalpro.com/clinic/billing',
            '#tab-labs': 'https://app.dentalpro.com/clinic/labs',
            '#tab-expenses': 'https://app.dentalpro.com/clinic/expenses',
            '#tab-users': 'https://app.dentalpro.com/clinic/users'
        };

        const paneMap = {
            '#tab-dashboard': 'pane-dashboard',
            '#tab-odontogram': 'pane-odontogram',
            '#tab-patients': 'pane-patients',
            '#tab-appointments': 'pane-appointments',
            '#tab-billing': 'pane-billing',
            '#tab-labs': 'pane-labs',
            '#tab-expenses': 'pane-expenses',
            '#tab-users': 'pane-users'
        };

        let currentActiveImg = "{{ asset('images/screenshots/dashboard.png') }}";
        let currentActiveTitle = "لوحة التحكم والتحليلات اللحظية";

        screenTabs.forEach(tab => {
            tab.addEventListener('click', function () {
                screenTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                const target = this.getAttribute('data-target');
                const img = this.getAttribute('data-img');
                const title = this.getAttribute('data-title');

                currentActiveTitle = title;

                // Handle visual display: Image vs Simulated UI
                if (img) {
                    currentActiveImg = img;
                    activeScreenImg.classList.remove('d-none');
                    if (simLabs) simLabs.classList.add('d-none');
                    if (simExpenses) simExpenses.classList.add('d-none');
                    if (simUsers) simUsers.classList.add('d-none');

                    activeScreenImg.style.opacity = '0.3';
                    setTimeout(() => {
                        activeScreenImg.src = img;
                        activeScreenImg.style.opacity = '1';
                    }, 150);
                } else {
                    activeScreenImg.classList.add('d-none');
                    if (simLabs) simLabs.classList.add('d-none');
                    if (simExpenses) simExpenses.classList.add('d-none');
                    if (simUsers) simUsers.classList.add('d-none');

                    if (target === '#tab-labs' && simLabs) simLabs.classList.remove('d-none');
                    if (target === '#tab-expenses' && simExpenses) simExpenses.classList.remove('d-none');
                    if (target === '#tab-users' && simUsers) simUsers.classList.remove('d-none');
                }

                if (screenUrlMap[target]) {
                    activeScreenUrl.textContent = screenUrlMap[target];
                }

                // Switch explanation pane
                document.querySelectorAll('.screen-content-pane').forEach(p => p.classList.add('d-none'));
                const activePaneId = paneMap[target];
                if (activePaneId && document.getElementById(activePaneId)) {
                    document.getElementById(activePaneId).classList.remove('d-none');
                }
            });
        });

        // ==========================================================
        // 2. LIGHTBOX MODAL FUNCTIONALITY
        // ==========================================================
        const lightboxModal = document.getElementById('imageLightboxModal');
        const lightboxImg = document.getElementById('imageLightboxImg');
        const lightboxTitle = document.getElementById('imageLightboxTitle');

        if (lightboxModal) {
            lightboxModal.addEventListener('show.bs.modal', function (event) {
                const trigger = event.relatedTarget;
                if (trigger && trigger.getAttribute('data-img')) {
                    lightboxImg.src = trigger.getAttribute('data-img');
                    lightboxTitle.textContent = trigger.getAttribute('data-title') || 'شاشة البرنامج';
                } else {
                    lightboxImg.src = currentActiveImg;
                    lightboxTitle.textContent = currentActiveTitle;
                }
            });
        }

        if (openActiveLightboxBtn) {
            openActiveLightboxBtn.addEventListener('click', function () {
                const modal = new bootstrap.Modal(lightboxModal);
                lightboxImg.src = currentActiveImg;
                lightboxTitle.textContent = currentActiveTitle;
                modal.show();
            });
        }

        if (zoomCurrentScreenBtn) {
            zoomCurrentScreenBtn.addEventListener('click', function () {
                const modal = new bootstrap.Modal(lightboxModal);
                lightboxImg.src = currentActiveImg;
                lightboxTitle.textContent = currentActiveTitle;
                modal.show();
            });
        }

        if (activeScreenImg) {
            activeScreenImg.addEventListener('click', function () {
                const modal = new bootstrap.Modal(lightboxModal);
                lightboxImg.src = currentActiveImg;
                lightboxTitle.textContent = currentActiveTitle;
                modal.show();
            });
        }

        // ==========================================================
        // 3. PLAYABLE ODONTOGRAM SANDBOX INTERACTION
        // ==========================================================
        let currentStatus = 'caries';
        let currentStatusName = 'تسوس عميق';
        let currentStatusCost = 250;
        let treatedTeeth = {}; // toothNumber -> { status, cost, name }

        const statusPills = document.querySelectorAll('#statusPickerGroup .status-picker-pill');
        const toothButtons = document.querySelectorAll('.sandbox-tooth-btn');
        const lastActionLabel = document.getElementById('lastActionLabel');
        const treatedTeethCount = document.getElementById('treatedTeethCount');
        const totalEstimatedCost = document.getElementById('totalEstimatedCost');
        const resetSandboxBtn = document.getElementById('resetSandboxBtn');

        statusPills.forEach(pill => {
            pill.addEventListener('click', function () {
                statusPills.forEach(p => {
                    p.classList.remove('active');
                    p.style.backgroundColor = '';
                    p.style.borderColor = '';
                });
                this.classList.add('active');

                currentStatus = this.getAttribute('data-status');
                currentStatusName = this.getAttribute('data-name');
                currentStatusCost = parseInt(this.getAttribute('data-cost'), 10) || 0;
            });
        });

        function updateSandboxSummary() {
            let count = Object.keys(treatedTeeth).length;
            let total = 0;
            for (let num in treatedTeeth) {
                total += treatedTeeth[num].cost;
            }
            if (treatedTeethCount) treatedTeethCount.textContent = count;
            if (totalEstimatedCost) totalEstimatedCost.textContent = total.toLocaleString('en-US') + ' ر.س';
        }

        toothButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const toothNum = this.getAttribute('data-tooth');
                
                // Remove all status classes
                this.classList.remove('status-healthy', 'status-caries', 'status-filling', 'status-endo', 'status-crown', 'status-implant');
                
                // Add new status class
                this.classList.add('status-' + currentStatus);

                // Update treated teeth map
                if (currentStatus === 'healthy') {
                    delete treatedTeeth[toothNum];
                    if (lastActionLabel) {
                        lastActionLabel.innerHTML = `تم تعيين السن <strong>#${toothNum}</strong> كـ <strong>سن سليم</strong>.`;
                    }
                } else {
                    treatedTeeth[toothNum] = {
                        status: currentStatus,
                        cost: currentStatusCost,
                        name: currentStatusName
                    };
                    if (lastActionLabel) {
                        lastActionLabel.innerHTML = `تم تسجيل <strong>${currentStatusName}</strong> على السن <strong>#${toothNum}</strong> (تقديري: ${currentStatusCost} ر.س).`;
                    }
                }

                updateSandboxSummary();
            });
        });

        if (resetSandboxBtn) {
            resetSandboxBtn.addEventListener('click', function () {
                treatedTeeth = {};
                toothButtons.forEach(btn => {
                    btn.classList.remove('status-caries', 'status-filling', 'status-endo', 'status-crown', 'status-implant');
                    btn.classList.add('status-healthy');
                });
                if (lastActionLabel) {
                    lastActionLabel.textContent = 'تمت إعادة ضبط المخطط لأسنان سليمة بالكامل.';
                }
                updateSandboxSummary();
            });
        }

        // ==========================================================
        // 4. ROI & TIME SAVINGS CALCULATOR
        // ==========================================================
        const chairsRange = document.getElementById('chairsRange');
        const patientsRange = document.getElementById('patientsRange');
        const chairsValueBadge = document.getElementById('chairsValueBadge');
        const patientsValueBadge = document.getElementById('patientsValueBadge');
        const hoursSavedOutput = document.getElementById('hoursSavedOutput');
        const revenueProtectedOutput = document.getElementById('revenueProtectedOutput');

        function calculateROI() {
            if (!chairsRange || !patientsRange) return;
            const chairs = parseInt(chairsRange.value, 10);
            const patients = parseInt(patientsRange.value, 10);

            chairsValueBadge.textContent = chairs + (chairs === 1 ? ' طبيب واحد' : ' أطباء');
            patientsValueBadge.textContent = patients + ' مريض';

            // Hours saved: 0.75 hours saved per doctor per day * 24 working days
            const hoursPerMonth = Math.round(chairs * 0.75 * 24);
            hoursSavedOutput.textContent = hoursPerMonth;

            // Revenue protected from no-shows: average ticket ~ 200 SAR, prevent 7% no-shows
            const monthlyAppointments = patients * 24;
            const preventedNoShows = Math.round(monthlyAppointments * 0.07);
            const revenueProtected = preventedNoShows * 200;
            revenueProtectedOutput.textContent = revenueProtected.toLocaleString('en-US');
        }

        if (chairsRange && patientsRange) {
            chairsRange.addEventListener('input', calculateROI);
            patientsRange.addEventListener('input', calculateROI);
        }

        // ==========================================================
        // 5. PRICING CYCLE TOGGLE (MONTHLY / ANNUAL 20% OFF)
        // ==========================================================
        const pricingSwitch = document.getElementById('pricingCycleSwitch');
        const priceVals = document.querySelectorAll('.price-val');
        const billingNotes = document.querySelectorAll('.billing-note');

        if (pricingSwitch) {
            pricingSwitch.addEventListener('change', function () {
                const isAnnual = this.checked;
                priceVals.forEach(span => {
                    const monthly = span.getAttribute('data-monthly');
                    const annual = span.getAttribute('data-annual');
                    span.textContent = isAnnual ? annual : monthly;
                });

                billingNotes.forEach(note => {
                    note.textContent = isAnnual ? 'تُدفع سنوياً (وفرت 20%)' : 'تُدفع شهرياً';
                });
            });
        }

        // ==========================================================
        // 6. LEAD & DEMO REQUEST FORM
        // ==========================================================
        const leadForm = document.getElementById('leadRequestForm');
        const leadSuccessAlert = document.getElementById('leadSuccessAlert');
        const leadDirectWhatsappBtn = document.getElementById('leadDirectWhatsappBtn');

        if (leadForm) {
            leadForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const name = document.getElementById('leadName').value.trim();
                const clinic = document.getElementById('leadClinic').value.trim();
                const phone = document.getElementById('leadPhone').value.trim();
                const chairs = document.getElementById('leadChairs').value;
                const city = document.getElementById('leadCity').value.trim();

                const whatsappMsg = encodeURIComponent(
                    `مرحباً، أود بدء التجربة المجانية وحجز عرض توضيحي لنظام Dental Pro ERP.\nالاسم: ${name}\nالمركز: ${clinic}\nالجوال: ${phone}\nعدد الأطباء: ${chairs}\nالمدينة: ${city}`
                );

                const waUrl = `https://wa.me/966500000000?text=${whatsappMsg}`;
                if (leadDirectWhatsappBtn) {
                    leadDirectWhatsappBtn.href = waUrl;
                }

                leadForm.classList.add('d-none');
                leadSuccessAlert.classList.remove('d-none');
            });
        }

    });
</script>
@endpush
