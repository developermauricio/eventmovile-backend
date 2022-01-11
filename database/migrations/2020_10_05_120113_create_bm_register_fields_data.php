<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBmRegisterFieldsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bm_register_fields_data', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bmr_field_id')->unsigned();
            $table->foreign('bmr_field_id')->references('id')->on('bm_register_fields');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');   
            $table->string('value');     
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
        Schema::dropIfExists('bm_register_fields_data');
    }
}
