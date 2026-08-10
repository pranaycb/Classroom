<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\BaseResource;
use Illuminate\Support\Facades\Auth;

class CommentResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn($comment) => [
            'id' => $comment->id,
            'by' => $comment->user->name,
            'avatar' => $comment->user->profile_path,
            'comment' => $comment->content,
            'date' => Carbon::parse($comment->created_at)->diffForHumans(),
            'delete' => Auth::user()->can('delete', $comment),
            'replies' => $comment->replies->map(fn($reply) => [
                'id' => $reply->id,
                'by' => $reply->user->name,
                'avatar' => $reply->user->profile_path,
                'mentioned' => $reply->parent ? $reply->parent->user->name : $comment->user->name,
                'comment' => $reply->content,
                'date' => now()->parse($reply->created_at)->diffForHumans(),
                'delete' => Auth::user()->can('delete', $reply),
            ]),
        ])->toArray();
    }
}
