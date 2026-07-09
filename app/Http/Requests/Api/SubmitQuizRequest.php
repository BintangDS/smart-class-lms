<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SubmitQuizRequest extends FormRequest
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
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:quiz_questions,id',
            'answers.*.option_id' => 'required|exists:quiz_options,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'answers.required' => 'Jawaban kuis wajib dikirim.',
            'answers.array' => 'Format jawaban kuis tidak valid.',
            'answers.*.question_id.required' => 'ID pertanyaan wajib diisi.',
            'answers.*.question_id.exists' => 'Pertanyaan tidak ditemukan.',
            'answers.*.option_id.required' => 'ID pilihan jawaban wajib diisi.',
            'answers.*.option_id.exists' => 'Pilihan jawaban tidak ditemukan.',
        ];
    }
}
