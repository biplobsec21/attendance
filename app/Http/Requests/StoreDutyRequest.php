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

            // 'manpower' => 'required|integer|min:1',
            'remark'   => 'nullable|string',
            'status'   => 'required|in:Active,Inactive',
        ];
    }
}
