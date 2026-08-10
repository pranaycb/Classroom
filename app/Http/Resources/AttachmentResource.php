<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AttachmentResource extends ResourceCollection
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
                'name' => $item->name,
                'url' => route('dashboard.classroom.download', [
                    $item->attachable->classroom->code,
                    $item->id,
                ]),
                'size' => Number::fileSize($item->size, 2),
                'type' => $item->type,
                'icon' => $item->icon,
            ];
        })->toArray();
    }
}
