<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
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
            'description' => 'required|string',
            'due_date' => 'required|date|after:now',
            'max_score' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul tugas wajib diisi.',
            'title.string' => 'Judul tugas harus berupa teks.',
            'title.max' => 'Judul tugas tidak boleh lebih dari 255 karakter.',
            'description.required' => 'Deskripsi/panduan tugas wajib diisi.',
            'due_date.required' => 'Batas waktu (deadline) wajib diisi.',
            'due_date.date' => 'Batas waktu harus berupa format tanggal.',
            'due_date.after' => 'Batas waktu pengerjaan tugas harus di masa depan.',
            'max_score.required' => 'Nilai maksimal tugas wajib diisi.',
            'max_score.integer' => 'Nilai maksimal harus berupa angka.',
            'max_score.min' => 'Nilai maksimal minimal 1.',
        ];
    }
}
