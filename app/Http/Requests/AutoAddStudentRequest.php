<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AutoAddStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('Admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'angkatan' => ['required', 'numeric'],
            'jumlahSiswa' => ['required', 'numeric'],
            'jumlahSiswaLaki' => ['required', 'numeric'],
            'jumlahSiswaPerempuan' => ['required', 'numeric'],
            
        ];
    }
}
