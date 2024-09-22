<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTimeSlotRequest;
use App\Http\Resources\TimeSlotResource;
use App\Models\TimeSlot;
use App\Http\Services\TimeSlotService;
use Illuminate\Http\Request;
use JWTAuth;

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

    public function index(Request $request, $startDate, TimeSlotService $timeSlotService)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user==null) {
            return response()->json(['error' => 'Invalid token'], 400);
        }
        if ($user->master == null) {
            return response()->json(['error' => 'User is not a master'], 400);
        }
        $slots = $timeSlotService->getSlotsForMaster($user->master, $startDate);

        return TimeSlotResource::collection($slots);
    }
}
