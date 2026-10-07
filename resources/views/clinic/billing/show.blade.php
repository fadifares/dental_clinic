@extends('layouts.clinic')

@section('title', 'فاتورة علاج #' . $invoice->invoice_number . ' | Dental Pro')

@push('styles')
<style>
    /* Screen styling */
    .invoice-card {
        max-width: 820px;
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        margin: 0 auto;
        padding: 36px 42px;
        position: relative;
    }

    .invoice-watermark {
        position: absolute;
        top: 45%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-25deg);
        font-size: 5rem;
        font-weight: 900;
        color: rgba(15, 23, 42, 0.03);
        pointer-events: none;
        white-space: nowrap;
        user-select: none;
    }

    /* Print Styles (Exact A4 size specifications) */
    @page {
        size: A4 portrait;
        margin: 8mm 12mm 10mm 12mm;
    }

    @media print {
        html, body {
            background: #ffffff !important;
            color: #000000 !important;
            font-size: 11pt !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .d-print-none,
        .clinic-sidebar,
        .clinic-topbar,
        .sidebar-backdrop {
            display: none !important;
        }

        .clinic-content-wrapper {
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
            width: 100% !important;
            min-height: auto !important;
        }

        .invoice-card {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 16px 20px !important;
            border: 1px solid #94a3b8 !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            page-break-inside: avoid !important;
        }

        .table-light {
            background-color: #f1f5f9 !important;
            -webkit-print-color-adjust: exact !important;
        }

        .badge {
            border: 1px solid #475569 !important;
            -webkit-print-color-adjust: exact !important;
        }

        .invoice-watermark {
            display: none !important;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #cbd5e1 !important;
        }
    }
</style>
@endpush

@section('content')

<!-- Print & Action Toolbar (Hidden in Print Mode) -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 d-print-none" style="max-width: 820px; margin: 0 auto;">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('clinic.billing.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm">
            <i class="bi bi-arrow-right me-1"></i> العودة للفواتير
        </a>
        <a href="{{ route('clinic.patients.show', $invoice->patient_id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm">
            <i class="bi bi-person-badge me-1"></i> ملف المريض
        </a>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-light text-muted border px-2 py-1 small">
            <i class="bi bi-file-earmark-text me-1"></i> مقاس الطباعة: A4 Portrait
        </span>
        <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-printer-fill me-1"></i> طباعة الفاتورة (A4)
        </button>
    </div>
</div>

<!-- A4 Printable Invoice Card -->
<div class="invoice-card">
    <div class="invoice-watermark">DENTAL PRO</div>

    <!-- Clinic Header & Brand -->
    <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 bg-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 54px; height: 54px; font-size: 1.8rem;">
                🦷
            </div>
            <div>
                <h4 class="fw-bold mb-1 text-dark">مركز دنتال<span class="text-primary">كير</span> التخصصي لطب وجراحة الأسنان</h4>
                <div class="small text-muted">الرقم الضريبي: 300123456700003 • هاتف المواعيد: 920001234</div>
                <div class="small text-muted">طريق الملك فهد - برج الماسة الطبي - الدور الثالث</div>
            </div>
        </div>

        <div class="text-end">
            <div class="badge bg-primary text-white px-3 py-2 rounded-pill font-monospace fs-6 shadow-sm">
                {{ $invoice->invoice_number }}
            </div>
            <div class="small text-muted mt-2"><strong>تاريخ الإصدار:</strong> {{ $invoice->created_at->format('Y-m-d') }}</div>
            <div class="small text-muted"><strong>الوقت:</strong> {{ $invoice->created_at->format('h:i A') }}</div>
        </div>
    </div>

    <!-- Patient & Doctor Metadata Grid -->
    <div class="row g-2 p-3 bg-light rounded-3 mb-3 border">
        <div class="col-6">
            <div class="text-muted small">بيانات المريض:</div>
            <div class="fw-bold text-dark fs-6">{{ $invoice->patient->name }}</div>
            <div class="small text-muted font-monospace mt-1">
                رقم الملف: #{{ $invoice->patient->file_number }} • هاتف: {{ $invoice->patient->phone }}
            </div>
            @if($invoice->patient->national_id)
                <div class="small text-muted">رقم الهوية / الإقامة: {{ $invoice->patient->national_id }}</div>
            @endif
        </div>

        <div class="col-6 text-end">
            <div class="text-muted small">الطبيب المعالج:</div>
            <div class="fw-bold text-dark fs-6">{{ $invoice->doctor ? $invoice->doctor->name : 'عيادة عامة' }}</div>
            <div class="small text-muted">{{ $invoice->doctor ? $invoice->doctor->speciality : 'طب وجراحة الأسنان' }}</div>
            <div class="mt-2">
                @if($invoice->status === 'paid')
                    <span class="badge bg-success text-white px-3 py-1 rounded-pill">
                        <i class="bi bi-check-circle-fill me-1"></i> مسددة بالكامل
                    </span>
                @elseif($invoice->status === 'partially_paid')
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill">
                        <i class="bi bi-clock-fill me-1"></i> سداد جزئي (متبقي أقساط)
                    </span>
                @else
                    <span class="badge bg-danger text-white px-3 py-1 rounded-pill">
                        <i class="bi bi-exclamation-circle-fill me-1"></i> غير مسددة (آجلة)
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Procedures & Treatments Table -->
    <table class="table table-bordered align-middle mb-3">
        <thead class="table-light">
            <tr>
                <th class="text-center" style="width: 45px;">#</th>
                <th>البيان / الخدمة والإجراء الطبي</th>
                <th class="text-center" style="width: 75px;">الكمية</th>
                <th class="text-end" style="width: 140px;">السعر الإفرادي</th>
                <th class="text-end" style="width: 140px;">الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center font-monospace">1</td>
                <td>
                    <div class="fw-bold text-dark">جلسة علاج وخدمات طبية للأسنان</div>
                    <small class="text-muted">موجب السجل الطبي لجلسة التاريخ: {{ $invoice->created_at->format('Y-m-d') }}</small>
                </td>
                <td class="text-center font-monospace">1</td>
                <td class="text-end font-monospace">{{ number_format($invoice->subtotal, 2) }} ج.م</td>
                <td class="text-end fw-bold font-monospace">{{ number_format($invoice->subtotal, 2) }} ج.م</td>
            </tr>
        </tbody>
    </table>

    <!-- Totals & E-Invoice QR Code -->
    <div class="row align-items-center pt-1 mb-4">
        <!-- E-Invoice QR Stamp -->
        <div class="col-6">
            <div class="p-3 bg-light rounded-3 border d-inline-flex align-items-center gap-3">
                <div class="bg-white p-2 border rounded shadow-sm">
                    <!-- Clean Vector QR Code representation -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="68" height="68" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-dark">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                        <line x1="17" y1="7" x2="17.01" y2="7"></line>
                        <line x1="7" y1="17" x2="7.01" y2="17"></line>
                        <line x1="17" y1="17" x2="17.01" y2="17"></line>
                    </svg>
                </div>
                <div class="small text-muted">
                    <span class="fw-bold d-block text-dark">فاتورة ضريبية مبسطة</span>
                    <span>معتمدة إلكترونياً بنظام دنتال برو</span>
                    <div class="font-monospace text-secondary" style="font-size: 0.72rem;">REF: {{ substr(md5($invoice->invoice_number . $invoice->created_at), 0, 12) }}</div>
                </div>
            </div>
        </div>

        <!-- Financial Summary Breakdown -->
        <div class="col-6">
            <div class="d-flex justify-content-between py-1 small border-bottom">
                <span class="text-muted">المجموع الفرعي:</span>
                <span class="font-monospace fw-semibold">{{ number_format($invoice->subtotal, 2) }} ج.م</span>
            </div>

            @if($invoice->discount > 0)
            <div class="d-flex justify-content-between py-1 small border-bottom text-danger">
                <span>الخصم الممنوح:</span>
                <span class="font-monospace">- {{ number_format($invoice->discount, 2) }} ج.م</span>
            </div>
            @endif

            @if($invoice->tax > 0)
            <div class="d-flex justify-content-between py-1 small border-bottom text-muted">
                <span>ضريبة القيمة المضافة:</span>
                <span class="font-monospace">+ {{ number_format($invoice->tax, 2) }} ج.م</span>
            </div>
            @endif

            <div class="d-flex justify-content-between py-2 fs-5 fw-bold border-bottom text-dark">
                <span>الإجمالي الصافي:</span>
                <span class="text-primary font-monospace">{{ number_format($invoice->total, 2) }} ج.م</span>
            </div>

            <div class="d-flex justify-content-between py-1 small border-bottom text-success fw-bold">
                <span>
                    المبلغ المدفوع 
                    <small class="text-muted fw-normal">
                        ({{ match($invoice->payment_method) {
                            'card' => 'مدى / بطاقة',
                            'cash' => 'نقداً (كاش)',
                            'bank_transfer' => 'تحويل بنكي',
                            'installments' => 'أقساط مجدولة',
                            default => $invoice->payment_method
                        } }})
                    </small>:
                </span>
                <span class="font-monospace">{{ number_format($invoice->paid_amount, 2) }} ج.م</span>
            </div>

            @if($invoice->remaining_amount > 0)
            <div class="d-flex justify-content-between py-1 small text-danger fw-bold">
                <span>المتبقي (أقساط مستحقة):</span>
                <span class="font-monospace">{{ number_format($invoice->remaining_amount, 2) }} ج.م</span>
            </div>
            @else
            <div class="d-flex justify-content-between py-1 small text-muted">
                <span>المتبقي:</span>
                <span class="font-monospace text-success fw-bold">0.00 ج.م (خالص)</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Signatures & Official Stamp Footer -->
    <div class="row pt-3 border-top text-center text-muted small mt-4">
        <div class="col-6">
            <div class="fw-bold text-dark">توقيع واستلام المريض</div>
            <div class="text-muted" style="font-size: 0.75rem;">أقر بصحة البيانات واستلام سند السداد</div>
            <div class="mt-4 border-bottom w-50 mx-auto" style="border-style: dashed !important;"></div>
        </div>
        <div class="col-6">
            <div class="fw-bold text-dark">ختم وتوقيع المحاسب الطبي</div>
            <div class="text-muted" style="font-size: 0.75rem;">قسم المالية والمحاسبة - دنتال برو</div>
            <div class="mt-4 border-bottom w-50 mx-auto" style="border-style: dashed !important;"></div>
        </div>
    </div>

    <!-- Bottom Notice -->
    <div class="text-center text-muted pt-3 mt-3 border-top" style="font-size: 0.72rem;">
        فاتورة علاجية رسمية صادرة من نظام إدارة العيادات دنتال برو • نتمنى لكم دوام الصحة والعافية
    </div>
</div>

@endsection
