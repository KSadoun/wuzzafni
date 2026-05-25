<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'candidate_profile_id',
        'employer_profile_id',
        'resume',
        'cover_letter',
        'phone',
        'email',
        'status',
        'applied_at'
    ];

    protected $casts = [
        'applied_at' => 'datetime'
    ];

    // Relationships
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function candidateProfile()
    {
        return $this->belongsTo(CandidateProfile::class);
    }

    public function employerProfile()
    {
        return $this->belongsTo(EmployerProfile::class);
    }

    public function notification()
    {
        return $this->hasOne(Notification::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}