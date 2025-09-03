<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMedicalRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow access, implement auth checks if needed
    }

    public function rules()
    {
        $rules = [
            // Medical
            'medical'              => 'nullable|array',
            'medical.*.category'   => 'nullable|integer|exists:medical_categories,id',
            'medical.*.start_date' => [
                'nullable',
                'date',
                Rule::requiredIf(function () {
                    foreach ($this->input('medical', []) as $row) {
                        if (!empty($row['category'])) {
                            return true;
                        }
                    }
                    return false;
                }),
            ],
            'medical.*.end_date'   => [
                'nullable',
                'date',
                Rule::requiredIf(function () {
                    foreach ($this->input('medical', []) as $row) {
                        if (!empty($row['category'])) {
                            return true;
                        }
                    }
                    return false;
                }),
            ],
            'medical.*.remarks'     => 'nullable|string|max:255',

            // Sickness
            'sickness'              => 'nullable|array',
            'sickness.*.category'   => 'nullable|integer|exists:permanent_sickness,id',
            'sickness.*.start_date' => [
                'nullable',
                'date',
                Rule::requiredIf(function () {
                    foreach ($this->input('sickness', []) as $row) {
                        if (!empty($row['category'])) {
                            return true;
                        }
                    }
                    return false;
                }),
            ],
            'sickness.*.remarks'    => 'nullable|string|max:255',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            // Medical
            'medical.*.category.exists'   => 'The selected medical category is invalid.',
            'medical.*.start_date.required' => 'Start date is required',
            'medical.*.end_date.required'   => 'End date is required',
            'medical.*.start_date.date'     => 'Start date must be a valid date.',
            'medical.*.end_date.date'       => 'End date must be a valid date.',
            'medical.*.remarks.max'         => 'Not be greater than 255 characters.',

            // Sickness
            'sickness.*.category.exists'    => 'The selected sickness category is invalid.',
            'sickness.*.start_date.required' => 'Start date is required',
            'sickness.*.start_date.date'     => 'Start date must be a valid date.',
            'sickness.*.remarks.max'         => 'Not be greater than 255 characters.',
        ];
    }
}
