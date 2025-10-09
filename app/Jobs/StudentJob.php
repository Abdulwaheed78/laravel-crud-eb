<?php

namespace App\Jobs;

use App\Models\Student;
use App\Models\Notification;
use App\Helpers\StudentHelper;
use App\Events\StudentActionEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Throwable;

class StudentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $action;
    protected array $data;

    public function __construct(string $action, array $data = [])
    {
        $this->action = $action;
        $this->data = $data;
    }

    public function handle(): void
    {
        try {
            Log::info('🎯 Starting StudentJob', [
                'action' => $this->action,
                'data'   => $this->data,
            ]);

            switch ($this->action) {
                case 'create':
                    $this->handleCreate();
                    break;
                case 'update':
                    $this->handleUpdate();
                    break;
                case 'delete':
                    $this->handleDelete();
                    break;
                default:
                    throw new \Exception("Unknown StudentJob action: {$this->action}");
            }
        } catch (Throwable $e) {
            Log::error("❌ StudentJob failed: {$e->getMessage()}", [
                'action' => $this->action,
                'data'   => $this->data,
            ]);

            // 🔔 Notify error in MongoDB
            $this->notify(
                'Student Job Error',
                ucfirst($this->action) . ' Operation Failed',
                $e->getMessage()
            );

            // 🔥 Trigger global event (failed)
            event(new StudentActionEvent($this->action, null, false));
        }
    }

    /**
     * Handle create logic.
     */
    protected function handleCreate(): void
    {
        $validated = StudentHelper::validate($this->data);

        if (!$validated['status']) {
            throw new \Exception('Validation failed during student creation: ' . json_encode($validated['errors']));
        }

        $data = $validated['data'];
        $data['password'] = Hash::make($data['password'] ?? '123456');

        $student = Student::create($data);
        Log::info('✅ Student created successfully via Job.');

        event(new StudentActionEvent('created', $student->id, true));

        // ✅ Optional success notification
        $this->notify(
            'Student',
            'Student Created Successfully',
            "Student {$student->name} (ID: {$student->id}) has been added.",
            $student->id
        );
    }

    /**
     * Handle update logic.
     */
    protected function handleUpdate(): void
    {
        $validated = StudentHelper::validate($this->data);

        if (!$validated['status']) {
            throw new \Exception('Validation failed during student update: ' . json_encode($validated['errors']));
        }

        $student = Student::find($this->data['id']);
        if (!$student) {
            throw new \Exception("Student not found for update: {$this->data['id']}");
        }

        $data = $validated['data'];
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $student->update($data);
        Log::info('✏️ Student updated successfully via Job.');

        event(new StudentActionEvent('updated', $student->id, true));

        // ✅ Success notification
        $this->notify(
            'Student',
            'Student Updated Successfully',
            "Student {$student->name} (ID: {$student->id}) updated.",
            $student->id
        );
    }

    /**
     * Handle delete logic.
     */
    protected function handleDelete(): void
    {
        if (empty($this->data['id'])) {
            throw new \Exception('Delete action called without ID');
        }

        $student = Student::find($this->data['id']);
        if (!$student) {
            throw new \Exception("Student not found for delete: {$this->data['id']}");
        }

        $student->update(['is_active' => 0]);
        Log::info('🚫 Student deactivated successfully via Job.', ['id' => $this->data['id']]);

        event(new StudentActionEvent('deactivated', $student->id, true));

        // ✅ Success notification
        $this->notify(
            'Student',
            'Student Deactivated',
            "Student {$student->name} (ID: {$student->id}) has been deactivated.",
            $student->id
        );
    }

    /**
     * 🔔 Create a notification safely.
     */
    private function notify(string $type, string $title, string $message, ?string $relatedId = null): void
    {
        try {
            Notification::create([
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'related_id' => $relatedId,
                'related_model' => Student::class,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (Throwable $e) {
            Log::error('❗ Failed to create notification: ' . $e->getMessage());
        }
    }
}
