<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name', 500);
            $table->string('description', 500);
            $table->bigInteger('city_id')->unsigned();
            $table->foreign('city_id')->references('id')->on('cities');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('pic');
            $table->string('friendly_url');
            $table->integer('duration_minutes');
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies');
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
        Schema::dropIfExists('events');
    }
}
