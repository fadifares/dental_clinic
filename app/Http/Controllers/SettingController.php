<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display clinic system settings management page.
     */
    public function index(): View
    {
        $currencies = [
            [
                'code' => 'EGP',
                'name' => 'جنيه مصري',
                'symbol' => 'ج.م',
                'country' => 'مصر 🇪🇬',
            ],
            [
                'code' => 'SAR',
                'name' => 'ريال سعودي',
                'symbol' => 'ر.س',
                'country' => 'المملكة العربية السعودية 🇸🇦',
            ],
            [
                'code' => 'AED',
                'name' => 'درهم إماراتي',
                'symbol' => 'د.إ',
                'country' => 'الإمارات العربية المتحدة 🇦🇪',
            ],
            [
                'code' => 'KWD',
                'name' => 'دينار كويتي',
                'symbol' => 'د.ك',
                'country' => 'الكويت 🇰🇼',
            ],
            [
                'code' => 'QAR',
                'name' => 'ريال قطري',
                'symbol' => 'ر.ق',
                'country' => 'قطر 🇶🇦',
            ],
            [
                'code' => 'BHD',
                'name' => 'دينار بحريني',
                'symbol' => 'د.ب',
                'country' => 'البحرين 🇧🇭',
            ],
            [
                'code' => 'OMR',
                'name' => 'ريال عماني',
                'symbol' => 'ر.ع',
                'country' => 'عمان 🇴🇲',
            ],
            [
                'code' => 'JOD',
                'name' => 'دينار أردني',
                'symbol' => 'د.أ',
                'country' => 'الأردن 🇯🇴',
            ],
            [
                'code' => 'USD',
                'name' => 'دولار أمريكي',
                'symbol' => '$',
                'country' => 'الولايات المتحدة 🇺🇸',
            ],
            [
                'code' => 'EUR',
                'name' => 'يورو أوروبي',
                'symbol' => '€',
                'country' => 'الاتحاد الأوروبي 🇪🇺',
            ],
        ];

        $currentSettings = [
            'currency_symbol' => Setting::currencySymbol(),
            'currency_name' => Setting::currencyName(),
            'currency_code' => Setting::currencyCode(),
            'currency_position' => Setting::get('currency_position', 'after'),
            'clinic_name' => Setting::get('clinic_name', 'عيادة دنتال برو لطب وجراحة الأسنان'),
            'clinic_phone' => Setting::get('clinic_phone', '+20 100 000 0000'),
            'clinic_email' => Setting::get('clinic_email', 'info@dentalcare.com'),
            'clinic_address' => Setting::get('clinic_address', 'شارع النصر، المعادي، القاهرة'),
            'tax_number' => Setting::get('tax_number', '300123456789003'),
            'tax_rate' => Setting::get('tax_rate', '0.00'),
            'default_payment_method' => Setting::get('default_payment_method', 'card'),
        ];

        $paymentMethods = Setting::paymentMethods();

        return view('clinic.settings.index', compact('currencies', 'currentSettings', 'paymentMethods'));
    }

    /**
     * Update clinic system settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'currency_symbol' => ['required', 'string', 'max:20'],
            'currency_name' => ['required', 'string', 'max:100'],
            'currency_code' => ['required', 'string', 'max:10'],
            'currency_position' => ['required', 'in:after,before'],
            'default_payment_method' => ['required', 'string'],
            'clinic_name' => ['nullable', 'string', 'max:255'],
            'clinic_phone' => ['nullable', 'string', 'max:50'],
            'clinic_email' => ['nullable', 'email', 'max:100'],
            'clinic_address' => ['nullable', 'string', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'methods' => ['nullable', 'array'],
        ]);

        // 1. Update Currency & Financial Settings
        Setting::set('currency_symbol', trim($validated['currency_symbol']), 'financial');
        Setting::set('currency_name', trim($validated['currency_name']), 'financial');
        Setting::set('currency_code', strtoupper(trim($validated['currency_code'])), 'financial');
        Setting::set('currency_position', $validated['currency_position'], 'financial');

        // 2. Update Clinic Info Settings
        if (isset($validated['clinic_name'])) {
            Setting::set('clinic_name', trim($validated['clinic_name']), 'clinic');
        }
        if (isset($validated['clinic_phone'])) {
            Setting::set('clinic_phone', trim($validated['clinic_phone']), 'clinic');
        }
        if (isset($validated['clinic_email'])) {
            Setting::set('clinic_email', trim($validated['clinic_email']), 'clinic');
        }
        if (isset($validated['clinic_address'])) {
            Setting::set('clinic_address', trim($validated['clinic_address']), 'clinic');
        }
        if (isset($validated['tax_number'])) {
            Setting::set('tax_number', trim($validated['tax_number']), 'clinic');
        }
        if (isset($validated['tax_rate'])) {
            Setting::set('tax_rate', (float) $validated['tax_rate'], 'clinic');
        }

        // 3. Update Payment Methods
        $defaultCatalog = Setting::defaultPaymentMethods();
        $submittedMethods = $validated['methods'] ?? [];
        $defaultMethodKey = $validated['default_payment_method'];

        $updatedMethods = [];
        foreach ($defaultCatalog as $key => $methodData) {
            $isEnabled = isset($submittedMethods[$key]['enabled']) && (bool) $submittedMethods[$key]['enabled'];
            $customName = ! empty($submittedMethods[$key]['name']) ? trim($submittedMethods[$key]['name']) : $methodData['name'];
            $customDesc = ! empty($submittedMethods[$key]['description']) ? trim($submittedMethods[$key]['description']) : $methodData['description'];

            $isDefault = ($key === $defaultMethodKey);
            // Default method must always be enabled
            if ($isDefault) {
                $isEnabled = true;
            }

            $updatedMethods[$key] = array_merge($methodData, [
                'name' => $customName,
                'description' => $customDesc,
                'enabled' => $isEnabled,
                'is_default' => $isDefault,
            ]);
        }

        Setting::set('default_payment_method', $defaultMethodKey, 'payments');
        Setting::set('payment_methods', $updatedMethods, 'payments');

        Setting::clearCache();

        return redirect()->route('clinic.settings.index')
            ->with('success', 'تم حفظ وتحديث إعدادات العملة وطرق الدفع والعيادة بنجاح.');
    }
}
