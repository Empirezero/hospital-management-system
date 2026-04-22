<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AppointmentStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public Appointment $appointment) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $status = ucfirst($this->appointment->status);
        return (new MailMessage)
            ->subject('Appointment ' . $status)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your appointment scheduled for **' . $this->appointment->date . '** has been **' . $status . '**.')
            ->line('Doctor: Dr. ' . $this->appointment->doctor?->name)
            ->action('View Appointment', url('/show_appointment'))
            ->line('Please contact us if you have any questions.');
    }

    public function toArray($notifiable): array
    {
        $url = match ($notifiable->role) {
            'patient' => url('/my_appointment'),
            'doctor'  => url('/doctor_appointment'),
            'admin'   => url('/show_appointment'),
            default   => url('/my_appointment'),
        };

        return [
            'type'    => 'appointment',
            'title'   => 'Appointment ' . ucfirst($this->appointment->status),
            'message' => 'Your appointment on ' . \Carbon\Carbon::parse($this->appointment->scheduled_at)->format('d M Y') . ' has been ' . $this->appointment->status . '.',
            'url'     => $url,
        ];
    }
}
