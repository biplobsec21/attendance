<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExAreaRequest extends FormRequest
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
                Rule::unique('ex_areas')->ignore($this->route('ex_area'))
            ],
            'status' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The Exercise Area name is required.',
            'name.unique' => 'This Exercise Area name already exists.',
            'status.required' => 'The status field is required.',
        ];
    }
}
