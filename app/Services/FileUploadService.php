<?php

namespace App\Services;

use App\Models\Assessment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * FileUploadService
 *
 * Handles file upload operations for the assessment system.
 * Provides secure file storage with validation and error handling.
 *
 * @author Laravel Assessment System
 * @version 1.0.0
 */
class FileUploadService
{
    private const ALLOWED_MIME_TYPES = ['pdf', 'doc', 'docx'];
    private const MAX_FILE_SIZE = 2048; // 2MB in KB

    /**
     * Upload resume file and associate with assessment.
     *
     * @param UploadedFile $file
     * @param int $assessmentId
     * @return string The stored file path
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function uploadResume(UploadedFile $file, int $assessmentId): string
    {
        $this->validateFile($file);
        
        $assessment = Assessment::findOrFail($assessmentId);
        
        // Delete old resume if exists
        if ($assessment->resume_path) {
            Storage::disk('public')->delete($assessment->resume_path);
        }
        
        // Store new file
        $path = $file->store('resumes', 'public');
        
        // Update assessment record
        $assessment->update(['resume_path' => $path]);
        
        return $path;
    }

    /**
     * Validate uploaded file.
     *
     * @param UploadedFile $file
     * @throws \InvalidArgumentException
     */
    private function validateFile(UploadedFile $file): void
    {
        if (!$file->isValid()) {
            throw new \InvalidArgumentException('The uploaded file is not valid.');
        }

        if (!in_array($file->getClientOriginalExtension(), self::ALLOWED_MIME_TYPES)) {
            throw new \InvalidArgumentException('Resume must be a PDF, DOC, or DOCX file.');
        }

        if ($file->getSize() > self::MAX_FILE_SIZE * 1024) {
            throw new \InvalidArgumentException('Resume file size must not exceed 2MB.');
        }
    }

    /**
     * Get allowed file types.
     *
     * @return array
     */
    public function getAllowedTypes(): array
    {
        return self::ALLOWED_MIME_TYPES;
    }

    /**
     * Get maximum file size in KB.
     *
     * @return int
     */
    public function getMaxFileSize(): int
    {
        return self::MAX_FILE_SIZE;
    }
}