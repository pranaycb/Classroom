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
        // create exams table
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->decimal('marks');
            $table->decimal('pass_mark');
            $table->timestamp('start');
            $table->timestamp('end');
            $table->timestamps();
        });

        // create exam questions table
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->longText('question');
            $table->decimal('right')->default(1);
            $table->decimal('wrong')->default(0);
            $table->timestamps();
        });

        // create question options table
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_question_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->longText('option');
            $table->boolean('correct');
            $table->timestamps();
        });

        // create question answers table
        Schema::create('question_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('exam_question_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('question_option_id')->nullable()->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
            $table->unique(['user_id', 'exam_question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('question_answers');
    }
};
