<?php

namespace App\Console\Commands;

use App\Http\Services\Appointment\AppointmentRedisService;
use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class SyncAppointmentsRedis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-appointments-redis';

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
        $this->info('Починаємо синхронізацію даних про зайнятість з бази даних у Redis...');
        $appointments = Appointment::all();
        $masters = Appointment::distinct('master_id')->pluck('master_id');
        foreach ($masters as $masterId) {
            Redis::del($this->appointmentRedisService->getMasterBusyIntervalsKey($masterId));
        }

        foreach ($appointments as $appointment) {
            $this->appointmentRedisService->markAsBusy(
                $appointment->master_id,
                $appointment->start_time,
                $appointment->end_time
            );
        }

        $this->info('Синхронізацію даних про зайнятість завершено.');
    }
}
