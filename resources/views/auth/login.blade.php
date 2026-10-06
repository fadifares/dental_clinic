<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | نظام إدارة عيادات الأسنان Dental Pro</title>

    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🦷</text></svg>">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        body {
            background: radial-gradient(circle at top right, rgba(2, 132, 199, 0.08), transparent 45%),
                        radial-gradient(circle at bottom left, rgba(13, 148, 136, 0.08), transparent 45%),
                        #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 460px;
            width: 100%;
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 35px -10px rgba(15, 23, 42, 0.08);
        }
        .quick-role-btn {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .quick-role-btn:hover {
            background-color: #e0f2fe;
            border-color: #0284c7;
        }
    </style>
</head>
<body>

<div class="container p-3">
    <div class="login-card p-4 p-md-5 mx-auto">
        <!-- Logo & Header -->
        <div class="text-center mb-4">
            <div class="clinic-stat-icon bg-primary text-white mx-auto mb-3 shadow-sm" style="width: 58px; height: 58px; font-size: 1.8rem;">
                <i class="bi bi-heart-pulse-fill"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1">دنتال<span class="text-primary">برو</span> كلينيك</h4>
            <p class="text-muted small">بوابة تسجيل الدخول للكادر الطبي والإداري المعتمد</p>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 small p-3 mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger rounded-3 small p-3 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <!-- Email Field -->
            <div class="mb-3">
                <label class="form-label small fw-semibold text-dark">البريد الإلكتروني المهني</label>
                <div class="clinic-input-icon">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" id="loginEmail" class="form-control" value="{{ old('email', 'admin@dentalcare.com') }}" placeholder="user@dentalcare.com" required autofocus>
                </div>
            </div>

            <!-- Password Field -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label small fw-semibold text-dark mb-0">كلمة المرور</label>
                    <a href="#" class="text-primary small text-decoration-none" onclick="alert('يرجى التواصل مع مدير النظام لإعادة تعيين كلمة المرور');">نسيت كلمة المرور؟</a>
                </div>
                <div class="clinic-input-icon mt-1">
                    <i class="bi bi-shield-lock"></i>
                    <input type="password" name="password" id="loginPassword" class="form-control" value="Password123#" placeholder="••••••••" required>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" checked>
                <label class="form-check-label small text-muted" for="rememberMe">
                    تذكر تسجيل دخولي على هذا الجهاز
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                <i class="bi bi-box-arrow-in-left me-1"></i> تسجيل الدخول للنظام
            </button>
        </form>

        <!-- Quick Demo Switcher -->
        <div class="mt-4 pt-3 border-top text-center">
            <span class="small fw-semibold text-muted d-block mb-2">حسابات تجريبية للأدوار والصلاحيات (انقر للتعبئة):</span>
            <div class="d-flex flex-wrap gap-1 justify-content-center small">
                <span class="badge bg-light text-dark border p-2 quick-role-btn" data-email="admin@dentalcare.com" data-role="Admin">
                    👑 مدير النظام
                </span>
                <span class="badge bg-light text-dark border p-2 quick-role-btn" data-email="doctor@dentalcare.com" data-role="Doctor">
                    🦷 طبيب أسنان
                </span>
                <span class="badge bg-light text-dark border p-2 quick-role-btn" data-email="reception@dentalcare.com" data-role="Receptionist">
                    🗓️ استقبال
                </span>
                <span class="badge bg-light text-dark border p-2 quick-role-btn" data-email="accounting@dentalcare.com" data-role="Accountant">
                    💳 محاسب
                </span>
                <span class="badge bg-warning-subtle text-dark border p-2 quick-role-btn" data-email="dual@dentalcare.com" data-role="Dual">
                    ⚡ استقبال + محاسب (صلاحيات مزدوجة)
                </span>
            </div>
            <div class="small text-muted mt-2" style="font-size:0.75rem;">كلمة المرور المشتركة: <code>Password123#</code></div>
        </div>

        <!-- Back to Website -->
        <div class="text-center mt-4">
            <a href="{{ url('/') }}" class="text-secondary small text-decoration-none">
                <i class="bi bi-arrow-right me-1"></i> العودة للموقع الخارجي للعيادة
            </a>
        </div>
    </div>
</div>

<script>
    // Quick Demo Account Clicker
    document.querySelectorAll('.quick-role-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('loginEmail').value = this.dataset.email;
            document.getElementById('loginPassword').value = 'Password123#';
        });
    });
</script>

</body>
</html>
