<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance records.
     */
    public function index(Request $request)
    {
        $query = Attendance::with('user');

        // Filter by user if specified
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('date', 'desc')
                            ->orderBy('user_id')
                            ->paginate(20);

        $users = User::orderBy('name')->get();

        return view('rh.pointage.index', compact('attendances', 'users'));
    }

    /**
     * Show the form for creating a new attendance record.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('rh.pointage.create', compact('users'));
    }

    /**
     * Store a newly created attendance record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,absent,late,half_day',
            'notes' => 'nullable|string|max:500',
        ]);

        Attendance::create($request->all());

        return redirect()->route('rh.pointages.index')->with('success', 'Pointage enregistré avec succès.');
    }

    /**
     * Display the specified attendance record.
     */
    public function show(Attendance $attendance)
    {
        return view('rh.pointage.show', compact('attendance'));
    }

    /**
     * Show the form for editing the attendance record.
     */
    public function edit(Attendance $attendance)
    {
        $users = User::orderBy('name')->get();
        return view('rh.pointage.edit', compact('attendance', 'users'));
    }

    /**
     * Update the specified attendance record.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,absent,late,half_day',
            'notes' => 'nullable|string|max:500',
        ]);

        $attendance->update($request->all());

        return redirect()->route('rh.pointages.index')->with('success', 'Pointage mis à jour avec succès.');
    }

    /**
     * Remove the specified attendance record.
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('rh.pointages.index')->with('success', 'Pointage supprimé avec succès.');
    }

    /**
     * Handle check-in/check-out for current user.
     */
    public function checkInOut(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // Find or create today's attendance record
        $attendance = Attendance::firstOrNew([
            'user_id' => $user->id,
            'date' => $today,
        ]);

        if (!$attendance->check_in) {
            // Check-in
            $attendance->check_in = $now->format('H:i:s');
            $attendance->status = 'present';
            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => 'Arrivée enregistrée à ' . $now->format('H:i'),
                'check_in' => $now->format('H:i'),
                'action' => 'check_in'
            ]);
        } elseif (!$attendance->check_out) {
            // Check-out
            $attendance->check_out = $now->format('H:i:s');
            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => 'Départ enregistré à ' . $now->format('H:i'),
                'check_out' => $now->format('H:i'),
                'action' => 'check_out'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà pointé votre arrivée et votre départ aujourd\'hui.'
            ]);
        }
    }

    /**
     * Get today's attendance status for current user.
     */
    public function getTodayStatus()
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
                               ->where('date', $today)
                               ->first();

        return response()->json([
            'has_checked_in' => $attendance && $attendance->check_in,
            'has_checked_out' => $attendance && $attendance->check_out,
            'check_in_time' => $attendance ? $attendance->check_in : null,
            'check_out_time' => $attendance ? $attendance->check_out : null,
            'status' => $attendance ? $attendance->status : null,
        ]);
    }

    /**
     * Export attendance data to CSV.
     */
    public function export(Request $request)
    {
        $filename = 'pointages_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($request) {
            $out = fopen('php://output', 'w');
            // BOM UTF-8 for Excel compatibility
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Utilisateur', 'Date', 'Arrivée', 'Départ', 'Statut', 'Notes']);

            $query = Attendance::with('user');

            // Apply same filters as index
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            if ($request->filled('date_from')) {
                $query->where('date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->where('date', '<=', $request->date_to);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $attendances = $query->orderBy('date', 'desc')->get();

            foreach ($attendances as $attendance) {
                fputcsv($out, [
                    $attendance->user->name,
                    $attendance->date,
                    $attendance->check_in,
                    $attendance->check_out,
                    $attendance->status,
                    $attendance->notes,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
