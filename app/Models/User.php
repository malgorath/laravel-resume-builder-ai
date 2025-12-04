<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
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

    /**
     * Get the user's resumes.
     */
    public function resumes()
    {
        return $this->hasMany(Resume::class);
    }

    // Define relationship with UserDetail
    public function userDetail()
    {
        return $this->hasOne(UserDetail::class);
    }

    // Define relationship with UserSkill
    public function userSkills()
    {
        return $this->hasMany(UserSkill::class);
    }

    // Define relationship with Skills Table
    public function skills()
    {
        return $this->hasManyThrough(Skill::class, UserSkill::class, 'user_id', 'id', 'id', 'skill_id');
    }

    // Define relationship with Applications
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
