<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScpCartsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('scp_carts', function (Blueprint $table) {
            $table->id();
            $table->integer('quotation_no')->nullable();
            $table->integer('article_number')->nullable();
            //Bare Pump
            $table->integer('pump_id')->nullable();
            $table->integer('pump_name')->nullable();
            $table->integer('material_id');
            $table->integer('seal_gland_pack_id');
            $table->integer('is_bare_manual');
            $table->float('bare_pump_price', 8, 2);
            // Motor Power
            $table->string('brand');
            $table->float('power');
            $table->float('motor_height', 8, 2);
            $table->string('frame_size');
            $table->integer('no_of_pole');
            $table->integer('no_of_phase');
            $table->float('voltage', 8, 2);
            $table->float('frequecy', 8, 2);
            $table->string('efficiency');

            //Accesories Data
            $table->integer('is_accesories_manual');
            $table->float('accesories_price', 8, 2);
            $table->integer('application');
            //Master Price
            $table->float('master_price', 8, 2);
            $table->float('insulate_bearing_price', 8, 2);

            $table->string('adder_ids')->nullable();
            $table->string('adder_ids_prices')->nullable();
            $table->float('total_adders_price'); //Optional
            //Assembly Cost
            $table->float('assembly_price', 8, 2);
            $table->float('painting_price', 8, 2);
            $table->float('packing_charge', 8, 2);
            $table->float('overhead_price', 8, 2);

            $table->float('shipping_cost_price', 8, 2);
            $table->float('shipping_cost_percentage', 8, 2);
            $table->float('inter_company_margin_price', 8, 2);
            //Calulation Price
            $table->integer('qty');
            $table->float('price', 8, 2);
            $table->float('total_price', 8, 2);
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('scp_carts');
    }

}
