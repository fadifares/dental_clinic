@extends('layouts.clinic')

@section('title', 'الفواتير والحسابات المالية | Dental Pro ERP')

@section('content')

<!-- Header & Quick Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">الفوترة والحسابات المالية (Billing & Invoicing) 💳</h3>
        <p class="text-muted small mb-0">إصدار الفواتير الإلكترونية، سندات القبض، ومتابعة الأقساط والدفعات المستحقة.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#newInvoiceModal">
            <i class="bi bi-receipt me-1"></i> إصدار فاتورة جديدة
        </button>
    </div>
</div>

<!-- Stats Bar -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">إجمالي الفواتير الصادرة</span>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['total_invoiced'], 2) }} <small class="fs-6 text-muted">ج.م</small></h3>
            </div>
            <div class="clinic-stat-icon bg-primary-subtle text-primary">
                <i class="bi bi-receipt-cutoff fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">المبالغ المحصلة (نقدي/شبكة)</span>
                <h3 class="fw-bold text-success mb-0">{{ number_format($stats['total_collected'], 2) }} <small class="fs-6 text-muted">ج.م</small></h3>
            </div>
            <div class="clinic-stat-icon bg-success-subtle text-success">
                <i class="bi bi-cash-stack fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">المبالغ المستحقة (الأقساط)</span>
                <h3 class="fw-bold text-danger mb-0">{{ number_format($stats['total_outstanding'], 2) }} <small class="fs-6 text-muted">ج.م</small></h3>
            </div>
            <div class="clinic-stat-icon bg-danger-subtle text-danger">
                <i class="bi bi-clock-history fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">عدد الفواتير الكلي</span>
                <h3 class="fw-bold text-info mb-0">{{ $stats['count'] }}</h3>
            </div>
            <div class="clinic-stat-icon bg-info-subtle text-info">
                <i class="bi bi-file-earmark-text fs-4"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="clinic-card p-3 mb-4">
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <span class="small fw-bold text-muted me-2"><i class="bi bi-funnel-fill"></i> تصفية الفواتير:</span>
        <a href="{{ route('clinic.billing.index') }}" class="btn btn-sm rounded-pill {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">
            الكل ({{ $stats['count'] }})
        </a>
        <a href="{{ route('clinic.billing.index', ['status' => 'paid']) }}" class="btn btn-sm rounded-pill {{ request('status') == 'paid' ? 'btn-primary' : 'btn-outline-secondary' }}">
            مدفوعة بالكامل
        </a>
        <a href="{{ route('clinic.billing.index', ['status' => 'partially_paid']) }}" class="btn btn-sm rounded-pill {{ request('status') == 'partially_paid' ? 'btn-primary' : 'btn-outline-secondary' }}">
            دفعات جزئية وأقساط
        </a>
        <a href="{{ route('clinic.billing.index', ['status' => 'unpaid']) }}" class="btn btn-sm rounded-pill {{ request('status') == 'unpaid' ? 'btn-primary' : 'btn-outline-secondary' }}">
            غير مدفوعة
        </a>
    </div>
</div>

