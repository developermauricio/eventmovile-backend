<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailTracking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email-tracking', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('email_id')->unsigned();
            $table->foreign('email_id')->references('id')->on('emails');

            $table->string('action')->nullable()->default('view');

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
        //
    }
}
