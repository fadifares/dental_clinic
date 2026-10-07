@extends('layouts.clinic')

@section('title', 'إدارة المستخدمين والصلاحيات الأمنية | Dental Pro ERP')

@section('content')

<!-- Header & Quick Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">إدارة المستخدمين وصلاحيات الوصول (RBAC & Security) 🛡️</h3>
        <p class="text-muted small mb-0">التحكم بكادر العمل، تعيين الصلاحيات الدقيقة، تجميد أو تنشيط الحسابات، ومراقبة معايير الأمان والحماية.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#newUserModal">
            <i class="bi bi-person-plus-fill me-1"></i> إضافة موظف / مستخدم جديد
        </button>
    </div>
</div>

<!-- Alert Notifications -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-shield-x fs-5 me-2 text-danger"></i>
    <div>{{ session('error') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
    <ul class="mb-0 small">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Stats Bar -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">إجمالي الحسابات</span>
                <h3 class="fw-bold text-dark mb-0">{{ $stats['total'] }}</h3>
            </div>
            <div class="clinic-stat-icon bg-primary-subtle text-primary">
                <i class="bi bi-people-fill fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between border-info-subtle">
            <div>
                <span class="text-muted small d-block mb-1">صلاحيات متعددة (Multi-Group)</span>
                <h3 class="fw-bold text-info mb-0">{{ $stats['multi_role'] }}</h3>
                <span class="text-muted" style="font-size: 0.75rem;">مثل: محاسب + استقبال</span>
            </div>
            <div class="clinic-stat-icon bg-info-subtle text-info">
                <i class="bi bi-layers-fill fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">الاستقبال والمحاسبة</span>
                <h3 class="fw-bold text-warning mb-0">{{ $stats['receptionists'] + $stats['accountants'] }}</h3>
                <span class="text-muted" style="font-size: 0.75rem;">الأطباء: {{ $stats['doctors'] }}</span>
            </div>
            <div class="clinic-stat-icon bg-warning-subtle text-warning">
                <i class="bi bi-headset fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">الحسابات النشطة</span>
                <h3 class="fw-bold text-success mb-0">{{ $stats['active'] }} <span class="fs-6 fw-normal text-muted">/ {{ $stats['total'] }}</span></h3>
                <span class="text-muted" style="font-size: 0.75rem;">معطلة: {{ $stats['inactive'] }}</span>
            </div>
            <div class="clinic-stat-icon bg-success-subtle text-success">
                <i class="bi bi-shield-check fs-4"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabs: Users List & Permissions Matrix & Security Evaluation -->
<ul class="nav nav-pills mb-4 bg-white p-2 rounded-4 shadow-sm border" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active rounded-pill fw-bold" id="pills-users-tab" data-bs-toggle="pill" data-bs-target="#pills-users" type="button" role="tab">
            <i class="bi bi-person-lines-fill me-1"></i> قائمة الموظفين والمستخدمين ({{ $users->count() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill fw-bold" id="pills-matrix-tab" data-bs-toggle="pill" data-bs-target="#pills-matrix" type="button" role="tab">
            <i class="bi bi-diagram-3-fill me-1"></i> مصفوفة الصلاحيات (Role-Based Matrix)
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill fw-bold" id="pills-audit-tab" data-bs-toggle="pill" data-bs-target="#pills-audit" type="button" role="tab">
            <i class="bi bi-shield-lock-fill me-1 text-danger"></i> تقرير التقييم الأمني والامتثال (Security Audit)
        </button>
    </li>
</ul>

<div class="tab-content" id="pills-tabContent">
    <!-- Tab 1: Users List -->
    <div class="tab-pane fade show active" id="pills-users" role="tabpanel">
        <div class="clinic-card">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">المستخدم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الدور الوظيفي / الصلاحية</th>
                            <th>حالة الحساب</th>
                            <th>تاريخ الإنشاء</th>
                            <th class="text-end pe-4">الإجراءات والتحكم</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
                                         style="width: 40px; height: 40px; background: {{ $u->role === \App\Enums\UserRole::Admin ? '#dc3545' : ($u->role === \App\Enums\UserRole::Doctor ? '#0d6efd' : ($u->role === \App\Enums\UserRole::Accountant ? '#198754' : '#fd7e14')) }};">
                                        <i class="bi {{ $u->role === \App\Enums\UserRole::Admin ? 'bi-shield-shaded' : ($u->role === \App\Enums\UserRole::Doctor ? 'bi-heart-pulse' : ($u->role === \App\Enums\UserRole::Accountant ? 'bi-cash-coin' : 'bi-headset')) }}"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">
                                            {{ $u->name }}
                                            @if($u->id === auth()->id())
                                                <span class="badge bg-secondary-subtle text-secondary border ms-1" style="font-size: 0.7rem;">أنت حالياً</span>
                                            @endif
                                        </div>
                                        <div class="text-muted small">ID: #USR-{{ str_pad($u->id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="font-monospace text-dark">{{ $u->email }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($u->getRoleInstances() as $r)
                                        @if($r === \App\Enums\UserRole::Admin)
                                            <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill fw-semibold border border-danger-subtle">
                                                <i class="bi bi-shield-check me-1"></i> {{ $r->label() }}
                                            </span>
                                        @elseif($r === \App\Enums\UserRole::Doctor)
                                            <span class="badge bg-primary-subtle text-primary px-2 py-1 rounded-pill fw-semibold border border-primary-subtle">
                                                <i class="bi bi-person-badge me-1"></i> {{ $r->label() }}
                                            </span>
                                        @elseif($r === \App\Enums\UserRole::Accountant)
                                            <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill fw-semibold border border-success-subtle">
                                                <i class="bi bi-wallet2 me-1"></i> {{ $r->label() }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis px-2 py-1 rounded-pill fw-semibold border border-warning-subtle">
                                                <i class="bi bi-headset me-1"></i> {{ $r->label() }}
                                            </span>
                                        @endif
                                    @endforeach
                                    @if(count($u->getRolesArray()) > 1)
                                        <span class="badge bg-info text-white px-2 py-1 rounded-pill" title="هذا الموظف يمتلك صلاحيات مدمجة في أكثر من قسم">
                                            <i class="bi bi-layers-fill"></i> مدمج
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($u->is_active)
                                    <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">
                                        <i class="bi bi-check-circle-fill me-1"></i> حساب نشط ومفعل
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">
                                        <i class="bi bi-x-circle-fill me-1"></i> حساب مجمد ومحظور
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $u->created_at->format('Y-m-d') }}
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-1">
                                    <button class="btn btn-sm btn-light border rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}" title="تعديل الحساب والصلاحيات">
                                        <i class="bi bi-pencil-square text-primary"></i>
                                    </button>

                                    @if($u->id !== auth()->id())
                                    <form action="{{ route('clinic.users.toggle', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من تغيير حالة نشاط هذا المستخدم؟')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $u->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} rounded-pill px-2" title="{{ $u->is_active ? 'تجميد وتعطيل الحساب' : 'إعادة تفعيل الحساب' }}">
                                            <i class="bi {{ $u->is_active ? 'bi-lock-fill' : 'bi-unlock-fill' }}"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Edit User Modal -->
                        <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('clinic.users.update', $u) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header border-bottom">
                                            <h5 class="modal-title fw-bold">
                                                <i class="bi bi-pencil-fill text-primary me-2"></i> تعديل حساب: {{ $u->name }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">الاسم الكامل <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="{{ old('name', $u->name) }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">البريد الإلكتروني <span class="text-danger">*</span></label>
                                                <input type="email" name="email" class="form-control" value="{{ old('email', $u->email) }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">مجموعات الصلاحيات الممنوحة (يمكن اختيار أكثر من مجموعة) <span class="text-danger">*</span></label>
                                                <div class="row g-2">
                                                    @foreach(\App\Enums\UserRole::cases() as $role)
                                                    <div class="col-6">
                                                        <div class="form-check p-2 border rounded-3 bg-light">
                                                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->value }}" id="editRole{{ $u->id }}_{{ $role->value }}"
                                                                   {{ $u->hasRole($role) ? 'checked' : '' }}
                                                                   {{ ($u->id === auth()->id() && $role === \App\Enums\UserRole::Admin) ? 'checked disabled' : '' }}>
                                                            <label class="form-check-label small fw-bold text-dark" for="editRole{{ $u->id }}_{{ $role->value }}">
                                                                {{ $role->label() }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                @if($u->id === auth()->id())
                                                    <input type="hidden" name="roles[]" value="admin">
                                                    <span class="text-muted small" style="font-size: 0.75rem;">لا يمكنك إزالة صلاحية المدير عن حسابك الخاص لمنع قفل النظام.</span>
                                                @endif
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">تغيير كلمة المرور (اتركه فارغاً للإبقاء على الحالية)</label>
                                                <input type="password" name="password" class="form-control" placeholder="•••••••• (حد أدنى 8 خانات)">
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top bg-light">
                                            <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">إلغاء</button>
                                            <button type="submit" class="btn btn-primary rounded-pill px-4">حفظ التعديلات</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 2: Permissions Matrix -->
    <div class="tab-pane fade" id="pills-matrix" role="tabpanel">
        <div class="clinic-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">مصفوفة التحكم في الوصول المبنية على الأدوار (RBAC Matrix)</h5>
                    <p class="text-muted small mb-0">توزيع الحقوق البرمجية والميدلوير (Middleware) بين أقسام العيادة لضمان سرية البيانات الطبية والمالية.</p>
                </div>
                <span class="badge bg-primary-subtle text-primary border px-3 py-2 rounded-pill">
                    <i class="bi bi-shield-check me-1"></i> مطبق برمجياً عبر EnsureUserHasRole
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-end" style="width: 35%;">الوحدة / الصلاحية البرمجية</th>
                            <th style="width: 16%;" class="text-danger fw-bold"><i class="bi bi-shield-shaded me-1"></i> مدير النظام (Admin)</th>
                            <th style="width: 16%;" class="text-primary fw-bold"><i class="bi bi-heart-pulse me-1"></i> طبيب أسنان (Doctor)</th>
                            <th style="width: 16%;" class="text-warning-emphasis fw-bold"><i class="bi bi-headset me-1"></i> موظف استقبال (Receptionist)</th>
                            <th style="width: 16%;" class="text-success fw-bold"><i class="bi bi-cash-coin me-1"></i> محاسب مالي (Accountant)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($matrix as $item)
                        <tr>
                            <td class="text-end py-3">
                                <div class="fw-bold text-dark">{{ $item['module'] }}</div>
                                <div class="text-muted small">{{ $item['description'] }}</div>
                            </td>
                            <td>
                                @if($item['admin'])
                                    <span class="badge bg-success-subtle text-success p-2 rounded-circle"><i class="bi bi-check-lg fs-6"></i></span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger p-2 rounded-circle"><i class="bi bi-x-lg fs-6"></i></span>
                                @endif
                            </td>
                            <td>
                                @if($item['doctor'])
                                    <span class="badge bg-success-subtle text-success p-2 rounded-circle"><i class="bi bi-check-lg fs-6"></i></span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger p-2 rounded-circle"><i class="bi bi-x-lg fs-6"></i></span>
                                @endif
                            </td>
                            <td>
                                @if($item['receptionist'])
                                    <span class="badge bg-success-subtle text-success p-2 rounded-circle"><i class="bi bi-check-lg fs-6"></i></span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger p-2 rounded-circle"><i class="bi bi-x-lg fs-6"></i></span>
                                @endif
                            </td>
                            <td>
                                @if($item['accountant'])
                                    <span class="badge bg-success-subtle text-success p-2 rounded-circle"><i class="bi bi-check-lg fs-6"></i></span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger p-2 rounded-circle"><i class="bi bi-x-lg fs-6"></i></span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 3: Security Audit & Controls -->
    <div class="tab-pane fade" id="pills-audit" role="tabpanel">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="clinic-card p-4 h-100">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check text-success fs-4"></i> عناصر الحماية والأمان المفعلة برمجياً
                    </h5>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0 py-3 d-flex align-items-start gap-3">
                            <span class="badge bg-success rounded-circle p-2 mt-1"><i class="bi bi-check2"></i></span>
                            <div>
                                <h6 class="fw-bold mb-1">حماية ضد هجمات القوة الغاشمة (Brute Force Rate Limiting)</h6>
                                <p class="text-muted small mb-0">نظام تقييد محاولات الدخول عبر <code class="text-primary">RateLimiter</code> (أقصى حد 5 محاولات في الدقيقة الواحدة لكل IP وبريد)، مع حظر تلقائي وإظهار عد تنازلي بالثواني.</p>
                            </div>
                        </div>
                        <div class="list-group-item px-0 py-3 d-flex align-items-start gap-3">
                            <span class="badge bg-success rounded-circle p-2 mt-1"><i class="bi bi-check2"></i></span>
                            <div>
                                <h6 class="fw-bold mb-1">تشفير كلمات المرور المتقدم (Bcrypt / Argon2)</h6>
                                <p class="text-muted small mb-0">جميع كلمات المرور مشفرة بواسطة خوارزمية التجزئة ذات التكلفة العالية، ولا يتم تخزين أي كلمة مرور بصيغة نصية صريحة إطلاقاً.</p>
                            </div>
                        </div>
                        <div class="list-group-item px-0 py-3 d-flex align-items-start gap-3">
                            <span class="badge bg-success rounded-circle p-2 mt-1"><i class="bi bi-check2"></i></span>
                            <div>
                                <h6 class="fw-bold mb-1">الوقاية من تثبيت الجلسات (Session Fixation Prevention)</h6>
                                <p class="text-muted small mb-0">يتم تجديد معرف الجلسة تلقائياً عبر <code class="text-primary">$request->session()->regenerate()</code> فور نجاح المصادقة وإبطالها وتفريغ الـ Tokens عند تسجيل الخروج.</p>
                            </div>
                        </div>
                        <div class="list-group-item px-0 py-3 d-flex align-items-start gap-3">
                            <span class="badge bg-success rounded-circle p-2 mt-1"><i class="bi bi-check2"></i></span>
                            <div>
                                <h6 class="fw-bold mb-1">الحماية من تزوير الطلبات والتسلل (CSRF & XSS Protection)</h6>
                                <p class="text-muted small mb-0">كل النماذج والطلبات البرمجية محمية برمز تشفير CSRF فريد لكل جلسة، بالإضافة لفلترة النصوص ومخرجات Blade التلقائية لمنع حقن السكربتات الخبيثة.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="clinic-card p-4 h-100">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-award-fill text-warning fs-4"></i> سياسات وممارسات أمان السجلات الطبية (HIPAA / GDPR Ready)
                    </h5>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0 py-3 d-flex align-items-start gap-3">
                            <span class="badge bg-info rounded-circle p-2 mt-1"><i class="bi bi-lock-fill"></i></span>
                            <div>
                                <h6 class="fw-bold mb-1">عزل صلاحيات المرضى المالية عن السجلات السريرية</h6>
                                <p class="text-muted small mb-0">لا يستطيع موظف الاستقبال أو المحاسب التلاعب ببيانات مخطط الأسنان أو التقرير الجراحي، كما لا يمكن للطبيب العبث بالسجلات المحاسبية والضرائب.</p>
                            </div>
                        </div>
                        <div class="list-group-item px-0 py-3 d-flex align-items-start gap-3">
                            <span class="badge bg-info rounded-circle p-2 mt-1"><i class="bi bi-person-x-fill"></i></span>
                            <div>
                                <h6 class="fw-bold mb-1">تجميد الحسابات الفوري (Instant Account Revocation)</h6>
                                <p class="text-muted small mb-0">يمكن للإدارة بضغطة زر واحدة تجميد أي حساب موظف غادر العمل، مما يمنعه فوراً من الوصول لبيانات العيادة أو تنفيذ أي عملية برمجية.</p>
                            </div>
                        </div>
                        <div class="list-group-item px-0 py-3 d-flex align-items-start gap-3">
                            <span class="badge bg-info rounded-circle p-2 mt-1"><i class="bi bi-shield-shaded"></i></span>
                            <div>
                                <h6 class="fw-bold mb-1">حماية المشرف من القفل الذاتي (Admin Lockout Prevention)</h6>
                                <p class="text-muted small mb-0">يمنع النظام برمجياً قيام المشرف بتعطيل حسابه الخاص أو تغيير دوره الإداري لتجنب فقدان السيطرة على لوحة التحكم.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: New User -->
<div class="modal fade" id="newUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('clinic.users.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-person-plus-fill text-primary me-2"></i> إضافة موظف أو مستخدم جديد للنظام
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">الاسم الكامل للموظف <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="مثال: د. عبدالمحسن الأحمد" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">البريد الإلكتروني (لتسجيل الدخول) <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="user@dentalcare.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">مجموعات الصلاحيات الممنوحة (يمكنك اختيار أكثر من مجموعة) <span class="text-danger">*</span></label>
                        <p class="text-muted small mb-2" style="font-size: 0.75rem;">يمكنك تحديد أكثر من دور معاً لنفس الموظف (مثال: الجمع بين موظف استقبال ومحاسب مالي):</p>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="form-check p-2 border rounded-3 bg-light">
                                    <input class="form-check-input role-group-check" type="checkbox" name="roles[]" value="receptionist" id="newRoleReceptionist" checked>
                                    <label class="form-check-label small fw-bold text-dark" for="newRoleReceptionist">
                                        <i class="bi bi-headset text-warning me-1"></i> موظف استقبال
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check p-2 border rounded-3 bg-light">
                                    <input class="form-check-input role-group-check" type="checkbox" name="roles[]" value="accountant" id="newRoleAccountant">
                                    <label class="form-check-label small fw-bold text-dark" for="newRoleAccountant">
                                        <i class="bi bi-cash-coin text-success me-1"></i> محاسب مالي
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check p-2 border rounded-3 bg-light">
                                    <input class="form-check-input role-group-check" type="checkbox" name="roles[]" value="doctor" id="newRoleDoctor">
                                    <label class="form-check-label small fw-bold text-dark" for="newRoleDoctor">
                                        <i class="bi bi-heart-pulse text-primary me-1"></i> طبيب أسنان
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check p-2 border rounded-3 bg-light">
                                    <input class="form-check-input role-group-check" type="checkbox" name="roles[]" value="admin" id="newRoleAdmin">
                                    <label class="form-check-label small fw-bold text-dark" for="newRoleAdmin">
                                        <i class="bi bi-shield-shaded text-danger me-1"></i> مدير النظام
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="doctorExtraFields" class="d-none">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">التخصص الطبي</label>
                            <input type="text" name="speciality" class="form-control" placeholder="مثال: أخصائي علاج جذور وعصب">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">رقم هاتف الطبيب</label>
                            <input type="text" name="phone" class="form-control" placeholder="05xxxxxxxx">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">كلمة المرور الابتدائية <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="حد أدنى 8 أحرف وأرقام ورموز" required>
                        <span class="text-muted small" style="font-size: 0.75rem;">يجب أن تكون كلمة مرور قوية لتلبية سياسة الأمان.</span>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">إنشاء وتفعيل الحساب</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#newRoleDoctor').on('change', function() {
        if ($(this).is(':checked')) {
            $('#doctorExtraFields').removeClass('d-none');
        } else {
            $('#doctorExtraFields').addClass('d-none');
        }
    });
});
</script>
@endpush

@endsection
