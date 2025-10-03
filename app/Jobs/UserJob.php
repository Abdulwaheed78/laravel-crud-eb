<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $action;
    protected ?int $userId;

    public function __construct(string $action, array $data = [], int $userId = null)
    {
        $this->action = $action;
        $this->userId = $userId;
    }

    public function handle()
    {
        Log::info("User job executed", [
            'action' => $this->action,
            'userId' => $this->userId,
        ]);
    }
}
