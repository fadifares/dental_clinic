@extends('layouts.clinic')

@section('title', 'لوحة التحكم المركزية | دنتال برو لإدارة العيادات')

@section('content')

<!-- Page Title & Quick Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">لوحة القيادة والتشغيل اليومي 🩺</h3>
        <p class="text-muted small mb-0">نظام إدارة العيادات الشامل • متصل بقاعدة بيانات MySQL (Dental)</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="window.location.reload();">
            <i class="bi bi-arrow-clockwise me-1"></i> تحديث
        </button>
        <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#newAppointmentModal">
            <i class="bi bi-calendar-plus me-1"></i> تسجيل موعد جديد
        </button>
    </div>
</div>

<!-- Alert Banner for AJAX feedback -->
<div id="ajaxAlert" class="alert alert-success d-none rounded-4 p-3 shadow-sm mb-4" role="alert">
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <span id="ajaxAlertText">تم حفظ البيانات بنجاح</span>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <!-- Stat 1 -->
    <div class="col-sm-6 col-xl-3">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">مواعيد اليوم الكلية</span>
                <h3 class="fw-bold text-dark mb-0" id="totalAppointmentsCount">{{ $totalAppointmentsCount }}</h3>
                <span class="badge bg-success-subtle text-success small mt-1">
                    <i class="bi bi-check2"></i> مجدولة اليوم
                </span>
            </div>
            <div class="clinic-stat-icon bg-primary-subtle text-primary">
                <i class="bi bi-calendar-check fs-4"></i>
            </div>
        </div>
    </div>

    <!-- Stat 2 -->
    <div class="col-sm-6 col-xl-3">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">في صالة الانتظار</span>
                <h3 class="fw-bold text-dark mb-0 text-warning" id="waitingPatientsCount">{{ $waitingCount }}</h3>
                <span class="badge bg-warning-subtle text-warning small mt-1">حالات جاهزة للدخول</span>
            </div>
            <div class="clinic-stat-icon bg-warning-subtle text-warning">
                <i class="bi bi-hourglass-split fs-4"></i>
            </div>
        </div>
    </div>

    <!-- Stat 3 -->
    <div class="col-sm-6 col-xl-3">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">تحصيل اليوم (سندات القبض)</span>
                <h3 class="fw-bold text-dark mb-0 text-success">{{ number_format($todayCollection, 2) }} <small class="fs-6 text-muted">ر.س</small></h3>
                <span class="badge bg-success-subtle text-success small mt-1">
                    <i class="bi bi-shield-check"></i> مدفوع بالكامل
                </span>
            </div>
            <div class="clinic-stat-icon bg-success-subtle text-success">
                <i class="bi bi-cash-stack fs-4"></i>
            </div>
        </div>
    </div>

    <!-- Stat 4 -->
    <div class="col-sm-6 col-xl-3">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">طلبيات معمل نشطة</span>
                <h3 class="fw-bold text-dark mb-0 text-info">{{ $activeLabOrdersCount }}</h3>
                <span class="badge bg-info-subtle text-info small mt-1">تيجان وجسور وقوالب</span>
            </div>
            <div class="clinic-stat-icon bg-info-subtle text-info">
                <i class="bi bi-box-seam fs-4"></i>
            </div>
        </div>
    </div>
</div>

