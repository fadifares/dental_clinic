@extends('layouts.app')

@section('title', 'دنتال كير | مركز طب وجراحة وتجميل الأسنان المتقدم')

@section('content')

<!-- Hero Section -->
<section class="bg-clinic-hero py-5 position-relative overflow-hidden">
    <div class="container py-lg-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 text-start">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold mb-3">
                    <i class="bi bi-patch-check-fill me-1"></i> المركز الرائد لطب وتجميل الأسنان الرقمي
                </span>
                <h1 class="display-4 fw-extrabold text-dark lh-base mb-3">
                    ابتسامتك المثالية تبدأ <br>
                    <span class="text-gradient">بخبرة طبية وتقنيات عالمية</span>
                </h1>
                <p class="lead text-muted mb-4 fs-6">
                    نجمع بين الفن والدقة الطبية لتقديم حلول شاملة لصحة وجمال أسنانك، من الزراعة الفورية بدون ألم وتصميم ابتسامة هوليوود الرقمية وحتى التقويم الشفاف.
                </p>

                <div class="d-flex flex-wrap gap-3 mb-4">
                    <a href="#booking-section" class="btn btn-primary btn-lg rounded-pill px-4 py-3 shadow-sm fw-bold">
                        <i class="bi bi-calendar2-check-fill me-2"></i> احجز استشارتك الآن
                    </a>
                    <a href="tel:920001234" class="btn btn-outline-dark btn-lg rounded-pill px-4 py-3 fw-bold">
                        <i class="bi bi-telephone-outbound-fill me-2"></i> اتصل بالطوارئ
                    </a>
                </div>

                <!-- Live Clinic Highlights -->
                <div class="row g-3 pt-3 border-top">
                    <div class="col-4">
                        <div class="fw-bold fs-4 text-dark">+18,000</div>
                        <div class="small text-muted">ابتسامة تم علاجها</div>
                    </div>
                    <div class="col-4 border-start border-end">
                        <div class="fw-bold fs-4 text-primary">15+</div>
                        <div class="small text-muted">طبيب واستشاري</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold fs-4 text-success">99.2%</div>
                        <div class="small text-muted">نسبة رضا المرضى</div>
                    </div>
                </div>
            </div>

            <!-- Hero Image & Interactive Quick Booking Card -->
            <div class="col-lg-6" id="booking-section">
                <div class="clinic-card p-4 p-md-5 border-0 shadow-lg position-relative">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <span class="badge bg-success-subtle text-success px-2 py-1 rounded">حجز فوري مباشر</span>
                            <h4 class="fw-bold mb-0 mt-1">حجز موعد كشف أونلاين</h4>
                        </div>
                        <span class="clinic-stat-icon bg-light text-primary">
                            <i class="bi bi-calendar-event fs-3"></i>
                        </span>
                    </div>

                    <form id="publicBookingForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">اسم المريض بالكامل</label>
                                <div class="clinic-input-icon">
                                    <i class="bi bi-person"></i>
                                    <input type="text" class="form-control" id="patientName" placeholder="مثال: محمد عبدالله" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">رقم الجوال (واتساب)</label>
                                <div class="clinic-input-icon">
                                    <i class="bi bi-phone"></i>
                                    <input type="tel" class="form-control" id="patientPhone" placeholder="05XXXXXXXX" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">القسم أو الخدمة المطلوبة</label>
                                <select class="form-select bg-light" id="bookingService" required>
                                    <option value="" selected disabled>اختر التخصص...</option>
                                    <option value="implant">زراعة الأسنان الفورية</option>
                                    <option value="cosmetic">ابتسامة هوليود وفينير</option>
                                    <option value="orthodontics">التقويم الشفاف والمعدني</option>
                                    <option value="endodontics">علاج العصب والجذور المجهري</option>
                                    <option value="pediatric">طب أسنان الأطفال</option>
                                    <option value="cleaning">تنظيف وتبييض الأسنان بالليزر</option>
                                    <option value="checkup">كشف عام وطوارئ أسنان</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">الطبيب المفضل</label>
                                <select class="form-select bg-light" id="bookingDoctor">
                                    <option value="any">أول طبيب متاح</option>
                                    <option value="dr_ahmed">د. أحمد السالم (استشاري جراحة وزراعة)</option>
                                    <option value="dr_sara">د. سارة المنصور (أخصائية تجميل وفينير)</option>
                                    <option value="dr_khalid">د. خالد القحطاني (استشاري علاج جذور)</option>
                                    <option value="dr_reem">د. ريم الشمري (أخصائية أسنان أطفال)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">التاريخ المناسب</label>
                                <input type="date" class="form-control bg-light" id="bookingDate" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">الفترة المناسبة</label>
                                <select class="form-select bg-light" id="bookingTime">
                                    <option value="morning">الفترة الصباحية (9:00 ص - 1:00 م)</option>
                                    <option value="evening" selected>الفترة المسائية (4:00 م - 9:30 م)</option>
                                </select>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold fs-6 shadow-sm">
                                    <i class="bi bi-check2-circle me-1"></i> تأكيد إرسال طلب الحجز
                                </button>
                                <div class="text-center mt-2 small text-muted">
                                    <i class="bi bi-shield-check text-success"></i> سيتم التواصل معك خلال 10 دقائق لتأكيد وقت الجلسة عبر واتساب
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Alert message on booking submit (jQuery handled) -->
                    <div id="bookingSuccessAlert" class="alert alert-success d-none mt-3 rounded-4 p-3 text-center" role="alert">
                        <i class="bi bi-check-circle-fill fs-3 text-success d-block mb-1"></i>
                        <h6 class="fw-bold mb-1">تم استلام طلب حجزك بنجاح!</h6>
                        <p class="small mb-0">تم تحويل طلبك لفريق الاستقبال وسنرسل لك رسالة تأكيد فورية عبر الواتساب.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-5" id="services">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold">رعاية طبية تخصصية</span>
            <h2 class="fw-bold mt-2 fs-1 text-dark">أقسام وخدمات عيادتنا المتطورة</h2>
            <p class="text-muted">نوفر كافة التخصصات الدقيقة تحت سقف واحد بأعلى معايير التعقيم والراحة التامة بدون ألم.</p>
        </div>

        <div class="row g-4">
            <!-- Service 1 -->
            <div class="col-md-6 col-lg-4">
                <div class="clinic-card p-4 h-100 position-relative">
                    <div class="clinic-stat-icon bg-primary-subtle text-primary mb-3">
                        <i class="bi bi-shield-plus fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark">زراعة الأسنان الفورية</h5>
                    <p class="text-muted small">
                        تعويض الأسنان المفقودة بأجود الغرسات الألمانية والسويسرية في جلسة واحدة مع توجيه حاسوبي ثلاثي الأبعاد لضمان أعلى ثبات ودقة.
                    </p>
                    <ul class="list-unstyled small text-secondary d-flex flex-column gap-1 mb-3">
                        <li><i class="bi bi-check2 text-primary me-1"></i> بدون جراحة تقليدية وبدون ألم</li>
                        <li><i class="bi bi-check2 text-primary me-1"></i> ضمان مدى الحياة على الغرسات</li>
                    </ul>
                    <a href="#booking-section" class="stretched-link text-primary fw-semibold small text-decoration-none">
                        حجز استشارة زراعة <i class="bi bi-arrow-left"></i>
                    </a>
                </div>
            </div>

            <!-- Service 2 -->
            <div class="col-md-6 col-lg-4">
                <div class="clinic-card p-4 h-100 position-relative">
                    <div class="clinic-stat-icon bg-info-subtle text-info mb-3">
                        <i class="bi bi-stars fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark">ابتسامة هوليود وتجميل الفينير</h5>
                    <p class="text-muted small">
                        عدسات فينير وإيماكس (E-Max) فائقة الرقة مصممة خصيصاً لتناسب تفاصيل وجهك ودرجة البياض الطبيعية بأحدث تقنيات الـ CAD/CAM.
                    </p>
                    <ul class="list-unstyled small text-secondary d-flex flex-column gap-1 mb-3">
                        <li><i class="bi bi-check2 text-info me-1"></i> معاينة الابتسامة قبل البدء (Digital Mockup)</li>
                        <li><i class="bi bi-check2 text-info me-1"></i> مقاومة للتصبغات ودرجة لمعان طبيعية</li>
                    </ul>
                    <a href="#booking-section" class="stretched-link text-info fw-semibold small text-decoration-none">
                        حجز استشارة تجميل <i class="bi bi-arrow-left"></i>
                    </a>
                </div>
            </div>

            <!-- Service 3 -->
            <div class="col-md-6 col-lg-4">
                <div class="clinic-card p-4 h-100 position-relative">
                    <div class="clinic-stat-icon bg-success-subtle text-success mb-3">
                        <i class="bi bi-arrow-repeat fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark">التقويم الشفاف (Clear Aligners)</h5>
                    <p class="text-muted small">
                        قوالب غير مرئية وقابلة للإزالة لتعديل صفة الأسنان دون إحراج التقويم المعدني، مع متابعة دورية ومحاكاة لمراحل التحسن.
                    </p>
                    <ul class="list-unstyled small text-secondary d-flex flex-column gap-1 mb-3">
                        <li><i class="bi bi-check2 text-success me-1"></i> مظهر غير مرئي وسهولة في تنظيف الأسنان</li>
                        <li><i class="bi bi-check2 text-success me-1"></i> خطة علاجية دقيقة مقسمة على مراحل</li>
                    </ul>
                    <a href="#booking-section" class="stretched-link text-success fw-semibold small text-decoration-none">
                        كشف التقويم <i class="bi bi-arrow-left"></i>
                    </a>
                </div>
            </div>

            <!-- Service 4 -->
            <div class="col-md-6 col-lg-4">
                <div class="clinic-card p-4 h-100 position-relative">
                    <div class="clinic-stat-icon bg-warning-subtle text-warning mb-3">
                        <i class="bi bi-activity fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark">علاج الجذور والعصب المجهري</h5>
                    <p class="text-muted small">
                        إنقاذ الأسنان المتضررة وإزالة الآلام المزعجة في جلسة واحدة مريحة باستخدام المجهر الجراحي المكبر ومحددات الذروة الإلكترونية.
                    </p>
                    <ul class="list-unstyled small text-secondary d-flex flex-column gap-1 mb-3">
                        <li><i class="bi bi-check2 text-warning me-1"></i> تعقيم فائق بقنوات الجذور بأشعة الليزر</li>
                        <li><i class="bi bi-check2 text-warning me-1"></i> بدون أي شعور بالألم مع التخدير الرقمي</li>
                    </ul>
                    <a href="#booking-section" class="stretched-link text-warning fw-semibold small text-decoration-none">
                        حجز علاج عصب <i class="bi bi-arrow-left"></i>
                    </a>
                </div>
            </div>

            <!-- Service 5 -->
            <div class="col-md-6 col-lg-4">
                <div class="clinic-card p-4 h-100 position-relative">
                    <div class="clinic-stat-icon bg-danger-subtle text-danger mb-3">
                        <i class="bi bi-emoji-smile fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark">طب أسنان الأطفال وسلوكيات الراحة</h5>
                    <p class="text-muted small">
                        عيادة مجهزة خصيصاً لأحبائنا الصغار بأجواء ترفيهية وودودة مع أخصائيين مدربين على إزالة رهبة الطبيب وتطبيق الفلورايد الوقائي.
                    </p>
                    <ul class="list-unstyled small text-secondary d-flex flex-column gap-1 mb-3">
                        <li><i class="bi bi-check2 text-danger me-1"></i> توفير غاز الضحك (النيتروس) لراحة الطفل</li>
                        <li><i class="bi bi-check2 text-danger me-1"></i> سد الشقوق وحماية الأسنان اللبنية من التسوس</li>
                    </ul>
                    <a href="#booking-section" class="stretched-link text-danger fw-semibold small text-decoration-none">
                        حجز موعد لطفلك <i class="bi bi-arrow-left"></i>
                    </a>
                </div>
            </div>

            <!-- Service 6 -->
            <div class="col-md-6 col-lg-4">
                <div class="clinic-card p-4 h-100 position-relative">
                    <div class="clinic-stat-icon bg-primary-subtle text-primary mb-3">
                        <i class="bi bi-gem fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark">تبييض الأسنان بالليزر والتنظيف</h5>
                    <p class="text-muted small">
                        إزالة الجير والتصبغات العميقة واستعادة بياض الأسنان الطبيعي لعدة درجات خلال 45 دقيقة فقط بدون حساسية الأسنان المفرطة.
                    </p>
                    <ul class="list-unstyled small text-secondary d-flex flex-column gap-1 mb-3">
                        <li><i class="bi bi-check2 text-primary me-1"></i> تقنية التبييض البارد لحماية طبقة المينا</li>
                        <li><i class="bi bi-check2 text-primary me-1"></i> تنظيف وصقل عميق يمنح اللثة انتعاشاً وصحة</li>
                    </ul>
                    <a href="#booking-section" class="stretched-link text-primary fw-semibold small text-decoration-none">
                        حجز جلسة تبييض <i class="bi bi-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Before & After Showcase -->
