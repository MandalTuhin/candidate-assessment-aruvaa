<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

/**
 * QuestionRepositoryInterface
 *
 * Contract for question data access operations.
 * Defines the interface for retrieving questions from the data source
 * with various filtering and selection criteria.
 *
 * @author Laravel Assessment System
 * @version 1.0.0
 */
interface QuestionRepositoryInterface
{
    /**
     * Get random questions for specified languages.
     *
     * @param array $languageIds
     * @param int|null $limit
     * @return Collection
     */
    public function getRandomQuestionsByLanguages(array $languageIds, ?int $limit = null): Collection;

    /**
     * Get questions by their IDs.
     *
     * @param array $questionIds
     * @return Collection
     */
    public function getQuestionsByIds(array $questionIds): Collection;

    /**
     * Get all questions for a specific language.
     *
     * @param int $languageId
     * @return Collection
     */
    public function getQuestionsByLanguage(int $languageId): Collection;

    /**
     * Check if questions exist for given language IDs.
     *
     * @param array $languageIds
     * @return bool
     */
    public function hasQuestionsForLanguages(array $languageIds): bool;
}