<!-- SECTION: Interactive Odontogram (Dental Chart) -->
<div class="clinic-card p-4 mb-4" id="odontogram-section">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3 pb-3 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary text-white p-2 rounded-3"><i class="bi bi-grid-3x3-gap-fill"></i></span>
                <div>
                    <h5 class="fw-bold text-dark mb-0">مخطط الأسنان التفاعلي (Interactive Odontogram EMR)</h5>
                    <small class="text-muted">نظام الترقيم الدولي FDI • يتم الحفظ الفوري لكل سن في قاعدة البيانات MySQL</small>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <span class="badge bg-light text-dark border"><span class="badge bg-success me-1">●</span> سليم</span>
            <span class="badge bg-light text-dark border"><span class="badge bg-danger me-1">●</span> تسوس</span>
            <span class="badge bg-light text-dark border"><span class="badge bg-primary me-1">●</span> حشوة</span>
            <span class="badge bg-light text-dark border"><span class="badge bg-warning me-1">●</span> تاج</span>
            <span class="badge bg-light text-dark border"><span class="badge bg-secondary me-1">●</span> مخلوع</span>
            <span class="badge bg-light text-dark border"><span class="badge text-white me-1" style="background:#8b5cf6;">●</span> زراعة</span>
            <span class="badge bg-light text-dark border"><span class="badge text-white me-1" style="background:#ec4899;">●</span> علاج عصب</span>
        </div>
    </div>

    <!-- Active Patient Banner in Chart -->
    @if($activePatient)
    <div class="bg-light rounded-3 p-3 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2 border">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                {{ mb_substr($activePatient->name, 0, 1) }}
            </div>
            <div>
                <span class="fw-bold text-dark">المريض الحالي: {{ $activePatient->name }}</span>
                @if($activePatient->allergies)
                    <span class="badge bg-danger-subtle text-danger ms-2"><i class="bi bi-exclamation-triangle-fill"></i> {{ $activePatient->allergies }}</span>
                @endif
                <div class="small text-muted">ملف رقم: #{{ $activePatient->file_number }} • هاتف: {{ $activePatient->phone }}</div>
            </div>
        </div>
        <div>
            <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill">
                <i class="bi bi-database me-1"></i> متصل بـ MySQL
            </span>
        </div>
    </div>
    @endif

    <!-- UPPER JAW (Maxilla) -->
    <div class="text-center mb-4">
        <div class="small fw-bold text-muted mb-2">الفك العلوي (Upper Arch - Maxilla)</div>
        <div class="dental-arch justify-content-center">
            <!-- Upper Right (18 down to 11) -->
            @foreach([18, 17, 16, 15, 14, 13, 12, 11] as $tNum)
                @php $status = $patientToothConditions[$tNum] ?? 'healthy'; @endphp
                <div class="tooth-item" data-tooth-id="{{ $tNum }}" data-tooth-name="السن رقم {{ $tNum }}" data-status="{{ $status }}">
                    <div class="tooth-visual">
                        <div class="surface-grid">
                            <span class="surface"></span>
                            <span class="surface"></span>
                            <span class="surface"></span>
                            <span class="surface"></span>
                        </div>
                    </div>
                    <span class="tooth-number">{{ $tNum }}</span>
                </div>
            @endforeach

            <!-- Divider -->
            <div class="border-start border-2 border-secondary mx-2 align-self-stretch" style="opacity: 0.3;"></div>

            <!-- Upper Left (21 up to 28) -->
            @foreach([21, 22, 23, 24, 25, 26, 27, 28] as $tNum)
                @php $status = $patientToothConditions[$tNum] ?? 'healthy'; @endphp
                <div class="tooth-item" data-tooth-id="{{ $tNum }}" data-tooth-name="السن رقم {{ $tNum }}" data-status="{{ $status }}">
                    <div class="tooth-visual">
                        <div class="surface-grid">
                            <span class="surface"></span>
                            <span class="surface"></span>
                            <span class="surface"></span>
                            <span class="surface"></span>
                        </div>
                    </div>
                    <span class="tooth-number">{{ $tNum }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- LOWER JAW (Mandible) -->
    <div class="text-center">
        <div class="dental-arch justify-content-center">
            <!-- Lower Right (48 down to 41) -->
            @foreach([48, 47, 46, 45, 44, 43, 42, 41] as $tNum)
                @php $status = $patientToothConditions[$tNum] ?? 'healthy'; @endphp
                <div class="tooth-item" data-tooth-id="{{ $tNum }}" data-tooth-name="السن رقم {{ $tNum }}" data-status="{{ $status }}">
                    <span class="tooth-number">{{ $tNum }}</span>
                    <div class="tooth-visual">
                        <div class="surface-grid">
                            <span class="surface"></span>
                            <span class="surface"></span>
                            <span class="surface"></span>
                            <span class="surface"></span>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Divider -->
            <div class="border-start border-2 border-secondary mx-2 align-self-stretch" style="opacity: 0.3;"></div>

            <!-- Lower Left (31 up to 38) -->
            @foreach([31, 32, 33, 34, 35, 36, 37, 38] as $tNum)
                @php $status = $patientToothConditions[$tNum] ?? 'healthy'; @endphp
                <div class="tooth-item" data-tooth-id="{{ $tNum }}" data-tooth-name="السن رقم {{ $tNum }}" data-status="{{ $status }}">
                    <span class="tooth-number">{{ $tNum }}</span>
                    <div class="tooth-visual">
                        <div class="surface-grid">
                            <span class="surface"></span>
                            <span class="surface"></span>
                            <span class="surface"></span>
                            <span class="surface"></span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="small fw-bold text-muted mt-2">الفك السفلي (Lower Arch - Mandible)</div>
    </div>
</div>

<!-- SECTION: Today's Appointments & Lab Tracking -->
<div class="row g-4 mb-4">
    <!-- Appointments Table -->
    <div class="col-xl-8" id="appointments">
        <div class="clinic-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">جدول مواعيد اليوم ({{ date('Y-m-d') }})</h5>
                    <small class="text-muted">مباشر من جدول Appointments في قاعدة البيانات</small>
                </div>
                <button class="btn btn-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#newAppointmentModal">
                    <i class="bi bi-plus-lg me-1"></i> موعد جديد
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>الوقت</th>
                            <th>المريض</th>
                            <th>الطبيب المعالج</th>
                            <th>الإجراء الطبي</th>
                            <th>الحالة</th>
                            <th class="text-center">إجراءات سريعة</th>
                        </tr>
                    </thead>
                    <tbody id="appointmentTableBody">
                        @forelse($todayAppointments as $apt)
                        <tr data-appointment-id="{{ $apt->id }}">
                            <td class="fw-bold text-primary">{{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $apt->patient->name }}</div>
                                <div class="text-muted small">{{ $apt->patient->phone }} • #{{ $apt->patient->file_number }}</div>
                            </td>
                            <td>{{ $apt->doctor->name }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $apt->service_type }}</span></td>
                            <td>
                                @if($apt->status === 'in_consultation')
                                    <span class="badge bg-warning text-dark status-badge">في غرفة الكشف</span>
                                @elseif($apt->status === 'waiting')
                                    <span class="badge bg-info text-white status-badge">في صالة الانتظار</span>
                                @elseif($apt->status === 'completed')
                                    <span class="badge bg-success status-badge">مكتمل</span>
                                @else
                                    <span class="badge bg-secondary status-badge">مؤكد</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($apt->status === 'scheduled')
                                    <button class="btn btn-sm btn-warning rounded-pill btn-change-status" data-status="waiting" title="تسجيل وصول المريض">
                                        <i class="bi bi-person-check"></i>
                                    </button>
                                @elseif($apt->status === 'waiting')
                                    <button class="btn btn-sm btn-primary rounded-pill btn-change-status" data-status="in_consultation" title="دخول غرفة الكشف">
                                        <i class="bi bi-box-arrow-in-right"></i>
                                    </button>
                                @elseif($apt->status === 'in_consultation')
                                    <button class="btn btn-sm btn-success rounded-pill btn-change-status" data-status="completed" title="اكتمال الجلسة">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                @else
                                    <span class="text-success small"><i class="bi bi-check-circle-fill"></i> تمت</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">لا توجد مواعيد مسجلة اليوم</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Active Lab Orders & Installments Quick Widget -->
    <div class="col-xl-4" id="labs">
        <div class="clinic-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0">طلبيات معمل الأسنان 📦</h5>
                    @if(auth()->user()->isAdmin() || auth()->user()->isDoctor())
                        <a href="{{ route('clinic.labs.index') }}" class="badge bg-info-subtle text-info text-decoration-none px-2 py-1 rounded-pill">
                            كل الطلبيات <i class="bi bi-arrow-left"></i>
                        </a>
                    @else
                        <span class="badge bg-info-subtle text-info">Lab Orders</span>
                    @endif
                </div>
                
                <div class="d-flex flex-column gap-3">
                    @forelse($labOrders as $order)
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold text-dark small">{{ $order->item_type }} @if($order->tooth_numbers) (سن #{{ $order->tooth_numbers }}) @endif</span>
                            @if($order->status === 'ready')
                                <span class="badge bg-success small">جاهز للاستلام</span>
                            @elseif($order->status === 'in_progress')
                                <span class="badge bg-warning text-dark small">قيد التصنيع</span>
                            @else
                                <span class="badge bg-secondary small">تم الإرسال</span>
                            @endif
                        </div>
                        <div class="small text-muted mt-1">المريض: {{ $order->patient->name }} • {{ $order->lab_name }}</div>
                        <div class="small text-secondary mt-1">
                            <i class="bi bi-palette text-primary me-1"></i> درجة اللون: {{ $order->shade ?? 'N/A' }} • التكلفة: {{ number_format($order->cost, 2) }} ر.س
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3 small">لا توجد طلبيات معمل حالياً</div>
                    @endforelse
                </div>
            </div>

            <!-- Installment Notice -->
            <div class="mt-4 pt-3 border-top" id="billing">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small fw-bold text-dark">نظام الفواتير والتحصيل</div>
                        <div class="text-muted small">سندات القبض المسجلة: {{ $todayAppointments->count() }}</div>
                    </div>
                    @if(auth()->user()->isAdmin() || auth()->user()->isAccountant())
                        <a href="{{ route('clinic.billing.index') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                            عرض المالية <i class="bi bi-arrow-left ms-1"></i>
                        </a>
                    @else
                        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" disabled title="خاص بالمحاسبة والإدارة فقط">
                            عرض المالية
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Tooth Action & Clinical Condition (Saves to MySQL) -->
<div class="modal fade" id="toothActionModal" tabindex="-1" aria-labelledby="toothActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark mb-0">
                    <i class="bi bi-gear-fill text-primary me-2"></i>
                    تحديث السجل الطبي للسن: <span id="selectedToothDisplay" class="text-primary">--</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="activeToothNumber" value="">
                <input type="hidden" id="activePatientId" value="{{ $activePatient->id ?? 1 }}">

                <p class="text-muted small mb-3">اختر الحالة أو الإجراء ليتم حفظه تلقائياً في قاعدة البيانات MySQL:</p>
                
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-success w-100 py-2 btn-apply-tooth-condition" data-condition="healthy">
                            <i class="bi bi-check-circle me-1"></i> سليم (Healthy)
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-danger w-100 py-2 btn-apply-tooth-condition" data-condition="caries">
                            <i class="bi bi-exclamation-octagon me-1"></i> تسوس (Caries)
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-primary w-100 py-2 btn-apply-tooth-condition" data-condition="filled">
                            <i class="bi bi-layers-fill me-1"></i> حشوة (Filled)
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-warning w-100 py-2 btn-apply-tooth-condition" data-condition="crown">
                            <i class="bi bi-award-fill me-1"></i> تاج (Crown)
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-dark w-100 py-2 btn-apply-tooth-condition" data-condition="missing">
                            <i class="bi bi-dash-circle me-1"></i> مخلوع (Missing)
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-purple w-100 py-2 btn-apply-tooth-condition" data-condition="implant" style="border-color:#8b5cf6; color:#8b5cf6;">
                            <i class="bi bi-pin-angle-fill me-1"></i> زراعة (Implant)
                        </button>
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn btn-outline-pink w-100 py-2 btn-apply-tooth-condition" data-condition="rct" style="border-color:#ec4899; color:#ec4899;">
                            <i class="bi bi-activity me-1"></i> علاج عصب وجذور (Root Canal)
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">ملاحظات الطبيب السريرية</label>
                    <textarea class="form-control" id="toothClinicalNotes" rows="2" placeholder="أدخل أي ملاحظة حول السن أو العلاج..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: New Appointment (Saves to MySQL) -->
