<?php

namespace App\Http\Resources;

use App\Action\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn($assignment) => [
            'id' => $assignment->id,
            'title' => $assignment->title,
            'marks' => round($assignment->marks, 2),
            'due' => $assignment->due->format('M d, Y \a\t h:i a'),
            'status' => $this->when(Permission::isStudent(), function() use($assignment) {
                return $assignment->submissions()->where('user_id', Auth::id())->value('status') ?? 'missing';
            }),
        ])->toArray();
    }
}
