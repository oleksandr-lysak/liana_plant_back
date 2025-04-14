<?php

namespace App\Observers;

use App\Http\Services\Master\MasterService;
use App\Models\Master;

class MasterObserver
{
    /**
     * Handle the Master "created" event.
     */
    public function created(Master $master): void
    {
        //
    }

    public function creating(Master $master): void
    {
        $master->slug = MasterService::generateSlug($master);
    }

    /**
     * Handle the Master "updated" event.
     */
    public function updated(Master $master): void
    {
        //
    }

    /**
     * Handle the Master "deleted" event.
     */
    public function deleted(Master $master): void
    {
        //
    }

    /**
     * Handle the Master "restored" event.
     */
    public function restored(Master $master): void
    {
        //
    }

    /**
     * Handle the Master "force deleted" event.
     */
    public function forceDeleted(Master $master): void
    {
        //
    }
}
