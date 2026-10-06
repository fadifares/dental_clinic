@extends('layouts.clinic')

@section('title', 'فاتورة علاج #' . $invoice->invoice_number . ' | Dental Pro')

@section('content')

<!-- Print Action Buttons (hidden in print mode) -->
<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <a href="{{ route('clinic.billing.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-right me-1"></i> العودة للفواتير
    </a>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-printer-fill me-1"></i> طباعة الفاتورة الضريبية
        </button>
    </div>
</div>

<!-- Printable Invoice Card -->
<div class="clinic-card p-5 bg-white border shadow-sm mx-auto" style="max-width: 850px;">
    <!-- Clinic Header -->
    <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
        <div class="d-flex align-items-center gap-3">
            <span class="clinic-stat-icon bg-primary text-white" style="width: 55px; height: 55px; font-size: 1.8rem;">
                <i class="bi bi-heart-pulse-fill"></i>
            </span>
            <div>
                <h4 class="fw-bold mb-0 text-dark">مركز دنتال<span class="text-primary">كير</span> لطب وجراحة الأسنان</h4>
                <div class="small text-muted">الرقم الضريبي: 300123456700003 • هاتف: 920001234</div>
                <div class="small text-muted">الرياض، طريق الملك فهد - برج الماسة الطبي</div>
            </div>
        </div>
        <div class="text-start">
            <span class="badge bg-primary text-white px-3 py-2 rounded-pill font-monospace fs-6">
                {{ $invoice->invoice_number }}
            </span>
            <div class="small text-muted mt-2">تاريخ الإصدار: {{ $invoice->created_at->format('Y-m-d') }}</div>
            <div class="small text-muted">الوقت: {{ $invoice->created_at->format('h:i A') }}</div>
        </div>
    </div>

    <!-- Patient & Doctor Info Box -->
    <div class="row g-3 p-3 bg-light rounded-3 mb-4 border">
        <div class="col-6">
            <div class="text-muted small">اسم المريض:</div>
            <div class="fw-bold text-dark fs-6">{{ $invoice->patient->name }}</div>
            <div class="small text-muted font-monospace">رقم الملف: #{{ $invoice->patient->file_number }} • هاتف: {{ $invoice->patient->phone }}</div>
            @if($invoice->patient->national_id)
                <div class="small text-muted">الهوية: {{ $invoice->patient->national_id }}</div>
            @endif
        </div>
        <div class="col-6 text-start">
            <div class="text-muted small">الطبيب المعالج:</div>
            <div class="fw-bold text-dark fs-6">{{ $invoice->doctor ? $invoice->doctor->name : 'عيادة عامة' }}</div>
            <div class="small text-muted">{{ $invoice->doctor ? $invoice->doctor->speciality : '-' }}</div>
            <div class="mt-2">
                <span class="badge {{ $invoice->status == 'paid' ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-1">
                    {{ $invoice->status == 'paid' ? 'مدفوعة بالكامل' : 'دفعة جزئية / أقساط' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Invoice Items Table -->
    <table class="table table-bordered align-middle mb-4">
        <thead class="table-light">
            <tr>
                <th style="width: 50px;">#</th>
                <th>البيان / الإجراء الطبي</th>
                <th class="text-center" style="width: 100px;">الكمية</th>
                <th class="text-end" style="width: 150px;">السعر الإفرادي</th>
                <th class="text-end" style="width: 150px;">الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>
                    <div class="fw-bold text-dark">جلسة علاج وخدمات طبية للأسنان</div>
                    <small class="text-muted">موجب السجل الطبي لجلسة التاريخ: {{ $invoice->created_at->format('Y-m-d') }}</small>
                </td>
                <td class="text-center font-monospace">1</td>
                <td class="text-end font-monospace">{{ number_format($invoice->subtotal, 2) }} ر.س</td>
                <td class="text-end fw-bold font-monospace">{{ number_format($invoice->subtotal, 2) }} ر.س</td>
            </tr>
        </tbody>
    </table>

    <!-- Totals Summary & QR -->
    <div class="row align-items-center pt-2">
        <div class="col-6">
            <div class="p-3 bg-light rounded-3 border d-inline-flex align-items-center gap-3">
                <div class="bg-white p-2 border rounded">
                    <!-- QR Code Placeholder -->
                    <i class="bi bi-qr-code fs-1 text-dark"></i>
                </div>
                <div class="small text-muted">
                    <span class="fw-bold d-block text-dark">فاتورة ضريبية مبسطة</span>
                    معتمدة إلكترونياً من نظام دنتال برو
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="d-flex justify-content-between py-1 small border-bottom">
                <span class="text-muted">المجموع الفرعي:</span>
                <span class="font-monospace fw-semibold">{{ number_format($invoice->subtotal, 2) }} ر.س</span>
            </div>
            @if($invoice->discount > 0)
            <div class="d-flex justify-content-between py-1 small border-bottom text-danger">
                <span>الخصم الممنوح:</span>
                <span class="font-monospace">- {{ number_format($invoice->discount, 2) }} ر.س</span>
            </div>
            @endif
            <div class="d-flex justify-content-between py-2 fs-5 fw-bold border-bottom text-dark">
                <span>الإجمالي الكلي:</span>
                <span class="text-primary font-monospace">{{ number_format($invoice->total, 2) }} ر.س</span>
            </div>
            <div class="d-flex justify-content-between py-1 small border-bottom text-success fw-bold">
                <span>المبلغ المدفوع ({{ $invoice->payment_method }}):</span>
                <span class="font-monospace">{{ number_format($invoice->paid_amount, 2) }} ر.س</span>
            </div>
            @if($invoice->remaining_amount > 0)
            <div class="d-flex justify-content-between py-1 small text-danger fw-bold">
                <span>المتبقي (أقساط):</span>
                <span class="font-monospace">{{ number_format($invoice->remaining_amount, 2) }} ر.س</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Signatures & Footer -->
    <div class="row mt-5 pt-4 border-top text-center text-muted small">
        <div class="col-6">
            <div>توقيع واستلام المريض</div>
            <div class="mt-4 border-bottom w-50 mx-auto"></div>
        </div>
        <div class="col-6">
            <div>ختم وتوقيع المحاسب الطبي</div>
            <div class="mt-4 border-bottom w-50 mx-auto"></div>
        </div>
    </div>
</div>

@endsection
