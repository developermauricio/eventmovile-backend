<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNetworkingChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('networking_chats', function (Blueprint $table) {
            $table->id();
            $table->string('message');
            $table->bigInteger('networking_id')->unsigned();
            $table->foreign('networking_id')->references('id')->on('networkings');
            $table->bigInteger('creator_id')->unsigned();
            $table->foreign('creator_id')->references('id')->on('users');
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
        Schema::dropIfExists('networking_chats');
    }
}
