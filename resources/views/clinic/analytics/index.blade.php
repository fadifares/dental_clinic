@extends('layouts.clinic')

@section('title', 'مؤشرات الأداء وإحصائيات النشاط للعيادة | Dental Pro ERP')

@section('content')

<!-- Header & Quick Overview -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">مؤشرات الأداء وإحصائيات النشاط للعيادة 📈</h3>
        <p class="text-muted small mb-0">تحليل تفصيلي لحركة تسجيل الدخول، الصفحات الأكثر استخداماً، إنتاجية الكادر، ومؤشرات الأداء الطبية والمالية.</p>
    </div>

    <div class="d-flex align-items-center gap-2">
        @if(auth()->user()->isSuperAdmin())
            <a href="{{ route('appmanager.index') }}" class="btn btn-dark btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-shield-lock-fill text-warning me-1"></i> لوحة مدير التطبيق (App Manager)
            </a>
        @endif
        <button class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> طباعة التقرير
        </button>
    </div>
</div>

<!-- Quick High-Level KPIs -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="clinic-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">حسابات كادر العيادة</span>
                <i class="bi bi-people text-primary fs-5"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1">{{ $analytics['kpis']['active_users'] }} <span class="fs-6 text-muted fw-normal">/ {{ $analytics['kpis']['max_users'] }} مسموح</span></h4>
            @php
                $userPercent = $analytics['kpis']['max_users'] > 0 ? min(100, round(($analytics['kpis']['active_users'] / $analytics['kpis']['max_users']) * 100)) : 0;
            @endphp
            <div class="progress" style="height: 5px; background: #e2e8f0;">
                <div class="progress-bar bg-primary" style="width: {{ $userPercent }}%;"></div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="clinic-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">إجمالي زيارات الشاشات</span>
                <i class="bi bi-eye text-info fs-5"></i>
            </div>
            <h4 class="fw-bold text-info mb-1">{{ number_format($analytics['total_visits']) }}</h4>
            <span class="text-muted small">تصفح الشاشات والملفات الطبية</span>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="clinic-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">سجل المرضى والمواعيد</span>
                <i class="bi bi-calendar-check text-success fs-5"></i>
            </div>
            <h4 class="fw-bold text-success mb-1">{{ $analytics['kpis']['patients_total'] }} <small class="fs-6 text-muted fw-normal">مريض</small></h4>
            <span class="text-muted small">منهم {{ $analytics['kpis']['patients_this_month'] }} جدد هذا الشهر</span>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="clinic-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">صافي دخل العيادة</span>
                <i class="bi bi-cash-coin text-warning fs-5"></i>
            </div>
            <h4 class="fw-bold text-warning mb-1">{{ number_format($analytics['kpis']['net_profit'], 2) }} <small class="fs-6 text-muted">ر.س</small></h4>
            <span class="text-muted small">إيراد: {{ number_format($analytics['kpis']['total_revenue']) }} | مصروف: {{ number_format($analytics['kpis']['total_expenses']) }}</span>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- 1. مؤشر حركة الدخول والنشاط اليومي (Daily Logins & Peak Hours) -->
<!-- ============================================================== -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="clinic-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-graph-up text-primary me-2"></i> مؤشر حركة الدخول والنشاط اليومي (آخر 14 يوماً)
                    </h5>
                    <p class="text-muted small mb-0">مقارنة وتيرة تسجيلات دخول الكادر بالعمليات المنجزة يومياً.</p>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill small">تحديث لحظي</span>
            </div>
            <div style="height: 280px;">
                <canvas id="clinicDailyActivityChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="clinic-card p-4 h-100">
            <div class="mb-3">
                <h5 class="fw-bold text-dark mb-1">
                    <i class="bi bi-clock-history text-warning me-2"></i> ساعات الذروة اليومية (Peak Hours)
                </h5>
                <p class="text-muted small mb-0">توزيع كثافة النشاط والعمليات على ساعات العمل اليوم.</p>
            </div>
            <div style="height: 280px;">
                <canvas id="clinicHourlyPeakChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- 2. أكثر الصفحات التي يتم زيارتها داخل العيادة (Top Visited Pages) -->
