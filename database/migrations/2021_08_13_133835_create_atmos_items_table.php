<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtmosItemsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('atmos_items', function (Blueprint $table) {
            $table->id();
            $table->integer('atmos_cart_id'); //Atmos cart id
            $table->string('item_description', 255);
            $table->string('unit_price', 255);
            $table->string('wilo_artilce_no', 255);
            $table->float('qty', 8, 2);
            $table->float('total_price', 8, 2);
            $table->timestamps();
    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('atmos_items');
    }

}
