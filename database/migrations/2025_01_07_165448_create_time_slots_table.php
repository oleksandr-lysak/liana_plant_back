<?php

use App\Enums\TimeSlotStatus;
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
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_id')->constrained()->onDelete('cascade');
            $table->date('date')->index();
            $table->time('start_time')->index();
            $table->time('end_time')->index();
            $table->unsignedBigInteger('client_id')->nullable()->index();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->foreign('service_id')->references('id')->on('services')->onDelete('set null');
            $table->text('comment')->nullable();
            $table->enum('status', array_column(TimeSlotStatus::cases(), 'value'))->default(TimeSlotStatus::Booked->value);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['master_id', 'date', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};
