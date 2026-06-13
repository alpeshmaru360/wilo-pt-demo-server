<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FirefightingControlPanelMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firefighting_control_panel_master', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->string('model')->nullable();
            $table->string('enclosure')->nullable();
            $table->string('type')->nullable();
            $table->string('brand')->nullable();
            $table->string('approval')->nullable();
            $table->string('category')->nullable();
            $table->string('motor_power')->nullable();
            $table->string('voltage')->nullable();
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
        Schema::dropIfExists('firefighting_control_panel_master');
    }
}
