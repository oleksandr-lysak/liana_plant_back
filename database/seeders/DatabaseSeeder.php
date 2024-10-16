<?php

namespace Database\Seeders;

use App\Enums\SourceTimeslotsEnum;
use App\Models\Client;
use App\Models\Master;
use App\Models\Review;
use App\Models\Service;
use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        $services = [
            'pedicure',
            'manicure',
            'hair_coloring',
            'haircut',
            'cosmetics_selection',
            'facial',
            'massage',
            'eyelash_extensions',
            'eyebrow_shaping',
            'waxing',
            'makeup',
            'hair_styling',
            'nail_art',
            'hair_removal',
            'spa_treatments',
            'body_scrub',
            'tanning',
            'botox',
            'laser_treatment',
            'microdermabrasion',
        ];

        foreach ($services as $serviceName) {
            Service::create(['name' => $serviceName]);
        }
        // count of masters
        $total = 1000;
        $this->command->info("Creating {$total} masters...");
        $this->command->getOutput()->progressStart($total);
        for ($i = 0; $i < $total; $i++) {
            Master::factory()->create();
            $this->command->getOutput()->progressAdvance();
        }
        $this->command->getOutput()->progressFinish();

        // count of reviews
        $total = 10000;
        $this->command->info("Creating {$total} reviews...");
        $this->command->getOutput()->progressStart($total);
        for ($i = 0; $i < $total; $i++) {
            Review::factory()->create();
            $this->command->getOutput()->progressAdvance();
        }
        $this->command->getOutput()->progressFinish();

        // count of clients
        $total = 10000;
        $this->command->info("Creating {$total} clients...");
        $this->command->getOutput()->progressStart($total);
        for ($i = 0; $i < $total; $i++) {
            Client::factory()->create();
            $this->command->getOutput()->progressAdvance();
        }
        $this->command->getOutput()->progressFinish();


        $startTime = Carbon::createFromTime(9, 0);
        $endTime = Carbon::createFromTime(18, 0);
        $midnight = Carbon::createFromTime(0, 0);
        $endOfDay = Carbon::createFromTime(23, 59);
        $startDate = Carbon::createFromDate(now()->year, 1, 1);
        $endDate = Carbon::createFromDate(now()->year, 12, 31);


        $masters = Master::all();
        $this->command->info("Creating {$masters->count()} clients...");
        $this->command->getOutput()->progressStart($masters->count());
        foreach ($masters as $master) {

            $currentDate = $startDate->copy();

            // each day of the year
            while ($currentDate->lessThanOrEqualTo($endDate)) {

                // Generate free time slots between 00:00 and 09:00
                $timeSlots = collect();
                $currentSlot = $midnight->copy();

                while ($currentSlot->lessThan($startTime)) {
                    $timeSlots->push([
                        'time' => $currentSlot->copy(),
                        'duration' => 60,
                        'is_booked' => false
                    ]);

                    $currentSlot->addMinutes(60);
                }

                // Generate random time slots between 09:00 and 18:00
                $currentSlot = $startTime->copy();

                while ($currentSlot->lessThan($endTime)) {
                    $slotDuration = rand(30, 120);
                    $timeSlots->push([
                        'time' => $currentSlot->copy(),
                        'duration' => $slotDuration
                    ]);
                    $currentSlot->addMinutes($slotDuration);
                }

                $randomSlots = $timeSlots->random((int) ($timeSlots->count() * 0.5));

                foreach ($timeSlots as $slot) {
                    $isBooked = $slot['time']->between($startTime, $endTime) && $randomSlots->contains($slot);

                    TimeSlot::create([
                        'master_id' => $master->id,
                        'client_id' => $isBooked ? Client::inRandomOrder()->first()->id : null,
                        'date' => $currentDate->toDateString(),
                        'time' => $slot['time']->format('H:i:s'),
                        'is_booked' => $isBooked,
                        'service_id' => $isBooked ? Service::inRandomOrder()->first()->id : null,
                        'source' => $isBooked ? fake()->randomElement(SourceTimeslotsEnum::cases()) : null,
                        'duration' => $slot['duration'],
                        'comment' => $isBooked ? fake()->sentence() : null,
                    ]);
                }

                // Generate free time slots between 18:00 and 23:59
                $currentSlot = $endTime->copy();

                while ($currentSlot->lessThanOrEqualTo($endOfDay)) {
                    TimeSlot::create([
                        'master_id' => $master->id,
                        'client_id' => null,
                        'date' => $currentDate->toDateString(),
                        'time' => $currentSlot->format('H:i:s'),
                        'is_booked' => false,
                        'service_id' => null,
                        'source' => null,
                        'duration' => 60,
                        'comment' => null,
                    ]);
                    $currentSlot->addMinutes(60);
                }
                $currentDate->addDay();
            }
            $this->command->getOutput()->progressAdvance();
        }
        $this->command->getOutput()->progressFinish();

    }
}
