<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class ImageActionEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $action;
    public ?string $imageId;
    public bool $status;

    /**
     * Create a new event instance.
     */
    public function __construct(string $action, ?string $imageId = null, bool $status = true)
    {
        $this->action = $action;
        $this->imageId = $imageId;
        $this->status = $status;
    }
}
