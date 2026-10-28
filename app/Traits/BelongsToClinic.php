<?php

namespace App\Traits;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToClinic
{
    /**
     * Boot the trait and apply the Clinic Global Scope.
     */
    protected static function bootBelongsToClinic(): void
    {
        static::addGlobalScope('clinic', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();
                // In clinic portal, always isolate by current user's clinic
                if ($user->clinic_id && ! request()->routeIs('appmanager.*')) {
                    $builder->where($builder->getModel()->getTable().'.clinic_id', $user->clinic_id);
                }
            }
        });

        static::creating(function ($model) {
            if (empty($model->clinic_id) && $model->getTable() !== 'settings') {
                $model->clinic_id = (auth()->check() && auth()->user()->clinic_id) ? auth()->user()->clinic_id : 1;
            }
        });
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
