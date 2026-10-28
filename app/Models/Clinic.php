<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Clinic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'phone',
        'email',
        'address',
        'status',
        'max_users',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'max_users' => 'integer',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function primaryOwner(): HasOne
    {
        return $this->hasOne(User::class)->where('is_primary_owner', true);
    }

    public function activeUsersCount(): int
    {
        return $this->users()->where('is_active', true)->count();
    }

    public function canAddUser(): bool
    {
        return $this->activeUsersCount() < $this->max_users;
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function pageVisits(): HasMany
    {
        return $this->hasMany(ClinicPageVisit::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ClinicUserActivity::class);
    }
}
