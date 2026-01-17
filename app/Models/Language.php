<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Language Model
 *
 * Represents a programming language available for technical assessment.
 * Each language can have multiple associated questions and can be selected
 * by candidates during the language selection phase. Languages serve as
 * categories for organizing assessment questions.
 *
 * @author Laravel Assessment System
 *
 * @version 1.0.0
 *
 * @property int $id Primary key
 * @property string $name Programming language name (e.g., "JavaScript", "Python")
 * @property string|null $description Optional description of the language
 * @property \Illuminate\Support\Carbon $created_at Language creation timestamp
 * @property \Illuminate\Support\Carbon $updated_at Last modification timestamp
 * @property-read \Illuminate\Database\Eloquent\Collection<Question> $questions Collection of questions for this language
 */
class Language extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * Defines which model attributes can be safely mass-assigned
     * through create() or update() operations. Includes language
     * name and optional description.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get all questions that belong to this programming language.
     *
     * Defines the relationship where each language can have many
     * associated questions. Used for retrieving questions based
     * on candidate's selected languages during assessment.
     *
     * @return HasMany<Question>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
