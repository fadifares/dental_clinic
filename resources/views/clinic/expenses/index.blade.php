@extends('layouts.clinic')

@section('title', 'مصاريف ونفقات العيادة | Dental Pro')

@section('content')

<!-- Header & Add Button -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-wallet2 text-warning me-2"></i> سجل مصاريف ونفقات العيادة
        </h3>
        <p class="text-muted small mb-0">توثيق ومتابعة كافة المصروفات التشغيلية، فواتير المشتريات، وصافي أرباح العيادة.</p>
    </div>

    <div>
        <button class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#newExpenseModal">
            <i class="bi bi-plus-circle me-1"></i> تسجيل مصروف جديد
        </button>
    </div>
</div>

<!-- Alert Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
    <i class="bi bi-check-circle-fill fs-5 text-success"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
    <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> يرجى تصحيح الأخطاء التالية:</div>
    <ul class="mb-0 small ps-3">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Stats KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="clinic-card p-3 h-100 d-flex align-items-center justify-content-between border-danger-subtle">
            <div>
                <span class="text-muted small d-block mb-1">مصاريف الشهر الحالي</span>
                <h3 class="fw-bold text-danger mb-0">{{ number_format($stats['month_expenses'], 2) }} <small class="fs-6 text-muted">ج.م</small></h3>
                <span class="text-muted" style="font-size: 0.75rem;">إجمالي نفقات {{ now()->translatedFormat('F Y') }}</span>
            </div>
            <div class="clinic-stat-icon bg-danger-subtle text-danger">
                <i class="bi bi-cash-stack fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="clinic-card p-3 h-100 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">مصروفات اليوم</span>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['today_expenses'], 2) }} <small class="fs-6 text-muted">ج.م</small></h3>
                <span class="text-muted" style="font-size: 0.75rem;">المسجل لتاريخ اليوم</span>
            </div>
            <div class="clinic-stat-icon bg-warning-subtle text-warning">
                <i class="bi bi-calendar-day fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="clinic-card p-3 h-100 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">إيرادات الشهر المحصلة</span>
                <h3 class="fw-bold text-success mb-0">{{ number_format($stats['month_collections'], 2) }} <small class="fs-6 text-muted">ج.م</small></h3>
                <span class="text-muted" style="font-size: 0.75rem;">سندات القبض المسددة</span>
            </div>
            <div class="clinic-stat-icon bg-success-subtle text-success">
                <i class="bi bi-graph-up-arrow fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="clinic-card p-3 h-100 d-flex align-items-center justify-content-between {{ $stats['net_income_month'] >= 0 ? 'border-primary-subtle' : 'border-danger-subtle' }}">
            <div>
                <span class="text-muted small d-block mb-1">صافي التدفق المالي (الأرباح)</span>
                <h3 class="fw-bold {{ $stats['net_income_month'] >= 0 ? 'text-primary' : 'text-danger' }} mb-0">
                    {{ number_format($stats['net_income_month'], 2) }} <small class="fs-6 text-muted">ج.م</small>
                </h3>
                <span class="text-muted" style="font-size: 0.75rem;">(الإيرادات - المصاريف)</span>
            </div>
            <div class="clinic-stat-icon {{ $stats['net_income_month'] >= 0 ? 'bg-primary-subtle text-primary' : 'bg-danger-subtle text-danger' }}">
                <i class="bi bi-pie-chart-fill fs-4"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters & Search Toolbar -->
<div class="clinic-card p-3 mb-4">
    <form action="{{ route('clinic.expenses.index') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small fw-semibold">بحث بالبيان أو الملاحظات</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control bg-light border-0" placeholder="مثال: شراء بنج، صيانة..." value="{{ request('search') }}">
            </div>
        </div>

        <div class="col-md-3">
            <label class="form-label small fw-semibold">تصنيف المصروف</label>
            <select name="category" class="form-select form-select-sm bg-light border-0">
                <option value="">جميع التصنيفات</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-semibold">من تاريخ</label>
            <input type="date" name="from_date" class="form-control form-control-sm bg-light border-0" value="{{ request('from_date') }}">
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-semibold">إلى تاريخ</label>
            <input type="date" name="to_date" class="form-control form-control-sm bg-light border-0" value="{{ request('to_date') }}">
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm rounded-pill w-100 fw-bold">
                <i class="bi bi-funnel me-1"></i> تصفية
            </button>
            @if(request()->hasAny(['search', 'category', 'from_date', 'to_date']))
            <a href="{{ route('clinic.expenses.index') }}" class="btn btn-light btn-sm rounded-pill border" title="إعادة ضبط">
                <i class="bi bi-arrow-counterclockwise"></i>
            </a>
            @endif
        </div>
    </form>
