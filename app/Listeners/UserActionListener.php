<?php

namespace App\Listeners;

use App\Events\UserActionEvent;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class UserActionListener
{
    /**
     * Handle the event.
     */
    public function handle(UserActionEvent $event): void
    {
        // 💡 Try to find the user by ID (if it exists)
        $user = User::find($event->userId);

        if ($user) {
            Log::info('🎯 UserActionListener triggered', [
                'action' => $event->action,
                'status' => $event->status,
                'userId' => $user->id,
                'userName' => $user->name ?? ($user->first_name . ' ' . $user->last_name),
                'email' => $user->email ?? null,
            ]);

            $displayName = $user->name ?? ($user->first_name . ' ' . $user->last_name);
            $message = "User {$displayName} was {$event->action}.";
        } else {
            Log::warning('⚠️ User not found for ID: ' . $event->userId);
            $message = "User with ID {$event->userId} was {$event->action}.";
        }

        // ✅ Create a notification (even if user no longer exists)
        Notification::create([
            'type' => 'user_action',
            'title' => "User {$event->action}",
            'message' => $message,
            'related_id' => $event->userId,
            'related_model' => User::class,
            'is_read' => false,
        ]);
    }
}
