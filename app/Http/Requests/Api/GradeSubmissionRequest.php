<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class GradeSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'score' => 'required|integer|min:0',
            'feedback' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'score.required' => 'Nilai tugas wajib diisi.',
            'score.integer' => 'Nilai tugas harus berupa angka.',
            'score.min' => 'Nilai tugas minimal 0.',
        ];
    }
}
