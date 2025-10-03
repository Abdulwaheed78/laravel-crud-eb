<?php

namespace App\Models;

use MongoDB\Laravel\Auth\User as Authenticatable; // ✅ correct class
use MongoDB\Laravel\Eloquent\Model;
class User extends Authenticatable
{
    protected $connection = 'mongodb';
    protected $collection = 'users'; // your MongoDB collection

    protected $fillable = [
        'name',
        'email',
        'image',
    ];
}
