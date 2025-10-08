<?php

namespace App\Listeners;

use App\Events\StudentActionEvent;
use Illuminate\Support\Facades\Log;
use App\Models\Student;
use App\Models\Notification;

class StudentActionListener
{
    /**
     * Handle the event.
     */
    public function handle(StudentActionEvent $event): void
    {
        $student = Student::find($event->studentId);

        if ($student) {
            // Example log entry or DB insert
            Log::info('🎯 StudentActionListener triggered', [
                'action' => $event->action,
                'status' => $event->status,
                'studentId' => $student->id,
                'studentName' => $student->name,
            ]);


            $message = $student
                ? "Student {$student->first_name} {$student->last_name} was {$event->action}."
                : "Student with ID {$event->studentId} was {$event->action}.";

            Notification::create([
                'type' => 'student_action',
                'title' => "Student {$event->action}",
                'message' => $message,
                'related_id' => $event->studentId,
                'related_model' => Student::class,
                'is_read' => false,
            ]);
        } else {
            Log::warning('⚠️ Student not found for ID: ' . $event->studentId);
        }
    }
}
