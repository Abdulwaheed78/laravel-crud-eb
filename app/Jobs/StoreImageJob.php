<?php

namespace App\Jobs;

use App\Models\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StoreImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $fileName;
    protected string $mimeType;
    protected string $tempPath;

    public function __construct(string $fileName, string $mimeType, string $tempPath)
    {
        $this->fileName = $fileName;
        $this->mimeType = $mimeType;
        $this->tempPath = $tempPath;
    }

    public function handle(): void
    {
        sleep(25);
        Log::info('🧩 StoreImageJob started', [
            'fileName' => $this->fileName,
            'path' => $this->tempPath,
        ]);

        // 👀 Stage 1: Job picked up by worker
        Log::info('⏳ [Stage 1] Job reserved by worker, simulating long process...');
        sleep(15); // You’ll see it as “Reserved” in Aurora

        // 👀 Stage 2: Reading the file
        Log::info('📂 [Stage 2] Reading file from storage...');
        $binaryData = Storage::disk('local')->get($this->tempPath);
        sleep(10); // Still visible in Aurora

        // 👀 Stage 3: Writing to DB
        Log::info('💾 [Stage 3] Writing image to MongoDB...');
        Image::create([
            'file_name' => $this->fileName,
            'mime_type' => $this->mimeType,
            'image_blob' => new \MongoDB\BSON\Binary($binaryData, \MongoDB\BSON\Binary::TYPE_GENERIC),
        ]);
        sleep(10); // Job still "running" in Aurora

        // 👀 Stage 4: Cleaning up
        Log::info('🧹 [Stage 4] Cleaning up temp file...');
        Storage::disk('local')->delete($this->tempPath);

        // ✅ Done
        Log::info('✅ Image stored successfully and temp file deleted');
    }
}
