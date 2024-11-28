<?php

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
            // Додати композитний індекс
            $table->point('location')->nullable();
        });

        DB::table('masters')->update(['location' => DB::raw('POINT(longitude, latitude)')]);

        Schema::table('masters', function (Blueprint $table) {
            // Додати просторовий індекс
            $table->spatialIndex('location', 'idx_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('masters', function (Blueprint $table) {
            // Видалити просторовий індекс
            $table->dropSpatialIndex('idx_location');

            // Видалити стовпець location
            $table->dropColumn('location');
        });
    }
};
