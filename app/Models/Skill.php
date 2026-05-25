<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_profile_id',
        'skill_name'
    ];

    // Relationships
    public function candidateProfile()
    {
        return $this->belongsTo(CandidateProfile::class);
    }
}