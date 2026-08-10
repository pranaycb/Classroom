<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewSubmission extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $user, public $classroom, public $assignment, public $submission)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Assignment Submission :: ' . $this->assignment->title)
            ->greeting($this->classroom->title)
            ->line($this->user->name . ' has submitted an assignment. Click see details to see the submission')
            ->line(new HtmlString('<small>Submitted ' . $this->submission->created_at->format('M d, Y \a\t h:i') . '</small>'))
            ->action('See Details', route('dashboard.classroom.assignments.submissions.index', [$this->classroom->code, $this->assignment->id]))
            ->salutation('');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
