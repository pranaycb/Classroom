<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PeopleResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($item) {
            return [
                ...$item->only(['id', 'name', 'phone', 'email', 'university', 'metric_id', 'department', 'designation']),
                'profile' => $item->profile_path,
                'moderator' => (bool) $item->pivot->moderator,
                'blocked' => $item->pivot->status === 'banned',
                'created' => $item->pivot->created_at->format('M d, Y'),
            ];
        })->sortByDesc('moderator')->toArray();
    }
}
