<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFieldsBusinessMarket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_markets', function (Blueprint $table) {
            $table->string('speaker_name')->nullable()->change();
            $table->string('tags')->nullable()->change();
            $table->string('friendly_url')->nullable()->change();
            $table->string('location_coordinates')->nullable()->change();
            $table->string('address', 200)->nullable()->change();
            $table->integer('guests_limit')->nullable()->change();
            $table->string('type')->nullable();
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
