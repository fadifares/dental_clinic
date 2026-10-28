@extends('appmanager.layout')

@section('title', 'لوحة تحكم مدير المنصة - إدارة العيادات والمشتركين | Dental SaaS')

@section('content')

<!-- Header & Quick Action -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-white">لوحة تحكم مدير التطبيق (App Manager) 🏢</h2>
        <p class="text-secondary small mb-0">إدارة العيادات المشتركة، تحديد حصص المستخدمين (User Quotas)، ومراقبة مؤشرات الاستخدام والأمان.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-saas-primary px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#newClinicModal">
            <i class="bi bi-plus-circle-fill me-1"></i> إضافة عيادة جديدة
        </button>
    </div>
</div>

<!-- Platform KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="saas-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-secondary small d-block mb-1">إجمالي العيادات المسجلة</span>
                <h3 class="fw-bold text-white mb-0">{{ $stats['total_clinics'] }}</h3>
                <span class="text-success small"><i class="bi bi-check-circle me-1"></i> نشطة: {{ $stats['active_clinics'] }}</span>
            </div>
            <div class="rounded-3 p-3" style="background: rgba(56, 189, 248, 0.1); color: var(--app-primary);">
                <i class="bi bi-buildings-fill fs-3"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="saas-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-secondary small d-block mb-1">إجمالي مستخدمي المنصة</span>
                <h3 class="fw-bold text-info mb-0">{{ $stats['total_users'] }}</h3>
                <span class="text-secondary small">طواقم طبية وإدارية</span>
            </div>
            <div class="rounded-3 p-3" style="background: rgba(129, 140, 248, 0.1); color: var(--app-accent);">
                <i class="bi bi-people-fill fs-3"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="saas-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-secondary small d-block mb-1">إجمالي المرضى المسجلين</span>
                <h3 class="fw-bold text-success mb-0">{{ number_format($stats['total_patients']) }}</h3>
                <span class="text-secondary small">المواعيد: {{ number_format($stats['total_appointments']) }}</span>
            </div>
            <div class="rounded-3 p-3" style="background: rgba(52, 211, 153, 0.1); color: var(--app-success);">
                <i class="bi bi-person-lines-fill fs-3"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="saas-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-secondary small d-block mb-1">إجمالي تحصيلات المنصة</span>
                <h3 class="fw-bold text-warning mb-0">{{ number_format($stats['total_revenue'], 2) }} <small class="fs-6 text-secondary">ر.س</small></h3>
                <span class="text-secondary small">عبر جميع العيادات</span>
            </div>
            <div class="rounded-3 p-3" style="background: rgba(251, 191, 36, 0.1); color: var(--app-warning);">
                <i class="bi bi-cash-stack fs-3"></i>
            </div>
        </div>
    </div>
</div>

