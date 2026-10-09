@extends('layouts.clinic')

@section('title', 'الملف الطبي: ' . $patient->name . ' | Dental Pro EMR')

@section('content')

<!-- Patient Header Profile Banner -->
<div class="clinic-card p-4 mb-4 border-0 shadow-sm">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-primary text-white fs-3 fw-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 64px; height: 64px;">
                {{ mb_substr($patient->name, 0, 1) }}
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h3 class="fw-bold text-dark mb-0">{{ $patient->name }}</h3>
                    <span class="badge bg-primary-subtle text-primary font-monospace fs-6">#{{ $patient->file_number }}</span>
                    <span class="badge {{ $patient->gender == 'male' ? 'bg-light text-dark' : 'bg-pink-subtle text-danger' }} border">
                        {{ $patient->gender == 'male' ? 'ذكر' : 'أنثى' }}
                    </span>
                    @if($patient->date_of_birth)
                        <span class="badge bg-light text-muted border">{{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} سنة</span>
                    @endif
                </div>
                <div class="d-flex flex-wrap gap-3 text-muted small mt-2">
                    <span><i class="bi bi-telephone-fill text-primary me-1"></i> <a href="tel:{{ $patient->phone }}" class="text-muted text-decoration-none font-monospace">{{ $patient->phone }}</a></span>
                    @if($patient->email)
                        <span><i class="bi bi-envelope-fill text-info me-1"></i> <a href="mailto:{{ $patient->email }}" class="text-muted text-decoration-none">{{ $patient->email }}</a></span>
                    @endif
                    @if($patient->national_id)
                        <span><i class="bi bi-card-text text-muted me-1"></i> الهوية: {{ $patient->national_id }}</span>
                    @endif
                    <span><i class="bi bi-calendar3 text-muted me-1"></i> تاريخ فتح الملف: {{ $patient->created_at->format('Y-m-d') }}</span>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editPatientModal">
                <i class="bi bi-pencil-square me-1"></i> تعديل البيانات
            </button>
            <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#newPatientAppointmentModal">
                <i class="bi bi-calendar-plus me-1"></i> حجز جلسة / موعد
            </button>
        </div>
    </div>

    <!-- Critical Medical Alerts Banner -->
    @if($patient->allergies || $patient->chronic_diseases)
    <div class="alert alert-danger d-flex align-items-center gap-2 mt-3 mb-0 rounded-3 py-2 border-danger-subtle">
        <i class="bi bi-exclamation-triangle-fill fs-5 text-danger flex-shrink-0"></i>
        <div class="small fw-semibold">
            <strong class="text-danger">تنبيه سريري هام:</strong>
            @if($patient->allergies)
                <span>حساسية: {{ $patient->allergies }}</span>
            @endif
            @if($patient->chronic_diseases)
                <span class="ms-2">| أمراض مزمنة: {{ $patient->chronic_diseases }}</span>
            @endif
        </div>
    </div>
    @endif
</div>

<!-- EMR Tabs Navigation -->
<ul class="nav nav-pills clinic-card p-2 mb-4 d-flex flex-nowrap overflow-auto gap-2 border-0" id="emrTabs" role="tablist" style="-webkit-overflow-scrolling: touch;">
    <li class="nav-item flex-shrink-0 flex-md-fill text-center" role="presentation">
        <button class="nav-link active w-100 rounded-pill fw-semibold py-2 text-nowrap" id="odontogram-tab" data-bs-toggle="pill" data-bs-target="#tab-odontogram" type="button" role="tab">
            <i class="bi bi-grid-3x3-gap-fill me-1"></i> مخطط الأسنان التفاعلي (Odontogram)
        </button>
    </li>
    <li class="nav-item flex-shrink-0 flex-md-fill text-center" role="presentation">
        <button class="nav-link w-100 rounded-pill fw-semibold py-2 text-nowrap" id="appointments-tab" data-bs-toggle="pill" data-bs-target="#tab-appointments" type="button" role="tab">
            <i class="bi bi-calendar2-week-fill me-1"></i> سجل المواعيد والجلسات ({{ $patient->appointments->count() }})
        </button>
    </li>
    <li class="nav-item flex-shrink-0 flex-md-fill text-center" role="presentation">
        <button class="nav-link w-100 rounded-pill fw-semibold py-2 text-nowrap" id="billing-tab" data-bs-toggle="pill" data-bs-target="#tab-billing" type="button" role="tab">
            <i class="bi bi-receipt-cutoff me-1"></i> الفواتير والحسابات
        </button>
    </li>
    <li class="nav-item flex-shrink-0 flex-md-fill text-center" role="presentation">
        <button class="nav-link w-100 rounded-pill fw-semibold py-2 text-nowrap" id="labs-tab" data-bs-toggle="pill" data-bs-target="#tab-labs" type="button" role="tab">
            <i class="bi bi-box-seam-fill me-1"></i> طلبيات المعامل ({{ $patient->labOrders->count() }})
        </button>
    </li>
</ul>

<!-- Tabs Content -->
<div class="tab-content" id="emrTabsContent">

    <!-- TAB 1: Odontogram (Dental Chart) -->
    <div class="tab-pane fade show active" id="tab-odontogram" role="tabpanel">
        <div class="clinic-card p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3 pb-3 border-bottom">
                <div>
                    <h5 class="fw-bold text-dark mb-0">المخطط الطبي الرقمي للأسنان (FDI Chart)</h5>
                    <small class="text-muted">انقر على أي سن لتسجيل التشخيص، نوع الحشوة، أو الإجراء الطبي المنجز.</small>
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

            <!-- UPPER JAW -->
            <div class="text-center mb-4">
                <div class="small fw-bold text-muted mb-2">الفك العلوي (Upper Arch - Maxilla)</div>
                <div class="dental-arch justify-content-center">
                    @foreach([18, 17, 16, 15, 14, 13, 12, 11] as $tNum)
                        @php $status = $patientToothConditions[$tNum] ?? 'healthy'; @endphp
                        <div class="tooth-item" data-tooth-id="{{ $tNum }}" data-tooth-name="السن رقم {{ $tNum }}" data-status="{{ $status }}">
                            <div class="tooth-visual">
                                <div class="surface-grid">
                                    <span class="surface"></span><span class="surface"></span>
                                    <span class="surface"></span><span class="surface"></span>
                                </div>
                            </div>
                            <span class="tooth-number">{{ $tNum }}</span>
                        </div>
                    @endforeach

                    <div class="border-start border-2 border-secondary mx-2 align-self-stretch" style="opacity: 0.3;"></div>

                    @foreach([21, 22, 23, 24, 25, 26, 27, 28] as $tNum)
                        @php $status = $patientToothConditions[$tNum] ?? 'healthy'; @endphp
                        <div class="tooth-item" data-tooth-id="{{ $tNum }}" data-tooth-name="السن رقم {{ $tNum }}" data-status="{{ $status }}">
                            <div class="tooth-visual">
                                <div class="surface-grid">
                                    <span class="surface"></span><span class="surface"></span>
                                    <span class="surface"></span><span class="surface"></span>
                                </div>
                            </div>
                            <span class="tooth-number">{{ $tNum }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- LOWER JAW -->
            <div class="text-center mb-4">
                <div class="dental-arch justify-content-center">
                    @foreach([48, 47, 46, 45, 44, 43, 42, 41] as $tNum)
                        @php $status = $patientToothConditions[$tNum] ?? 'healthy'; @endphp
                        <div class="tooth-item" data-tooth-id="{{ $tNum }}" data-tooth-name="السن رقم {{ $tNum }}" data-status="{{ $status }}">
                            <span class="tooth-number">{{ $tNum }}</span>
                            <div class="tooth-visual">
                                <div class="surface-grid">
                                    <span class="surface"></span><span class="surface"></span>
                                    <span class="surface"></span><span class="surface"></span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="border-start border-2 border-secondary mx-2 align-self-stretch" style="opacity: 0.3;"></div>

                    @foreach([31, 32, 33, 34, 35, 36, 37, 38] as $tNum)
                        @php $status = $patientToothConditions[$tNum] ?? 'healthy'; @endphp
                        <div class="tooth-item" data-tooth-id="{{ $tNum }}" data-tooth-name="السن رقم {{ $tNum }}" data-status="{{ $status }}">
                            <span class="tooth-number">{{ $tNum }}</span>
                            <div class="tooth-visual">
                                <div class="surface-grid">
                                    <span class="surface"></span><span class="surface"></span>
                                    <span class="surface"></span><span class="surface"></span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="small fw-bold text-muted mt-2">الفك السفلي (Lower Arch - Mandible)</div>
            </div>

            <!-- Tooth History Log Table -->
            <div class="mt-4 pt-3 border-top">
                <h6 class="fw-bold text-dark mb-3">سجل حالات الأسنان المسجلة للمريض في قاعدة البيانات:</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>رقم السن</th>
                                <th>التشخيص / الحالة</th>
                                <th>الأسطح</th>
                                <th>الملاحظات السريرية</th>
                                <th>تاريخ التحديث</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patient->dentalCharts as $chart)
                            <tr>
                                <td class="fw-bold text-primary font-monospace fs-6">سن #{{ $chart->tooth_number }}</td>
                                <td>
                                    @if($chart->condition == 'caries') <span class="badge bg-danger">تسوس</span>
                                    @elseif($chart->condition == 'filled') <span class="badge bg-primary">حشوة</span>
                                    @elseif($chart->condition == 'crown') <span class="badge bg-warning text-dark">تاج</span>
                                    @elseif($chart->condition == 'missing') <span class="badge bg-secondary">مخلوع</span>
                                    @elseif($chart->condition == 'implant') <span class="badge bg-purple text-white" style="background:#8b5cf6;">زراعة</span>
                                    @elseif($chart->condition == 'rct') <span class="badge text-white" style="background:#ec4899;">علاج عصب</span>
                                    @else <span class="badge bg-success">سليم</span> @endif
                                </td>
                                <td>{{ $chart->surfaces ?? '-' }}</td>
                                <td>{{ $chart->notes ?? 'لا توجد ملاحظات' }}</td>
                                <td class="text-muted">{{ $chart->updated_at->format('Y-m-d') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">لم يتم تسجيل أي تعديلات على الأسنان بعد. انقر على أي سن في المخطط أعلاه لحفظ حالته.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: Appointments History -->
    <div class="tab-pane fade" id="tab-appointments" role="tabpanel">
        <div class="clinic-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0">سجل الجلسات والمواعيد</h5>
                <button class="btn btn-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#newPatientAppointmentModal">
                    <i class="bi bi-plus-lg me-1"></i> إضافة موعد جديد
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>التاريخ والوقت</th>
                            <th>الطبيب المعالج</th>
                            <th>نوع الإجراء</th>
                            <th>الحالة</th>
                            <th>الملاحظات السريرية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patient->appointments as $apt)
                        <tr>
                            <td class="fw-bold text-dark">
                                {{ $apt->appointment_date->format('Y-m-d') }}
                                <span class="text-muted small d-block font-monospace">{{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}</span>
                            </td>
                            <td>{{ $apt->doctor->name }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $apt->service_type }}</span></td>
                            <td>
                                @if($apt->status === 'completed') <span class="badge bg-success">مكتمل</span>
                                @elseif($apt->status === 'in_consultation') <span class="badge bg-warning text-dark">في الكشف</span>
                                @elseif($apt->status === 'waiting') <span class="badge bg-info text-white">في الانتظار</span>
                                @else <span class="badge bg-secondary">مؤكد</span> @endif
                            </td>
                            <td class="small text-muted">{{ $apt->notes ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">لا توجد مواعيد سابقة لهذا المريض</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 3: Financial & Invoices -->
    <div class="tab-pane fade" id="tab-billing" role="tabpanel">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="clinic-card p-3">
                    <span class="text-muted small d-block mb-1">إجمالي الفواتير الصادرة</span>
                    <h4 class="fw-bold text-dark mb-0">{{ number_format($totalInvoiced, 2) }} ج.م</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="clinic-card p-3">
                    <span class="text-muted small d-block mb-1">إجمالي المبالغ المدفوعة</span>
                    <h4 class="fw-bold text-success mb-0">{{ number_format($totalPaid, 2) }} ج.م</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="clinic-card p-3">
                    <span class="text-muted small d-block mb-1">المبلغ المتبقي / المستحق</span>
                    <h4 class="fw-bold text-danger mb-0">{{ number_format($totalRemaining, 2) }} ج.م</h4>
                </div>
            </div>
        </div>

        <div class="clinic-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0">فواتير وسندات القبض الصادرة</h5>
                @if(auth()->user()->isAdmin() || auth()->user()->isAccountant() || auth()->user()->isReceptionist())
                    <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#newPatientInvoiceModal">
                        <i class="bi bi-receipt me-1"></i> إصدار فاتورة جديدة
                    </button>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>رقم الفاتورة</th>
                            <th>التاريخ</th>
                            <th>الطبيب</th>
                            <th>الإجمالي</th>
                            <th>المدفوع</th>
                            <th>المتبقي</th>
                            <th>طريقة الدفع</th>
                            <th>حالة السداد</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patient->invoices as $inv)
                        <tr>
                            <td class="fw-bold text-primary font-monospace">#{{ $inv->invoice_number }}</td>
                            <td>{{ $inv->created_at->format('Y-m-d') }}</td>
                            <td>{{ $inv->doctor ? $inv->doctor->name : '-' }}</td>
                            <td class="fw-bold">{{ number_format($inv->total, 2) }} ج.م</td>
                            <td class="text-success fw-bold">{{ number_format($inv->paid_amount, 2) }} ج.م</td>
                            <td>
                                @if($inv->remaining_amount > 0)
                                    <span class="text-danger fw-bold font-monospace">{{ number_format($inv->remaining_amount, 2) }} ج.م</span>
                                @else
                                    <span class="text-muted small">0.00 ج.م</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi {{ $inv->paymentMethodIcon() }} me-1"></i> {{ $inv->paymentMethodName() }}
                                </span>
                            </td>
                            <td>
                                @if($inv->status === 'paid') <span class="badge bg-success">مدفوعة بالكامل</span>
                                @elseif($inv->status === 'partially_paid') <span class="badge bg-warning text-dark">دفع جزئي</span>
                                @else <span class="badge bg-danger">غير مدفوعة</span> @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    @if($inv->remaining_amount > 0 && (auth()->user()->isAdmin() || auth()->user()->isAccountant() || auth()->user()->isReceptionist()))
                                        <button class="btn btn-sm btn-success rounded-pill px-3 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#payInvoiceModal-{{ $inv->id }}" title="سداد الفاتورة">
                                            <i class="bi bi-cash-stack me-1"></i> سداد / دفع
                                        </button>
                                    @endif
                                    <a href="{{ route('clinic.billing.show', $inv) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2" title="طباعة الفاتورة">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    <a href="{{ route('clinic.billing.show', [$inv, 'type' => 'receipt']) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-2" title="طباعة سند القبض">
                                        <i class="bi bi-receipt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">لا توجد فواتير مسجلة للمريض حالياً</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modals for each Invoice Payment --}}
        @foreach($patient->invoices as $inv)
            @if($inv->remaining_amount > 0)
            <div class="modal fade" id="payInvoiceModal-{{ $inv->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-cash-coin me-2"></i> تسجيل سند سداد / دفعة مالية
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('clinic.billing.payment', $inv) }}" method="POST">
                            @csrf
                            <div class="modal-body p-4">
                                <!-- Summary Info Box -->
                                <div class="bg-light p-3 rounded-3 mb-3 border">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">رقم الفاتورة:</span>
                                        <span class="fw-bold font-monospace text-primary">#{{ $inv->invoice_number }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">المريض:</span>
                                        <span class="fw-bold text-dark">{{ $patient->name }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">إجمالي قيمة الفاتورة:</span>
                                        <span class="fw-bold">{{ number_format($inv->total, 2) }} ج.م</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">المدفوع مسبقاً:</span>
                                        <span class="text-success fw-bold">{{ number_format($inv->paid_amount, 2) }} ج.م</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-danger">المبلغ المتبقي المطلوب سداده:</span>
                                        <span class="fw-bold text-danger fs-5 font-monospace">{{ number_format($inv->remaining_amount, 2) }} ج.م</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">مبلغ السداد الحالي (ج.م) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0.01" max="{{ $inv->remaining_amount }}" name="payment_amount" class="form-control fw-bold fs-5 text-center text-success" value="{{ $inv->remaining_amount }}" required>
                                        <span class="input-group-text bg-light fw-bold">ج.م</span>
                                    </div>
                                    <small class="text-muted d-block mt-1">يمكنك إبقاء المبلغ كاملاً لإتمام السداد، أو تعديله لتسجيل دفعة جزئية.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">طريقة الدفع <span class="text-danger">*</span></label>
                                    <select name="payment_method" class="form-select" required>
                                        @foreach(\App\Models\Setting::enabledPaymentMethods() as $mKey => $m)
                                            <option value="{{ $mKey }}" {{ $inv->payment_method === $mKey ? 'selected' : '' }}>
                                                {{ $m['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label small fw-bold">ملاحظات / رقم المرجع (اختياري)</label>
                                    <input type="text" name="notes" class="form-control" placeholder="رقم عملية مدى، اسم المحول، أو أي ملاحظة...">
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                                    <i class="bi bi-check2-circle me-1"></i> تأكيد السداد وحفظ السند
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        @endforeach

        {{-- Modal for New Invoice --}}
        <div class="modal fade" id="newPatientInvoiceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-receipt me-2"></i> إصدار فاتورة جديدة للمريض
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('clinic.billing.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                        <div class="modal-body p-4">
                            <div class="alert alert-info py-2 small mb-3">
                                <i class="bi bi-person-fill me-1"></i> المريض: <strong>{{ $patient->name }}</strong> (#{{ $patient->file_number }})
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">الطبيب المعالج</label>
                                <select name="doctor_id" class="form-select">
                                    <option value="">عيادة عامة / بدون تحديد</option>
                                    @foreach($doctors as $doc)
                                        <option value="{{ $doc->id }}">{{ $doc->name }} - {{ $doc->speciality }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">المبلغ الفرعي (تكلفة العلاج) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" name="subtotal" class="form-control" placeholder="0.00" required>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-bold">الخصم (ج.م)</label>
                                    <input type="number" step="0.01" min="0" name="discount" class="form-control" value="0.00">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold">ضريبة القيمة المضافة</label>
                                    <input type="number" step="0.01" min="0" name="tax" class="form-control" value="0.00">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">المبلغ المدفوع حالياً عند الإصدار</label>
                                <input type="number" step="0.01" min="0" name="paid_amount" class="form-control" value="0.00" required>
                                <small class="text-muted">اتركه 0.00 إذا كانت الفاتورة آجلة أو لم يسدد المريض بعد.</small>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold">طريقة الدفع <span class="text-danger">*</span></label>
                                <select name="payment_method" class="form-select" required>
                                    @foreach(\App\Models\Setting::enabledPaymentMethods() as $mKey => $m)
                                        <option value="{{ $mKey }}" {{ $mKey === \App\Models\Setting::get('default_payment_method', 'card') ? 'selected' : '' }}>
                                            {{ $m['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                                <i class="bi bi-check-lg me-1"></i> إصدار الفاتورة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 4: Lab Orders -->
    <div class="tab-pane fade" id="tab-labs" role="tabpanel">
        <div class="clinic-card p-4">
            <h5 class="fw-bold text-dark mb-3">طلبيات معمل الأسنان والتركيبات الخاصة بالمريض</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>نوع التركيبة</th>
                            <th>المعمل</th>
                            <th>رقم السن</th>
                            <th>درجة اللون (Shade)</th>
                            <th>التكلفة</th>
                            <th>تاريخ الاستلام</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patient->labOrders as $order)
                        <tr>
                            <td class="fw-bold text-dark">{{ $order->item_type }}</td>
                            <td>{{ $order->lab_name }}</td>
                            <td>سن #{{ $order->tooth_numbers ?? '-' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $order->shade ?? 'N/A' }}</span></td>
                            <td>{{ number_format($order->cost, 2) }} ج.م</td>
                            <td>{{ $order->expected_delivery_date ? $order->expected_delivery_date->format('Y-m-d') : '-' }}</td>
                            <td>
                                @if($order->status === 'ready') <span class="badge bg-success">جاهز للاستلام</span>
                                @elseif($order->status === 'in_progress') <span class="badge bg-warning text-dark">قيد التصنيع</span>
                                @else <span class="badge bg-secondary">أرسل للمعمل</span> @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">لا توجد طلبيات معمل مسجلة لهذا المريض</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- MODAL: Tooth Action & Condition -->
<div class="modal fade" id="toothActionModal" tabindex="-1" aria-labelledby="toothActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark mb-0">
                    <i class="bi bi-gear-fill text-primary me-2"></i>
                    تحديث السجل الطبي: <span id="selectedToothDisplay" class="text-primary">--</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="activeToothNumber" value="">
                <input type="hidden" id="activePatientId" value="{{ $patient->id }}">

                <p class="text-muted small mb-3">اختر الحالة أو الإجراء ليتم حفظه تلقائياً في السجل الطبي للمريض:</p>
                
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
                    <label class="form-label small fw-semibold">ملاحظات الطبيب السريرية للسن</label>
                    <textarea class="form-control" id="toothClinicalNotes" rows="2" placeholder="أدخل أي ملاحظة حول السن أو العلاج..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Edit Patient Info -->
<div class="modal fade" id="editPatientModal" tabindex="-1" aria-labelledby="editPatientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-pencil-square text-primary me-2"></i> تعديل بيانات المريض
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('clinic.patients.update', $patient) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">الاسم بالكامل</label>
                        <input type="text" name="name" class="form-control" value="{{ $patient->name }}" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">رقم الجوال</label>
                            <input type="tel" name="phone" class="form-control" value="{{ $patient->phone }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">البريد الإلكتروني (لتذكيرات المواعيد)</label>
                            <input type="email" name="email" class="form-control" value="{{ $patient->email }}" placeholder="patient@example.com">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">رقم الهوية</label>
                            <input type="text" name="national_id" class="form-control" value="{{ $patient->national_id }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">الجنس</label>
                            <select name="gender" class="form-select">
                                <option value="male" {{ $patient->gender == 'male' ? 'selected' : '' }}>ذكر</option>
                                <option value="female" {{ $patient->gender == 'female' ? 'selected' : '' }}>أنثى</option>
                            </select>
                        </div>
                    </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">تاريخ الميلاد</label>
                            <input type="date" name="date_of_birth" class="form-control" value="{{ $patient->date_of_birth ? $patient->date_of_birth->format('Y-m-d') : '' }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-danger">الحساسية الدوائية</label>
                        <input type="text" name="allergies" class="form-control" value="{{ $patient->allergies }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">الأمراض المزمنة</label>
                        <input type="text" name="chronic_diseases" class="form-control" value="{{ $patient->chronic_diseases }}">
                    </div>
                </div>
                <div class="modal-footer border-top bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: Book Appointment for this patient -->
<div class="modal fade" id="newPatientAppointmentModal" tabindex="-1" aria-labelledby="newPatientAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-calendar-plus text-primary me-2"></i> حجز جلسة للمريض: {{ $patient->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('clinic.appointments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                <div class="modal-body p-4">
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
                            <label class="form-label small fw-semibold">التاريخ</label>
                            <input type="date" name="appointment_date" class="form-control" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">الوقت</label>
                            <input type="time" name="appointment_time" class="form-control" value="12:00" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">نوع الإجراء الطبي</label>
                        <input type="text" name="service_type" class="form-control" placeholder="مثال: جلسة علاج عصب ثانية، تركيب فينير..." required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">ملاحظات إضافية</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="أي تعليمات أو ملاحظات خاصة..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">تأكيد الحجز</button>
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

        // Tooth click in patient profile
        $(document).on('click', '.tooth-item', function () {
            const toothId = $(this).data('tooth-id');
            const toothName = $(this).data('tooth-name') || `السن رقم ${toothId}`;
            $('.tooth-item').removeClass('selected');
            $(this).addClass('selected');

            $('#activeToothNumber').val(toothId);
            $('#selectedToothDisplay').text(`${toothId} - ${toothName}`);
            
            const modalEl = document.getElementById('toothActionModal');
            if (modalEl) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        });

        // Apply Tooth condition and save via AJAX
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
                    const modalEl = document.getElementById('toothActionModal');
                    if (modalEl) {
                        bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                    }
                    window.location.reload(); // Refresh to update tooth history table
                },
                error: function (err) {
                    alert('تعذر حفظ حالة السن.');
                }
            });
        });
    });
</script>
@endpush
