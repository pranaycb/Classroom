<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'commentable_type',
        'commentable_id',
        'content',
    ];

    /**
     * Get all of the owning commentable models
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * Get user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get replies
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * Get attachments
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Check if can delete comment.
     */
    public function getCanDeleteAttribute(): bool
    {
        return $this->user_id === Auth::id();
    }
}
