<?php

// app/Http/Requests/UpdateDutyRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDutyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Allow all users to update duties
    }

    // app/Http/Requests/UpdateDutyRequest.php

    public function rules(): array
    {
        return [
            'duty_name' => 'required|string|max:255',
            // Change the time format validation here
            'start_time' => 'required|date_format:g:i A',
            'end_time' => 'required|date_format:g:i A|after:start_time',
            'manpower' => 'required|integer|min:1',
            'remark' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
        ];
    }
}