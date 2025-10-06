<?php

namespace App\Jobs;

use App\Models\Student;
use App\Helpers\StudentHelper;
use App\Events\StudentJobCompleted;
use App\Events\StudentJobFailed;
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
            // 👇 Simulate slow processing (for demo/testing)
            sleep(20);

            Log::info('Processing StudentJob', [
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

            // ✅ Fire job completed event
            event(new StudentJobCompleted($this->action, $this->data));

        } catch (Throwable $e) {
            // ❌ Log and fire failure event
            Log::error("StudentJob failed: {$e->getMessage()}", [
                'action' => $this->action,
                'data'   => $this->data,
                'trace'  => $e->getTraceAsString(),
            ]);

            event(new StudentJobFailed($this->action, $this->data, $e));
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

        Student::create($data);

        Log::info('✅ Student created successfully via Job.');
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
        Log::info('✅ Student updated successfully via Job.');
    }

    /**
     * Handle delete logic.
     */
    protected function handleDelete(): void
    {
        if (empty($this->data['id'])) {
            throw new \Exception('Delete action called without ID');
        }

        Student::destroy($this->data['id']);
        Log::info('🗑️ Student deleted successfully via Job.', ['id' => $this->data['id']]);
    }
}
