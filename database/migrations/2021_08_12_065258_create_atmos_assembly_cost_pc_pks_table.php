<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtmosAssemblyCostPcPksTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('atmos_assembly_cost_pc_pks', function (Blueprint $table) {
            $table->id();
            $table->float('power');
            $table->float('assembly_charge', 8, 2);
            $table->float('painting_charge', 8, 2);
            $table->float('packing_charge', 8, 2);
            $table->float('labour_hour', 8, 2);
            $table->string('shipping');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('atmos_assembly_cost_pc_pks');
    }

}
