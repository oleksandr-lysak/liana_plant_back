<?php
namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SlotStatusChanged
{
    use Dispatchable, SerializesModels;

    public int $masterId;
    public string $eventType;

    public function __construct(int $masterId, string $eventType)
    {
        $this->masterId = $masterId;
        $this->eventType = $eventType;
    }
}
