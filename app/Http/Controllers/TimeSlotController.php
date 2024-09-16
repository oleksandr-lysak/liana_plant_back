<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTimeSlotRequest;
use App\Models\TimeSlot;
use Illuminate\Http\Request;

class TimeSlotController extends Controller
{
    public function store(StoreTimeSlotRequest $request)
    {
        $validated = $request->validated();

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

    public function index(Request $request, $masterId, $startDate)
    {
        $slots = TimeSlot::where('master_id', $masterId)
            ->where('date', '>=', $startDate)
            ->get();

        return response()->json($slots);
    }
}
