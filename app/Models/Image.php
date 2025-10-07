<?php

namespace App\Models;

use MongoDB\Laravel\Auth\User as Authenticatable; // ✅ correct class

class Image extends Authenticatable
{
    protected $connection = 'mongodb'; // MongoDB connection name (from config/database.php)
    protected $collection = 'images';  // MongoDB collection name

    protected $fillable = ['file_name', 'mime_type', 'image_blob'];

    // Optional helper: convert binary data to base64 for display
    public function getBase64Attribute()
    {
        return 'data:' . $this->mime_type . ';base64,' . base64_encode($this->image_blob);
    }
}
