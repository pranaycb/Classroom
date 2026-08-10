<?php

namespace App\Models;

use App\Models\ExamQuestion;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'classroom_id',
        'name',
        'marks',
        'pass_mark',
        'start',
        'end',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    /**
     * An exam belongs to a classroom
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * An exam has many questions
     */
    public function questions()
    {
        return $this->hasMany(ExamQuestion::class);
    }

    /**
     * An exam has many results
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Get status of the class
     */
    public function getStatusAttribute(): string
    {
        $now = now();

        if ($now->lessThan($this->start)) {
            return 'upcoming';
        }

        if ($now->between($this->start, $this->end)) {
            return 'ongoing';
        }

        return 'ended';
    }

    /**
     * Get duration of the class
     */
    public function getDurationAttribute()
    {
        $start = Carbon::parse($this->start);
        $end = Carbon::parse($this->end);

        return $start->diffForHumans($end, [
            'short' => false,
            'parts' => 2,
            'syntax' => Carbon::DIFF_ABSOLUTE,
        ]);
    }
}
