<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address',
        'phone',
        'email',
        'linkedin',
        'website',
        'github',
        'other_contact',
    ];

    // Define relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
