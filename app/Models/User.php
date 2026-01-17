<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model
 *
 * Represents an authenticated user in the Laravel assessment system.
 * Extends Laravel's default Authenticatable model with standard
 * authentication features including password hashing, email verification,
 * and notification capabilities. Currently used for system administration
 * rather than candidate authentication.
 *
 * @author Laravel Assessment System
 *
 * @version 1.0.0
 *
 * @property int $id Primary key
 * @property string $name Full name of the user
 * @property string $email Email address (unique)
 * @property \Illuminate\Support\Carbon|null $email_verified_at Email verification timestamp
 * @property string $password Hashed password
 * @property string|null $remember_token Token for "remember me" functionality
 * @property \Illuminate\Support\Carbon $created_at User creation timestamp
 * @property \Illuminate\Support\Carbon $updated_at Last modification timestamp
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * Defines which model attributes can be safely mass-assigned
     * through create() or update() operations. Includes basic
     * user information but excludes sensitive fields like password
     * for security reasons.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * Specifies which attributes should be excluded when the model
     * is converted to an array or JSON. Protects sensitive information
     * like passwords and remember tokens from being exposed in API
     * responses or logs.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast to native types.
     *
     * Defines automatic type casting for model attributes when
     * retrieved from the database. Ensures email_verified_at is
     * properly cast to a Carbon datetime instance and password
     * is automatically hashed when set.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
