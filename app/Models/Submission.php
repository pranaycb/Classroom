<?php

namespace App\Models;

use App\Observers\SubmissionObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(SubmissionObserver::class)]
class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'user_id',
        'feedback',
        'marks',
        'status',
    ];

    /**
     * Get assignment
     */
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Get user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get attachment
     */
    public function attachment()
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }

    /**
     * Get percentage
     */
    protected function getPercentageAttribute()
    {
        return round($this->marks / $this->assignment->marks * 100, 2);
    }
}
