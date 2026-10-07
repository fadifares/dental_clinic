@extends('layouts.clinic')

@section('title', 'إعدادات النظام والعملة وطرق الدفع | Dental Pro ERP')

@section('content')

<form action="{{ route('clinic.settings.update') }}" method="POST" id="settingsForm">
    @csrf
    @method('PUT')

    <!-- Header & Save Action -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-gear-wide-connected text-primary me-2"></i>إعدادات النظام والعيادة (Settings)
            </h3>
            <p class="text-muted small mb-0">تخصيص العملة المعتمدة في النظام، تفعيل قنوات وسُبل الدفع، وضبط بيانات الفواتير والمطبوعات.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-check2-circle me-1"></i> حفظ كافة الإعدادات
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
        <div class="fw-semibold">{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> يرجى مراجعة الأخطاء التالية:</div>
        <ul class="mb-0 small ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row g-4">
        <!-- 1. CURRENCY & FINANCIAL SETTINGS -->
        <div class="col-lg-6">
            <div class="clinic-card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="p-3 px-4 bg-light border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary-subtle text-primary p-2 rounded-3">
                            <i class="bi bi-cash-coin fs-5"></i>
                        </span>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">العملة والتهيئة المالية</h5>
                            <span class="text-muted small" style="font-size: 0.75rem;">تحديد العملة المستخدمة في الفواتير والتقارير والسندات</span>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <!-- Quick Preset Selectors -->
                    <label class="form-label small fw-bold text-muted mb-2">اختر عملة سريعة شائعة (انقر للتطبيق الفوري):</label>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @foreach($currencies as $c)
                        <button type="button" 
                                class="btn btn-sm btn-outline-secondary currency-preset-btn rounded-pill px-3 py-1.5"
                                data-symbol="{{ $c['symbol'] }}"
                                data-name="{{ $c['name'] }}"
                                data-code="{{ $c['code'] }}">
                            <span class="me-1">{{ $c['country'] }}</span>
                            <strong class="text-dark">{{ $c['symbol'] }}</strong>
                            <small class="text-muted">({{ $c['code'] }})</small>
                        </button>
                        @endforeach
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold">رمز العملة (Symbol) <span class="text-danger">*</span></label>
                            <input type="text" name="currency_symbol" id="currencySymbolInput" 
                                   class="form-control form-control-lg fw-bold text-primary" 
                                   value="{{ old('currency_symbol', $currentSettings['currency_symbol']) }}" required>
                            <span class="text-muted small" style="font-size: 0.72rem;">مثال: ج.م أو ر.س أو $ أو د.إ</span>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label small fw-bold">كود العملة الدولي (ISO Code) <span class="text-danger">*</span></label>
                            <input type="text" name="currency_code" id="currencyCodeInput" 
                                   class="form-control form-control-lg text-uppercase font-monospace" 
                                   value="{{ old('currency_code', $currentSettings['currency_code']) }}" required>
                            <span class="text-muted small" style="font-size: 0.72rem;">مثال: EGP أو SAR أو USD</span>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">الاسم الكامل للعملة <span class="text-danger">*</span></label>
                            <input type="text" name="currency_name" id="currencyNameInput" 
                                   class="form-control" 
                                   value="{{ old('currency_name', $currentSettings['currency_name']) }}" required>
                            <span class="text-muted small" style="font-size: 0.72rem;">مثال: جنيه مصري أو ريال سعودي</span>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">موضع رمز العملة بالنسبة للمبلغ</label>
                            <div class="d-flex gap-4 p-2 bg-light rounded-3 border">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="currency_position" id="posAfter" value="after" 
                                           {{ old('currency_position', $currentSettings['currency_position']) === 'after' ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-bold" for="posAfter">
                                        بعد المبلغ (مثال: <span class="text-primary font-monospace">1,250.00 <span class="preview-symbol">{{ $currentSettings['currency_symbol'] }}</span></span>)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="currency_position" id="posBefore" value="before"
                                           {{ old('currency_position', $currentSettings['currency_position']) === 'before' ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-bold" for="posBefore">
                                        قبل المبلغ (مثال: <span class="text-primary font-monospace"><span class="preview-symbol">{{ $currentSettings['currency_symbol'] }}</span> 1,250.00</span>)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Live Preview Box -->
                    <div class="mt-4 p-3 rounded-3 border border-primary-subtle bg-primary-subtle bg-opacity-25">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold text-primary">
                                <i class="bi bi-eye-fill me-1"></i> معاينة حية للمبالغ في الفواتير:
                            </span>
                            <span class="badge bg-primary" id="previewBadge">{{ $currentSettings['currency_code'] }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between bg-white p-2.5 rounded-2 border shadow-sm">
                            <span class="text-muted small">إجمالي كشف علاج العصب والتركيبة:</span>
                            <span class="fs-5 fw-bold text-dark font-monospace" id="previewFormattedAmount">
                                @if($currentSettings['currency_position'] === 'before')
                                    {{ $currentSettings['currency_symbol'] }} 1,850.00
                                @else
                                    1,850.00 {{ $currentSettings['currency_symbol'] }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. CLINIC & INVOICE PROFILE -->
        <div class="col-lg-6">
            <div class="clinic-card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="p-3 px-4 bg-light border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success-subtle text-success p-2 rounded-3">
                            <i class="bi bi-hospital fs-5"></i>
                        </span>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">بيانات العيادة والفواتير العامة</h5>
                            <span class="text-muted small" style="font-size: 0.75rem;">تظهر في ترويسة ومطبوعات الفواتير الرسمية وسندات القبض</span>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold">اسم المنشأة / العيادة</label>
                            <input type="text" name="clinic_name" class="form-control" 
                                   value="{{ old('clinic_name', $currentSettings['clinic_name']) }}" 
                                   placeholder="مثال: مجمع دنتال برو لطب وجراحة الأسنان">
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label small fw-bold">رقم الهاتف الرسمي</label>
                            <input type="text" name="clinic_phone" class="form-control font-monospace" 
                                   value="{{ old('clinic_phone', $currentSettings['clinic_phone']) }}" 
                                   placeholder="010xxxxxxxx أو 05xxxxxxxx">
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label small fw-bold">البريد الإلكتروني للعيادة</label>
                            <input type="email" name="clinic_email" class="form-control" 
                                   value="{{ old('clinic_email', $currentSettings['clinic_email']) }}" 
                                   placeholder="contact@dentalclinic.com">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">العنوان ومقر العيادة</label>
                            <input type="text" name="clinic_address" class="form-control" 
                                   value="{{ old('clinic_address', $currentSettings['clinic_address']) }}" 
                                   placeholder="المدينة، الشارع، رقم المبنى">
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label small fw-bold">الرقم الضريبي (إن وجد)</label>
                            <input type="text" name="tax_number" class="form-control font-monospace" 
                                   value="{{ old('tax_number', $currentSettings['tax_number']) }}" 
                                   placeholder="300xxxxxxxxx">
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label small fw-bold">نسبة ضريبة القيمة المضافة (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="100" name="tax_rate" class="form-control font-monospace" 
                                       value="{{ old('tax_rate', $currentSettings['tax_rate']) }}">
                                <span class="input-group-text bg-light fw-bold">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4 mb-0 py-2.5 px-3 rounded-3 small d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle-fill fs-5 flex-shrink-0 text-info"></i>
                        <div>يتم تضمين هذه البيانات تلقائياً في قوالب الطباعة بحجم A4 والإيصالات الحرارية وسندات الدفع.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. PAYMENT METHODS MANAGEMENT -->
        <div class="col-12">
            <div class="clinic-card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="p-3 px-4 bg-light border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning-subtle text-warning p-2 rounded-3">
                            <i class="bi bi-credit-card-2-front fs-5"></i>
                        </span>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">قنوات وطرق الدفع والتحصيل (Payment Methods)</h5>
                            <span class="text-muted small" style="font-size: 0.75rem;">تفعيل أو تعطيل طرق السداد وتعيين الطريقة الافتراضية في شاشات المحاسبة والاستقبال</span>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <p class="text-muted small mb-3">
                        يمكنك التحكم بكل طريقة سداد على حدة، تعديل المسمى العربي والوصف، واختيار الطريقة التي يتم تحديدها تلقائياً عند فتح نافذة تحصيل جديدة:
                    </p>

                    <div class="row g-3">
                        @foreach($paymentMethods as $key => $method)
                        <div class="col-md-6 col-xl-4">
                            <div class="payment-card p-3 rounded-3 border h-100 bg-white shadow-sm position-relative {{ $method['enabled'] ? 'is-enabled' : 'is-disabled' }}">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="payment-icon-box bg-{{ $method['color'] }}-subtle text-{{ $method['color'] }}">
                                            <i class="bi {{ $method['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">{{ $method['name'] }}</h6>
                                            <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.68rem;">{{ $key }}</span>
                                        </div>
                                    </div>

                                    <!-- Enable / Disable Switch -->
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input method-enable-switch" type="checkbox" role="switch" 
                                               name="methods[{{ $key }}][enabled]" value="1" id="enableMethod_{{ $key }}"
                                               {{ $method['enabled'] ? 'checked' : '' }}>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1" style="font-size: 0.75rem;">الاسم المعروض في النظام:</label>
                                    <input type="text" name="methods[{{ $key }}][name]" class="form-control form-control-sm" 
                                           value="{{ $method['name'] }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1" style="font-size: 0.75rem;">وصف توضيحي:</label>
                                    <input type="text" name="methods[{{ $key }}][description]" class="form-control form-control-sm text-muted" 
                                           value="{{ $method['description'] }}">
                                </div>

                                <!-- Set as default radio -->
                                <div class="pt-2 border-top d-flex align-items-center justify-content-between">
                                    <label class="form-check-label small text-muted" for="defaultMethod_{{ $key }}">
                                        تعيين كطريقة افتراضية:
                                    </label>
                                    <div class="form-check m-0">
                                        <input class="form-check-input default-method-radio" type="radio" 
                                               name="default_payment_method" value="{{ $key }}" id="defaultMethod_{{ $key }}"
                                               {{ $currentSettings['default_payment_method'] === $key ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="p-3 px-4 bg-light border-top d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold">
                        <i class="bi bi-save2-fill me-1"></i> حفظ وتطبيق الإعدادات
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('styles')
<style>
.currency-preset-btn {
    transition: all 0.2s ease-in-out;
    border-color: #cbd5e1;
    background-color: #ffffff;
}
.currency-preset-btn:hover {
    border-color: #0d6efd;
    background-color: #f0f7ff;
    color: #0d6efd;
    transform: translateY(-1px);
}
.payment-card {
    transition: all 0.25s ease-in-out;
}
.payment-card.is-disabled {
    opacity: 0.65;
    background-color: #f8fafc !important;
}
.payment-card.is-enabled {
    border-color: #cbd5e1;
}
.payment-card:hover {
    border-color: #94a3b8;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
.payment-icon-box {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
}
.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

/* Dark Mode support */
body.dark-mode .payment-card {
    background-color: #1e293b !important;
    border-color: #334155;
}
body.dark-mode .payment-card.is-disabled {
    background-color: #151f2e !important;
}
body.dark-mode .currency-preset-btn {
    background-color: #1e293b;
    border-color: #334155;
    color: #cbd5e1;
}
body.dark-mode .currency-preset-btn:hover {
    background-color: #27354a;
    border-color: #3b82f6;
    color: #60a5fa;
}
body.dark-mode .bg-light {
    background-color: #172033 !important;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    function updateLivePreview() {
        const symbol = $('#currencySymbolInput').val() || 'ج.م';
        const code = ($('#currencyCodeInput').val() || 'EGP').toUpperCase();
        const position = $('input[name="currency_position"]:checked').val() || 'after';

        $('.preview-symbol').text(symbol);
        $('#previewBadge').text(code);

        if (position === 'before') {
            $('#previewFormattedAmount').text(symbol + ' 1,850.00');
        } else {
            $('#previewFormattedAmount').text('1,850.00 ' + symbol);
        }
    }

    // Quick Currency Preset Click
    $('.currency-preset-btn').on('click', function() {
        const symbol = $(this).data('symbol');
        const name = $(this).data('name');
        const code = $(this).data('code');

        $('#currencySymbolInput').val(symbol);
        $('#currencyNameInput').val(name);
        $('#currencyCodeInput').val(code);

        updateLivePreview();
    });

    // Inputs change event
    $('#currencySymbolInput, #currencyCodeInput').on('input', updateLivePreview);
    $('input[name="currency_position"]').on('change', updateLivePreview);

    // Payment card toggle switch
    $('.method-enable-switch').on('change', function() {
        const $card = $(this).closest('.payment-card');
        if ($(this).is(':checked')) {
            $card.removeClass('is-disabled').addClass('is-enabled');
        } else {
            $card.removeClass('is-enabled').addClass('is-disabled');
            // If was default, cannot uncheck default without changing default
            const $radio = $card.find('.default-method-radio');
            if ($radio.is(':checked')) {
                // Keep checked if default
                $(this).prop('checked', true);
                $card.removeClass('is-disabled').addClass('is-enabled');
                alert('لا يمكن تعطيل طريقة الدفع المعينة كطريقة افتراضية. يرجى اختيار طريقة افتراضية أخرى أولاً.');
            }
        }
    });

    // When default radio is checked, automatically enable its switch
    $('.default-method-radio').on('change', function() {
        const $card = $(this).closest('.payment-card');
        const $switch = $card.find('.method-enable-switch');
        $switch.prop('checked', true);
        $card.removeClass('is-disabled').addClass('is-enabled');
    });
});
</script>
@endpush
