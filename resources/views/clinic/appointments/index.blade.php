@extends('layouts.clinic')

@section('title', 'جدول المواعيد والاستقبال | Dental Pro ERP')

@section('content')

<!-- Header & Quick Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">جدول الاستقبال والمواعيد (Reception Desk) 🗓️</h3>
        <p class="text-muted small mb-0">متابعة دقيقة لمواعيد الكشف والعمليات وتوزيع المرضى على غرف الأطباء.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#newAppointmentModal">
            <i class="bi bi-calendar-plus me-1"></i> تسجيل موعد جديد
        </button>
    </div>
</div>

<!-- Stats Bar for Selected Day -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">مواعيد اليوم الكلية</span>
                <h3 class="fw-bold text-dark mb-0" id="statTotal">{{ $stats['total'] }}</h3>
            </div>
            <div class="clinic-stat-icon bg-primary-subtle text-primary">
                <i class="bi bi-calendar-check fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">في صالة الانتظار</span>
                <h3 class="fw-bold text-info mb-0" id="statWaiting">{{ $stats['waiting'] }}</h3>
            </div>
            <div class="clinic-stat-icon bg-info-subtle text-info">
                <i class="bi bi-hourglass-split fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">في غرفة الكشف</span>
                <h3 class="fw-bold text-warning mb-0" id="statInConsultation">{{ $stats['in_consultation'] }}</h3>
            </div>
            <div class="clinic-stat-icon bg-warning-subtle text-warning">
                <i class="bi bi-heart-pulse fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">الجلسات المكتملة</span>
                <h3 class="fw-bold text-success mb-0" id="statCompleted">{{ $stats['completed'] }}</h3>
            </div>
            <div class="clinic-stat-icon bg-success-subtle text-success">
                <i class="bi bi-check2-circle fs-4"></i>
            </div>
        </div>
    </div>
</div>

<!-- Date & Doctor Filter Bar -->
<div class="clinic-card p-3 mb-4">
    <form method="GET" action="{{ route('clinic.appointments.index') }}" class="row g-3 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="bi bi-calendar-event text-primary"></i></span>
                <input type="date" name="date" class="form-control bg-light border-0 fw-semibold" value="{{ $selectedDate }}" onchange="this.form.submit()">
            </div>
        </div>
        <div class="col-md-4">
            <select name="doctor_id" class="form-select bg-light border-0" onchange="this.form.submit()">
                <option value="">جميع أطباء العيادة</option>
                @foreach($doctors as $doc)
                    <option value="{{ $doc->id }}" {{ request('doctor_id') == $doc->id ? 'selected' : '' }}>{{ $doc->name }} ({{ $doc->speciality }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <a href="{{ route('clinic.appointments.index', ['date' => now()->toDateString()]) }}" class="btn btn-outline-secondary btn-sm rounded-pill flex-fill">
                اليوم
            </a>
            <a href="{{ route('clinic.appointments.index', ['date' => now()->addDay()->toDateString()]) }}" class="btn btn-outline-secondary btn-sm rounded-pill flex-fill">
                غداً
            </a>
        </div>
    </form>
</div>

<!-- Appointments Table Card -->
<div class="clinic-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>الوقت</th>
                    <th>المريض</th>
                    <th>الطبيب المعالج</th>
                    <th>الإجراء الطبي</th>
                    <th>الحالة</th>
                    <th>الملاحظات</th>
                    <th class="text-center">إجراءات الاستقبال</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $apt)
                <tr data-appointment-id="{{ $apt->id }}">
                    <td class="fw-bold text-primary font-monospace fs-6">
                        {{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}
                    </td>
                    <td>
                        <a href="{{ route('clinic.patients.show', $apt->patient) }}" class="fw-bold text-dark text-decoration-none hover-primary">
                            {{ $apt->patient->name }}
                        </a>
                        <div class="text-muted small font-monospace" style="font-size:0.75rem;">
                            {{ $apt->patient->phone }} • #{{ $apt->patient->file_number }}
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $apt->doctor->name }}</span>
                    </td>
                    <td>
                        <span class="badge bg-light text-primary border">{{ $apt->service_type }}</span>
                    </td>
                    <td>
                        @if($apt->status === 'completed')
                            <span class="badge bg-success status-badge">مكتمل</span>
                        @elseif($apt->status === 'in_consultation')
                            <span class="badge bg-warning text-dark status-badge">في غرفة الكشف</span>
                        @elseif($apt->status === 'waiting')
                            <span class="badge bg-info text-white status-badge">في الانتظار</span>
                        @else
                            <span class="badge bg-secondary status-badge">مؤكد</span>
                        @endif
                    </td>
                    <td class="small text-muted">{{ $apt->notes ?? '-' }}</td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-1 align-items-center">
                            @if($apt->status === 'scheduled')
                                <button class="btn btn-sm btn-warning rounded-pill btn-change-status" data-status="waiting" title="تسجيل وصول">
                                    <i class="bi bi-person-check"></i> وصول
                                </button>
                            @elseif($apt->status === 'waiting')
                                <button class="btn btn-sm btn-primary rounded-pill btn-change-status" data-status="in_consultation" title="دخول الكشف">
                                    <i class="bi bi-box-arrow-in-right"></i> دخول
                                </button>
                            @elseif($apt->status === 'in_consultation')
                                <button class="btn btn-sm btn-success rounded-pill btn-change-status" data-status="completed" title="إنهاء الجلسة">
                                    <i class="bi bi-check-lg"></i> إكمال
                                </button>
                            @else
                                <span class="badge bg-success-subtle text-success small"><i class="bi bi-check-circle-fill"></i> تمت</span>
                            @endif

                            <a href="{{ route('clinic.patients.show', $apt->patient) }}" class="btn btn-sm btn-outline-secondary rounded-circle" title="فتح الملف الطبي">
                                <i class="bi bi-folder2-open"></i>
                            </a>

                            <form action="{{ route('clinic.appointments.destroy', $apt) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الموعد؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="إلغاء الموعد">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x display-4 text-secondary d-block mb-2"></i>
                        لا توجد مواعيد مسجلة في هذا التاريخ المحدد
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL: New Appointment -->
<div class="modal fade" id="newAppointmentModal" tabindex="-1" aria-labelledby="newAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-calendar-plus text-primary me-2"></i> تسجيل موعد جديد
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('clinic.appointments.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">اختر المريض المسجل</label>
                        <select name="patient_id" class="form-select" required>
                            <option value="" disabled selected>ابحث أو اختر المريض...</option>
                            @foreach($patients as $pt)
                                <option value="{{ $pt->id }}">{{ $pt->name }} ({{ $pt->phone }} - #{{ $pt->file_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">الطبيب المعالج</label>
                        <select name="doctor_id" class="form-select" required>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->speciality }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">تاريخ الموعد</label>
                            <input type="date" name="appointment_date" class="form-control" value="{{ $selectedDate }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">وقت الموعد</label>
                            <input type="time" name="appointment_time" class="form-control" value="10:00" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">نوع الإجراء الطبي</label>
                        <input type="text" name="service_type" class="form-control" placeholder="كشف، زراعة، حشوة، علاج عصب..." required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="ملاحظات حول الموعد..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">تثبيت الموعد</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Fast AJAX Status Switcher
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
                    window.location.reload();
                },
                error: function () {
                    alert('تعذر تحديث حالة الموعد.');
                }
            });
        });
    });
</script>
@endpush
