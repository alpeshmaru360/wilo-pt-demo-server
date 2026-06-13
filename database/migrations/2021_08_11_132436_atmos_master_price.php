<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AtmosMasterPrice extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        //
        Schema::create('atmos_master_motor_prices', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->float('power');
            $table->float('motor_article_number', 8, 2);
            $table->float('wilo_article_number', 8, 2);
            $table->float('motor_height', 8, 2);
            $table->string('frame_size');
            $table->integer('no_of_pole');
            $table->integer('no_of_phase');
            $table->float('voltage', 8, 2);
            $table->float('frequency', 8, 2);
            $table->string('efficiency');
            $table->float('price', 8, 2);
            $table->float('insulate_bearing', 8, 2);
            $table->float('forwinding', 8, 2);
            $table->float('forbearing', 8, 2);
            $table->float('space_heater', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
    }

}
