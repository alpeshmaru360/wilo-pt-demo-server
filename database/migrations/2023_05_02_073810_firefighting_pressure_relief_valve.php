<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FirefightingPressureReliefValve extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firefighting_pressure_relief_valve', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->string('size')->nullable();
            $table->string('unit_price')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('firefighting_pressure_relief_valve');
    }
}
