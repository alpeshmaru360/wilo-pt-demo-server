<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FirefightingFlowMeter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firefighting_flow_meter', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->string('size')->nullable();
            $table->string('min_gpm')->nullable();
            $table->string('max_gpm')->nullable();
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
        Schema::dropIfExists('firefighting_flow_meter');
    }
}
