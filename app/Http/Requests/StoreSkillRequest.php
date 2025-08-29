<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:350', 'unique:skill_categories,name'],
            'category_id' => ['required'],
            'status' => ['required', 'boolean'],
        ];
    }
}