<!-- Clinics Table Card -->
<div class="saas-card overflow-hidden">
    <div class="p-3 border-bottom border-dark d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-white mb-0">
            <i class="bi bi-hospital text-info me-2"></i> قائمة العيادات والمراكز الطبية ({{ $clinics->total() }})
        </h5>
        <span class="text-secondary small">يتم تعيين الحد الأقصى للمستخدمين لكل عيادة عبر زر التعديل</span>
    </div>

    <div class="table-responsive">
        <table class="table table-dark-custom align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">العيادة / المركز</th>
                    <th>المسؤول الرئيسي (Owner)</th>
                    <th>سعة المستخدمين (Quota)</th>
                    <th>الحالة</th>
                    <th>تاريخ التسجيل</th>
                    <th class="text-end pe-4">الإجراءات والإحصائيات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clinics as $c)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 42px; height: 42px; background: {{ $c->id === 1 ? '#0284c7' : '#334155' }}; color: white;">
                                <i class="bi {{ $c->id === 1 ? 'bi-star-fill text-warning' : 'bi-hospital' }}"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-white fs-6">
                                    {{ $c->name }}
                                    @if($c->id === 1)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1" style="font-size: 0.65rem;">العيادة الأولى الأساسية</span>
                                    @endif
                                </div>
                                <div class="text-secondary small font-monospace">
                                    slug: {{ $c->slug }} @if($c->phone) • {{ $c->phone }} @endif
                                </div>
                            </div>
                        </div>
                    </td>

                    <td>
                        @if($c->primaryOwner)
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-person-badge text-warning fs-5"></i>
                                <div>
                                    <div class="text-white fw-semibold">{{ $c->primaryOwner->name }}</div>
                                    <div class="text-secondary small font-monospace">{{ $c->primaryOwner->email }}</div>
                                </div>
                            </div>
                        @else
                            <span class="text-secondary small fst-italic">غير محدد</span>
                        @endif
                    </td>

                    <td style="min-width: 170px;">
                        <div class="d-flex justify-content-between align-items-center small mb-1">
                            <span class="text-white fw-bold">{{ $c->active_users_count }} مستخدم نشط</span>
                            <span class="text-secondary">الحد الأقصى: {{ $c->max_users }}</span>
                        </div>
                        @php
                            $quotaPercent = $c->max_users > 0 ? min(100, round(($c->active_users_count / $c->max_users) * 100)) : 0;
                            $barColor = $quotaPercent >= 100 ? '#f87171' : ($quotaPercent >= 80 ? '#fbbf24' : '#34d399');
                        @endphp
                        <div class="progress" style="height: 6px; background-color: #1e293b;">
                            <div class="progress-bar rounded-pill" role="progressbar" style="width: {{ $quotaPercent }}%; background-color: {{ $barColor }};" aria-valuenow="{{ $quotaPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </td>

                    <td>
                        @if($c->status === 'active')
                            <span class="badge saas-badge-active px-3 py-1 rounded-pill">
                                <i class="bi bi-check-circle-fill me-1"></i> نشط
                            </span>
                        @else
                            <span class="badge saas-badge-suspended px-3 py-1 rounded-pill">
                                <i class="bi bi-pause-circle-fill me-1"></i> مجمد
                            </span>
                        @endif
                    </td>

                    <td class="text-secondary small">
                        {{ $c->created_at->format('Y-m-d') }}
                    </td>

                    <td class="text-end pe-4">
                        <div class="d-inline-flex gap-2">
                            <!-- Statistics Dashboard Button -->
                            <a href="{{ route('appmanager.clinic.stats', $c) }}" class="btn btn-sm btn-outline-info rounded-pill px-3" title="عرض صفحة الإحصائيات الشاملة للعيادة">
                                <i class="bi bi-graph-up-arrow me-1"></i> الإحصائيات
                            </a>

                            <!-- Edit Clinic & Max Users Button -->
                            <button class="btn btn-sm btn-outline-light rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#editClinicModal{{ $c->id }}" title="تعديل بيانات العيادة وحد المستخدمين">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <!-- Delete button (Disabled for Clinic #1) -->
                            @if($c->id !== 1)
                            <form action="{{ route('appmanager.clinics.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('تحذير: هل أنت متأكد من حذف هذه العيادة نهائياً وكافة بياناتها؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="حذف العيادة">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>

                <!-- Edit Clinic Modal -->
                <div class="modal fade" id="editClinicModal{{ $c->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content modal-content-dark border border-secondary shadow-lg">
                            <form action="{{ route('appmanager.clinics.update', $c) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header border-bottom border-dark">
                                    <h5 class="modal-title fw-bold text-white">
                                        <i class="bi bi-sliders text-info me-2"></i> تعديل بيانات العيادة: {{ $c->name }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <label class="form-label small text-secondary fw-semibold">اسم العيادة / المركز <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control form-control-dark" value="{{ old('name', $c->name) }}" required>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label small text-secondary fw-semibold">الحد الأقصى للمستخدمين (Max Users) <span class="text-danger">*</span></label>
                                            <input type="number" name="max_users" class="form-control form-control-dark text-center fw-bold text-info" value="{{ old('max_users', $c->max_users) }}" min="1" max="500" required>
                                            <div class="text-secondary small mt-1" style="font-size:0.75rem;">المستخدمون الحاليون: {{ $c->active_users_count }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-secondary fw-semibold">حالة الحساب <span class="text-danger">*</span></label>
                                            <select name="status" class="form-select form-select-dark" required>
                                                <option value="active" {{ $c->status === 'active' ? 'selected' : '' }}>نشط (Active)</option>
                                                <option value="suspended" {{ $c->status === 'suspended' ? 'selected' : '' }}>مجمد (Suspended)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label small text-secondary fw-semibold">رقم الهاتف</label>
                                            <input type="text" name="phone" class="form-control form-control-dark font-monospace" value="{{ old('phone', $c->phone) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-secondary fw-semibold">البريد الإلكتروني</label>
                                            <input type="email" name="email" class="form-control form-control-dark font-monospace" value="{{ old('email', $c->email) }}">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small text-secondary fw-semibold">العنوان والموقع</label>
                                        <input type="text" name="address" class="form-control form-control-dark" value="{{ old('address', $c->address) }}">
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label small text-secondary fw-semibold">ملاحظات داخلية للمدير</label>
                                        <textarea name="notes" class="form-control form-control-dark" rows="2">{{ old('notes', $c->notes) }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-top border-dark">
                                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">إلغاء</button>
                                    <button type="submit" class="btn btn-saas-primary rounded-pill px-4">
                                        <i class="bi bi-check-lg me-1"></i> حفظ التعديلات
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-secondary">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        لا توجد عيادات مسجلة حالياً.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($clinics->hasPages())
    <div class="p-3 border-top border-dark d-flex justify-content-center">
        {{ $clinics->links() }}
    </div>
    @endif
</div>

<!-- New Clinic Modal -->
<div class="modal fade" id="newClinicModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-content-dark border border-secondary shadow-lg">
            <form action="{{ route('appmanager.clinics.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom border-dark">
                    <h5 class="modal-title fw-bold text-white">
                        <i class="bi bi-hospital-fill text-info me-2"></i> إضافة عيادة جديدة وتعيين مسؤولها الرئيسي
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <h6 class="fw-bold text-info border-bottom border-dark pb-2 mb-3">
                        <i class="bi bi-building me-1"></i> بيانات العيادة الأساسية
                    </h6>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-semibold">اسم العيادة / المركز <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-dark" placeholder="مثال: مجمع ابتسامة النخبة لطب الأسنان" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-semibold">المعرف اللاتيني (Slug - اختياري)</label>
                            <input type="text" name="slug" class="form-control form-control-dark font-monospace" placeholder="elite-dental">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-semibold">الحد الأقصى للمستخدمين <span class="text-danger">*</span></label>
                            <input type="number" name="max_users" class="form-control form-control-dark text-center fw-bold text-info" value="5" min="1" max="500" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-semibold">رقم الهاتف</label>
                            <input type="text" name="phone" class="form-control form-control-dark font-monospace" placeholder="0500000000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-semibold">الحالة <span class="text-danger">*</span></label>
                            <select name="status" class="form-select form-select-dark" required>
                                <option value="active" selected>نشط (Active)</option>
                                <option value="suspended">مجمد (Suspended)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small text-secondary fw-semibold">العنوان</label>
                        <input type="text" name="address" class="form-control form-control-dark" placeholder="المدينة، الحي، الشارع">
                    </div>

                    <h6 class="fw-bold text-warning border-bottom border-dark pb-2 mb-3">
                        <i class="bi bi-crown-fill me-1"></i> حساب المسؤول الرئيسي للعيادة (لا يمكن حذفه ويملك الصلاحيات الكاملة)
                    </h6>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-semibold">اسم المسؤول الرئيسي <span class="text-danger">*</span></label>
                            <input type="text" name="owner_name" class="form-control form-control-dark" placeholder="مثال: د. ماجد السعيد" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-semibold">البريد الإلكتروني للدخول <span class="text-danger">*</span></label>
                            <input type="email" name="owner_email" class="form-control form-control-dark font-monospace" placeholder="owner@clinic.com" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-secondary fw-semibold">كلمة المرور الأولية (8 أحرف على الأقل) <span class="text-danger">*</span></label>
                        <input type="password" name="owner_password" class="form-control form-control-dark" placeholder="••••••••" required>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small text-secondary fw-semibold">ملاحظات داخلية للمدير (اختياري)</label>
                        <textarea name="notes" class="form-control form-control-dark" rows="2" placeholder="ملاحظات حول الباقة والاشتراك..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top border-dark">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-saas-primary rounded-pill px-4">
                        <i class="bi bi-check-circle-fill me-1"></i> إنشاء العيادة والمسؤول
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

