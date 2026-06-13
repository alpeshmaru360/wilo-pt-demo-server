<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtmosPumps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atmos_pumps', function (Blueprint $table) {
            $table->id();
            $table->integer('pump_id');
            $table->integer('material_id');
            $table->string('bare_pump_article_no',255);
            $table->float('tpl_fob_price',8,2);
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
        Schema::dropIfExists('atmos_pumps');
    }
}
