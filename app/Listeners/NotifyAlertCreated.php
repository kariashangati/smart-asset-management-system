<?php

namespace App\Listeners;

use App\Events\AlertCreated;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AlertNotification;

class NotifyAlertCreated implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(AlertCreated $event): void
    {
        // Get managers for the asset's department
        $managers = User::whereHas('roles', function ($query) {
            $query->where('name', 'asset_manager');
        })
        ->where('department_id', $event->alert->asset->department_id)
        ->get();

        // Notify all managers
        Notification::send($managers, new AlertNotification($event->alert));
    }
}
