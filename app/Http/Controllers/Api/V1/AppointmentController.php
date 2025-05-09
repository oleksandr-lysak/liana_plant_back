<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Availability\SetAvailableMasterRequest;
use App\Http\Requests\BookTimeRequest;
use App\Http\Services\Appointment\AppointmentService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Check if the master is busy at the given time.
     */
    public function isBusy(Request $request, AppointmentService $appointmentService)
    {
        $request->validate([
            'master_id' => 'required|integer',
            'datetime'  => 'required|date',
        ]);

        return response()->json([
            'busy' => $appointmentService->isMasterBusy(
                $request->master_id,
                $request->datetime
            )
        ]);
    }

    /**
     * Get all booked slots for the given master and date.
     */
    public function bookedSlots(Request $request, AppointmentService $appointmentService)
    {
        $request->validate([
            'master_id' => 'required|integer',
            'date'      => 'required|date',
        ]);

        return response()->json(
            $appointmentService->getBookedSlots(
                $request->master_id,
                $request->date
            )
        );
    }

    /**
     * Book a slot for the given master.
     */
    public function book(BookTimeRequest $request, AppointmentService $appointmentService)
    {
        $data = $request->validated();
        $booking = $appointmentService->bookSlot(
            $data['master_id'],
            $data['client_phone'],
            $data['start_time'],
            $data['service_id'],
            $data['comment'] ?? '',
            $data['duration'] ?? 30
        );

        return $booking
            ? response()->json(['message' => 'Slot has been booked'], 201)
            : response()->json(['message' => 'This slot is busy'], 409);
    }
}
