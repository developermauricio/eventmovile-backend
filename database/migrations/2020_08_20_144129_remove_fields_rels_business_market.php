<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFieldsRelsBusinessMarket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_markets', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['country_id']);

            $table->dropColumn(['company_id']);
            $table->dropColumn(['city_id']);
            $table->dropColumn(['country_id']);

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
