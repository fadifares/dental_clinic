<!-- ==================== لوحة قيادة المحاسب المالي (Accountant Financial Dashboard) ==================== -->

<!-- Accountant Quick Actions Bar -->
<div class="clinic-card p-3 mb-4 bg-white shadow-sm border-0 rounded-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success text-white px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-wallet2 me-1"></i> الإدارة المالية والتحصيل (Cashier & Revenue)
            </span>
            <span class="text-muted small">متابعة الإيرادات اليومية، الفواتير الضريبية، سندات القبض، وتحصيل الذمم</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('clinic.billing.index') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> إصدار فاتورة ضريبية جديدة
            </a>
            <button class="btn btn-outline-success btn-sm rounded-pill px-3" onclick="window.print();">
                <i class="bi bi-printer me-1"></i> طباعة كشف اليومية
            </button>
        </div>
    </div>
</div>

<!-- Financial KPIs Grid -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="clinic-card p-3 h-100 d-flex align-items-center justify-content-between border-success-subtle">
            <div>
                <span class="text-muted small d-block mb-1">التحصيل المالي لليوم</span>
                <h3 class="fw-bold text-success mb-0">{{ number_format($accountantData['todayCollection'], 2) }} <span class="fs-6 fw-normal text-muted">ر.س</span></h3>
                <span class="text-muted" style="font-size: 0.75rem;">سندات قبض اليوم المسجلة</span>
            </div>
            <div class="clinic-stat-icon bg-success-subtle text-success">
                <i class="bi bi-cash-stack fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="clinic-card p-3 h-100 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">إجمالي إيراد الشهر الحالي</span>
                <h3 class="fw-bold text-primary mb-0">{{ number_format($accountantData['monthCollection'], 2) }} <span class="fs-6 fw-normal text-muted">ر.س</span></h3>
                <span class="text-muted" style="font-size: 0.75rem;">إجمالي فواتير الشهر: {{ $accountantData['totalInvoicesMonth'] }}</span>
            </div>
            <div class="clinic-stat-icon bg-primary-subtle text-primary">
                <i class="bi bi-graph-up-arrow fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="clinic-card p-3 h-100 d-flex align-items-center justify-content-between border-danger-subtle">
            <div>
                <span class="text-muted small d-block mb-1">الذمم المدينة المتبقية (أقساط)</span>
                <h3 class="fw-bold text-danger mb-0">{{ number_format($accountantData['outstandingDebt'], 2) }} <span class="fs-6 fw-normal text-muted">ر.س</span></h3>
                <span class="text-danger" style="font-size: 0.75rem;">مبالغ مستحقة على المرضى</span>
            </div>
            <div class="clinic-stat-icon bg-danger-subtle text-danger">
                <i class="bi bi-clock-history fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="clinic-card p-3 h-100 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">المدفوعات الإلكترونية (مدى)</span>
                <h3 class="fw-bold text-info mb-0">{{ number_format($accountantData['cardTotal'], 2) }} <span class="fs-6 fw-normal text-muted">ر.س</span></h3>
                <span class="text-muted" style="font-size: 0.75rem;">نقداً (كاش): {{ number_format($accountantData['cashTotal'], 2) }} ر.س</span>
            </div>
            <div class="clinic-stat-icon bg-info-subtle text-info">
                <i class="bi bi-credit-card-2-front-fill fs-4"></i>
            </div>
        </div>
    </div>
</div>

