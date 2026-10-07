<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'roles', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'roles' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Sync roles and keep single role attribute in sync with primary role.
     */
    public function setRolesAttribute($value): void
    {
        $array = is_array($value) ? array_values(array_unique($value)) : [$value];
        $this->attributes['roles'] = json_encode($array);
        if (! empty($array)) {
            $this->attributes['role'] = $array[0];
        }
    }

    /**
     * Get array of all string role identifiers assigned to the user.
     *
     * @return string[]
     */
    public function getRolesArray(): array
    {
        $roles = $this->roles ?? [];
        if (! is_array($roles) || empty($roles)) {
            $raw = $this->getRawOriginal('role');

            return $raw ? [$raw] : ['admin'];
        }

        return array_values(array_unique($roles));
    }

    /**
     * Get array of UserRole enum instances assigned to the user.
     *
     * @return UserRole[]
     */
    public function getRoleInstances(): array
    {
        $instances = [];
        foreach ($this->getRolesArray() as $val) {
            $enum = UserRole::tryFrom($val);
            if ($enum) {
                $instances[] = $enum;
            }
        }

        return ! empty($instances) ? $instances : [UserRole::Admin];
    }

    /**
     * Get array of human-readable Arabic role labels.
     *
     * @return string[]
     */
    public function getRoleLabels(): array
    {
        return array_map(fn (UserRole $r) => $r->label(), $this->getRoleInstances());
    }

    /**
     * Get joined string of all role labels.
     */
    public function getRoleLabelsString(): string
    {
        return implode(' • ', $this->getRoleLabels());
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::Admin);
    }

    public function isDoctor(): bool
    {
        return $this->hasRole(UserRole::Doctor);
    }

    public function isReceptionist(): bool
    {
        return $this->hasRole(UserRole::Receptionist);
    }

    public function isAccountant(): bool
    {
        return $this->hasRole(UserRole::Accountant);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(UserRole|string $role): bool
    {
        $needle = $role instanceof UserRole ? $role->value : $role;

        return in_array($needle, $this->getRolesArray(), true);
    }

    /**
     * Check if user has any of the given roles.
     *
     * @param  array<UserRole|string>  $roles
     */
    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the doctor profile associated with the user.
     */
    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    /**
     * The "booted" method of the model.
     * Automatically keeps doctor records synchronized when user name or email changes.
     */
    protected static function booted(): void
    {
        static::saved(function (User $user): void {
            if ($user->wasChanged('name')) {
                // 1. Direct update for any doctor record linked by user_id
                $updatedCount = Doctor::where('user_id', $user->id)->update([
                    'name' => $user->name,
                ]);

                // 2. If no record was updated by user_id, search by email or previous name to link and update
                if ($updatedCount === 0) {
                    Doctor::whereNull('user_id')
                        ->where(function ($query) use ($user) {
                            $query->where('email', $user->email)
                                ->orWhere('email', $user->getOriginal('email'))
                                ->orWhere('name', $user->getOriginal('name'));
                        })
                        ->update([
                            'name' => $user->name,
                            'user_id' => $user->id,
                        ]);
                }
            }

            if ($user->wasChanged('email')) {
                Doctor::where('user_id', $user->id)->update([
                    'email' => $user->email,
                ]);
            }
        });
    }
}
