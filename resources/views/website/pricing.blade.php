@extends('layouts.app')

@section('title', 'باقات وأسعار Dental Pro ERP في مصر | أفضل قيمة لعيادات الأسنان')

@push('styles')
<style>
    /* ==========================================================
       DENTAL PRO ERP - PRICING PAGE STYLING & ANIMATIONS
       ========================================================== */
    .pricing-hero-bg {
        position: relative;
        background: radial-gradient(circle at 85% 15%, rgba(2, 132, 199, 0.12) 0%, transparent 45%),
                    radial-gradient(circle at 15% 85%, rgba(13, 148, 136, 0.12) 0%, transparent 45%),
                    #ffffff;
        overflow: hidden;
    }

    .pricing-hero-bg::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(2, 132, 199, 0.08) 1px, transparent 1px);
        background-size: 28px 28px;
        pointer-events: none;
        opacity: 0.6;
    }

    .pricing-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(2, 132, 199, 0.08);
        border: 1px solid rgba(2, 132, 199, 0.25);
        color: #0369a1;
        font-size: 0.85rem;
        font-weight: 700;
        padding: 0.4rem 1.25rem;
        border-radius: 9999px;
    }

    /* Billing Cycle Toggle Switch */
    .billing-switch-wrapper {
        display: inline-flex;
        align-items: center;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 9999px;
        padding: 5px;
        position: relative;
    }

    .billing-switch-btn {
        border: none;
        background: transparent;
        color: #475569;
        font-size: 0.95rem;
        font-weight: 700;
        padding: 8px 22px;
        border-radius: 9999px;
        cursor: pointer;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .billing-switch-btn.active {
        background: #0284c7;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35);
    }

    /* Pricing Cards */
    .pricing-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 1.5rem;
        padding: 2.25rem 1.75rem;
        transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .pricing-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 22px 40px -10px rgba(15, 23, 42, 0.12);
    }

    .pricing-card.featured {
        border: 2px solid #0284c7;
        box-shadow: 0 15px 35px -8px rgba(2, 132, 199, 0.25);
        background: linear-gradient(180deg, #ffffff 0%, #f0f9ff 100%);
    }

    .pricing-card.featured:hover {
        transform: translateY(-10px);
        box-shadow: 0 26px 50px -10px rgba(2, 132, 199, 0.32);
    }

    .featured-ribbon {
        position: absolute;
        top: -14px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);
        color: #ffffff;
        font-size: 0.8rem;
        font-weight: 800;
        padding: 4px 16px;
        border-radius: 9999px;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35);
        white-space: nowrap;
    }

    .price-currency {
        font-size: 1.15rem;
        font-weight: 700;
        color: #64748b;
    }

    .price-value {
        font-size: 3rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1;
        letter-spacing: -1px;
    }

    .price-period {
        font-size: 0.9rem;
        color: #64748b;
    }

    .feature-item {
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
        font-size: 0.93rem;
        color: #334155;
        margin-bottom: 0.75rem;
        line-height: 1.45;
    }

    .feature-item i {
        font-size: 1.1rem;
        margin-top: 1px;
        flex-shrink: 0;
    }

    /* Comparison Table */
    .comparison-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 1.25rem;
        overflow: hidden;
    }

    .comparison-table th, .comparison-table td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        text-align: center;
        vertical-align: middle;
    }

    .comparison-table th:first-child, .comparison-table td:first-child {
        text-align: right;
        font-weight: 600;
    }

    .comparison-table thead th {
        background: #f8fafc;
        font-weight: 800;
        color: #0f172a;
        border-bottom: 2px solid #e2e8f0;
    }

    .comparison-table tr:hover td {
        background: #f0f9ff;
    }

    .table-section-title {
        background: #f1f5f9 !important;
        font-weight: 800 !important;
        color: #0369a1 !important;
        text-align: right !important;
    }
</style>
@endpush

@section('content')

<!-- ==========================================================
     HERO & PRICING HEADER
     ========================================================== -->
