<?php

namespace App\Jobs;

use App\Models\Alert;
use App\Events\AlertCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $alertData;

    /**
     * Create a new job instance.
     */
    public function __construct(array $alertData)
    {
        $this->alertData = $alertData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $alert = Alert::create($this->alertData);

        // Dispatch event to notify stakeholders
        AlertCreated::dispatch($alert);
    }
}