<section class="py-5 bg-white border-top border-bottom" id="before-after">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold">نتائج ملموسة</span>
            <h2 class="fw-bold mt-2 fs-1 text-dark">معرض حالات قبل وبعد</h2>
            <p class="text-muted">شاهد الفارق الحقيقي الذي صنعناه لمرضانا الكرام بفضل الله ثم خبرة كادرنا وتقنياتنا.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="clinic-card overflow-hidden">
                    <div class="bg-light p-4 text-center border-bottom">
                        <div class="d-flex justify-content-around">
                            <span class="badge bg-secondary">قبل: تباعد وتصبغات</span>
                            <span class="badge bg-success">بعد: فينير إيماكس 16 سن</span>
                        </div>
                        <div class="py-4">
                            <i class="bi bi-stars text-warning display-4"></i>
                            <div class="mt-2 fw-bold text-dark">تصميم ابتسامة هوليوود</div>
                            <span class="small text-muted">د. سارة المنصور</span>
                        </div>
                    </div>
                    <div class="p-3 bg-white text-center">
                        <p class="small text-muted mb-0">تم إنجاز الحالة خلال جلستين فقط بدون أي برد جائر لطبقة المينا.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="clinic-card overflow-hidden">
                    <div class="bg-light p-4 text-center border-bottom">
                        <div class="d-flex justify-content-around">
                            <span class="badge bg-secondary">قبل: فقدان 3 أسنان</span>
                            <span class="badge bg-primary">بعد: زراعة فورية وتيجان زيركون</span>
                        </div>
                        <div class="py-4">
                            <i class="bi bi-shield-check text-primary display-4"></i>
                            <div class="mt-2 fw-bold text-dark">زراعة أسنان أمامية</div>
                            <span class="small text-muted">د. أحمد السالم</span>
                        </div>
                    </div>
                    <div class="p-3 bg-white text-center">
                        <p class="small text-muted mb-0">تركيب تاج فوري مؤقت في نفس الجلسة وثبات متكامل بنسبة 100%.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="clinic-card overflow-hidden">
                    <div class="bg-light p-4 text-center border-bottom">
                        <div class="d-flex justify-content-around">
                            <span class="badge bg-secondary">قبل: تزاحم شديد</span>
                            <span class="badge bg-info">بعد: اصطفاف مثالي</span>
                        </div>
                        <div class="py-4">
                            <i class="bi bi-arrow-repeat text-info display-4"></i>
                            <div class="mt-2 fw-bold text-dark">تقويم شفاف غير مرئي</div>
                            <span class="small text-muted">د. خالد القحطاني</span>
                        </div>
                    </div>
                    <div class="p-3 bg-white text-center">
                        <p class="small text-muted mb-0">فترة علاج استغرقت 7 أشهر فقط بفضل التخطيط الرقمي المسبق.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Doctors Team -->
