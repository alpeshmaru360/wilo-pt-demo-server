<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMigrationBoosterFullPumpPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booster_full_pump_price', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pump_article_no_helix_pump')->nullable();
            $table->string('description', 255)->nullable();
            $table->string('model_no', 255)->nullable();
            $table->decimal('pump_height',15,2)->nullable();
            $table->decimal('pump_weight',15,2)->nullable();
            $table->decimal('power',15,2)->nullable();
            $table->integer('no_of_phase')->nullable();
            $table->decimal('voltage',15,2)->nullable();
            $table->integer('frequency')->nullable();
            $table->decimal('unit_price',15,2)->nullable();
            $table->timestamps();
        });

        Schema::create('booster_bareshaft_pump_price', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bareshaft_article_no_helix_pump')->nullable();
            $table->string('description', 255)->nullable();
            $table->string('model_no', 255)->nullable();
            $table->decimal('pump_height',15,2)->nullable();
            $table->decimal('pump_weight',15,2)->nullable();
            $table->decimal('actual_power',15,2)->nullable();
            $table->integer('no_of_phase')->nullable();
            $table->decimal('voltage',15,2)->nullable();
            $table->integer('frequency')->nullable();
            $table->decimal('unit_price',15,2)->nullable();
            $table->timestamps();
        });

        Schema::create('booster_motor_price', function (Blueprint $table) {
            $table->increments('id');
            $table->string('brand')->nullable();
            $table->decimal('power',15,2)->nullable();
            $table->string('motor_article_number', 255)->nullable();
            $table->string('wilo_article_number')->nullable();
            $table->integer('motor_height')->nullable();
            $table->integer('motor_weight')->nullable();
            $table->integer('no_of_pole')->nullable();
            $table->integer('no_of_phase')->nullable();
            $table->integer('voltage')->nullable();
            $table->integer('frequency')->nullable();
            $table->integer('frame')->nullable();
            $table->string('efficiency')->nullable();
            $table->decimal('price',15,2)->nullable();

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
        Schema::dropIfExists('booster_full_pump_price');
        Schema::dropIfExists('booster_bareshaft_pump_price');
        Schema::dropIfExists('booster_motor_price');

    }
}
