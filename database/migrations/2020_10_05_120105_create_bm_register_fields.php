<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBmRegisterFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bm_register_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->json('options')->nullable();
            $table->boolean('required')->default(false);
            $table->bigInteger('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business_markets');
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
        Schema::dropIfExists('bm_register_fields');
    }
}
