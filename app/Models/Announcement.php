<?php

namespace App\Models;

use App\Observers\AnnouncementObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(AnnouncementObserver::class)]
class Announcement extends Model
{
    protected $fillable = [
        'classroom_id',
        'user_id',
        'content',
    ];

    /**
     * Get classroom
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Get user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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

    /**
     * Check if can delete comment.
     */
    public function getCanDeleteAttribute(): bool
    {
        return $this->classroom->user_id === Auth::id() || $this->user_id === Auth::id();
    }
}
