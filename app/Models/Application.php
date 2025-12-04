<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_id',
        'status',
        'resume_id',
        'notes',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the user that owns the application.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the job for the application.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    /**
     * Get the resume used for the application.
     */
    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }
}
