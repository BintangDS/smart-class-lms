<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorized in controller (course ownership check)
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'order' => 'required|integer',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul modul wajib diisi.',
            'title.string' => 'Judul modul harus berupa teks.',
            'title.max' => 'Judul modul tidak boleh lebih dari 255 karakter.',
            'order.required' => 'Urutan modul wajib diisi.',
            'order.integer' => 'Urutan modul harus berupa angka.',
        ];
    }
}
