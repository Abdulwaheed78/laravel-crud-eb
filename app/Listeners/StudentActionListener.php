<?php

namespace App\Listeners;

use App\Events\StudentActionEvent;
use Illuminate\Support\Facades\Log;

class StudentActionListener
{
    /**
     * Handle the event.
     */
    public function handle(StudentActionEvent $event): void
    {
        Log::info('🎯 StudentActionListener triggered', [
            'action' => $event->action,
            'status' => $event->status,
            'student' => $event->student,
        ]);
    }
}
