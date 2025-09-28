<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDutyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Allow all users to create duties
    }

    public function rules(): array
    {
        return [
            'duty_name' => 'required|string|max:255',

            // Start time: HH:MM format (00:00 to 23:59)
            'start_time' => [
                'required',
                'regex:/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/',
            ],

            // End time: HH:MM format and must be after start_time
            'end_time' => [
                'required',
                'regex:/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/',
                function ($attribute, $value, $fail) {
                    $start = request('start_time');
                    if ($start) {
                        $startMinutes = (int)substr($start, 0, 2) * 60 + (int)substr($start, 3, 2);
                        $endMinutes   = (int)substr($value, 0, 2) * 60 + (int)substr($value, 3, 2);

                        if ($endMinutes <= $startMinutes) {
                            $fail('End time must be after start time.');
                        }
                    }
                },
            ],

            'remark'   => 'nullable|string',
            'status'   => 'required|in:Active,Inactive',
            'manpower' => ['required', 'integer', 'min:1'],
            // Validate the rank_manpower array
            'rank_manpower' => 'required|array|min:1',
            'rank_manpower.*.rank_id' => 'required|exists:ranks,id',
            'rank_manpower.*.manpower' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'duty_name.required' => 'Duty name is required.',
            'duty_name.string' => 'Duty name must be text.',
            'duty_name.max' => 'Duty name may not be greater than 255 characters.',

            'start_time.required' => 'Start time is required.',
            'start_time.regex' => 'Start time must be in HH:MM format (e.g., 08:30, 17:30).',

            'end_time.required' => 'End time is required.',
            'end_time.regex' => 'End time must be in HH:MM format (e.g., 10:00, 22:00).',

            'remark.string' => 'Remark must be text.',

            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either Active or Inactive.',

            'rank_manpower.required' => 'At least one rank must be selected with manpower.',
            'rank_manpower.array' => 'Rank data must be an array.',
            'rank_manpower.min' => 'At least one rank must be selected with manpower.',

            'rank_manpower.*.rank_id.required' => 'Rank ID is required.',
            'rank_manpower.*.rank_id.exists' => 'Selected rank does not exist.',

            'rank_manpower.*.manpower.required' => 'Manpower is required for each rank.',
            'rank_manpower.*.manpower.integer' => 'Manpower must be a whole number.',
            'rank_manpower.*.manpower.min' => 'Manpower must be at least 1.',
        ];
    }
}
