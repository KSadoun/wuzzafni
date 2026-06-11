<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $table = 'job_posts';

    protected $fillable = [
        'employer_profile_id',
        'title',
        'description',
        'responsibilities',
        'requirements',
        'benefits',
        'salary_min',
        'salary_max',
        'location',
        'work_type',
        'experience_level',
        'application_deadline',
        'status',
        'views_count',
        'applications_count',
    ];

    protected $casts = [
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'application_deadline' => 'date',
        'views_count' => 'integer',
        'applications_count' => 'integer',
    ];

    // Relationships
    public function employerProfile()
    {
        return $this->belongsTo(EmployerProfile::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_job');
    }

    public function technologies()
    {
        return $this->belongsToMany(Technology::class, 'job_technology');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function comments()
    {
        return $this->hasMany(JobComment::class);
    }
}
