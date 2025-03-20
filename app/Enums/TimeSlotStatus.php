<?php

namespace App\Enums;

enum TimeSlotStatus: string
{
    case Booked = 'booked';
    case Free = 'free';
    case Completed = 'completed';
}