<section class="pricing-hero-bg py-5 pt-lg-5 pb-lg-5 text-center position-relative">
    <div class="container py-lg-4">
        <!-- Egyptian Market Badge -->
        <div class="pricing-badge-pill mb-3">
            <span>🇪🇬 خطط تسعير مخصصة لجمهورية مصر العربية</span>
            <span class="badge bg-success text-white rounded-pill px-2 py-0">أسعار تنافسية</span>
        </div>

        <h1 class="display-4 fw-extrabold text-dark mb-3">
            استثمر في نمو عيادتك بأفضل سعر في مصر
        </h1>
        <p class="lead text-muted mx-auto mb-4" style="max-width: 720px; font-size: 1.15rem; line-height: 1.8;">
            نظام سحابي متكامل يجمع بين مخطط الأسنان الرقمي (Odontogram)، إدارة المواعيد، الفواتير، ومتابعة المعامل. 
            شفافية كاملة بدون أي تكاليف خفية أو رسوم ترقية مفاجئة.
        </p>

        <!-- Billing Toggle -->
        <div class="d-flex flex-column flex-sm-row align-items-center justify-content-center gap-2 mb-4">
            <div class="billing-switch-wrapper shadow-sm">
                <button type="button" class="billing-switch-btn active" id="btn-annual" onclick="setBillingCycle('annual')">
                    <i class="bi bi-star-fill text-warning"></i>
                    دفع سنوي
                    <span class="badge bg-danger rounded-pill px-2 py-0 ms-1">وفر شهرين 🔥</span>
                </button>
                <button type="button" class="billing-switch-btn" id="btn-monthly" onclick="setBillingCycle('monthly')">
                    دفع شهري
                </button>
            </div>
        </div>

        <div class="small text-muted mb-2">
            <i class="bi bi-check-circle-fill text-success me-1"></i> تجربة مجانية لمدة 14 يوماً بدون الحاجة لبطاقة بنكية
            <span class="mx-2">•</span>
            <i class="bi bi-shield-check text-primary me-1"></i> دعم محلي متاح عبر الواتساب على مدار الساعة
        </div>
    </div>
</section>

