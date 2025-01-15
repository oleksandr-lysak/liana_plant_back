<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Master;
use App\Models\Review;
use App\Models\Service;
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

    }
}
