<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessMarketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_markets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('sort_description', 400);
            $table->integer('duration_minutes')->default(60);
            $table->string('speaker_name', 100);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('pic');
            $table->string('tags');
            $table->string('friendly_url');
            $table->string('location_coordinates');
            $table->string('address', 200);
            $table->bigInteger('country_id')->unsigned();
            $table->foreign('country_id')->references('id')->on('countries');
            $table->bigInteger('city_id')->unsigned();
            $table->foreign('city_id')->references('id')->on('cities');
            $table->integer('guests_limit')->default(0);
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
        Schema::dropIfExists('business_markets');
    }
}
