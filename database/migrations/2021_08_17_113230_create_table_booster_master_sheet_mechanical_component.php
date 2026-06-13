<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBoosterMasterSheetMechanicalComponent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booster_master_sheet_mechanical_component', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description', 255)->nullable();
            $table->decimal('weight', 15)->nullable();
            $table->string('wilo_article_no',255,2)->nullable();
            $table->integer('brand_code')->nullable();
            $table->integer('function_code')->nullable();
            $table->integer('range')->nullable();
            $table->decimal('price',15,2)->nullable();
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
        Schema::dropIfExists('booster_master_sheet_mechanical_component');
    }
}
