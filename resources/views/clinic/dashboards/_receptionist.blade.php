<!-- ==================== لوحة قيادة موظف الاستقبال (Receptionist Frontdesk Dashboard) ==================== -->

<!-- Reception Quick Actions Bar -->
<div class="clinic-card p-3 mb-4 bg-white shadow-sm border-0 rounded-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-headset me-1"></i> مكتب الاستقبال وصالة الانتظار (Reception & Queue)
            </span>
            <span class="text-muted small">تنظيم تدفق المراجعين، تأكيد الحضور، وإدارة جدول المواعيد الحي</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#newAppointmentModal">
                <i class="bi bi-calendar-plus me-1"></i> حجز موعد جديد
            </button>
            <a href="{{ route('clinic.patients.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-person-plus-fill me-1"></i> فتح ملف مريض جديد (EMR)
            </a>
            <a href="{{ route('clinic.appointments.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-calendar2-range me-1"></i> جدول المواعيد الكامل
            </a>
        </div>
    </div>
</div>

<!-- Frontdesk KPIs Grid -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="clinic-card p-3 h-100 d-flex align-items-center justify-content-between border-warning-subtle">
            <div>
                <span class="text-muted small d-block mb-1">في صالة الانتظار حالياً</span>
                <h3 class="fw-bold text-warning mb-0">{{ $receptionData['waitingQueue']->count() }} <span class="fs-6 fw-normal text-muted">مراجع</span></h3>
                <span class="text-muted" style="font-size: 0.75rem;">جاهزون للدخول للأطباء</span>
            </div>
            <div class="clinic-stat-icon bg-warning-subtle text-warning">
                <i class="bi bi-person-lines-fill fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="clinic-card p-3 h-100 d-flex align-items-center justify-content-between border-primary-subtle">
            <div>
                <span class="text-muted small d-block mb-1">في غرف الكشف حالياً</span>
                <h3 class="fw-bold text-primary mb-0">{{ $receptionData['inConsultationList']->count() }} <span class="fs-6 fw-normal text-muted">على الكرسي</span></h3>
                <span class="text-muted" style="font-size: 0.75rem;">جلسات علاجية جارية</span>
            </div>
            <div class="clinic-stat-icon bg-primary-subtle text-primary">
                <i class="bi bi-heart-pulse-fill fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="clinic-card p-3 h-100 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">مواعيد اليوم المتبقية</span>
                <h3 class="fw-bold text-dark mb-0">{{ $receptionData['scheduledList']->count() }} <span class="fs-6 fw-normal text-muted">مجدول</span></h3>
                <span class="text-muted" style="font-size: 0.75rem;">لم يصلوا بعد للعيادة</span>
            </div>
            <div class="clinic-stat-icon bg-info-subtle text-info">
                <i class="bi bi-clock-history fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="clinic-card p-3 h-100 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">المواعيد المكتملة اليوم</span>
                <h3 class="fw-bold text-success mb-0">{{ $receptionData['completedCount'] }} <span class="fs-6 fw-normal text-muted">/ {{ $receptionData['totalToday'] }}</span></h3>
                <span class="text-muted" style="font-size: 0.75rem;">ملفات مسجلة جديدة: {{ $receptionData['newPatientsToday'] }}</span>
            </div>
            <div class="clinic-stat-icon bg-success-subtle text-success">
                <i class="bi bi-check2-circle fs-4"></i>
            </div>
        </div>
    </div>
</div>

