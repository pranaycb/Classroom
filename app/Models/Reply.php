<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $fillable = [
        'parent_id',
        'comment_id',
        'user_id',
        'content'
    ];

    /**
     * Get user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get comment
     */
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Get parent comment
     */
    public function parent()
    {
        return $this->belongsTo(Reply::class, 'parent_id');
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
        return $this->user_id === Auth::id() || $this->parent->user_id === Auth::id();
    }
}
