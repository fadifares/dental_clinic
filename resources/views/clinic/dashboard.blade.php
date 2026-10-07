@extends('layouts.clinic')

@section('title', 'لوحة القيادة المخصصة | دنتال برو ERP')

@section('content')

<!-- Header & Role Identity -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
    <div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <h3 class="fw-bold text-dark mb-0 fs-4 fs-md-3">لوحة القيادة والتشغيل اليومي 🩺</h3>
            <span class="badge bg-primary-subtle text-primary border px-3 py-1 rounded-pill">
                {{ auth()->user()->getRoleLabelsString() }}
            </span>
        </div>
        <p class="text-muted small mb-0 mt-1">مرحباً بك، <strong class="text-dark">{{ auth()->user()->name }}</strong> • البيانات معروضة خصيصاً وفق مهامك ومسؤولياتك</p>
    </div>
    <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 flex-fill flex-md-grow-0" onclick="window.location.reload();">
            <i class="bi bi-arrow-clockwise me-1"></i> تحديث
        </button>
        <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm flex-fill flex-md-grow-0" data-bs-toggle="modal" data-bs-target="#newAppointmentModal">
            <i class="bi bi-calendar-plus me-1"></i> حجز موعد سريع
        </button>
    </div>
</div>

<!-- Admin Role-Preview Switcher (Executive Privilege) -->
@if(auth()->user()->isAdmin())
<div class="clinic-card p-3 mb-4 bg-white border border-info-subtle rounded-4 shadow-sm">
    <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-info text-white p-2 rounded-circle flex-shrink-0">
                <i class="bi bi-eye-fill"></i>
            </span>
            <div>
                <strong class="text-dark small d-block">مستعرض الأدوار (Executive Role Switcher):</strong>
                <span class="text-muted" style="font-size: 0.75rem;">بصفتك مدير النظام، يمكنك معاينة وتجربة لوحة القيادة المخصصة لكل دور وظيفي:</span>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-1 p-1 bg-light border rounded-4 shadow-sm w-100 w-lg-auto" role="group">
            <a href="{{ route('clinic.dashboard', ['role_preview' => 'admin']) }}" 
               class="btn btn-sm rounded-pill px-3 py-1 fw-bold flex-fill text-center {{ $effectiveRole === 'admin' ? 'btn-danger shadow-sm' : 'btn-light text-muted' }}">
                <i class="bi bi-shield-shaded me-1"></i> الإدارة (Admin)
            </a>
            <a href="{{ route('clinic.dashboard', ['role_preview' => 'doctor']) }}" 
               class="btn btn-sm rounded-pill px-3 py-1 fw-bold flex-fill text-center {{ $effectiveRole === 'doctor' ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                <i class="bi bi-heart-pulse me-1"></i> طبيب الأسنان (Doctor)
            </a>
            <a href="{{ route('clinic.dashboard', ['role_preview' => 'receptionist']) }}" 
               class="btn btn-sm rounded-pill px-3 py-1 fw-bold flex-fill text-center {{ $effectiveRole === 'receptionist' ? 'btn-warning text-dark shadow-sm' : 'btn-light text-muted' }}">
                <i class="bi bi-headset me-1"></i> الاستقبال (Receptionist)
            </a>
            <a href="{{ route('clinic.dashboard', ['role_preview' => 'accountant']) }}" 
               class="btn btn-sm rounded-pill px-3 py-1 fw-bold flex-fill text-center {{ $effectiveRole === 'accountant' ? 'btn-success shadow-sm' : 'btn-light text-muted' }}">
                <i class="bi bi-wallet2 me-1"></i> المحاسب (Accountant)
            </a>
        </div>
    </div>
</div>
@endif

<!-- Multi-Role Switcher for Dual-Role Staff (e.g. Receptionist + Accountant) -->
@if(!auth()->user()->isAdmin() && count($userRoles) > 1)
<div class="clinic-card p-3 mb-4 bg-white border border-warning-subtle rounded-4 shadow-sm">
    <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-warning text-dark p-2 rounded-circle flex-shrink-0">
                <i class="bi bi-layers-fill"></i>
            </span>
            <div>
                <strong class="text-dark small d-block">أنت معين في أكثر من مجموعة صلاحيات (صلاحيات مدمجة):</strong>
                <span class="text-muted" style="font-size: 0.75rem;">يمكنك التبديل الفوري بين واجهات مهامك اليومية:</span>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-1 p-1 bg-light border rounded-4 shadow-sm w-100 w-lg-auto" role="group">
            @foreach(auth()->user()->getRoleInstances() as $roleInstance)
                <a href="{{ route('clinic.dashboard', ['role_view' => $roleInstance->value]) }}" 
                   class="btn btn-sm rounded-pill px-3 py-1 fw-bold flex-fill text-center {{ $effectiveRole === $roleInstance->value ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                    <i class="bi bi-check2-circle me-1"></i> لوحة {{ $roleInstance->label() }}
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Dynamic Role Dashboard View -->
@if($effectiveRole === 'admin')
    @include('clinic.dashboards._admin')
@elseif($effectiveRole === 'doctor')
    @include('clinic.dashboards._doctor')
@elseif($effectiveRole === 'receptionist')
    @include('clinic.dashboards._receptionist')
@elseif($effectiveRole === 'accountant')
    @include('clinic.dashboards._accountant')
@endif

