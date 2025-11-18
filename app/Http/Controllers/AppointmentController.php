<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::query()
            ->where('is_done', false)
            ->orderBy('appointment_date')
            ->orderBy('appointment_time');

        // Apply search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter for today
        if ($request->boolean('today')) {
            $query->whereDate('appointment_date', now()->toDateString());
        }

        $appointments = $query->paginate(10)->withQueryString();

        return Inertia::render('AppointmentList', compact('appointments'));
    }


    public function completed(Request $request)
    {
        $query = Appointment::where('is_done', true);

        // Apply search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Sort by latest date and time
        $appointments = $query
            ->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time')
            ->paginate(10)
            ->withQueryString(); // Keeps search query in pagination links

        return Inertia::render('CompletedList', [
            'appointments' => $appointments,
            'filters' => $request->only('search'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|size:10',
            'appointment_date' => 'required|date',
            'hour' => 'required|in:01,02,03,04,05,06,07,08,09,10,11,12',
            'minute' => 'required|in:00,15,30,45',
            'ampm' => 'required|in:AM,PM',
        ]);

        // Convert hour, minute, and AM/PM to 24-hour time
        $hour = (int) $request->input('hour');
        $minute = (int) $request->input('minute');
        $ampm = $request->input('ampm');

        if ($ampm === 'PM' && $hour !== 12) {
            $hour += 12;
        }
        if ($ampm === 'AM' && $hour === 12) {
            $hour = 0;
        }

        // Build Carbon time object
        $appointmentTime = Carbon::createFromTime($hour, $minute);
        $appointmentDate = $validated['appointment_date'];

        // 30-minute conflict window
        $startWindow = $appointmentTime->copy()->subMinutes(29)->format('H:i');
        $endWindow = $appointmentTime->copy()->addMinutes(29)->format('H:i');

        // Check for conflicts
        $conflict = Appointment::where('appointment_date', $appointmentDate)
            ->where('is_done', false)
            ->whereBetween('appointment_time', [$startWindow, $endWindow])
            ->exists();

        if ($conflict) {
            return back()->withErrors([
                'appointment_time' => 'Another appointment exists in this 30-minute slot.',
            ])->withInput();
        }

        // Store appointment
        Appointment::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'appointment_date' => $appointmentDate,
            'appointment_time' => $appointmentTime->format('H:i'),
            'is_done' => false,
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment added!');
    }


    public function markComplete(Appointment $appointment)
    {
        $appointment->is_done = true;
        $appointment->save();

        return redirect()->route('appointments.index')->with('success', 'Appointment marked as completed.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('appointments.index')->with('success', 'Appointment cancelled.');
    }
}
