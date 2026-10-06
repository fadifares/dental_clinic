@extends('layouts.clinic')

@section('title', 'طلبيات معامل الأسنان والتركيبات | Dental Pro ERP')

@section('content')

<!-- Header & Quick Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">طلبيات معامل الأسنان والتركيبات (Dental Lab Orders) 📦</h3>
        <p class="text-muted small mb-0">تتبع تصنيع التيجان، الجسور، قوالب التقويم، ودرجات الألوان (Shades) مع المعامل الخارجية.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#newLabOrderModal">
            <i class="bi bi-plus-lg me-1"></i> طلبية معمل جديدة
        </button>
    </div>
</div>

<!-- Stats Bar -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">إجمالي طلبيات المعامل</span>
                <h3 class="fw-bold text-dark mb-0">{{ $stats['total'] }}</h3>
            </div>
            <div class="clinic-stat-icon bg-primary-subtle text-primary">
                <i class="bi bi-box-seam fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">قيد التصنيع في المعمل</span>
                <h3 class="fw-bold text-warning mb-0">{{ $stats['in_progress'] }}</h3>
            </div>
            <div class="clinic-stat-icon bg-warning-subtle text-warning">
                <i class="bi bi-tools fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">جاهزة للاستلام / التركيب</span>
                <h3 class="fw-bold text-success mb-0">{{ $stats['ready'] }}</h3>
            </div>
            <div class="clinic-stat-icon bg-success-subtle text-success">
                <i class="bi bi-check2-circle fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="clinic-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block mb-1">تم تسليمها وتركيبها</span>
                <h3 class="fw-bold text-secondary mb-0">{{ $stats['delivered'] }}</h3>
            </div>
            <div class="clinic-stat-icon bg-light text-secondary">
                <i class="bi bi-shield-check fs-4"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="clinic-card p-3 mb-4">
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <span class="small fw-bold text-muted me-2"><i class="bi bi-funnel-fill"></i> تصفية الطلبيات:</span>
        <a href="{{ route('clinic.labs.index') }}" class="btn btn-sm rounded-pill {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">
            الكل ({{ $stats['total'] }})
        </a>
        <a href="{{ route('clinic.labs.index', ['status' => 'sent']) }}" class="btn btn-sm rounded-pill {{ request('status') == 'sent' ? 'btn-primary' : 'btn-outline-secondary' }}">
            أُرسلت حديثاً
        </a>
        <a href="{{ route('clinic.labs.index', ['status' => 'in_progress']) }}" class="btn btn-sm rounded-pill {{ request('status') == 'in_progress' ? 'btn-primary' : 'btn-outline-secondary' }}">
            قيد التصنيع
        </a>
        <a href="{{ route('clinic.labs.index', ['status' => 'ready']) }}" class="btn btn-sm rounded-pill {{ request('status') == 'ready' ? 'btn-primary' : 'btn-outline-secondary' }}">
            جاهزة للاستلام
        </a>
        <a href="{{ route('clinic.labs.index', ['status' => 'delivered']) }}" class="btn btn-sm rounded-pill {{ request('status') == 'delivered' ? 'btn-primary' : 'btn-outline-secondary' }}">
            تم التركيب للمريض
        </a>
    </div>
</div>

