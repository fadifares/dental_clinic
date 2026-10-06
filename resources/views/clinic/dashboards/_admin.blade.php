<!-- ==================== لوحة قيادة مدير النظام (Admin Executive Dashboard) ==================== -->

<!-- Executive Quick Actions Bar -->
<div class="clinic-card p-3 mb-4 bg-white shadow-sm border-0 rounded-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger text-white px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-shield-shaded me-1"></i> لوحة الإدارة العامة (Executive Control)
            </span>
            <span class="text-muted small">نظرة شاملة ومؤشرات أداء كافة الأقسام الطبية، التشغيلية، والمالية</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#newAppointmentModal">
                <i class="bi bi-calendar-plus me-1"></i> حجز موعد سريع
            </button>
            <a href="{{ route('clinic.patients.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-person-plus me-1"></i> سجل المرضى
            </a>
            <a href="{{ route('clinic.billing.index') }}" class="btn btn-outline-success btn-sm rounded-pill px-3">
                <i class="bi bi-receipt me-1"></i> الفواتير والمالية
            </a>
            <a href="{{ route('clinic.labs.index') }}" class="btn btn-outline-info btn-sm rounded-pill px-3">
                <i class="bi bi-box-seam me-1"></i> طلبيات المعامل
            </a>
            <a href="{{ route('clinic.users.index') }}" class="btn btn-danger btn-sm rounded-pill px-3 text-white">
                <i class="bi bi-shield-lock-fill me-1"></i> المستخدمين والأمان
            </a>
        </div>
    </div>
</div>

<!-- Executive Stats Grid -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="clinic-card p-3 h-100 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">التحصيل المالي لليوم</span>
                <h3 class="fw-bold text-success mb-0">{{ number_format($adminData['todayCollection'], 2) }} <span class="fs-6 fw-normal text-muted">ر.س</span></h3>
                <span class="text-muted" style="font-size: 0.75rem;">إجمالي الشهر: {{ number_format($adminData['monthCollection'], 2) }} ر.س</span>
            </div>
            <div class="clinic-stat-icon bg-success-subtle text-success">
                <i class="bi bi-cash-stack fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="clinic-card p-3 h-100 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">إجمالي مواعيد اليوم</span>
                <h3 class="fw-bold text-primary mb-0">{{ $adminData['totalAppointmentsCount'] }}</h3>
                <div class="d-flex gap-2 mt-1" style="font-size: 0.75rem;">
                    <span class="text-warning"><i class="bi bi-hourglass-split"></i> {{ $adminData['waitingCount'] }} انتظار</span>
                    <span class="text-success"><i class="bi bi-check-circle"></i> {{ $adminData['completedCount'] }} منتهية</span>
                </div>
            </div>
            <div class="clinic-stat-icon bg-primary-subtle text-primary">
                <i class="bi bi-calendar2-check-fill fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="clinic-card p-3 h-100 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">أطباء العيادة العاملين</span>
                <h3 class="fw-bold text-info mb-0">{{ $adminData['totalDoctorsCount'] }} <span class="fs-6 fw-normal text-muted">طبيب</span></h3>
                <span class="text-muted" style="font-size: 0.75rem;">إجمالي الكادر: {{ $adminData['totalStaffCount'] }} موظف نشط</span>
            </div>
            <div class="clinic-stat-icon bg-info-subtle text-info">
                <i class="bi bi-heart-pulse-fill fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="clinic-card p-3 h-100 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">إجمالي ملفات المرضى (EMR)</span>
                <h3 class="fw-bold text-dark mb-0">{{ $adminData['totalPatientsCount'] }}</h3>
                <span class="text-muted" style="font-size: 0.75rem;">طلبيات المعامل النشطة: {{ $adminData['activeLabOrdersCount'] }}</span>
            </div>
            <div class="clinic-stat-icon bg-warning-subtle text-warning">
                <i class="bi bi-people-fill fs-4"></i>
            </div>
        </div>
    </div>
</div>