<!-- Live Waiting Room Lounge (Interactive Queue for Frontdesk) -->
<div class="row g-4 mb-4">
    <!-- Active Waiting Lounge -->
    <div class="col-xl-7">
        <div class="clinic-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">صالة الانتظار الحية (Live Waiting Lounge) 🛋️</h5>
                    <span class="text-muted small">المراجعين الحاضرين في بهو العيادة بانتظار استدعاء الطبيب</span>
                </div>
                <span class="badge bg-warning-subtle text-warning-emphasis border px-3 py-2 rounded-pill">
                    {{ $receptionData['waitingQueue']->count() }} بالانتظار
                </span>
            </div>

            <div class="d-flex flex-column gap-3">
                @forelse($receptionData['waitingQueue'] as $waitApt)
                <div class="p-3 bg-light rounded-4 border d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-warning text-dark fw-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
                            <i class="bi bi-person-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-6">{{ $waitApt->patient->name }}</div>
                            <div class="text-muted small">
                                <span class="text-primary fw-semibold"><i class="bi bi-clock me-1"></i>{{ substr($waitApt->appointment_time, 0, 5) }}</span>
                                • الطبيب: <strong class="text-dark">{{ $waitApt->doctor->name }}</strong>
                                • الخدمة: {{ $waitApt->service_type }}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm rounded-pill px-3 btn-change-status shadow-sm" data-id="{{ $waitApt->id }}" data-status="in_consultation">
                            <i class="bi bi-door-open me-1"></i> تحويل لغرفة الكشف
                        </button>
                        <a href="{{ route('clinic.patients.show', $waitApt->patient) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-2" title="فتح الملف">
                            <i class="bi bi-folder"></i>
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-cup-hot fs-1 text-secondary opacity-50 d-block mb-2"></i>
                    صالة الانتظار فارغة حالياً - لا يوجد مرضى قيد الانتظار
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Doctors & Clinics Live Availability -->
    <div class="col-xl-5">
        <div class="clinic-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">حالة عيادات الأطباء الآن 🚪</h5>
                    <span class="text-muted small">جاهزية الأطباء لاستقبال المرضى</span>
                </div>
                <span class="badge bg-info-subtle text-info">Live Status</span>
            </div>

            <div class="d-flex flex-column gap-3">
                @foreach($receptionData['doctorsLoad'] as $docItem)
                <div class="p-3 bg-light rounded-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-dark">{{ $docItem['doctor']->name }}</span>
                        @if($docItem['current_patient'])
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                <i class="bi bi-dot"></i> مشغول (في كشف)
                            </span>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-dot"></i> متاح للكشف
                            </span>
                        @endif
                    </div>
                    <div class="small text-muted mb-2">{{ $docItem['doctor']->speciality }}</div>

                    @if($docItem['current_patient'])
                    <div class="p-2 bg-white rounded-2 border small text-dark mb-2">
                        <i class="bi bi-person-check text-primary me-1"></i> مع المريض: <strong>{{ $docItem['current_patient']->name }}</strong>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between text-muted small pt-2 border-top">
                        <span>في الانتظار: <strong class="text-warning">{{ $docItem['waiting_count'] }}</strong></span>
                        <span>مجدول لاحقاً: <strong class="text-dark">{{ $docItem['scheduled_count'] }}</strong></span>
                        <span>اكتملت: <strong class="text-success">{{ $docItem['completed_count'] }}</strong></span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Master Daily Schedule Table for Receptionist -->
<div class="clinic-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold text-dark mb-0">جدول مواعيد اليوم ومتابعة الحضور 📋</h5>
            <span class="text-muted small">تسجيل وصول المريض، تغيير الحالة، والتواصل السريع</span>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-light text-dark border px-3 py-2">
                إجمالي {{ $todayAppointments->count() }} مواعيد اليوم
            </span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>المريض</th>
                    <th>الهاتف</th>
                    <th>الطبيب المعالج</th>
                    <th>الإجراء / الخدمة</th>
                    <th>التوقيت</th>
                    <th>حالة الحضور</th>
                    <th class="text-end">تحديث الحالة السريعة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($todayAppointments as $apt)
                <tr>
                    <td>
                        <div class="fw-bold text-dark">{{ $apt->patient->name }}</div>
                        <div class="text-muted small">{{ $apt->patient->file_number }}</div>
                    </td>
                    <td>
                        <a href="tel:{{ $apt->patient->phone }}" class="text-decoration-none font-monospace small">
                            <i class="bi bi-telephone text-primary me-1"></i>{{ $apt->patient->phone }}
                        </a>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $apt->doctor->name }}</span>
                    </td>
                    <td>{{ $apt->service_type }}</td>
                    <td class="font-monospace fw-bold text-primary">{{ substr($apt->appointment_time, 0, 5) }}</td>
                    <td>
                        @if($apt->status === 'waiting')
                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> في الانتظار</span>
                        @elseif($apt->status === 'in_consultation')
                            <span class="badge bg-primary"><i class="bi bi-heart-pulse me-1"></i> في غرفة الكشف</span>
                        @elseif($apt->status === 'completed')
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> اكتملت</span>
                        @elseif($apt->status === 'cancelled')
                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> ملغي</span>
                        @else
                            <span class="badge bg-secondary"><i class="bi bi-clock me-1"></i> مجدول</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="d-inline-flex gap-1">
                            @if($apt->status === 'scheduled')
                            <button class="btn btn-sm btn-outline-warning rounded-pill px-2 btn-change-status" data-id="{{ $apt->id }}" data-status="waiting" title="تسجيل وصول المريض إلى الاستقبال">
                                <i class="bi bi-person-check-fill me-1"></i> وصول
                            </button>
                            @elseif($apt->status === 'waiting')
                            <button class="btn btn-sm btn-primary rounded-pill px-2 btn-change-status" data-id="{{ $apt->id }}" data-status="in_consultation" title="تحويل لغرفة الكشف">
                                <i class="bi bi-door-open me-1"></i> دخول
                            </button>
                            @elseif($apt->status === 'in_consultation')
                            <button class="btn btn-sm btn-success rounded-pill px-2 btn-change-status" data-id="{{ $apt->id }}" data-status="completed" title="اكتمال الجلسة">
                                <i class="bi bi-check-lg me-1"></i> إنهاء
                            </button>
                            @endif
                            <a href="{{ route('clinic.patients.show', $apt->patient) }}" class="btn btn-sm btn-light border rounded-pill px-2" title="فتح الملف">
                                <i class="bi bi-folder2-open text-primary"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">لا توجد مواعيد مسجلة لليوم</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
