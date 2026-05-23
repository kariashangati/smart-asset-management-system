<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuditLogService
{
    public function log($action, $module, $description, $userId = null)
    {
        $request = request();
        return AuditLog::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    public function logCreated($module, $identifier)
    {
        $this->log('created', $module, "{$module} '{$identifier}' was created.");
    }

    public function logUpdated($module, $identifier)
    {
        $this->log('updated', $module, "{$module} '{$identifier}' was updated.");
    }

    public function logDeleted($module, $identifier)
    {
        $this->log('deleted', $module, "{$module} '{$identifier}' was deleted.");
    }

    public function getLogs($filters = [])
    {
        $query = AuditLog::with('user')->latest();
        if (!empty($filters['module'])) {
            $query->where('module', $filters['module']);
        }
        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        return $query->get();
    }
}