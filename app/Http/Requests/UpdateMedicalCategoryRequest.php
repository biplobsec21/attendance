<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMedicalCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:350',
                Rule::unique('medical_categories')->ignore($this->medical_category),
            ],
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
