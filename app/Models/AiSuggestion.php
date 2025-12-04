<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiSuggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'resume_id',
        'suggestion_type',
        'content',
        'status',
    ];

    /**
     * Get the user that owns the suggestion.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the resume associated with the suggestion.
     */
    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }
}
