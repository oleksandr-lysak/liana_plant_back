<?php

namespace Database\Seeders;

use App\Models\Master;
use App\Models\Review;
use App\Models\Speciality;
use App\Models\User;
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
        $specialities = [
            'Manicure',
            'Pedicure',
            'Hair coloring',
            'Haircut',
            'Cosmetics selection',
        ];

        foreach ($specialities as $specialityName) {
            Speciality::create(['name' => $specialityName]);
        }
        // Кількість майстрів, які ви хочете створити
        $total = 1000;

        // Виведення прогресу у консоль
        $this->command->info("Creating {$total} masters...");

        $this->command->getOutput()->progressStart($total);

        // Створення майстрів
        for ($i = 0; $i < $total; $i++) {
            Master::factory()->create();
            $this->command->getOutput()->progressAdvance();
        }

        // Завершення прогресу
        $this->command->getOutput()->progressFinish();

        // Виведення прогресу у консоль
        $this->command->info("Creating {$total} users...");
        $this->command->getOutput()->progressStart($total);
        // Створення юзерів
        for ($i = 0; $i < $total; $i++) {
            User::factory()->create();
            $this->command->getOutput()->progressAdvance();
        }
        // Завершення прогресу
        $this->command->getOutput()->progressFinish();

        $total = 100000;
        // Виведення прогресу у консоль
        $this->command->info("Creating {$total} reviews...");
        $this->command->getOutput()->progressStart($total);
        // Створення юзерів
        for ($i = 0; $i < $total; $i++) {
            Review::factory()->create();
            $this->command->getOutput()->progressAdvance();
        }
        // Завершення прогресу
        $this->command->getOutput()->progressFinish();
    }
}
