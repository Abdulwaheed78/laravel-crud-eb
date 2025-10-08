<?php

namespace App\Events;

use App\Models\Student;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class StudentActionEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $action;
    public ?string $studentId;
    public bool $status;

    public function __construct(string $action, ?string $studentId = null, bool $status = true)
    {
        $this->action = $action;
        $this->studentId = $studentId;
        $this->status = $status;
    }
}
