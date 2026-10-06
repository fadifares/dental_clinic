<!-- ==================== لوحة قيادة طبيب الأسنان (Doctor Clinical Dashboard) ==================== -->

<!-- Doctor Welcome Banner -->
<div class="clinic-card p-4 mb-4 bg-primary text-white rounded-4 shadow-sm position-relative overflow-hidden">
    <div class="row align-items-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center shadow" style="width: 58px; height: 58px;">
                    <i class="bi bi-heart-pulse-fill fs-2"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1">مرحباً، {{ $doctorData['currentDoctor']->name }} 🩺</h4>
                    <p class="mb-0 text-white-50 small">{{ $doctorData['currentDoctor']->speciality }} • نسبة العمولة المعتمدة: {{ $doctorData['currentDoctor']->commission_rate }}%</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <div class="d-inline-flex gap-2 bg-white bg-opacity-25 p-2 rounded-pill backdrop-blur">
                <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold">
                    <i class="bi bi-check2-all me-1"></i> {{ $doctorData['completedCasesCount'] }} حالات منجزة هذا الشهر
                </span>
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                    <i class="bi bi-cash me-1"></i> عمولة تقريبية: {{ number_format($doctorData['estimatedCommission'], 2) }} ر.س
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Active Chair-Side Patient Alert & Odontogram -->
<div class="row g-4 mb-4">
    <!-- Active Patient on Dental Chair -->
    <div class="col-xl-4">
        <div class="clinic-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-bold border border-danger-subtle">
                        <i class="bi bi-person-wheelchair me-1"></i> المريض على كرسي الكشف حالياً
                    </span>
                    <a href="{{ route('clinic.patients.show', $doctorData['activePatient']) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                        الملف الطبي الكامل <i class="bi bi-arrow-left ms-1"></i>
                    </a>
                </div>

                <div class="p-3 bg-light rounded-4 border mb-3">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 48px; height: 48px;">
                            {{ mb_substr($doctorData['activePatient']->name, 0, 1) }}
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">{{ $doctorData['activePatient']->name }}</h5>
                            <span class="text-muted small">رقم الملف: {{ $doctorData['activePatient']->file_number }} • هاتف: {{ $doctorData['activePatient']->phone }}</span>
                        </div>
                    </div>

                    <!-- Medical Alerts & Allergies (CRITICAL FOR DENTISTS) -->
                    <div class="p-2 bg-danger-subtle rounded-3 border border-danger-subtle mt-2">
                        <div class="d-flex align-items-center gap-2 text-danger fw-bold small mb-1">
                            <i class="bi bi-exclamation-triangle-fill"></i> تنبيهات طبية وحساسية دوائية:
                        </div>
                        <div class="text-danger small">
                            {{ $doctorData['activePatient']->allergies ?? 'لا توجد حساسية مسجلة للمريض' }}
                        </div>
                    </div>
                </div>

                <!-- Patient Quick History -->
                <div class="mb-3">
                    <label class="text-muted small fw-bold d-block mb-1">التاريخ المرضي والأمراض المزمنة:</label>
                    <p class="small text-dark bg-white p-2 border rounded-3 mb-0">
                        {{ $doctorData['activePatient']->medical_history ?? 'سليم صحياً' }}
                    </p>
                </div>
            </div>

            <!-- Patient Switcher for Today's Appointments -->
            <div class="border-top pt-3">
                <label class="text-muted small fw-bold d-block mb-2">التبديل إلى مريض آخر من مواعيد اليوم:</label>
                <div class="d-flex flex-column gap-1">
                    @foreach($doctorData['doctorAppointments'] as $apt)
                    <a href="?patient_id={{ $apt->patient->id }}" class="btn btn-sm {{ $apt->patient->id === $doctorData['activePatient']->id ? 'btn-primary' : 'btn-light border' }} text-start d-flex justify-content-between align-items-center rounded-3">
                        <span>{{ $apt->patient->name }} ({{ substr($apt->appointment_time, 0, 5) }})</span>
                        <span class="badge bg-secondary-subtle text-dark" style="font-size: 0.7rem;">{{ $apt->service_type }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Live Odontogram for Dental Chair Diagnosis -->
    <div class="col-xl-8" id="odontogram-section">
        <div class="clinic-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">مخطط الأسنان السريري التفاعلي (Live Odontogram) 🦷</h5>
                    <span class="text-muted small">انقر على أي سن لتحديد الحالة السريرية والحفظ الفوري في قاعدة البيانات</span>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-success-subtle text-success border px-2 py-1">سليم</span>
                    <span class="badge bg-danger-subtle text-danger border px-2 py-1">تسوس</span>
                    <span class="badge bg-primary-subtle text-primary border px-2 py-1">حشوة</span>
                    <span class="badge bg-warning-subtle text-warning-emphasis border px-2 py-1">تاج / تركيب</span>
                    <span class="badge bg-purple-subtle text-info border px-2 py-1">زراعة</span>
                </div>
            </div>

            <!-- Adult Teeth Chart FDI (11..48) -->
            <div class="odontogram-container p-3 rounded-4 bg-white border">
                <!-- Upper Arch (Maxilla) -->
                <div class="text-center mb-4">
                    <div class="small fw-bold text-muted mb-2">الفك العلوي (Upper Arch - Maxilla)</div>
                    <div class="dental-arch justify-content-center">
                        @foreach([18, 17, 16, 15, 14, 13, 12, 11] as $tNum)
                            @php $status = $doctorData['patientToothConditions'][$tNum] ?? 'healthy'; @endphp
                            <div class="tooth-item" data-tooth-id="{{ $tNum }}" data-tooth-name="السن رقم {{ $tNum }}" data-status="{{ $status }}">
                                <div class="tooth-visual">
                                    <div class="surface-grid">
                                        <span class="surface"></span><span class="surface"></span>
                                        <span class="surface"></span><span class="surface"></span>
                                    </div>
                                </div>
                                <span class="tooth-number">{{ $tNum }}</span>
                            </div>
                        @endforeach

                        <div class="border-start border-2 border-secondary mx-2 align-self-stretch" style="opacity: 0.3;"></div>

                        @foreach([21, 22, 23, 24, 25, 26, 27, 28] as $tNum)
                            @php $status = $doctorData['patientToothConditions'][$tNum] ?? 'healthy'; @endphp
                            <div class="tooth-item" data-tooth-id="{{ $tNum }}" data-tooth-name="السن رقم {{ $tNum }}" data-status="{{ $status }}">
                                <div class="tooth-visual">
                                    <div class="surface-grid">
                                        <span class="surface"></span><span class="surface"></span>
                                        <span class="surface"></span><span class="surface"></span>
                                    </div>
                                </div>
                                <span class="tooth-number">{{ $tNum }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Lower Arch (Mandible) -->
                <div class="text-center">
                    <div class="dental-arch justify-content-center">
                        @foreach([48, 47, 46, 45, 44, 43, 42, 41] as $tNum)
                            @php $status = $doctorData['patientToothConditions'][$tNum] ?? 'healthy'; @endphp
                            <div class="tooth-item" data-tooth-id="{{ $tNum }}" data-tooth-name="السن رقم {{ $tNum }}" data-status="{{ $status }}">
                                <span class="tooth-number">{{ $tNum }}</span>
                                <div class="tooth-visual">
                                    <div class="surface-grid">
                                        <span class="surface"></span><span class="surface"></span>
                                        <span class="surface"></span><span class="surface"></span>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="border-start border-2 border-secondary mx-2 align-self-stretch" style="opacity: 0.3;"></div>

                        @foreach([31, 32, 33, 34, 35, 36, 37, 38] as $tNum)
                            @php $status = $doctorData['patientToothConditions'][$tNum] ?? 'healthy'; @endphp
                            <div class="tooth-item" data-tooth-id="{{ $tNum }}" data-tooth-name="السن رقم {{ $tNum }}" data-status="{{ $status }}">
                                <span class="tooth-number">{{ $tNum }}</span>
                                <div class="tooth-visual">
                                    <div class="surface-grid">
                                        <span class="surface"></span><span class="surface"></span>
                                        <span class="surface"></span><span class="surface"></span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="small fw-bold text-muted mt-2">الفك السفلي (Lower Arch - Mandible)</div>
                </div>
            </div>

            <div class="mt-3 text-muted small d-flex justify-content-between align-items-center">
                <span>المريض المعروض حالياً: <strong class="text-primary">{{ $doctorData['activePatient']->name }}</strong></span>
                <span class="text-success"><i class="bi bi-shield-check"></i> حفظ فوري متزامن مع MySQL</span>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section: Doctor's Queue Today & Lab Orders -->
<div class="row g-4">
    <!-- Doctor's Patient Queue Today -->
    <div class="col-xl-7">
        <div class="clinic-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">قائمة مرضاي وجدول العيادة اليوم 📋</h5>
                    <span class="text-muted small">المرضى المجدولين لعيادتك اليوم</span>
                </div>
                <span class="badge bg-primary-subtle text-primary border px-3 py-2 rounded-pill">
                    {{ $doctorData['doctorAppointments']->count() }} مواعيد اليوم
                </span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>المريض</th>
                            <th>الإجراء المطلوب</th>
                            <th>الوقت</th>
                            <th>الحالة</th>
                            <th class="text-end">الإجراء السريري</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($doctorData['doctorAppointments'] as $apt)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $apt->patient->name }}</div>
                                <div class="text-muted small">{{ $apt->patient->file_number }}</div>
                            </td>
                            <td>{{ $apt->service_type }}</td>
                            <td class="font-monospace fw-bold text-primary">{{ substr($apt->appointment_time, 0, 5) }}</td>
                            <td>
                                @if($apt->status === 'waiting')
                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> بصالة الانتظار</span>
                                @elseif($apt->status === 'in_consultation')
                                    <span class="badge bg-primary"><i class="bi bi-heart-pulse me-1"></i> على الكرسي</span>
                                @elseif($apt->status === 'completed')
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> اكتملت</span>
                                @else
                                    <span class="badge bg-secondary">مجدول</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    @if($apt->status === 'waiting')
                                    <button class="btn btn-sm btn-primary rounded-pill px-2 btn-change-status" data-id="{{ $apt->id }}" data-status="in_consultation">
                                        <i class="bi bi-arrow-down-left-circle me-1"></i> استدعاء للكشف
                                    </button>
                                    @elseif($apt->status === 'in_consultation')
                                    <button class="btn btn-sm btn-success rounded-pill px-2 btn-change-status" data-id="{{ $apt->id }}" data-status="completed">
                                        <i class="bi bi-check-lg me-1"></i> اكتمال الجلسة
                                    </button>
                                    @endif
                                    <a href="?patient_id={{ $apt->patient->id }}" class="btn btn-sm btn-outline-info rounded-pill px-2" title="تفعيل المخطط الطبي">
                                        <i class="bi bi-grid-3x3-gap"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">لا توجد مواعيد مخصصة لعيادتك اليوم</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Doctor's Prosthetic & Lab Orders -->
    <div class="col-xl-5">
        <div class="clinic-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">طلبيات المعامل والتركيبات الخاصة بي 📦</h5>
                    <span class="text-muted small">تيجان، زيركون، وجسور قيد التصنيع أو جاهزة</span>
                </div>
                <a href="{{ route('clinic.labs.index') }}" class="btn btn-sm btn-outline-info rounded-pill">
                    طلب معمل جديد <i class="bi bi-plus-lg ms-1"></i>
                </a>
            </div>

            <div class="d-flex flex-column gap-3">
                @forelse($doctorData['doctorLabOrders'] as $order)
                <div class="p-3 bg-light rounded-3 border">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark small">{{ $order->item_type }} @if($order->tooth_numbers) (سن #{{ $order->tooth_numbers }}) @endif</span>
                        @if($order->status === 'ready')
                            <span class="badge bg-success small"><i class="bi bi-check2-circle me-1"></i> جاهز للتركيب</span>
                        @elseif($order->status === 'in_progress')
                            <span class="badge bg-warning text-dark small"><i class="bi bi-tools me-1"></i> بالمعمل</span>
                        @else
                            <span class="badge bg-secondary small">تم الإرسال</span>
                        @endif
                    </div>
                    <div class="small text-muted mt-1">المريض: {{ $order->patient->name }} • {{ $order->lab_name }}</div>
                    <div class="d-flex justify-content-between align-items-center small text-secondary mt-2 pt-2 border-top">
                        <span><i class="bi bi-palette text-primary me-1"></i> درجة اللون: <strong class="text-dark">{{ $order->shade ?? 'A2' }}</strong></span>
                        <span>تاريخ التسليم: {{ $order->expected_delivery_date ?? 'اليوم' }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4 small">لا توجد طلبيات معمل مسجلة باسمك</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
