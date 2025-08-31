<?php

// app/Http/Requests/StoreDutyRequest.php

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
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'manpower' => 'required|integer|min:1',
            'remark' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
        ];
    }
}