<!-- ==========================================================
     PRICING TIERS SECTION
     ========================================================== -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4 justify-content-center">

            <!-- ===================== PLAN 1: STARTER ===================== -->
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card">
                    <div class="mb-3">
                        <span class="badge bg-light text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-bold">
                            للأطباء المستقلين
                        </span>
                        <h3 class="fw-bold text-dark mt-2 mb-1">باقة البداية (Starter)</h3>
                        <p class="text-muted small">الخيار المثالي لعيادة طبيب أسنان مستقل يبحث عن التوثيق السحابي السريع.</p>
                    </div>

                    <!-- Price Box -->
                    <div class="my-3 pb-3 border-bottom border-light-subtle">
                        <div class="d-flex align-items-baseline gap-1">
                            <span class="price-currency">ج.م</span>
                            <span class="price-value tier-price" data-monthly="450" data-annual="4,500">4,500</span>
                            <span class="price-period tier-period">/ سنوياً</span>
                        </div>
                        <div class="small text-success fw-bold mt-1 tier-subtext">
                            يعادل <span class="fw-extrabold font-monospace">375 ج.م</span> فقط شهرياً (وفرت 900 ج.م)
                        </div>
                        <div class="small text-muted mt-2">
                            <i class="bi bi-people-fill text-primary me-1"></i> سعة المستخدمين: <strong>حتى 2 مستخدمين</strong> (طبيب + سكرتير)
                        </div>
                    </div>

                    <!-- Features List -->
                    <div class="flex-grow-1 my-3">
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>مخطط الأسنان الرقمي FDI:</strong> تفاعلي كامل للبالغين والأطفال</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>إدارة المرضى:</strong> فتح ملفات وسجلات مرضى غير محدودة</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>المواعيد والانتظار:</strong> جدول مواعيد العيادة وقائمة الانتظار اليومية</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>الفوترة الأساسية:</strong> إنشاء فواتير العلاج وسندات القبض الفورية</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>مساحة التخزين:</strong> 5 جيجابايت لصور الأشعة والمستندات الطبية</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>التذكيرات:</strong> إرسال تذكيرات المواعيد عبر الواتساب والإيميل</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>النسخ الاحتياطي:</strong> حفظ سحابي يومي تلقائي على سيرفرات LiteSpeed</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>الدعم الفني:</strong> دعم سريع عبر الواتساب وإرشادات الاستخدام</span>
                        </div>
                    </div>

                    <!-- CTA -->
                    <div class="pt-3">
                        <a href="https://wa.me/201553408200?text=مرحباً، أود الاشتراك أو بدء تجربة مجانية لباقة (البداية Starter - 4500 ج.م)" 
                           target="_blank" 
                           class="btn btn-outline-primary btn-unified-card tier-cta-btn" 
                           data-base-text="مرحباً، أود الاشتراك أو بدء تجربة مجانية لباقة (البداية Starter)">
                            <i class="bi bi-play-circle-fill"></i>
                            <span>ابدأ تجربتك المجانية (14 يوماً)</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ===================== PLAN 2: PRO (FEATURED) ===================== -->
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card featured">
                    <div class="featured-ribbon">
                        ⭐ الباقة الأكثر طلباً في مصر ⭐
                    </div>

                    <div class="mb-3 mt-2">
                        <span class="badge bg-primary text-white rounded-pill px-3 py-1 fw-bold">
                            العيادات المتميزة والنشطة
                        </span>
                        <h3 class="fw-bold text-dark mt-2 mb-1">الباقة الاحترافية (Pro Clinic)</h3>
                        <p class="text-muted small">شاملة لإدارة العيادة بالكامل: المعامل، الأقساط، ونسب الأطباء والتحليلات.</p>
                    </div>

                    <!-- Price Box -->
                    <div class="my-3 pb-3 border-bottom border-light-subtle">
                        <div class="d-flex align-items-baseline gap-1">
                            <span class="price-currency">ج.م</span>
                            <span class="price-value tier-price text-primary" data-monthly="850" data-annual="8,500">8,500</span>
                            <span class="price-period tier-period">/ سنوياً</span>
                        </div>
                        <div class="small text-success fw-bold mt-1 tier-subtext">
                            يعادل <span class="fw-extrabold font-monospace">708 ج.م</span> فقط شهرياً (وفرت 1,700 ج.م)
                        </div>
                        <div class="small text-muted mt-2">
                            <i class="bi bi-people-fill text-primary me-1"></i> سعة المستخدمين: <strong>حتى 5 مستخدمين</strong> (أطباء + تمريض + استقبال)
                        </div>
                    </div>

                    <!-- Features List -->
                    <div class="flex-grow-1 my-3">
                        <div class="feature-item text-primary fw-bold">
                            <i class="bi bi-stars text-primary"></i>
                            <span>تشمل كافة مميزات باقة البداية، بالإضافة إلى:</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>إدارة الأقساط والدفعات:</strong> جدولة خطط العلاج الطويلة كالتقويم والزراعة</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>تتبع طلبات المعامل (Lab Orders):</strong> التركيبات، تواريخ الاستلام، وتكلفة المعمل</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>حسابات ونسب الأطباء:</strong> احتساب مستحقات الأطباء الشركاء تلقائياً</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>إدارة المصروفات والأرباح:</strong> تسجيل نفقات العيادة وإيراداتها الصافية</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>لوحة التحليلات والإحصائيات:</strong> تتبع ساعات الذروة والصفحات الأكثر زيارة</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>مساحة تخزين مضاعفة:</strong> 20 جيجابايت لملفات وصور الأشعة السينية</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>دعم فني وتدريب VIP:</strong> أولوية الرد المباشر وتدريب سكرتارية العيادة</span>
                        </div>
                    </div>

                    <!-- CTA -->
                    <div class="pt-3">
                        <a href="https://wa.me/201553408200?text=مرحباً، أود الاشتراك أو بدء تجربة مجانية للباقة الاحترافية (Pro Clinic - 8500 ج.م)" 
                           target="_blank" 
                           class="btn btn-primary btn-unified-card shadow-sm tier-cta-btn text-white" 
                           style="background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%); border: none;"
                           data-base-text="مرحباً، أود الاشتراك أو بدء تجربة مجانية للباقة الاحترافية (Pro Clinic)">
                            <i class="bi bi-lightning-charge-fill"></i>
                            <span>ابدأ تجربتك المجانية الآن</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ===================== PLAN 3: MEDICAL CENTER ===================== -->
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card">
                    <div class="mb-3">
                        <span class="badge bg-light text-dark border border-secondary-subtle rounded-pill px-3 py-1 fw-bold">
                            للمراكز والمجمعات الكبرى
                        </span>
                        <h3 class="fw-bold text-dark mt-2 mb-1">باقة المراكز (Center)</h3>
                        <p class="text-muted small">حل شامل متعدد الأطباء والتخصصات مع أدوار وصلاحيات متقدمة وإدارة موسعة.</p>
                    </div>

                    <!-- Price Box -->
                    <div class="my-3 pb-3 border-bottom border-light-subtle">
                        <div class="d-flex align-items-baseline gap-1">
                            <span class="price-currency">ج.م</span>
                            <span class="price-value tier-price" data-monthly="1,500" data-annual="15,000">15,000</span>
                            <span class="price-period tier-period">/ سنوياً</span>
                        </div>
                        <div class="small text-success fw-bold mt-1 tier-subtext">
                            يعادل <span class="fw-extrabold font-monospace">1,250 ج.م</span> فقط شهرياً (وفرت 3,000 ج.م)
                        </div>
                        <div class="small text-muted mt-2">
                            <i class="bi bi-people-fill text-primary me-1"></i> سعة المستخدمين: <strong>حتى 15 مستخدماً</strong> (قابلة للزيادة)
                        </div>
                    </div>

                    <!-- Features List -->
                    <div class="flex-grow-1 my-3">
                        <div class="feature-item text-primary fw-bold">
                            <i class="bi bi-building-check text-primary"></i>
                            <span>تشمل كل مميزات الباقة الاحترافية، بالإضافة إلى:</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>تعدد كراسي وعيادات المركز:</strong> إدارة متزامنة لأكثر من تخصص وطبيب</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>صلاحيات أمان مشددة:</strong> تحديد دقيق لما يراه كل طبيب ومحاسب واستقبال</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>تقارير مالية وتدقيق شامل:</strong> كشوفات حساب تفصيلية وإيرادات الكراسي</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>مساحة تخزين ضخمة:</strong> 50 جيجابايت للأشعة البانورامية والمستندات</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>نقل البيانات السابقة مجاناً:</strong> مساعدة فريقنا في إدخال بياناتك القديمة</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>مدير حساب مخصص:</strong> متابعة دورية ودعم هاتفي وتقني مباشر</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>جلسات تدريبية مخصصة:</strong> تدريب كامل طاقم التمريض والسكرتارية</span>
                        </div>
                    </div>

                    <!-- CTA -->
                    <div class="pt-3">
                        <a href="https://wa.me/201553408200?text=مرحباً، أود الاستفسار عن باقة المراكز الطبية الكبرى (Medical Center - 15000 ج.م)" 
                           target="_blank" 
                           class="btn btn-outline-dark btn-unified-card tier-cta-btn" 
                           data-base-text="مرحباً، أود الاستفسار عن باقة المراكز الطبية الكبرى (Medical Center)">
                            <i class="bi bi-headset"></i>
                            <span>تواصل لطلب عرض للمركز</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ==========================================================
     PAYMENT METHODS IN EGYPT
     ========================================================== -->
