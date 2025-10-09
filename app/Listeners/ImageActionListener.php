<?php

namespace App\Listeners;

use App\Events\ImageActionEvent;
use App\Models\Image;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class ImageActionListener
{
    /**
     * Handle the event.
     */
    public function handle(ImageActionEvent $event): void
    {
        $image = Image::find($event->imageId);

        // 🧩 Logging for debugging
        Log::info('🖼️ ImageActionListener triggered', [
            'action' => $event->action,
            'status' => $event->status,
            'imageId' => $event->imageId,
            'imageFound' => (bool) $image,
        ]);

        // ✅ Always create a notification — even if image record missing
        Notification::create([
            'type' => 'image_action',
            'title' => "Image {$event->action}",
            'message' => $image
                ? "Image '{$image->file_name}' (ID: {$event->imageId}) was {$event->action}."
                : "An image with ID {$event->imageId} was {$event->action}, but the record is no longer in the database.",
            'related_id' => $event->imageId,
            'related_model' => 'App\Models\Image',
            'is_read' => false,
        ]);

        Log::info('📢 Notification created for image action', [
            'related_id' => $event->imageId,
            'has_image' => (bool) $image,
        ]);
    }
}
