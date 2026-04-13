<?php

namespace App\Services;

use App\Models\LabRequest;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\InsuranceClaim;
use App\Notifications\LabResultReleased;
use App\Notifications\AppointmentStatusChanged;
use App\Notifications\PrescriptionReady;
use App\Notifications\ClaimStatusChanged;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function __construct(protected SmsService $sms) {}

    public function labResultReleased(LabRequest $labRequest): void
    {
        $user = $labRequest->user ??
            \App\Models\User::where('email', $labRequest->patient_email)->first();

        if (!$user) return;

        $user->notify(new LabResultReleased($labRequest));

        $phone = $labRequest->patient_phone
            ?? $user->patient?->phone;

        if ($phone) {
            $this->sms->send(
                $phone,
                "Hello {$user->name}, your lab result for {$labRequest->labTest?->name} is ready. Log in to view it."
            );
        }
    }

    public function appointmentStatusChanged(Appointment $appointment): void
    {
        $user = \App\Models\User::find($appointment->user_id)
            ?? \App\Models\User::where('email', $appointment->email)->first();

        if (!$user) {
            Log::warning('No user found for appointment: ' . $appointment->id);
            return;
        }

        $user->notify(new AppointmentStatusChanged($appointment));

        $phone = $appointment->number;
        if ($phone) {
            $this->sms->send(
                $phone,
                "Hello {$appointment->name}, your appointment on {$appointment->scheduled_at} has been {$appointment->status}. - Hospital"
            );
        }
    }

    public function prescriptionReady(Prescription $prescription): void
    {
        $user = $prescription->patient?->user ?? null;

        if (!$user) return;

        $user->notify(new PrescriptionReady($prescription));

        $phone = $prescription->patient?->phone ?? null;

        if ($phone) {
            $this->sms->send(
                $phone,
                "Hello {$user->name}, your prescription for {$prescription->medicine?->name} is ready for pickup at the pharmacy."
            );
        }
    }

    public function claimStatusChanged(InsuranceClaim $claim): void
    {
        $user = $claim->patient?->user ?? null;

        if (!$user) return;

        $user->notify(new ClaimStatusChanged($claim));

        $phone = $claim->patient?->phone ?? null;

        if ($phone) {
            $this->sms->send(
                $phone,
                "Hello {$user->name}, your insurance claim #{$claim->id} has been updated to {$claim->status}."
            );
        }
    }
}
