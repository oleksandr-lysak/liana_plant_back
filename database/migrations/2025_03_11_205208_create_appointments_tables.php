<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Pulse\Support\PulseMigration;

return new class extends PulseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! $this->shouldRun()) {
            return;
        }

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_id');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('client_id');
            $table->string('comment')->nullable();
            $table->timestamps();

            $table->foreign('master_id')->references('id')->on('masters')->onDelete('cascade');

            $table->index(['master_id', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
