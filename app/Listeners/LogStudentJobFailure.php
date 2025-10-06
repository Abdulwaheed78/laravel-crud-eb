<?php

namespace App\Listeners;

use App\Events\StudentJobFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
class LogStudentJobFailure
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StudentJobFailed $event)
    {
        Log::error("StudentJob Failed: {$event->action}", [
            'data' => $event->data,
            'error' => $event->exception->getMessage()
        ]);
    }
}
