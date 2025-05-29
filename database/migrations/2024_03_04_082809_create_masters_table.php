<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('masters', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('phone');
            $table->string('name');
            $table->integer('age')->default(0);
            $table->integer('service_id');
            $table->decimal('longitude', 12, 8);
            $table->decimal('latitude', 12, 8);
            $table->longText('description');
            $table->longText('address')->nullable();
            $table->longText('photo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('masters');
    }
};
