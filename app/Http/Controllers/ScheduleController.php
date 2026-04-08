<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    // ─── Admin: View all doctor schedules ────────────────────────────

    public function index()
    {
        $doctors = Doctor::with('schedules')->where('status', 'active')->get();
        return view('admin.schedules.index', compact('doctors'));
    }

    // ─── Admin: Manage a specific doctor's schedule ───────────────────

    public function manage($doctor_id)
    {
        $doctor = Doctor::findOrFail($doctor_id);
        $days   = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        // Get existing schedules keyed by day
        $schedules = DoctorSchedule::where('doctor_id', $doctor_id)
            ->get()
            ->keyBy('day');

        return view('admin.schedules.manage', compact('doctor', 'schedules', 'days'));
    }

    // ─── Admin: Save schedule for a doctor ───────────────────────────

    public function save(Request $request, $doctor_id)
    {
        $request->validate([
            'days'                => 'required|array',
            'days.*'              => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time.*'        => 'required_with:days.*|date_format:H:i',
            'end_time.*'          => 'required_with:days.*|date_format:H:i|after:start_time.*',
            'slot_duration.*'     => 'required_with:days.*|integer|in:15,20,30,45,60',
        ]);

        $doctor = Doctor::findOrFail($doctor_id);
        $days   = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            if (in_array($day, $request->days ?? [])) {
                // Upsert — create or update
                DoctorSchedule::updateOrCreate(
                    ['doctor_id' => $doctor->id, 'day' => $day],
                    [
                        'start_time'    => $request->start_time[$day],
                        'end_time'      => $request->end_time[$day],
                        'slot_duration' => $request->slot_duration[$day],
                        'is_active'     => true,
                    ]
                );
            } else {
                // If day unchecked, deactivate it
                DoctorSchedule::where('doctor_id', $doctor->id)
                    ->where('day', $day)
                    ->update(['is_active' => false]);
            }
        }

        return redirect()->route('admin.schedules.index')
            ->with('message', 'Schedule saved for Dr. ' . $doctor->name);
    }

    // ─── Get available slots for a doctor on a date (used by booking) ─

    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date'      => 'required|date|after_or_equal:today',
        ]);

        $date      = Carbon::parse($request->date);
        $dayName   = strtolower($date->format('l')); // e.g. "monday"

        $schedule = DoctorSchedule::where('doctor_id', $request->doctor_id)
            ->where('day', $dayName)
            ->where('is_active', true)
            ->first();

        if (!$schedule) {
            return response()->json([
                'available' => false,
                'message'   => 'Doctor is not available on ' . ucfirst($dayName),
                'slots'     => [],
            ]);
        }

        // Get all slots
        $allSlots = $schedule->getSlots();

        // Get already booked slots for this doctor on this date
        $bookedSlots = Appointment::where('doctor_id', $request->doctor_id)
            ->whereDate('scheduled_at', $date->toDateString())
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->pluck('scheduled_at')
            ->map(fn($t) => Carbon::parse($t)->format('H:i'))
            ->toArray();

        // Mark slots as available or booked
        $slots = collect($allSlots)->map(fn($slot) => [
            'time'      => $slot,
            'available' => !in_array($slot, $bookedSlots),
        ]);

        return response()->json([
            'available' => true,
            'day'       => ucfirst($dayName),
            'slots'     => $slots,
        ]);
    }
}