<section class="py-4 bg-light border-top border-bottom">
    <div class="container text-center">
        <h5 class="fw-bold text-dark mb-3">طرق دفع مريحة ومحلية تناسب جميع الأطباء في مصر</h5>
        <div class="d-flex flex-wrap justify-content-center align-items-center gap-3">
            <span class="badge bg-white text-dark border px-3 py-2 fs-6 shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-phone-fill text-danger fs-5"></i> محفظة فودافون كاش (Vodafone Cash)
            </span>
            <span class="badge bg-white text-dark border px-3 py-2 fs-6 shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-lightning-charge-fill text-warning fs-5"></i> شبكة المدفوعات اللحظية (إنستاباي InstaPay)
            </span>
            <span class="badge bg-white text-dark border px-3 py-2 fs-6 shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-bank2 text-primary fs-5"></i> تحويل بنكي (الأهلي / CIB / مصر)
            </span>
            <span class="badge bg-white text-dark border px-3 py-2 fs-6 shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-credit-card-2-front-fill text-success fs-5"></i> بطاقات فيزا / ماستركارد / ميزة
            </span>
        </div>
    </div>
</section>

<!-- ==========================================================
     FEATURE COMPARISON TABLE
     ========================================================== -->
<section class="py-5 bg-white">
    <div class="container py-lg-3">
        <div class="text-center mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill fw-bold mb-2">مقارنة تفصيلية</span>
            <h2 class="fw-extrabold text-dark">مقارنة شاملة بين ميزات الباقات</h2>
            <p class="text-muted">اختر الباقة المناسبة لاحتياجات عيادتك الحالية مع إمكانية الترقية في أي وقت بكل سهولة.</p>
        </div>

        <div class="table-responsive">
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">الميزة / الوحدة البرمجية</th>
                        <th style="width: 20%;">باقة البداية (Starter)</th>
                        <th style="width: 20%;" class="text-primary bg-primary-subtle">الباقة الاحترافية (Pro) ⭐</th>
                        <th style="width: 20%;">باقة المراكز (Center)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Section: General -->
                    <tr>
                        <td colspan="4" class="table-section-title">👥 المستخدمين والحسابات</td>
                    </tr>
                    <tr>
                        <td>الحد الأقصى للمستخدمين (حسابات الكادر)</td>
                        <td>حتى 2 مستخدمين</td>
                        <td class="fw-bold text-primary">حتى 5 مستخدمين</td>
                        <td class="fw-bold text-success">حتى 15 مستخدماً (أو أكثر)</td>
                    </tr>
                    <tr>
                        <td>سجلات وبيانات المرضى</td>
                        <td>غير محدود</td>
                        <td>غير محدود</td>
                        <td>غير محدود</td>
                    </tr>
                    <tr>
                        <td>إدارة أدوار وصلاحيات الكادر (أطباء، سكرتير، محاسب)</td>
                        <td>صلاحيات أساسية</td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i> (صلاحيات مخصصة)</td>
                    </tr>

                    <!-- Section: Clinical -->
                    <tr>
                        <td colspan="4" class="table-section-title">🦷 السجل الطبي ومخطط الأسنان FDI</td>
                    </tr>
                    <tr>
                        <td>مخطط الأسنان التفاعلي (Odontogram) 32 سن</td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                    </tr>
                    <tr>
                        <td>مخطط أسنان الأطفال اللبنية (Pediatric Chart)</td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                    </tr>
                    <tr>
                        <td>تخزين صور الأشعة السينية والتقارير الطبية</td>
                        <td>5 جيجابايت</td>
                        <td class="fw-bold text-primary">20 جيجابايت</td>
                        <td class="fw-bold text-success">50 جيجابايت</td>
                    </tr>
                    <tr>
                        <td>توليد وطباعة خطط العلاج التقديرية الملونة</td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                    </tr>

                    <!-- Section: Lab & Billing -->
                    <tr>
                        <td colspan="4" class="table-section-title">🧪 المعامل والحسابات المالية</td>
                    </tr>
                    <tr>
                        <td>متابعة طلبيات المعامل والتركيبات الخارجية</td>
                        <td><i class="bi bi-dash text-muted fs-4"></i></td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                    </tr>
                    <tr>
                        <td>إدارة أقساط العلاج والمدفوعات المتأخرة</td>
                        <td><i class="bi bi-dash text-muted fs-4"></i></td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                    </tr>
                    <tr>
                        <td>احتساب نسب وأرباح الأطباء الشركاء</td>
                        <td><i class="bi bi-dash text-muted fs-4"></i></td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                    </tr>
                    <tr>
                        <td>تتبع مصروفات العيادة وصافي الأرباح</td>
                        <td><i class="bi bi-dash text-muted fs-4"></i></td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                    </tr>

                    <!-- Section: Infrastructure & Support -->
                    <tr>
                        <td colspan="4" class="table-section-title">⚡ الاستضافة، الأمان والدعم</td>
                    </tr>
                    <tr>
                        <td>خوادم سحابية سريعة LiteSpeed بتشفير SSL</td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                    </tr>
                    <tr>
                        <td>نسخ احتياطي يومي سحابي للبيانات</td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                    </tr>
                    <tr>
                        <td>نقل بيانات المرضى السابقة للعيادة</td>
                        <td>إرشادات استيراد</td>
                        <td>مساعدة مجدولة</td>
                        <td class="fw-bold text-success"><i class="bi bi-check-circle-fill text-success me-1"></i> مجاناً بالكامل من فريقنا</td>
                    </tr>
                    <tr>
                        <td>قنوات الدعم الفني</td>
                        <td>واتساب وإيميل</td>
                        <td class="fw-bold text-primary">واتساب وهاتف مباشر</td>
                        <td class="fw-bold text-success">مدير حساب مخصص + هاتف 24/7</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- ==========================================================
     FREQUENTLY ASKED QUESTIONS (FAQ)
     ========================================================== -->