<!-- ============================================================== -->
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="clinic-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-compass text-info me-2"></i> أكثر الصفحات والشاشات زيارة داخل العيادة
                    </h5>
                    <p class="text-muted small mb-0">ترتيب الشاشات الأكثر ارتياداً واستخداماً من قِبل الأطباء وموظفي الاستقبال والمحاسبة.</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>الشاشة / الصفحة</th>
                            <th>المسار (Path)</th>
                            <th class="text-center">عدد الزيارات</th>
                            <th style="width: 140px;">النسبة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($analytics['top_pages'] as $page)
                        <tr>
                            <td>
                                <span class="fw-semibold text-dark">
                                    <i class="bi bi-file-earmark-text text-primary me-1"></i> {{ $page->page_title ?? 'شاشة النظام' }}
                                </span>
                            </td>
                            <td>
                                <code class="text-muted small">{{ $page->url_path }}</code>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light border text-primary fw-bold px-2 py-1">
                                    {{ number_format($page->visit_count) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px; background-color: #f1f5f9;">
                                        <div class="progress-bar bg-info rounded-pill" style="width: {{ $page->percentage }}%;"></div>
                                    </div>
                                    <span class="small text-muted" style="font-size:0.75rem;">{{ $page->percentage }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                لم يتم تسجيل زيارات كافية حتى الآن.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Action Types Breakdown & Operational Indicators -->
    <div class="col-lg-5">
        <div class="clinic-card p-4 h-100">
            <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-pie-chart text-primary me-2"></i> نوعية العمليات التشغيلية والسريرية
            </h5>

            <!-- Medical KPIs List -->
            <div class="list-group list-group-flush mb-4">
                <div class="list-group-item bg-transparent text-dark border-light d-flex justify-content-between align-items-center px-0 py-2">
                    <span class="small text-muted"><i class="bi bi-check2-circle text-success me-2"></i> نسبة إنجاز المواعيد:</span>
                    <span class="fw-bold text-success">{{ $analytics['kpis']['appointment_completion_rate'] }}% ({{ $analytics['kpis']['appointments_completed'] }} مكتمل)</span>
                </div>
                <div class="list-group-item bg-transparent text-dark border-light d-flex justify-content-between align-items-center px-0 py-2">
                    <span class="small text-muted"><i class="bi bi-x-circle text-danger me-2"></i> المواعيد الملغاة:</span>
                    <span class="fw-bold text-danger">{{ $analytics['kpis']['appointments_cancelled'] }}</span>
                </div>
                <div class="list-group-item bg-transparent text-dark border-light d-flex justify-content-between align-items-center px-0 py-2">
                    <span class="small text-muted"><i class="bi bi-diagram-2 text-info me-2"></i> إجراءات مخطط الأسنان:</span>
                    <span class="fw-bold text-info">{{ $analytics['kpis']['dental_charts_count'] }} سن</span>
                </div>
                <div class="list-group-item bg-transparent text-dark border-light d-flex justify-content-between align-items-center px-0 py-2">
                    <span class="small text-muted"><i class="bi bi-box-seam text-warning me-2"></i> طلبيات المعامل والتركيبات:</span>
                    <span class="fw-bold text-warning">{{ $analytics['kpis']['lab_orders_count'] }} طلبية</span>
                </div>
            </div>

            <div style="height: 180px;">
                <canvas id="clinicActionsDoughnutChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- 3. مستخدمو العيادة ونشاطهم (Users & Productivity Breakdown) -->
<!-- ============================================================== -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="clinic-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-people-fill text-warning me-2"></i> مستخدمو العيادة ومعدل نشاطهم وإنتاجيتهم
                    </h5>
                    <p class="text-muted small mb-0">بيانات كادر العيادة، آخر ظهور، وعدد العمليات المسجلة اليوم وإجمالياً.</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>المستخدم</th>
                            <th>الدور</th>
                            <th>آخر تسجيل دخول</th>
                            <th class="text-center">عمليات اليوم</th>
                            <th class="text-center">إجمالي العمليات</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($analytics['users'] as $u)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 36px; height: 36px; background: #0284c7;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">
                                            {{ $u->name }}
                                            @if($u->is_primary_owner)
                                                <span class="badge bg-warning text-dark border border-warning shadow-sm ms-1" style="font-size: 0.65rem;">
                                                    <i class="bi bi-crown-fill me-1"></i> المسؤول الرئيسي
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-muted small font-monospace">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1 rounded-pill small">
                                    {{ $u->getRoleLabelsString() }}
                                </span>
                            </td>
                            <td class="small">
                                @if($u->last_login_at)
                                    <div class="text-dark">{{ $u->last_login_at->diffForHumans() }}</div>
                                    <div class="text-muted font-monospace" style="font-size:0.7rem;">IP: {{ $u->last_login_ip ?? 'محلي' }}</div>
                                @else
                                    <span class="text-muted fst-italic">لم يدخل بعد</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill">
                                    {{ $u->today_actions_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 rounded-pill">
                                    {{ $u->total_actions_count }}
                                </span>
                            </td>
                            <td>
                                @if($u->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">نشط</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1 small">معطل</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">لا يوجد مستخدمون في هذه العيادة.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Live Activity Timeline -->
    <div class="col-lg-4">
        <div class="clinic-card p-4 h-100">
            <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-activity text-danger me-2"></i> سجل العمليات المباشر (Activity Feed)
            </h5>

            <div class="timeline overflow-y-auto" style="max-height: 380px;">
                @forelse($analytics['recent_activities'] as $act)
                <div class="d-flex gap-3 mb-3 pb-2 border-bottom">
                    <div class="mt-1">
                        @if($act->action_type === 'login')
                            <span class="badge bg-success-subtle text-success p-2 rounded-circle"><i class="bi bi-box-arrow-in-right"></i></span>
                        @elseif($act->action_type === 'create')
                            <span class="badge bg-primary-subtle text-primary p-2 rounded-circle"><i class="bi bi-plus-lg"></i></span>
                        @elseif($act->action_type === 'update')
                            <span class="badge bg-warning-subtle text-warning p-2 rounded-circle"><i class="bi bi-pencil-fill"></i></span>
                        @elseif($act->action_type === 'delete')
                            <span class="badge bg-danger-subtle text-danger p-2 rounded-circle"><i class="bi bi-trash-fill"></i></span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary p-2 rounded-circle"><i class="bi bi-lightning-charge"></i></span>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-dark small fw-semibold">{{ $act->description }}</div>
                        <div class="d-flex justify-content-between align-items-center text-muted" style="font-size:0.75rem;">
                            <span>{{ $act->user ? $act->user->name : 'النظام' }}</span>
                            <span>{{ $act->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                    لم يتم تسجيل حركات نشاط حديثة.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // 1. Daily Activity Line Chart
        const dailyCanvas = document.getElementById('clinicDailyActivityChart');
        if (dailyCanvas) {
            new Chart(dailyCanvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($analytics['daily_activity_chart']['dates']) !!},
                    datasets: [
                        {
                            label: 'عمليات وإجراءات الطاقم',
                            data: {!! json_encode($analytics['daily_activity_chart']['actions']) !!},
                            borderColor: '#0284c7',
                            backgroundColor: 'rgba(2, 132, 199, 0.1)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2,
                        },
                        {
                            label: 'مرات تسجيل الدخول',
                            data: {!! json_encode($analytics['daily_activity_chart']['logins']) !!},
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            fill: false,
                            tension: 0.35,
                            borderWidth: 2,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { font: { family: 'IBM Plex Sans Arabic' } }
                        }
                    },
                    scales: {
                        x: { ticks: { font: { size: 10 } } },
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    }
                }
            });
        }

        // 2. Hourly Peak Chart
        const hourlyCanvas = document.getElementById('clinicHourlyPeakChart');
        if (hourlyCanvas) {
            new Chart(hourlyCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($analytics['hourly_chart']['labels']) !!},
                    datasets: [{
                        label: 'نشاط اليوم بالساعة',
                        data: {!! json_encode($analytics['hourly_chart']['data']) !!},
                        backgroundColor: '#f59e0b',
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { ticks: { font: { size: 9 }, maxRotation: 45 } },
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    }
                }
            });
        }

        // 3. Actions Doughnut Chart
        const doughnutCanvas = document.getElementById('clinicActionsDoughnutChart');
        if (doughnutCanvas) {
            const actionsData = {!! json_encode($analytics['action_types_breakdown']) !!};
            const labels = Object.keys(actionsData).map(k => {
                const map = { login: 'دخول', create: 'إنشاء وإضافة', update: 'تعديل وتحديث', delete: 'حذف', view: 'عرض' };
                return map[k] || k;
            });
            const values = Object.values(actionsData);

            new Chart(doughnutCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: labels.length > 0 ? labels : ['لا توجد حركات'],
                    datasets: [{
                        data: values.length > 0 ? values : [1],
                        backgroundColor: ['#0284c7', '#10b981', '#f59e0b', '#ef4444', '#6366f1', '#64748b'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: { font: { family: 'IBM Plex Sans Arabic', size: 11 } }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
