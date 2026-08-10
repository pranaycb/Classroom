<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Number;

class SubmissionResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($submission) {
            return [
                'id' => $submission->id,
                'student' => $submission->user->name,
                'marks' => $submission->marks ? round($submission->marks, 2) : null,
                'feedback' => $submission->feedback,
                'percentage' => $submission->marks ? $submission->percentage : null,
                'created' => $submission->created_at->format('M d, Y \a\t h:i a'),
                'attachment' => route('dashboard.classroom.download', [
                    $submission->assignment->classroom->code,
                    $submission->attachment->id,
                ]),
            ];
        })->toArray();
    }
}
