<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->integer('cp_cart_id');
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
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('items');
    }

}
