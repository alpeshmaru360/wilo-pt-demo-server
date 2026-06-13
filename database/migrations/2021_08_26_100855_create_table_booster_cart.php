<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBoosterCart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booster_carts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('quotation_no')->nullable();
            $table->integer('article_number')->nullable();
            //Pump Info
            $table->string('pump_type')->nullable();
            $table->string('model_no')->nullable();
            $table->float('motor_power',8,2);
            $table->integer('supply_voltage');
            $table->string('manifold');
            $table->string('system_pressure', 8, 2);
            $table->float('pump_price',8,2);
            $table->float('booster_price',8,2);
            $table->float('standard_component_price',8,2);
            $table->float('mechanical_system_price',8,2);
            $table->float('cablePrice',8,2);
            $table->integer('ptp_distance_id');


            // Motor Power
            $table->integer('cp_id');
            $table->float('cp_price',8,2); //control panel price


            $table->string('adder_ids')->nullable();
            $table->string('adder_ids_prices')->nullable();
            $table->float('total_adders_price'); //Optional electrical

            $table->string('mechanical_adder_ids')->nullable();
            $table->string('mechanical_adder_ids_prices')->nullable();
            $table->float('mechanical_total_adders_price'); //Optional mechanical
            // constant from admin
            $table->float('base_frame_size_constant', 8, 2);
            $table->float('cable_size_ampere_constant', 8, 2);
            $table->float('cable_length_constant', 8, 2);
            $table->float('spare_length', 8, 2);
            $table->float('inter_company_margin', 8, 2);
            $table->float('booster_overhead', 8, 2);

            //Calulation Price
            $table->integer('qty');
            $table->float('price', 8, 2);
            $table->float('total_price', 8, 2);
            $table->integer('user_id');
            $table->timestamps();
        });

        Schema::create('booster_items', function (Blueprint $table) {
            $table->id();
            $table->integer('booster_cart_id'); //booster cart id
            $table->string('item_description', 255);
            $table->string('material_number', 255);
            $table->string('wilo_artilce_no', 255);
            $table->float('weight', 8, 2);
            $table->float('height', 8, 2);
            $table->float('width', 8, 2);
            $table->float('depth', 8, 2);
            $table->integer('brand_code');
            $table->integer('function_code');
            $table->string('margin');
            $table->string('qty');
            $table->float('price', 8, 2);
            $table->float('total_price', 8, 2);
            $table->timestamps();
        });

        Schema::create('booster_cp_items', function (Blueprint $table) {
            $table->id();
            $table->integer('booster_cart_id');
            $table->string('item_description', 255);
            $table->string('material_number', 255);
            $table->string('wilo_artilce_no', 255);
            $table->float('weight', 8, 2);
            $table->float('height', 8, 2);
            $table->float('width', 8, 2);
            $table->float('depth', 8, 2);
            $table->integer('brand_code');
            $table->integer('function_code');
            $table->string('margin');
            $table->string('qty');
            $table->float('price', 8, 2);
            $table->float('total_price', 8, 2);
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
        Schema::dropIfExists('booster_carts');
        Schema::dropIfExists('booster_items');

    }
}
