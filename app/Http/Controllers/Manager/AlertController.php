<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Services\AlertService;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    protected $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    public function index()
    {
        $alerts = Alert::with(['asset', 'trackerDevice'])->orderBy('triggered_at', 'desc')->get();
        return view('manager.alerts.index', compact('alerts'));
    }

    public function show(Alert $alert)
    {
        return view('manager.alerts.show', compact('alert'));
    }

    public function markAsRead(Alert $alert)
    {
        $this->alertService->markAsRead($alert);
        return redirect()->back()->with('success', 'Alert marked as read.');
    }

    public function markAsResolved(Alert $alert)
    {
        $this->alertService->markAsResolved($alert);
        return redirect()->back()->with('success', 'Alert marked as resolved.');
    }

    public function destroy(Alert $alert)
    {
        $alert->delete();
        return redirect()->route('manager.alerts.index')->with('success', 'Alert deleted.');
    }
}