<!-- Lab Orders Table Card -->
<div class="clinic-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>نوع التركيبة</th>
                    <th>اسم المعمل</th>
                    <th>المريض</th>
                    <th>الطبيب المشرف</th>
                    <th>رقم السن</th>
                    <th>درجة اللون</th>
                    <th>التكلفة</th>
                    <th>تاريخ التسليم المتوقع</th>
                    <th>الحالة</th>
                    <th class="text-center">تحديث الحالة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($labOrders as $order)
                <tr data-order-id="{{ $order->id }}">
                    <td>
                        <div class="fw-bold text-dark">{{ $order->item_type }}</div>
                        <small class="text-muted">أرسلت: {{ $order->order_date->format('Y-m-d') }}</small>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $order->lab_name }}</span>
                    </td>
                    <td>
                        <a href="{{ route('clinic.patients.show', $order->patient) }}" class="fw-bold text-dark text-decoration-none">
                            {{ $order->patient->name }}
                        </a>
                        <div class="text-muted small" style="font-size:0.75rem;">#{{ $order->patient->file_number }}</div>
                    </td>
                    <td>{{ $order->doctor ? $order->doctor->name : '-' }}</td>
                    <td>
                        @if($order->tooth_numbers)
                            <span class="badge bg-primary-subtle text-primary font-monospace">سن #{{ $order->tooth_numbers }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($order->shade)
                            <span class="badge bg-warning-subtle text-dark border font-monospace"><i class="bi bi-palette text-primary me-1"></i> {{ $order->shade }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="font-monospace fw-semibold">{{ number_format($order->cost, 2) }} ر.س</td>
                    <td>
                        {{ $order->expected_delivery_date ? $order->expected_delivery_date->format('Y-m-d') : '-' }}
                    </td>
                    <td>
                        @if($order->status === 'ready') <span class="badge bg-success status-badge">جاهز للاستلام</span>
                        @elseif($order->status === 'in_progress') <span class="badge bg-warning text-dark status-badge">قيد التصنيع</span>
                        @elseif($order->status === 'delivered') <span class="badge bg-secondary status-badge">تم التركيب</span>
                        @else <span class="badge bg-info text-white status-badge">أرسل للمعمل</span> @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            @if($order->status === 'sent')
                                <button class="btn btn-warning btn-lab-status" data-status="in_progress">بدء التصنيع</button>
                            @elseif($order->status === 'in_progress')
                                <button class="btn btn-success btn-lab-status" data-status="ready">جاهز</button>
                            @elseif($order->status === 'ready')
                                <button class="btn btn-secondary btn-lab-status" data-status="delivered">تم التركيب</button>
                            @else
                                <span class="badge bg-light text-success"><i class="bi bi-check-all"></i> منجز</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-5 text-muted">
                        <i class="bi bi-box-seam display-4 text-secondary d-block mb-2"></i>
                        لا توجد طلبيات معمل مطابقة للتصفية
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($labOrders->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $labOrders->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<!-- MODAL: New Lab Order -->
<div class="modal fade" id="newLabOrderModal" tabindex="-1" aria-labelledby="newLabOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-box-seam text-primary me-2"></i> تسجيل طلبية معمل جديدة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('clinic.labs.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">المريض <span class="text-danger">*</span></label>
                        <select name="patient_id" class="form-select" required>
                            <option value="" disabled selected>اختر المريض...</option>
                            @foreach($patients as $pt)
                                <option value="{{ $pt->id }}">{{ $pt->name }} ({{ $pt->phone }} - #{{ $pt->file_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">الطبيب المشرف <span class="text-danger">*</span></label>
                        <select name="doctor_id" class="form-select" required>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->speciality }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">اسم المعمل <span class="text-danger">*</span></label>
                            <input type="text" name="lab_name" class="form-control" placeholder="معمل النخبة، الرواد..." required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">نوع التركيبة <span class="text-danger">*</span></label>
                            <input type="text" name="item_type" class="form-control" placeholder="تاج زيركون، جسر إيماكس..." required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">أرقام الأسنان</label>
                            <input type="text" name="tooth_numbers" class="form-control" placeholder="مثال: 16 أو 11, 12, 13">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">درجة اللون (Shade)</label>
                            <input type="text" name="shade" class="form-control" placeholder="A1, A2, A3, BL1, BL2">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">تكلفة المعمل (ر.س) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="cost" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">تاريخ الاستلام المتوقع</label>
                            <input type="date" name="expected_delivery_date" class="form-control" value="{{ now()->addDays(3)->toDateString() }}">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">ملاحظات للمعمل</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="أي تعليمات حول الشفافية أو البرد..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
                        <i class="bi bi-send-check me-1"></i> حفظ وإرسال الطلبية
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Fast AJAX Status Switcher for Lab Orders
        $(document).on('click', '.btn-lab-status', function () {
            const btn = $(this);
            const row = btn.closest('tr');
            const orderId = row.data('order-id');
            const newStatus = btn.data('status');

            $.ajax({
                url: `/clinic/labs/${orderId}/status`,
                method: "PATCH",
                data: { status: newStatus },
                success: function (res) {
                    window.location.reload();
                },
                error: function () {
                    alert('تعذر تحديث حالة طلبية المعمل.');
                }
            });
        });
    });
</script>
@endpush
