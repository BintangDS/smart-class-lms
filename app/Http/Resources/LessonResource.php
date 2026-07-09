<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
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
            'module_id' => $this->module_id,
            'title' => $this->title,
            'content_type' => $this->content_type,
            'content' => ($this->content_type === 'text') ? $this->content : url('storage/' . $this->content),
            'order' => $this->order,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
