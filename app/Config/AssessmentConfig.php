<?php

namespace App\Config;

/**
 * AssessmentConfig
 *
 * Configuration constants and settings for the assessment system.
 * Centralizes all configurable values to follow DRY principles.
 *
 * @author Laravel Assessment System
 * @version 1.0.0
 */
class AssessmentConfig
{
    /**
     * Test duration in seconds (5 minutes).
     */
    public const TEST_DURATION = 300;

    /**
     * Minimum passing score percentage.
     */
    public const PASSING_THRESHOLD = 50;

    /**
     * Maximum file upload size in KB (2MB).
     */
    public const MAX_UPLOAD_SIZE = 2048;

    /**
     * Allowed resume file types.
     */
    public const ALLOWED_FILE_TYPES = ['pdf', 'doc', 'docx'];

    /**
     * Auto-save interval in seconds.
     */
    public const AUTO_SAVE_INTERVAL = 30;

    /**
     * Progress save debounce time in milliseconds.
     */
    public const SAVE_DEBOUNCE_TIME = 500;

    /**
     * Session keys used throughout the application.
     */
    public const SESSION_KEYS = [
        'CANDIDATE_NAME' => 'candidate_name',
        'CANDIDATE_EMAIL' => 'candidate_email',
        'SELECTED_LANGUAGES' => 'selected_languages',
        'TEST_START_TIME' => 'test_start_time',
        'TEST_PROGRESS' => 'test_progress',
        'TEST_QUESTION_IDS' => 'test_question_ids',
        'LAST_SCORE' => 'last_score',
        'ASSESSMENT_ID' => 'assessment_id',
        'ANALYTICS' => 'analytics',
        'ALL_QUESTIONS_REVIEW' => 'all_questions_review',
    ];

    /**
     * Get test duration in seconds.
     */
    public static function getTestDuration(): int
    {
        return self::TEST_DURATION;
    }

    /**
     * Get passing threshold percentage.
     */
    public static function getPassingThreshold(): int
    {
        return self::PASSING_THRESHOLD;
    }

    /**
     * Get maximum upload size in KB.
     */
    public static function getMaxUploadSize(): int
    {
        return self::MAX_UPLOAD_SIZE;
    }

    /**
     * Get allowed file types for resume upload.
     */
    public static function getAllowedFileTypes(): array
    {
        return self::ALLOWED_FILE_TYPES;
    }

    /**
     * Get auto-save interval in seconds.
     */
    public static function getAutoSaveInterval(): int
    {
        return self::AUTO_SAVE_INTERVAL;
    }

    /**
     * Get save debounce time in milliseconds.
     */
    public static function getSaveDebounceTime(): int
    {
        return self::SAVE_DEBOUNCE_TIME;
    }
}