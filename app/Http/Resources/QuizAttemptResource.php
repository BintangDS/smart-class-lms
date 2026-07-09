<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizAttemptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $passingScore = $this->quiz->passing_score ?? 70;
        return [
            'id' => $this->id,
            'quiz_id' => $this->quiz_id,
            'quiz_title' => $this->quiz->title ?? null,
            'score' => $this->score,
            'passing_score' => $passingScore,
            'passed' => $this->score >= $passingScore,
            'submitted_at' => $this->submitted_at->toIso8601String(),
        ];
    }
}
