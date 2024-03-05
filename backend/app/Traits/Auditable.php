<?php

namespace App\Traits;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::updated(function ($model) {
            if (!$model->isDirty()) {
                return;
            }

            $model->audits()->create([
                'snapshot' => json_encode($model->getAttributes()),
                'user_id' => auth()->id(),
            ]);
        });
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }
}
