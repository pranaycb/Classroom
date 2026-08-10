<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ExamResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn($exam) => [
            'id' => $exam->id,
            'name' => $exam->name,
            'start' => $exam->start->format('M d, Y \a\t h:i a'),
            'end' => $exam->end->format('M d, Y \a\t h:i a'),
            'duration' => $exam->duration,
            'status' => $exam->status,
        ])->toArray();
    }
}
