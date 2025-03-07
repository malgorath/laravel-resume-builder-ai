<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'filename', 'mime_type', 'file_data']; // Added 'user_id'

    protected $casts = [
        'file_data' => 'string', // Ensure file data is stored as string
    ];

    /**
     * Get the user that owns the resume.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
