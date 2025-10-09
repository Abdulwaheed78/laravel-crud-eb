<?php

namespace App\Events;

use App\Models\Student;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class UserActionEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $action;
    public ?string $userId;
    public bool $status;

    public function __construct(string $action, ?string $userId = null, bool $status = true)
    {
        $this->action = $action;
        $this->userId = $userId;
        $this->status = $status;
    }
}
