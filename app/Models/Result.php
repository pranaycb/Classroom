<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'user_id',
        'exam_id',
        'marks',
    ];

    /**
     * A result belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A result belongs to a exam
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get status for the result
     */
    public function getStatusAttribute()
    {
        return ($this->marks >= $this->exam->pass_mark) ? 'passed' : 'failed';
    }
}
