<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeaveApplicationStatusRequest extends FormRequest
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
    public function rules()
    {
        return [
            'leave_id' => ['required', 'exists:soldier_leave_applications,id'],
            'status' => ['required', 'in:pending,approved,rejected'],
            'status_reason' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($this->status === 'rejected' && empty($value)) {
                        $fail('Reason is required when rejecting a leave application.');
                    }
                },
            ],
        ];
    }
    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'soldier_id.required' => 'Please select a  profile.',
            'soldier_id.exists' => 'Selected profile is invalid.',
            'leave_type_id.required' => 'Leave type is required.',
            'leave_type_id.exists' => 'Selected leave type is invalid.',
            'start_date.required' => 'Start date is required.',
            'end_date.required' => 'End date is required.',
            'end_date.after_or_equal' => 'End date must be the same or after the start date.',
        ];
    }
}
