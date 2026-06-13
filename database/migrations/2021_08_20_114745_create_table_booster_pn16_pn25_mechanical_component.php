<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBoosterPn16Pn25MechanicalComponent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booster_ptp_distance_mechanical_component', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pump_model_range1', 255)->nullable();
            $table->string('pump_model_range2',255,2)->nullable();
            $table->string('no_of_pumps')->nullable();
            $table->integer('ptp')->nullable();
            $table->timestamps();

        });
        // Schema::create('booster_pn25_mechanical_component', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->string('pump_model_range1', 255)->nullable();
        //     $table->string('pump_model_range2',255,2)->nullable();
        //     $table->string('no_of_pumps')->nullable();
        //     $table->integer('ptp')->nullable();
        //     $table->timestamps();
        //
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('booster_pn16_mechanical_component');
        // Schema::dropIfExists('booster_pn25_mechanical_component');
        Schema::dropIfExists('booster_ptp_distance_mechanical_component');

    }
}
