<?php

namespace App\Models;

use MongoDB\Laravel\Auth\User as Authenticatable; // ✅ correct class
use MongoDB\Laravel\Eloquent\Model;

class Student extends Authenticatable
{
    protected $connection = 'mongodb';
    protected $collection = 'students'; // your MongoDB collection
    protected $fillable = [
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
        'profile_photo',
        'grade',
        'website',
        'favorite_color',
        'secret_token',
        'password',
        'hobbies',
    ];
}