<section class="py-5 bg-light border-top">
    <div class="container py-lg-4" style="max-width: 860px;">
        <div class="text-center mb-5">
            <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill fw-bold mb-2">إجابات واضحة</span>
            <h2 class="fw-extrabold text-dark">الأسئلة الأكثر شيوعاً حول الاشتراكات والأسعار</h2>
            <p class="text-muted">كل ما تود معرفته قبل بدء استخدام البرنامج في عيادتك.</p>
        </div>

        <div class="accordion shadow-sm rounded-4 overflow-hidden" id="pricingFaqAccordion">
            <!-- Item 1 -->
            <div class="accordion-item border-0 border-bottom">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button fw-bold text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <i class="bi bi-question-circle text-primary me-2 fs-5"></i>
                        كيف تعمل التجربة المجانية لمدة 14 يوماً؟ وهل تتطلب بطاقة بنكية؟
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#pricingFaqAccordion">
                    <div class="accordion-body text-muted leading-relaxed">
                        التجربة المجانية لا تتطلب أي بطاقة بنكية أو رسوم مسبقة. نمنحك حساباً فعالاً بكامل الصلاحيات لتجربة مخطط الأسنان، إضافة المرضى، وتنظيم المواعيد داخل عيادتك لمدة 14 يوماً. إذا ناسبك البرنامج يمكنك الدفع وتأكيد الاشتراك، وإذا لم ترغب بالاستمرار فلن يُطلب منك أي شيء.
                    </div>
                </div>
            </div>

            <!-- Item 2 -->
            <div class="accordion-item border-0 border-bottom">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed fw-bold text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        <i class="bi bi-shield-lock text-primary me-2 fs-5"></i>
                        هل بيانات مرضاي وفواتيري في أمان وخصوصية تامة؟
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#pricingFaqAccordion">
                    <div class="accordion-body text-muted leading-relaxed">
                        نعم بكل تأكيد. بيانات كل عيادة معزولة رقمياً بشكل مستقل، ومحمية بتشفير 256-Bit SSL مماثل للأنظمة المصرفية. يتم حفظ نسخ احتياطية سحابية يومية لحماية ملفاتك وصورك من أي ضياع، ولا يمكن لأي عيادة أخرى أو طرف ثالث الاطلاع على سجلاتك الطبية.
                    </div>
                </div>
            </div>

            <!-- Item 3 -->
            <div class="accordion-item border-0 border-bottom">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed fw-bold text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        <i class="bi bi-laptop text-primary me-2 fs-5"></i>
                        هل أحتاج أجهزة بمواصفات معينة لتشغيل البرنامج في العيادة؟
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#pricingFaqAccordion">
                    <div class="accordion-body text-muted leading-relaxed">
                        لا، إطلاقاً! البرنامج سحابي بالكامل (Cloud Native) ويعمل عبر متصفح الويب (كروم، سفاري، إيدج). يمكنك استخدامه من أي جهاز كمبيوتر، لابتوب، جهاز لوحي (iPad أو Android Tablet) بجانب كرسي المريض، أو حتى من هاتفك الذكي لمتابعة مواعيدك وإحصائياتك من خارج العيادة.
                    </div>
                </div>
            </div>

            <!-- Item 4 -->
            <div class="accordion-item border-0 border-bottom">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed fw-bold text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        <i class="bi bi-arrow-up-circle text-primary me-2 fs-5"></i>
                        هل يمكنني ترقية باقتي لاحقاً عند إضافة طبيب أو سكرتارية جدد؟
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#pricingFaqAccordion">
                    <div class="accordion-body text-muted leading-relaxed">
                        نعم بكل بساطة. يمكنك في أي وقت طلب ترقية باقتك لزيادة عدد المستخدمين ومساحة التخزين، وسيتم احتساب الفرق المتبقي من مدة اشتراكك فقط دون أن تخسر أي بيانات أو إعدادات سابقة.
                    </div>
                </div>
            </div>

            <!-- Item 5 -->
            <div class="accordion-item border-0">
                <h2 class="accordion-header" id="headingFive">
                    <button class="accordion-button collapsed fw-bold text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                        <i class="bi bi-file-earmark-arrow-up text-primary me-2 fs-5"></i>
                        لدي بيانات مرضى مسجلة على إكسل أو برنامج قديم، كيف أنقلها؟
                    </button>
                </h2>
                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#pricingFaqAccordion">
                    <div class="accordion-body text-muted leading-relaxed">
                        فريق الدعم الفني لدينا يقدم مساعدة كاملة في تصدير واستيراد بيانات المرضى وأرقام الهواتف من ملفات Excel السابقة ودمجها في حساب عيادتك الجديد مجاناً لتتمكن من العمل فوراً دون أي انقطاع.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================
     DIRECT CTA BANNER
     ========================================================== -->
