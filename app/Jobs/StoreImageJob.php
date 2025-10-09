<?php

namespace App\Jobs;

use App\Models\Image;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Events\ImageActionEvent;

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
        try {
            Log::info('🧩 StoreImageJob started', [
                'fileName' => $this->fileName,
                'path' => $this->tempPath,
            ]);

            // 👀 Stage 1: Check if file exists
            if (!Storage::disk('local')->exists($this->tempPath)) {
                Log::error("❌ Temp file not found: {$this->tempPath}");
                $this->notify('Image Upload Error', 'Temp File Missing', "File not found at path: {$this->tempPath}");
                return;
            }

            // 👀 Stage 2: Reading file
            Log::info('📂 [Stage 2] Reading file from storage...');
            $binaryData = Storage::disk('local')->get($this->tempPath);

            // 👀 Stage 3: Writing image to MongoDB
            Log::info('💾 [Stage 3] Writing image to MongoDB...');
            $image = Image::create([
                'file_name' => $this->fileName,
                'mime_type' => $this->mimeType,
                'image_blob' => new \MongoDB\BSON\Binary($binaryData, \MongoDB\BSON\Binary::TYPE_GENERIC),
            ]);

            // Fire event after successful creation
            event(new ImageActionEvent('created', $image->_id, true));

            // 👀 Stage 4: Cleanup
            Log::info('🧹 [Stage 4] Cleaning up temp file...');
            Storage::disk('local')->delete($this->tempPath);
            Log::info('✅ Image stored successfully and temp file deleted');
        } catch (\Throwable $e) {
            Log::error("💥 StoreImageJob failed: " . $e->getMessage());
            $this->notify('Image Upload Error', 'Image Processing Failed', $e->getMessage());
        }
    }

    /**
     * Helper method to safely create notifications in MongoDB.
     */
    private function notify(string $type, string $title, string $message, ?string $relatedId = null): void
    {
        try {
            Notification::create([
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'related_id' => $relatedId,
                'related_model' => Image::class,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error("❗Failed to insert notification: " . $e->getMessage());
        }
    }
}
