<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePaymentGatewayPayu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_gateways', function (Blueprint $table){
            $table->string("merchantId")->nullable();
            $table->string("accountId")->nullable();
            $table->string("api_Login")->nullable();
            $table->string("merchantId_dev")->nullable();
            $table->string("accountId_dev")->nullable();
            $table->string("api_Login_dev")->nullable();
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
