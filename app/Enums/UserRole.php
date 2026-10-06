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
