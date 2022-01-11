<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBusinessMarkets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_markets', function (Blueprint $table) {
            $table->dropColumn(['duration_minutes']);
            $table->string('logo')->nullable();
            $table->string('background_banner')->nullable();
            $table->string('principal_color',25)->nullable();
            $table->string('secundary_color',25)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
