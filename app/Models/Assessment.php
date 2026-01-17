<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Assessment Model
 *
 * Represents a candidate's technical assessment attempt including their
 * personal information, final score, and optional resume upload path.
 * This model stores the results of completed assessments for tracking
 * and recruitment purposes.
 *
 * @author Laravel Assessment System
 *
 * @version 1.0.0
 *
 * @property int $id Primary key
 * @property string $candidate_name Full name of the candidate
 * @property string $candidate_email Email address of the candidate
 * @property int $score Final assessment score (0-100 percentage)
 * @property string|null $resume_path Path to uploaded resume file
 * @property \Illuminate\Support\Carbon $created_at Assessment completion timestamp
 * @property \Illuminate\Support\Carbon $updated_at Last modification timestamp
 */
class Assessment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * Defines which model attributes can be safely mass-assigned
     * through create() or update() operations. Includes candidate
     * information, score, and resume file path.
     *
     * @var array<string>
     */
    protected $fillable = [
        'candidate_name',
        'candidate_email',
        'score',
        'resume_path',
    ];
}
