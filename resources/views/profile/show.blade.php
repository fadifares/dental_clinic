@extends($layout)

@section('title', 'الملف الشخصي وأمان الحساب | DentalPro ERP')

@section('content')
<style>
    .clinic-field-wrap {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }
    .clinic-field-wrap .form-control {
        height: 48px;
        font-size: 0.95rem;
        border-radius: 0.65rem !important;
        background-color: #f8fafc;
        border: 1px solid #cbd5e1;
        transition: all 0.2s ease-in-out;
        width: 100%;
        color: #1e293b;
    }
    .clinic-field-wrap .form-control:focus {
        background-color: #ffffff;
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
    }
    .clinic-field-wrap .field-icon-end {
        position: absolute;
        right: 14px;
        color: #94a3b8;
        font-size: 1.15rem;
        pointer-events: none;
        z-index: 4;
        transition: color 0.2s ease;
    }
    .clinic-field-wrap:focus-within .field-icon-end {
        color: #0284c7;
    }
    .clinic-field-wrap .field-icon-start {
        position: absolute;
        left: 14px;
        color: #94a3b8;
        font-size: 1.15rem;
        pointer-events: none;
        z-index: 4;
        transition: color 0.2s ease;
    }
    .clinic-field-wrap:focus-within .field-icon-start {
        color: #0284c7;
    }
    .clinic-field-wrap .field-toggle-btn {
        position: absolute;
        left: 10px;
        background: transparent;
        border: none;
        color: #64748b;
        font-size: 1.15rem;
        width: 32px;
        height: 32px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 5;
        border-radius: 50%;
        transition: all 0.2s ease;
    }
    .clinic-field-wrap .field-toggle-btn:hover {
        color: #0284c7;
        background-color: rgba(2, 132, 199, 0.1);
    }
</style>