<!-- Middle Section: Payment Methods & Outstanding Balances -->
<div class="row g-4 mb-4">
    <!-- Payment Methods Breakdown -->
    <div class="col-xl-4">
        <div class="clinic-card p-4 h-100">
            <h5 class="fw-bold text-dark mb-3">توزيع وسائل الدفع المقبوضة 💳</h5>
            
            <div class="p-3 bg-light rounded-3 border mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small fw-bold text-dark"><i class="bi bi-credit-card text-primary me-1"></i> مدى وبطاقات بنكية</span>
                    <span class="small fw-bold text-primary">{{ number_format($accountantData['cardTotal'], 2) }} ر.س</span>
                </div>
                @php
                    $totalPaid = max(1, $accountantData['cardTotal'] + $accountantData['cashTotal']);
                    $cardPct = round(($accountantData['cardTotal'] / $totalPaid) * 100);
                    $cashPct = 100 - $cardPct;
                @endphp
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $cardPct }}%"></div>
                </div>
                <div class="text-muted small mt-1" style="font-size: 0.75rem;">{{ $cardPct }}% من إجمالي التحصيل</div>
            </div>

            <div class="p-3 bg-light rounded-3 border">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small fw-bold text-dark"><i class="bi bi-cash-coin text-success me-1"></i> نقداً (كاش في الصندوق)</span>
                    <span class="small fw-bold text-success">{{ number_format($accountantData['cashTotal'], 2) }} ر.س</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $cashPct }}%"></div>
                </div>
                <div class="text-muted small mt-1" style="font-size: 0.75rem;">{{ $cashPct }}% من إجمالي التحصيل</div>
            </div>
        </div>
    </div>

    <!-- Outstanding Debt & Aging Receivables -->
    <div class="col-xl-8">
        <div class="clinic-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">الذمم والأقساط المستحقة للتحصيل ⏳</h5>
                    <span class="text-muted small">فواتير متبقي عليها مبالغ مالية بذمة المرضى</span>
                </div>
                <span class="badge bg-danger-subtle text-danger border px-3 py-2 rounded-pill">
                    {{ $accountantData['unpaidInvoices']->count() }} فواتير معلقة
                </span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>رقم الفاتورة</th>
                            <th>المريض</th>
                            <th>الطبيب</th>
                            <th>الإجمالي</th>
                            <th>المسدد</th>
                            <th class="text-danger">المتبقي</th>
                            <th class="text-end">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accountantData['unpaidInvoices'] as $unpaid)
                        <tr>
                            <td class="font-monospace fw-bold text-dark">{{ $unpaid->invoice_number }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $unpaid->patient->name }}</div>
                                <div class="text-muted small">{{ $unpaid->patient->phone }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $unpaid->doctor->name }}</span></td>
                            <td>{{ number_format($unpaid->total, 2) }} ر.س</td>
                            <td class="text-success">{{ number_format($unpaid->paid_amount, 2) }} ر.س</td>
                            <td class="fw-bold text-danger">{{ number_format($unpaid->remaining_amount, 2) }} ر.س</td>
                            <td class="text-end">
                                <a href="{{ route('clinic.billing.show', $unpaid) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                    تحصيل <i class="bi bi-cash-coin ms-1"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-3 text-success">
                                <i class="bi bi-check-circle-fill me-1"></i> ممتاز! لا توجد ذمم أو فواتير متأخرة
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Latest Invoices Ledger -->
<div class="clinic-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold text-dark mb-0">سجل الفواتير وسندات القبض الأخيرة 🧾</h5>
            <span class="text-muted small">آخر العمليات المحاسبية المسجلة مع تفاصيل الضريبة وطرق السداد</span>
        </div>
        <a href="{{ route('clinic.billing.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
            إدارة الفواتير بالكامل <i class="bi bi-arrow-left ms-1"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>رقم الفاتورة</th>
                    <th>المريض</th>
                    <th>الطبيب المعالج</th>
                    <th>طريقة الدفع</th>
                    <th>المبلغ الإجمالي</th>
                    <th>المدفوع</th>
                    <th>المتبقي</th>
                    <th>حالة السداد</th>
                    <th class="text-end">الطباعة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accountantData['recentInvoices'] as $inv)
                <tr>
                    <td class="font-monospace fw-bold text-dark">{{ $inv->invoice_number }}</td>
                    <td>
                        <div class="fw-bold text-dark">{{ $inv->patient->name }}</div>
                        <div class="text-muted small">{{ $inv->patient->file_number }}</div>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $inv->doctor->name }}</span></td>
                    <td>
                        @if($inv->payment_method === 'card')
                            <span class="badge bg-info-subtle text-info"><i class="bi bi-credit-card me-1"></i> شبكة / مدى</span>
                        @elseif($inv->payment_method === 'cash')
                            <span class="badge bg-success-subtle text-success"><i class="bi bi-cash me-1"></i> نقداً (كاش)</span>
                        @else
                            <span class="badge bg-secondary-subtle text-dark">تحويل</span>
                        @endif
                    </td>
                    <td class="fw-bold text-dark">{{ number_format($inv->total, 2) }} ر.س</td>
                    <td class="text-success fw-semibold">{{ number_format($inv->paid_amount, 2) }} ر.س</td>
                    <td class="{{ $inv->remaining_amount > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                        {{ number_format($inv->remaining_amount, 2) }} ر.س
                    </td>
                    <td>
                        @if($inv->status === 'paid')
                            <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill">
                                <i class="bi bi-check-circle-fill me-1"></i> مسددة بالكامل
                            </span>
                        @elseif($inv->status === 'partially_paid' || $inv->status === 'partial')
                            <span class="badge bg-warning-subtle text-warning px-3 py-1 rounded-pill">
                                <i class="bi bi-clock-fill me-1"></i> سداد جزئي
                            </span>
                        @else
                            <span class="badge bg-danger-subtle text-danger px-3 py-1 rounded-pill">
                                <i class="bi bi-x-circle-fill me-1"></i> غير مسددة
                            </span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('clinic.billing.show', $inv) }}" class="btn btn-sm btn-light border rounded-pill" title="طباعة الفاتورة الضريبية">
                            <i class="bi bi-printer text-dark"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">لا توجد فواتير مسجلة حتى الآن</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
