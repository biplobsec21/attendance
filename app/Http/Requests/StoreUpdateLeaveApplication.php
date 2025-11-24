<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateLeaveApplication extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow all for now, restrict with policies if needed
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Check if we're updating (has leave_id) or creating
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        $rules = [
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'hard_copy' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'application_current_status' => ['nullable', 'string', 'max:100'],
            'remove_hard_copy' => ['nullable', 'boolean'],
        ];

        // For new applications, require soldier_ids array
        if (!$isUpdate) {
            $rules['soldier_ids'] = ['required', 'array', 'min:1'];
            $rules['soldier_ids.*'] = ['required', 'exists:soldiers,id'];
        } else {
            // For updates, use single soldier_id (maintains backward compatibility)
            $rules['soldier_id'] = ['required', 'exists:soldiers,id'];
        }

        return $rules;
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'soldier_ids.required' => 'Please select at least one soldier.',
            'soldier_ids.array' => 'Invalid soldier selection.',
            'soldier_ids.min' => 'Please select at least one soldier.',
            'soldier_ids.*.required' => 'Invalid soldier selection.',
            'soldier_ids.*.exists' => 'One or more selected soldiers are invalid.',
            'soldier_id.required' => 'Please select a profile.',
            'soldier_id.exists' => 'Selected profile is invalid.',
            'leave_type_id.required' => 'Leave type is required.',
            'leave_type_id.exists' => 'Selected leave type is invalid.',
            'start_date.required' => 'Start date is required.',
            'end_date.required' => 'End date is required.',
            'end_date.after_or_equal' => 'End date must be the same or after the start date.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Convert empty string to null for remove_hard_copy
        if ($this->has('remove_hard_copy') && $this->remove_hard_copy === '') {
            $this->merge([
                'remove_hard_copy' => null,
            ]);
        }

        // For new applications with multiple soldiers, ensure soldier_ids is an array
        if ($this->isMethod('POST') && $this->has('soldier_ids')) {
            $soldierIds = $this->soldier_ids;
            if (is_string($soldierIds)) {
                $soldierIds = [$soldierIds];
            }

            $this->merge([
                'soldier_ids' => $soldierIds,
            ]);
        }
    }
}
