@extends('appmanager.layout')

@section('title', 'إحصائيات وتحليلات عيادة: ' . $clinic->name . ' | App Manager')

@section('content')

<!-- Back navigation & Clinic Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="{{ route('appmanager.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2">
                <i class="bi bi-arrow-right"></i> كل العيادات
            </a>
            <h3 class="fw-bold mb-0 text-white">إحصائيات وتحليلات النشاط: {{ $clinic->name }} 📊</h3>
            @if($clinic->status === 'active')
                <span class="badge saas-badge-active rounded-pill px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i> نشط</span>
            @else
                <span class="badge saas-badge-suspended rounded-pill px-2 py-1"><i class="bi bi-pause-circle-fill me-1"></i> مجمد</span>
            @endif
        </div>
        <p class="text-secondary small mb-0">لوحة المراقبة الشاملة لمؤشرات الدخول، الصفحات الأكثر زيارة، إنتاجية الموظفين، والبيانات التشغيلية والمالية.</p>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('clinic.dashboard') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
            <i class="bi bi-box-arrow-up-right me-1"></i> فتح بوابة العيادة
        </a>
    </div>
</div>

<!-- Quick High-Level KPIs -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="saas-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary small">سعة المستخدمين</span>
                <i class="bi bi-people text-info fs-5"></i>
            </div>
            <h4 class="fw-bold text-white mb-1">{{ $analytics['kpis']['active_users'] }} <span class="fs-6 text-secondary fw-normal">/ {{ $analytics['kpis']['max_users'] }} مسموح</span></h4>
            @php
                $userPercent = $analytics['kpis']['max_users'] > 0 ? min(100, round(($analytics['kpis']['active_users'] / $analytics['kpis']['max_users']) * 100)) : 0;
            @endphp
            <div class="progress" style="height: 5px; background: #1f2937;">
                <div class="progress-bar bg-info" style="width: {{ $userPercent }}%;"></div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="saas-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary small">إجمالي زيارات الشاشات</span>
                <i class="bi bi-eye text-primary fs-5"></i>
            </div>
            <h4 class="fw-bold text-primary mb-1">{{ number_format($analytics['total_visits']) }}</h4>
            <span class="text-secondary small">تصفح الشاشات والملفات</span>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="saas-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary small">سجل المرضى والمواعيد</span>
                <i class="bi bi-calendar-check text-success fs-5"></i>
            </div>
            <h4 class="fw-bold text-success mb-1">{{ $analytics['kpis']['patients_total'] }} <small class="fs-6 text-secondary fw-normal">مريض</small></h4>
            <span class="text-secondary small">منهم {{ $analytics['kpis']['patients_this_month'] }} جدد هذا الشهر</span>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="saas-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary small">صافي دخل العيادة</span>
                <i class="bi bi-cash-coin text-warning fs-5"></i>
            </div>
            <h4 class="fw-bold text-warning mb-1">{{ number_format($analytics['kpis']['net_profit'], 2) }} <small class="fs-6 text-secondary">ر.س</small></h4>
            <span class="text-secondary small">إيراد: {{ number_format($analytics['kpis']['total_revenue']) }} | مصروف: {{ number_format($analytics['kpis']['total_expenses']) }}</span>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- 1. مؤشر حركة الدخول والنشاط اليومي (Daily Logins & Peak Hours) -->
<!-- ============================================================== -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="saas-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-white mb-1">
                        <i class="bi bi-graph-up text-primary me-2"></i> مؤشر حركة الدخول والنشاط اليومي (آخر 14 يوماً)
                    </h5>
                    <p class="text-secondary small mb-0">مقارنة وتيرة تسجيلات دخول الطاقم بالعمليات المنجزة يومياً.</p>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill small">تحديث لحظي</span>
            </div>
            <div style="height: 280px;">
                <canvas id="dailyActivityChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="saas-card p-4 h-100">
            <div class="mb-3">
                <h5 class="fw-bold text-white mb-1">
                    <i class="bi bi-clock-history text-warning me-2"></i> ساعات الذروة اليومية (Peak Hours)
                </h5>
                <p class="text-secondary small mb-0">توزيع كثافة النشاط والعمليات على ساعات اليوم.</p>
            </div>
            <div style="height: 280px;">
                <canvas id="hourlyPeakChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- 2. أكثر الصفحات التي يتم زيارتها داخل العيادة (Top Visited Pages) -->
