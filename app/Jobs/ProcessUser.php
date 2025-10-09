<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\UserActionEvent;

class ProcessUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $path;

    /**
     * Create a new job instance.
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            sleep(10); // simulate slow processing
            Log::info("🚀 Processing User CSV: {$this->path}");

            $file = Storage::disk('public')->path($this->path);

            if (!file_exists($file)) {
                Log::error("❌ CSV file not found: {$this->path}");
                $this->notify('Csv Upload Error', 'CSV File Missing', "File not found: {$this->path}");
                return;
            }

            // Read file content safely
            $content = preg_replace('/^\xEF\xBB\xBF/', '', file_get_contents($file)); // remove BOM
            $lines = array_filter(array_map('trim', explode("\n", $content)));

            if (empty($lines)) {
                Log::warning("⚠️ CSV file is empty: {$this->path}");
                $this->notify('Csv Upload Error', 'Empty CSV File', "No data found in {$this->path}");
                return;
            }

            // Define expected columns
            $header = ['name', 'email', 'image'];

            foreach ($lines as $index => $line) {
                // Skip header row
                if ($index === 0 && str_contains(strtolower($line), 'name')) {
                    continue;
                }

                $rowData = str_getcsv($line);
                if (count(array_filter($rowData)) === 0) continue; // skip blank rows

                $rowData = array_pad($rowData, count($header), null);
                $row = [];
                foreach ($header as $i => $key) {
                    $row[$key] = trim($rowData[$i] ?? '');
                }

                // Validate inline using Laravel Validator
                $validator = Validator::make($row, [
                    'name'  => 'required|string|max:100',
                    'email' => 'required|email|unique:users,email',
                    'image' => 'nullable|string|max:255',
                ]);

                if ($validator->fails()) {
                    $errorMessages = collect($validator->errors()->toArray())
                        ->map(fn($msgs, $field) => $field . ': ' . implode(', ', $msgs))
                        ->implode(' | ');

                    Log::warning("Validation failed for row #{$index}", [
                        'errors' => $validator->errors()->toArray(),
                        'data' => $row,
                    ]);

                    $this->notify(
                        'Csv Upload Error',
                        "User Validation Failed (Row #{$index})",
                        $errorMessages
                    );
                    continue;
                }

                try {
                    // Insert user
                    $user = User::create([
                        'name' => $row['name'],
                        'email' => $row['email'],
                        'image' => $row['image'],
                    ]);

                    Log::info("✅ User inserted: {$user->email}");
                    event(new UserActionEvent('inserted', $user->id, false));

                } catch (\Throwable $e) {
                    Log::error("❌ Failed to insert user at row #{$index}", [
                        'error' => $e->getMessage(),
                        'data' => $row,
                    ]);

                    $this->notify(
                        'Csv Upload Error',
                        "User Insert Failed (Row #{$index})",
                        $e->getMessage()
                    );
                }
            }

            Log::info("🎉 User CSV Processing completed for: {$this->path}");
        } catch (\Throwable $e) {
            Log::critical("💥 User CSV Job Failed: " . $e->getMessage());
            $this->notify('Csv Upload Error', 'CSV Processing Failed', $e->getMessage());
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
                'related_model' => User::class,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed to insert notification: " . $e->getMessage());
        }
    }
}