<div class="modal fade" id="newAppointmentModal" tabindex="-1" aria-labelledby="newAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-calendar-plus text-primary me-2"></i> حجز موعد جديد في قاعدة البيانات
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="newClinicAppointmentForm">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">اسم المريض</label>
                        <input type="text" class="form-control" placeholder="اسم المريض بالكامل" required id="modalPatientName">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">رقم الجوال</label>
                            <input type="tel" class="form-control" placeholder="05XXXXXXXX" required id="modalPatientPhone">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">الطبيب المعالج</label>
                            <select class="form-select" id="modalDoctorId" required>
                                @foreach($doctors as $doc)
                                    <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->speciality }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">الإجراء الطبي</label>
                            <select class="form-select" id="modalProcedure" required>
                                <option>كشف واستشارة</option>
                                <option>زراعة أسنان فورية</option>
                                <option>علاج عصب وجذور</option>
                                <option>حشوة تجميلية كمبوزيت</option>
                                <option>تركيب تاج زيركون</option>
                                <option>تقويم أسنان دوري</option>
                                <option>تنظيف وتبييض ليزر</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">وقت الموعد</label>
                            <input type="time" class="form-control" value="12:00" id="modalTime" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill" id="btnSaveAppointment">
                        <i class="bi bi-save me-1"></i> حفظ الموعد في MySQL
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function () {
        // Setup CSRF Token for jQuery AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Function to show toast alert
        function showNotification(msg) {
            $('#ajaxAlertText').text(msg);
            $('#ajaxAlert').removeClass('d-none').hide().fadeIn(300).delay(2500).fadeOut(400);
        }

        // Open Tooth Modal and store tooth id
        $(document).on('click', '.tooth-item', function () {
            const toothId = $(this).data('tooth-id');
            const toothName = $(this).data('tooth-name');
            $('.tooth-item').removeClass('selected');
            $(this).addClass('selected');

            $('#activeToothNumber').val(toothId);
            $('#selectedToothDisplay').text(`${toothId} - ${toothName}`);
            $('#toothActionModal').modal('show');
        });

        // Apply Tooth Condition and save to MySQL via AJAX
        $(document).on('click', '.btn-apply-tooth-condition', function () {
            const condition = $(this).data('condition');
            const toothNumber = $('#activeToothNumber').val();
            const patientId = $('#activePatientId').val();
            const notes = $('#toothClinicalNotes').val();

            const activeTooth = $(`.tooth-item[data-tooth-id="${toothNumber}"]`);
            if (activeTooth.length) {
                activeTooth.attr('data-status', condition);
            }

            $.ajax({
                url: "{{ route('clinic.tooth.update') }}",
                method: "POST",
                data: {
                    patient_id: patientId,
                    tooth_number: toothNumber,
                    condition: condition,
                    notes: notes
                },
                success: function (res) {
                    $('#toothActionModal').modal('hide');
                    showNotification(`تم حفظ حالة السن #${toothNumber} (${condition}) بنجاح في MySQL`);
                },
                error: function (err) {
                    alert('حدث خطأ أثناء حفظ حالة السن');
                }
            });
        });

        // Change Appointment Status via AJAX
        $(document).on('click', '.btn-change-status', function () {
            const btn = $(this);
            const row = btn.closest('tr');
            const appointmentId = row.data('appointment-id');
            const newStatus = btn.data('status');

            $.ajax({
                url: `/clinic/appointments/${appointmentId}/status`,
                method: "PATCH",
                data: { status: newStatus },
                success: function (res) {
                    if (newStatus === 'waiting') {
                        row.find('.status-badge').removeClass().addClass('badge bg-info text-white status-badge').text('في صالة الانتظار');
                        btn.replaceWith('<button class="btn btn-sm btn-primary rounded-pill btn-change-status" data-status="in_consultation" title="دخول غرفة الكشف"><i class="bi bi-box-arrow-in-right"></i></button>');
                        let wc = parseInt($('#waitingPatientsCount').text());
                        $('#waitingPatientsCount').text(wc + 1);
                    } else if (newStatus === 'in_consultation') {
                        row.find('.status-badge').removeClass().addClass('badge bg-warning text-dark status-badge').text('في غرفة الكشف');
                        btn.replaceWith('<button class="btn btn-sm btn-success rounded-pill btn-change-status" data-status="completed" title="اكتمال الجلسة"><i class="bi bi-check-lg"></i></button>');
                        let wc = parseInt($('#waitingPatientsCount').text());
                        if (wc > 0) $('#waitingPatientsCount').text(wc - 1);
                    } else if (newStatus === 'completed') {
                        row.find('.status-badge').removeClass().addClass('badge bg-success status-badge').text('مكتمل');
                        btn.replaceWith('<span class="text-success small"><i class="bi bi-check-circle-fill"></i> تمت</span>');
                    }
                    showNotification('تم تحديث حالة الموعد في قاعدة البيانات MySQL');
                }
            });
        });

        // Add New Appointment Form via AJAX
        $('#newClinicAppointmentForm').on('submit', function (e) {
            e.preventDefault();
            const btn = $('#btnSaveAppointment');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> جاري الحفظ...');

            const formData = {
                patient_name: $('#modalPatientName').val(),
                patient_phone: $('#modalPatientPhone').val(),
                doctor_id: $('#modalDoctorId').val(),
                service_type: $('#modalProcedure').val(),
                appointment_time: $('#modalTime').val()
            };

            $.ajax({
                url: "{{ route('clinic.appointments.store') }}",
                method: "POST",
                data: formData,
                success: function (res) {
                    btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> حفظ الموعد في MySQL');
                    $('#newAppointmentModal').modal('hide');
                    $('#newClinicAppointmentForm')[0].reset();

                    const apt = res.appointment;
                    const newRow = `
                        <tr data-appointment-id="${apt.id}">
                            <td class="fw-bold text-primary">${apt.appointment_time}</td>
                            <td>
                                <div class="fw-bold text-dark">${apt.patient.name}</div>
                                <div class="text-muted small">${apt.patient.phone} • #${apt.patient.file_number}</div>
                            </td>
                            <td>${apt.doctor.name}</td>
                            <td><span class="badge bg-light text-dark border">${apt.service_type}</span></td>
                            <td><span class="badge bg-secondary status-badge">مؤكد</span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning rounded-pill btn-change-status" data-status="waiting" title="تسجيل وصول المريض">
                                    <i class="bi bi-person-check"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                    $('#appointmentTableBody').prepend(newRow);

                    let count = parseInt($('#totalAppointmentsCount').text());
                    $('#totalAppointmentsCount').text(count + 1);

                    showNotification('تم تسجيل الموعد الجديد بنجاح في قاعدة بيانات MySQL');
                },
                error: function (err) {
                    btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> حفظ الموعد في MySQL');
                    alert('تعذر حفظ الموعد. تأكد من إدخال كافة البيانات بشكل صحيح.');
                }
            });
        });
    });
</script>
@endpush
