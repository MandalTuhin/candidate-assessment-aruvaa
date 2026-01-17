<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Question Model
 *
 * Represents a technical assessment question with multiple choice options.
 * Each question belongs to a specific programming language and contains
 * the question text, answer options array, and correct answer. Questions
 * are randomly selected during assessments based on candidate's language choices.
 *
 * @author Laravel Assessment System
 *
 * @version 1.0.0
 *
 * @property int $id Primary key
 * @property int $language_id Foreign key to languages table
 * @property string $question_text The actual question content
 * @property array $options Array of multiple choice options
 * @property string $correct_answer The correct answer option
 * @property \Illuminate\Support\Carbon $created_at Question creation timestamp
 * @property \Illuminate\Support\Carbon $updated_at Last modification timestamp
 * @property-read Language $language The programming language this question belongs to
 */
class Question extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * Automatically converts the 'options' JSON column to a PHP array
     * when retrieved from the database and back to JSON when stored.
     * This enables easy manipulation of multiple choice options.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',
    ];

    /**
     * Get the programming language that this question belongs to.
     *
     * Defines the inverse relationship where each question belongs
     * to exactly one programming language. Used for filtering
     * questions based on candidate's selected languages.
     *
     * @return BelongsTo<Language, Question>
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
