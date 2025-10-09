<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Events\UserActionEvent;

class UserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $action;
    protected array $data;
    protected ?string $userId;

    public function __construct(string $action, array $data = [], ?string $userId = null)
    {
        $this->action = $action;
        $this->data   = $data;
        $this->userId = (string) $userId; // always cast to string
    }

    public function handle(): void
    {
        Log::info("UserJob executed", [
            'action' => $this->action,
            'userId' => $this->userId,
            'data'   =>json_encode($this->data),
        ]);
        Log::info("user event called");
        event(new UserActionEvent($this->action, $this->userId, true));
    }
}
