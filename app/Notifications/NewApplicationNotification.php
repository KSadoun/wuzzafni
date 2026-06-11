<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use Illuminate\Queue\SerializesModels;

class NewApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected Application $application;

    /**
     * Create a new notification instance.
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Can add 'mail' later if needed
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_id,
            'job_title' => $this->application->job->title,
            'candidate_id' => $this->application->candidateProfile->id,
            'candidate_name' => $this->application->candidateProfile->user->first_name . ' ' . $this->application->candidateProfile->user->last_name,
            'message' => 'A new candidate has applied for your job: ' . $this->application->job->title,
        ];
    }
}
