<?php

namespace App\Models;

use App\Observers\AssignmentObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(AssignmentObserver::class)]
class Assignment extends Model
{
    protected $fillable = [
        'classroom_id',
        'title',
        'description',
        'marks',
        'due',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'due' => 'datetime',
        ];
    }

    /**
     * Get classroom
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Get users
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Get submissions
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Get attachments
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get comments
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
