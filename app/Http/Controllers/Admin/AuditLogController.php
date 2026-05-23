<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    protected $auditLogService;

    public function __construct(AuditLogService $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }

    public function index(Request $request)
    {
        $filters = [
            'module' => $request->get('module'),
            'action' => $request->get('action'),
            'user_id' => $request->get('user_id'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];
        $logs = $this->auditLogService->getLogs($filters);
        $modules = AuditLog::distinct()->pluck('module');
        $actions = ['created', 'updated', 'deleted'];
        return view('admin.audit-logs.index', compact('logs', 'modules', 'actions', 'filters'));
    }
}