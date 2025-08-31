<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SoldierServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // allow all authenticated users
    }

    public function rules(): array
    {
        $rules = [
            'joining_date' => 'required|date|before_or_equal:today',
            'current_appointment_name' => 'required|string|max:255',
            'current_appointment_from_date' => 'required|date|before_or_equal:today',
            'previous_appointments' => 'array',
        ];

        // Add dynamic validation for each previous appointment
        if ($this->has('previous_appointments')) {
            foreach ($this->previous_appointments as $index => $prev) {
                // Only apply if any field is filled
                if (!empty($prev['name']) || !empty($prev['from_date']) || !empty($prev['to_date'])) {
                    $rules["previous_appointments.$index.name"] = 'required|string|max:255';
                    $rules["previous_appointments.$index.from_date"] = 'required|date|before_or_equal:today';
                    $rules["previous_appointments.$index.to_date"] = 'required|date|after_or_equal:previous_appointments.' . $index . '.from_date';
                }
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [
            'joining_date.before_or_equal' => 'Date cannot be in the future.',
            'current_appointment_from_date.before_or_equal' => ' Date cannot be in the future.',
            'current_appointment_from_date.required_with' => 'From Date is required',

        ];

        // Custom messages for previous appointments
        if ($this->has('previous_appointments')) {
            foreach ($this->previous_appointments as $index => $prev) {
                $messages["previous_appointments.$index.name.required"] = "Appointment Name is required.";
                $messages["previous_appointments.$index.from_date.required"] = "From Date is required.";
                $messages["previous_appointments.$index.to_date.required"] = "To Date is required.";
                $messages["previous_appointments.$index.to_date.after_or_equal"] = "To Date cannot be before From Date.";
                $messages["previous_appointments.$index.from_date.before_or_equal"] = "From Date cannot be in the future.";
                $messages["previous_appointments.$index.to_date.before_or_equal"] = "To Date cannot be in the future.";
            }
        }

        return $messages;
    }
}