<div class="container-fluid py-2">
    <!-- Header & Breadcrumb -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary text-white p-2 rounded-circle">
                    <i class="bi bi-person-gear fs-5"></i>
                </span>
                <div>
                    <h3 class="fw-bold text-dark mb-0 fs-4">الملف الشخصي وأمان الحساب</h3>
                    <p class="text-muted small mb-0 mt-1">إدارة معلوماتك الشخصية، البريد الإلكتروني، وتحديث كلمة المرور لحماية حسابك</p>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if($user->isSuperAdmin())
                <a href="{{ route('appmanager.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-right me-1"></i> لوحة المنصة
                </a>
            @else
                <a href="{{ route('clinic.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-right me-1"></i> لوحة العيادة
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                <strong>يرجى تصحيح الأخطاء التالية:</strong>
            </div>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- User Summary & Identity Badge (Sidebar Card) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header border-0 py-4 text-center text-white" style="background: linear-gradient(135deg, #0b192c 0%, #0284c7 60%, #0d9488 100%);">
                    <div class="position-relative d-inline-block mb-3">
                        <div class="rounded-circle bg-white text-primary fw-extrabold d-flex align-items-center justify-content-center shadow-lg mx-auto" 
                             style="width: 88px; height: 88px; font-size: 2.2rem; border: 4px solid rgba(255, 255, 255, 0.4);">
                            {{ mb_substr($user->name, 0, 1) }}
                        </div>
                        <span class="position-absolute bottom-0 start-0 translate-middle p-2 bg-success border border-white rounded-circle" title="حساب نشط">
                            <span class="visually-hidden">نشط</span>
                        </span>
                    </div>
                    <h5 class="fw-bold mb-1 text-white">{{ $user->name }}</h5>
                    <p class="text-white-50 small mb-2 font-monospace" dir="ltr">{{ $user->email }}</p>

                    <div>
                        @if($user->isSuperAdmin())
                            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold">
                                <i class="bi bi-shield-lock-fill me-1"></i> مدير عام منصة SaaS
                            </span>
                        @else
                            <span class="badge bg-light text-primary px-3 py-1 rounded-pill fw-bold">
                                {{ $user->getRoleLabelsString() }}
                            </span>
                            @if($user->isPrimaryOwner())
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small ms-1">
                                    <i class="bi bi-patch-check-fill me-1"></i> المالك الرئيسي
                                </span>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="card-body p-4 bg-white">
                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="bi bi-shield-check text-primary me-1"></i> بيانات ومعلومات الحساب
                    </h6>

                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3 small">
                        @if($user->clinic)
                            <li class="d-flex justify-content-between align-items-center gap-2">
                                <span class="text-muted text-nowrap"><i class="bi bi-hospital me-1 text-info"></i> العيادة التابع لها:</span>
                                <span class="fw-bold text-dark text-truncate">{{ $user->clinic->name }}</span>
                            </li>
                            <li class="d-flex justify-content-between align-items-center gap-2">
                                <span class="text-muted text-nowrap"><i class="bi bi-hash me-1 text-info"></i> معرّف العيادة (Slug):</span>
                                <span class="badge bg-light text-dark font-monospace border px-2 py-1">{{ $user->clinic->slug }}</span>
                            </li>
                        @endif

                        <li class="d-flex justify-content-between align-items-center gap-2">
                            <span class="text-muted text-nowrap"><i class="bi bi-telephone me-1 text-primary"></i> الهاتف:</span>
                            <span class="fw-bold text-dark font-monospace" dir="ltr">{{ $user->phone ?? 'غير مسجل' }}</span>
                        </li>

                        <li class="d-flex justify-content-between align-items-center gap-2">
                            <span class="text-muted text-nowrap"><i class="bi bi-calendar-event me-1 text-secondary"></i> تاريخ التسجيل:</span>
                            <span class="fw-medium text-dark font-monospace">{{ $user->created_at->format('Y-m-d') }}</span>
                        </li>

                        <li class="d-flex justify-content-between align-items-center gap-2">
                            <span class="text-muted text-nowrap"><i class="bi bi-clock-history me-1 text-warning"></i> آخر تسجيل دخول:</span>
                            <span class="fw-bold text-dark font-monospace text-end">
                                {{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : 'الآن' }}
                            </span>
                        </li>

                        <li class="d-flex justify-content-between align-items-center gap-2">
                            <span class="text-muted text-nowrap"><i class="bi bi-globe me-1 text-info"></i> عنوان IP الأخير:</span>
                            <span class="badge bg-secondary-subtle text-dark font-monospace px-2 py-1">
                                {{ $user->last_login_ip ?? request()->ip() }}
                            </span>
                        </li>

                        <li class="d-flex justify-content-between align-items-center gap-2">
                            <span class="text-muted text-nowrap"><i class="bi bi-check-circle me-1 text-success"></i> حالة الحساب:</span>
                            <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill fw-bold">
                                {{ $user->is_active ? 'نشط وفعّال' : 'معطّل' }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Recent Activity Mini Card -->
            @if($recentActivities->isNotEmpty())
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-activity text-primary me-1"></i> آخر حركاتك على النظام
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush small">
                        @foreach($recentActivities as $act)
                            <div class="list-group-item px-3 py-2.5">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-primary-subtle text-primary" style="font-size: 0.68rem;">{{ $act->action_type }}</span>
                                    <span class="text-muted" style="font-size: 0.72rem;">{{ $act->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-dark fw-medium text-truncate" title="{{ $act->description }}">{{ $act->description }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Main Form Column -->
        <div class="col-lg-8">
            <!-- 1. Edit Personal Info -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2.5">
                    <span class="badge bg-primary-subtle text-primary p-2 rounded-3">
                        <i class="bi bi-person-lines-fill fs-6"></i>
                    </span>
                    <div>
                        <h5 class="fw-bold text-dark mb-0 fs-6">1. البيانات الشخصية ومعلومات الاتصال</h5>
                        <div class="text-muted small mt-0.5">تحديث اسمك، بريدك الإلكتروني، ورقم هاتفك المستخدم للتواصل والإشعارات</div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route($user->isSuperAdmin() ? 'appmanager.profile.info' : 'clinic.profile.info') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark mb-1.5">
                                    الاسم الكامل <span class="text-danger">*</span>
                                </label>
                                <div class="clinic-field-wrap">
                                    <i class="bi bi-person field-icon-end"></i>
                                    <input type="text" name="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           style="padding-right: 42px; padding-left: 14px;"
                                           value="{{ old('name', $user->name) }}" required placeholder="أدخل اسمك الكامل">
                                </div>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark mb-1.5">
                                    البريد الإلكتروني (لتسجيل الدخول) <span class="text-danger">*</span>
                                </label>
                                <div class="clinic-field-wrap" dir="ltr">
                                    <i class="bi bi-envelope field-icon-start"></i>
                                    <input type="email" name="email" 
                                           class="form-control text-start @error('email') is-invalid @enderror" 
                                           style="padding-left: 42px; padding-right: 14px;"
                                           value="{{ old('email', $user->email) }}" required placeholder="your.email@example.com">
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark mb-1.5">
                                    رقم الهاتف / الواتساب
                                </label>
                                <div class="clinic-field-wrap" dir="ltr">
                                    <i class="bi bi-telephone field-icon-start"></i>
                                    <input type="text" name="phone" 
                                           class="form-control text-start @error('phone') is-invalid @enderror" 
                                           style="padding-left: 42px; padding-right: 14px;"
                                           value="{{ old('phone', $user->phone) }}" placeholder="+20 1x xxx xxxx">
                                </div>
                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark mb-1.5">
                                    الدور الوظيفي في النظام
                                </label>
                                <div class="clinic-field-wrap">
                                    <i class="bi bi-shield-lock field-icon-end"></i>
                                    <input type="text" class="form-control bg-light" 
                                           style="padding-right: 42px; padding-left: 14px;"
                                           value="{{ $user->isSuperAdmin() ? 'مدير المنصة العام (SaaS Super Admin)' : $user->getRoleLabelsString() }}" 
                                           disabled readonly>
                                </div>
                                <div class="form-text text-muted" style="font-size: 0.74rem;">الصلاحيات يتم تحديدها من قبل إدارة العيادة والمنصة.</div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-1.5">
                                <i class="bi bi-check2-circle fs-6"></i>
                                <span>حفظ التعديلات الشخصية</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 2. Change Password -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2.5">
                    <span class="badge bg-warning-subtle text-warning p-2 rounded-3">
                        <i class="bi bi-key-fill fs-6 text-dark"></i>
                    </span>
                    <div>
                        <h5 class="fw-bold text-dark mb-0 fs-6">2. تعديل كلمة المرور وتأمين الحساب</h5>
                        <div class="text-muted small mt-0.5">تأكد من اختيار كلمة مرور قوية مكونة من 8 خانات على الأقل تحتوي على حروف وأرقام</div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route($user->isSuperAdmin() ? 'appmanager.profile.password' : 'clinic.profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold small text-dark mb-1.5">
                                    كلمة المرور الحالية <span class="text-danger">*</span>
                                </label>
                                <div class="clinic-field-wrap">
                                    <i class="bi bi-lock field-icon-end"></i>
                                    <input type="password" name="current_password" id="current_password" 
                                           class="form-control @error('current_password') is-invalid @enderror" 
                                           style="padding-right: 42px; padding-left: 46px;" 
                                           required placeholder="أدخل كلمة المرور الحالية للتأكيد">
                                    <button type="button" class="field-toggle-btn" 
                                            onclick="togglePassVisibility('current_password', this)" title="إظهار / إخفاء كلمة المرور">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark mb-1.5">
                                    كلمة المرور الجديدة <span class="text-danger">*</span>
                                </label>
                                <div class="clinic-field-wrap">
                                    <i class="bi bi-shield-lock field-icon-end"></i>
                                    <input type="password" name="password" id="new_password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           style="padding-right: 42px; padding-left: 46px;" 
                                           required minlength="8" placeholder="8 خانات على الأقل">
                                    <button type="button" class="field-toggle-btn" 
                                            onclick="togglePassVisibility('new_password', this)" title="إظهار / إخفاء كلمة المرور">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark mb-1.5">
                                    تأكيد كلمة المرور الجديدة <span class="text-danger">*</span>
                                </label>
                                <div class="clinic-field-wrap">
                                    <i class="bi bi-shield-check field-icon-end"></i>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="form-control" 
                                           style="padding-right: 42px; padding-left: 46px;" 
                                           required minlength="8" placeholder="أعد كتابة كلمة المرور الجديدة">
                                    <button type="button" class="field-toggle-btn" 
                                            onclick="togglePassVisibility('password_confirmation', this)" title="إظهار / إخفاء كلمة المرور">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info-subtle border-0 rounded-3 p-3 mt-3 mb-0 small text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle-fill text-info fs-5"></i>
                            <div>
                                عند تحديث كلمة المرور، يظل حسابك مسجلاً للدخول، ولكن سيتعين عليك استخدام كلمة المرور الجديدة في المرات القادمة.
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning px-4 py-2 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-1.5 text-dark">
                                <i class="bi bi-key-fill fs-6"></i>
                                <span>تحديث كلمة المرور</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>
@endsection


