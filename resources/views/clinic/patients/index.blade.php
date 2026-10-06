@extends('layouts.clinic')

@section('title', 'دليل وسجلات المرضى | Dental Pro EMR')

@section('content')

<!-- Header & Quick Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">دليل وسجلات المرضى (Patients EMR) 👥</h3>
        <p class="text-muted small mb-0">إدارة الملفات الطبية، أرقام السجلات، والتاريخ الصحي للمراجعين.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#newPatientGlobalModal">
            <i class="bi bi-person-plus-fill me-1"></i> فتح ملف مريض جديد
        </button>
    </div>
</div>

<!-- Quick Stats -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-4">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">إجمالي المرضى المسجلين</span>
                <h3 class="fw-bold text-dark mb-0">{{ $totalPatientsCount }}</h3>
            </div>
            <div class="clinic-stat-icon bg-primary-subtle text-primary">
                <i class="bi bi-people-fill fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">مرضى لديهم تحذيرات حساسية</span>
                <h3 class="fw-bold text-danger mb-0">{{ $allergiesCount }}</h3>
                <span class="badge bg-danger-subtle text-danger small mt-1">تنبيهات حرجة للأطباء</span>
            </div>
            <div class="clinic-stat-icon bg-danger-subtle text-danger">
                <i class="bi bi-exclamation-triangle-fill fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">المواعيد النشطة اليوم</span>
                <h3 class="fw-bold text-success mb-0">{{ \App\Models\Appointment::whereDate('appointment_date', today())->count() }}</h3>
                <span class="badge bg-success-subtle text-success small mt-1">جداول متابعة مستمرة</span>
            </div>
            <div class="clinic-stat-icon bg-success-subtle text-success">
                <i class="bi bi-calendar2-check-fill fs-4"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="clinic-card p-4 mb-4">
    <form method="GET" action="{{ route('clinic.patients.index') }}" class="row g-3 align-items-end">
        <div class="col-md-6 col-lg-5">
            <label class="form-label small fw-semibold">بحث بالاسم، رقم الملف، الجوال، أو الهوية</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control bg-light border-0" value="{{ request('search') }}" placeholder="مثال: فهد، 0501234567، PT-1049...">
            </div>
        </div>
        <div class="col-md-3 col-lg-3">
            <label class="form-label small fw-semibold">الجنس</label>
            <select name="gender" class="form-select bg-light border-0">
                <option value="">جميع المرضى</option>
                <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>ذكور</option>
                <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>إناث</option>
            </select>
        </div>
        <div class="col-md-3 col-lg-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-funnel-fill me-1"></i> تصفية
            </button>
            @if(request()->hasAny(['search', 'gender']))
            <a href="{{ route('clinic.patients.index') }}" class="btn btn-outline-secondary rounded-pill">
                إلغاء الفلتر
            </a>
            @endif
        </div>
    </form>
</div>

<!-- Patients Table Card -->
<div class="clinic-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>رقم الملف</th>
                    <th>اسم المريض</th>
                    <th>رقم الجوال</th>
                    <th>الجنس / العمر</th>
                    <th>التنبيهات الطبية</th>
                    <th>الزيارات السابقة</th>
                    <th class="text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                <tr>
                    <td>
                        <span class="badge bg-primary-subtle text-primary fw-bold font-monospace px-2 py-1">
                            #{{ $patient->file_number }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-bold text-dark">{{ $patient->name }}</div>
                        @if($patient->national_id)
                            <div class="text-muted small" style="font-size:0.75rem;">هوية: {{ $patient->national_id }}</div>
                        @endif
                    </td>
                    <td>
                        <a href="tel:{{ $patient->phone }}" class="text-decoration-none font-monospace small">
                            <i class="bi bi-telephone-fill text-muted me-1"></i> {{ $patient->phone }}
                        </a>
                    </td>
                    <td>
                        <span class="badge {{ $patient->gender == 'male' ? 'bg-light text-dark' : 'bg-pink-subtle text-danger' }} border">
                            {{ $patient->gender == 'male' ? 'ذكر' : 'أنثى' }}
                        </span>
                        @if($patient->date_of_birth)
                            <span class="small text-muted ms-1">({{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} سنة)</span>
                        @endif
                    </td>
                    <td>
                        @if($patient->allergies)
                            <span class="badge bg-danger-subtle text-danger small d-inline-block text-truncate" style="max-width: 180px;" title="{{ $patient->allergies }}">
                                <i class="bi bi-exclamation-triangle-fill"></i> {{ $patient->allergies }}
                            </span>
                        @elseif($patient->chronic_diseases)
                            <span class="badge bg-warning-subtle text-dark small d-inline-block text-truncate" style="max-width: 180px;">
                                <i class="bi bi-shield-exclamation"></i> {{ $patient->chronic_diseases }}
                            </span>
                        @else
                            <span class="text-muted small"><i class="bi bi-check-circle text-success me-1"></i> سليم</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            {{ $patient->appointments_count }} جلسات
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('clinic.patients.show', $patient) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-folder2-open me-1"></i> فتح الملف الطبي
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-people display-4 text-secondary d-block mb-2"></i>
                        لا توجد سجلات مرضى مطابقة للبحث
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($patients->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $patients->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection
