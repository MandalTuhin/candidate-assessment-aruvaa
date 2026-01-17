<?php

namespace App\Exceptions;

use Exception;

/**
 * AssessmentException
 *
 * Custom exception for assessment-related errors.
 * Provides specific error handling for assessment system failures.
 *
 * @author Laravel Assessment System
 * @version 1.0.0
 */
class AssessmentException extends Exception
{
    /**
     * Create a new assessment exception for expired sessions.
     */
    public static function sessionExpired(): self
    {
        return new self('Assessment session has expired. Please start a new assessment.');
    }

    /**
     * Create a new assessment exception for invalid sessions.
     */
    public static function invalidSession(): self
    {
        return new self('Invalid assessment session. Please restart the assessment.');
    }

    /**
     * Create a new assessment exception for missing questions.
     */
    public static function noQuestionsAvailable(): self
    {
        return new self('No questions available for the selected programming languages. Please try again later.');
    }

    /**
     * Create a new assessment exception for database errors.
     */
    public static function databaseError(string $message = null): self
    {
        $defaultMessage = 'Unable to process your request due to a database error. Please try again.';
        return new self($message ?? $defaultMessage);
    }

    /**
     * Create a new assessment exception for file upload errors.
     */
    public static function fileUploadError(string $message = null): self
    {
        $defaultMessage = 'File upload failed. Please try again.';
        return new self($message ?? $defaultMessage);
    }
}