<!-- ============================================================== -->
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="saas-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-white mb-1">
                        <i class="bi bi-compass text-info me-2"></i> أكثر الصفحات والشاشات زيارة داخل العيادة
                    </h5>
                    <p class="text-secondary small mb-0">ترتيب الشاشات الأكثر ارتياداً واستخداماً من قبل الطاقم الطبي والإداري.</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-dark-custom align-middle mb-0">
                    <thead>
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
                                <span class="fw-semibold text-white">
                                    <i class="bi bi-file-earmark-text text-info me-1"></i> {{ $page->page_title ?? 'شاشة النظام' }}
                                </span>
                            </td>
                            <td>
                                <code class="text-secondary small">{{ $page->url_path }}</code>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-dark border border-secondary px-2 py-1 text-info fw-bold">
                                    {{ number_format($page->visit_count) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px; background-color: #1e293b;">
                                        <div class="progress-bar bg-info rounded-pill" style="width: {{ $page->percentage }}%;"></div>
                                    </div>
                                    <span class="small text-secondary" style="font-size:0.75rem;">{{ $page->percentage }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-secondary">
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
        <div class="saas-card p-4 h-100">
            <h5 class="fw-bold text-white mb-3">
                <i class="bi bi-pie-chart text-accent me-2" style="color: var(--app-accent);"></i> نوعية العمليات التشغيلية والطبية
            </h5>

            <!-- Medical KPIs List -->
            <div class="list-group list-group-flush mb-4">
                <div class="list-group-item bg-transparent text-white border-dark d-flex justify-content-between align-items-center px-0 py-2">
                    <span class="small text-secondary"><i class="bi bi-check2-circle text-success me-2"></i> معدل إتمام المواعيد:</span>
                    <span class="fw-bold text-success">{{ $analytics['kpis']['appointment_completion_rate'] }}% ({{ $analytics['kpis']['appointments_completed'] }} مكتمل)</span>
                </div>
                <div class="list-group-item bg-transparent text-white border-dark d-flex justify-content-between align-items-center px-0 py-2">
                    <span class="small text-secondary"><i class="bi bi-x-circle text-danger me-2"></i> المواعيد الملغاة:</span>
                    <span class="fw-bold text-danger">{{ $analytics['kpis']['appointments_cancelled'] }}</span>
                </div>
                <div class="list-group-item bg-transparent text-white border-dark d-flex justify-content-between align-items-center px-0 py-2">
                    <span class="small text-secondary"><i class="bi bi-diagram-2 text-info me-2"></i> مخططات الأسنان المسجلة:</span>
                    <span class="fw-bold text-info">{{ $analytics['kpis']['dental_charts_count'] }} سن</span>
                </div>
                <div class="list-group-item bg-transparent text-white border-dark d-flex justify-content-between align-items-center px-0 py-2">
                    <span class="small text-secondary"><i class="bi bi-box-seam text-warning me-2"></i> طلبيات المعامل والتركيبات:</span>
                    <span class="fw-bold text-warning">{{ $analytics['kpis']['lab_orders_count'] }} طلبية</span>
                </div>
            </div>

            <div style="height: 180px;">
                <canvas id="actionsDoughnutChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- 3. مستخدمو العيادة ونشاطهم (Users & Productivity Breakdown) -->
<!-- ============================================================== -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="saas-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-white mb-1">
                        <i class="bi bi-people-fill text-warning me-2"></i> مستخدمو العيادة ونشاطهم ومعدل إنتاجيتهم
                    </h5>
                    <p class="text-secondary small mb-0">بيانات كادر العيادة، آخر ظهور، وعدد العمليات المسجلة اليوم وإجمالياً.</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-dark-custom align-middle mb-0">
                    <thead>
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
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 36px; height: 36px; background: #1e293b; color: white;">
                                        <i class="bi bi-person-fill text-info"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-white">
                                            {{ $u->name }}
                                            @if($u->is_primary_owner)
                                                <span class="badge bg-warning text-dark border border-warning shadow-sm ms-1" style="font-size: 0.65rem;">
                                                    <i class="bi bi-crown-fill me-1"></i> المسؤول الرئيسي
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-secondary small font-monospace">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-dark border border-secondary text-light px-2 py-1 rounded-pill small">
                                    {{ $u->getRoleLabelsString() }}
                                </span>
                            </td>
                            <td class="small">
                                @if($u->last_login_at)
                                    <div class="text-light">{{ $u->last_login_at->diffForHumans() }}</div>
                                    <div class="text-secondary font-monospace" style="font-size:0.7rem;">IP: {{ $u->last_login_ip ?? 'محلي' }}</div>
                                @else
                                    <span class="text-secondary fst-italic">لم يدخل بعد</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill">
                                    {{ $u->today_actions_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-light border border-secondary px-2 py-1 rounded-pill">
                                    {{ $u->total_actions_count }}
                                </span>
                            </td>
                            <td>
                                @if($u->is_active)
                                    <span class="badge saas-badge-active rounded-pill px-2 py-1 small">نشط</span>
                                @else
                                    <span class="badge saas-badge-suspended rounded-pill px-2 py-1 small">معطل</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-secondary">لا يوجد مستخدمون في هذه العيادة.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Live Activity Timeline -->
    <div class="col-lg-4">
        <div class="saas-card p-4 h-100">
            <h5 class="fw-bold text-white mb-3">
                <i class="bi bi-activity text-danger me-2"></i> سجل العمليات المباشر (Activity Feed)
            </h5>

            <div class="timeline overflow-y-auto" style="max-height: 380px;">
                @forelse($analytics['recent_activities'] as $act)
                <div class="d-flex gap-3 mb-3 pb-2 border-bottom border-dark">
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
                        <div class="text-white small fw-semibold">{{ $act->description }}</div>
                        <div class="d-flex justify-content-between align-items-center text-secondary" style="font-size:0.75rem;">
                            <span>{{ $act->user ? $act->user->name : 'النظام' }}</span>
                            <span>{{ $act->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-secondary py-5">
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
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // 1. Daily Activity Line Chart
        const dailyCtx = document.getElementById('dailyActivityChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($analytics['daily_activity_chart']['dates']) !!},
                datasets: [
                    {
                        label: 'عمليات وإجراءات الطاقم',
                        data: {!! json_encode($analytics['daily_activity_chart']['actions']) !!},
                        borderColor: '#38bdf8',
                        backgroundColor: 'rgba(56, 189, 248, 0.15)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2,
                    },
                    {
                        label: 'مرات تسجيل الدخول',
                        data: {!! json_encode($analytics['daily_activity_chart']['logins']) !!},
                        borderColor: '#34d399',
                        backgroundColor: 'rgba(52, 211, 153, 0.1)',
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
                        labels: { color: '#9ca3af', font: { family: 'IBM Plex Sans Arabic' } }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#6b7280', font: { size: 10 } },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#6b7280', stepSize: 1 },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    }
                }
            }
        });

        // 2. Hourly Peak Chart
        const hourlyCtx = document.getElementById('hourlyPeakChart').getContext('2d');
        new Chart(hourlyCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($analytics['hourly_chart']['labels']) !!},
                datasets: [{
                    label: 'نشاط اليوم بالساعة',
                    data: {!! json_encode($analytics['hourly_chart']['data']) !!},
                    backgroundColor: '#fbbf24',
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        ticks: { color: '#6b7280', font: { size: 9 }, maxRotation: 45 },
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#6b7280', stepSize: 1 },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    }
                }
            }
        });

        // 3. Actions Doughnut Chart
        const doughnutCtx = document.getElementById('actionsDoughnutChart').getContext('2d');
        const actionsData = {!! json_encode($analytics['action_types_breakdown']) !!};
        const labels = Object.keys(actionsData).map(k => {
            const map = { login: 'دخول', create: 'إنشاء وإضافة', update: 'تعديل وتحديث', delete: 'حذف', view: 'عرض' };
            return map[k] || k;
        });
        const values = Object.values(actionsData);

        new Chart(doughnutCtx, {
            type: 'doughnut',
            data: {
                labels: labels.length > 0 ? labels : ['لا توجد حركات'],
                datasets: [{
                    data: values.length > 0 ? values : [1],
                    backgroundColor: ['#38bdf8', '#34d399', '#fbbf24', '#f87171', '#818cf8', '#64748b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { color: '#9ca3af', font: { family: 'IBM Plex Sans Arabic', size: 11 } }
                    }
                }
            }
        });
    });
</script>
@endpush

