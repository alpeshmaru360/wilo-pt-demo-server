<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateControlPanelsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('control_panels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('article_number')->nullable();
            $table->integer('no_of_pump_id');
            $table->integer('power_id');
            $table->integer('voltage_id');  //Power Supply 
            $table->integer('application_id');
            $table->integer('ambient_temp_id');
            $table->integer('stater_type_id');
            $table->integer('communication_protocol_id');
            $table->integer('ip_rating_id');
            $table->integer('components_id');
            $table->integer('enclosure_id');
            $table->integer('range');
            $table->string('folder_name', 255);
            $table->string('file_name_under_folder', 255);
            $table->float('price', 8, 2)->nullable();
            $table->float('tax', 8, 2)->nullable();
            $table->float('total_price', 8, 2)->nullable();
            $table->integer('user_id');
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
        Schema::dropIfExists('control_panels');
    }

}
