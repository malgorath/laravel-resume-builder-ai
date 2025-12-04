<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    protected $table = 'jobListings';

    protected $fillable = [
        'title',
        'company',
        'description',
        'location',
        'requirements',
        'company_id',
    ];

    protected $casts = [
        'requirements' => 'array',
    ];

    /**
     * Get the company that owns the job.
     */
    public function companyRelation(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Get the applications for the job.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'job_id');
    }
}
