<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorized via policy
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
            'level' => 'required|in:beginner,intermediate,advanced',
            'status' => 'nullable|in:draft,published',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'title.required' => 'Judul kursus wajib diisi.',
            'title.string' => 'Judul kursus harus berupa teks.',
            'title.max' => 'Judul kursus tidak boleh lebih dari 255 karakter.',
            'description.required' => 'Deskripsi kursus wajib diisi.',
            'thumbnail.image' => 'File thumbnail harus berupa gambar.',
            'thumbnail.max' => 'Ukuran thumbnail tidak boleh melebihi 2MB.',
            'level.required' => 'Level kursus wajib dipilih.',
            'level.in' => 'Level harus beginner, intermediate, atau advanced.',
            'status.in' => 'Status harus draft atau published.',
        ];
    }
}
