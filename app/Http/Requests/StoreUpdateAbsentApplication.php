<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateAbsentApplication extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'soldier_ids' => 'required|array|min:1',
            'soldier_ids.*' => 'exists:soldiers,id',
            'absent_type_id' => 'required|exists:absent_types,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:500',
            'application_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'soldier_ids.required' => 'Please select at least one soldier.',
            'soldier_ids.array' => 'Invalid soldier selection.',
            'soldier_ids.min' => 'Please select at least one soldier.',
            'soldier_ids.*.exists' => 'The selected soldier does not exist.',
            'absent_type_id.required' => 'Please select an absent type.',
            'absent_type_id.exists' => 'The selected absent type does not exist.',
            'start_date.required' => 'Start date is required.',
            // 'end_date.required' => 'End date is required.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'application_file.image' => 'The file must be an image.',
            'application_file.mimes' => 'The file must be a jpeg, png, jpg, or gif.',
            'application_file.max' => 'The file may not be greater than 2MB.',
        ];
    }
}
