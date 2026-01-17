<?php

namespace App\Services;

use App\Config\AssessmentConfig;
use App\ValueObjects\TestSession;
use Illuminate\Support\Facades\Session;

/**
 * SessionService
 *
 * Handles all session-related operations for the assessment system.
 * Provides a clean interface for managing test sessions, progress tracking,
 * and candidate information storage.
 *
 * @author Laravel Assessment System
 * @version 1.0.0
 */
class SessionService
{
    /**
     * Initialize a new test session.
     *
     * @param string $candidateName
     * @param string $candidateEmail
     * @param array $selectedLanguages
     * @return TestSession
     */
    public function initializeTestSession(
        string $candidateName,
        string $candidateEmail,
        array $selectedLanguages
    ): TestSession {
        $this->clearTestSession();
        
        $startTime = now()->timestamp;
        
        Session::put([
            AssessmentConfig::SESSION_KEYS['CANDIDATE_NAME'] => $candidateName,
            AssessmentConfig::SESSION_KEYS['CANDIDATE_EMAIL'] => $candidateEmail,
            AssessmentConfig::SESSION_KEYS['SELECTED_LANGUAGES'] => $selectedLanguages,
            AssessmentConfig::SESSION_KEYS['TEST_START_TIME'] => $startTime,
        ]);

        return new TestSession(
            $candidateName,
            $candidateEmail,
            $selectedLanguages,
            $startTime,
            AssessmentConfig::getTestDuration()
        );
    }

    /**
     * Get current test session.
     *
     * @return TestSession|null
     */
    public function getCurrentTestSession(): ?TestSession
    {
        $candidateName = Session::get(AssessmentConfig::SESSION_KEYS['CANDIDATE_NAME']);
        $candidateEmail = Session::get(AssessmentConfig::SESSION_KEYS['CANDIDATE_EMAIL']);
        $selectedLanguages = Session::get(AssessmentConfig::SESSION_KEYS['SELECTED_LANGUAGES'], []);
        $startTime = Session::get(AssessmentConfig::SESSION_KEYS['TEST_START_TIME']);

        if (!$candidateName || !$candidateEmail || empty($selectedLanguages) || !$startTime) {
            return null;
        }

        return new TestSession(
            $candidateName,
            $candidateEmail,
            $selectedLanguages,
            $startTime,
            AssessmentConfig::getTestDuration()
        );
    }

    /**
     * Save test progress.
     *
     * @param array $answers
     * @return void
     */
    public function saveProgress(array $answers): void
    {
        Session::put(AssessmentConfig::SESSION_KEYS['TEST_PROGRESS'], $answers);
    }

    /**
     * Get saved progress.
     *
     * @return array
     */
    public function getSavedProgress(): array
    {
        return Session::get(AssessmentConfig::SESSION_KEYS['TEST_PROGRESS'], []);
    }

    /**
     * Store question IDs for the current test.
     *
     * @param array $questionIds
     * @return void
     */
    public function storeQuestionIds(array $questionIds): void
    {
        Session::put(AssessmentConfig::SESSION_KEYS['TEST_QUESTION_IDS'], $questionIds);
    }

    /**
     * Get stored question IDs.
     *
     * @return array
     */
    public function getQuestionIds(): array
    {
        return Session::get(AssessmentConfig::SESSION_KEYS['TEST_QUESTION_IDS'], []);
    }

    /**
     * Store assessment results in session.
     *
     * @param array $resultData
     * @return void
     */
    public function storeAssessmentResults(array $resultData): void
    {
        Session::put([
            AssessmentConfig::SESSION_KEYS['LAST_SCORE'] => $resultData['score'],
            AssessmentConfig::SESSION_KEYS['ASSESSMENT_ID'] => $resultData['assessment_id'],
            AssessmentConfig::SESSION_KEYS['ANALYTICS'] => $resultData['analytics'],
            AssessmentConfig::SESSION_KEYS['ALL_QUESTIONS_REVIEW'] => $resultData['all_questions_review'],
        ]);
    }

    /**
     * Get assessment results from session.
     *
     * @return array
     */
    public function getAssessmentResults(): array
    {
        return [
            'score' => Session::get(AssessmentConfig::SESSION_KEYS['LAST_SCORE'], 0),
            'assessment_id' => Session::get(AssessmentConfig::SESSION_KEYS['ASSESSMENT_ID']),
            'analytics' => Session::get(AssessmentConfig::SESSION_KEYS['ANALYTICS'], []),
            'all_questions_review' => Session::get(AssessmentConfig::SESSION_KEYS['ALL_QUESTIONS_REVIEW'], []),
            'candidate_name' => Session::get(AssessmentConfig::SESSION_KEYS['CANDIDATE_NAME']),
        ];
    }

    /**
     * Clear test session data.
     *
     * @return void
     */
    public function clearTestSession(): void
    {
        Session::forget([
            AssessmentConfig::SESSION_KEYS['TEST_PROGRESS'],
            AssessmentConfig::SESSION_KEYS['TEST_START_TIME'],
            AssessmentConfig::SESSION_KEYS['TEST_QUESTION_IDS']
        ]);
    }

    /**
     * Check if valid test session exists.
     *
     * @return bool
     */
    public function hasValidTestSession(): bool
    {
        return $this->getCurrentTestSession() !== null;
    }
}