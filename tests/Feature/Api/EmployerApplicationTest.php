<?php

namespace Tests\Feature\Api;

use App\Models\Application;
use App\Models\EmployerProfile;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployerApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_can_list_applications_for_their_job(): void
    {
        $employer = User::factory()->employer()->create();
        $profile = EmployerProfile::factory()->for($employer)->create();
        $job = Job::factory()->for($profile, 'employerProfile')->create();
        Application::factory()->count(2)->create([
            'job_id' => $job->id,
            'employer_profile_id' => $profile->id,
        ]);

        $response = $this->actingAs($employer)->getJson("/api/employer/jobs/{$job->id}/applications");

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_employer_can_view_job_analytics(): void
    {
        $employer = User::factory()->employer()->create();
        $profile = EmployerProfile::factory()->for($employer)->create();
        $job = Job::factory()->for($profile, 'employerProfile')->create();
        Application::factory()->create([
            'job_id' => $job->id,
            'employer_profile_id' => $profile->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($employer)->getJson("/api/jobs/{$job->id}/analytics");

        $response->assertOk();
        $response->assertJsonPath('total_applications', 1);
        $response->assertJsonPath('pending', 1);
    }

    public function test_employer_cannot_view_another_employers_job_applications(): void
    {
        $employer = User::factory()->employer()->create();
        EmployerProfile::factory()->for($employer)->create();

        $otherJob = Job::factory()->create();

        $response = $this->actingAs($employer)->getJson("/api/employer/jobs/{$otherJob->id}/applications");

        $response->assertForbidden();
    }
}
