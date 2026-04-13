<?php

namespace App\Notifications;

use App\Models\InsuranceClaim;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ClaimStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public InsuranceClaim $claim) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $status = ucfirst($this->claim->status);
        return (new MailMessage)
            ->subject('Insurance Claim Update — ' . $status)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your insurance claim #' . $this->claim->id . ' status has been updated to **' . $status . '**.')
            ->line('Insurer: ' . $this->claim->insurer_name)
            ->line('Claimed Amount: Ksh ' . number_format($this->claim->claimed_amount, 2))
            ->when(
                $this->claim->approved_amount,
                fn($mail) =>
                $mail->line('Approved Amount: Ksh ' . number_format($this->claim->approved_amount, 2))
            )
            ->when(
                $this->claim->rejection_reason,
                fn($mail) =>
                $mail->line('Rejection Reason: ' . $this->claim->rejection_reason)
            )
            ->action('View Claim', route('admin.claims.show', $this->claim->id));
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'claim',
            'title'   => 'Claim #' . $this->claim->id . ' — ' . ucfirst($this->claim->status),
            'message' => 'Insurance claim status updated to ' . $this->claim->status . '.',
            'url'     => route('admin.claims.show', $this->claim->id),
        ];
    }
}
