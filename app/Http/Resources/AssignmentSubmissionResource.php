<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentSubmissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'assignment_id' => $this->assignment_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'file_url' => url('storage/' . $this->file_path),
            'score' => $this->score,
            'feedback' => $this->feedback,
            'submitted_at' => $this->submitted_at->toIso8601String(),
            'graded_at' => $this->graded_at ? $this->graded_at->toIso8601String() : null,
        ];
    }
}
