<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTimeSlotRequest;
use App\Http\Resources\TimeSlotResource;
use App\Http\Services\TimeSlotService;
use Illuminate\Http\Request;
use JWTAuth;

class TimeSlotController extends Controller
{
    public function store(StoreTimeSlotRequest $request, TimeSlotService $timeSlotService)
    {
        $validated = $request->validated();
        $timeSlotService->storeTimeSlot($validated);

        return response()->json(['message' => 'Time slot updated successfully']);
    }

    public function index(Request $request, $startDate, $masterId, TimeSlotService $timeSlotService)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            $user = null;
        }

        $result = $timeSlotService->validateUserForMaster($user, $masterId);
        if ($result['status'] == false) {
            return response()->json(['error' => $result['message']], 400);
        }

        $slots = $timeSlotService->getSlotsForMaster($result['master'], $startDate);
        return TimeSlotResource::collection($slots);
    }
}
