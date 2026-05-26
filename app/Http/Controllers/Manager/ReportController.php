<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function alerts()
    {
        $departmentId = Auth::user()->department_id;
        
        $alerts = Alert::whereHas('asset', function($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })->with(['asset', 'trackerDevice'])->orderBy('triggered_at', 'desc')->paginate(20);

        return view('admin.reports.alerts', compact('alerts'));
    }
}