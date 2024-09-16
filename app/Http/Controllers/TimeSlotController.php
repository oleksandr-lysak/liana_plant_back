<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTimeSlotRequest;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function index(Request $request, $startDate)
    {
        $token = JWTAuth::getToken();
$payload = JWTAuth::decode($token);
dd($payload);
        $master = JWTAuth::parseToken()->authenticate();
        dd($master);
        $slots = TimeSlot::where('master_id', $master->id)
            ->where('date', '>=', $startDate)
            ->get();

        return response()->json($slots);
    }
}
