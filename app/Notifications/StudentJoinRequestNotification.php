<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StudentJoinRequestNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $user, public $classroom)
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Join Request: ' . $this->user->name . ' wants to join ' . $this->classroom->title)
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('A new student has requested to join your classroom **"' . $this->classroom->title . '"**.')
            ->line('')
            ->line('This request is currently pending and requires your approval.')
            ->action('Review Join Request', route('dashboard.classroom.people.requests', $this->classroom->code))
            ->line('')
            ->line('Thanks for using our platform! If you face any difficulties or problem, feel free to contact us.')
            ->salutation('— Classroom Team');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'classroom_id' => $this->classroom->id,
            'message' => 'A new student has requested to join your classroom ' . $this->classroom->title,
            'url' => '/classrooms/' . $this->classroom->id . '/requests'
        ];
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
