<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get user-friendly Arabic label for the expense category.
     */
    public function getCategoryLabel(): string
    {
        return match ($this->category) {
            'materials' => 'مواد ومستهلكات طبية',
            'rent' => 'إيجار العيادة',
            'utilities' => 'فواتير وكهرباء ومياه',
            'maintenance' => 'صيانة أجهزة ومعدات',
            'salaries' => 'رواتب ومستحقات',
            'marketing' => 'تسويق وإعلانات',
            default => 'نثريات ومصاريف أخرى',
        };
    }

    /**
     * Get bootstrap badge color class for category.
     */
    public function getCategoryBadgeClass(): string
    {
        return match ($this->category) {
            'materials' => 'bg-primary-subtle text-primary border-primary-subtle',
            'rent' => 'bg-danger-subtle text-danger border-danger-subtle',
            'utilities' => 'bg-warning-subtle text-dark border-warning-subtle',
            'maintenance' => 'bg-info-subtle text-info border-info-subtle',
            'salaries' => 'bg-purple-subtle text-purple border-purple-subtle',
            'marketing' => 'bg-success-subtle text-success border-success-subtle',
            default => 'bg-secondary-subtle text-dark border-secondary-subtle',
        };
    }

    /**
     * Get user-friendly Arabic label for payment method.
     */
    public function getPaymentMethodLabel(): string
    {
        return match ($this->payment_method) {
            'cash' => 'نقداً (كاش)',
            'card' => 'مدى / بطاقة بنكية',
            'bank_transfer' => 'تحويل بنكي',
            default => $this->payment_method,
        };
    }
}
