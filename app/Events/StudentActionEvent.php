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
    public ?Student $student;
    public bool $status;

    public function __construct(string $action, ?Student $student = null, bool $status = true)
    {
        $this->action = $action;
        $this->student = $student;
        $this->status = $status;
    }
}
