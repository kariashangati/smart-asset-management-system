<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AuditLogsExport;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    /**
     * Get paginated audit logs with filters
     * GET /api/audit-logs
     */
    public function index(Request $request): JsonResponse
    {
        // Only admins can view audit logs
        if (!auth()->user()?->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $query = Activity::query()->with('causer');

        // Filter by user/causer
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->input('user_id'));
        }

        // Filter by subject model type
        if ($request->filled('model')) {
            $query->where('subject_type', $request->input('model'));
        }

        // Filter by event/description
        if ($request->filled('event')) {
            $query->where('description', $request->input('event'));
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => $logs->items(),
            'pagination' => [
                'total'        => $logs->total(),
                'per_page'     => $logs->perPage(),
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
            ],
        ]);
    }

    /**
     * Export audit logs to CSV
     * GET /api/audit-logs/export
     */
    public function export(Request $request): Response
    {
        if (!auth()->user()?->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $filters = $request->only('user_id', 'model', 'event', 'date_from', 'date_to');

        return Excel::download(
            new AuditLogsExport($filters),
            'audit-logs-' . now()->format('Y-m-d') . '.csv'
        );
    }

    /**
     * Get single audit log detail
     * GET /api/audit-logs/{id}
     */
    public function show(Activity $activity): JsonResponse
    {
        if (!auth()->user()?->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'           => $activity->id,
                'description'  => $activity->description,
                'subject_type' => $activity->subject_type,
                'subject_id'   => $activity->subject_id,
                'causer_id'    => $activity->causer_id,
                'causer_name'  => $activity->causer?->name,
                'properties'   => $activity->properties,
                'created_at'   => $activity->created_at,
            ],
        ]);
    }
}
