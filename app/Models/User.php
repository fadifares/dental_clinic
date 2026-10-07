<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
}
