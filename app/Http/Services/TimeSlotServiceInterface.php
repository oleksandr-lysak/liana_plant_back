<?php
namespace App\Http\Services;

use App\Models\Master;

interface TimeSlotServiceInterface
{
    public function getSlotsForMaster(Master $master, String $startDate);
}
