<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'resume',
        'phone',
        'linkedin_url',
        'github_url',
        'bio',
        'experience_level',
        'years_of_experience',
        'current_position',
        'location',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
