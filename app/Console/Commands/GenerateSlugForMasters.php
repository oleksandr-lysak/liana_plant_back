<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateSlugForMasters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-slug-for-masters';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating slugs for masters...');

        $masters = \App\Models\Master::all();

        foreach ($masters as $master) {
            $slug = \App\Http\Services\Master\MasterService::generateSlug($master);
            $master->slug = $slug;
            $master->save();
            $this->info("Generated slug for master: {$master->name} - {$slug}");
        }

        $this->info('Slugs generated successfully.');
        return Command::SUCCESS;
    }
}
