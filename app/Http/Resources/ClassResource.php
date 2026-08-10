<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ClassResource extends BaseResource
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
                'title' => $item->title,
                'section' => $item->section,
                'teacher' => $item->teacher->name,
                'theme' => $item->theme,
                'assigned' => !$item->assigned ? null : [
                    'id' => $item->assigned->id,
                    'title' => $item->assigned->title,
                    'date' => now()->parse($item->assigned->due)->format('M j, Y \a\t h:i a'),
                ],
                'code' => $item->code,
            ];
        })->toArray();
    }
}
