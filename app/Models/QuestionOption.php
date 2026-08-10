<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    protected $fillable = [
        'exam_question_id',
        'option',
        'correct',
    ];

    protected $casts = [
        'correct' => 'boolean',
    ];

    /**
     * A option belongs to an exam question
     */
    public function examQuestion()
    {
        return $this->belongsTo(ExamQuestion::class);
    }
}
