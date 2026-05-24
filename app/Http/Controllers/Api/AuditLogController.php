<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Spatie\ActivityLog\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AuditLogsExport;

class AuditLogController extends Controller
{
    /**
     * Get audit logs
     * GET /api/audit-logs
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Activity::class);

        $query = Activity::query();

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->input('user_id'));
        }

        // Filter by model
        if ($request->filled('model')) {
            $query->where('subject_type', $request->input('model'));
        }

        // Filter by event
        if ($request->filled('event')) {
            $query->where('description', $request->input('event'));
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $logs = $query->with('causer')
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    /**
     * Export audit logs to CSV
     * GET /api/audit-logs/export
     */
    public function export(Request $request): Response
    {
        $this->authorize('viewAny', Activity::class);

        $filters = $request->only('user_id', 'model', 'event', 'date_from', 'date_to');

        return Excel::download(new AuditLogsExport($filters), 'audit-logs-' . now()->format('Y-m-d') . '.csv');
    }

    /**
     * Get audit log details
     * GET /api/audit-logs/{id}
     */
    public function show(Activity $activity): JsonResponse
    {
        $this->authorize('view', $activity);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $activity->id,
                'description' => $activity->description,
                'subject_type' => $activity->subject_type,
                'subject_id' => $activity->subject_id,
                'causer_id' => $activity->causer_id,
                'causer_name' => $activity->causer->name ?? null,
                'properties' => $activity->properties,
                'created_at' => $activity->created_at,
            ],
        ]);
    }
}
