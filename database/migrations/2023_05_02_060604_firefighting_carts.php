<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FirefightingCarts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firefighting_carts', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_no')->nullable();
            $table->string('article_number')->nullable();
            $table->string('full_article_number')->nullable();
            $table->string('pump_id')->nullable();
            $table->string('category')->nullable();
            
            $table->string('pump_models')->nullable();
            $table->string('pump_type')->nullable();
            $table->string('frequency')->nullable();
            $table->string('pump_approval')->nullable();
            $table->string('engine_approval')->nullable();
            $table->string('flow')->nullable();
            $table->string('head')->nullable();
            $table->string('speed_rpm')->nullable();
            $table->string('wilo_article_number')->nullable();

            $table->string('adder_ids')->nullable();
            $table->string('adder_ids_prices')->nullable();
            $table->string('total_adders_price')->nullable();
            $table->string('overhead_price')->nullable();
            $table->string('shipping_cost_percentage')->nullable();
            $table->string('inter_company_margin_price')->nullable();
            $table->string('qty')->nullable();
            $table->string('price')->nullable();
            $table->string('user_id')->nullable();
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
        Schema::dropIfExists('firefighting_carts');
    }
}
