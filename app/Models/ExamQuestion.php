<?php

namespace App\Models;

use App\Models\Exam;
use App\Models\QuestionAnswer;
use App\Models\QuestionOption;
use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    protected $fillable = [
        'exam_id',
        'question',
        'right',
        'wrong',
    ];

    /**
     * A question belongs to an exam
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * A question has many options
     */
    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    /**
     * A question has many answer
     */
    public function answers()
    {
        return $this->hasMany(QuestionAnswer::class);
    }
}
