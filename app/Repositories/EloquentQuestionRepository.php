<?php

namespace App\Repositories;

use App\Models\Question;
use Illuminate\Support\Collection;

/**
 * EloquentQuestionRepository
 *
 * Eloquent implementation of the QuestionRepositoryInterface.
 * Handles all database operations related to questions using
 * Laravel's Eloquent ORM.
 *
 * @author Laravel Assessment System
 * @version 1.0.0
 */
class EloquentQuestionRepository implements QuestionRepositoryInterface
{
    /**
     * Get random questions for specified languages.
     *
     * @param array $languageIds
     * @param int|null $limit
     * @return Collection
     */
    public function getRandomQuestionsByLanguages(array $languageIds, ?int $limit = null): Collection
    {
        // If no limit specified, get all questions
        if (!$limit) {
            return Question::with('language')
                ->whereIn('language_id', $languageIds)
                ->inRandomOrder()
                ->get();
        }

        // Calculate questions per language for balanced distribution
        $questionsPerLanguage = intval($limit / count($languageIds));
        $questions = collect();

        // Get questions for each language separately to ensure balanced distribution
        foreach ($languageIds as $languageId) {
            $languageQuestions = Question::with('language')
                ->where('language_id', $languageId)
                ->inRandomOrder()
                ->limit($questionsPerLanguage)
                ->get();
            
            $questions = $questions->merge($languageQuestions);
        }

        // Shuffle the final collection to randomize the order
        return $questions->shuffle();
    }

    /**
     * Get questions by their IDs.
     *
     * @param array $questionIds
     * @return Collection
     */
    public function getQuestionsByIds(array $questionIds): Collection
    {
        return Question::with('language')
            ->whereIn('id', $questionIds)
            ->get();
    }

    /**
     * Get all questions for a specific language.
     *
     * @param int $languageId
     * @return Collection
     */
    public function getQuestionsByLanguage(int $languageId): Collection
    {
        return Question::with('language')
            ->where('language_id', $languageId)
            ->get();
    }

    /**
     * Check if questions exist for given language IDs.
     *
     * @param array $languageIds
     * @return bool
     */
    public function hasQuestionsForLanguages(array $languageIds): bool
    {
        return Question::whereIn('language_id', $languageIds)->exists();
    }
}