<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTimeSlotRequest;
use App\Http\Resources\TimeSlotResource;
use App\Models\TimeSlot;
use App\Http\Services\TimeSlotService;
use App\Models\Master;
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

    public function storeFromClient(StoreTimeSlotRequest $request, TimeSlotService $timeSlotService)
    {
        $validated = $request->validated();

        $storeResult = $timeSlotService->storeTimeSlot($validated);
        if ($storeResult['status'] == false) {
            return response()->json(['message' => $storeResult['message']], 400);
        }

        return response()->json(['message' => 'Time slot updated successfully']);
    }

    public function index(Request $request, $startDate, $masterId, TimeSlotService $timeSlotService)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            $user = null;
        }
        if ($user == null && $masterId == 0) {
            return response()->json(['error' => 'Invalid token'], 400);
        }
        if ($user != null) {
            if ($user->master == null && $masterId == 0) {
                return response()->json(['error' => 'User is not a master'], 400);
            }
        }

        if ($masterId != 0) {
            $master = Master::find($masterId);
        } else {
            $master = $user->master;
        }
        $slots = $timeSlotService->getSlotsForMaster($master, $startDate);

        return TimeSlotResource::collection($slots);
    }
}
