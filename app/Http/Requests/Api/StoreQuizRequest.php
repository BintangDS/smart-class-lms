<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'passing_score' => 'required|integer|min:0|max:100',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*.option_text' => 'required|string',
            'questions.*.options.*.is_correct' => 'required|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul kuis wajib diisi.',
            'passing_score.required' => 'Nilai kelulusan wajib diisi.',
            'passing_score.integer' => 'Nilai kelulusan harus berupa angka.',
            'passing_score.min' => 'Nilai kelulusan minimal 0.',
            'passing_score.max' => 'Nilai kelulusan maksimal 100.',
            'questions.required' => 'Kuis harus memiliki minimal 1 pertanyaan.',
            'questions.array' => 'Daftar pertanyaan tidak valid.',
            'questions.*.question_text.required' => 'Teks pertanyaan wajib diisi.',
            'questions.*.options.required' => 'Pertanyaan wajib memiliki minimal 2 pilihan jawaban.',
            'questions.*.options.*.option_text.required' => 'Teks pilihan jawaban wajib diisi.',
            'questions.*.options.*.is_correct.required' => 'Status kebenaran pilihan jawaban wajib diisi.',
        ];
    }
}
