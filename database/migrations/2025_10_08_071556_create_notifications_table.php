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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable(); // e.g., 'student_action', 'system_alert'
            $table->string('title'); // short title like "Student Updated"
            $table->text('message')->nullable(); // full message or details
            $table->string('related_id')->nullable(); // e.g., student_id or other reference
            $table->string('related_model')->nullable(); // e.g., 'App\Models\Student'
            $table->boolean('is_read')->default(false); // for marking notifications as read
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
