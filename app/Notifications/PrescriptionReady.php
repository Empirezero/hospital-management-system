<?php

namespace App\Notifications;

use App\Models\Prescription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PrescriptionReady extends Notification
{
    use Queueable;

    public function __construct(public Prescription $prescription) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Prescription Ready for Pickup')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your prescription for **' . $this->prescription->medicine?->name . '** is ready for pickup at the pharmacy.')
            ->line('Prescribed by: Dr. ' . $this->prescription->doctor?->name)
            ->action('View Prescription', url('/patient/prescriptions'))
            ->line('Please bring your patient ID when collecting.');
    }

    public function toArray($notifiable): array
    {
        $url = match ($notifiable->role){
    
            'patient' => route('/patient/prescriptions'),
            default => url('/prescriptions/' . $this->prescription->id),
        };
        return [
            'type'    => 'prescription',
            'title'   => 'Prescription Ready',
            'message' => $this->prescription->medicine?->name . ' is ready for pickup at the pharmacy.',
            'url'     => $url,
        ];
    }
}
