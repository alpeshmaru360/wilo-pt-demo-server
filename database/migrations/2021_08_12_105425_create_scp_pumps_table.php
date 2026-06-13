<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScpPumpsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('scp_pumps', function (Blueprint $table) {
            $table->id();
            $table->integer('pump_id');
            $table->integer('material_id');
            $table->float('gland_packed_price', 8, 2);
            $table->float('mechanical_seal_price', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('scp_pumps');
    }

}
