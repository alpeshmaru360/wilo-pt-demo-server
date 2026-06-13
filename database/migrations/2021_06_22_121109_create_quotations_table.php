<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->integer('quotation_number');
            $table->integer('cp_cart_id');
            $table->integer('customer_id');
            $table->string('status', 255);
            $table->string('reason', 255);
            $table->longText('modification', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('quotations');
    }

}
