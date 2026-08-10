<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class OnlineClassResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn($class) => [
            'id' => $class->id,
            'name' => $class->name,
            'scheduled' => $class->start->format('M d, Y \a\t h:i a'),
            'duration' => $class->duration,
            'status' => $class->status,
        ])->toArray();
    }
}
