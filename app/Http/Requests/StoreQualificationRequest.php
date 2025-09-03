<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQualificationRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow access, implement auth checks if needed
    }
    public function attributes()
    {
        return [
            'education.*.name' => 'education name',
            'education.*.status' => 'education status',
            'education.*.year' => 'education year',

            'courses.*.name' => 'course name',
            'courses.*.status' => 'course status',
            'courses.*.start_date' => 'start date',
            'courses.*.end_date' => 'end date',
            'courses.*.remark' => 'remark',

            'cadres.*.name' => 'cadre name',
            'cadres.*.status' => 'cadre status',
            'cadres.*.start_date' => 'start date',
            'cadres.*.end_date' => 'end date',
            'cadres.*.remark' => 'remark',

            'cocurricular.*.name' => 'co-curricular activity',
            'cocurricular.*.remark' => 'remark',

            'ere.*.start_date' => 'start date',
            'ere.*.end_date' => 'end date',
            'ere.*.remark' => 'remark',

            'att.*.start_date' => 'start date',
            'att.*.end_date' => 'end date',
            'att.*.remark' => 'remark',
        ];
    }
    public function rules()
    {
        return [
            // Education
            'education' => 'required|array|min:1',
            'education.*.name' => 'required|integer|exists:educations,id',
            'education.*.status' => 'required|string|in:Running,Passed',
            'education.*.year' => 'required_if:education.*.status,Passed|nullable|digits:4',

            // Course
            // Course
            'courses' => 'nullable|array',
            'courses.*.name' => 'nullable|integer|exists:courses,id',
            'courses.*.status' => 'required_with:courses.*.name|string|in:Running,Passed',
            'courses.*.start_date' => 'required_if:courses.*.status,Passed|nullable|date',
            'courses.*.end_date'   => 'required_if:courses.*.status,Passed|nullable|date',
            'courses.*.result'     => 'nullable|string|max:255',

            // Cadre
            'cadres' => 'nullable|array',
            'cadres.*.name' => 'nullable|integer|exists:cadres,id',
            'cadres.*.status' => [
                'sometimes', // only run if the field is present
                'string',
                'in:Running,Passed',
                function ($attribute, $value, $fail) {
                    // Get the current index from the attribute name
                    $index = explode('.', $attribute)[1];
                    $cadres = $this->input('cadres', []);
                    $name = $cadres[$index]['name'] ?? null;

                    if (!empty($name) && empty($value)) {
                        $fail('The cadre status is required when a cadre name is selected.');
                    }
                },
            ],
            'cadres.*.start_date' => 'required_if:cadres.*.status,Passed|nullable|date',
            'cadres.*.end_date' => 'required_if:cadres.*.status,Passed|nullable|date',
            'cadres.*.result' => 'nullable|string|max:255',

            // Co-Curricular
            'cocurricular' => 'nullable|array',
            'cocurricular.*.name' => 'nullable|integer|exists:skills,id',
            'cocurricular.*.result' => 'nullable|string|max:255',

            // ERE
            'ere' => 'nullable|array',
            'ere.*.start_date' => 'nullable|date',
            'ere.*.end_date'   => 'nullable|date',
            'ere.*.result'     => 'nullable|string|max:255',

            // Attachment
            'att' => 'nullable|array',
            'att.*.start_date' => 'nullable|date',
            'att.*.end_date'   => 'nullable|date',
            'att.*.result'     => 'nullable|string|max:255',
        ];
    }

    // public function withValidator($validator)
    // {
    //     $validator->after(function ($validator) {
    //         foreach (['courses', 'cadre'] as $type) {
    //             if ($this->has($type)) {
    //                 foreach ($this->$type as $index => $item) {
    //                     if (!empty($item['status']) && $item['status'] === 'Passed') {
    //                         if (empty($item['start_date']) || empty($item['end_date'])) {
    //                             $validator->errors()->add(
    //                                 "$type.$index.start_date",
    //                                 "A start date is required when the {$type} status is Passed."
    //                             );
    //                             $validator->errors()->add(
    //                                 "$type.$index.end_date",
    //                                 "An end date is required when the {$type} status is Passed."
    //                             );
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     });
    // }

    public function messages()
    {
        return [
            // Education messages
            'education.required' => 'You must add at least one education record.',
            'education.*.name.required' => 'Please select an education.',
            'education.*.status.in' => 'Status must be either "Running" or "Passed".',
            'education.*.year.required_if' => 'Year is required if education status is Passed.',
            'education.*.year.digits' => 'Year must be 4 digits (e.g., 2025).',

            // Course messages
            // For name
            'courses.*.name.integer' => 'The course name must be a valid ID.',
            'courses.*.name.exists'  => 'The selected course does not exist.',
            // For status
            'courses.*.status.required_with' => 'The course status is required when a course name is selected.',
            'courses.*.status.in'            => 'The course status must be either Running or Passed.',
            // For dates
            'courses.*.start_date.date' => 'The start date must be a valid date.',
            'courses.*.end_date.date'   => 'The end date must be a valid date.',
            // For remark
            'courses.*.remark.max' => 'The remark may not be greater than 255 characters.',



            // Cadre messages
            // For name
            'cadres.*.name.integer' => 'The cadre name must be a valid ID.',
            'cadres.*.name.exists'  => 'The selected cadre does not exist.',
            // For status
            'cadres.*.status.required_with' => 'The cadre status is required when a cadre name is selected.',
            'cadres.*.status.in'            => 'The cadre status must be either Running or Passed.',
            // For dates
            'cadres.*.start_date.date' => 'The start date must be a valid date.',
            'cadres.*.end_date.date'   => 'The end date must be a valid date.',
            // For result
            'cadres.*.result.max' => 'The result may not be greater than 255 characters.',
            'cadres.*.status.string' => 'Please select a valid status for the cadre.',
            'cadres.*.status.in' => 'The status must be either "Running" or "Passed".',
            // The custom closure already shows:
            // "The cadre status is required when a cadre name is selected."


            // Co-Curricular messages
            'cocurricular.*.name.required' => 'Please select a co-curricular activity.',
            'cocurricular.*.remark.max' => 'Remark cannot exceed 255 characters.',

            // ERE messages
            'ere.*.start_date.date' => 'Start date must be a valid date.',
            'ere.*.end_date.date' => 'End date must be a valid date.',
            'ere.*.remark.max' => 'Remark cannot exceed 255 characters.',

            // Attachment messages
            'att.*.start_date.date' => 'Start date must be a valid date.',
            'att.*.end_date.date' => 'End date must be a valid date.',
            'att.*.remark.max' => 'Remark cannot exceed 255 characters.',
        ];
    }
}
