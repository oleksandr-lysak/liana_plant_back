<?php

namespace App\Http\Controllers;

use App\Models\TimeSlot;
use Illuminate\Http\Request;

class TimeSlotController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'master_id' => 'required|exists:masters,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'is_booked' => 'required|boolean',
            'client_name' => 'nullable|string',
            'service' => 'nullable|string',
        ]);

        TimeSlot::updateOrCreate(
            [
                'master_id' => $validated['master_id'],
                'date' => $validated['date'],
                'time' => $validated['time'],
            ],
            $validated
        );

        return response()->json(['message' => 'Time slot updated successfully']);
    }

    public function index(Request $request)
    {
        $masterId = $request->input('master_id');
        $date = $request->input('date');

        $slots = TimeSlot::where('master_id', $masterId)
            ->where('date', $date)
            ->get();

        return response()->json($slots);
    }
}
