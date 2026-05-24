<?php

namespace App\Broadcasting;

use App\Models\User;
use Illuminate\Broadcasting\Channel;

class PrivateChannelBroadcaster
{
    /**
     * Authenticate the user's access to private channels.
     */
    public function authenticate(User $user, string $channelName): bool|Channel
    {
        $parts = explode('.', $channelName);

        if (count($parts) === 2) {
            [$type, $id] = $parts;

            // Asset channel - only authorized users can access
            if ($type === 'asset') {
                return $user->can('view', \App\Models\Asset::findOrFail($id));
            }

            // Department channel - users in that department or admin
            if ($type === 'department') {
                return $user->isAdmin() || $user->department_id == $id;
            }

            // Geofence channel - admin or department manager
            if ($type === 'geofence') {
                return $user->isAdmin();
            }
        }

        // Admin can access alerts and breaches channels
        if (in_array($channelName, ['alerts', 'breaches', 'locations'])) {
            return $user->isAdmin();
        }

        return false;
    }
}
