<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePermanentSicknessRequest extends FormRequest
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
                'max:255',
                Rule::unique('permanent_sickness')->ignore($this->permanent_sickness),
            ],
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
