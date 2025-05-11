<?php

namespace App\Console\Commands;

use App\Http\Services\Appointment\AppointmentRedisService;
use App\Models\Master;
use Illuminate\Console\Command;

class ClearExpiredAppointmentsRedis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-expired-appointments-redis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected AppointmentRedisService $appointmentRedisService;

    public function __construct(AppointmentRedisService $appointmentRedisService)
    {
        parent::__construct();
        $this->appointmentRedisService = $appointmentRedisService;
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $masters = Master::all();

        foreach ($masters as $master) {
            $this->appointmentRedisService->clearExpiredIntervals($master->id);
            $this->info("Old records for master id {$master->id} deleted.");
        }

        $this->info('Clearing expired appointments in Redis finished.');
    }
}
