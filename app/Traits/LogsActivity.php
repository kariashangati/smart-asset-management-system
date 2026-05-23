<?php

namespace App\Traits;

use App\Services\AuditLogService;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        $auditService = app(AuditLogService::class);

        static::created(function ($model) use ($auditService) {
            $identifier = $model->getLogIdentifier();
            $auditService->logCreated(class_basename($model), $identifier);
        });

        static::updated(function ($model) use ($auditService) {
            $identifier = $model->getLogIdentifier();
            $auditService->logUpdated(class_basename($model), $identifier);
        });

        static::deleted(function ($model) use ($auditService) {
            $identifier = $model->getLogIdentifier();
            $auditService->logDeleted(class_basename($model), $identifier);
        });
    }

    public function getLogIdentifier()
    {
        return $this->name ?? $this->asset_code ?? $this->device_code ?? $this->email ?? $this->id;
    }
}