<!-- Middle Section: Today's Master Schedule + Doctor Productivity -->
<div class="row g-4 mb-4">
    <!-- Today's Master Schedule -->
    <div class="col-xl-8">
        <div class="clinic-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">جدول العمليات والمواعيد اليومية 📅</h5>
                    <span class="text-muted small">متابعة دقيقة لكل العيادات وغرف الكشف والاستقبال</span>
                </div>
                <a href="{{ route('clinic.appointments.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                    عرض شاشة الاستقبال <i class="bi bi-arrow-left ms-1"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>المريض</th>
                            <th>الطبيب المعالج</th>
                            <th>الإجراء / الخدمة</th>
                            <th>الوقت</th>
                            <th>الحالة</th>
                            <th class="text-end">التحكم</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todayAppointments as $apt)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $apt->patient->name }}</div>
                                <div class="text-muted small">{{ $apt->patient->file_number }} • {{ $apt->patient->phone }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $apt->doctor->name }}</span>
                            </td>
                            <td>{{ $apt->service_type }}</td>
                            <td class="font-monospace text-primary fw-semibold">{{ substr($apt->appointment_time, 0, 5) }}</td>
                            <td>
                                @if($apt->status === 'waiting')
                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> في الانتظار</span>
                                @elseif($apt->status === 'in_consultation')
                                    <span class="badge bg-primary"><i class="bi bi-heart-pulse me-1"></i> في غرفة الكشف</span>
                                @elseif($apt->status === 'completed')
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> اكتملت الجلسة</span>
                                @else
                                    <span class="badge bg-secondary">مجدول</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('clinic.patients.show', $apt->patient) }}" class="btn btn-sm btn-light border rounded-pill" title="فتح ملف المريض">
                                    <i class="bi bi-folder2-open text-primary"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">لا توجد مواعيد مسجلة لليوم</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Doctor Performance & Production Breakdown -->
    <div class="col-xl-4">
        <div class="clinic-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0">إنتاجية كادر الأطباء 🩺</h5>
                <span class="badge bg-info-subtle text-info">شهري ولحظي</span>
            </div>

            <div class="d-flex flex-column gap-3">
                @foreach($adminData['doctorsPerformance'] as $perf)
                <div class="p-3 bg-light rounded-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-dark">{{ $perf['doctor']->name }}</span>
                        <span class="badge bg-primary-subtle text-primary">{{ $perf['doctor']->commission_rate }}% نسبة</span>
                    </div>
                    <div class="small text-muted mb-2">{{ $perf['doctor']->speciality }}</div>
                    <div class="d-flex justify-content-between text-muted small pt-2 border-top">
                        <span>مواعيد اليوم: <strong class="text-dark">{{ $perf['today_appointments'] }}</strong></span>
                        <span>دخل الشهر: <strong class="text-success">{{ number_format($perf['monthly_revenue'], 2) }} ر.س</strong></span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Bottom Row: Financial Overview & Active Lab Orders -->
<div class="row g-4">
    <!-- Financial Stream -->
    <div class="col-xl-6">
        <div class="clinic-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">آخر العمليات والفواتير المالية 💳</h5>
                    <span class="text-muted small">تدفق سندات القبض والتحصيل المسجلة</span>
                </div>
                <a href="{{ route('clinic.billing.index') }}" class="btn btn-sm btn-outline-success rounded-pill">
                    سجل الفواتير <i class="bi bi-arrow-left ms-1"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>رقم الفاتورة</th>
                            <th>المريض</th>
                            <th>المبلغ الإجمالي</th>
                            <th>حالة السداد</th>
                            <th class="text-end">التفاصيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($adminData['recentInvoices'] as $inv)
                        <tr>
                            <td class="font-monospace fw-bold text-dark">{{ $inv->invoice_number }}</td>
                            <td>{{ $inv->patient->name }}</td>
                            <td class="fw-bold text-success">{{ number_format($inv->total, 2) }} ر.س</td>
                            <td>
                                @if($inv->status === 'paid')
                                    <span class="badge bg-success-subtle text-success">مسددة بالكامل</span>
                                @elseif($inv->status === 'partially_paid' || $inv->status === 'partial')
                                    <span class="badge bg-warning-subtle text-warning">سداد جزئي</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">غير مسددة</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('clinic.billing.show', $inv) }}" class="btn btn-sm btn-light border rounded-pill">
                                    <i class="bi bi-printer text-dark"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">لا توجد فواتير مسجلة</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Active Lab Orders -->
    <div class="col-xl-6">
        <div class="clinic-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">طلبيات معمل الأسنان والتركيبات 📦</h5>
                    <span class="text-muted small">تتبع تصنيع التيجان، الجسور، والفينير الخارجي</span>
                </div>
                <a href="{{ route('clinic.labs.index') }}" class="btn btn-sm btn-outline-info rounded-pill">
                    كل الطلبيات <i class="bi bi-arrow-left ms-1"></i>
                </a>
            </div>

            <div class="d-flex flex-column gap-3">
                @forelse($adminData['labOrders'] as $order)
                <div class="p-3 bg-light rounded-3 border d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold text-dark">{{ $order->item_type }} @if($order->tooth_numbers) (سن #{{ $order->tooth_numbers }}) @endif</div>
                        <div class="small text-muted">المريض: {{ $order->patient->name }} • {{ $order->lab_name }}</div>
                        <div class="small text-secondary mt-1">
                            <i class="bi bi-palette text-primary me-1"></i> درجة اللون: {{ $order->shade ?? 'N/A' }} • التكلفة: {{ number_format($order->cost, 2) }} ر.س
                        </div>
                    </div>
                    <div>
                        @if($order->status === 'ready')
                            <span class="badge bg-success p-2">جاهز للاستلام</span>
                        @elseif($order->status === 'in_progress')
                            <span class="badge bg-warning text-dark p-2">قيد التصنيع</span>
                        @else
                            <span class="badge bg-secondary p-2">تم الإرسال</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">لا توجد طلبيات معمل جارية</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
