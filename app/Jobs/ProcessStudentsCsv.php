<?php

namespace App\Jobs;

use App\Models\Student;
use App\Helpers\StudentHelper;
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
        sleep(20);//sleep for 20 seconds to see in aurora
        Log::info("Processing CSV: {$this->path}");

        $file = Storage::disk('public')->path($this->path);

        if (!file_exists($file)) {
            Log::error("CSV file not found: {$this->path}");
            return;
        }

        // Read file safely and remove BOM if present
        $content = file_get_contents($file);
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        $lines = array_filter(array_map('trim', explode("\n", $content)));

        if (empty($lines)) {
            Log::warning("CSV is empty: {$this->path}");
            return;
        }

        // Correct header mapping (matches StudentHelper::validate)
        $header = [
            'first_name',
            'last_name',
            'email',
            'phone',
            'roll_number',
            'age',
            'gender',
            'date_of_birth',
            'admission_date',
            'class_time',
            'address',
            'bio',
            'course',
            'department',
            'batch',
            'is_active',
            'has_scholarship',
            'grade',
            'website',
            'favorite_color',
            'password',
            'profile_photo'
        ];

        foreach ($lines as $index => $line) {
            // Skip header row if detected
            if ($index === 0 && str_contains(strtolower($line), 'first_name')) {
                Log::info("Skipping header row in CSV");
                continue;
            }

            $rowData = str_getcsv($line);

            // Skip empty lines
            if (count(array_filter($rowData)) === 0) {
                continue;
            }

            // Pad missing columns to match header length
            $rowData = array_pad($rowData, count($header), null);

            // Combine header with row data
            $row = [];
            foreach ($header as $i => $key) {
                $row[$key] = $rowData[$i] ?? null;
            }

            // Debugging: log first row’s structure
            if ($index === 1) {
                Log::info("CSV Row Keys", ['keys' => array_keys($row)]);
            }

            // Validate row
            $validated = StudentHelper::validate($row);
            if (!$validated['status']) {
                Log::warning("Row #{$index} failed validation", [
                    'errors' => $validated['errors']->toArray(),
                    'input' => $row,
                ]);
                continue;
            }

            $data = $validated['data'];
            $data['password'] = Hash::make($data['password'] ?? '123456');

            try {
                Student::create($data);
                Log::info("Row #{$index} inserted successfully");
            } catch (\Throwable $e) {
                Log::error("Insert failed for Row #{$index}", [
                    'message' => $e->getMessage(),
                    'data' => $data,
                ]);
            }
        }

        Log::info("CSV Processing complete for: {$this->path}");
    }
}
