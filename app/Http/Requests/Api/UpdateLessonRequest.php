<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonRequest extends FormRequest
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
        $rules = [
            'title' => 'sometimes|required|string|max:255',
            'content_type' => 'sometimes|required|in:text,video,document',
            'order' => 'sometimes|required|integer',
        ];

        if ($this->hasFile('content')) {
            $rules['content'] = 'sometimes|required|file|max:20480';
        } elseif ($this->has('content')) {
            $rules['content'] = 'sometimes|required|string';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul lesson wajib diisi.',
            'title.string' => 'Judul lesson harus berupa teks.',
            'title.max' => 'Judul lesson tidak boleh lebih dari 255 karakter.',
            'content_type.required' => 'Tipe konten wajib diisi.',
            'content_type.in' => 'Tipe konten harus berupa text, video, atau document.',
            'content.required' => 'Konten lesson wajib diisi.',
            'content.string' => 'Konten teks harus berupa string.',
            'content.file' => 'Konten file harus berupa berkas dokumen/video.',
            'content.max' => 'Ukuran berkas tidak boleh melebihi 20MB.',
            'order.required' => 'Urutan lesson wajib diisi.',
            'order.integer' => 'Urutan lesson harus berupa angka.',
        ];
    }
}
