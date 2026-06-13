<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->integer('cp_cart_id');
            $table->string('name', 255);
            $table->string('project_name', 255);
            $table->string('country', 255);
            $table->integer('revision_number');
            $table->string('segment_category', 255);
            $table->string('project_location', 255);
            $table->string('email_id', 255);
            $table->string('phone_no', 255);
            $table->string('address', 255);
            $table->string('enquiry_form_number', 255);
            $table->string('consultant', 255);
            $table->string('contractor', 255);
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
        Schema::dropIfExists('customers');
    }

}
