<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuestBusinessMarketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_business_markets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('guest_id')->unsigned();
            $table->foreign('guest_id')->references('id')->on('guests');
            $table->bigInteger('business_market_id')->unsigned();
            $table->foreign('business_market_id')->references('id')->on('business_markets');
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
        Schema::dropIfExists('guest_business_markets');
    }
}
