<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UploadResumeRequest
 *
 * Form request for validating resume upload data.
 * Handles validation of file type, size, and assessment association.
 *
 * @author Laravel Assessment System
 * @version 1.0.0
 */
class UploadResumeRequest extends FormRequest
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
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'assessment_id' => 'required|integer|exists:assessments,id',
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
            'resume.required' => 'Please select a resume file to upload.',
            'resume.file' => 'The uploaded file is not valid.',
            'resume.mimes' => 'Resume must be a PDF, DOC, or DOCX file.',
            'resume.max' => 'Resume file size must not exceed 2MB.',
            'assessment_id.required' => 'Assessment ID is missing.',
            'assessment_id.integer' => 'Invalid assessment ID format.',
            'assessment_id.exists' => 'Invalid assessment record.',
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
            'resume' => 'resume file',
            'assessment_id' => 'assessment',
        ];
    }
}