<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfilePersonalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Set to true if all authenticated users can create profiles
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'image' => 'nullable|image|max:2048',
            'army_no' => 'required|string|max:50|unique:soldiers,army_no',
            'full_name' => 'required|string|max:255',
            'rank_id' => 'required|exists:ranks,id',
            'company_id' => 'required|exists:companies,id',
            'mobile' => 'required|string|max:20',
            'gender' => 'required|string|in:Male,Female',
            'blood_group' => 'required|string|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'marital_status' => 'required|string|in:Single,Married,Divorced,Widowed',
            'num_boys' => 'nullable|integer|min:0',
            'num_girls' => 'nullable|integer|min:0',
            'village' => 'required|string|max:255',
            'district_id' => 'required|exists:districts,id',
            'permanent_address' => 'required|string',
            'family_mobile_1' => 'nullable|string|max:20',
            'family_mobile_2' => 'nullable|string|max:20',
            'living_type' => 'nullable|in:cantonment,rental,bachelor_mess',
            'living_address' => 'nullable|string|max:255',
            'no_of_brothers' => 'nullable|integer|min:0|max:20',
            'no_of_sisters' => 'nullable|integer|min:0|max:20',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Custom messages for validation errors (optional)
     */
    public function messages(): array
    {
        return [
            'army_no.required' => 'Army number is required.',
            'army_no.unique' => 'This army number is already taken.',
            'full_name.required' => 'Full name is required.',
            'rank_id.required' => 'Please select a rank.',
            'company_id.required' => 'Please select a company.',
            'mobile.required' => 'Mobile number is required.',
            'gender.required' => 'Gender is required.',
            'blood_group.required' => 'Blood group is required.',
            'district_id.required' => 'District is required.',
        ];
    }
}
