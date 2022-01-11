<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsPayusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments_payus', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->string('status')->default('init')->nullable();
            $table->string('referenceCode')->nullable();
            $table->string('signature')->nullable();
            $table->string('processingDate')->nullable();
            $table->bigInteger('ticket_id')->unsigned();
            $table->foreign('ticket_id')->references('id')->on('tickets');
            $table->bigInteger('event_id')->unsigned();
            $table->foreign('event_id')->references('id')->on('events');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('payments_payus');
    }
}
