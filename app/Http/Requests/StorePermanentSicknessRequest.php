<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePermanentSicknessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:permanent_sickness,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Permanent sickness name is required',
            'name.unique' => 'This permanent sickness name already exists',
            'name.max' => 'Permanent sickness name cannot exceed 255 characters',
        ];
    }
}
