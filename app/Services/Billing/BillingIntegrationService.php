<?php

namespace App\Services\Billing;

use App\Enums\Billing\BillItemType;
use App\Enums\Billing\BillStatus;
use App\Enums\Billing\BillType;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Encounter;
use App\Models\LabRequest;
use App\Models\LabTest;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingIntegrationService
{
    public function __construct(
        private readonly BillingService         $billing,
        private readonly NumberGeneratorService $numbers,
    ) {}

    // =========================================================================
    // STEP 1 — Encounter created → Create bill + add consultation fee
    // =========================================================================

    public function onEncounterCreated(Encounter $encounter): Bill
    {
        return DB::transaction(function () use ($encounter) {

            // Check if bill already exists for this encounter
            $existing = Bill::where('encounter_id', $encounter->id)->first();
            if ($existing) {
                return $existing;
            }

            $billType = match ($encounter->visit_type) {
                'inpatient'  => BillType::Inpatient,
                'emergency'  => BillType::Emergency,
                default      => BillType::Outpatient,
            };

            // Get the system/admin user to create the bill
            $systemUser = User::where('role', 'admin')->first();

            // Create the bill
            $bill = Bill::create([
                'bill_number'  => $this->numbers->billNumber(),
                'bill_type'    => $billType,
                'patient_id'   => $encounter->patient_id,
                'encounter_id' => $encounter->id,
                'created_by'   => $systemUser->id,
                'status'       => BillStatus::Draft,
            ]);

            // Auto-add consultation fee based on visit type
            $consultationCode = match ($encounter->visit_type) {
                'emergency'  => 'CONS-EM-001',
                'inpatient'  => 'CONS-SP-001',
                'follow_up'  => 'CONS-GP-001',
                default      => 'CONS-GP-001',
            };

            $service = Service::where('code', $consultationCode)->first();

            if ($service) {
                $item = new BillItem([
                    'bill_id'     => $bill->id,
                    'item_type'   => BillItemType::Consultation,
                    'service_id'  => $service->id,
                    'description' => $service->name,
                    'quantity'    => 1,
                    'unit_price'  => $service->standard_price,
                    'nhif_rate'   => $service->nhif_price,
                ]);
                $item->computeAndSave();
            }

            $bill->recalculate();

            Log::info('Bill auto-created for encounter', [
                'encounter_id' => $encounter->id,
                'bill_number'  => $bill->bill_number,
            ]);

            return $bill;
        });
    }

    // =========================================================================
    // STEP 2 — Prescription created → Add pharmacy item to bill
    // =========================================================================

    public function onPrescriptionCreated(Prescription $prescription): ?BillItem
    {
        return DB::transaction(function () use ($prescription) {

            // Find the bill for this encounter
            $bill = Bill::where('encounter_id', $prescription->encounter_id)->first();

            if (! $bill) {
                Log::warning('No bill found for prescription encounter', [
                    'prescription_id' => $prescription->id,
                    'encounter_id'    => $prescription->encounter_id,
                ]);
                return null;
            }

            // Don't add to void/paid bills
            if (in_array($bill->status, [BillStatus::Void, BillStatus::WrittenOff])) {
                return null;
            }

            $medicine = $prescription->medicine;

            if (! $medicine) {
                return null;
            }

            // Get price from inventory
            $inventory = \App\Models\Inventory::where('medicine_id', $medicine->id)
                ->latest()
                ->first();

            $unitPrice = $inventory?->price ?? 0;
            $quantity  = $prescription->duration_days ?? 1;

            $item = new BillItem([
                'bill_id'              => $bill->id,
                'item_type'            => BillItemType::Pharmacy,
                'description'          => $medicine->name . ' — ' . $prescription->dosage
                    . ' for ' . $prescription->duration_days . ' day(s)',
                'quantity'             => $quantity,
                'unit_price'           => $unitPrice,
                'notes'                => $prescription->instructions,
            ]);

            $item->computeAndSave();

            // Link bill_item back to prescription
            $prescription->update(['bill_item_id' => $item->id]);

            $bill->recalculate();

            Log::info('Pharmacy item auto-added to bill', [
                'bill_number'     => $bill->bill_number,
                'medicine'        => $medicine->name,
                'prescription_id' => $prescription->id,
            ]);

            return $item;
        });
    }

    // =========================================================================
    // STEP 3 — Lab request created → Add lab item to bill
    // =========================================================================

    public function onLabRequestCreated(LabRequest $labRequest): ?BillItem
    {
        return DB::transaction(function () use ($labRequest) {

            // Find bill via encounter
            $bill = null;

            if ($labRequest->encounter_id) {
                $bill = Bill::where('encounter_id', $labRequest->encounter_id)->first();
            }

            // Fallback — find via appointment's encounter
            if (! $bill && $labRequest->appointment_id) {
                $encounter = Encounter::where('appointment_id', $labRequest->appointment_id)->first();
                if ($encounter) {
                    $bill = Bill::where('encounter_id', $encounter->id)->first();
                }
            }

            if (! $bill) {
                Log::warning('No bill found for lab request', [
                    'lab_request_id' => $labRequest->id,
                    'encounter_id'   => $labRequest->encounter_id,
                ]);
                return null;
            }

            if (in_array($bill->status, [BillStatus::Void, BillStatus::WrittenOff])) {
                return null;
            }

            $labTest = $labRequest->labTest;

            if (! $labTest) {
                return null;
            }

            // Try to match lab test to services catalogue
            $service = Service::where('category', 'lab')
                ->where('name', 'like', '%' . $labTest->name . '%')
                ->first();

            $unitPrice = $service?->standard_price ?? $labTest->price ?? 0;
            $nhifRate  = $service?->nhif_price ?? null;

            $item = new BillItem([
                'bill_id'     => $bill->id,
                'item_type'   => BillItemType::Lab,
                'service_id'  => $service?->id,
                'description' => $labTest->name,
                'quantity'    => 1,
                'unit_price'  => $unitPrice,
                'nhif_rate'   => $nhifRate,
                'notes'       => $labRequest->notes,
            ]);

            $item->computeAndSave();

            // Link bill_item back to lab request
            $labRequest->update(['bill_item_id' => $item->id]);

            $bill->recalculate();

            Log::info('Lab item auto-added to bill', [
                'bill_number'    => $bill->bill_number,
                'lab_test'       => $labTest->name,
                'lab_request_id' => $labRequest->id,
            ]);

            return $item;
        });
    }

    // =========================================================================
    // STEP 4 — Sale (pharmacy dispense) → Open the bill
    // =========================================================================

    public function onPharmacyDispensed(Encounter $encounter): void
    {
        $bill = Bill::where('encounter_id', $encounter->id)->first();

        if (! $bill) {
            return;
        }

        // If still draft, open it for payment
        if ($bill->status === BillStatus::Draft) {
            $bill->recalculate();
            $bill->update(['status' => BillStatus::Open]);

            Log::info('Bill auto-opened after pharmacy dispense', [
                'bill_number' => $bill->bill_number,
            ]);
        }
    }

    // =========================================================================
    // HELPER — Get or create bill for encounter
    // =========================================================================

    public function getOrCreateBillForEncounter(Encounter $encounter): Bill
    {
        $bill = Bill::where('encounter_id', $encounter->id)->first();

        if ($bill) {
            return $bill;
        }

        return $this->onEncounterCreated($encounter);
    }
}
