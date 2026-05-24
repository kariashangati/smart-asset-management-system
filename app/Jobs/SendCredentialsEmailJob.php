<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserCredential;
use App\Notifications\SendUserCredentialsNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCredentialsEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private UserCredential $credential)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->credential->user;
        if ($user && $user->email_notifications_enabled) {
            $user->notify(new SendUserCredentialsNotification($this->credential));
        }
    }
}