<!-- MODAL: Tooth Action & Clinical Condition (Saves to MySQL) -->
<div class="modal fade" id="toothActionModal" tabindex="-1" aria-labelledby="toothActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark mb-0">
                    <i class="bi bi-gear-fill text-primary me-2"></i>
                    تحديث السجل الطبي للسن: <span id="selectedToothDisplay" class="text-primary font-monospace">--</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="activeToothNumber" value="">
                <input type="hidden" id="activePatientId" value="{{ $activePatient->id ?? 1 }}">

                <p class="text-muted small mb-3">اختر الحالة أو الإجراء السريري ليتم حفظه تلقائياً في قاعدة البيانات MySQL:</p>
                
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
                        <button type="button" class="btn btn-outline-warning text-dark w-100 py-2 btn-apply-tooth-condition" data-condition="crown">
                            <i class="bi bi-award-fill me-1"></i> تاج / تركيب (Crown)
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-dark w-100 py-2 btn-apply-tooth-condition" data-condition="missing">
                            <i class="bi bi-dash-circle me-1"></i> مخلوع (Missing)
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-info w-100 py-2 btn-apply-tooth-condition" data-condition="implant">
                            <i class="bi bi-pin-angle-fill me-1"></i> زراعة (Implant)
                        </button>
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn btn-outline-danger w-100 py-2 btn-apply-tooth-condition" data-condition="rct">
                            <i class="bi bi-activity me-1"></i> علاج عصب وجذور (Root Canal)
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">ملاحظات الطبيب السريرية للسن</label>
                    <textarea class="form-control" id="toothClinicalNotes" rows="2" placeholder="أدخل أي ملاحظة حول التشخيص أو خطة العلاج..."></textarea>
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
                        <label class="form-label small fw-semibold">اسم المريض <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="اسم المريض بالكامل" required id="modalPatientName">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">رقم الجوال <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" placeholder="05XXXXXXXX" required id="modalPatientPhone">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">الطبيب المعالج <span class="text-danger">*</span></label>
                            <select class="form-select" id="modalDoctorId" required>
                                @foreach($allDoctors as $doc)
                                    <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->speciality }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">نوع الإجراء / الخدمة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="مثال: تنظيف جير، كشف أولي" required id="modalProcedure">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">وقت الموعد <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" required id="modalTime" value="12:00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4" id="btnSaveAppointment">
                        <i class="bi bi-save me-1"></i> حفظ وتأكيد الموعد
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Handle Odontogram Tooth Clicks
    $(document).on('click', '.tooth-box, .tooth-item', function () {
        const tooth = $(this).data('tooth') || $(this).data('tooth-id');
        const toothName = $(this).data('tooth-name') || `السن رقم ${tooth}`;
        $('.tooth-item').removeClass('selected');
        $(this).addClass('selected');
        $('#activeToothNumber').val(tooth);
        $('#selectedToothDisplay').text(`${tooth} - ${toothName}`);
        
        const modalEl = document.getElementById('toothActionModal');
        if (modalEl) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    });

    // Save Tooth Condition via AJAX
    $(document).on('click', '.btn-apply-tooth-condition', function () {
        const condition = $(this).data('condition');
        const toothNumber = $('#activeToothNumber').val();
        const patientId = $('#activePatientId').val();
        const notes = $('#toothClinicalNotes').val();

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
                const modalEl = document.getElementById('toothActionModal');
                if (modalEl) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                }
                
                // Update anatomical tooth item status
                const targetTooth = $(`.tooth-item[data-tooth-id="${toothNumber}"]`);
                if (targetTooth.length) {
                    targetTooth.attr('data-status', condition);
                }

                // Update button style
                const targetBtn = $(`.tooth-box[data-tooth="${toothNumber}"]`);
                if (targetBtn.length) {
                    targetBtn.removeClass('btn-outline-secondary btn-danger btn-primary btn-warning btn-info btn-dark bg-white text-white text-dark');
                    if (condition === 'caries') targetBtn.addClass('btn-danger text-white');
                    else if (condition === 'filled') targetBtn.addClass('btn-primary text-white');
                    else if (condition === 'crown') targetBtn.addClass('btn-warning text-dark');
                    else if (condition === 'implant') targetBtn.addClass('btn-info text-white');
                    else if (condition === 'rct') targetBtn.addClass('btn-danger text-white');
                    else if (condition === 'missing') targetBtn.addClass('btn-dark text-white');
                    else targetBtn.addClass('btn-outline-secondary bg-white');
                }
            },
            error: function () {
                alert('حدث خطأ أثناء حفظ حالة السن. يرجى التحقق من الاتصال.');
            }
        });
    });

    // Change Appointment Status via AJAX
    $(document).on('click', '.btn-change-status', function () {
        const btn = $(this);
        const aptId = btn.data('id') || btn.closest('tr').data('appointment-id');
        const newStatus = btn.data('status');

        if (!aptId) return;

        btn.prop('disabled', true);

        $.ajax({
            url: `/clinic/appointments/${aptId}/status`,
            method: "PATCH",
            data: { status: newStatus },
            success: function () {
                window.location.reload();
            },
            error: function () {
                btn.prop('disabled', false);
                alert('تعذر تحديث حالة الموعد. تأكد من صلاحيات حسابك.');
            }
        });
    });

    // Create New Appointment via AJAX
    $('#newClinicAppointmentForm').on('submit', function (e) {
        e.preventDefault();
        const btn = $('#btnSaveAppointment');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> جاري الحفظ...');

        $.ajax({
            url: "{{ route('clinic.appointments.store') }}",
            method: "POST",
            data: {
                patient_name: $('#modalPatientName').val(),
                patient_phone: $('#modalPatientPhone').val(),
                doctor_id: $('#modalDoctorId').val(),
                service_type: $('#modalProcedure').val(),
                appointment_time: $('#modalTime').val()
            },
            success: function () {
                $('#newAppointmentModal').modal('hide');
                window.location.reload();
            },
            error: function () {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> حفظ وتأكيد الموعد');
                alert('تعذر تسجيل الموعد. تأكد من صحة المدخلات.');
            }
        });
    });
});
</script>
@endpush

@endsection
