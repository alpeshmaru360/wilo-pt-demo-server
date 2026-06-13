<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FirefightingMotor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firefighting_motor', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->string('motor_power')->nullable();
            $table->string('frequency')->nullable();
            $table->string('voltage')->nullable();
            $table->string('number_of_pole')->nullable();
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
        Schema::dropIfExists('firefighting_motor');
    }
}
