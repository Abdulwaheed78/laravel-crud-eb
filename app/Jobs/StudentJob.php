<?php

namespace App\Jobs;

use App\Models\Student;
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

    /**
     * Create a new job instance.
     */
    public function __construct(string $action, array $data = [])
    {
        $this->action = $action;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            //sleep(5); // Optional delay for demo

            Log::info('🎯 Starting StudentJob', [
                'action' => $this->action,
                'data'   => $this->data
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
                'trace'  => $e->getTraceAsString(),
            ]);

            // Fire failure event globally
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

        // Fire success event
        event(new StudentActionEvent('created', $student, true));
        Log::info('🚀 StudentActionEvent fired for CREATE.');
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

        // Fire success event
        event(new StudentActionEvent('updated', $student, true));
        Log::info('🚀 StudentActionEvent fired for UPDATE.');
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

        $student->delete();
        Log::info('🗑️ Student deleted successfully via Job.', ['id' => $this->data['id']]);

        // Fire success event
        event(new StudentActionEvent('deleted', $student, true));
        Log::info('🚀 StudentActionEvent fired for DELETE.');
    }
}
