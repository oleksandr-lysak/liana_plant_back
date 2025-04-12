<?php

use App\Http\Services\Master\MasterService;
use App\Models\Master;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('masters', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable();
        });

        Master::all()->each(function ($master) {
            $master->slug = MasterService::generateSlug($master);
            $master->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('masters', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
