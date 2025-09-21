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
        ];

        // Current appointment validation (conditional)
        if ($this->filled('current_appointment_id')) {
            $rules['current_appointment_id'] = 'exists:appointments,id';
            $rules['current_appointment_from_date'] = 'required|date|before_or_equal:today';
        } else {
            $rules['current_appointment_id'] = 'nullable|exists:appointments,id';
            $rules['current_appointment_from_date'] = 'nullable|date|before_or_equal:today';
        }

        // Previous appointments validation (conditional)
        if ($this->has('previous_appointments')) {
            foreach ($this->previous_appointments as $index => $prev) {
                // Only validate if an appointment is selected
                if (!empty($prev['id'])) {
                    $rules["previous_appointments.$index.id"] = 'exists:appointments,id';
                    $rules["previous_appointments.$index.from_date"] = 'required|date|before_or_equal:today';
                    $rules["previous_appointments.$index.to_date"] = 'required|date|after_or_equal:previous_appointments.' . $index . '.from_date|before_or_equal:today';
                } else {
                    $rules["previous_appointments.$index.id"] = 'nullable|exists:appointments,id';
                    $rules["previous_appointments.$index.from_date"] = 'nullable|date|before_or_equal:today';
                    $rules["previous_appointments.$index.to_date"] = 'nullable|date|before_or_equal:today';
                }
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [
            'joining_date.required' => 'Date of joining is required.',
            'joining_date.before_or_equal' => 'Date cannot be in the future.',
            'current_appointment_id.exists' => 'The selected appointment is invalid.',
            'current_appointment_from_date.required' => 'From date is required when an appointment is selected.',
            'current_appointment_from_date.before_or_equal' => 'From date cannot be in the future.',
        ];

        // Custom messages for previous appointments
        if ($this->has('previous_appointments')) {
            foreach ($this->previous_appointments as $index => $prev) {
                $messages["previous_appointments.$index.id.exists"] = "The selected appointment is invalid.";
                $messages["previous_appointments.$index.from_date.required"] = "From date is required when an appointment is selected.";
                $messages["previous_appointments.$index.from_date.before_or_equal"] = "From date cannot be in the future.";
                $messages["previous_appointments.$index.to_date.required"] = "To date is required when an appointment is selected.";
                $messages["previous_appointments.$index.to_date.after_or_equal"] = "To date cannot be before from date.";
                $messages["previous_appointments.$index.to_date.before_or_equal"] = "To date cannot be in the future.";
            }
        }

        return $messages;
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate that to_date is after from_date for previous appointments
            if ($this->has('previous_appointments')) {
                foreach ($this->previous_appointments as $index => $prev) {
                    if (!empty($prev['id']) && !empty($prev['from_date']) && !empty($prev['to_date'])) {
                        $fromDate = \Carbon\Carbon::parse($prev['from_date']);
                        $toDate = \Carbon\Carbon::parse($prev['to_date']);

                        if ($toDate->lt($fromDate)) {
                            $validator->errors()->add("previous_appointments.$index.to_date", "To date must be after or equal to from date.");
                        }
                    }
                }
            }
        });
    }
}