<section class="py-5 text-white position-relative" style="background: linear-gradient(135deg, #0b192c 0%, #0369a1 60%, #0d9488 100%);">
    <div class="container py-lg-4 text-center">
        <h2 class="display-5 fw-extrabold mb-3">هل لديك استفسار أو ترغب في عرض توضيحي مباشر؟</h2>
        <p class="lead text-white-50 mx-auto mb-4" style="max-width: 650px;">
            مستشارو الأنظمة الطبية جاهزون لمساعدتك في اختيار الباقة الأنسب لعيادتك والإجابة على كل أسئلتك.
        </p>
        <div class="d-flex flex-wrap justify-content-center align-items-center gap-3">
            <a href="https://wa.me/201553408200?text=مرحباً، أود التحدث مع مستشار الأنظمة بخصوص باقات Dental Pro ERP لعيادات الأسنان" 
               target="_blank" 
               class="btn btn-success btn-unified-cta shadow-lg">
                <i class="bi bi-whatsapp fs-5"></i>
                <span>محادثة واتساب فورية</span>
            </a>
            <a href="tel:+201553408200" 
               class="btn btn-outline-light btn-unified-cta shadow-sm">
                <i class="bi bi-telephone-fill fs-5"></i>
                <span>اتصال هاتفي مباشر</span>
            </a>
        </div>
        <div class="d-flex align-items-center justify-content-center gap-2 mt-3 text-white-50 small">
            <i class="bi bi-clock-history text-warning"></i>
            <span>فريق الاستشارات متاح لخدمتكم يومياً | </span>
            <span dir="ltr" class="text-white fw-bold font-monospace">+201553408200</span>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    let currentBilling = 'annual';

    function setBillingCycle(cycle) {
        currentBilling = cycle;

        const btnAnnual = document.getElementById('btn-annual');
        const btnMonthly = document.getElementById('btn-monthly');

        if (cycle === 'annual') {
            btnAnnual.classList.add('active');
            btnMonthly.classList.remove('active');
        } else {
            btnMonthly.classList.add('active');
            btnAnnual.classList.remove('active');
        }

        // Update all price cards
        document.querySelectorAll('.pricing-card').forEach(card => {
            const priceEl = card.querySelector('.tier-price');
            const periodEl = card.querySelector('.tier-period');
            const subtextEl = card.querySelector('.tier-subtext');
            const ctaBtn = card.querySelector('.tier-cta-btn');

            if (!priceEl) return;

            const monthlyVal = parseInt(priceEl.getAttribute('data-monthly'));
            const annualVal = parseInt(priceEl.getAttribute('data-annual').replace(',', ''));
            const baseText = ctaBtn ? ctaBtn.getAttribute('data-base-text') : '';

            if (cycle === 'annual') {
                priceEl.textContent = annualVal.toLocaleString();
                periodEl.textContent = '/ سنوياً';
                
                const effectiveMonthly = Math.round(annualVal / 12);
                const savedAmount = (monthlyVal * 12) - annualVal;
                subtextEl.innerHTML = `يعادل <span class="fw-extrabold font-monospace">${effectiveMonthly.toLocaleString()} ج.م</span> فقط شهرياً (وفرت ${savedAmount.toLocaleString()} ج.م)`;
                subtextEl.classList.remove('d-none');

                if (ctaBtn) {
                    const planMsg = `${baseText} - الدفع السنوي (${annualVal.toLocaleString()} ج.م/سنة)`;
                    ctaBtn.href = `https://wa.me/201553408200?text=${encodeURIComponent(planMsg)}`;
                }
            } else {
                priceEl.textContent = monthlyVal.toLocaleString();
                periodEl.textContent = '/ شهرياً';
                subtextEl.innerHTML = `اشتراك شهري مرن يُجدد كل شهر`;

                if (ctaBtn) {
                    const planMsg = `${baseText} - الدفع الشهري (${monthlyVal.toLocaleString()} ج.م/شهر)`;
                    ctaBtn.href = `https://wa.me/201553408200?text=${encodeURIComponent(planMsg)}`;
                }
            }
        });
    }

    // Default init
    document.addEventListener('DOMContentLoaded', function() {
        setBillingCycle('annual');
    });
</script>
@endpush

