<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCableSelectionAndBaseFrameCalculation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booster_base_frame_calculation', function (Blueprint $table) {
            $table->increments('id');
            $table->string('item_description', 255)->nullable();
            $table->string('no_of_pumps')->nullable();
            $table->string('pump_model_range1')->nullable();
            $table->string('pump_model_range2')->nullable();
            $table->integer('ptp')->nullable();
            $table->integer('base_frame_length')->nullable();
            $table->string('material_number')->nullable();
            $table->string('wilo_article_number')->nullable();
            $table->integer('brand_code')->nullable();
            $table->integer('function_code')->nullable();
            $table->integer('range')->nullable();
            $table->integer('unit_price')->nullable();
            $table->integer('qty')->nullable();
            $table->timestamps();
        });
        //base_frame_calculation
        Schema::create('booster_cable_selection', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cable', 255)->nullable();
            $table->string('material_number')->nullable();
            $table->string('wilo_article_number')->nullable();
            $table->integer('brand_code')->nullable();
            $table->integer('function_code')->nullable();
            $table->integer('range')->nullable();
            $table->integer('unit_price')->nullable();
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
        Schema::dropIfExists('booster_cable_selection');
        Schema::dropIfExists('booster_base_frame_calculation');

    }
}