<section class="py-5" id="doctors">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold">نخبة الكفاءات الطبية</span>
            <h2 class="fw-bold mt-2 fs-1 text-dark">فريق أطباء واستشاريي العيادة</h2>
            <p class="text-muted">نخبة من حملة البورد والزمالات الدولية في تخصصات جراحة، تجميل، وعلاج الأسنان.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="clinic-card p-4 text-center h-100">
                    <div class="clinic-stat-icon bg-primary text-white mx-auto mb-3" style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-1">د. أحمد السالم</h5>
                    <span class="badge bg-primary-subtle text-primary mb-3">استشاري جراحة وزراعة الأسنان</span>
                    <p class="small text-muted mb-3">
                        زمالة الكلية الملكية للجراحين، خبرة أكثر من 16 عاماً في الزراعة الموجهة بالكمبيوتر.
                    </p>
                    <a href="#booking-section" class="btn btn-outline-primary btn-sm rounded-pill w-100">حجز موعد كشف</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="clinic-card p-4 text-center h-100">
                    <div class="clinic-stat-icon bg-info text-white mx-auto mb-3" style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-1">د. سارة المنصور</h5>
                    <span class="badge bg-info-subtle text-info mb-3">أخصائية تجميل وابتسامة هوليود</span>
                    <p class="small text-muted mb-3">
                        ماجستير طب الأسنان التجميلي من جامعة لندن، خبيرة في تصميم الابتسامة ثلاثية الأبعاد.
                    </p>
                    <a href="#booking-section" class="btn btn-outline-info btn-sm rounded-pill w-100">حجز موعد كشف</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="clinic-card p-4 text-center h-100">
                    <div class="clinic-stat-icon bg-success text-white mx-auto mb-3" style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-1">د. خالد القحطاني</h5>
                    <span class="badge bg-success-subtle text-success mb-3">استشاري تقويم وتعديل الفكين</span>
                    <p class="small text-muted mb-3">
                        البورد الأمريكي في تقويم الأسنان، معتمد دولياً للتقويم الشفاف (Invisalign Diamond Provider).
                    </p>
                    <a href="#booking-section" class="btn btn-outline-success btn-sm rounded-pill w-100">حجز موعد كشف</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="clinic-card p-4 text-center h-100">
                    <div class="clinic-stat-icon bg-danger text-white mx-auto mb-3" style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-1">د. ريم الشمري</h5>
                    <span class="badge bg-danger-subtle text-danger mb-3">أخصائية طب أسنان الأطفال</span>
                    <p class="small text-muted mb-3">
                        ماجستير طب أسنان الأطفال الوقائي والسلوكي، خبرة واسعة في التعامل مع خوف الأطفال وذوي الاحتياجات.
                    </p>
                    <a href="#booking-section" class="btn btn-outline-danger btn-sm rounded-pill w-100">حجز موعد كشف</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5 bg-light" id="testimonials">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold">تجارب واقعية</span>
            <h2 class="fw-bold mt-2 fs-1 text-dark">ماذا يقول مراجعونا عن دنتال كير؟</h2>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="clinic-card p-4 h-100">
                    <div class="text-warning mb-2 fs-5">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <p class="small text-secondary mb-3">
                        "تجربتي في زراعة ضرسين مع دكتور أحمد السالم كانت لا تصدق! كنت خائفاً جداً من الألم ولكن الإجراء تم بسلاسة وبدون أي وجع يذكر والتعامل راقي جداً."
                    </p>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary-subtle text-primary p-2 fw-bold small">ع.غ</div>
                        <div>
                            <div class="fw-bold small text-dark">عبدالعزيز الغامدي</div>
                            <div class="text-muted" style="font-size: 0.75rem;">مريض زراعة أسنان</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="clinic-card p-4 h-100">
                    <div class="text-warning mb-2 fs-5">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <p class="small text-secondary mb-3">
                        "عملت ابتسامة الفينير عند دكتورة سارة والنتيجة طبيعية جداً وبيضاء بشكل هادئ غير مصطنع، أكثر شيء أعجبني هو احترام المواعيد بالدقيقة."
                    </p>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-info-subtle text-info p-2 fw-bold small">ن.ش</div>
                        <div>
                            <div class="fw-bold small text-dark">نورة الشهراني</div>
                            <div class="text-muted" style="font-size: 0.75rem;">مريضة تجميل وفينير</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="clinic-card p-4 h-100">
                    <div class="text-warning mb-2 fs-5">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <p class="small text-secondary mb-3">
                        "ابنتي كانت تبكي بمجرد رؤية كرسي الأسنان، لكن دكتورة ريم غيرت فكرتها تماماً وأصبحت تسألني متى موعدنا القادم! نظافة العيادة فوق الممتازة."
                    </p>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-success-subtle text-success p-2 fw-bold small">س.ع</div>
                        <div>
                            <div class="fw-bold small text-dark">سعود العتيبي</div>
                            <div class="text-muted" style="font-size: 0.75rem;">ولي أمر مريضة</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Handle Public Booking Form Submission with jQuery
    $(function () {
        $('#publicBookingForm').on('submit', function (e) {
            e.preventDefault();
            
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> جاري إرسال الطلب...');

            setTimeout(function() {
                $('#publicBookingForm').slideUp(300);
                $('#bookingSuccessAlert').removeClass('d-none').hide().fadeIn(400);
            }, 800);
        });
    });
</script>
@endpush
