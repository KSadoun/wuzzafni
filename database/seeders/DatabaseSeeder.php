<?php

namespace Database\Seeders;

use App\Models\AdminProfile;
use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\Category;
use App\Models\EmployerProfile;
use App\Models\Experience;
use App\Models\Job;
use App\Models\JobComment;
use App\Models\Notification;
use App\Models\Skill;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('category_job')->truncate();
        DB::table('job_technology')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categoryNames = [
            'Software Engineering', 'Design', 'Product', 'Marketing',
            'Sales', 'Data', 'Customer Support', 'Operations',
        ];

        $technologyNames = [
            'PHP', 'Laravel', 'Vue.js', 'React', 'TypeScript',
            'MySQL', 'PostgreSQL', 'Docker', 'AWS', 'Redis', 'Kubernetes',
        ];

        $categories = collect($categoryNames)->map(function (string $name) {
            return Category::firstOrCreate(['name' => $name], Category::factory()->make(['name' => $name])->toArray());
        });

        $technologies = collect($technologyNames)->map(function (string $name) {
            return Technology::firstOrCreate(['name' => $name], Technology::factory()->make(['name' => $name])->toArray());
        });

        $adminUser = User::factory()->admin()->create([
            'first_name' => 'System',
            'last_name' => 'Admin',
            'email' => 'admin@example.com',
        ]);

        AdminProfile::factory()->for($adminUser)->create([
            'permissions' => ['manage_users', 'manage_jobs', 'manage_payments', 'view_reports'],
        ]);

        $employerUsers = User::factory()->count(5)->employer()->create();
        $employerProfiles = $employerUsers->map(function (User $user) {
            return EmployerProfile::factory()->for($user)->create();
        });

        $candidateUsers = User::factory()->count(12)->candidate()->create();
        $candidateProfiles = $candidateUsers->map(function (User $user) {
            return CandidateProfile::factory()->for($user)->create();
        });

        $candidateProfiles->each(function (CandidateProfile $candidateProfile) {
            Skill::factory()->count(fake()->numberBetween(3, 6))->for($candidateProfile)->create();
            Experience::factory()->count(fake()->numberBetween(1, 3))->for($candidateProfile)->create();
        });

        $jobs = collect();

        foreach ($employerProfiles as $employerProfile) {
            $numJobs = fake()->numberBetween(3, 6);

            for ($i = 0; $i < $numJobs; $i++) {
                $job = Job::create([
                    'employer_profile_id' => $employerProfile->id,
                    'title' => fake()->jobTitle(),
                    'description' => fake()->paragraphs(3, true),
                    'responsibilities' => fake()->paragraphs(2, true),
                    'requirements' => fake()->paragraphs(2, true),
                    'benefits' => fake()->paragraphs(1, true),
                    'location' => fake()->city(),
                    'salary_min' => fake()->numberBetween(30000, 60000),
                    'salary_max' => fake()->numberBetween(70000, 120000),
                    'work_type' => fake()->randomElement(['remote', 'onsite', 'hybrid']),
                    'experience_level' => fake()->randomElement(['entry', 'mid', 'senior', 'lead']),
                    'application_deadline' => fake()->dateTimeBetween('+1 week', '+3 months'),
                    'status' => fake()->randomElement(['active', 'active', 'active', 'closed', 'draft']),
                    'applications_count' => 0,
                    'views_count' => fake()->numberBetween(25, 500),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $jobs->push($job);
            }
        }

        if ($jobs->isEmpty()) {
            return;
        }

        $jobs->each(function (Job $job) use ($categories, $technologies) {
            $categoryIds = $categories->random(min(3, $categories->count()))->pluck('id')->values()->toArray();
            $technologyIds = $technologies->random(min(5, $technologies->count()))->pluck('id')->values()->toArray();

            $job->categories()->attach($categoryIds);
            $job->technologies()->attach($technologyIds);
        });

        $jobs->each(function (Job $job) use ($candidateProfiles) {
            $numApplications = fake()->numberBetween(2, min(6, $candidateProfiles->count()));
            $selectedCandidates = $candidateProfiles->shuffle()->take($numApplications);

            $selectedCandidates->each(function (CandidateProfile $candidateProfile) use ($job) {
                $application = Application::create([
                    'job_id' => $job->id,
                    'candidate_profile_id' => $candidateProfile->id,
                    'employer_profile_id' => $job->employer_profile_id,
                    'email' => $candidateProfile->user->email,
                    'phone' => $candidateProfile->phone,
                    'status' => fake()->randomElement(['pending', 'reviewed', 'accepted', 'rejected']),
                    'cover_letter' => fake()->paragraph(),
                    'applied_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if (fake()->boolean(65)) {
                    Notification::create([
                        'user_id' => $candidateProfile->user->id,
                        'application_id' => $application->id,
                        'type' => 'application_status',
                        'title' => 'Application received',
                        'message' => 'Your application was received and is under review.',
                        'is_read' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

            $job->update(['applications_count' => $job->applications()->count()]);
        });

        $allUsers = $candidateUsers->merge($employerUsers)->merge(collect([$adminUser]));

        $jobs->each(function (Job $job) use ($allUsers) {
            $commentCount = fake()->numberBetween(0, 3);

            for ($i = 0; $i < $commentCount; $i++) {
                JobComment::create([
                    'job_id' => $job->id,
                    'user_id' => $allUsers->random()->id,
                    'content' => fake()->paragraph(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }
}
