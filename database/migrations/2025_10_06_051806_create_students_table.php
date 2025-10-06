<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // Basic info (text, number, email, etc.)
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->string('roll_number')->unique();
            $table->unsignedInteger('age')->nullable();

            // Gender (radio/select)
            $table->enum('gender', ['male', 'female', 'other'])->nullable();

            // Date/time fields
            $table->date('date_of_birth')->nullable();
            $table->dateTime('admission_date')->nullable();
            $table->time('class_time')->nullable();

            // Address and textarea inputs
            $table->string('address')->nullable();
            $table->text('bio')->nullable();

            // Select dropdown fields (for academic info)
            $table->string('course')->nullable();
            $table->string('department')->nullable();
            $table->year('batch')->nullable();

            // Checkbox / boolean inputs
            $table->boolean('is_active')->default(true);
            $table->boolean('has_scholarship')->default(false);

            // File input
            $table->string('profile_photo')->nullable(); // store filename/path

            // Range / number type example
            $table->decimal('grade', 4, 2)->nullable();

            // URL, color, hidden, etc.
            $table->string('website')->nullable();
            $table->string('favorite_color', 7)->nullable(); // e.g. #FF5733
            $table->string('secret_token')->nullable(); // for hidden input

            // Password field
            $table->string('password');

            // JSON field for storing multiple checkboxes, interests, etc.
            $table->json('hobbies')->nullable();

            // Remember and timestamps
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
