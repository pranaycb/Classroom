<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class QuestionResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentPage = $this->currentPage();
        $perPage = $this->perPage();
        $start = ($currentPage - 1) * $perPage;

        return $this->collection->map(function($question, $index) use ($start) {
            return [
                's' => ($start + $index + 1),
                'id' => $question->id,
                'question' => $question->question,
                'right' => round($question->right, 2),
                'wrong' => round($question->wrong, 2),
                'options' => $question->options->map(fn($option) => [
                    'option' => $option->option,
                    'correct' => $option->correct,
                ]),
            ];
        })->toArray();
    }
}