</div>

<!-- Expenses Table Card -->
<div class="clinic-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark mb-0">قائمة سندات وفواتير المصاريف</h5>
        <span class="badge bg-light text-muted border font-monospace">إجمالي السجلات: {{ $expenses->total() }}</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th style="width: 60px;">#</th>
                    <th style="width: 120px;">التاريخ</th>
                    <th>البيان / تفاصيل المصروف</th>
                    <th>التصنيف</th>
                    <th>طريقة الدفع</th>
                    <th>المرجع / الفاتورة</th>
                    <th>المبلغ</th>
                    <th>المسؤول</th>
                    <th class="text-center" style="width: 80px;">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $exp)
                <tr>
                    <td class="font-monospace text-muted small">#{{ $exp->id }}</td>
                    <td class="font-monospace">{{ $exp->expense_date->format('Y-m-d') }}</td>
                    <td>
                        <div class="fw-bold text-dark">{{ $exp->title }}</div>
                        @if($exp->notes)
                            <div class="text-muted small" style="font-size: 0.78rem;">{{ $exp->notes }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $exp->getCategoryBadgeClass() }} border px-2 py-1 rounded-pill">
                            {{ $exp->getCategoryLabel() }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            <i class="bi {{ \App\Models\Setting::paymentMethodIcon($exp->payment_method) }} me-1 text-primary"></i>
                            {{ $exp->getPaymentMethodLabel() }}
                        </span>
                    </td>
                    <td>
                        <div class="font-monospace text-muted small">{{ $exp->invoice_reference ?? '-' }}</div>
                        @if($exp->hasReceiptImage())
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill py-0 px-2 mt-1 fw-bold" style="font-size: 0.72rem;" data-bs-toggle="modal" data-bs-target="#viewReceiptModal-{{ $exp->id }}">
                                <i class="bi bi-receipt me-1"></i> عرض الفاتورة
                            </button>
                        @endif
                    </td>
                    <td class="fw-bold text-danger font-monospace fs-6">
                        - {{ number_format($exp->amount, 2) }} ج.م
                    </td>
                    <td class="small text-muted">
                        {{ $exp->user ? $exp->user->name : '-' }}
                    </td>
                    <td class="text-center">
                        <form action="{{ route('clinic.expenses.destroy', $exp) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف سند المصروف هذا؟');" class="d-inline mb-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle p-1" title="حذف المصروف" style="width: 32px; height: 32px;">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </form>
                    </td>
                </tr>

                @if($exp->hasReceiptImage())
                <!-- Modal View Receipt Image -->
                <div class="modal fade" id="viewReceiptModal-{{ $exp->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content rounded-4 border-0 shadow">
                            <div class="modal-header border-bottom d-flex justify-content-between align-items-center">
                                <h5 class="modal-title fw-bold text-dark mb-0">
                                    <i class="bi bi-receipt text-primary me-2"></i> صورة الفاتورة / الإيصال: {{ $exp->title }}
                                </h5>
                                <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4 text-center bg-light">
                                <div class="mb-3 d-flex flex-wrap justify-content-between align-items-center bg-white p-3 rounded-3 border">
                                    <span class="small text-muted"><i class="bi bi-calendar3 me-1"></i> {{ $exp->expense_date->format('Y-m-d') }}</span>
                                    <span class="fw-bold text-danger font-monospace fs-5">- {{ number_format($exp->amount, 2) }} ج.م</span>
                                    <span class="small text-muted">
                                        <i class="bi {{ \App\Models\Setting::paymentMethodIcon($exp->payment_method) }} me-1"></i>
                                        {{ $exp->getPaymentMethodLabel() }}
                                    </span>
                                    @if($exp->invoice_reference)
                                        <span class="badge bg-light text-dark border font-monospace">مرجع: {{ $exp->invoice_reference }}</span>
                                    @endif
                                </div>
                                <div class="p-2 bg-white rounded-3 border shadow-sm d-inline-block w-100">
                                    <img src="{{ $exp->getReceiptImageUrl() }}" alt="فاتورة {{ $exp->title }}" class="img-fluid rounded-2 shadow-sm" style="max-height: 520px; object-fit: contain;">
                                </div>
                            </div>
                            <div class="modal-footer border-top bg-light rounded-bottom-4 d-flex justify-content-between">
                                <a href="{{ $exp->getReceiptImageUrl() }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> فتح الصورة بالحجم الكامل
                                </a>
                                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-wallet2 fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            لا توجد أي سندات مصاريف مسجلة حتى الآن.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($expenses->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $expenses->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<!-- MODAL: Add New Expense -->
<div class="modal fade" id="newExpenseModal" tabindex="-1" aria-labelledby="newExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-bold text-dark mb-0">
                    <i class="bi bi-wallet-fill text-warning me-2"></i> تسجيل سند مصروف جديد
                </h5>
                <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('clinic.expenses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">عنوان / بيان المصروف <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="مثال: شراء كراتين حشوات كمبوزيت، إيجار العيادة..." required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">تصنيف المصروف <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="materials" selected>مواد ومستهلكات طبية</option>
                                <option value="rent">إيجار العيادة</option>
                                <option value="utilities">فواتير وكهرباء ومياه</option>
                                <option value="maintenance">صيانة أجهزة ومعدات</option>
                                <option value="salaries">رواتب ومستحقات</option>
                                <option value="marketing">تسويق وإعلانات</option>
                                <option value="other">نثريات ومصاريف أخرى</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">المبلغ (ج.م) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="amount" class="form-control fw-bold" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">تاريخ الصرف <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">طريقة الدفع <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                @foreach(\App\Models\Setting::enabledPaymentMethods() as $key => $method)
                                    <option value="{{ $key }}" {{ $key === \App\Models\Setting::get('default_payment_method', 'cash') ? 'selected' : '' }}>
                                        {{ $method['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">رقم الفاتورة أو إيصال الشراء المرجعي</label>
                        <input type="text" name="invoice_reference" class="form-control" placeholder="مثال: BILL-8894 أو رقم سند المورد...">
                    </div>

                    <!-- Invoice / Receipt Image Upload Field -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-paperclip text-primary me-1"></i> صورة الفاتورة أو إيصال الدفع / التحويل (إنستاباي / بنك)</span>
                            <span class="badge bg-light text-muted border">اختياري</span>
                        </label>
                        <input type="file" name="receipt_image" id="receipt_image_input" class="form-control" accept="image/jpeg,image/png,image/webp,image/jpg" onchange="previewReceiptImage(this)">
                        <div class="form-text text-muted small mt-1">
                            <i class="bi bi-info-circle me-1 text-primary"></i> يدعم صور الفواتير الورقية، إيصالات الشراء، وسكرين شوت تحويلات إنستاباي / البنك (يتم ضغط الصورة تلقائياً للحفاظ على جودتها وسرعتها).
                        </div>
                        <div id="receipt_preview_box" class="mt-2 p-2 bg-light rounded-3 border text-center d-none">
                            <img id="receipt_preview_tag" src="#" alt="معاينة الفاتورة" class="img-thumbnail rounded shadow-sm" style="max-height: 160px; object-fit: contain;">
                            <div class="small text-muted mt-1 font-monospace" id="receipt_preview_info"></div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">ملاحظات إضافية</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="أي تفاصيل أو بنود حول عملية الصرف..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">
                        <i class="bi bi-check2-circle me-1"></i> حفظ وتأكيد الصرف
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewReceiptImage(input) {
    const previewBox = document.getElementById('receipt_preview_box');
    const previewTag = document.getElementById('receipt_preview_tag');
    const previewInfo = document.getElementById('receipt_preview_info');
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            previewTag.src = e.target.result;
            previewInfo.textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
            previewBox.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    } else {
        previewBox.classList.add('d-none');
        previewTag.src = '#';
        previewInfo.textContent = '';
    }
}
</script>
@endpush

@endsection
