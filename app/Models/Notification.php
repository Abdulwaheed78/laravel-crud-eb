<?php

namespace App\Models;

use MongoDB\Laravel\Auth\User as Authenticatable; // ✅ correct class

class Notification extends Authenticatable
{
    protected $connection = 'mongodb'; // use your MongoDB connection name
    protected $primaryKey = '_id'; // 👈 required for MongoDB
    protected $collection = 'notifications'; // optional (Laravel will auto-pluralize)

    protected $fillable = [
        'type',
        'title',
        'message',
        'related_id',
        'related_model',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];
}
