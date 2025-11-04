<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:350|unique:medical_categories,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Medical category name is required',
            'name.unique' => 'This medical category name already exists',
            'name.max' => 'Medical category name cannot exceed 350 characters',
        ];
    }
}
