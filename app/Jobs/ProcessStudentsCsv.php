<?php

namespace App\Jobs;

use App\Models\Student;
use App\Helpers\StudentHelper;
use App\Models\Notification;
use App\Events\StudentActionEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProcessStudentsCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function handle(): void
    {
        try {
            sleep(20); // optional: delay for debugging
            Log::info("Processing CSV: {$this->path}");

            $file = Storage::disk('public')->path($this->path);

            if (!file_exists($file)) {
                Log::error("CSV file not found: {$this->path}");
                $this->notify('Csv Upload Error', 'CSV File Missing', "File not found: {$this->path}");
                return;
            }

            // Read file & clean BOM
            $content = preg_replace('/^\xEF\xBB\xBF/', '', file_get_contents($file));
            $lines = array_filter(array_map('trim', explode("\n", $content)));

            // 2️Empty CSV
            if (empty($lines)) {
                Log::warning("CSV is empty: {$this->path}");
                $this->notify('Csv Upload Error', 'Empty CSV File', "No data found in {$this->path}");
                return;
            }

            // Expected columns
            $header = [
                'first_name', 'last_name', 'email', 'phone', 'roll_number', 'age', 'gender',
                'date_of_birth', 'admission_date', 'class_time', 'address', 'bio', 'course',
                'department', 'batch', 'is_active', 'has_scholarship', 'grade', 'website',
                'favorite_color', 'password', 'profile_photo'
            ];

            foreach ($lines as $index => $line) {
                // Skip header
                if ($index === 0 && str_contains(strtolower($line), 'first_name')) {
                    continue;
                }

                $rowData = str_getcsv($line);

                if (count(array_filter($rowData)) === 0) {
                    continue; // skip empty lines
                }

                $rowData = array_pad($rowData, count($header), null);
                $row = array_combine($header, $rowData);

                // Validate row
                $validated = StudentHelper::validate($row);
                if (!$validated['status']) {
                    $errorMessages = collect($validated['errors']->toArray())
                        ->map(fn($msgs, $field) => $field . ': ' . implode(', ', $msgs))
                        ->implode(' | ');

                    $this->notify(
                        'Csv Upload Error',
                        "Student Validation Failed (Row #{$index})",
                        $errorMessages
                    );

                    continue;
                }

                $data = $validated['data'];
                $data['password'] = Hash::make($data['password'] ?? '123456');

                // Try to insert into DB
                try {
                    $student = Student::create($data);
                    event(new StudentActionEvent('insert', $student->id, false));
                } catch (\Throwable $e) {
                    Log::error("💥 DB Insert Failed (Row #{$index}): " . $e->getMessage());
                    $this->notify(
                        'Csv Upload Error',
                        "Student Insert Failed (Row #{$index})",
                        $e->getMessage()
                    );
                }
            }

            Log::info("CSV Processing complete for: {$this->path}");
        } catch (\Throwable $e) {
            // 5️ Unexpected error in entire job
            Log::critical(" CSV Job Failed: " . $e->getMessage());
            $this->notify(
                'Csv Upload Error',
                'CSV Processing Failed',
                $e->getMessage()
            );
        }
    }

    /**
     * Helper method to create notifications safely.
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
        } catch (\Throwable $e) {
            Log::error("Failed to insert notification: " . $e->getMessage());
        }
    }
}
