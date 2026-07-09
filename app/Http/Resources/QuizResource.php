<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $isStudent = $user && $user->role === 'student';

        return [
            'id' => $this->id,
            'module_id' => $this->module_id,
            'title' => $this->title,
            'passing_score' => $this->passing_score,
            'questions' => $this->questions->map(function ($question) use ($isStudent) {
                return [
                    'id' => $question->id,
                    'question_text' => $question->question_text,
                    'options' => $question->options->map(function ($option) use ($isStudent) {
                        $data = [
                            'id' => $option->id,
                            'option_text' => $option->option_text,
                        ];

                        if (!$isStudent) {
                            $data['is_correct'] = $option->is_correct;
                        }

                        return $data;
                    }),
                ];
            }),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
