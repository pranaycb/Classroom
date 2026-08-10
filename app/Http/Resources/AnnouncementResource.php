<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CommentResource;

class AnnouncementResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($item) {

            $comments = $item->comments()
                ->orderBy('id', 'desc')
                ->paginate(10, ['*'], 'comment')
                ->onEachSide(0)
                ->withQueryString();

            return [
                'id' => $item->id,
                'by' => $item->user->name,
                'avatar' => $item->user->profile_path,
                'content' => $item->content,
                'date' => now()->parse($item->created_at)->diffForHumans(),
                'attachments' => [
                    'count' => $item->attachments->count(),
                    'data' => AttachmentResource::make($item->attachments)
                ],
                'comments' => (new CommentResource($comments))->response()->getData(true),
                'action' => [
                    'delete' => Auth::user()->can('delete', $item),
                    'update' => Auth::user()->can('update', $item),
                ]
            ];
        })->toArray();
    }
}
