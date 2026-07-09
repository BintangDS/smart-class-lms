<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAssignmentRequest extends FormRequest
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
            'file' => 'required|file|max:10240', // Max 10MB
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Berkas jawaban tugas wajib diunggah.',
            'file.file' => 'Input harus berupa file valid.',
            'file.max' => 'Ukuran berkas jawaban tugas tidak boleh melebihi 10MB.',
        ];
    }
}
