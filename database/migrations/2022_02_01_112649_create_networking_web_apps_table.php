<?php

use App\NetworkingWebApp;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNetworkingWebAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('networking_web_apps', function (Blueprint $table) {
            $table->id();

            $table->string('chat_id')->unique();

            $table->unsignedBigInteger('creator_id');
            $table->foreign('creator_id')->references('id')->on('users');

            $table->unsignedBigInteger('guest_id');
            $table->foreign('guest_id')->references('id')->on('users');

            $table->unsignedSmallInteger('status')->default(NetworkingWebApp::PENDING);

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
        Schema::dropIfExists('networking_web_apps');
    }
}
