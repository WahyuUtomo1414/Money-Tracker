<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait AuditedBySoftDelete
{
    protected static function resolveAuditUserId(): int
    {
        return Auth::id() ?? 1;
    }

    public static function bootAuditedBySoftDelete()
    {
        static::creating(function ($model) {
            if (!$model->isDirty('created_by')) {
                $model->created_by = static::resolveAuditUserId();
            }
        });

        static::updating(function ($model) {
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = static::resolveAuditUserId();
            }
        });

        static::deleting(function ($model) {
            if ($model->isForceDeleting()) {
                return;
            }

            $model->deleted_by = static::resolveAuditUserId();
            $model->saveQuietly();
        });

        static::restoring(function ($model) {
            $model->deleted_by = null;
            $model->saveQuietly();
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCreatorNameAttribute()
    {
        return $this->createdBy?->name;
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getUpdatedNameAttribute()
    {
        return $this->updatedBy?->name;
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function getDeletedNameAttribute()
    {
        return $this->deletedBy?->name;
    }
}