<!-- Invoices Table Card -->
<div class="clinic-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>رقم الفاتورة</th>
                    <th>المريض</th>
                    <th>الطبيب المعالج</th>
                    <th>الإجمالي</th>
                    <th>المدفوع</th>
                    <th>المتبقي</th>
                    <th>طريقة الدفع</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th class="text-center">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr>
                    <td>
                        <span class="badge bg-primary-subtle text-primary font-monospace fw-bold px-2 py-1 fs-6">
                            #{{ $inv->invoice_number }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('clinic.patients.show', $inv->patient) }}" class="fw-bold text-dark text-decoration-none">
                            {{ $inv->patient->name }}
                        </a>
                        <div class="text-muted small font-monospace" style="font-size:0.75rem;">
                            {{ $inv->patient->phone }} • #{{ $inv->patient->file_number }}
                        </div>
                    </td>
                    <td>{{ $inv->doctor ? $inv->doctor->name : 'عيادة عامة' }}</td>
                    <td class="fw-bold text-dark">{{ number_format($inv->total, 2) }} ج.م</td>
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
                        @if($inv->status === 'paid')
                            <span class="badge bg-success">مدفوعة بالكامل</span>
                        @elseif($inv->status === 'partially_paid')
                            <span class="badge bg-warning text-dark">دفع جزئي</span>
                        @else
                            <span class="badge bg-danger">غير مدفوعة</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $inv->created_at->format('Y-m-d') }}</td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            @if($inv->remaining_amount > 0)
                                <button class="btn btn-sm btn-success rounded-pill px-3 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#payInvoiceModal-billing-{{ $inv->id }}" title="سداد الفاتورة">
                                    <i class="bi bi-cash-stack me-1"></i> سداد
                                </button>
                            @endif
                            <a href="{{ route('clinic.billing.show', $inv) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2" title="معاينة وطباعة الفاتورة">
                                <i class="bi bi-printer"></i>
                            </a>
                            <a href="{{ route('clinic.billing.show', [$inv, 'type' => 'receipt']) }}" class="btn btn-sm btn-outline-success rounded-pill px-2" title="طباعة سند القبض">
                                <i class="bi bi-receipt"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-5 text-muted">
                        <i class="bi bi-receipt display-4 text-secondary d-block mb-2"></i>
                        لا توجد فواتير مسجلة مطابقة للتصفية
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modals for Invoices Payment --}}
    @foreach($invoices as $inv)
        @if($inv->remaining_amount > 0)
        <div class="modal fade" id="payInvoiceModal-billing-{{ $inv->id }}" tabindex="-1" aria-hidden="true">
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
                        <div class="modal-body p-4 text-start">
                            <!-- Summary Info Box -->
                            <div class="bg-light p-3 rounded-3 mb-3 border">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">رقم الفاتورة:</span>
                                    <span class="fw-bold font-monospace text-primary">#{{ $inv->invoice_number }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">المريض:</span>
                                    <span class="fw-bold text-dark">{{ $inv->patient->name }}</span>
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

    @if($invoices->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $invoices->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<!-- MODAL: New Invoice -->
<div class="modal fade" id="newInvoiceModal" tabindex="-1" aria-labelledby="newInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-receipt text-primary me-2"></i> إصدار فاتورة علاج جديدة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('clinic.billing.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">المريض <span class="text-danger">*</span></label>
                        <select name="patient_id" class="form-select" required>
                            <option value="" disabled selected>اختر المريض...</option>
                            @foreach($patients as $pt)
                                <option value="{{ $pt->id }}">{{ $pt->name }} ({{ $pt->phone }} - #{{ $pt->file_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">الطبيب المشرف</label>
                        <select name="doctor_id" class="form-select">
                            <option value="">عيادة عامة</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->speciality }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">قيمة الإجراء الطبي (ج.م) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="subtotal" id="inputSubtotal" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">الخصم الممنوح (ج.م)</label>
                            <input type="number" step="0.01" name="discount" id="inputDiscount" class="form-control" placeholder="0.00" value="0.00">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">طريقة الدفع <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                @foreach(\App\Models\Setting::enabledPaymentMethods() as $mKey => $m)
                                    <option value="{{ $mKey }}" {{ $mKey === \App\Models\Setting::get('default_payment_method', 'card') ? 'selected' : '' }}>
                                        {{ $m['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">المبلغ المسدد الآن (ج.م) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="paid_amount" id="inputPaid" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
                        <i class="bi bi-check2-circle me-1"></i> إصدار وحفظ الفاتورة
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
        // Auto calculate paid amount equal to subtotal - discount by default
        $('#inputSubtotal, #inputDiscount').on('input', function () {
            let sub = parseFloat($('#inputSubtotal').val()) || 0;
            let disc = parseFloat($('#inputDiscount').val()) || 0;
            let net = Math.max(0, sub - disc);
            $('#inputPaid').val(net.toFixed(2));
        });
    });
</script>
@endpush
