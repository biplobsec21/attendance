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
        //dd($this->input('courses'));
        return [
            // Education
            'education' => 'required|array|min:1',
            'education.*.name' => 'required|integer|exists:educations,id',
            'education.*.status' => 'required|string|in:Running,Passed',
            'education.*.year' => 'required_if:education.*.status,Passed|nullable|digits:4',

            // Course
            // Courses
            'courses'              => 'nullable|array',
            'courses.*.name'       => 'nullable|integer|exists:courses,id',
            'courses.*.status'     => 'required_with:courses.*.name',
            'courses.*.start_date' => 'nullable|date|required_if:courses.*.status,Passed',
            'courses.*.end_date'   => 'nullable|date|required_if:courses.*.status,Passed',
            'courses.*.result'     => 'nullable|string|max:255',

            // Cadres
            'cadres'              => 'nullable|array',
            'cadres.*.name'       => 'nullable|integer|exists:cadres,id',
            'cadres.*.status'     => 'required_with:cadres.*.name',
            'cadres.*.start_date' => 'nullable|date|required_if:cadres.*.status,Passed',
            'cadres.*.end_date'   => 'nullable|date|required_if:cadres.*.status,Passed',
            'cadres.*.result'     => 'nullable|string|max:255',

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


    public function messages()
    {
        return [
            // Education messages
            'education.required' => 'You must add at least one education record.',
            'education.*.name.required' => 'Please select an education.',
            'education.*.status.in' => 'Status must be either "Running" or "Passed".',
            'education.*.year.required_if' => 'Year is required if education status is Passed.',
            'education.*.year.digits' => 'Year must be 4 digits (e.g., 2025).',

            // Courses
            'courses.*.name.integer' => 'The course name must be a valid ID.',
            'courses.*.name.exists'  => 'The selected course does not exist.',
            'courses.*.status.required_with' => 'Course status is required when a course is selected.',
            'courses.*.status.string'        => 'The course status must be a text value.',
            'courses.*.status.in'            => 'Course status must be either "Running" or "Passed".',
            'courses.*.start_date.required_if' => 'The start date is required when the course status is "Passed".',
            'courses.*.end_date.required_if'   => 'The end date is required when the course status is "Passed".',
            'courses.*.start_date.date'        => 'The course start date must be a valid date.',
            'courses.*.end_date.date'          => 'The course end date must be a valid date.',
            'courses.*.result.max'             => 'The course result may not exceed 255 characters.',

            // Cadres
            'cadres.*.name.integer' => 'The cadre name must be a valid ID.',
            'cadres.*.name.exists'  => 'The selected cadre does not exist.',
            'cadres.*.status.required_with' => 'Cadre status is required when a cadre is selected.',
            'cadres.*.status.string'        => 'The cadre status must be a text value.',
            'cadres.*.status.in'            => 'Cadre status must be either "Running" or "Passed".',
            'cadres.*.start_date.required_if' => 'The start date is required when the cadre status is "Passed".',
            'cadres.*.end_date.required_if'   => 'The end date is required when the cadre status is "Passed".',
            'cadres.*.start_date.date'        => 'The cadre start date must be a valid date.',
            'cadres.*.end_date.date'          => 'The cadre end date must be a valid date.',
            'cadres.*.result.max'             => 'The cadre result may not exceed 255 characters.',



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
