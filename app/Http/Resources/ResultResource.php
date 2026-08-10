<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ResultResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn($result) => [
            'id' => $result->id,
            'student' => $result->user->name,
            'marks' => round($result->marks, 2) . '/' . round($result->exam->marks, 2),
            'status' => $result->status,
        ])->toArray();
    }
}
