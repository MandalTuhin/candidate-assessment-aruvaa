<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StartTestRequest
 *
 * Form request for validating test initialization data.
 * Handles validation of candidate information and selected languages.
 *
 * @author Laravel Assessment System
 * @version 1.0.0
 */
class StartTestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'languages' => 'required|array|min:1',
            'languages.*' => 'integer|exists:languages,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'name.max' => 'Name must not exceed 255 characters.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email must not exceed 255 characters.',
            'languages.required' => 'Please select at least one programming language.',
            'languages.min' => 'Please select at least one programming language.',
            'languages.*.exists' => 'One or more selected languages are invalid.',
        ];
    }

    /**
     * Get custom attribute names for error messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'languages' => 'programming languages',
        ];
    }
}