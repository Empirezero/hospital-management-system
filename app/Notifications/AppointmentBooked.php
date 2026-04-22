<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AppointmentBooked extends Notification
{
    use Queueable;

    public function __construct(public Appointment $appointment) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Appointment Booked')
            ->greeting('Hello Dr. ' . $notifiable->name . ',')
            ->line('A new appointment has been booked with you.')
            ->line('Patient: ' . $this->appointment->name)
            ->line('Date: ' . $this->appointment->scheduled_at)
            ->line('Message: ' . ($this->appointment->message ?? 'N/A'))
            ->action('View Appointment', url('/doctor_appointment'));
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'appointment',
            'title'   => 'New Appointment Booked',
            'message' => $this->appointment->name . ' booked an appointment on ' . \Carbon\Carbon::parse($this->appointment->scheduled_at)->format('d M Y'),
            'url'     => url('/doctor_appointment'),
        ];
    }
}
