<?php

namespace App\Services;

use App\Models\Job;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class JobSearchService
{
    /**
     * Search and filter jobs.
     */
    public function search(array $filters): LengthAwarePaginator
    {
        $query = Job::query()->with(['employerProfile', 'categories', 'technologies']);

        // Only show active jobs that haven't passed the deadline
        $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('application_deadline')
                    ->orWhere('application_deadline', '>=', now()->toDateString());
            });

        // Keyword search (title or description)
        $query->when(isset($filters['keyword']), function ($q) use ($filters) {
            $keyword = '%'.$filters['keyword'].'%';
            $q->where(function ($sub) use ($keyword) {
                $sub->where('title', 'like', $keyword)
                    ->orWhere('description', 'like', $keyword);
            });
        });

        // Location
        $query->when(isset($filters['location']), function ($q) use ($filters) {
            $q->where('location', 'like', '%'.$filters['location'].'%');
        });

        // Work Type
        $query->when(isset($filters['work_type']), function ($q) use ($filters) {
            $q->where('work_type', $filters['work_type']);
        });

        // Salary Range
        $query->when(isset($filters['salary_min']), function ($q) use ($filters) {
            $q->where('salary_min', '>=', $filters['salary_min']);
        });
        $query->when(isset($filters['salary_max']), function ($q) use ($filters) {
            $q->where('salary_max', '<=', $filters['salary_max']);
        });

        // Date Posted (e.g., 'today', 'week', 'month')
        $query->when(isset($filters['date_posted']), function ($q) use ($filters) {
            $date = match ($filters['date_posted']) {
                'today' => now()->startOfDay(),
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                default => null,
            };
            if ($date) {
                $q->where('created_at', '>=', $date);
            }
        });

        // Category
        $query->when(isset($filters['category_id']), function ($q) use ($filters) {
            $q->whereHas('categories', function ($sub) use ($filters) {
                $sub->where('categories.id', $filters['category_id']);
            });
        });

        // Technologies
        $query->when(isset($filters['technology_ids']) && is_array($filters['technology_ids']), function ($q) use ($filters) {
            $q->whereHas('technologies', function ($sub) use ($filters) {
                $sub->whereIn('technologies.id', $filters['technology_ids']);
            });
        });

        // Sorting
        $sort = $filters['sort'] ?? 'latest';
        match ($sort) {
            'salary_asc' => $query->orderBy('salary_min', 'asc'),
            'salary_desc' => $query->orderBy('salary_max', 'desc'),
            default => $query->orderBy('created_at', 'desc'), // latest
        };

        return $query->paginate($filters['per_page'] ?? 15);
    }
}
