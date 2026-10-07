<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    /**
     * Runtime cache for settings in current process.
     *
     * @var array<string, mixed>
     */
    protected static array $runtimeCache = [];

    /**
     * Get a setting by key with an optional default value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, self::$runtimeCache)) {
            return self::$runtimeCache[$key];
        }

        $setting = static::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        $value = match ($setting->type) {
            'json', 'array' => json_decode($setting->value, true) ?? $default,
            'boolean', 'bool' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'integer', 'int' => (int) $setting->value,
            'float' => (float) $setting->value,
            default => $setting->value,
        };

        self::$runtimeCache[$key] = $value;

        return $value;
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, mixed $value, string $group = 'general', ?string $type = null): self
    {
        if ($type === null) {
            if (is_array($value)) {
                $type = 'json';
            } elseif (is_bool($value)) {
                $type = 'boolean';
            } elseif (is_int($value)) {
                $type = 'integer';
            } elseif (is_float($value)) {
                $type = 'float';
            } else {
                $type = 'string';
            }
        }

        $serializedValue = match ($type) {
            'json', 'array' => json_encode($value, JSON_UNESCAPED_UNICODE),
            'boolean', 'bool' => $value ? '1' : '0',
            default => (string) $value,
        };

        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $serializedValue,
                'group' => $group,
                'type' => $type,
            ]
        );

        self::$runtimeCache[$key] = $value;

        return $setting;
    }

    /**
     * Clear runtime cache.
     */
    public static function clearCache(): void
    {
        self::$runtimeCache = [];
    }

    /**
     * Get clinic currency symbol.
     */
    public static function currencySymbol(): string
    {
        return (string) static::get('currency_symbol', 'ج.م');
    }

    /**
     * Get clinic currency code.
     */
    public static function currencyCode(): string
    {
        return (string) static::get('currency_code', 'EGP');
    }

    /**
     * Get clinic currency name in Arabic.
     */
    public static function currencyName(): string
    {
        return (string) static::get('currency_name', 'جنيه مصري');
    }

    /**
     * Format money with currency symbol and position.
     */
    public static function formatMoney(float|int $amount, int $decimals = 2): string
    {
        $formattedNumber = number_format((float) $amount, $decimals);
        $symbol = static::currencySymbol();
        $position = static::get('currency_position', 'after');

        return $position === 'before'
            ? "{$symbol} {$formattedNumber}"
            : "{$formattedNumber} {$symbol}";
    }

    /**
     * Get default payment methods catalog.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function defaultPaymentMethods(): array
    {
        return [
            'cash' => [
                'key' => 'cash',
                'name' => 'نقداً (كاش)',
                'description' => 'الدفع المباشر بالنقود في الاستقبال',
                'icon' => 'bi-cash-stack',
                'color' => 'success',
                'enabled' => true,
                'is_default' => true,
            ],
            'card' => [
                'key' => 'card',
                'name' => 'بطاقة بنكية / شبكة (POS)',
                'description' => 'بطاقات مدى، فيزا، ماستركارد عبر أجهزة نقاط البيع',
                'icon' => 'bi-credit-card-2-front-fill',
                'color' => 'primary',
                'enabled' => true,
                'is_default' => false,
            ],
            'bank_transfer' => [
                'key' => 'bank_transfer',
                'name' => 'تحويل بنكي مباشر',
                'description' => 'التحويل المالي المباشر إلى حساب العيادة البنكي',
                'icon' => 'bi-bank2',
                'color' => 'info',
                'enabled' => true,
                'is_default' => false,
            ],
            'installments' => [
                'key' => 'installments',
                'name' => 'أقساط وخطة دفعات',
                'description' => 'جدولة سداد خطط العلاج الطويلة على دفعات ميسرة',
                'icon' => 'bi-calendar-range-fill',
                'color' => 'warning',
                'enabled' => true,
                'is_default' => false,
            ],
            'insurance' => [
                'key' => 'insurance',
                'name' => 'تأمين طبي',
                'description' => 'المطالبات المباشرة عبر شركات التأمين المعتمدة',
                'icon' => 'bi-shield-check',
                'color' => 'secondary',
                'enabled' => false,
                'is_default' => false,
            ],
            'wallet' => [
                'key' => 'wallet',
                'name' => 'محفظة إلكترونية (فودافون كاش / إنستاباي / STC Pay)',
                'description' => 'الدفع الفوري عبر المحافظ الذكية وتطبيقات الدفع السريع',
                'icon' => 'bi-phone-fill',
                'color' => 'dark',
                'enabled' => false,
                'is_default' => false,
            ],
        ];
    }

    /**
     * Get all configured payment methods (merged with defaults).
     *
     * @return array<string, array<string, mixed>>
     */
    public static function paymentMethods(): array
    {
        $saved = static::get('payment_methods', []);
        $defaults = static::defaultPaymentMethods();

        if (empty($saved) || ! is_array($saved)) {
            return $defaults;
        }

        $merged = [];
        foreach ($defaults as $key => $defaultData) {
            if (isset($saved[$key])) {
                $merged[$key] = array_merge($defaultData, $saved[$key]);
            } else {
                $merged[$key] = $defaultData;
            }
        }

        foreach ($saved as $key => $savedData) {
            if (! isset($merged[$key])) {
                $merged[$key] = $savedData;
            }
        }

        return $merged;
    }

    /**
     * Get only active/enabled payment methods.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function enabledPaymentMethods(): array
    {
        return array_filter(static::paymentMethods(), fn ($m) => ! empty($m['enabled']));
    }
}
