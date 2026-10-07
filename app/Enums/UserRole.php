<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Doctor = 'doctor';
    case Receptionist = 'receptionist';
    case Accountant = 'accountant';

    /**
     * Get user-friendly Arabic label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'مدير النظام (Admin)',
            self::Doctor => 'طبيب أسنان (Doctor)',
            self::Receptionist => 'موظف استقبال (Receptionist)',
            self::Accountant => 'محاسب مالي (Accountant)',
        };
    }

    /**
     * Get Arabic title for role cards.
     */
    public function title(): string
    {
        return match ($this) {
            self::Admin => 'مدير النظام',
            self::Doctor => 'طبيب أسنان',
            self::Receptionist => 'موظف استقبال',
            self::Accountant => 'محاسب مالي',
        };
    }

    /**
     * Get English subtitle for role cards.
     */
    public function subtitle(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Doctor => 'Doctor',
            self::Receptionist => 'Receptionist',
            self::Accountant => 'Accountant',
        };
    }

    /**
     * Get Bootstrap icon class for the role.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Admin => 'bi-shield-shaded',
            self::Doctor => 'bi-heart-pulse',
            self::Receptionist => 'bi-headset',
            self::Accountant => 'bi-cash-coin',
        };
    }

    /**
     * Get Bootstrap contextual color class for badges and borders.
     */
    public function colorClass(): string
    {
        return match ($this) {
            self::Admin => 'danger',
            self::Doctor => 'primary',
            self::Receptionist => 'warning',
            self::Accountant => 'success',
        };
    }

    /**
     * Check if role has access to financial records.
     */
    public function canAccessFinances(): bool
    {
        return in_array($this, [self::Admin, self::Accountant]);
    }

    /**
     * Check if role has access to clinical charts & treatment plans.
     */
    public function canAccessClinicalCharts(): bool
    {
        return in_array($this, [self::Admin, self::Doctor]);
    }

    /**
     * Check if role can manage appointments.
     */
    public function canManageAppointments(): bool
    {
        return in_array($this, [self::Admin, self::Receptionist, self::Doctor]);
    }
}
