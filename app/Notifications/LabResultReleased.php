<?php

namespace App\Notifications;

use App\Models\LabRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LabResultReleased extends Notification
{
    use Queueable;

    public function __construct(public LabRequest $labRequest) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Lab Result is Ready')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your lab result for **' . $this->labRequest->labTest?->name . '** has been released.')
            ->line('You can now view your result in the patient portal.')
            ->action('View Result', route('lab.view_result', $this->labRequest->id))
            ->line('If you have any questions, please consult your doctor.');
    }

    public function toArray($notifiable): array
    {
        
        return [
            'type'    => 'lab_result',
            'title'   => 'Lab Result Ready',
            'message' => 'Your result for ' . $this->labRequest->labTest?->name . ' is now available.',
            'url'     => route('lab.view_result', $this->labRequest->id),
        ];
    }
}
