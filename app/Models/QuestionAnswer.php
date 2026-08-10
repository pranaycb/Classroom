<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;

class QuestionAnswer extends Model
{
    protected $fillable = [
        'user_id',
        'exam_question_id',
        'question_option_id',
    ];

    /**
     * An answer belongs to an exam question
     */
    public function examQuestion()
    {
        return $this->belongsTo(ExamQuestion::class);
    }

    /**
     * An answer belongs to an question option
     */
    public function questionOption()
    {
        return $this->belongsTo(QuestionOption::class);
    }
}
