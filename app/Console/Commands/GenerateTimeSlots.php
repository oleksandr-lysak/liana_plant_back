<?php

namespace App\Console\Commands;

use App\Http\Services\TimeSlotService;
use App\Models\Master;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateTimeSlots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-time-slots';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command generate timeslots for all masters for current year';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $masters = Master::all();
        $timeSlotService = new TimeSlotService;

        $totalMasters = $masters->count();

        $this->output->progressStart($totalMasters);

        foreach ($masters as $master) {
            $timeSlotService->generateTimeSlots($master->id, $startDate, $endDate);

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        $this->info('The time slots has been generate successfully');
    }
}
