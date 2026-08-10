<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()
                ->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('title');
            $table->string('section');
            $table->string('subject')->nullable();
            $table->string('room')->nullable();
            $table->string('theme')->default('blue');
            $table->boolean('moderation')->default(false);
            $table->text('student_permissions');
            $table->text('moderator_permissions');
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
